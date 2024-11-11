<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\PriceAlertChecker;
use App\Services\Notifier;
use Illuminate\Support\Facades\Log;
use App\Mail\PriceNotification;
use Illuminate\Queue\Middleware\WithoutOverlapping;

class CheckPriceAlertsJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected string $channel,
        protected int $currentPrice,
        protected Notifier $notifier = new Notifier(),
        protected PriceAlertChecker $priceAlertChecker = new PriceAlertChecker()
    )
    {
        $this->channel = $channel;
        $this->currentPrice = $currentPrice;
    }

    public function handle()
    {
        Log::channel($this->channel)->info('Price Alert check job dispatched');
        $notifier = $this->notifier;

        $this->priceAlertChecker->checkAlerts(
            currentPrice: $this->currentPrice,
            callback: function ($alert, $currentPrice) use ($notifier) {
                $notifier->sendNotification(
                    email: $alert->email, 
                    channel: $this->channel,
                    notification: new PriceNotification($currentPrice, $alert->price)
                );
            }
        );

        Log::channel($this->channel)->info('End');
    }
}
