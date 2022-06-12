<?php

namespace App\Jobs;

use App\Models\BillingInvoice;
use App\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeactivateMerchantPlan implements ShouldQueue, ShouldBeUnique
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
        if ($this->invoice->status === BillingInvoice::APPROVED) {
            $expired = ($this->user->expires) ? $this->user->expires->isPast() : true;

            if ($expired) {
                $this->user->plan_id = null;
                $this->user->remote_customer_id = null;
                $this->user->previous_remote_customer_id = $this->invoice->order_id;
                $this->user->expires = null;

                $this->user->save();
            }
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
