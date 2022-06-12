<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TimezoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('timezones')->truncate();

        $timezone_data = [
            ['timezone_name' => 'UTC']
        ];

        $timezones = \DateTimeZone::listIdentifiers(\DateTimeZone::ALL);

        foreach ($timezones as $timezone) {
            if ($timezone != 'UTC') {
                $timezone_data[] = ['timezone_name' => $timezone];
            }
        }

        DB::table('timezones')->insert($timezone_data);
    }
}
