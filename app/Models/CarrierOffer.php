<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarrierOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'load_id','carrier_id','offer_price','eta_minutes','status'
    ];

    protected $casts = [
        'offer_price' => 'decimal:3',
    ];

    public function freight(){ return $this->belongsTo(Load::class, 'load_id'); }
    public function carrierUser(){ return $this->belongsTo(User::class, 'carrier_id'); }
}
