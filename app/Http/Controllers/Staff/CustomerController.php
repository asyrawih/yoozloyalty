<?php

namespace App\Http\Controllers\Staff;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Platform\Models\History;
use App\Jobs\ProcessSendMail;
use App\Models\EmailTemplate;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessMerchantSendMail;
use Illuminate\Support\Facades\Validator;
use App\Repositories\NotifPusherRepositories;
use App\Repositories\Staff\CustomerRepository;
use App\Repositories\Campaign\CampaignRepository;
use Illuminate\Support\Carbon;

class CustomerController extends Controller
{
    private CampaignRepository $campaign;
    private CustomerRepository $customer;
    private NotifPusherRepositories $notifPusher;

    public function __construct(
        CampaignRepository $campaign,
        CustomerRepository $customer,
        NotifPusherRepositories $notifPusher
    ) {
        $this->campaign = $campaign;
        $this->customer = $customer;
        $this->notifPusher = $notifPusher;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $campaign = $this->campaign->readUuid(request('uuid'));

        $page = request('page', 1);
        $perPage = request('perPage', 10);
        $search = request('search', '');

        $response = [
            'status' => 'success',
            'message' => NULL,
            'data' => [],
            'meta' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => 0,
            ],
        ];

        if (! $campaign) {
            return response()->json($response);
        }

        $customers = $this->customer->getByCampaign($campaign->id, $page, $perPage, $search);

        $response['data'] = $customers['data'];
        $response['meta'] = $customers['meta'];

        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $account = app()->make('account');

            $campaign = $this->campaign->readUuid($request->uuid);

            // Check if account limitations are reached
            $maxCustomer = $campaign->user->plan_limitations['customers'];
            $currentCustomer = count($campaign->user->customers);

            if ($currentCustomer > $maxCustomer) {
                return response()->json([
                    'status' => 'error',
                    'error' => 'limitation_reached'
                ], 400);
            }

            $rawCustomerNumber = explode(' ', $request->customer_number);

            $isdCode = $rawCustomerNumber[0];

            $customerNumber = '';

            foreach ($rawCustomerNumber as $index => $value) {
                if ($index !== 0) {
                    $customerNumber .= $value;
                }
            }

            if ($customerNumber) {
                $request->merge([
                    'customer_number' => str_replace($isdCode, '', preg_replace('/\D+/', '', $customerNumber))
                ]);
            }

            $validate = Validator::make($request->all(), [
                'name' => 'required|min:2|max:32',
                'email' => [
                    'required',
                    'email',
                    'max:64',
                    Rule::unique('customers')->where(function ($query) use ($campaign) {
                        return $query->where('campaign_id', $campaign->id);
                    })
                ],
                'customer_number' => 'required|', Rule::unique('customers')->where(function ($query) use ($campaign) {
                    return $query->where('campaign_id', $campaign->id);
                }),
                'card_number' => [
                    'nullable',
                    'digits:16',
                    Rule::unique('customers')->where(function ($query) use ($campaign) {
                        return $query->where('campaign_id', $campaign->id);
                    }),
                ],
                'password' => 'required',
                'avatar' => 'sometimes|image|mimes:jpg,jpeg,png|max:1024',
            ]);

