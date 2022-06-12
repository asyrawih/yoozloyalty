<?php

namespace Platform\Controllers\Staff;

use App\Staff;
use Exception;
use App\Customer;
use Platform\Models\Code;
use Illuminate\Http\Request;
use Platform\Models\History;
use App\Models\EmailTemplate;
use Barzo\Password\Generator;
use Platform\Models\Campaign;
use Illuminate\Support\Carbon;
use Platform\Controllers\Core;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessMerchantSendMail;
use App\Repositories\NotifPusherRepositories;
use App\Repositories\Campaign\PointsConversionRuleRepository;
use Illuminate\Http\JsonResponse;

/**
 * @group Staff Point
 *
 * Endpoints related to handling redemption for staff
 * @package Platform\Controllers\Staff
 */
class PointController extends Controller {

    private $notifPusher;

    private PointsConversionRuleRepository $pointsConversionRule;

    public function __construct(
        NotifPusherRepositories $notifPusher,
        PointsConversionRuleRepository $pointsConversionRule
    ) {
        $this->notifPusher = $notifPusher;
        $this->pointsConversionRule = $pointsConversionRule;
    }


    /*
     |--------------------------------------------------------------------------
     | Points related functions
     |--------------------------------------------------------------------------
     */

    /**
     * Validate if link token is (still) valid
     * @queryParam uuid string required uuid of website. Example: 283ca865-a71c-4d4a-b8cb-8c46c5b3ca57
     * @queryParam token string required token of user.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postValidateLinkToken(Request $request)
    {

        $campaign = Campaign::withoutGlobalScopes()
            ->whereUuid(request('uuid', 0))
            ->firstOrFail();

        $token = $request->token;

        // Validate token
        $code = Code::query()
            ->where('code', $token)
            ->where('campaign_id', $campaign->id)
            ->where('type', 'token')
            ->where('expires_at', '>', Carbon::now())->first();

        $tokenIsValid = false;

        $customer = null;

        if ($code !== null) {
            $tokenIsValid = true;
            $customer = $code->customer;
        }

        return response()->json([
            'status' => 'success',
            'tokenIsValid' => $tokenIsValid,
            'customer' => $customer
        ]);
    }

    /**
     * Push credited points to user using token (qr code)
     * @bodyParam uuid string required uuid of website. Example: 283ca865-a71c-4d4a-b8cb-8c46c5b3ca57
     * @bodyParam token string required token of user.
     * @bodyParam points integer required amount of point for credited to user.
     * @bodyParam segments integer[] id of segments.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postPushCreditsByToken(Request $request)
    {
        DB::beginTransaction();

        try {
            $currentStaff = auth('staff')->user();

            $campaign = Campaign::withoutGlobalScopes()
                ->whereUuid($request->input('uuid', 0))
                ->firstOrFail();

            $segments = $request->segments;

            // Validate token
            $code = Code::query()
                ->where('code', $request->token)
                ->where('campaign_id', $campaign->id)
                ->where('type', 'token')
                ->where('expires_at', '>', Carbon::now())
                ->first();

            if (! $code) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Token is not valid or has expired.',
                ], JsonResponse::HTTP_BAD_REQUEST);
            }

            $customerNumber = preg_replace('/\D+/', '', $request->customer_number);

            // Find customer by number
            $customer = Customer::query()
                ->where('campaign_id', $campaign->id)
                ->where('customer_number', $customerNumber)
                ->where('active', 1)
                ->firstOrFail();

            $countHistoryToday = History::query()
                ->where('campaign_id', $campaign->id)
                ->where('customer_id', $customer->id)
                ->where('points', '>' , 0)
                ->where('event', '!=', 'Sign up bonus')
                ->whereDate('created_at', Carbon::today())
                ->count();

            if ($countHistoryToday >= $campaign->credit_transaction_limit) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'The customer has reached the credit transaction limit.',
                    'errors' => [
                        'code' => 'The customer has reached the credit transaction limit.'
                    ]
                ], JsonResponse::HTTP_BAD_REQUEST);
            }

            // GET credit points
            $response = $this->pointsConversionRule->credit_points(
                $campaign->id,
                floatval($request->receipt_amount),
                $campaign->credit_points_mode
            );

            if ($response['status'] === 'error') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Please contact website administrator to setup the Conversion Rule.',
                    'errors' => [
                        'receipt_amount' => 'Please contact website administrator to setup the Conversion Rule.',
                    ]
                ], JsonResponse::HTTP_BAD_REQUEST);
            }

            $points = $response['points'];

            $history = new History;
            $history->customer_id = $customer->id;
            $history->campaign_id = $campaign->id;
            $history->staff_id = $currentStaff->id;
            $history->staff_name = $currentStaff->name;
            $history->staff_email = $currentStaff->email;
            $history->points = $points;
            $history->bill_number = $request->receipt_number;
            $history->bill_amount = $request->receipt_amount;
            $history->event = 'Credited with QR code';
            $history->created_by = $campaign->created_by;
            $history->points_expired_date = Carbon::now()->addDays($campaign->user->points_expiry->points_expiry ?? config('system.default_points_expiry'));

            $history->save();

            // Segments
            if (is_array($segments) && count($segments) > 0) {
                $history->segments()->sync($segments);
            }

            // Delete
            $code->delete();

            // Push credited
            $options = array(
                'cluster' => config('broadcasting.connections.pusher.options.cluster'),
                'useTLS' => true
            );

            $pusher = new \Pusher\Pusher(
                config('broadcasting.connections.pusher.key'),
                config('broadcasting.connections.pusher.secret'),
                config('broadcasting.connections.pusher.app_id'),
                $options
            );

            $data = ['points' => $points];

            $pusher->trigger($code->code, 'credited', $data);

            if ($this->notifPusher->isAvailable()) {
                $this->notifPusher->pointCredited($code->customer, $points, 'Credited with QR code');
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'The customer has been successfully credited with QR Code',
                'data' => [
                    'points' => $points
                ]
            ]);
        } catch (Exception $exception) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate easy to remember code for merchant
     * @bodyParam uuid string required uuid of website. Example: 283ca865-a71c-4d4a-b8cb-8c46c5b3ca57
     * @bodyParam expires string required following value hour , day , week , month.
     * @bodyParam bill_amount integer required
     * @bodyParam bill_number integer required
     * @bodyParam segments integer[] id of segments.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postGenerateCustomerCode(Request $request)
    {
        try {
            // $account = app()->make('account');

            $campaign = \Platform\Models\Campaign::withoutGlobalScopes()->whereUuid(request('uuid', 0))->firstOrFail();

            $points = $request->points;

            $expires = $request->expires;

            $segments = $request->segments;

            // First, delete existing codes
            Code::query()
                ->where('campaign_id', $campaign->id)
                ->where('staff_id', auth('staff')->id())
                ->where('type', 'customer')
                ->where('expires_at', '<', Carbon::now())
                ->delete();

            // Code::where('type', 'customer')->where('expires_at', '<', \Carbon\Carbon::now())->delete();
            // $deletedRows = Code::where('campaign_id', $campaign->id)->where('type', 'customer')->where('staff_id', auth('staff')->user()->id)->delete();

            // GET credit points
            $response = $this->pointsConversionRule->credit_points(
                $campaign->id,
                floatval($request->bill_amount),
                $campaign->credit_points_mode
            );

            if ($response['status'] === 'error') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Please contact website administrator to setup the Conversion Rule.',
                    'errors' => [
                        'bill_amount' => 'Please contact website administrator to setup the Conversion Rule.',
                    ]
                ], 422);
            }

            $points = $response['points'];

            // Create new code
            $customer_code = $this->getUniqueCode('customer_code', $campaign->id);

            // GET expires
            switch ($expires) {
                case 'hour': $expires_at = Carbon::now()->addHours(1); break;
                case 'day': $expires_at = Carbon::now()->addDays(1); break;
                case 'week': $expires_at = Carbon::now()->addWeeks(1); break;
                case 'month': $expires_at = Carbon::now()->addMonths(1); break;
            }

            $code = new Code;
            $code->campaign_id = $campaign->id;
            $code->staff_id = auth('staff')->user()->id;
            $code->staff_name = auth('staff')->user()->name;
            $code->staff_email = auth('staff')->user()->email;
            $code->type = 'customer';
            $code->code = $customer_code;
            $code->bill_number = $request->bill_number;
            $code->bill_amount = $request->bill_amount;
            $code->points = $points;
            $code->expires_at = $expires_at;
            $code->created_by = $campaign->created_by;

            $code->save();

            // Segments
            if (is_array($segments) && count($segments) > 0) {
                $code->segments()->sync($segments);
            }

            // Format code
            $customer_code = implode('-', str_split($customer_code, 3));

            return response()->json([
                'status' => 'success',
                'code' => $customer_code
            ]);
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Get active customer code(s)
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getActiveCustomerCodes(Request $request)
    {
        $staff = Staff::query()->find(auth('staff')->id());

        $codes = Code::query()
            ->where('type', 'customer')
            ->where('staff_id', $staff->id)
            ->where('expires_at', '>', Carbon::now())
            ->orderBy('expires_at', 'asc')
            ->get(['uuid', 'code', 'expires_at', 'points']);

        $codes = $codes->map(function ($item) use($staff) {
            if (isset($item['expires_at']) && $item['expires_at'] != null) {
                $item['expires_at']  = Carbon::parse(
                    $item['expires_at'],
                    config('app.timezone')
                )->setTimezone($staff->getTimezone());
            }

            return $item;
        });

      return response()->json($codes);
    }

    /**
     * Generate easy to remember code for merchant
     * @bodyParam uuid string required uuid of website. Example: 283ca865-a71c-4d4a-b8cb-8c46c5b3ca57
     * @bodyParam expires string required following value hour , day , week , month.
     * @bodyParam segments integer[] id of segments.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postGenerateMerchantCode(Request $request)
    {
        // $account = app()->make('account');

        $campaign = \Platform\Models\Campaign::withoutGlobalScopes()->whereUuid(request('uuid', 0))->firstOrFail();

        $expires = $request->expires;

        // First, delete existing codes

        Code::query()->where(
            'campaign_id', $campaign->id
        )->where(
            'staff_id', auth('staff')->id()
        )->where(
            'type', 'merchant'
        )->where(
            'expires_at', '<', Carbon::now()
        )->delete();

        // Create new code
        $merchant_code = $this->getUniqueCode('merchant_code', $campaign->id);

        // GET expires_at
        switch ($expires) {
            case 'hour': $expires_at = Carbon::now()->addHours(1); break;
            case 'day': $expires_at = Carbon::now()->addDays(1); break;
            case 'week': $expires_at = Carbon::now()->addWeeks(1); break;
            case 'month': $expires_at = Carbon::now()->addMonths(1); break;
        }

        // Create Code
        $code = new Code;
        $code->campaign_id = $campaign->id;
        $code->staff_id = auth('staff')->user()->id;
        $code->staff_name = auth('staff')->user()->name;
        $code->staff_email = auth('staff')->user()->email;
        $code->type = 'merchant';
        $code->code = $merchant_code;
        $code->expires_at = $expires_at;
        $code->created_by = $campaign->created_by;

        $code->save();

        return response()->json([
            'status' => 'success',
            'code' => $merchant_code
        ]);
    }

    /**
     * Get active merchant code
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getActiveMerchantCode(Request $request)
    {
        $staff = Staff::query()->find(auth('staff')->id());

        $code = Code::query()
            ->where('type', 'merchant')
            ->where('staff_id', $staff->id)
            ->where('expires_at', '>', Carbon::now())
            ->first(['uuid', 'code', 'expires_at']);

        if ($code !== null && $code->expires_at !== null) {
            $code->expires_at = Carbon::parse(
                $code->expires_at,
                config('app.timezone')
            )->setTimezone($staff->getTimezone());
        }

        return response()->json($code);
    }

    /**
     * Make sure code is unique
     *
     * @return boolean
     */
    public function getUniqueCode($type, $campaign_id)
    {
        if ($type == 'customer_code') {
            $customer_code = Core\Secure::getRandom(9, '1234567890000');

            $code_type = 'customer';
        } elseif ($type == 'merchant_code') {
            $customer_code = Generator::generateEn(2);

            $code_type = 'merchant';
        }

        $code = Code::query()
            ->where('campaign_id', $campaign_id)
            ->where('code', $customer_code)
            ->where('type', $code_type)
            ->first();

        if (! $code) {
            return $customer_code;
        } else {
            return $this->getUniqueCode($type, $campaign_id);
        }
    }

