<?php

namespace Database\Factories;

use Platform\Models\Business;
use Illuminate\Database\Eloquent\Factories\Factory;

class BusinessFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Business::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'account_id' => 1,
            'name' => $this->faker->name,
            'timezone_id' => 1,
            'active_monday' => 1,
            'active_tuesday' => 1,
            'active_wednesday' => 1,
            'active_thursday' => 1,
            'active_friday' => 1,
            'active_saturday' => 1,
            'active_sunday' => 1,
            'active_monday_from' => '00:00',
            'active_tuesday_from' => '00:00',
            'active_wednesday_from' => '00:00',
            'active_thursday_from' => '00:00',
            'active_friday_from' => '00:00',
            'active_saturday_from' => '00:00',
            'active_sunday_from' => '00:00',
            'active_monday_to' => '23:59',
            'active_tuesday_to' => '23:59',
            'active_wednesday_to' => '23:59',
            'active_thursday_to' => '23:59',
            'active_friday_to' => '23:59',
            'active_saturday_to' => '23:59',
            'active_sunday_to' => '23:59'
        ];
    }
}
