<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $user = \App\User::where('role', '3')->get();
        \App\Models\EmailTemplate::truncate();
        foreach ($user as $key => $value) {
            \App\Models\EmailTemplate::insertRecord($value->id);
        }
    }
}
