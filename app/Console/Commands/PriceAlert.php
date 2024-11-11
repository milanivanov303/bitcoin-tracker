<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PriceAlert as PriceAlertModel;
use Illuminate\Support\Facades\Mail;
use App\Mail\PriceNotification;
use App\Services\TickerService;
use App\Jobs\CheckPriceAlertsJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Bus;

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

    protected readonly string $currency;

    protected string $channel = 'price_alerts';

    public function __construct(
        protected TickerService $tickerService
    )
    {
        parent::__construct();
        $this->currency = 'tBTCUSD';
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::channel($this->channel)->info(now() . " Running");

        $currentPrice = $this->getCurrentPrice();
        if(!$currentPrice) {
            Log::channel($this->channel)->info('End');
            return;
        }

        Log::channel($this->channel)->info("Current price {$currentPrice}");
        
        Bus::dispatchSync(new CheckPriceAlertsJob(
            channel: $this->channel,
            currentPrice: $currentPrice
        ));
    }

    /**
     * Get current bitcoin price
     */
    protected function getCurrentPrice(): int|null
    {
        $ticker = $this->tickerService->getTicker(symbol: $this->currency);

        if ($ticker === null) {
            Log::channel($this->channel)->error('Error getting current bitcoin price');
            return null;
        }

        $tickerData = collect($ticker->getData());

        return $tickerData->get('lastPrice');
    }
}
