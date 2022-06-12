<?php

namespace App\Console\Commands;

use App\Customer;
use App\Jobs\SendSMSCustomerBirthday;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CheckCustomerBirthday extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:customer-birthday';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check customer birthday each merchant.';

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
        $tomorrow = Carbon::tomorrow()->format('Y-m-d');

        $customers = Customer::query()
            ->whereDate('date_of_birth', $tomorrow)
            ->get();

        foreach ($customers as $customer) {
            SendSMSCustomerBirthday::dispatch($customer)
                ->onQueue('sms')
                ->delay(Carbon::tomorrow());
        }
    }
}
