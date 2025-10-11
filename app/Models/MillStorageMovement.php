<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MillStorageMovement extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'mill_id','product','type','qty','unit','ref_object_type','ref_object_id','note','created_at'
    ];
    protected $casts = [
        'created_at' => 'datetime',
    ];
}
