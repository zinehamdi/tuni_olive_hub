<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Financing extends Model
{
    use HasFactory;
    protected $fillable = ['financer_id','carrier_id','qty_target','delivered_qty','amount','price_per_kg','terms','status'];
    protected $casts = [
        'qty_target' => 'decimal:3',
        'delivered_qty' => 'decimal:3',
        'amount' => 'decimal:3',
        'price_per_kg' => 'decimal:3',
    ];

    public function mill(){ return $this->belongsTo(User::class, 'financer_id'); }
    public function carrier(){ return $this->belongsTo(User::class, 'carrier_id'); }
}
