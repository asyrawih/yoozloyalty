<?php

namespace App\Repositories\Merchant;

use App\User;
use Exception;
use App\Libraries\Crypto;
use Platform\Models\Plan;
use App\Models\BillingInvoice;
use Illuminate\Support\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use App\Jobs\DeactivateMerchantPlan;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Jobs\DeactivateMerchantInvoice;
use App\Http\Resources\OrderResource;
use App\Libraries\{
    Yoozpgsdk,
    Paytmsdk,
    Payusdk,
    Instamojosdk,
    Paypalsdk,
};

class BillingRepository extends BaseRepository
{
    protected User $user;

    public function __construct(BillingInvoice $model)
    {
        parent::__construct($model);

        $this->user = User::query()->find(Auth::id());
    }

    /**
     * Get merchant orders
     *
     * @return void
     */
    public function orders(
        array $options = array(),
        array $payment_methods = array('cheque', 'bank_transfer', 'lynx'),
    ) {
        $orders = $this->model->query()
            ->whereIn('payment_method', $payment_methods)
            ->orderByDesc('created_at')
            ->paginate($options['itemsPerPage'], ['*'], 'page', $options['page']);

        return OrderResource::collection($orders);
    }

    /**
     * Get merchant plan change history
     *
     * @return void
     */
    public function planChangeRequestHistory(
        array $options = array(),
        array $payment_methods = array('cheque', 'bank_transfer', 'lynx'),
    ) {
        $orders = $this->model->query()
            ->whereIn('payment_method', $payment_methods)
            ->orderByDesc('created_at')
            ->paginate($options['itemsPerPage'], ['*'], 'page', $options['page']);

        return $orders;
        // return PlanChangeRequestHistoryResource::collection($orders);
    }

    /**
     * Get subscription status
     */
    public function stat()
    {
        return $this->user->getSubscriptionStat();
    }

    /**
     * Get merchant plans
     */
    public function plans()
    {
        return Plan::getPlansForBilling($this->user->plan, $this->user->currency_code);
    }

