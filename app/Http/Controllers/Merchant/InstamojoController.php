<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Libraries\Instamojosdk;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use App\Models\BillingInvoice;
use App\User;
use App\Jobs\DeactivateMerchantPlan;

class InstamojoController extends Controller
{
    public function callback(Request $request) {
        $details = Instamojosdk::details($request->payment_request_id, $request->payment_id);
        $status = $request->payment_status;
        $orderId = explode('_', $details['payment_request']['purpose'])[0];

        DB::beginTransaction();
        try {
            $invoice = BillingInvoice::where('order_id', $orderId)->first();
            if ($invoice) {
                $user = User::find($invoice->user_id);
    
                if ($status === 'Credit') {
                    $user->plan_id = $invoice->plan_id;
                    $user->previous_remote_gateway = $invoice->payment_method;
                    $user->remote_customer_id = $orderId;
                    $user->expires = Carbon::now()->addMonths(1);;
                    $user->save();

                    $invoice->status = BillingInvoice::APPROVED;
                    $invoice->amount_paid = floatval($request->TXNAMOUNT);
                    $invoice->paid_at = Carbon::parse($request->TXNDATE)->format('Y-m-d');
                    $invoice->save();

                    DeactivateMerchantPlan::dispatch($user, $invoice)
                        ->delay($user->expires)
                        ->onQueue('billings');

                    // send email
                    // $this->sendMail($user, $invoice, BillingInvoice::APPROVED);
                } 
    
                if ($status === 'Failed') {
                    $invoice->status = BillingInvoice::CANCELED;
                    $invoice->save();

                    // send email
                    // $this->sendMail($user, $invoice, BillingInvoice::CANCELED);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }

        return redirect('/go#/billing');
    }

    public function webhook(Request $request) {
        dd($request->all());
    }

    private function sendMail(User $user, BillingInvoice $invoice, $action)
    {
        $invoice->load(['plan']);

        $subject = "Subscription plan {$action}";
        $body_top = "Dear {$user->name} ({$user->email})," . PHP_EOL . PHP_EOL;
        $body_top .= "This message is to ensure that your subscription has been {$action}." . PHP_EOL . PHP_EOL;

        if ($action === BillingInvoice::APPROVED) {
            $body_top .= "Your subscription details are below: " . PHP_EOL . PHP_EOL;
            $body_top .= "Subscription period: 1 Month" . PHP_EOL . PHP_EOL;
            $body_top .= "Amount: $ {$invoice->amount_formatted}" . PHP_EOL . PHP_EOL;
            $body_top .= "Next Due Date: {$user->expires->format('l, F jS, Y')}" . PHP_EOL . PHP_EOL;
        }

        $body_bottom = "";

        $email = new \stdClass;
        $email->app_name = $user->account->app_name;
        $email->app_url = '//' . $user->account->app_host;
        $email->from_name = $user->account->app_mail_name_from;
        $email->from_email = $user->account->app_mail_address_from;
        $email->to_name = $user->name;
        $email->to_email = $user->email;
        $email->subject = $subject;
        $email->body_top = $body_top;
        $email->cta_label = "Go to dashboard";
        $email->cta_url = '//' . $user->account->app_host . '/go#/login';
        $email->body_bottom = $body_bottom;

        Mail::send(new \App\Mail\SendMail($email));
    }
}