    /**
     * Credit customer by number
     * @bodyParam uuid string required uuid of website. Example: 283ca865-a71c-4d4a-b8cb-8c46c5b3ca57
     * @bodyParam number string required customer number.
     * @bodyParam bill_amount integer required.
     * @bodyParam bill_number integer required.
     * @bodyParam segments integer[] id of segments.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postCreditCustomer(Request $request)
    {
        $account = app()->make('account');

        $campaign = \Platform\Models\Campaign::withoutGlobalScopes()
            ->whereUuid(request('uuid', 0))
            ->firstOrFail();

        $customerNumber = str_replace("-", "", $request->number);


        $segments = $request->segments;

        $staff = auth('staff')->user();

        // Find customer by number
        $customer = Customer::query()
            ->where('campaign_id', $campaign->id)
            ->where('active', 1)
            ->when(
                $request->mode === 'customerCard',
                fn($query) => $query->where('card_number', $customerNumber),
                fn($query) => $query->where('customer_number', $customerNumber)
            )->first();

        if (! $customer) {
            return response()->json([
                'status' => 'error',
                'errors' => [
                    'number' => 'Customer not found'
                ]
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $countHistoryToday = History::query()
            ->where('campaign_id', $campaign->id)
            ->where('customer_id', $customer->id)
            ->where('points', '>' , 0)
            ->where('event', '!=', 'Sign up bonus')
            ->whereDate('created_at', Carbon::today())
            ->count();

        if ($countHistoryToday >= $campaign->credit_transaction_limit) {
            return response()->json([
                'status' => 'error',
                'errors' => [
                    'number' => 'The customer has reached the credit transaction limit.'
                ]
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        if ($request->mode === 'customerCard' && $customer->card_status !== 1) {
            $card_statuses = collect(json_decode(file_get_contents(storage_path('json/customers/card_status.json')), true));

            $card_status = $card_statuses->firstWhere('value', $customer->card_status);

            return response()->json([
                'status' => 'error',
                'errors' => [
                    'number' => 'Your membership card status is ' . strtolower($card_status['text']) . '.'
                ]
            ]);
        }

        $response = $this->pointsConversionRule->credit_points(
            $campaign->id,
            floatval($request->bill_amount),
            $campaign->credit_points_mode
        );

        if ($response['status'] === 'error') {
            return response()->json([
                'status' => 'error',
                'message' => 'Please contact website administrator to setup the Conversion Rule.',
                'errors' => [
                    'bill_amount' => 'Please contact website administrator to setup the Conversion Rule.',
                ]
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $points = $response['points'];

        $history = new History;
        $history->customer_id = $customer->id;
        $history->campaign_id = $campaign->id;
        $history->staff_id = $staff->id;
        $history->staff_name = $staff->name;
        $history->staff_email = $staff->email;
        $history->points = $points;
        $history->points_expired_date = Carbon::now()->addDays($campaign->user->points_expiry->points_expiry ?? config('system.default_points_expiry'));
        $history->bill_number = $request->bill_number;
        $history->bill_amount = $request->bill_amount;
        $history->event = 'Credited by staff member';
        $history->created_by = $campaign->created_by;

        $history->save();

        $emailTemplate = EmailTemplate::query()
            ->where('name', 'customer_credit_point')
            ->where('created_by', $campaign->created_by)
            ->first();

        $login_url = $campaign->url . '/login';

        $cta_button = '<a href="'.$login_url.'" class="button button-primary" target="_blank">Login</a>';

        $variableTemplate = [
            '{{ website_name }}',
            '{{ website_url }}',
            '{{ login_button }}',
            '{{ login_url }}',
            '{{ point_got }}',
            '{{ current_point }}',
            '{{ event }}',
            '{{ name_of_user }}',
            '{{ email_of_user }}',
        ];

        $variableChange = [
            $campaign->name,
            $campaign->url,
            $cta_button,
            $login_url,
            $points,
            $customer->points,
            'Credited by staff member',
            $customer->name,
            $customer->email,
        ];

        $email = new \stdClass;

        $email->website_name = $campaign->name;
        $email->website_url = $campaign->url;
        $email->from_name = $account->app_mail_name_from;
        $email->from_email = $account->app_mail_address_from;

        $email->to_name = $customer->name;
        $email->to_email = $customer->email;
        $email->subject = str_replace($variableTemplate, $variableChange, $emailTemplate->subject);
        $email->template = str_replace($variableTemplate, $variableChange, $emailTemplate->template);

        // Mail::send(new \App\Mail\TemplateMail($email));
        ProcessMerchantSendMail::dispatch(
            $email,
            $campaign->smtp_service_id
        )->onQueue('emails');


        if ($this->notifPusher->isAvailable()) {
            $this->notifPusher->pointCredited($customer, $points, 'Credited by staff member');
        }

        // Segments
        if (is_array($segments) && count($segments) > 0) {
            $history->segments()->sync($segments);
        }

        return response()->json([
            'status' => 'success'
        ]);
    }
}
