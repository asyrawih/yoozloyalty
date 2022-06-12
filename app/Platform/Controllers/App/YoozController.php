<?php

namespace Platform\Controllers\App;

use App\Http\Controllers\Controller;
use App\Libraries\Crypto;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Platform\Models\Plan;

class YoozController extends Controller
{
    public function checkout(Request $request)
    {
        $cryptoJS = new Crypto();
        $user = Auth::user();

        $mode = config('yooz.mode');
        $config = config("yooz.{$mode}");

        $merchantId = $config['merchantId'];
        $encryptionKey = $config['encryptionKey'];
        $secretKey = $config['secretKey'];
        $host = $config['host'];
        $language = 'EN';
        $planId = $request->plan_id;
        $orderId = intval(date('isd') + 20000);
        $orderAmount = (int) $request->amount / 100;
        $currency = $request->currency;
        $customerName = $user->name;
        $customerEmail = $user->email;
        $customerPhone = $user->phone_personal;
        $apiOption = 'hosted';

        $successUrl = url("api/webhooks/yooz/success?plan_id={$planId}&order_id={$orderId}&user_id={$user->id}");
        $failedUrl = url('api/webhooks/yooz/failed');

        /*
        |--------------------------------------------------------------
        | Billing Fields
        |--------------------------------------------------------------
        */
        $billingName = "";
        $billingEmail = "";
        $billingAddress = "";
        $billingCity = "";
        $billingState = "";
        $billingZip = "";
        $billingPhone = "";

        /*
        |--------------------------------------------------------------
        | Shipping Fields
        |--------------------------------------------------------------
        */
        $shippingName = "";
        $shippingEmail = "";
        $shippingAddress = "";
        $shippingCity = "";
        $shippingState = "";
        $shippingZip = "";
        $shippingPhone = "";

        /*
        |--------------------------------------------------------------
        | Additional Field
        |--------------------------------------------------------------
        */
        $udf1 = "";
        $udf2 = "";
        $udf3 = "";
        $udf4 = "";
        $udf5 = "";
        $udf6 = "";

        $value = "merchant_id::$merchantId||order_id::$orderId||order_amount::$orderAmount||currency::$currency||customer_name::$customerName||customer_email::$customerEmail||customer_phone::$customerPhone||language::$language||api_option::$apiOption||success_url::$successUrl||failed_url::$failedUrl||billing_name::$billingName||billing_address::$billingAddress||billing_city::$billingCity||billing_state::$billingState||billing_zip::$billingZip||billing_phone::$billingPhone||billing_email::$billingEmail||shipping_name::$shippingName||shipping_address::$shippingAddress||shipping_city::$shippingCity||shipping_state::$shippingState||shipping_zip::$shippingZip||shipping_phone::$shippingPhone||shipping_email::$shippingEmail||udf1::$udf1||udf2::$udf2||udf3::$udf3||udf4::$udf4||udf5::$udf5||udf6::$udf6";
        $encData = base64_encode($cryptoJS->encrypt($encryptionKey, $value));

        $payload = [
            'merchant_id' => $merchantId,
            'enc_data' => $encData,
        ];

        try {
            set_time_limit(0);
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => "{$host}/payments",
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_TIMEOUT => 1000000,
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_HTTPHEADER => [
                    "Accept: application/json",
                    "X-Requested-With: XMLHttpRequest",
                    "Authorization: Bearer $secretKey"
                ]
            ]);

            /*
            |----------------------------------------------------------------
            | Get Response
            |----------------------------------------------------------------
            */
            $response = curl_exec($curl);

            /*
            |----------------------------------------------------------------
            | Close CURL Process
            |----------------------------------------------------------------
            */
            curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            $response = json_decode($response, true);

            if (! $response['success']) {
                return json_encode($response);
            }

            if (isset($response['data']['redirect'])) {

                return [
                    'redirect' => $response['data']['redirect'],
                ];
            }

            return $response;
        } catch (Exception $exception) {

        }
    }

    public function success(Request $request)
    {
        $user = User::query()->find($request->user_id);

        $user->plan_id = $request->plan_id;
        $user->previous_remote_gateway = 'yooz_pg';
        $user->remote_customer_id = $request->order_id;
        $user->expires = Carbon::now()->addMonths(1);;
        $user->save();

        return redirect('/go#/billing');
    }

    public function failed(Request $request)
    {
        return redirect('/go#/billing');
    }

    public function subcription(Request $request)
    {
        $expires = Carbon::now()->addMonths(1);

        $user = User::query()->find(Auth::id());

        $user->plan_id = $request->plan_id;
        $user->previous_remote_gateway = 'yooz_pg';
        $user->remote_customer_id = $request->order_id;
        $user->expires = $expires;
        $user->save();

        return redirect('/go#/billing');
    }

    public function unsubcription(Request $request)
    {
        $sendMail = false;
        $sendUserMail = false;

        $user = User::query()->find(Auth::id());

        if ($user->remote_customer_id !== null) {
            $plan = Plan::query()->where('id', $user->plan_id)->first();
            $user->previous_remote_customer_id = $user->remote_customer_id;
            $user->remote_customer_id = null;
            $user->save();

            $sendMail = true;
            $subject = "Subscription cancelled";
            $body_top = "Customer: " . $user->name . " (" . $user->email . ")" . PHP_EOL . PHP_EOL;
            $body_top .= "Plan: " . $plan->name . PHP_EOL . PHP_EOL;
            $body_top .= "User ID: " . $user->id . PHP_EOL . PHP_EOL;
            $body_top .= "Yooz Paymnet Gateway Subcription ID: " . $user->previous_remote_customer_id . PHP_EOL . PHP_EOL;
            $body_bottom = "";

            $sendUserMail = true;
            $user_subject = "Subscription cancelled";
            $user_body_top = "Your subscription has been cancelled successfully." . PHP_EOL . PHP_EOL;
            $user_body_bottom = "";

            if ($sendMail) {
                $email = new \stdClass;
                $email->app_name = $user->account->app_name;
                $email->app_url = '//' . $user->account->app_host;
                $email->from_name = $user->account->app_mail_name_from;
                $email->from_email = $user->account->app_mail_address_from;
                $email->to_name = $user->account->name;
                $email->to_email = $user->account->email;
                $email->subject = $subject;
                $email->body_top = $body_top;
                $email->cta_label = "Go to dashboard";
                $email->cta_url = '//' . $user->account->app_host . '/go#/login';
                $email->body_bottom = $body_bottom;

                Mail::send(new \App\Mail\SendMail($email));
            }

            if ($sendUserMail) {
                $email = new \stdClass;
                $email->app_name = $user->account->app_name;
                $email->app_url = '//' . $user->account->app_host;
                $email->from_name = $user->account->app_mail_name_from;
                $email->from_email = $user->account->app_mail_address_from;
                $email->to_name = $user->name;
                $email->to_email = $user->email;
                $email->subject = $user_subject;
                $email->body_top = $user_body_top;
                $email->cta_label = "Go to dashboard";
                $email->cta_url = '//' . $user->account->app_host . '/go#/login';
                $email->body_bottom = $user_body_bottom;

                Mail::send(new \App\Mail\SendMail($email));
            }

            return response()->json(true);
        } else {
            return response()->json(['msg' => 'User has no remote customer id.']);
        }
    }
}
