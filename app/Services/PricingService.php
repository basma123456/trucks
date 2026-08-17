<?php

namespace App\Services;

use App\Models\PriceGroupPricing;
use App\Models\PricingList;
use Illuminate\Support\Facades\Http;

class PricingService
{
    public function buildPriceMap(array $items, ApiService $api)
    {
        $token = $api->token();

        $priceMap = [];

        // Group items by brand + price group
        $groups = collect($items)->groupBy(function ($item) {
            return $item['attributes']['brand_id']
                . '_' .
                ($item['attributes']['price_group_id'] ?? 'null');
        });

        foreach ($groups as $key => $groupItems) {

            foreach ($groupItems as $item) {

                $itemId = $item['id'];
                $brandId = $item['attributes']['brand_id'] ?? null;
                $priceGroupId = $item['attributes']['price_group_id'] ?? null;

                // 1. Try ITEM pricing
                $pricing = $api->get("/v1/pricing/{$itemId}");

                $price = data_get($pricing, 'data.attributes.pricelists.0.price');

                if ($price) {
                    $priceMap[$itemId] = $price;
                    continue;
                }

                // 2. Fallback BRAND + GROUP
                if ($brandId && $priceGroupId) {

                    $pricing = $api->get(
                        "/v1/pricing/brand/{$brandId}/pricegroup/{$priceGroupId}?page=1"
                    );

                    $price = data_get(
                        $pricing,
                        'data.0.attributes.pricelists.0.price'
                    );

                    $priceMap[$itemId] = $price;
                }
            }
        }

        return $priceMap;
    }


    /***************part of jobs*****************/
    public function pricingServiceListForJob($listData)
    {
        foreach ($listData as $brand) {

            PriceGroupPricing::updateOrCreate(
                [
                    'code' => $brand['id'], // unique field
                ],
                [
                    'name' => $brand['attributes']['name'] ?? null,
                    'logo' => $brand['attributes']['logo'] ?? null,
                ]
            );
        }

    }


    public function batchOfPricngListFuncOnjobs($itemsData , $batch)
    {

        foreach ($itemsData as $item) {

            foreach ($item['attributes']['pricelists'] ?? [] as $priceList) {

                $batch[] = [
                    'item_code' => $item['id'],
                    'price_name' => $priceList['name'],
                    'price' => $priceList['price'] ?? 0,
                    'can_purchase' => $item['attributes']['can_purchase'] ?? false,
                    'purchase_cost' => $item['attributes']['purchase_cost'] ?? null,
                    'has_map' => $item['attributes']['has_map'] ?? false,
                ];
            }
        }

        return $batch;
    }

    public function DBPricingslistSyncForJob($batch){
        // chunk DB inserts
        collect($batch)->chunk(500)->each(function ($smallChunk) {
            PricingList::upsert(
                $smallChunk->toArray(),
                ['item_code', 'price_name'],
                ['price', 'can_purchase', 'purchase_cost', 'has_map']
            );
        });
    }


    public function responsePricingList($token , $num)
    {
     return   Http::withToken($token)
            ->retry(3, 1000)
            ->timeout(300)
            ->connectTimeout(30)
            ->get(config('app.API_URL') . '/pricing?page=' . $num);

    }
    /***************end part of jobs*****************/

}
