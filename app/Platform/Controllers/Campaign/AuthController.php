<?php

namespace Platform\Controllers\Campaign;

use App\Customer;
use App\Models\Otp;
use Platform\Models\History;
use Platform\Controllers\Core;
use App\Jobs\ProcessMerchantSendMail;
use App\Jobs\ProcessSendMail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use App\Models\EmailTemplate;
use App\Repositories\EmailTemplateRepository;
use App\Repositories\NotifPusherRepositories;
use Exception;
use Illuminate\Support\Facades\Mail;

/**
 * @group Customer Auth
 *
 * Endpoints for customer authentication
 * @package Platform\Controllers\Campaign
 */
class AuthController extends \App\Http\Controllers\Controller
{
    private NotifPusherRepositories $notifPusher;
    private EmailTemplateRepository $emailTemplate;

    public function __construct(
        NotifPusherRepositories $notifPusher,
        EmailTemplateRepository $emailTemplate
    ) {
        $this->notifPusher = $notifPusher;
        $this->emailTemplate = $emailTemplate;
    }
    /*
    |--------------------------------------------------------------------------
    | Authorization Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling authentication related
    | features like registration, login, logout and password reset.
    | It's designed for /api/ use with JSON responses.
    |
    */

    /**
     * Handle user registration.
     *
     * @bodyParam uuid string required uuid of website. Example: 283ca865-a71c-4d4a-b8cb-8c46c5b3ca57
     * @bodyParam name string required Full name of the user. Example: John Doe
     * @bodyParam email string required User's email. Example: johndoe@internet.com
     * @bodyParam country_isd_code integer required User's phone number. Example: 62 , 91
     * @bodyParam customer_number integer required User's phone number. Example: 08123456789
     * @bodyParam password string required Password. Example: password
     * @bodyParam terms boolean required : 0, 1 Example: 0 , 1
     *
     * @unauthenticated
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function register(Request $request)
    {
        $account = app()->make('account');

        $campaign = \Platform\Models\Campaign::withoutGlobalScopes()
            ->whereUuid(request('uuid', 0))
            ->firstOrFail();

        $numbers = explode(" ", $request->customer_number);

        $isdCode = $numbers[0];

        $phoneNumber = '';

        foreach ($numbers as $key => $number) {
            if ($key != 0) {
                $phoneNumber .= $number;
            }
        }

        if ($phoneNumber) {
            $request->customer_number = str_replace($isdCode, "", preg_replace('/\D+/', '', $phoneNumber));
        } else {
            $request->customer_number = $request->customer_number;
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
            'password'  => 'required',
            'terms' => 'accepted',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validate->errors()
            ], 422);
        }

        $customer = Customer::query()
            ->where('customer_number', $request->customer_number)
            ->where('campaign_id', $campaign->id)
            ->first();

        if ($customer) {
            return response()->json([
                'status' => 'error',
                'errors' => [
                    'customer_number' => [
                        'Customer number already been taken'
                    ]
                ]
            ], 422);
        }

        // Check if account limitations are reached
        $max = $campaign->user->plan_limitations['customers'];

        $count = count($campaign->user->customers);

        if ($count > $max && !env('APP_DEMO', false)) {
            $email = new \stdClass;
            $email->app_name = $account->app_name;
            $email->app_url = '//' . $account->app_host;
            $email->from_name = $account->app_mail_name_from;
            $email->from_email = $account->app_mail_address_from;
            $email->to_name = $campaign->user->name;
            $email->to_email = $campaign->user->email;
            $email->subject = "Account limitation reached";
            $email->body_top = "A user (" . $request->email . ") could not sign up on https:" . $campaign->url . " because the maximum amount of customers (" . $max . ") has been reached.";
            $email->cta_label = "Upgrade account";
            $email->cta_url = '//' . $account->app_host . '/go#/billing';
            $email->body_bottom = "Update your subscription to allow more customers to sign up.";

            ProcessSendMail::dispatch($email)->onQueue('emails');

            return response()->json([
                'status' => 'error',
                'error' => 'limitation_reached'
            ], 422);
        }

        DB::beginTransaction();

        $locale = request('locale', config('system.default_locale'));

        app()->setLocale($locale);

        $language = ($request->language !== null) ? $request->language : config('system.default_language');

        $timezone = ($request->timezone !== null) ? $request->timezone : config('system.default_timezone');

        $verification_code = Str::random(32);

        try {
            $locale = request('locale', config('system.default_locale'));

            app()->setLocale($locale);

            $language = ($request->language !== null) ? $request->language : config('system.default_language');


            $timezone = ($request->timezone !== null) ? $request->timezone : config('system.default_timezone');

            $verification_code = Str::random(32);

            $user = new Customer;
            $user->account_id = $account->id;
            $user->campaign_id = $campaign->id;
            $user->created_by = $campaign->created_by;
            $user->role = 1;
            $user->active = 1;
            $user->country_code = $request->country_code;
            $user->country_isd_code = $request->country_isd_code;
            $user->customer_number = $request->customer_number;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->language = $language;
            $user->locale = $locale;
            $user->timezone = $timezone;
            $user->signup_ip_address = request()->ip();
            $user->verification_code = $verification_code;
            $user->expired_date = Carbon::now()->addYear()->format('Y-m-d H:i:s');
            $user->save();

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

                $this->emailTemplate->customerCreditPoint(
                    $campaign,
                    $user,
                    $campaign->signup_bonus_points,
                    'Sign up bonus'
                );
            }
        } catch (Exception $exception) {
            DB::rollback();

            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage()
            ], 500);
        }

        DB::commit();

        $this->emailTemplate->sendCustomerRegistration($campaign, $user);

        if ($this->notifPusher->isAvailable()) {
            $this->notifPusher->registerCustomer($user);
            $this->notifPusher->customerWelcomeMessage($user, $campaign->name);
        }

        return response()->json([
            'status' => 'success'
        ]);
    }

    /**
     * Make sure customer number is unique
     *
     * @return boolean
     */
    public function ensureNumberIsUnique(Customer $customer)
    {
        $user = Customer::query()
            ->where('id', '<>', $customer->id)
            ->where('created_by', $customer->created_by)
            ->where('customer_number', $customer->customer_number)
            ->first();

        if (!$user) {
            return true;
        } else {
            $customer_number = Core\Secure::getRandom(9, '1234567890');

            $customer->customer_number = $customer_number;

            $customer->save();

            $this->ensureNumberIsUnique($customer);
        }
    }

