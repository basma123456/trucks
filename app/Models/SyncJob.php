<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncJob extends Model
{
    protected $fillable = [
        'job_name',
        'page',
        'done'
    ];
}
