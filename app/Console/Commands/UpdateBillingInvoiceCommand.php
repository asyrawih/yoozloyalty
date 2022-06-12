<?php

namespace App\Console\Commands;

use App\Models\BillingInvoice;
use Illuminate\Console\Command;
use Platform\Models\Plan;

class UpdateBillingInvoiceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:billing_invoice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update merchant_name and plan_name column if user_id or plan_id is null.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        echo "Update merchant_name and plan_name column if user_id or plan_id is null." . PHP_EOL;

        $invoices = BillingInvoice::all();

        $invoices->each(function ($invoice) {
            $merchant_name = ($invoice->user) ? $invoice->user->name : 'Deleted';

            $plan_name = ($invoice->plan) ? $invoice->plan->name : 'Deleted';

            if ($plan_name === 'Deleted') {
                if ($invoice->amount) {
                    $plan = Plan::query()
                        ->where('price', (int) $invoice->amount * 100)->first();

                    if ($plan) {
                        $plan_name = $plan->name;
                    }
                }
            }

            $invoice->merchant_name = $merchant_name;
            $invoice->plan_name = $plan_name;

            $invoice->save();
        });

        echo 'Update Billing Invoice success.';
    }
}
