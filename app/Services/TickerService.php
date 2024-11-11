<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\TickerResource;

class TickerService
{
    public function getTicker(string $symbol): JsonResponse|null
    {
        $baseUrl = config("app.bitfinex.url");
        $response = Http::withHeaders(headers: [
            "accept" => "application/json"
            ])->get(url: "{$baseUrl}/ticker/{$symbol}");

        $price = $response->json();

        return isset($price[0]) && $price[0] === "error" ? null : response()->json(new TickerResource($price));
    }
}
