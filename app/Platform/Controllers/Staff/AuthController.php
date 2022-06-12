<?php

namespace Platform\Controllers\Staff;

use App\Staff;
use App\Customer;
use Carbon\Carbon;
use App\Models\Otp;
use App\Http\Controllers;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\EmailTemplate;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Jobs\ProcessMerchantSendMail;
use Illuminate\Support\Facades\Validator;
/**
 * @group Staff Auth
 *
 * Endpoints for staff authentication
 * @package Platform\Controllers\Staff
 */
class AuthController extends \App\Http\Controllers\Controller
{
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
     * Handle user login.
     *
     * @bodyParam uuid string required uuid of website. Example: 283ca865-a71c-4d4a-b8cb-8c46c5b3ca57
     * @bodyParam email string required User's email. Example: johndoe@internet.com
     * @bodyParam password string required Password. Example: password
     *
     * @unauthenticated
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login(Request $request) {
      $v = Validator::make($request->all(), [
        'email' => 'required|max:64',
        'password' => 'required|min:6|max:24'
      ]);

      if ($v->fails()) {
        return response()->json([
          'status' => 'error',
          'errors' => $v->errors()
        ], 422);
      }

      if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
          $staff = Staff::find($request->email);

          if ($staff === null) {
            return response()->json(['error' => 'login_error'], 401);
          }

          $request->email = $staff->email;
      }

      $account = app()->make('account');
      $campaign = \Platform\Models\Campaign::withoutGlobalScopes()->whereUuid(request('uuid', 0))->firstOrFail();

      $credentials['email'] = $request->email;
      $credentials['password'] = $request->password;
      $credentials['active'] = 1;
      $credentials['account_id'] = $account->id;

