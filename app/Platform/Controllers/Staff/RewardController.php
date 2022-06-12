<?php

namespace Platform\Controllers\Staff;

use Exception;
use App\Customer;
use Platform\Models\Code;
use App\Libraries\Formula;
use Illuminate\Http\Request;
use Platform\Models\History;
use App\Models\EmailTemplate;
use Barzo\Password\Generator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessMerchantSendMail;
use Platform\Models\RedeemTransactionRule;
use App\Repositories\NotifPusherRepositories;
use App\Staff;
use Illuminate\Support\Carbon;

/**
 * @group Staff Reward
 *
 * Endpoints for staff related to reward redemption procedures.
 * @package Platform\Controllers\Staff
 */
class RewardController extends Controller {

    private $notifPusher;

    public function __construct(NotifPusherRepositories $notifPusher)
    {
        $this->notifPusher = $notifPusher;
    }
    /*
     |--------------------------------------------------------------------------
     | Rewards related functions
     |--------------------------------------------------------------------------
     */

    /**
     * Get all rewards from campaign
     * @queryParam uuid string required uuid of website. Example: 283ca865-a71c-4d4a-b8cb-8c46c5b3ca57
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getRewards(Request $request)
    {
        $campaign = \Platform\Models\Campaign::withoutGlobalScopes()
            ->whereUuid($request->query('uuid', 0))
            ->first();

        $rewards = [];

        if ($campaign) {
            $rewards = $campaign->getActiveRewards();
        }

        return response()->json($rewards);
    }

    /**
     * Validate if link token is (still) valid
     * @queryParam uuid string required uuid of website. Example: 283ca865-a71c-4d4a-b8cb-8c46c5b3ca57
     * @queryParam token string required token of user.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postValidateLinkToken(Request $request)
    {
        $campaign = \Platform\Models\Campaign::withoutGlobalScopes()->whereUuid(request('uuid', 0))->firstOrFail();

        $token = $request->token;

        // Validate token
        $code = Code::query()
            ->where('code', $token)
            ->where('campaign_id', $campaign->id)
            ->where('type', 'reward_token')
            ->where('expires_at', '>', Carbon::now())
            ->first();

        $tokenIsValid = false;
        $customer = null;
        $reward = null;

        if ($code) {
            $tokenIsValid = true;
            $customer = $code->customer;
            $reward = $code->reward->uuid;
        }

        return response()->json([
            'status' => 'success',
            'tokenIsValid' => $tokenIsValid,
            'customer' => $customer,
            'reward' => $reward
        ], 200);
    }

    /**
     * Push redeemed reward to broadcast channel
     * @bodyParam uuid string required uuid of website. Example: 283ca865-a71c-4d4a-b8cb-8c46c5b3ca57
     * @bodyParam token string required token of user.
     * @bodyParam reward string required uuid of rewards. Example: 703fcac9-9ffa-4527-9b3d-c9549e02a353
     * @bodyParam segments integer[] id of segments.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postRedeemRewardByToken(Request $request)
    {
        try {
            $campaign = \Platform\Models\Campaign::withoutGlobalScopes()->whereUuid(request('uuid', 0))->firstOrFail();
            $token = $request->token;
            $reward = $request->reward;
            $reward = \Platform\Models\Reward::whereUuid(request('reward', 0))->firstOrFail();
            $segments = $request->segments;

            // Validate token
            $code = Code::query()
                ->where('code', $token)
                ->where('campaign_id', $campaign->id)
                ->where('type', 'reward_token')
                ->where('expires_at', '>', Carbon::now())
                ->first();

            // Verify if customer has enough points
            $new_points_customer = $code->customer->points - $reward->points_cost;

            if ($new_points_customer < 0) {
                return response()->json([
                    'status' => 'error',
                    'errors' => [
                        'reward' => 'Not enough points to redeem this offer.'
                    ]
                ], 422);
            }

            $reward_used_by_customer = History::query()
                ->where('customer_id', $code->customer_id)
                ->where('campaign_id', $campaign->id)
                ->where('reward_id', $reward->id)
                ->count();

            if (! $reward->multiple_time) {
                if ($reward_used_by_customer >= 1) {
                    return response()->json([
                        'status' => 'error',
                        'errors' => array(
                            'reward' => 'You have already availed this reward. Please explore other rewards.',
                        ),
                    ], 422);
                }
            }

            if ($code) {

                DB::beginTransaction();

                $history = new History;
                $history->customer_id = $code->customer_id;
                $history->campaign_id = $campaign->id;
                $history->staff_id = auth('staff')->user()->id;
                $history->staff_name = auth('staff')->user()->name;
                $history->staff_email = auth('staff')->user()->email;
                $history->reward_id = $reward->id;
                $history->reward_title = $reward->title;
                $history->points = -$reward->points_cost;
                $history->event = 'Redeemed with QR code';
                $history->created_by = $campaign->created_by;

                $history->save();

                $this->assignPointsUsage($reward->points_cost, $code->customer_id);

                // Segments
                if (is_array($segments) && count($segments) > 0) {
                    $history->segments()->sync($segments);
                }

                // Delete
                $code->delete();

                // Increment reward redemptions
                $reward->increment('number_of_times_redeemed');

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

                $data = [
                    'reward' => $reward->title_with_points,
                    'points' => $new_points_customer
                ];

                $pusher->trigger($code->code, 'redeemed', $data);


                if ($this->notifPusher->isAvailable()) {
                    $this->notifPusher->redeemReward($code->customer, $reward);
                    $this->notifPusher->pointRedeemed($code->customer, $reward->points_cost, $reward->title);
                }

                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'points' => $new_points_customer
                ]);
            }

        } catch (Exception $exception) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Generate easy to remember code for merchant
     * @bodyParam uuid string required uuid of website. Example: 283ca865-a71c-4d4a-b8cb-8c46c5b3ca57
     * @bodyParam expires string required following value hour , day , week , month.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postGenerateMerchantCode(Request $request)
    {
        $campaign = \Platform\Models\Campaign::withoutGlobalScopes()->whereUuid(request('uuid', 0))->firstOrFail();

        $expires = $request->expires;

        // First, delete existing codes
        Code::query()
            ->where('type', 'merchant')
            ->where('staff_id', auth('staff')->id())
            ->delete();

        // Create new code
        $merchant_code = $this->getUniqueCode('merchant_code', $campaign->id);

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

        $code = Code::select('uuid', 'code', 'expires_at')
            ->where('type', 'merchant')
            ->where('staff_id', $staff->id)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if ($code !== null && $code->expires_at !== null) {
            $code->expires_at = Carbon::parse($code->expires_at, config('app.timezone'))
                ->setTimezone($staff->getTimezone());
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
        if ($type == 'merchant_code') {
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
     * Redeem reward with customer number
     * @bodyParam uuid string required uuid of website. Example: 283ca865-a71c-4d4a-b8cb-8c46c5b3ca57
     * @bodyParam reward string required uuid of rewards. Example: 703fcac9-9ffa-4527-9b3d-c9549e02a353
     * @bodyParam mode string required following value number, card_number.
     * @bodyParam number string required customer number or card_number.
     * @bodyParam segments integer[] id of segments.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postRedeemReward(Request $request)
    {
        try {
            $account = app()->make('account');

            $campaign = \Platform\Models\Campaign::withoutGlobalScopes()
                ->whereUuid($request->input('uuid', 0))
                ->firstOrFail();

            $customerNumber = preg_replace('/\D+/', '', $request->number);

            $segments = $request->segments;

            $mode = $request->mode;

            $customer = Customer::withoutGlobalScopes()
                ->where('campaign_id', $campaign->id)
                ->where('active', 1);

            if ($mode === 'number') {
                // Find customer by number
                $customer = $customer->where('customer_number', $customerNumber)->first();

                $event = 'Redeemed with customer number';
            } elseif ($mode === 'redeem_transaction'){
                $customer = $customer->where('customer_number', $customerNumber)->first();

                $event = 'Redeem for transaction discount';
            } else {
                // Find customer by card number
                $customer = $customer->where('card_number', $customerNumber)->first();

                $event = 'Redeemed with customer card';
            }

            if (! $customer) {
                return response()->json([
                    'status' => 'error',
                    'errors' => ['number' => 'Customer not found']
                ], 422);
            }

            if ($mode == 'redeem_transaction') {
                // Check if receipt number already redeemed.

                $checkReceiptNumber = History::query()
                    ->where('bill_number', $request->reward['bill_number'])
                    ->where('event', $event)
                    ->first();

                if ($checkReceiptNumber) {
                    return response()->json([
                        'status' => 'error',
                        'errors' => [
                            'bill_number' => 'Receipt number already redeemed.'
                        ]
                    ], 422);
                }

                $redeemTransactionRule = RedeemTransactionRule::whereUser($customer->created_by)->first();

                if (! ($customer->points >= $redeemTransactionRule->minimum_points)) {
                    return response()->json([
                        'status' => 'error',
                        'errors' => [
                            'number' => 'Customer points do not reach the minimum points to exchange points for transaction discounts.'
                        ]
                    ], 422);
                }

                // Get Customer Total of Reedem Today
                $totalCurrentRedeem = \Platform\Models\History::query()
                    ->where('customer_id', $customer->id)
                    ->where('event', $event)
                    ->whereDate('created_at', Carbon::today())
                    ->count();

                // Checks if customer redeem not exceeds max redemption per day rule
                if ($totalCurrentRedeem >= $redeemTransactionRule->maximum_redeem){
                    return response()->json([
                        'status' => 'error',
                        'errors' => [
                            'number' => 'The customer has reached the maximum point redemption limit.'
                        ]
                    ], 422);
                }

                $currentPoint = $customer->points;

                if (! $currentPoint) {
                    return response()->json([
                        'status' => 'error',
                        'errors' => [
                            'number' => 'Not Enough Points to redeem this offer.'
                        ]
                    ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
                }

                $partial_points = (int) $request->reward['partial_points'];

                if ($partial_points > $currentPoint) {
                    return response()->json([
                        'status' => 'error',
                        'errors' => [
                            'partial_points' => "The point to redeem may not be greater than {$currentPoint}."
                        ]
                    ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
                }

                $pointUsed = ($partial_points) ? $partial_points : $currentPoint;

                $fractionDigits = $request->reward['fraction_digits'];
                $billAmount = floatval($request->reward['bill_amount']);
                $result = Formula::discount_transaction_with_points(
                    $pointUsed,
                    $billAmount,
                    $redeemTransactionRule->value
                );

                $reward = new \stdClass;
                $reward->title = 'Redeem for transacton discount';
                $reward->id = null;
                $reward->points_cost = $result['point_cost'];

                $response = [
                    'bill_amount' => number_format($billAmount, $fractionDigits),
                    'amount_left' => number_format($result['amount_left'], $fractionDigits),
                    'amount_return' => number_format($result['amount_return'], $fractionDigits),
                    'discount_point' => number_format($result['discount'], $fractionDigits),
                    'point_cost' => $result['point_cost'],
                    'point_left' => $currentPoint - $result['point_cost']
                ];

            } else {
                $response = true;
                $reward = $request->reward;
                $reward = \Platform\Models\Reward::whereUuid(request('reward', 0))->firstOrFail();

                // Check if customer has sufficient balance
                if ($reward->points_cost > $customer->points) {
                    return response()->json([
                        'status' => 'error',
                        'errors' => array(
                            'reward' => 'Not enough points to redeem this offer.',
                        )
                    ], 422);
                }

                // Check if reward just claim once
                $reward_used_by_customer = History::query()
                    ->where('customer_id', $customer->id)
                    ->where('campaign_id', $campaign->id)
                    ->where('reward_id', $reward->id)
                    ->count();

                if (! $reward->multiple_time) {
                    if ($reward_used_by_customer >= 1) {
                        return response()->json([
                            'status' => 'error',
                            'errors' => array(
                                'reward' => 'You have already availed this reward. Please explore other rewards.',
                            )
                        ], 422);
                    }
                }
            }

            DB::beginTransaction();

            $history = new History;
            $history->customer_id = $customer->id;
            $history->campaign_id = $campaign->id;
            $history->staff_id = auth('staff')->user()->id;
            $history->staff_name = auth('staff')->user()->name;
            $history->staff_email = auth('staff')->user()->email;
            $history->reward_id = $reward->id;
            $history->reward_title = $reward->title;
            $history->points = -$reward->points_cost;

            if ($mode == 'redeem_transaction') {
                $history->bill_number = $request->reward['bill_number'];
                $history->bill_amount = $request->reward['bill_amount'];
            }

            $history->event = $event;
            $history->created_by = $campaign->created_by;

            $history->save();

            $this->assignPointsUsage($reward->points_cost, $customer->id);

            $emailTemplate = EmailTemplate::where('name', 'customer_redeem_point')->where('created_by', $campaign->created_by)->first();

            $variableTemplate = [
                '{{ website_name }}',
                '{{ website_url }}',
                '{{ reward }}',
                '{{ current_point }}',
                '{{ name_of_user }}',
                '{{ email_of_user }}'
            ];

            $variableChange = [
                $campaign->name,
                $campaign->url,
                $reward->title,
                $customer->points -$reward->points_cost,
                $request->name,
                $request->email
            ];

            $email = new \stdClass;

            $email->website_name = $campaign->name;
            $email->website_url = $campaign->url;
            $email->from_name = $account->app_mail_name_from;
            $email->from_email = $account->app_mail_address_from;

            $email->to_name = $customer->name;
            $email->to_email = $customer->email;
            $email->subject = str_replace($variableTemplate, $variableChange ,$emailTemplate->subject);
            $email->template = str_replace($variableTemplate, $variableChange ,$emailTemplate->template);

            // Mail::send(new \App\Mail\TemplateMail($email));
            ProcessMerchantSendMail::dispatch(
                $email,
                $campaign->smtp_service_id
            )->onQueue('emails');


            if ($this->notifPusher->isAvailable()) {
                $this->notifPusher->redeemReward($customer, $reward);
                $this->notifPusher->pointRedeemed($customer, $reward->points_cost, $reward->title);
            }

            // Segments
            if (is_array($segments) && count($segments) > 0) {
                $history->segments()->sync($segments);
            }

            // Increment reward redemptions
            if ($mode !== 'redeem_transaction') {
                $reward->increment('number_of_times_redeemed');
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'response' => $response
            ]);
        } catch (Exception $exception) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function assignPointsUsage($points_cost, $customer_id)
    {
        $remaining_cost = $points_cost;

        $histories = History::query()->whereRaw('(points - points_usage) > 0')
            ->where('reward_id', NULL)
            ->where('customer_id', $customer_id)
            ->where('points_expired_date', '>', Carbon::now())
            ->orderBy('points_expired_date', 'ASC')
            ->get();

        foreach($histories as $history) {
            if ($remaining_cost <= 0) {
                break;
            }

            $remaining_points = $history->points - $history->points_usage;

            if ($remaining_points > 0) {
                // If there is still remaining points
                if ($remaining_points < $remaining_cost) {
                    History::query()
                        ->where('id', $history->id)
                        ->update([
                            'points_usage' => $remaining_points + $history->points_usage
                        ]);

                    $remaining_cost -= $remaining_points;
                } else {
                    History::query()
                        ->where('id', $history->id)
                        ->update([
                            'points_usage' => $remaining_cost + $history->points_usage
                        ]);

                    $remaining_cost = 0;
                }
            }
        }
    }
}
