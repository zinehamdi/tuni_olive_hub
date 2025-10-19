<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $load_id
 * @property int $carrier_id
 * @property numeric $offer_price
 * @property int $eta_minutes
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $carrierUser
 * @property-read \App\Models\Load $freight
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarrierOffer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarrierOffer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarrierOffer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarrierOffer whereCarrierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarrierOffer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarrierOffer whereEtaMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarrierOffer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarrierOffer whereLoadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarrierOffer whereOfferPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarrierOffer whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarrierOffer whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
