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

            return
                [
                    "code" => $this->code,
                    'type' => $this->type ,
                    "thumbnail" => $this->thumbnail,
                    "product_name" => $this->product_name,
                    "part_number" => $this->part_number,
                    "mfr_part_number" => $this->mfr_part_number,
                    "part_description" => $this->part_description,
                    "category" => $this->category,
                    "subcategory" => $this->subcategory,
                    'price' => is_numeric( $this->price) ? round( $this->price, 2) : 'N/A', //whole price
                ];
        }


}
