<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Load extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id','kind','qty','unit','pickup_addr_id','dropoff_addr_id','deadline_at','price_floor','price_ceiling','status','order_id','carrier_id','eta_minutes','meta'
    ];

    protected $casts = [
        'qty' => 'decimal:3',
        'price_floor' => 'decimal:3',
        'price_ceiling' => 'decimal:3',
        'deadline_at' => 'datetime',
        'meta' => 'array',
    ];

    public function owner(){ return $this->belongsTo(User::class,'owner_id'); }
    public function pickup(){ return $this->belongsTo(Address::class,'pickup_addr_id'); }
    public function dropoffAddress(){ return $this->belongsTo(Address::class,'dropoff_addr_id'); }
    public function offers(){ return $this->hasMany(CarrierOffer::class); }
    public function order(){ return $this->belongsTo(Order::class, 'order_id'); }
    public function carrier(){ return $this->belongsTo(User::class, 'carrier_id'); }

    public const ST_NEW = 'new';
    public const ST_MATCHED = 'matched';
    public const ST_IN_TRANSIT = 'in_transit';
    public const ST_DELIVERED = 'delivered';
    public const ST_SETTLED = 'settled';

    public function canMoveTo(string $next): bool
    {
        $allowed = [
            self::ST_NEW => [self::ST_MATCHED],
            self::ST_MATCHED => [self::ST_IN_TRANSIT],
            self::ST_IN_TRANSIT => [self::ST_DELIVERED],
            self::ST_DELIVERED => [self::ST_SETTLED],
            self::ST_SETTLED => [],
        ];
        return in_array($next, $allowed[$this->status] ?? [], true);
    }

    public function moveTo(string $next): void
    {
        if (!$this->canMoveTo($next)) {
            abort(422, 'Invalid load status transition.');
        }
        $this->status = $next;
        $this->save();
    }
}
