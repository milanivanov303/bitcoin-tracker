<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PercentAlert>
 */
class PercentAlertFactory extends Factory
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
            'percent' => fake()->randomFloat(2, 0.01, 10.00),
            'interval' => fake()->randomElement([1, 6, 24]),
            'user_id' => User::factory(),
        ];
    }
}
