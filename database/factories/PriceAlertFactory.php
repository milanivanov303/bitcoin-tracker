<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PriceAlert>
 */
class PriceAlertFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email'   => fake()->safeEmail(),
            'price'   => fake()->numberBetween(50000, 100000),
            'user_id' => User::factory(),
        ];
    }
}
