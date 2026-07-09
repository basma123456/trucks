<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;

class ItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
//        return parent::toArray($request);


            if ($this['type'] == 'api') {
                $price = $this['total_price'] ?? 0;
                $purchase = $this['purchase_cost'] ?? 0;
            } else {
                $price = $this['price'] ?? 0;
                $purchase = 0;
            }

            return [
                'id' => $this['code'] ?? null,
                'product_name' => $this['product_name'] ?? null,
                'thumbnail' => $this['thumbnail'] ?? '',
                'part_description' => $this['part_description'] ?? null,
                'category' => $this['category'] ?? null,
                'subcategory' => $this['subcategory'] ?? null,
                'type' => $this['type'] ?? null,
                'brand_code' => $this['brand_code'] ?? null,
                'price' => $price,
                'purchase_cost' => $purchase,
                'total_price' => $price + $purchase,
            ];



    }
}
