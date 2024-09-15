<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\PriceAlert;
use App\Models\PercentAlert;
use Illuminate\Http\Response;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_subscribe_for_price_alert_successfully()
    {
        $this->json('POST', '/tracker/subscribe', [
                'type'   => 'price',
                'email'  => $this->faker->email(),
                'price'  => $this->faker->randomNumber(),
            ])->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true
            ]);
    }

    public function test_subscribe_for_percent_alert_successfully()
    {
        $intervals = [1, 6, 24];

        $this->json('POST', '/tracker/subscribe', [
                'type'         => 'percent',
                'email'        => $this->faker->email(),
                'percent'      => $this->faker->numberBetween(0, 10000),
                'timeInterval' => $this->faker->randomElement($intervals),
            ])->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true
            ]);
    }

    public function test_return_error_for_invalid_subscription_type()
    {
        $invalidType = 'invalid_type';

        $this->json('POST', '/tracker/subscribe', [
                'type' => $invalidType,
            ])->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'error'   => true
            ]);
    }
}
