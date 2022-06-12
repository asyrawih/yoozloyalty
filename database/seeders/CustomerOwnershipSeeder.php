<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Customer;
use App\User;

class CustomerOwnershipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Customer::whereNotIn('created_by', User::whereRaw('id <> account_id')
        ->get()
        ->pluck('id'))
        ->get()
        ->map(function($cust){
            Customer::where('id', $cust->id)
            ->update([
                'created_by' => Staff::find($cust->created_by)->created_by
            ]);
        });
    }
}
