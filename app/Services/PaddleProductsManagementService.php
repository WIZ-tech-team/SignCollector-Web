<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\Price;
use Illuminate\Support\Facades\DB;
use Paddle\SDK\Client;
use Paddle\SDK\Environment;
use Paddle\SDK\Notifications\Entities\Price as PaddlePrice;
use Paddle\SDK\Notifications\Entities\Product;
use Paddle\SDK\Options;

class PaddleProductsManagementService
{

    protected Client $client;

    public function __construct()
    {
        $enviournment = config('services.paddle.is_sandbox') ? Environment::SANDBOX : Environment::PRODUCTION;
        $this->client = new Client(
            apiKey: config('services.paddle.api_key'),
            options: new Options($enviournment)
        );
    }

    public function syncWithPrices()
    {
        try {

            DB::beginTransaction();

            // 1. Fetch all products
            $products = $this->client->products->list();

            // 2. Delete old products not in incoming data
            $oldProductsIds = Plan::all()->pluck('paddle_product_id')->toArray();
            $newProductsIds = collect($products)->pluck('id')->toArray();
            $productsIdsToDelete = array_diff($oldProductsIds, $newProductsIds);
            if (!empty($productsIdsToDelete)) {
                Plan::whereIn('paddle_product_id', $productsIdsToDelete)->delete();
            }

            // 3. Fetch all prices (more efficient than querying per product)
            $allPrices = $this->client->prices->list();

            // 4. Group prices by product_id
            $priceMap = []; // will contain prices grouped by product_id
            foreach ($allPrices as $price) {

                $customData = $price->customData?->jsonSerialize();
                if (isset($customData['teletalker_type']) && $customData['teletalker_type'] === 'subscription-price') {
                    $priceMap[$price->productId][] = [
                        'paddle_product_id' => $price->productId,
                        'paddle_price_id' => $price->id,
                        // 'amount' => number_format($price->unitPrice->amount / 100, 2), // to get correct price: price/100 with 2 decimal places since that the returned plice remove the floating point (.)
                        'amount' => $price->unitPrice->amount,
                        'currency' => $price->unitPrice->currencyCode,
                        'interval' => $price->billingCycle->interval ?? null,
                        'frequency' => $price->billingCycle->frequency ?? null,
                        'status' => $price->status,
                        'custom_data' => json_encode($price->customData),
                        'meta' => json_encode($price)
                    ];
                }
            }

            // 5. Sync products to plans
            foreach ($products as $product) {

                $customData = $product->customData?->jsonSerialize();

                if (!(isset($customData['teletalker_type']) && $customData['teletalker_type'] === 'subscription-plan')) {
                    continue;
                }

                $mappedData = [
                    'paddle_product_id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'status' => $product->status,
                    'custom_data' => json_encode($product->customData),
                    'meta' => json_encode($product)
                ];

                $plan = Plan::where('paddle_product_id', $product->id)->first();

                if ($plan) {
                    $plan->update($mappedData, ['name', 'description', 'status', 'meta']);
                } else {
                    Plan::create($mappedData);
                }
            }

            // // 6. Upsert products
            // $plansInsert = Plan::upsert($productsArray, ['paddle_product_id'], ['name', 'description', 'status', 'meta']);

            // 7. Upsert each product prices
            $plans = Plan::all();
            foreach ($plans as $plan) {
                $planPrices = $priceMap[$plan->paddle_product_id] ?? [];
                foreach ($planPrices as &$price) {
                    $price['plan_id'] = $plan->id;
                    $plan->prices()->upsert($price, ['paddle_price_id', 'paddle_product_id']);
                }

                // 8. Delete old prices not in incoming data
                $oldPricesIds = $plan->prices()->pluck('paddle_price_id')->toArray();
                $newPricesIds = collect($planPrices)->pluck('paddle_price_id')->filter()->toArray();
                $pricesIdsToDelete = array_diff($oldPricesIds, $newPricesIds);
                if (!empty($pricesIdsToDelete)) {
                    $plan->prices()->whereIn('paddle_price_id', $pricesIdsToDelete)->delete();
                }
            }

            // if ($status) {
            //     DB::commit();
            //     return true;
            // } else {
            //     DB::rollBack();
            //     return false;
            // }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function fetchProductById(string $product_id)
    {
        $product = $this->client->products->get($product_id);
        return $product;
    }

    public function fetchPriceById(string $price_id)
    {
        $price = $this->client->prices->get($price_id);
        return $price;
    }

    public function storePlan(Product $product)
    {
        
        $customData = $product->customData->jsonSerialize();

        if(isset($customData['teletalker_type']) && $customData['teletalker_type'] === 'subscription-plan') {

            $plan = Plan::where('paddle_product_id', $product->id)->first();
            $dataToStore = [
                'paddle_product_id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'status' => $product->status,
                'custom_data' => json_encode($product->customData),
                'meta' => json_encode($product)
            ];

            if($plan) {
                $plan->update($dataToStore);
            } else {
                Plan::create($dataToStore);
            }

        }
    }

    public function storePlanPrice(PaddlePrice $paddlePrice)
    {

        $customData = $paddlePrice->customData->jsonSerialize();
        $localPrice = Price::where('paddle_price_id', $paddlePrice->id)->first();

        if (isset($customData['teletalker_type']) && $customData['teletalker_type'] === 'subscription-price') {

            $plan = Plan::where('paddle_product_id', $paddlePrice->productId)->first();

            if (!$plan) {

                $product = $this->fetchProductById($paddlePrice->productId);
                $planCustomData = $product->customData->jsonSerialize();

                if (!(isset($planCustomData['teletalker_type']) && $planCustomData['teletalker_type'] === 'subscription-plan')) {
                    return;
                }

                $plan = Plan::create([
                    'paddle_product_id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'status' => $product->status,
                    'custom_data' => json_encode($product->customData),
                    'meta' => json_encode($product)
                ]);
            }

            $dataToStore = [
                'plan_id' => $plan->id,
                'paddle_product_id' => $paddlePrice->productId,
                'paddle_price_id' => $paddlePrice->id,
                'name' => $paddlePrice->name,
                'amount' => $paddlePrice->unitPrice->amount,
                'currency' => $paddlePrice->unitPrice->currencyCode,
                'interval' => $paddlePrice->billingCycle->interval ?? null,
                'status' => $paddlePrice->status,
                'frequncy' => $paddlePrice->billingCycle->frequency,
                'custom_data' => json_encode($paddlePrice->customData),
                'meta' => json_encode($paddlePrice)
            ];

            if ($localPrice) {
                $localPrice->update($dataToStore);
            } else {
                $plan->prices()->create($dataToStore);
            }
        } elseif ($localPrice) { // Delete it when it is teletalker_type not allowed or changed but it is stored in the local database
            $localPrice->delete();
        }
    }
}
