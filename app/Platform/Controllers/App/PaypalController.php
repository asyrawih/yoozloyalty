<?php namespace Platform\Controllers\App;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

/**
 * @group Paypal
 *
 * Endpoint to check user's paypal subscription status
 *
 * @package Platform\Controllers\App
 */
class PaypalController extends \App\Http\Controllers\Controller {

  /*
   |--------------------------------------------------------------------------
   | Paypal Controller
   |--------------------------------------------------------------------------
   */

  /**
   * Update expired time
   */
  public function subcription(Request $request)
  {
      $plan_id = $request->plan_id;
      $paypal_subcription_id = $request->paypal_subcription_id;      
      $user = \App\User::where('id', auth()->user()->id)->first();
      $plan = \Platform\Models\Plan::where('id', $plan_id)->first();
      $expires = Carbon::now()->addMonths(1);

      $user->plan_id = $plan_id;                  
      $user->previous_remote_gateway = 'paypal';
      $user->remote_customer_id = $paypal_subcription_id;
      $user->expires = $expires;
      $user->save();
      

    $sendMail = false;
    $sendUserMail = false;

      $provider = new PayPalClient;
      $config = config('paypal');
      $provider->setApiCredentials($config);

      $provider->getAccessToken();

      $subscription = $provider->cancelSubscription($user->remote_customer_id, 'cancel manual by user');                          

      if ($paypal_subcription_id) {
        // Find matching plan
        $plan = \Platform\Models\Plan::where('id', $user->plan_id)->first();
        $user->previous_remote_customer_id = $user->remote_customer_id;
        $user->remote_customer_id = null;
        $user->save();
        
        $subject = "Subscription is Active";
        $body_top = "Customer: " . $user->name . " (" . $user->email . ")" . PHP_EOL . PHP_EOL;
        $body_top .= "Plan: " . $plan->name . PHP_EOL . PHP_EOL;
        $body_top .= "User ID: " . $user->id . PHP_EOL . PHP_EOL;
        $body_top .= "Paypal Subcription ID: " . $user->previous_remote_customer_id . PHP_EOL . PHP_EOL;
        $body_bottom = "";

        $sendUserMail = true;
        $user_subject = "Yor subscription is now active";
        $user_body_top = "Your subscription has been activated." . PHP_EOL . PHP_EOL;
        $user_body_bottom = "";

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

      }
  }
  
   /**
   * Cancel subscription
   */
  public function postCancelSubscription() {
    $sendMail = false;
    $sendUserMail = false;
    $user = auth()->user();

    if ($user->remote_customer_id !== null) {      
      $provider = new PayPalClient;
      $config = config('paypal');
      $provider->setApiCredentials($config);

      $provider->getAccessToken();

      $subscription = $provider->cancelSubscription($user->remote_customer_id, 'cancel manual by user');                          

      if (!$subscription) {
        // Find matching plan
        $plan = \Platform\Models\Plan::where('id', $user->plan_id)->first();
        $user->previous_remote_customer_id = $user->remote_customer_id;
        $user->remote_customer_id = null;
        $user->save();
        
        $sendMail = true;
        $subject = "Subscription cancelled";
        $body_top = "Customer: " . $user->name . " (" . $user->email . ")" . PHP_EOL . PHP_EOL;
        $body_top .= "Plan: " . $plan->name . PHP_EOL . PHP_EOL;
        $body_top .= "User ID: " . $user->id . PHP_EOL . PHP_EOL;
        $body_top .= "Paypal Subcription ID: " . $user->previous_remote_customer_id . PHP_EOL . PHP_EOL;
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

      } else {
        return response()->json(['msg' => 'User has no active subscription.']);
      }

      return response()->json(true);
    } else {
      return response()->json(['msg' => 'User has no remote customer id.']);
    }
  }
}
