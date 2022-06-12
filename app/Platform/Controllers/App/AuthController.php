<?php

namespace Platform\Controllers\App;

use App\User;
use App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Models\EmailTemplate;
use App\Repositories\NotifPusherRepositories;
use App\Repositories\PointsExpiryRepository;
use Exception;
use Illuminate\Support\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * @group Auth
 *
 * Endpoints for authentication
 * @package Platform\Controllers\App
 */
class AuthController extends \App\Http\Controllers\Controller
{
    private $notifPusher;
    private PointsExpiryRepository $pointsExpiry;

    public function __construct(
        NotifPusherRepositories $notifPusher,
        PointsExpiryRepository $pointsExpiry
    ) {
        $this->notifPusher = $notifPusher;
        $this->pointsExpiry = $pointsExpiry;
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
     * @group Merchant Register
     * @unauthenticated
     * @bodyParam name string required name of user.
     * @bodyParam email email required email of user.
     * @bodyParam password string required passwod of user.
     * @bodyParam terms boolean required terms.
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function register(Request $request)
    {


        $rules['name'] = 'required|min:2|max:32';
        $rules['email'] = 'required|email|max:64|unique:users';

        if($request->phone) {
            $rules['phone'] = 'required';
        }

        if($request->birthday) {
            $rules['birthday'] = 'required|date_format:Y-m-d';
        }

        if($request->country_id) {
            $rules['country_id'] = 'required|exists:countries,id';
        }

        if($request->country_id) {
            $rules['city'] = 'required';
        }

        $rules['password'] = 'required|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*[@#$%^&+=!?])(?=.*[0-9]).*$/';
        $rules['terms'] = 'accepted';
        $rules['store_logo'] = 'nullable|image';

        $validate = Validator::make($request->all(), $rules, [
            'password.regex' => 'Password should have 1 Capital, 1 Small letter, Number and a special symbol'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validate->errors()
            ], 422);
        }

        $locale = request('locale', config('system.default_locale'));

        app()->setLocale($locale);

        $account = app()->make('account');
        $language = ($request->language !== null) ? $request->language : config('system.default_language');
        $timezone = ($request->timezone !== null) ? $request->timezone : config('system.default_timezone');

        $currency = config('system.default_currency');

        // Detect currency based on locale
        if (false !== setlocale(LC_ALL, $locale)) {
            $locale_info = localeconv();

            $currency = $locale_info['int_curr_symbol'];
        }

        $verification_code = Str::random(32);

        $trial_days = config('system.trial_days');

        DB::beginTransaction();

        try {
            $user = new User;
            $user->account_id = $account->id;
            $user->created_by = $account->id;
            $user->role = 3;
            $user->active = 1;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->language = $language;
            $user->locale = $locale;
            $user->timezone = $timezone;
            $user->currency_code = $currency;
            $user->expires = Carbon::now()->addDays($trial_days);
            $user->signup_ip_address = request()->ip();
            $user->verification_code = $verification_code;

            // New Parameters
            $user->phone_personal = $request->phone;
            $user->date_of_birth = $request->birthday;
            $user->country_id = $request->country_id;
            $user->city = $request->city;
            $user->save();

            if($request->hasFile('store_logo')) {
                $file = $request->file('store_logo');
                if ($file) {
                    $user->addMedia($file)
                        ->sanitizingFileName(fn($fileName) => strtolower(str_replace(['#', '/', '\\', ' '], '-', $fileName)))
                        ->toMediaCollection('store_logo');
                }
            }

            $this->pointsExpiry->updateOrCreate($user->id);

            EmailTemplate::insertRecord($user->id);

            event(new Registered($user));

            if ($this->notifPusher->isAvailable()) {
                $this->notifPusher->registerMerchant($user);
            }

            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'error' => $exception->getMessage(),
            ], 400);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Handle user login.
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

        $remember = (bool) $request->get('remember', false);

        $credentials = $request->only('email', 'password');
        $credentials['active'] = 1;

        $auth = JWTAuth::attempt($credentials, $remember);