    /**
     * Checkout billing
     *
     * @param int $plan_id
     * @param string $payment_method
     * @param int|null $bank_id Default: null
     * @param int $amount Default: 0
     * @param string $currency ex: TTD, USD, etc. Default: TTD
     * @param $action Default: buy
     *
     * @return array
     */
    public function checkout(
        int $plan_id,
        string $payment_method,
        int|null $bank_id = null,
        int $amount = 0,
        string $currency = BillingInvoice::DEFAULT_CURRENCY,
        string $action = 'buy'
    ) {
        DB::beginTransaction();

        $response = [
            'status' => 'success',
            'message' => null,
            'data' => null
        ];

        try {
            $plan = Plan::query()->find($plan_id);

            $user_id = $this->user->id;
            $merchant_name = $this->user->name;
            $previous_plan_name = ($this->user->plan) ? $this->user->plan->name : 'Not Recorded';
            $plan_name = $plan->name;
            $currency_code = $plan->currency_code;
            $order_id = $this->invoice_number();
            $expired_at = $this->expiredAt('invoice_expires');
            $status = BillingInvoice::PENDING;

            /**
             * Create new invoice
             */
            $invoice = $this->create(compact(
                'user_id',
                'merchant_name',
                'order_id',
                'plan_id',
                'previous_plan_name',
                'plan_name',
                'payment_method',
                'bank_id',
                'currency_code',
                'amount',
                'expired_at',
                'status'
            ));

            $user = User::query()->findOrFail($user_id);

            /**
             * If action change plan
             */
            if ($action === 'change') {
                // Get current invoice
                $currenctInvoice = BillingInvoice::query()
                    ->whereOrder($user->remote_customer_id)
                    ->first();

                // set status current invoice to canceled
                $currenctInvoice->status = BillingInvoice::CANCELED;

                $currenctInvoice->save();
            }

            /**
             * If action upgrade plan
             */
            if ($action === 'upgrade') {
                // set previous invoice to current invoice
                $user->previous_remote_customer_id = $user->remote_customer_id;
            }

            /**
             * set current invoice to new invoice
             */
            $user->remote_customer_id = $order_id;

            $user->save();

            $response['data']['stat'] = $user->getSubscriptionStat();

            /**
             * If payment method cheque, bank transfer and lynx make jobs
             * to update invoice status if expired and send mail checkout
             */
            if (in_array($payment_method, array('cheque', 'bank_transfer', 'lynx'))) {
                DeactivateMerchantInvoice::dispatch($user, $invoice)
                    ->delay($expired_at)
                    ->onQueue('billings');

                // send email checkout
                $this->sendMailCheckout($user, $invoice);
            }

            // if payment method yooza pg use yoozPaymentGateway function
            if ($invoice->payment_method === 'yooz_pg') {
                $response['data'] = Yoozpgsdk::payments([
                    'order_id' => $order_id,
                    'order_amount' => $amount,
                    'currency' => $currency_code,
                    'customer_name' => $user->name,
                    'customer_phone' => str_replace([' ', '-'], '', $user->phone_personal ?? ''),
                    'customer_email' => $user->email,
                ]);
            }

            // PAYTM
            if ($invoice->payment_method === 'paytm') {
                $response['data'] = Paytmsdk::payments([
                    'order_id' => $order_id . '_' . time(),
                    'order_amount' => $amount,
                    'currency' => $currency_code,
                    'customer_id' => $user->id,
                    'customer_name' => $user->name,
                    'customer_phone' => str_replace([' ', '-'], '', $user->phone_personal ?? ''),
                    'customer_email' => $user->email,
                ]);
            }

            // PAYU
            if ($invoice->payment_method === 'payu') {
                $response['data'] = Payusdk::payments([
                    'order_id' => $order_id . '_' . time(),
                    'order_amount' => $amount,
                    'product_info' => $plan->name,
                    'customer_name' => $user->name,
                    'customer_phone' => str_replace([' ', '-'], '', $user->phone_personal ?? ''),
                    'customer_email' => $user->email,
                ]);
            }

            // Instamojo
            if ($invoice->payment_method === 'instamojo') {
                $response['data'] = Instamojosdk::payments([
                    'order_id' => $order_id . '_' . time(),
                    'order_amount' => $amount,
                    'customer_name' => $user->name,
                    'customer_phone' => str_replace([' ', '-'], '', $user->phone_personal ?? ''),
                    'customer_email' => $user->email,
                ]);
            }

            // Paypal
            if ($invoice->payment_method === 'paypal') {
                $response['data'] = Paypalsdk::payments([
                    'order_id' => $order_id . '_' . time(),
                    'order_amount' => $amount,
                    'currency' => $currency_code,
                    'customer_id' => $user->id,
                    'customer_name' => $user->name,
                    'customer_phone' => str_replace([' ', '-'], '', $user->phone_personal ?? ''),
                    'customer_email' => $user->email,
                ]);


                $currenctInvoice = BillingInvoice::query()
                    ->whereOrder($user->remote_customer_id)
                    ->first();

                $currenctInvoice->merchant_identifier = $response['data']['merchant_identifier'];
                $currenctInvoice->save();
            }

            DB::commit();

            return $response;
        } catch (Exception $exception) {
            DB::rollBack();

            throw $exception;
        }
    }

    /**
     * Confirm payment
     *
     * @param $order_id
     * @param $paid_at
     * @param string $merchant_identifier ex: bank account number, lynx transaction id
     * @param string $merchant_bank_name if merchant paid with cheque or bank transfer
     * @param int $amount_paid Default: 0
     * @param UploadedFile|array|null $receipt
     *
     * @return array
     */
    public function confirm(
        $order_id,
        $paid_at,
        string $merchant_identifier,
        string|null $merchant_bank_name = null,
        int $amount_paid = 0,
        UploadedFile|array|null $receipt = null
    ) {
        DB::beginTransaction();

        try {
            // Get invoice by order_id
            $invoice = BillingInvoice::query()->whereOrder($order_id)->firstOrFail();

            // Get user by user_id
            $user = User::query()->findOrFail($invoice->user_id);

            // if $receipt not null uploaded that
            if ($invoice && $receipt) {
                $invoice->addMedia($receipt)
                    ->sanitizingFileName(fn ($fileName) => strtolower(str_replace(['#', '/', '\\', ' '], '-', $fileName)))
                    ->toMediaCollection('receipt');
            }

            // set confirm information on invoice
            $invoice->merchant_bank_name = $merchant_bank_name;
            $invoice->merchant_identifier = $merchant_identifier;
            $invoice->amount_paid = $amount_paid;
            $invoice->paid_at = strtotime($paid_at);
            $invoice->status = BillingInvoice::CONFIRM;
            $invoice->save();

            DB::commit();

            return [
                'status' => 'success',
                'message' => 'Payment confirmation has been sent successfully.',
                'data' => [
                    'stat' => $user->getSubscriptionStat(),
                ]
            ];
        } catch (Exception $exception) {
            DB::rollBack();

            throw $exception;
        }
    }

