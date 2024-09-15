<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Modules\Tracker\TrackerController;
use App\Models\PriceAlert as PriceAlertModel;
use Illuminate\Support\Facades\Mail;
use App\Mail\PriceNotification;

class PriceAlert extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:price-alert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and send alerts for bitcoin prices';

    protected string $currency = 'tBTCUSD';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $time = now();
        $this->info("{$time} Running");

        $currentPrice = $this->getCurrentPrice();
        if(!$currentPrice) {
            $this->info('End');
            return;
        }

        $this->info("Current price {$currentPrice}");
        $this->checkPriceAlerts($currentPrice);

        $this->info('End');
    }

    /**
     * Check the price and send notification if it exceeds the set limit.
     *
     * @param int $currentPrice
     */
    protected function checkPriceAlerts(int $currentPrice) : bool
    {
        PriceAlertModel::where('price', '<', $currentPrice)
            ->get()
            ->each(function ($alert) use ($currentPrice) {
                try {
                    $now = now();
                    Mail::to($alert->email)->send(new PriceNotification($currentPrice, $alert->price));
                    
                    $this->info("Email sent to {$alert->email} at {$now}");
                } catch (\Exception $e) {
                    $this->error('Error sending email: ' . $e->getMessage());
                }
        });

        return true;
    }

    /**
     * Get current bitcoin price
     */
    protected function getCurrentPrice(): ?int
    {
        $ticker = app(TrackerController::class)->getTicker($this->currency);
        $tickerData = collect($ticker->getData());

        if ($tickerData->has('error') || !$tickerData->has('lastPrice')) {
            $this->error('Error getting current bitcoin price');
            return null;
        }

        return $tickerData->get('lastPrice');
    }
}
