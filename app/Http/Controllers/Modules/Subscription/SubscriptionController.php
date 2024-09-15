<?php

namespace App\Http\Controllers\Modules\Subscription;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PriceAlert;
use App\Models\PercentAlert;
use Illuminate\Http\JsonResponse;

/**
 * @group Subscription endpoints
 */
class SubscriptionController extends Controller
{
    /**
     * Handle subscription
     * 
     * @authenticated
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function subscribe(Request $request): JsonResponse
    {
        $subscriptionHandlers = [
            'price' => 'subscribeForPriceAlert',
            'percent' => 'subscribeForPercentAlert',
        ];
    
        return array_key_exists($request->type, $subscriptionHandlers) ?
            $this->{$subscriptionHandlers[$request->type]}($request) :
            response()->json([
                'error' => true,
                'message' => 'Not valid subscription type',
            ]);
    }

    /**
     * Subscribe for price alert notifications.
     *
     * @authenticated
     * 
     * @bodyParam email string required. Example demo@demo.com
     * @bodyParam price required
     * 
     * @resonse 200 {
     *  'success' => true,
     *  'message' => 'You have been subscribed to price alerts.'
     * }
     * 
     * @param Request $request
     * @return JsonResponse
     */
    protected function subscribeForPriceAlert(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'price' => 'required|numeric',
        ]);

        PriceAlert::create([
            'email'   => $request->email,
            'price'   => $request->price,
            'user_id' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'You have been subscribed to price alerts.',
        ]);
    }

    /**
     * Subscribe for percent alert notifications.
     *
     * @authenticated
     * 
     * @bodyParam email string required. Example demo@demo.com
     * @bodyParam percent integer required
     * @bodyParam timeInterval integer required
     * 
     * @response 200 {
     *  'success' => true,
     *  'message' => 'You have been subscribed to percent alerts.'
     * }
     * 
     * @param Request $request
     * @return JsonResponse
     */
    protected function subscribeForPercentAlert(Request $request): JsonResponse
    {
        $request->validate([
            'email'        => 'required|email',
            'percent'      => 'required|numeric|between:0,10000',
            'timeInterval' => 'required|numeric'
        ]);

        PercentAlert::create([
            'email'    => $request->email,
            'percent'  => $request->percent,
            'interval' => $request->timeInterval,
            'user_id'  => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'You have been subscribed to percent alerts.',
        ]);
    }
}
