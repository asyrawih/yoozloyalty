<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Jobs\DeactivateMerchantPlan;
use App\Libraries\Crypto;
use App\Models\BillingInvoice;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class YoozController extends Controller
{
    public function success(Request $request)
    {
        DB::beginTransaction();

        try {
            $encData = base64_decode($request->data['enc_data']);

            $data = $this->decryptData($encData);

            $invoice = BillingInvoice::query()->where('order_id', $data['order_id'])->first();

            $user = User::query()->find($invoice->user_id);

            $invoice->amount_paid = floatval($data['order_amount']);

            $invoice->paid_at = Carbon::now();

            $invoice->status = BillingInvoice::APPROVED;

            $expired = ($user->expires) ? $user->expires->isPast() : true;

            $expires = $this->expiredAt('plan_expires');

            if (! $expired) {
                $expires = $this->expiredAt('plan_expires', $user->expires);
            }

            $user->plan_id = $invoice->plan_id;

            $user->previous_remote_gateway = $invoice->payment_method;

            $user->remote_customer_id = $invoice->order_id;

            $user->expires = $expires;

            $user->save();

            $invoice->save();

            DeactivateMerchantPlan::dispatch($user, $invoice)
                ->delay($expires)
                ->onQueue('billings');

            $this->sendMail($user, $invoice, BillingInvoice::APPROVED);

            DB::commit();

            return redirect('/go#/billing');
        } catch (Exception $exception) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ]);
        }
    }

    public function failed(Request $request)
    {
        DB::beginTransaction();

        try {
            $encData = base64_decode($request->data['enc_data']);

            $data = $this->decryptData($encData);

            $invoice = BillingInvoice::query()
                ->where('order_id', $data['order_id'])
                ->first();

            $user = User::query()->find($invoice->user_id);

            $invoice->status = BillingInvoice::REJECTED;

            $expired = ($user->expires) ? $user->expires->isPast() : true;

            $user->plan_id = null;

            $user->remote_customer_id = null;

            if (! $expired) {
                $previous_invoice = null;

                if ($user->previous_remote_customer_id) {
                    $previous_invoice = BillingInvoice::query()
                        ->where('order_id', $user->previous_remote_customer_id)
                        ->first();

                    $user->plan_id = $previous_invoice->plan_id;

                    $user->remote_customer_id = $user->previous_remote_customer_id;
                }
            }

            $user->save();

            $invoice->save();

            $this->sendMail($user, $invoice, BillingInvoice::REJECTED);

            DB::commit();

            return redirect('/go#/billing');
        } catch (Exception $exception) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ]);
        }
    }

    public function decryptData(string $encData = '')
    {
        $cryptoJS = new Crypto();

        $mode = config('yooz.mode');

        $config = config("yooz.{$mode}");

        $encryptionKey = $config['encryptionKey'];

        return json_decode($cryptoJS->decrypt($encryptionKey, $encData), true);
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
            $body_top .= "Amount: $ {$invoice->amount}" . PHP_EOL . PHP_EOL;
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

    private function expiredAt(string $type, $current_expires = null)
    {
        $config = config("paymentmethod.{$type}");

        $interval = $config['interval'];
        $interval_value = (int) $config['value'];

        $today = Carbon::now();
        $diff = 0;

        if ($current_expires) {
            switch ($interval) {
                case 'minute': $diff = $today->diffInMinutes($current_expires); break;
                case 'day': $diff = $today->diffInDays($current_expires); break;
                case 'month': $diff = $today->diffInMonths($current_expires); break;
                default: $diff = $today->diffInDays($current_expires);
            }
        }

        switch ($interval) {
            case 'minute': return $today->addMinutes($interval_value + $diff);
            case 'day': return $today->addDays($interval_value + $diff);
            case 'month': return $today->addMonths($interval_value + $diff);
            default: return $today->addDays($interval_value + $diff);
        }
    }
}
