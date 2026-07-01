<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceGroupPricing extends Model
{
    protected $table = 'price_group_pricings';

    protected $fillable = [
        'group_id',
        'item_code',
        'group_name',
        'brand_code',
        'price',
        'price_names',
        'can_purchase',
    ];

    protected $casts = [
        'can_purchase' => 'boolean',
    ];


}
