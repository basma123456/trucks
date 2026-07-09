<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemSinglePageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
//        return parent::toArray($request);

//        dd($this);

        $allPrice = $this['allPrice'];
        $item = $this['allData'];

        if($item['type'] != 'local' && isset($item['attributes'])) {
            return
                [
                    "product_name" => $item['attributes']['product_name']??'',
                    "part_number" => $item['attributes']['part_number']??'',
                    "mfr_part_number" => $item['attributes']['mfr_part_number']??'',
                    "part_description" => $item['attributes']['part_description']??'',
                    "category" => $item['attributes']['category']??'',
                    "subcategory" => $item['attributes']['subcategory']??'',
                    'type' => $item['type'] ?? 'api',
                    'price' => is_numeric($allPrice) ? round($allPrice, 2) : 'N/A', //whole price
//                    "price_group_id" => $item['attributes']['price_group_id'],
//                    "price_group" => $item['attributes']['price_group'],
                ];
        }else{
            return
                [
                    "product_name" => $item->product_name,
                    "part_number" => $item->part_number,
                    "mfr_part_number" => $item->mfr_part_number,
                    "part_description" => $item->part_description,
                    "category" => $item->category,
                    "subcategory" => $item->subcategory,
                    'type' => $item->type ?? 'api',
                    'price' => is_numeric( $item->price) ? round( $item->price, 2) : 'N/A', //whole price
                ];
        }

    }
}
