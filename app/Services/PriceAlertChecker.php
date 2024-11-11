<?php

namespace App\Services;

use App\Models\PriceAlert;

class PriceAlertChecker
{
    public function checkAlerts(int $currentPrice, callable $callback): bool
    {
        PriceAlert::where('price', '<', $currentPrice)
            ->chunk(3, function ($alerts) use ($currentPrice, $callback) {
                $alerts->each(function ($alert) use ($currentPrice, $callback) {
                    $callback($alert, $currentPrice);
                });
            });    

        return true;
    }
}
