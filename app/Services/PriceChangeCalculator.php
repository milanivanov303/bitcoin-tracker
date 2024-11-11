<?php

namespace App\Services;

class PriceChangeCalculator
{
    /**
     * Calculate the percentage change between two prices.
     *
     * @param float $initialPrice
     * @param float $currentPrice
     * @return float
     */
    public function calculate(float $initialPrice, float $currentPrice): float
    {
        if ($initialPrice == 0) {
            return 0;
        }

        return number_format(((($currentPrice - $initialPrice) / $initialPrice) * 100), 2);
    }

    /**
     * @param float $percentageChange
     * @return bool
     */
    public function compare(float $percentageChange, $percentThreshold): bool
    {
        return abs($percentageChange) > $percentThreshold ? true : false;
    }
}
