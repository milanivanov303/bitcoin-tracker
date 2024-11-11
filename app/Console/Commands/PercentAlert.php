<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Jobs\CheckPercentAlertsJob;
use Illuminate\Support\Facades\Bus;

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

    protected string $channel = 'percent_alerts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::channel($this->channel)->info(now() . " Running");
        Bus::dispatchSync(new CheckPercentAlertsJob($this->channel));
    }
}
