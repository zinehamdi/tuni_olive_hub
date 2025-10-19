<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $owner_id
 * @property string $kind
 * @property numeric $qty
 * @property string $unit
 * @property int $pickup_addr_id
 * @property int $dropoff_addr_id
 * @property \Illuminate\Support\Carbon|null $deadline_at
 * @property numeric|null $price_floor
 * @property numeric|null $price_ceiling
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $order_id
 * @property int|null $carrier_id
 * @property int|null $eta_minutes
 * @property array<array-key, mixed>|null $meta
 * @property-read \App\Models\User|null $carrier
 * @property-read \App\Models\Address $dropoffAddress
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CarrierOffer> $offers
 * @property-read int|null $offers_count
 * @property-read \App\Models\Order|null $order
 * @property-read \App\Models\User $owner
 * @property-read \App\Models\Address $pickup
 * @method static \Database\Factories\LoadFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Load newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Load newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Load query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Load whereCarrierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Load whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Load whereDeadlineAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Load whereDropoffAddrId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Load whereEtaMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Load whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Load whereKind($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Load whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Load whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Load whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Load wherePickupAddrId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Load wherePriceCeiling($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Load wherePriceFloor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Load whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Load whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Load whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Load whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