    /**
     * Handle user login.
     *
     * @bodyParam uuid string required uuid of website. Example: 283ca865-a71c-4d4a-b8cb-8c46c5b3ca57
     * @bodyParam email string required User's email. Example: johndoe@internet.com
     * @bodyParam password string required Password. Example: password
     *
     * @unauthenticated
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required|email|max:64',
            'password' => 'required|min:6|max:24'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validate->errors()
            ], 422);
        }

        $account = app()->make('account');

        $campaign = \Platform\Models\Campaign::withoutGlobalScopes()->whereUuid(request('uuid', 0))->firstOrFail();

        $credentials = $request->only('email', 'password');

        $credentials['active'] = 1;

        $credentials['account_id'] = $account->id;

        $credentials['campaign_id'] = $campaign->id;


        if ($token = $this->guard()->attempt($credentials, true)) {
            $user = Customer::query()->find(auth('customer')->id());

            $user->logins = $user->logins + 1;
            $user->last_login_ip_address =  request()->ip();
            $user->last_login = Carbon::now('UTC');
            $user->save();

            return response()->json([
                'status' => 'success',
                'token' => $token
            ])->header('Authorization', $token);
        }

        return response()->json([
            'error' => 'login_error'
        ], 401);
    }

    /**
     * Handle user login.
     *
     * @bodyParam email string required User's email. Example: johndoe@internet.com
     * @bodyParam password string required Password. Example: password
     *
     * @unauthenticated
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginApp(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required|max:64',
            'password' => 'required|min:6'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validate->errors()
            ], 422);
        }

        $account = app()->make('account');

        $customer = Customer::where('email', $request->email)
            ->orWhere('customer_number', $request->email)
            ->first();

        if (! $customer) {
            return response()->json([
                'status' => 'login_error'
            ]);
        }

        $credentials = $request->only('email', 'password');

        $credentials['active'] = 1;

        $credentials['account_id'] = $account->id;

        $credentials['campaign_id'] = $customer->campaign_id;

        if ($token = $this->guard()->attempt($credentials, true)) {
            $user = Customer::query()->find(auth('customer')->id());

            $user->logins = $user->logins + 1;
            $user->last_login_ip_address =  request()->ip();
            $user->last_login = Carbon::now('UTC');
            $user->save();

            return response()->json([
                'status' => 'success',
                'token' => $token
            ])->header('Authorization', $token);
        }

        if (
            $user = Customer::where([
                'customer_number' => $request->email,
                'encrypted_password' => $request->password
            ])->first()
        ) {
            $token = $this->guard()->login($user);
            $user->logins = $user->logins + 1;
            $user->last_login_ip_address =  request()->ip();
            $user->last_login = Carbon::now('UTC');
            $user->save();

            return response()->json([

                'status' => 'success',
                'token' => $token
            ])->header('Authorization', $token);
        }

        return response()->json([
            'status' => 'login_error'
        ]);
    }

    public function updateEncryptedPassword(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'id' => 'required',
            'phone' => 'required',
            'password' => 'required|min:6'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validate->errors()
            ], 422);
        }

        $customer = Customer::where('customer_number', $request->phone)
            ->find($request->id);

        if (!$customer)
            return response()->json([
                'status' => 'error',
                'error' => 'Invalid ID or Customer Phone Number'
            ]);

        $customer->encrypted_password = $request->password;
        $customer->save();

        return response()->json([
            'status' => 'success'
        ]);
    }

    /**
     * Handle user logout.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function logout()
    {
        $this->guard()->logout();

        return response()->json([
            'status' => 'success',
            'msg' => 'Logged out successfully.'
        ]);
    }

    /**
     * Refresh authorization token.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function refresh()
    {
        try {
            $token = $this->guard()->refresh();
        } catch (\Exception $e) {
            return response()->json(['error' => 'refresh_token_error'], 401);
        }

        return response()
            ->json(['status' => 'successs', 'token' => $token], 200)
            ->header('Authorization', $token);
    }

    /**
     * Send a password reset email.
     * @bodyParam uuid string required uuid of website. Example: 283ca865-a71c-4d4a-b8cb-8c46c5b3ca57
     * @bodyParam email string required User's email. Example: johndoe@internet.com
     * @unauthenticated
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function passwordReset(Request $request)
    {
        $campaign = \Platform\Models\Campaign::withoutGlobalScopes()->whereUuid(request('uuid', 0))->firstOrFail();

        $account = app()->make('account');

        $validate = Validator::make($request->all(), [
            'email' => 'required|email|max:64'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validate->errors()
            ], 422);
        }

        $locale = request('locale', config('system.default_language'));

        app()->setLocale($locale);

        $user = Customer::withoutGlobalScopes()
            ->where('email', $request->email)
            ->where('campaign_id', $campaign->id)
            ->where('active', 1)
            ->first();

        if ($user !== null) {

            $token = Str::random(32);

            DB::table('password_resets')
                ->where('email', $user->email)
                ->delete();

            DB::table('password_resets')->insert([
                'email' => $user->email,
                'token' => $token,
                'created_at' => Carbon::now('UTC')
            ]);

            $emailTemplate = EmailTemplate::query()
                ->where('name', 'customer_forgot_password')
                ->where('created_by', $campaign->created_by)
                ->first();

            $forgot_password_url = $campaign->url . '/password/reset/' . $token;

            $cta_button = '<a href="' . $forgot_password_url . '" class="button button-primary" target="_blank">Reset password</a>';

            $variableTemplate = ['{{ website_name }}', '{{ website_url }}', '{{ forgot_password_button }}', '{{ forgot_password_url }}'];

            $variableChange = [$campaign->name, $campaign->url, $cta_button, $forgot_password_url];

            $email = new \stdClass;

            $email->website_name = $campaign->name;
            $email->website_url = $campaign->url;
            $email->from_name = $account->app_mail_name_from;
            $email->from_email = $account->app_mail_address_from;

            $email->to_name = $user->name;
            $email->to_email = $user->email;
            $email->subject = str_replace($variableTemplate, $variableChange, $emailTemplate->subject);
            $email->template = str_replace($variableTemplate, $variableChange, $emailTemplate->template);
            $email->body_top = trans('campaign.reset_password_mail_top');
            $email->cta_button = $cta_button;
            $email->cta_url = $forgot_password_url;
            $email->body_bottom = trans('campaign.reset_password_mail_bottom');

            ProcessMerchantSendMail::dispatch(
                $email,
                $campaign->smtp_service_id
            )->onQueue('emails');
        } else {
            return response()->json([
                'status' => 'error',
                'error' => trans('passwords.user')
            ]);
        }

        return response()->json([
            'status' => 'success'
        ]);
    }

    /**
     * Validate reset password token.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function passwordResetValidateToken(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'token' => 'required|min:32|max:32'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validate->errors()
            ], 422);
        }

        $locale = request('locale', config('system.default_language'));

        app()->setLocale($locale);

        $password_reset = DB::table('password_resets')
            ->select('email')
            ->where('token', $request->token)
            ->where('created_at', '>=', \Carbon\Carbon::now()->addHour(-24)->toDateTimeString())
            ->first();

        if ($password_reset !== null) {
            return response()->json([
                'status' => 'success'
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'error' => 'invalid_token'
            ]);
        }
    }

    /**
     * Update password.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function passwordUpdate(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'token' => 'required|min:32|max:32',
            'password' => 'required|min:8|max:24'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validate->errors()
            ], 422);
        }

        $locale = request('locale', config('system.default_language'));

        app()->setLocale($locale);

        $password_reset = DB::table('password_resets')
            ->select('email')
            ->where('token', $request->token)
            ->where('created_at', '>=', \Carbon\Carbon::now()->addHour(-24)->toDateTimeString())
            ->first();

        if ($password_reset !== null) {

            DB::table('password_resets')->where('token', $request->token)->delete();

            $user = Customer::withoutGlobalScopes()->where('email', $password_reset->email)
                ->where('active', 1)
                ->first();

            if ($user !== null) {
                $user->password = bcrypt($request->password);
                $user->save();

                return response()->json([
                    'status' => 'success'
                ]);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'error' => 'invalid_token'
            ]);
        }
    }

    /**
     * Update profile.
     * @bodyParam uuid string required uuid of website. Example: 283ca865-a71c-4d4a-b8cb-8c46c5b3ca57
     * @bodyParam name string required Full name of the user. Example: John Doe
     * @bodyParam email string required User's email. Example: johndoe@internet.com
     * @bodyParam new_password string  New Password leave empty if you don't want to change the password. Example: password
     * @bodyParam current_password string required Current Password. Example: password
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postUpdateProfile(Request $request)
    {
        $account = app()->make('account');

        $campaign = \Platform\Models\Campaign::withoutGlobalScopes()->whereUuid(request('uuid', 0))->firstOrFail();

        $locale = request('locale', config('system.default_language'));

        app()->setLocale($locale);

        $validate = Validator::make($request->all(), [
            'current_password' => 'required|min:8|max:24',
            'name' => 'required|min:2|max:32',
            'email' => [
                'required', 'email', 'max:64', Rule::unique('customers')->where(function ($query) use ($campaign) {
                    return $query->where('campaign_id', $campaign->id)->where('id', '<>', auth('customer')->user()->id);
                })
            ],
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validate->errors()
            ], 422);
        }

        // Verify password
        if (!Hash::check($request->current_password, auth('customer')->user()->password)) {
            return response()->json([
                'status' => 'error',
                'errors' => ['current_password' => [trans('campaign.current_password_incorrect')]]
            ], 422);
        }

        $user = Customer::query()->find(auth('customer')->id());

        // All good, update profile
        // auth('customer')->user()->name = $request->name;
        // auth('customer')->user()->email = $request->email;
        // auth('customer')->user()->locale = $request->locale;
        // auth('customer')->user()->timezone = $request->timezone;

        $user->name = $request->name;
        $user->email = $request->email;
        $user->locale = $request->locale;
        $user->timezone = $request->timezone;

        // Update password
        if ($request->new_password !== null && $request->new_password != 'null') {
            $validate = Validator::make(
                $request->all(),
                [
                    'new_password'  => 'required|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*[@#$%^&+=!?])(?=.*[0-9]).*$/',
                ],
                [
                    'new_password.regex' => 'Password should have 1 Capital , 1 Small letter , Number and a special symbol'
                ]
            );

            if ($validate->fails()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validate->errors()
                ], 422);
            }

            $user->password = bcrypt($request->new_password);
        }

        $user->save();

        // Update avatar
        if (json_decode($request->avatar_media_changed) === true) {
            $file = $request->file('avatar');

            if ($file !== null) {
                $user->addMedia($file)
                    ->sanitizingFileName(fn ($fileName) => strtolower(str_replace(['#', '/', '\\', ' '], '-', $fileName)))
                    ->toMediaCollection('avatar');
            } else {
                $user->clearMediaCollection('avatar');
            }
        }

        return response()->json([
            'status' => 'success',
            'user' => $user,
        ]);
    }

    /**
     * Get detailed joined website of user.
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function userWebsite(Request $request)
    {
        $user = Customer::withoutGlobalScopes()
            ->where('customer_number', Auth::user('customer')->customer_number)
            ->where('active', 1)
            ->get();

        $data = [];

        for ($i = 0; $i < count($user); $i++) {
            $business = ($user[$i]->campaign && $user[$i]->campaign->business)
                ? $user[$i]->campaign->business
                : null;

            array_push($data, [
                'campaign' => $user[$i]->campaign->name,
                'campaign_uuid' => $user[$i]->campaign->uuid,
                'campaign_slug' => $user[$i]->campaign->slug,
                'logo' => $business->logo,
                'points' => $user[$i]->points,
                'joining_date' => $user[$i]->created_at->format('Y-m-d H:i:s'),
                'expired_date' => $user[$i]->expired_date,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * Get user info .
     * @queryParam uuid string required uuid of website.
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function user(Request $request)
    {
        $campaign = \Platform\Models\Campaign::withoutGlobalScopes()
            ->whereUuid(request('uuid', 0))
            ->firstOrFail();

        $user = Customer::withoutGlobalScopes()
            ->where('email', Auth::user('customer')->email)
            ->where('active', 1)->where('campaign_id', $campaign->id)
            ->firstOrFail();

        $user->touch();

        $user->timezone = $user->getTimezone();
        $user->language = $user->getLanguage();
        $user->locale = $user->getLocale();
        $user->currency = $user->getCurrency();

        $return = [
            'uuid' => $user->uuid,
            'active' => (int) $user->active,
            'avatar' => $user->avatar,
            'name' => $user->name,
            'email' => $user->email,
            'number' => $user->number,
            'email_verified_at' => $user->email_verified_at,
            'points' => (int) $user->points,
            'role' => (int) $user->role,
            'language' => $user->language,
            'locale' => $user->locale,
            'timezone' => $user->timezone,
            'currency' => $user->currency,
            'card_number' => $user->card
        ];

        return response()->json([
            'status' => 'success',
            'data' => $return
        ]);
    }

    /**
     * Request OTP for customer for any purpose
     * @queryParam uuid string required uuid of website. Example: 283ca865-a71c-4d4a-b8cb-8c46c5b3ca57
     * @queryParam purpose string.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function requestOtp(Request $request)
    {
        $validate = Validator::make($request->all(), [
          'purpose' => 'required',
        ]);

        $purpose = $request->input('purpose');

        $campaign = \Platform\Models\Campaign::withoutGlobalScopes()->whereUuid($request->input('uuid'))->firstOrFail();

        $user = \auth()->user();

        $account = app()->make('account');

        $otp = Otp::generateOtp(get_class($user), $user->id, $purpose);

        $emailTemplate = EmailTemplate::where('name', 'customer_otp')->where('created_by', $campaign->created_by)->first();

        $variableTemplate = ['{{ otp }}', '{{ website_name }}'];

        $variableChange = [
            $otp->code,
            $campaign->name
        ];

        $email = new \stdClass;

        $email->website_name = $campaign->name;
        $email->website_url = $campaign->url;
        $email->from_name = $account->app_mail_name_from;
        $email->from_email = $account->app_mail_address_from;

        $email->to_name = $user->name;
        $email->to_email = $user->email;
        $email->subject = str_replace($variableTemplate, $variableChange, $emailTemplate->subject);
        $email->template = str_replace($variableTemplate, $variableChange, $emailTemplate->template);

        // Mail::send(new \App\Mail\TemplateMail($email));
        ProcessMerchantSendMail::dispatch(
            $email,
            $campaign->smtp_service_id
        )->onQueue('emails');

        if (Mail::failures()) {
            return response()->json([
                'status' => 'failed',
                'message' => "Failed to send OTP email."
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'message' => "OTP has been sent to your email. Please check for further confirmation."
        ]);
    }

    /**
     * Verify OTP code
     * @bodyParam code required Otp Code.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyOtp(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'code' => 'required',
        ]);

        $code = $request->code;

        $purpose = $request->purpose;

        $user = auth()->user();

        if (Otp::validate(get_class($user), $user->id, $code)) {
            return response()->json([
                'status' => 'success',
                'message' => "OTP is confirmed"
            ]);
        }

        return response()->json([
            'status' => 'failed',
            'message' => "Wrong OTP code"
        ], 400);
    }

    /**
     * Get guard for logged in user.
     *
     * @return \Illuminate\Support\Facades\Auth
     */
    private function guard()
    {
        return Auth::guard('customer');
    }
}
