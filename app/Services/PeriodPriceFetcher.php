<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Http\Controllers\Modules\Tracker\TrackerController;
use Illuminate\Support\Collection;
use App\Services\CandleService;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\CandleResource;

class PeriodPriceFetcher
{
    public function __construct(
        protected string $currency = 'tBTCUSD', 
        protected $hours = [1, 6, 24]
    )
    {
        $this->currency = $currency;
        $this->hours = collect($hours);
    }

    /**
     * Get candles for the default time intervals - 1, 6 and 24h
     */
    public function fetchDefault(): array
    {
        $data = [];
        $this->hours->each(function($hours) use (&$data) {
            $res = $this->fetch(hours: $hours);
            $data[$hours] = $res;
        });

        return $data;
    }

    /**
     * Fetch price data from the API for a given interval (in hours).
     *
     * @param int $hours
     * @return Collection
     */
    public function fetch(int $hours): Collection
    {
        $start = now()->subHours($hours)->getTimestampMs();
        $end = now()->getTimestampMs();

        $request = new Request([
            'symbol' => $this->currency,
            'start' => $start,
            'end' => $end
        ]);

        $candles = $this->getCandles(request: $request);
        //implement check

        return collect($candles->getData());
    }

    public function getCandles(Request $request): JsonResponse|null
    {
        if (!$request->has('symbol') || !$request->has('start') || !$request->has('end')) {
            return null;
        }
        
        $baseUrl = config("app.bitfinex.url");
        $symbol = $request->get('symbol');
        $start = $request->get('start');
        $end = $request->get('end');

        try {
            $response = Http::withHeaders(headers: [
                "accept" => "application/json"
            ])->get(
                url: "{$baseUrl}/candles/trade%3A1h%3A{$symbol}/hist",
                query: [
                    'start' => $start,
                    'end' => $end,
                    'sort' => 1
                ]
            );
        
            $candles = $response->json();

            if (!$candles) {
                return null;
            }
        
            return response()->json(
                data: CandleResource::collection($candles),
                status: 500
            );
        } catch (\Exception $e) {
            return null;
        }
    }
}
