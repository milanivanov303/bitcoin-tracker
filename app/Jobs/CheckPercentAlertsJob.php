<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\Notifier;
use App\Services\PeriodPriceFetcher;
use App\Services\PriceChangeCalculator;
use App\Models\PercentAlert as PercentAlertModel;
use App\Mail\PercentNotification;
use Illuminate\Support\Facades\Log;

class CheckPercentAlertsJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected string $channel,
        protected PeriodPriceFetcher $periodPrice = new PeriodPriceFetcher(),
        protected PriceChangeCalculator $priceChangeCalculator = new PriceChangeCalculator(),
        protected Notifier $notifier = new Notifier()
    )
    {
        $this->channel = $channel;
    }

    public function handle(): void
    {
        Log::channel($this->channel)->info('Percent Alert check job dispatched');

        $priceData = $this->periodPrice->fetchDefault();
        if (empty($priceData)) {
            Log::channel($this->channel)->error("Cound not get bitcoin price data");
            return;
        }

        $this->checkPercentAlerts($priceData);

        Log::channel($this->channel)->info('End');
    }

    protected function checkAlert($percentThreshold, $interval, $email, $priceData): bool
    {
        $prices = $priceData[$interval]->all();
        $initialPrice = $prices[0]->closingPrice;
        $lastPrice = end($prices);
        $currentPrice = $lastPrice->closingPrice;

        $percentageChange = $this->priceChangeCalculator->calculate(initialPrice: $initialPrice, currentPrice: $currentPrice);

        if(!$this->priceChangeCalculator->compare(percentageChange: $percentageChange, percentThreshold: $percentThreshold)) {
            return false;
        }

        $this->notifier->sendNotification(
            email: $email, 
            channel: $this->channel,
            notification: new PercentNotification($interval, $percentThreshold, $percentageChange)
        );

        return true;
    }

    /**
     * Check prices and send notification according to user's criteria.
     *
     * @return bool
     */
    protected function checkPercentAlerts(array $priceData): bool
    {
        PercentAlertModel::chunk(5, function ($alerts) use ($priceData) {
            $alerts->each(function ($alert) use ($priceData) {
                $this->checkAlert(
                    percentThreshold: $alert->percent, 
                    interval: $alert->interval,
                    email: $alert->email,
                    priceData: $priceData
                );
            });
        });

        return true;
    }
}
