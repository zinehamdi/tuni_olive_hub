<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransporterRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'carrier_id',
        'weight',
        'unit',
        'pickup_governorate',
        'pickup_delegation',
        'pickup_lat',
        'pickup_lng',
        'dropoff_governorate',
        'dropoff_delegation',
        'dropoff_lat',
        'dropoff_lng',
        'distance_km',
        'estimated_cost',
        'status',
        'accepted_at',
        'expires_at',
    ];

    protected $casts = [
        'weight' => 'float',
        'pickup_lat' => 'float',
        'pickup_lng' => 'float',
        'dropoff_lat' => 'float',
        'dropoff_lng' => 'float',
        'distance_km' => 'float',
        'estimated_cost' => 'float',
        'accepted_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public const STATUS_PENDING   = 'pending';
    public const STATUS_ACCEPTED  = 'accepted';
    public const STATUS_DECLINED  = 'declined';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_EXPIRED   = 'expired';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function carrier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'carrier_id');
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING && ($this->expires_at === null || $this->expires_at->isFuture());
    }
}
