<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\Notifier;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\Mail\PriceNotification;
use Illuminate\Foundation\Testing\WithFaker;

class NotifierTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->channel = 'price_alerts';
        $this->email = 'test@example.com';
        $this->cacheKey = 'price_alerts_test@example.com';
        $this->currentPrice = $this->faker->numberBetween(60000, 70000);
        $this->alertPrice = $this->faker->numberBetween(40000, 50000);
    }

    public function test_sends_email_and_stores_in_cache()
    {
        Cache::shouldReceive('has')->with($this->cacheKey)->andReturn(false);
        Cache::shouldReceive('put')->once();

        Mail::fake();
        $notifier = new Notifier();
        
        $notifier->sendNotification(
            email: $this->email, 
            notification: new PriceNotification(
                currentPrice: $this->currentPrice, 
                limit: $this->alertPrice
            ), 
            channel: $this->channel
        );
        
        Mail::assertSent(PriceNotification::class);
    }

    public function test_does_not_send_email_if_cached()
    {
        Cache::shouldReceive('has')->with($this->cacheKey)->andReturn(true);

        Mail::fake();
        $notifier = new Notifier();
        
        $notifier->sendNotification(
            email: $this->email, 
            notification: new PriceNotification(
                currentPrice: $this->currentPrice, 
                limit: $this->alertPrice
            ), 
            channel: $this->channel
        );
        
        Mail::assertNotSent(PriceNotification::class);
    }
}
