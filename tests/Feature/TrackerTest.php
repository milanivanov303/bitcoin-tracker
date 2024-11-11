<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use App\Models\User;
use Carbon\Carbon;

class TrackerTest extends TestCase
{
    use RefreshDatabase;

    protected array $symbols = ['tBTCUSD', 'tBTCEUR'];

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }
    
    public function test_return_actual_ticker_data_successfully()
    {
        $symbol = $this->faker->randomElement($this->symbols);

        $response = $this->json("GET", "/tracker/ticker/{$symbol}");

        $response->assertStatus(200)
            ->assertJsonStructure(
                ['lastPrice', 'dailyHighest', 'dailyLowest']
            );
    }

    public function test_return_actual_candles_data_successfully()
    {
        $symbol = $this->faker->randomElement($this->symbols);
        $start = now()->subDay()->getTimestampMs();
        $end = now()->getTimestampMs();

        $response = $this->json('GET', '/tracker/candles', [
            'symbol' => $symbol,
            'start'  => $start,
            'end'    => $end
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => ['timestamp', 'closingPrice']
            ]);
    }

    public function test_return_error_if_ticker_not_found()
    {
        $response = $this->json('GET', '/tracker/ticker/tINVALID');

        $response->assertStatus(400)
        ->assertJson([
            'error'   => 400,
            'message' => 'Error fetching price data'
        ]);
    }


    public function test_return_error_if_required_params_are_missing_for_candles()
    {
        $symbol = $this->faker->randomElement($this->symbols);

        $response = $this->json('GET', '/tracker/candles', [
            'symbol' => $symbol,
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'error' => 400
            ]);
    }
}
