<?php

namespace App\Jobs;

use App\Models\BillingInvoice;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeactivateMerchantInvoice implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected User $user;
    protected BillingInvoice $invoice;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, BillingInvoice $invoice)
    {
        $this->user = $user;
        $this->invoice = $invoice;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $invoice = $this->invoice;
        $user = $this->user;

        if ($invoice->status === BillingInvoice::PENDING) {
            $invoice->status = BillingInvoice::CANCELED;

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
        }

        return true;
    }

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        return $this->user->uuid;
    }
}
