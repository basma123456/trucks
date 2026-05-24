<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use softDeletes;

    protected $fillable = [
        'code',
        'name',
        'logo',
        'status',
    ];
}
