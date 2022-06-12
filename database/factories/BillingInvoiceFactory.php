<?php

namespace Database\Factories;

use App\Models\BillingInvoice;
use Illuminate\Database\Eloquent\Factories\Factory;

class BillingInvoiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BillingInvoice::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => 1,
            'merchant_name' => $this->faker->name,
            'order_id' => 'YLP' . date('Ymd') .'201',
            'plan_id' => 1,
            'plan_name' => '$20/mo',
            'previous_plan_name' => 'Trial',
            'payment_method' => 'bank_transfer'
        ];
    }
}
