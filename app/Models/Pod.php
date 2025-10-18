<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $trip_id
 * @property array<array-key, mixed>|null $pickup_photos
 * @property array<array-key, mixed>|null $dropoff_photos
 * @property string|null $signed_name
 * @property string|null $signed_pin
 * @property string|null $qr_token
 * @property \Illuminate\Support\Carbon|null $verified_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Trip $trip
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pod newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pod newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pod query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pod whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pod whereDropoffPhotos($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pod whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pod wherePickupPhotos($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pod whereQrToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pod whereSignedName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pod whereSignedPin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pod whereTripId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pod whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pod whereVerifiedAt($value)
 * @mixin \Eloquent
 */
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
