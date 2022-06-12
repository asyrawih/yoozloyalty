<?php

namespace App\Console\Commands;

use Platform\Models\ {
    Campaign,
    Business,
    Reward,
    Segment
};
use App\ {
    Customer,
    Staff,
    User
};
use Illuminate\Console\Command;

class UpdateAccountIdCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:account_id';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $campaigns = Campaign::all();
        foreach ($campaigns as $campaign) {
            $campaign->update([
                'account_id' => 1
            ]);
        }

        $businesses = Business::all();
        foreach ($businesses as $business) {
            $business->update(['account_id' => 1]);
        }

        $rewards = Reward::all();
        foreach ($rewards as $reward) {
            $reward->update(['account_id' => 1]);
        }

        $segments = Segment::all();
        foreach ($segments as $segment) {
            $segment->update(['account_id' => 1]);
        }

        $staffs = Staff::all();
        foreach ($staffs as $staff) {
            $staff->update(['account_id' => 1]);
        }

        $users = User::all();
        foreach ($users as $user) {
            $user->update(['account_id' => 1]);
        }

        $customers = Customer::all();
        foreach ($customers as $customer) {
            $customer->update(['account_id' => 1]);
        }
    }
}
