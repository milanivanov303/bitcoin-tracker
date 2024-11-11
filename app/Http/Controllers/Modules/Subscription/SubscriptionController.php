<?php

namespace App\Http\Controllers\Modules\Subscription;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PriceAlert;
use App\Models\PercentAlert;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreAlertRequest;
use Illuminate\Http\RedirectResponse;

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
     * @param StoreAlertRequest $request
     * @return JsonResponse
     */
    public function subscribe(StoreAlertRequest $request): JsonResponse
    {
        $subscriptionHandlers = [
            'price'   => 'subscribeForPriceAlert',
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
     * @param StoreAlertRequest $request
     * @return JsonResponse
     */
    protected function subscribeForPriceAlert(StoreAlertRequest $request): JsonResponse
    {
        $priceAlertDetails = $request->safe()->merge(['user_id' => auth()->id()])->input();
        PriceAlert::create($priceAlertDetails);

        return response()->json([
            'message' => 'You have been subscribed to price alerts'
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
     * @param StoreAlertRequest $request
     * @return JsonResponse
     */
    protected function subscribeForPercentAlert(StoreAlertRequest $request): JsonResponse
    {
        $percentAlertDetails = $request->safe()->merge(['user_id' => auth()->id()])->input();
        PercentAlert::create($percentAlertDetails);

        return response()->json([
            'message' => 'You have been subscribed to percent alerts',
        ]);
    }
}
