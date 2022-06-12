<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Platform\Models\Business;

class GiveBusinessWithNullTimezoneDefaultValueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $businesses = Business::whereNull('timezone_id')->get();
        foreach($businesses as $business) {
            $store = Business::find($business->id);
            $store->timezone_id = 1;
            $store->save();
        }
    }
}
