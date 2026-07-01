<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'product_name',
        'part_number',
        'category',
        'subcategory',
        'brand_id',
        'thumbnail',
        'barcode',
        'type',
        'part_description',
        'price',
        'thumbnail',
        'code',
        'brand_code',
        'type',

    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
    //    when type == 'local' then brand_code ==l-{brand_id}
}