    /**
     * Approved confirm
     *
     * @param order_id
     *
     * @return array
     */
    public function approved($order_id)
    {
        DB::beginTransaction();

        try {
            // Get invoice by order_id
            $invoice = BillingInvoice::query()->whereOrder($order_id)->firstOrFail();

            // Get user by user_id
            $user = User::query()->findOrFail($invoice->user_id);

            // set invoice status to approved
            $invoice->status = BillingInvoice::APPROVED;

            // check if user plan expired.
            $expired = ($user->expires) ? $user->expires->isPast() : true;

            // get expires for order plan
            $expires = $this->expiredAt('plan_expires');

            // if not expired additonal current expires.
            if (!$expired) {
                $expires = $this->expiredAt('plan_expires', $user->expires);
            }

            // set subscription information to user
            $user->plan_id = $invoice->plan_id;
            $user->previous_remote_gateway = $invoice->payment_method;
            $user->remote_customer_id = $invoice->order_id;
            $user->expires = $expires;

            $invoice->save();

            $user->save();

            // make jobs to change subscription status if expired
            DeactivateMerchantPlan::dispatch($user, $invoice)
                ->delay($expires)
                ->onQueue('billings');

            // send email
            $this->sendMail($user, $invoice, BillingInvoice::APPROVED);

            DB::commit();

            return [
                'status' => 'success',
                'message' => 'Successfully approved subscription plan.',
            ];
        } catch (Exception $exception) {
            DB::rollBack();

            throw $exception;
        }
    }

    public function rejected($order_id)
    {
        DB::beginTransaction();

        try {
            // Get invoice by order_id
            $invoice = BillingInvoice::query()->whereOrder($order_id)->firstOrFail();

            // Get user by user_id
            $user = User::query()->findOrFail($invoice->user_id);

            // set invoice status to rejected
            $invoice->status = BillingInvoice::REJECTED;

            // check if user plan expired.
            $expired = ($user->expires) ? $user->expires->isPast() : true;

            $user->plan_id = null;
            $user->remote_customer_id = null;

            if (!$expired) {
                $previous_invoice = null;

                if ($user->previous_remote_customer_id) {
                    $previous_invoice = BillingInvoice::query()
                        ->where('order_id', $user->previous_remote_customer_id)
                        ->first();

                    $user->plan_id = $previous_invoice->plan_id;
                    $user->remote_customer_id = $user->previous_remote_customer_id;
                }
            }

            $invoice->save();
            $user->save();

            $this->sendMail($user, $invoice, BillingInvoice::REJECTED);

            DB::commit();

            return [
                'status' => 'success',
                'message' => 'Successfully rejected subscription plan.',
            ];
        } catch (Exception $exception) {
            DB::rollBack();

            throw $exception;
        }
    }

