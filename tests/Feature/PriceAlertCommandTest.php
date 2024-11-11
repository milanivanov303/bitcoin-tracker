<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Console\Commands\PriceAlert;
use App\Jobs\CheckPriceAlertsJob;
use App\Services\TickerService;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Queue;

class PriceAlertCommandTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->price = $this->faker->numberBetween(40000, 80000);
    }

    public function test_command_dispatches_check_price_alerts_job()
    {
        $tickerServiceMock = $this->createMock(TickerService::class);
        $tickerServiceMock->method('getTicker')->willReturn(
            new JsonResponse((object)['getData' => fn() => ['lastPrice' => $this->price]])
        );

        $command = new PriceAlert($tickerServiceMock);
        $this->app->instance(PriceAlert::class, $command);

        $this->artisan('app:price-alert')->assertExitCode(0);
    }
}
