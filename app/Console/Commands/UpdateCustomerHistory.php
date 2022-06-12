<?php

namespace App\Console\Commands;

use App\Customer;
use Illuminate\Console\Command;
use Platform\Models\History;

class UpdateCustomerHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:customer-history';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update customer history';

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
        echo "Fixed customer history." . PHP_EOL;

        $histories = History::withoutGlobalScopes()
            ->whereNull('reward_id')
            ->whereNull('redemption_id')
            ->get();

        foreach ($histories as $history) {
            $current_customer = Customer::withoutGlobalScopes()
                ->where('id', $history->customer_id)
                ->first();

            if ($current_customer->campaign_id !== $history->campaign_id) {
                $customer = Customer::withoutGlobalScopes()
                    ->where('campaign_id', $history->campaign_id)
                    ->where('customer_number', $current_customer->customer_number)
                    ->first();

                $history->customer_id = $customer->id;
                $history->save();
            }
        }

        echo 'Fixed customer history success.';

        return 0;
    }
}
