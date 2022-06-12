<?php

namespace App\Console\Commands;

use App\Customer;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class UpdateCustomerExpiredDateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:customer_expired_date';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update column customer expired_date if null.';

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
        echo "Update column customer expired_date if null.";

        $customers = Customer::query()->whereNull("expired_date")->get();

        foreach ($customers as $customer)
        {
            $customer->expired_date = Carbon::parse($customer->created_at)->addYear()->format('Y-m-d H:i:s');

            $customer->save();
        }

        echo "Update column customer expired_date success.";
    }
}
