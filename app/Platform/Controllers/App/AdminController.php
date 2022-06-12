<?php

namespace Platform\Controllers\App;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\SettingStore;
use App\Models\SettingLegal;
use App\Models\SettingDomainGuide;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Platform\Models\Campaign;

class AdminController extends \App\Http\Controllers\Controller
{
    /*
    |--------------------------------------------------------------------------
    | Admin Controller
    |--------------------------------------------------------------------------
    */

    /**
     * Get admin stats.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getStats(Request $request) {
        $stats = User::query()->find(Auth::id())->getAdminStats();

        return response()->json($stats, 200);
    }

    /**
     * Get branding data.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getStore(Request $request) {
        $user = User::query()->find(1);

        $branding = [
            'payment_provider' => config('general.payment_provider'),
            'payment_test_mode' => config('general.payment_test_mode'),
            'app_name' => $user->app_name,
            'app_contact' => $user->app_contact,
            'app_mail_name_from' => $user->app_mail_name_from,
            'app_mail_address_from' => $user->app_mail_address_from,
            'app_host' => $user->app_host,
            'account_host' => config('general.cname_domain')
        ];

        return response()->json($branding, 200);
    }

    /**
     * Save settings > branding.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postUpdateStore(Request $request) {
        $user = User::query()->find(1);

        if ($user->app_demo == 1) return;

        if (env('APP_DEMO', false) === true && ($user->id == 1 || $user->id == 2)) {
            return;
        }

        $app_name = $request->app_name;
        $app_contact = $request->app_contact;
        $app_mail_address_from = $request->app_mail_address_from;
        $app_mail_name_from = $request->app_mail_name_from;
        $app_host = $request->domain;

        $validDomain = $this->validateDomain($app_host);

        if ($validDomain !== true) {
            return $validDomain;
        }

        // Validate
        $validate = Validator::make($request->all(), [
            'app_name' => 'required|min:3|max:64',
            'app_contact' => 'required|email|max:64',
            'app_mail_address_from' => 'required|email|max:64',
            'app_mail_name_from' => 'required|min:3|max:64'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validate->errors()
            ], 422);
        }

        $user->app_name = $app_name;
        $user->app_contact = $app_contact;
        $user->app_mail_address_from = $app_mail_address_from;
        $user->app_mail_name_from = $app_mail_name_from;
        $user->app_host = $app_host;
        $user->save();

        return response()->json([
            'status' => 'success'
        ], 200);
    }

    /**
     * Get Trial day data.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getTrial(Request $request) {
        $user = auth()->user();

        $branding = [
          'trial_days' => config('system.trial_days'),
          'grace_period_days' => config('system.grace_period_days'),
        ];

        return response()->json($branding, 200);
    }

    /**
     * Save trial day.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postUpdateTrial(Request $request) {
        if (auth()->user()->app_demo == 1) return;

        if (env('APP_DEMO', false) === true && (auth()->user()->id == 1 || auth()->user()->id == 2)) {
          return;
        }

        // Validate
        $v = Validator::make($request->all(), [
            'trial_days' => 'required',
            'grace_period_days' => 'required',
            'current_password' => 'required|min:8|max:24',
        ]);

        if ($v->fails()) {
            return response()->json([
              'status' => 'error',
              'errors' => $v->errors()
            ], 422);
        }

        // Verify password
        if (! Hash::check($request->current_password, auth()->user()->password)) {
            return response()->json([
            'status' => 'error',
            'errors' => ['current_password' => [trans('app.current_password_incorrect')]]
            ], 422);
        }

        $path = base_path('.env');
        $env = ['TRIAL_DAYS' => $request->trial_days , 'GRACE_PERIOD_DAYS' => $request->grace_period_days];


        foreach ($env as $key => $value) {
          if(is_bool(env($key))){
              $old = env($key)? 'true' : 'false';
          }elseif(env($key)===null){
              $old = 'null';
          }else{
              $old = env($key);
          }

          if (file_exists($path)) {
              file_put_contents($path, str_replace(
                  "$key=".$old, "$key=".$value, file_get_contents($path)
              ));
          }

        }
        Artisan::call('optimize:clear');

        return response()->json([
            'status' => 'success'
        ], 200);
    }

    /**
     * Get logo data.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getLogo(Request $request) {
        $store = SettingStore::find(1);

        $data = [
            'logo' => $store->getFirstMediaUrl('logo', 'logo'),
            'favicon' => $store->getFirstMediaUrl('favicon', 'favicon'),
        ];

        return response()->json($data, 200);
    }
    /**
     * Save logo store.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postUpdateLogo(Request $request) {
        if (auth()->user()->app_demo == 1) return;

        if (env('APP_DEMO', false) === true && (auth()->user()->id == 1 || auth()->user()->id == 2)) {
          return;
        }

        $validate = Validator::make($request->only(['logo', 'favicon']), [
            'logo' => 'image|mimes:png,jpg,PNG,JPG,jpeg,JPEG',
            'favicon' => 'file|mimes:ico,png'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validate->errors()
            ], 422);
        }

        $store = SettingStore::find(1);
        // Update logo
        if (json_decode($request->logo_media_changed)) {
            $file = $request->file('logo');

            if ($file !== null) {
                $store->addMedia($file)
                    ->sanitizingFileName(fn($fileName) => strtolower(str_replace(['#', '/', '\\', ' '], '-', $fileName)))
                    ->toMediaCollection('logo');
            } else {
                $store->clearMediaCollection('logo');
            }
        }
        // Update favicon
        if (json_decode($request->favicon_media_changed)) {
            $file = $request->file('favicon');
            if ($file !== null) {
                $store->addMedia($file)
                    ->sanitizingFileName(fn($fileName) => strtolower(str_replace(['#', '/', '\\', ' '], '-', $fileName)))
                    ->toMediaCollection('favicon');
            } else {
                $store->clearMediaCollection('favicon');
            }
        }

        return response()->json([
            'status' => 'success',
            'logo' => $store->getFirstMediaUrl('logo', 'logo'),
            'favicon' => $store->getFirstMediaUrl('favicon', 'favicon'),
        ]);
    }

    /**
     * Get logo data.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getLegal(Request $request) {
        $type = $request->input('type');
        $uuid = $request->input('uuid');
        if(\auth()->user() == null && !$uuid) {
            $query = SettingLegal::where('user_id', 1);
        } else {
            $campaign = Campaign::whereUuid($uuid)->first();
            $userId = $campaign ? $campaign->created_by : auth()->user()->id;
            $query = SettingLegal::where('user_id', $userId);
        }
        if ($type) {
            $legals = $query->where('type', $type)->first();
        } else {
            $legals = $query->get();
        }

        if(!$legals){
            return response()->json(['status' => 'failed', 'data' => ['content' => "No $type page yet"]]);
        }

        $data = [
            'data' => $legals
        ];

        return response()->json($data, 200);
    }
    /**
     * Save logo store.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postUpdateLegal(Request $request) {
        if (auth()->user()->app_demo == 1) return;

        if (env('APP_DEMO', false) === true && (auth()->user()->id == 1 || auth()->user()->id == 2)) {
          return;
        }

        $store = SettingLegal::where('user_id', \auth()->user()->id)->where('type', $request->type)->first();

        if($store){
            $store->update([
                'content' => $request->content
            ]);
        } else {
            $store = SettingLegal::create([
                'user_id' => \auth()->user()->id,
                'type' => $request->type,
                'content' => $request->content
            ]);
        }

        return response()->json([
            'status' => 'success',
        ], 200);
    }

    /**
     * Get Email setting data.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getEmail(Request $request) {
        $user = auth()->user();

        $branding = [
            'mail_from_name' => env('MAIL_FROM_NAME'),
            'mail_from_address' => env('MAIL_FROM_ADDRESS'),
            'mail_contact' => env('MAIL_CONTACT'),
            'mail_driver' => env('MAIL_DRIVER'),
            'mail_host' => env('MAIL_HOST'),
            'mail_port' => env('MAIL_PORT'),
            'mail_encryption' => env('MAIL_ENCRYPTION'),
            'mail_username' => env('MAIL_USERNAME'),
            'mail_password' => env('MAIL_PASSWORD'),
            'mailgun_domain' => env('MAILGUN_DOMAIN'),
            'mailgun_secret' => env('MAILGUN_SECRET'),
            'mailgun_endpoint' => env('MAILGUN_ENDPOINT')
        ];

        return response()->json($branding, 200);
    }

    /**
     * Save email setting.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postUpdateEmail(Request $request) {
        if (auth()->user()->app_demo == 1) return;

        if (env('APP_DEMO', false) === true && (auth()->user()->id == 1 || auth()->user()->id == 2)) {
          return;
        }

        // Validate
        $v = Validator::make($request->all(), [
            'mail_from_name' => 'required',
            'mail_from_address' => 'required|email|max:64',
            'mail_contact' => 'required|email|max:64',
            'mail_driver' => 'required',
            'mail_host' => 'required',
            'mail_port' => 'required',
            'current_password' => 'required|min:8|max:24',
        ]);

        if ($v->fails()) {
            return response()->json([
              'status' => 'error',
              'errors' => $v->errors()
            ], 422);
        }

        // Verify password
        if (! Hash::check($request->current_password, auth()->user()->password)) {
            return response()->json([
            'status' => 'error',
            'errors' => ['current_password' => [trans('app.current_password_incorrect')]]
            ], 422);
        }

        $path = base_path('.env');
        $env = [
                    'MAIL_FROM_NAME' => $request->mail_from_name,
                    'MAIL_FROM_ADDRESS' => $request->mail_from_address,
                    'MAIL_CONTACT' => $request->mail_contact,
                    'MAIL_DRIVER' => $request->mail_driver,
                    'MAIL_HOST' => $request->mail_host,
                    'MAIL_PORT' => $request->mail_port,
                    'MAIL_ENCRYPTION' => $request->mail_encryption,
                    'MAIL_USERNAME' => $request->mail_username,
                    'MAIL_PASSWORD' => $request->mail_password,
                    'MAILGUN_DOMAIN' => $request->mailgun_domain,
                    'MAILGUN_SECRET' => $request->mailgun_secret,
                    'MAILGUN_ENDPOINT' => $request->mailgun_endpoint
               ];


        foreach ($env as $key => $value) {
          if(is_bool(env($key))){
              $old = env($key)? 'true' : 'false';
          }elseif(env($key)===null){
              $old = 'null';
            }else{
                $old = env($key);
            }

            if (file_exists($path)) {
                    file_put_contents($path, str_replace(
                        "$key=".'"'.$old.'"', "$key=".'"'.$value.'"', file_get_contents($path)
                    ));
          }

        }
        Artisan::call('optimize:clear');

        return response()->json([
            'status' => 'success'
        ], 200);
    }

    /**
     * POST Test Mail Setting
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postTestEmail(Request $request)
    {
        // Validate
        $validate = Validator::make($request->all(), [
            'mail_to_name' => 'required',
            'mail_to_address' => 'required|email|max:64',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validate->errors()
            ], 422);
        }

        $email = new \stdClass;
        $email->to_name = $request->mail_to_name;
        $email->to_email = $request->mail_to_address;
        $email->subject = trans('app.testing_mail_subject');
        $email->body_top = trans('app.testing_mail_top');

        Mail::send(new \App\Mail\SendMail($email));

        return response()->json([
            'status' => 'success',
            'msg' => "Testing email has been sent."
        ]);
    }

    /**
     * Get payment setting data.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getPayment() {
        $settings = json_decode(file_get_contents(storage_path('json/payment-methods/settings.json')), true);

        $payment = [
            'yooz_pg_mode' => env("YOOZ_PG_MODE"),
            'yooz_pg_host_live' => env("YOOZ_PG_HOST_LIVE"),
            'yooz_pg_host_test' => env("YOOZ_PG_HOST_TEST"),
            'yooz_pg_merchant_id' => env("YOOG_PG_MERCHANT_ID"),
            'yooz_pg_encryption_key' => env("YOOZ_PG_ENCRYPTION_KEY"),
            'yooz_pg_secret_key' => env("YOOZ_PG_SECRET_KEY"),
            'paypal_mode' => env("PAYPAL_MODE"),
            'paypal_client_id' => env("PAYPAL_CLIENT_ID"),
            'paypal_secret_key' => env("PAYPAL_SECRET_KEY"),
            'stripe_mode' => env("STRIPE_MODE"),
            'stripe_public_key' => env("STRIPE_PUBLIC_KEY"),
            'stripe_secret_key' => env("STRIPE_SECRET_KEY"),
            'twocheckout_mode' => env("2CHECKOUT_MODE"),
            'twocheckout_vendor_id' => env("2CHECKOUT_VENDOR_ID"),
            'twocheckout_key' => env("2CHECKOUT_KEY"),
        ];

        $settings = array_merge($settings, $payment);

        return response()->json($settings);
    }

    /**
     * Save payment setting.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postUpdatePayment(Request $request) {
        if (auth()->user()->app_demo == 1) return;

        if (env('APP_DEMO', false) === true && (auth()->user()->id == 1 || auth()->user()->id == 2)) {
            return;
        }

        // Validate
        $validate = Validator::make($request->all(), [
            'current_password' => 'required|min:8|max:24',
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
                'errors' => ['current_password' => [trans('app.current_password_incorrect')]]
            ], 422);
        }

        $settings = json_encode([
            'is_active_cheque' => $request->is_active_cheque,
            'is_active_bank_transfer' => $request->is_active_bank_transfer,
            'is_active_lynx' => $request->is_active_lynx,
            'is_active_yooz_pg' => $request->is_active_yooz_pg,
            'is_active_paypal' => $request->is_active_paypal,
            'is_active_stripe' => $request->is_active_stripe,
            'is_active_twocheckout' => $request->is_active_twocheckout
        ], JSON_PRETTY_PRINT);

        File::put(storage_path('json/payment-methods/settings.json'), $settings);

        $path = base_path('.env');

        $env = [
            "YOOZ_PG_MODE" => $request->yooz_pg_mode,
            "YOOZ_PG_HOST_LIVE" => $request->yooz_pg_host_live,
            "YOOZ_PG_HOST_TEST" => $request->yooz_pg_host_test,
            "YOOG_PG_MERCHANT_ID" => $request->yooz_pg_merchant_id,
            "YOOZ_PG_ENCRYPTION_KEY" => $request->yooz_pg_encryption_key,
            "YOOZ_PG_SECRET_KEY" => $request->yooz_pg_secret_key,
            "PAYPAL_MODE" => $request->paypal_mode,
            "PAYPAL_CLIENT_ID" => $request->paypal_client_id,
            "PAYPAL_SECRET_KEY" => $request->paypal_secret_key,
            "STRIPE_MODE" => $request->stripe_mode,
            "STRIPE_PUBLIC_KEY" => $request->stripe_public_key,
            "STRIPE_SECRET_KEY" => $request->stripe_secret_key,
            "2CHECKOUT_MODE" => $request->twocheckout_mode,
            "2CHECKOUT_VENDOR_ID" => $request->twocheckout_vendor_id,
            "2CHECKOUT_KEY" => $request->twocheckout_key,
        ];

        foreach ($env as $key => $value) {
            if (is_bool(env($key))){
                $old = env($key)? 'true' : 'false';
            } elseif (env($key)===null) {
                $old = 'null';
            } else {
                $old = env($key);
            }

            if (file_exists($path)) {
                file_put_contents($path, str_replace(
                    "$key=" . '"' . $old . '"', "$key=" . '"' . $value . '"',
                    file_get_contents($path)
                ));
            }
        }

        Artisan::call('optimize:clear');

        return response()->json([
            'status' => 'success'
        ]);
    }

    /**
     * Get payment setting data.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getPushNotif(Request $request) {
        $user = auth()->user();

        $branding = [
            'pusher_app_id' => env("PUSHER_APP_ID"),
            'pusher_app_key' => env("PUSHER_APP_KEY"),
            'pusher_app_secret' => env("PUSHER_APP_SECRET"),
            'pusher_app_cluster' => env("PUSHER_APP_CLUSTER"),
        ];

        return response()->json($branding, 200);
    }

    /**
     * Save payment setting.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postUpdatePushNotif(Request $request) {
        if (auth()->user()->app_demo == 1) return;

        if (env('APP_DEMO', false) === true && (auth()->user()->id == 1 || auth()->user()->id == 2)) {
          return;
        }

        // Validate
        $v = Validator::make($request->all(), [
            'current_password' => 'required|min:8|max:24',
        ]);

        if ($v->fails()) {
            return response()->json([
              'status' => 'error',
              'errors' => $v->errors()
            ], 422);
        }

        // Verify password
        if (! Hash::check($request->current_password, auth()->user()->password)) {
            return response()->json([
            'status' => 'error',
            'errors' => ['current_password' => [trans('app.current_password_incorrect')]]
            ], 422);
        }

        $path = base_path('.env');
        $env = [
                    "PUSHER_APP_ID" => $request->pusher_app_id,
                    "PUSHER_APP_KEY" => $request->pusher_app_key,
                    "PUSHER_APP_SECRET" => $request->pusher_app_secret,
                    "PUSHER_APP_CLUSTER" => $request->pusher_app_cluster,
               ];


        foreach ($env as $key => $value) {
          if(is_bool(env($key))){
              $old = env($key)? 'true' : 'false';
          }elseif(env($key)===null){
              $old = 'null';
            }else{
                $old = env($key);
            }

            if (file_exists($path)) {
                    file_put_contents($path, str_replace(
                        "$key=".'"'.$old.'"', "$key=".'"'.$value.'"', file_get_contents($path)
                    ));
          }

        }

        Artisan::call('optimize:clear');

        return response()->json([
            'status' => 'success'
        ], 200);
    }

    /**
     * Get payment setting data.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getDomainGuide(Request $request) {
        $data = SettingDomainGuide::first();

        if ($data == null) {
            $data = SettingDomainGuide::create([
                'content' => null
            ]);
        }

        return response()->json($data, 200);
    }

    /**
     * Save payment setting.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postDomainGuide(Request $request) {
        // Validate
        $v = Validator::make($request->all(), [
            'current_password' => 'required|min:8|max:24',
        ]);

        if ($v->fails()) {
            return response()->json([
              'status' => 'error',
              'errors' => $v->errors()
            ], 422);
        }

        // Verify password
        if (! Hash::check($request->current_password, auth()->user()->password)) {
            return response()->json([
            'status' => 'error',
            'errors' => ['current_password' => [trans('app.current_password_incorrect')]]
            ], 422);
        }

        SettingDomainGuide::find($request->id)->update([
            'content' => $request->content
        ]);

        return response()->json([
            'status' => 'success'
        ], 200);
    }

    /**
     * Validate domain.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function validateDomain($domain) {
        $validDomain = true;

        // Remove http(s)
        if (Str::startsWith($domain, ['http://', 'https://'])) {
            $msg = ['domain' => ['Remove http:// or https://']];
            return response()->json([
              'status' => 'error',
              'errors' => $msg
            ], 422);
        }

        // Domain validation
        $v = Validator::make(['domain' => 'http://' . $domain], [
            'domain' => 'required|url'
        ]);

        if ($v->fails()) {
            return response()->json([
              'status' => 'error',
              'errors' => $v->errors()
            ], 422);
        }

        // Domain unique validation
        $v = Validator::make(['domain' => $domain], [
            'domain' => 'unique:users,app_host,' . 1
        ]);

        if ($v->fails()) {
            return response()->json([
              'status' => 'error',
              'errors' => $v->errors()
            ], 422);
        }

        // Sub dir validation
        $parts = parse_url('http://' . $domain);

        if (isset($parts['host']) && strpos($parts['host'], '.') === false) {
            $validDomain = false;
            $msg = ['domain' => ["The domain format is invalid."]];
        }

        if (isset($parts['path']) && $parts['path'] != '') {
            $validDomain = false;
            $msg = ['domain' => ["The domain cannot have a path"]];
        }

        if (! $validDomain) {
            return response()->json([
              'status' => 'error',
              'errors' => $msg
            ], 422);
        }

        return true;
    }
}