            if ($validate->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => NULL,
                    'errors' => $validate->errors()
                ], 400);
            }

            if ($this->customer->checkCustomerNumber($request->customer_number, $campaign->id)) {
                return response()->json([
                    'status' => 'error',
                    'errors' => [
                        'customer_number' => [
                            'Customer number already been taken'
                        ]
                    ]
                ], 422);
            }

            $locale = request('locale', config('system.default_language'));

            app()->setLocale($locale);

            $language = ($request->language !== null) ? $request->language : config('system.default_language');

            $timezone = ($request->timezone !== null) ? $request->timezone : config('system.default_timezone');

            $verification_code = Str::random(32);

            $request->merge([
                'account_id' => $account->id,
                'campaign_id' => $campaign->id,
                'created_by' => $campaign->created_by,
                'locale' => $locale,
                'language' => $language,
                'timezone' => $timezone,
                'verification_code' => $verification_code,
                'ip_address' => $request->ip(),
            ]);

            $user = $this->customer->store($request->all());

            // Add points for signing up
            if ($campaign->signup_bonus_points > 0) {
                $history = new History;

                $history->customer_id = $user->id;
                $history->campaign_id = $campaign->id;
                $history->created_by = $campaign->created_by;
                $history->event = 'Sign up bonus';
                $history->points = $campaign->signup_bonus_points;
                $history->points_expired_date = Carbon::now()->addDays($campaign->user->points_expiry->points_expiry ?? config('system.default_points_expiry'));
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
                    '{{ email_of_user }}'
                ];

                $variableChange = [
                    $campaign->name,
                    $campaign->url,
                    $cta_button,
                    $login_url,
                    $campaign->signup_bonus_points,
                    $campaign->signup_bonus_points,
                    'Sign up bonus',
                    $request->name,
                    $request->email
                ];

                $email = new \stdClass;

                $email->website_name = $campaign->name;
                $email->website_url = $campaign->url;
                $email->from_name = $account->app_mail_name_from;
                $email->from_email = $account->app_mail_address_from;
                $email->to_name = $request->name;
                $email->to_email = $request->email;
                $email->subject = str_replace($variableTemplate, $variableChange ,$emailTemplate->subject);
                $email->template = str_replace($variableTemplate, $variableChange ,$emailTemplate->template);
                $email->cta_button = $cta_button;
                $email->cta_url = $login_url;

                // Mail::send(new \App\Mail\TemplateMail($email));

                ProcessMerchantSendMail::dispatch(
                    $email,
                    $campaign->smtp_service_id
                )->onQueue('emails');
            }

            $emailTemplate = EmailTemplate::query()
                ->where('name', 'customer_registeration')
                ->where('created_by', $campaign->created_by)
                ->first();

            $verification_url = route('customer.verification.verify',[
                'id' => $user->id,
                'hash' => $user->verification_code
            ]);

            $cta_button = '<a href="'.$verification_url.'" class="button button-primary" target="_blank">Verify</a>';

            $variableTemplate = ['{{ website_name }}', '{{ website_url }}', '{{ verification_button }}', '{{ verification_url }}', '{{ name_of_user }}', '{{ email_of_user }}'];

            $variableChange = [$campaign->name, $campaign->url, $cta_button, $verification_url, $request->name, $request->email];

            $email = new \stdClass;

            $email->website_name = $campaign->name;
            $email->website_url = $campaign->url;
            $email->from_name = $account->app_mail_name_from;
            $email->from_email = $account->app_mail_address_from;

            $email->to_name = $request->name;
            $email->to_email = $request->email;
            $email->subject = str_replace($variableTemplate, $variableChange ,$emailTemplate->subject);
            $email->template = str_replace($variableTemplate, $variableChange ,$emailTemplate->template);

            ProcessMerchantSendMail::dispatch(
                $email,
                $campaign->smtp_service_id
            )->onQueue('emails');


            if ($this->notifPusher->isAvailable()) {
                $this->notifPusher->registerCustomer($user);
                $this->notifPusher->customerWelcomeMessage($user, $campaign->name);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Customer has been added.',
            ]);

        } catch (Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $customer = $this->customer->getByUuid($id);

        $response = [
            'status' => 'success',
            'message' => NULL,
            'data' => [
                'points' => 0,
				'histories' => [],
            ],
        ];

        if (! $customer) {
            return response()->json($response);
        }

        $response['data']['points'] = $customer->points;
        $response['data']['histories'] = $customer->getHistory();

        return response()->json($response);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return response()->json([
            'status' => 'error',
            'message' => 'NOT IMPLEMENTED'
        ], JsonResponse::HTTP_NOT_IMPLEMENTED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return response()->json([
            'status' => 'error',
            'message' => 'NOT IMPLEMENTED'
        ], JsonResponse::HTTP_NOT_IMPLEMENTED);
    }
}