    public function canceled($order_id)
    {
        DB::beginTransaction();

        try {
            // Get invoice by order_id
            $invoice = BillingInvoice::query()->whereOrder($order_id)->firstOrFail();

            // Get user by user_id
            $user = User::query()->findOrFail($invoice->user_id);

            // set status invoice to canceled
            $invoice->status = BillingInvoice::CANCELED;

            // check if user plan expired.
            $expired = ($user->expires) ? $user->expires->isPast() : true;

            $user->plan_id = null;
            $user->remote_customer_id = null;

            if (!$expired) {
                $previous_invoice = null;

                if ($user->previous_remote_customer_id) {
                    $previous_invoice = BillingInvoice::query()
                        ->whereOrder($user->previous_remote_customer_id)
                        ->firstOrFail();

                    $user->plan_id = $previous_invoice->plan_id;
                    $user->remote_customer_id = $user->previous_remote_customer_id;
                }
            }

            $invoice->save();
            $user->save();

            $this->sendMail($user, $invoice, BillingInvoice::CANCELED);

            DB::commit();

            return [
                'status' => 'success',
                'message' => 'Order successfully cancelled.',
                'data' => [
                    'stat' => $this->user->getSubscriptionStat(),
                ]
            ];
        } catch (Exception $exception) {
            DB::rollBack();

            throw $exception;
        }
    }

    private function invoice_number(): string
    {
        $today = Carbon::now()->format('Ymd');

        $currentSequential = $this->model->query()
            ->whereUser($this->user->id)
            ->count();

        $nextSequential = 1;

        if ($currentSequential) {
            $nextSequential = $currentSequential + 1;
        }

        $next_padded = sprintf("%02d", $nextSequential);

        return "YLP{$today}{$this->user->id}{$next_padded}";
    }

    private function expiredAt(string $type = 'plan_expires', $current_expires = null)
    {
        $config = config("paymentmethod.{$type}");

        $interval = $config['interval'];
        $interval_value = (int) $config['value'];

        $today = Carbon::now();

        $diff = 0;

        if ($current_expires) {
            switch ($interval) {
                case 'minute':
                    $diff = $today->diffInMinutes($current_expires);
                    break;
                case 'day':
                    $diff = $today->diffInDays($current_expires);
                    break;
                default:
                    $diff = $today->diffInDays($current_expires);
            }
        }

        switch ($interval) {
            case 'minute':
                return $today->addMinutes($interval_value + $diff);
            case 'day':
                return $today->addDays($interval_value + $diff);
            default:
                return $today->addDays($interval_value + $diff);
        }
    }

    private function selectedPaymentMethod($payment_method, $bank_name)
    {
        $payment_methods = array(
            'cheque' => 'Cheque',
            'bank_transfer' => 'Bank Transfer',
            'lynx' => 'Lynx',
            'yooz_pg' => 'Yooz Payment Gateway'
        );

        if ($payment_method === 'cheque' || $payment_method === 'bank_transfer') {
            return $payment_methods[$payment_method] . " ({$bank_name})";
        }

        return $payment_methods[$payment_method];
    }

    private function sendMailCheckout(User $user, BillingInvoice $invoice)
    {
        $invoice->load(['bank', 'plan']);

        $subject = "Plan Subscription Invoice #{$invoice->order_id}";
        $body_top = "Dear {$user->name} ({$user->email})," . PHP_EOL . PHP_EOL;
        $body_top .= "Your invoice was made on {$invoice->created_at->format('l, F jS, Y')}" . PHP_EOL . PHP_EOL;
        $body_top .= "Your Payment Method Is: " . $this->selectedPaymentMethod($invoice->payment_method, $invoice->bank->bank_name ?? null) . PHP_EOL . PHP_EOL;
        $body_top .= "Invoice #{$invoice->order_id}" . PHP_EOL . PHP_EOL;
        $body_top .= "Amount Due:  {$invoice->amount_formatted}" . PHP_EOL . PHP_EOL;
        $body_top .= "Due Date: {$invoice->expired_at->format('l, F jS, Y')}" . PHP_EOL . PHP_EOL;
        $body_top .= "Invoice items" . PHP_EOL . PHP_EOL;
        $body_top .= "Plan {$invoice->plan->name} - {$invoice->amount_formatted}" . PHP_EOL . PHP_EOL;
        $body_top .= "==================================================" . PHP_EOL . PHP_EOL;
        $body_top .= "After the transfer, confirm the payment via " . PHP_EOL . PHP_EOL;
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
        $email->cta_label = "Confirm Payment";
        $email->cta_url = '//' . $user->account->app_host . '/go#/billing';
        $email->body_bottom = $body_bottom;

        Mail::send(new \App\Mail\SendMail($email));
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
