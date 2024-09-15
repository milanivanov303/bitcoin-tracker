<?php

namespace App\Http\Controllers\Modules\Tracker;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\CandleResource;
use App\Http\Resources\TickerResource;
use App\Http\Controllers\Controller;

/**
 * @group Track endpoints (using Bitfinex API)
 */
class TrackerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store()
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update()
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy()
    {
        //
    }

    /**
     * Get the ticker data from Bitfinex API.
     * 
     * @authenticated
     * 
     * @param string $symbol
     * 
     * @return JsonResponse
     * @throws Exception
     * 
     * @apiResource App\Http\Resources\TickerResource
     * 
     * @response 200 {
     *  "lastPrice":54157,
     *  "dailyHighest":54925,
     *  "dailyLowest":52194
     * }
     * 
     * @response status=500 scenario="Invalid symbol" {
     *  "error": 500,
     *  "message": "Error message" 
     * }
     */
    public function getTicker(string $symbol) : JsonResponse
    {
        try {
            $client = new \GuzzleHttp\Client();
            $baseUrl = config("app.bitfinex.url");

            $response = $client->request("GET", "{$baseUrl}/ticker/{$symbol}", [
                "headers" => [
                  "accept" => "application/json",
                ],
              ]);

            return response()->json(new TickerResource(json_decode($response->getBody(), true)));
        } catch (\Exception $e) {
            return response()->json([
                "error" => $e->getCode(),
                "message" => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get candle data from Bitfinex API.
     * 
     * @authenticated
     * 
     * @param \Illuminate\Http\Request $request
     * 
     * @queryParam symbol required string The symbol of the asset (e.g., 'tBTCUSD'). Example: tBTCUSD
     * @queryParam start required int Start time in milliseconds. Example: 1622512800000
     * @queryParam end required int End time in milliseconds. Example: 1622599200000
     * 
     * @return JsonResponse
     * 
     * @apiResourceCollection App\Http\Resources\CandleResource
     * 
     * @response 200 {
     *      {
     *        "timestamp": 1622512800000,
     *        "closingPrice": 38156.5
     *      },
     *      {
     *        "timestamp": 1622599200000,
     *        "closingPrice": 38412.7
     *      }
     * }
     * 
     * @response status=400 scenario="Missing required parameters" {
     *    "error": 400,
     *    "message": "Params symbol, start and end are required"
     * }
     * 
     * @response status=500 scenario="Bitfinex API error" {
     *    "error": 500,
     *    "message": "Error message"
     * }
     */

    public function getCandles(Request $request) : JsonResponse
    {
        if (!$request->has('symbol') || !$request->has('start') || !$request->has('end')) {
            return response()->json([
                "error" => 400,
                "message" => "Params symbol, start and end are required",
            ]);
        }

        $symbol = $request->get('symbol');
        $start = $request->get('start');
        $end = $request->get('end');

        try {
            $client = new \GuzzleHttp\Client();
            $baseUrl = config("app.bitfinex.url");

            $response = $client->request("GET", "{$baseUrl}/candles/trade%3A1h%3A{$symbol}/hist", [
                "headers" => [
                  "accept" => "application/json",
                ],
                'query' => [
                    'start' => $start,
                    'end' => $end,
                    'sort' => 1
                ],
              ]);

            return response()->json(CandleResource::collection(json_decode($response->getBody(), true)));
        } catch (\Exception $e) {
            return response()->json([
                "error" => $e->getCode(),
                "message" => $e->getMessage(),
            ]);
        }  
    }
}
