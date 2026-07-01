<?php

namespace App\Services;

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
}
