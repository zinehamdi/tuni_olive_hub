<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pod extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_id','pickup_photos','dropoff_photos','signed_name','signed_pin','qr_token','verified_at'
    ];

    protected $casts = [
        'pickup_photos' => 'array',
        'dropoff_photos' => 'array',
        'verified_at' => 'datetime',
    ];

    public function trip(){ return $this->belongsTo(Trip::class); }
}
