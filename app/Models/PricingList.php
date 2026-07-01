<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingList extends Model
{
    protected $fillable = [
        'item_id',
        'item_code',
        'brand_code',
        'price',
        'price_name',
        'can_purchase',
        'purchase_cost',
        'has_map',

    ];

    protected $casts = [
        'can_purchase' => 'boolean',
    ];


}
