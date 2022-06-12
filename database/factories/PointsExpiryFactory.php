<?php

namespace Database\Factories;

use App\Models\PointsExpiry;
use Illuminate\Database\Eloquent\Factories\Factory;

class PointsExpiryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PointsExpiry::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'merchant_user_id' => 2,
            'points_expiry' => $this->faker->numberBetween(1, 30)
        ];
    }
}
