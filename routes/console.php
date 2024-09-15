<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command('app:price-alert')
    ->everyMinute()
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/price_alerts.log'));

Schedule::command('app:percent-alert')
    ->everyMinute()
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/percent_alerts.log'));
