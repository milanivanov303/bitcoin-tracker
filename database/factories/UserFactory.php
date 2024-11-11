<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\PriceAlert;
use App\Models\PercentAlert;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Define a relationship with price alerts.
     */
    public function hasPriceAlerts(int $count = 1): static
    {
        return $this->has(PriceAlert::factory()->count($count), 'priceAlerts');
    }

    /**
     * Define a relationship with percent alerts.
     */
    public function hasPercentAlerts(int $count = 1): static
    {
        return $this->has(PercentAlert::factory()->count($count), 'percentAlerts');
    }
}
