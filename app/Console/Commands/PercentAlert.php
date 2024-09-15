<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Modules\Tracker\TrackerController;
use App\Models\PercentAlert as PercentAlertModel;
use Illuminate\Support\Facades\Mail;
use App\Mail\PercentNotification;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;

class PercentAlert extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:percent-alert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and send alerts if bitcoin price jumps/drops by percent';

    protected string $currency = 'tBTCUSD';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $time = now();
        $this->info("{$time} Running");
        $this->checkPercentAlerts();

        $this->info('End');
    }

    /**
     * @return Collection
     */
    protected function fetchPriceData(int $hours) 
    {
        $start = now()->subHours($hours)->getTimestampMs();
        $end = now()->getTimestampMs();

        $request = new Request([
            'symbol' => $this->currency,
            'start' => $start,
            'end' => $end
        ]);

        $candles = app(TrackerController::class)->getCandles($request);

        return collect($candles->getData());
    }
    
    protected function calculatePercentageChange($initialPrice, $currentPrice) : float
    {
        if ($initialPrice == 0) {
            return 0;
        }
    
        return number_format(((($currentPrice - $initialPrice) / $initialPrice) * 100), 2);
    }

    protected function checkAlert($percentThreshold, $interval, $email)
    {
        $priceData = $this->fetchPriceData($interval);
        if ($priceData->isEmpty()) {
            $this->error("Cound not get bitcoin price data for interval {$interval}h, email {$email}");
            return false;
        }

        $prices = $priceData->all();
        $initialPrice = $prices[0]->closingPrice;
        $lastPrice = end($prices);
        $currentPrice = $lastPrice->closingPrice;

        $percentageChange = $this->calculatePercentageChange($initialPrice, $currentPrice);

        if (abs($percentageChange) > $percentThreshold) {
            try {
                $now = now();
                Mail::to($email)->send(new PercentNotification($interval, $percentThreshold, $percentageChange));
                
                $this->info("Email sent to {$email} at {$now}");
            } catch (\Exception $e) {
                $this->error('Error sending email: ' . $e->getMessage());
            }
        }

        return true;
    }

    /**
     * Check prices and send notification according to user's criteria.
     *
     * @return bool
     */
    protected function checkPercentAlerts() : bool
    {
        PercentAlertModel::all()->each(function ($alert) {
            $this->checkAlert($alert->percent, $alert->interval, $alert->email);
        });

        return true;
    }
}