        if ($auth) {

            $user = auth()->user();
            $token = auth()->tokenById($user->id);

            $user->logins = $user->logins + 1;
            $user->last_login_ip_address = request()->ip();
            $user->last_login = Carbon::now('UTC');
            $user->save();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'token' => $token
                ]
            ])->header('Authorization', $token);
        }

        return response()->json(['error' => 'login_error'], 401);
    }

    /**
     * Handle impersonate login.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function impersonate(Request $request)
    {
        $uuid = request('uuid', null);

        if (auth()->user()->role == 1) {
            $user = User::withoutGlobalScopes()->whereUuid($uuid)->firstOrFail();

            if ($token = $this->guard()->login($user)) {
                return response()->json([
                    'status' => 'success'
                ], 200)
                ->header('Authorization', $token);
            }
        }

        return response()->json(['error' => 'login_error'], 401);
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
            return response()->json([
                'error' => 'refresh_token_error'
            ], 401);
        }

        return response()
            ->json(['status' => 'successs'], 200)
            ->header('Authorization', $token);
    }

    /**
     * Send a password reset email.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function passwordReset(Request $request)
    {
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

        $user = User::withoutGlobalScopes()
            ->where('email', $request->email)
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

            $email = new \stdClass;
            $email->to_name = $user->name;
            $email->to_email = $user->email;
            $email->subject = trans('app.reset_password_mail_subject');
            $email->body_top = trans('app.reset_password_mail_top');
            $email->cta_label = trans('app.reset_password_mail_cta');
            $email->cta_url = url('go#/password/reset/' . $token);
            $email->body_bottom = trans('app.reset_password_mail_bottom');

            Mail::send(new \App\Mail\SendMail($email));

        } else {
            return response()->json([
                'status' => 'error',
                'error' => trans('passwords.user')
            ]);
        }

        return response()->json([
            'status' => 'success',
            'msg' => "Password reset email has been sent."
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
            ->where('created_at', '>=', Carbon::now()->addHour(-24)->toDateTimeString())
            ->first();

        if ($password_reset) {
            return response()->json([
                'status' => 'success'
            ]);
        }

        return response()->json([
            'status' => 'error',
            'error' => 'invalid_token'
        ]);
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
            ->where('created_at', '>=', Carbon::now()->addHour(-24)->toDateTimeString())
            ->first();

        if ($password_reset) {
            DB::table('password_resets')->where('token', $request->token)->delete();

            $user = User::withoutGlobalScopes()->where('email', $password_reset->email)
                ->where('active', 1)
                ->first();

            if ($user) {
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
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postUpdateProfile(Request $request)
    {
        $account = app()->make('account');

        if (
            env('APP_DEMO', false) === true &&
            (auth()->user()->id == 1 || auth()->user()->id == 2)
        ) {
            return response()->json([
                'status' => 'error',
                'error' => 'demo'
            ], 422);
        }

        $locale = request('locale', config('system.default_language'));

        app()->setLocale($locale);

        $validate = Validator::make($request->all(), [
            'current_password' => 'required|min:8|max:24',
            'name' => 'required|min:2|max:32',
            'phone_personal' => 'required',
            'email' => [
                'required',
                'email',
                'max:64',
                Rule::unique('users')->where(function ($query) use ($account) {
                    return $query
                        ->where('account_id', $account->id)
                        ->where('id', '<>', auth()->id());
                }),
            ],
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validate->errors()
            ], 422);
        }

        // Verify password
        if (! Hash::check($request->current_password, auth()->user()->password)) {
            return response()->json([
                'status' => 'error',
                'errors' => [
                    'current_password' => [
                        trans('app.current_password_incorrect')
                    ],
                ],
            ], 422);
        }

        $phoneNumber = $request->phone_personal;

        $countryCode = explode(' ', $phoneNumber);

        // All good, update profile
        $user = User::query()->find(Auth::id());

        $user->name = $request->name;
        $user->email = $request->email;
        $user->timezone = $request->timezone;
        $user->locale = $request->locale;
        $user->currency_code = $request->currency;
        $user->phone_personal = $phoneNumber;
        $user->country_code = $countryCode[0];
        $user->language = explode('_', $request->locale)[0];

        // Update password
        if ($request->new_password !== null && $request->new_password != 'null') {
            $user->password = bcrypt($request->new_password);
        }

        $user->save();

        // Update avatar
        if (json_decode($request->avatar_media_changed) === true) {
            $file = $request->file('avatar');

            if ($file) {
                $user->addMedia($file)
                    ->sanitizingFileName(fn($fileName) => strtolower(str_replace(['#', '/', '\\', ' '], '-', $fileName)))
                    ->toMediaCollection('avatar');
            } else {
                $user
                    ->clearMediaCollection('avatar');
            }
        }

        return response()->json([
            'status' => 'success',
            'user' => $this->user(false)
        ]);
    }

    /**
     * Get user info.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function user($returnResponse = true)
    {
        $user = User::withoutGlobalScopes()
            ->where('id', Auth::id())
            ->where('active', 1)
            ->firstOrFail();

        $user->timezone = $user->getTimezone();
        $user->language = $user->getLanguage();
        $user->locale = $user->getLocale();
        $user->currency = $user->getCurrency();
        $user->customer_count = $user->customers->count();

        $phoneNumber = $user->phone_personal;
        $countryCode = '';

        if(! is_null($phoneNumber)) {
            $countryCode = explode(' ', $user->phone_personal);
            $countryCode = $countryCode[0];
        }

        $response = [
            'uuid' => $user->uuid,
            'active' => (int) $user->active,
            'demo' => (int) $user->demo,
            'avatar' => $user->avatar,
            'customer_count' => $user->customer_count,
            'name' => $user->name,
            'email' => $user->email,
            'plan_name' => $user->plan_name,
            'plan_id' => $user->plan_id,
            'role' => (int) $user->role,
            'expires_at' => $user->expires_at,
            'email_verified_at' => $user->email_verified_at,
            'expired' => ($user->expires) ? $user->expires->isPast() : true,
            'language' => $user->language,
            'locale' => $user->locale,
            'timezone' => $user->timezone,
            'currency' => $user->currency,
            'phone_personal' => $user->phone_personal,
            'country_code' => $countryCode,
            'pending_plan_request' =>  $user->pending_plan_request
        ];

        if ($returnResponse) {
            return response()->json([
                'status' => 'success',
                'data' => $response
            ]);
        }

        return $response;
    }

    /**
     * Get guard for logged in user.
     *
     * @return \Illuminate\Support\Facades\Auth
     */
    private function guard()
    {
        return auth()->guard('api');
    }
}