      if ($token = $this->guard()->attempt($credentials, true)) {
        // Login correct, check if staff member has permission to this campaign/business
        $hasPermission = auth('staff')->user()->businesses->contains($campaign->business_id);

        if ($hasPermission) {

          if (!\App\Repositories\Staff\StoreRepository::isStoreOperational($campaign->business_id) ) {
              //$this->guard()->logout();
              return response()->json(['error' => 'store_not_open'], 401);
          }

            auth('staff')->user()->logins = auth('staff')->user()->logins + 1;
            auth('staff')->user()->last_login_ip_address =  request()->ip();
            auth('staff')->user()->last_login = Carbon::now('UTC');
            auth('staff')->user()->save();

            return response()->json(['status' => 'success', 'token' => $token], 200)->header('Authorization', $token);
        } else {
          $this->guard()->logout();
          return response()->json(['error' => 'login_error','msg'=>'User has no permission'], 401);
        }
      }
      return response()->json(['error' => 'login_error','token' => $token], 401);
    }

    /**
     * Handle user logout.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function logout() {
      $this->guard()->logout();
      return response()->json([
        'status' => 'success',
        'msg' => 'Logged out successfully.'
      ], 200);
    }

    /**
     * Refresh authorization token.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function refresh() {
      try {
        $token = $this->guard()->refresh();
      }
      catch (\Exception $e) {
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
    public function passwordReset(Request $request) {
      $campaign = \Platform\Models\Campaign::withoutGlobalScopes()->whereUuid(request('uuid', 0))->firstOrFail();
      $account = app()->make('account');
      $v = Validator::make($request->all(), [
        'email' => 'required|email|max:64'
      ]);

      if ($v->fails()) {
        return response()->json([
          'status' => 'error',
          'errors' => $v->errors()
        ], 422);
      }

      $locale = request('locale', config('system.default_language'));
      app()->setLocale($locale);

      $user = Staff::withoutGlobalScopes()
        ->where('email', $request->email)
        ->where('active', 1)
        ->first();

      if ($user !== null) {

        $token = Str::random(32);

        DB::table('password_resets')
          ->where('email', $user->email)
          ->delete();

        DB::table('password_resets')->insert(
          ['email' => $user->email, 'token' => $token, 'created_at' => Carbon::now('UTC')]
        );

        $emailTemplate = EmailTemplate::where('name', 'staff_forgot_password')->where('created_by', $campaign->created_by)->first();
        $forgot_password_url = $campaign->url . '/staff/password/reset/' . $token;
        $cta_button = '<a href="'.$forgot_password_url.'" class="button button-primary" target="_blank">Reset password</a>';
        $variableTemplate = ['{{ website_name }}', '{{ website_url }}', '{{ forgot_password_button }}', '{{ forgot_password_url }}'];
        $variableChange = [$campaign->name, $campaign->url . '/staff', $cta_button, $forgot_password_url];

        $email = new \stdClass;

        $email->website_name = $campaign->name;
        $email->website_url = $campaign->url . '/staff';
        $email->from_name = $account->app_mail_name_from;
        $email->from_email = $account->app_mail_address_from;

        $email->to_name = $user->name;
        $email->to_email = $user->email;
        $email->subject = str_replace($variableTemplate, $variableChange ,$emailTemplate->subject);
        $email->template = str_replace($variableTemplate, $variableChange ,$emailTemplate->template);
        $email->body_top = trans('campaign.reset_password_mail_top');
        $email->cta_button = $cta_button;
        $email->cta_url = $forgot_password_url;
        $email->body_bottom = trans('campaign.reset_password_mail_bottom');

        // Mail::send(new \App\Mail\TemplateMail($email));
        ProcessMerchantSendMail::dispatch(
            $email,
            $campaign->smtp_service_id
        )->onQueue('emails');


      } else {
        return response()->json([
          'status' => 'error',
          'error' => trans('passwords.user')
        ], 200);
      }

      return response()->json([
        'status' => 'success'
      ], 200);
    }

    /**
     * Validate reset password token.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function passwordResetValidateToken(Request $request) {
      $v = Validator::make($request->all(), [
        'token' => 'required|min:32|max:32'
      ]);

      if ($v->fails()) {
        return response()->json([
          'status' => 'error',
          'errors' => $v->errors()
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
        ], 200);
      } else {
        return response()->json([
          'status' => 'error',
          'error' => 'invalid_token'
        ], 200);
      }
    }

    /**
     * Update password.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function passwordUpdate(Request $request) {
      $v = Validator::make($request->all(), [
        'token' => 'required|min:32|max:32',
        'password' => 'required|min:8|max:24'
      ]);

      if ($v->fails()) {
        return response()->json([
          'status' => 'error',
          'errors' => $v->errors()
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

        $user = Staff::withoutGlobalScopes()->where('email', $password_reset->email)
          ->where('active', 1)
          ->first();

        if ($user !== null) {

          $user->password = bcrypt($request->password);
          $user->save();

          return response()->json([
            'status' => 'success'
          ], 200);
        }
      } else {
        return response()->json([
          'status' => 'error',
          'error' => 'invalid_token'
        ], 200);
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
        // $campaign = \Platform\Models\Campaign::withoutGlobalScopes()->whereUuid(request('uuid', 0))->firstOrFail();

        $locale = request('locale', config('system.default_language'));

        app()->setLocale($locale);

        $validate = Validator::make($request->all(), [
            'current_password' => 'required|min:8|max:24',
            'name' => 'required|min:2|max:32',
            'email' => [
                'required',
                'email',
                'max:64',
                Rule::unique('staff')->where(function ($query) use ($account) {
                    return $query->where('account_id', $account->id)->where('id', '<>', auth('staff')->user()->id);
                })
            ],
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validate->errors()
            ], 422);
        }

        $staff = Staff::withoutGlobalScopes()
            ->where('id', auth('staff')->id())
            ->first();

        // Verify password
        if (! Hash::check($request->current_password, $staff->password)) {
            return response()->json([
                'status' => 'error',
                'errors' => ['current_password' => [trans('app.current_password_incorrect')]]
            ], 422);
        }

        // All good, update profile
        $staff->name = $request->name;
        $staff->email = $request->email;
        $staff->locale = $request->locale;
        $staff->timezone = $request->timezone;

        // Update password
        if ($request->new_password !== null && $request->new_password != 'null') {
            $staff->password = bcrypt($request->new_password);
        }

        $staff->save();

        // Update avatar
        if (json_decode($request->avatar_media_changed) === true) {
            $file = $request->file('avatar');

            if ($file !== null) {
                $staff->addMedia($file)
                    ->sanitizingFileName(fn($fileName) => strtolower(str_replace(['#', '/', '\\', ' '], '-', $fileName)))
                    ->toMediaCollection('avatar');
            } else {
                $staff->clearMediaCollection('avatar');
            }
        }

        return response()->json([
            'status' => 'success',
            'user' => $staff,
        ]);
    }

    /**
     * Get user info.
     * @queryParam uuid string required uuid of website.
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function user(Request $request)
    {
        $campaign = \Platform\Models\Campaign::withoutGlobalScopes()
            ->whereUuid(request('uuid', 0))
            ->firstOrFail();

        $user = Staff::withoutGlobalScopes()
            ->where('id', auth('staff')->id())
            ->where('active', 1)
            ->firstOrFail();

        // Login correct, check if staff member has permission to this campaign/business
        $hasPermission = $user->businesses->contains($campaign->business_id);

        if (! $hasPermission) {
            $this->guard()->logout();

            return response()->json([
                'status' => 'error'
            ], 422);
        }

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
            'role' => (int) $user->role,
            'language' => $user->language,
            'locale' => $user->locale,
            'timezone' => $user->timezone,
            'currency' => $user->currency
        ];

        return response()->json([
            'status' => 'success',
            'data' => $return
        ]);
    }



    /**
     * Request OTP for customer for any purpose
     * @queryParam uuid string required uuid of website. Example: 283ca865-a71c-4d4a-b8cb-8c46c5b3ca57
     * @queryParam purpose string required.
     * @queryParam mode string required number or card_number.
     * @queryParam customer string required customer number or customer card_number.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function requestOtp(Request $request){
        $purpose = $request->input('purpose');
        $campaign = \Platform\Models\Campaign::withoutGlobalScopes()->whereUuid($request->input('uuid'))->firstOrFail();
        $customerNumber = preg_replace('/\D+/', '', $request->input('customer'));
        $mode = $request->mode;
        $account = app()->make('account');

        if($mode === 'number') {
            // Find customer by number
            $user = Customer::where('active', 1)
                ->where('campaign_id', $campaign->id)
                ->where('customer_number', $customerNumber)
                ->first();
        } else {
            // Find customer by card number
            $user = Customer::where('active', 1)
                ->where('campaign_id', $campaign->id)
                ->where(function($query) use ($customerNumber) {
                    $query->where('customer_number', $customerNumber);
                    $query->orWhere('card_number', $customerNumber);
                })
                ->first();
        }

        if (! $user){
            return response()->json([
                'status' => 'error',
                'errors' => ['number' => 'Customer not found']
            ], 422);
        }

        $otp = Otp::generateOtp(get_class($user), $user->id, $purpose);

        $emailTemplate = EmailTemplate::where('name', 'customer_otp')->where('created_by', $campaign->created_by)->first();
        $variableTemplate = ['{{ otp }}', '{{ website_name }}'];
        $variableChange = [$otp->code, $campaign->name];

        $email = new \stdClass;

        $email->website_name = $campaign->name;
        $email->website_url = $campaign->url;
        $email->from_name = $account->app_mail_name_from;
        $email->from_email = $account->app_mail_address_from;

        $email->to_name = $user->name;
        $email->to_email = $user->email;
        $email->subject = str_replace($variableTemplate, $variableChange ,$emailTemplate->subject);
        $email->template = str_replace($variableTemplate, $variableChange ,$emailTemplate->template);

        // Mail::send(new \App\Mail\TemplateMail($email));
        ProcessMerchantSendMail::dispatch(
            $email,
            $campaign->smtp_service_id
        )->onQueue('emails');

        // if (Mail::failures()){
        //     return response()->json([
        //         'status' => 'failed',
        //         'message' => "Failed to send OTP email."
        //     ], 400);
        // }

        return response()->json([
            'status' => 'success',
            'message' => "OTP has been sent to customer's email. Please check for further confirmation."
        ]);
    }

    /**
     * Verify OTP code
     * @bodyParam code required Otp Code.
     * @bodyParam purpose required string.
     * @bodyParam mode string required number or card_number.
     * @bodyParam customer string required customer number or customer card_number.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyOtp(Request $request){
        $code = $request->code;
        $purpose = $request->purpose;
        $customerNumber = preg_replace('/\D+/', '', $request->customer);
        $mode = $request->mode;

        if($mode === 'number') {
            // Find customer by number
            $user = Customer::where('active', 1)->where('customer_number', $customerNumber)->first();
        } else {
            // Find customer by card number
            $user = Customer::where('active', 1)
                            ->where(function($query) use ($customerNumber) {
                               $query->where('customer_number', $customerNumber);
                               $query->orWhere('card_number', $customerNumber);
                            })
                            ->first();
        }

        if(!$user){
            return response()->json([
                'status' => 'error',
                'errors' => ['number' => 'Customer not found']
            ], 422);
        }

        if(Otp::validate(get_class($user), $user->id, $code)){
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

    public function abilities() {
        $user = auth('staff')->user();

        $roles = collect(json_decode(file_get_contents(storage_path('json/staff/roles.json')), true));

        $role = $roles->firstWhere('id', '=', $user->role);

        return response()->json([
            'abilities' => $role['permissions'],
        ]);
    }

    /**
     * Get guard for logged in user.
     *
     * @return \Illuminate\Support\Facades\Auth
     */
    private function guard() {
      return Auth::guard('staff');
    }
}
