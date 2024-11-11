<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Jobs\CheckPriceAlertsJob;
use App\Services\Notifier;
use App\Services\PriceAlertChecker;
use Illuminate\Support\Facades\Log;
use App\Models\PriceAlert;
use Illuminate\Foundation\Testing\WithFaker;

class CheckPriceAlertsJobTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->channel = 'price_alerts';
        $this->email = 'test@example.com';
        $this->price = $this->faker->numberBetween(75000, 76000);
    }

    public function test_check_price_alerts_job()
    {
        $notifierMock = $this->createMock(Notifier::class);
        $checkerMock = $this->createMock(PriceAlertChecker::class);
        
        $notifierMock->expects($this->once())
                     ->method('sendNotification');

        $checkerMock->expects($this->once())
                    ->method('checkAlerts')
                    ->with($this->price)
                    ->willReturnCallback(function ($currentPrice, $callback) {
                        $alert = new PriceAlert(['email' => $this->email, 'price' => $currentPrice]);
                        $callback($alert, $currentPrice);
                        return $this->faker->boolean();
                    });
                    
        $job = new CheckPriceAlertsJob($this->channel, $this->price, $notifierMock, $checkerMock);
        $job->handle();
    }
}
