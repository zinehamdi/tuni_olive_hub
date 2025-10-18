<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $carrier_id
 * @property array<array-key, mixed>|null $load_ids
 * @property string|null $route_polyline
 * @property \Illuminate\Support\Carbon|null $start_at
 * @property \Illuminate\Support\Carbon|null $delivered_at
 * @property numeric|null $distance_km
 * @property numeric|null $earning
 * @property string $sr_code
 * @property string|null $pin_hash
 * @property string|null $pin_qr
 * @property string|null $pin_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $expected_eta
 * @property string|null $actual_eta
 * @property-read \App\Models\User $carrier
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Pod> $pods
 * @property-read int|null $pods_count
 * @method static \Database\Factories\TripFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trip newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trip newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trip query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trip whereActualEta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trip whereCarrierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trip whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trip whereDeliveredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trip whereDistanceKm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trip whereEarning($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trip whereExpectedEta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trip whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trip whereLoadIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trip wherePinHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trip wherePinQr($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trip wherePinToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trip whereRoutePolyline($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trip whereSrCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trip whereStartAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trip whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        'carrier_id','load_ids','route_polyline','start_at','delivered_at','distance_km','earning','sr_code','pin_token','pin_hash','pin_qr'
    ];

    protected $casts = [
        'load_ids' => 'array',
        'start_at' => 'datetime',
        'delivered_at' => 'datetime',
        'distance_km' => 'decimal:2',
        'earning' => 'decimal:3',
    ];

    public function carrier(){ return $this->belongsTo(User::class, 'carrier_id'); }
    public function pods(){ return $this->hasMany(Pod::class); }

    public const ST_DRAFT = 'draft';
    public const ST_STARTED = 'started';
    public const ST_POD_SUBMITTED = 'pod_submitted';
    public const ST_COMPLETED = 'completed';

    // Trips table does not currently have status column; we infer from timestamps.
    public function inferredStatus(): string
    {
        if ($this->delivered_at) return self::ST_COMPLETED;
        if ($this->pods()->exists()) return self::ST_POD_SUBMITTED;
        if ($this->start_at) return self::ST_STARTED;
        return self::ST_DRAFT;
    }

    public static function generatePin(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    public static function hashPin(string $pin): string
    {
        return hash('sha256', 'trip-pin:'.$pin);
    }
}
