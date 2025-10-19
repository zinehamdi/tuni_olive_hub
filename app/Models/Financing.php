<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $financer_id
 * @property int $carrier_id
 * @property numeric|null $qty_target
 * @property numeric $delivered_qty
 * @property numeric $amount
 * @property numeric|null $price_per_kg
 * @property string|null $terms
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $carrier
 * @property-read \App\Models\User $mill
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Financing newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Financing newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Financing query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Financing whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Financing whereCarrierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Financing whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Financing whereDeliveredQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Financing whereFinancerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Financing whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Financing wherePricePerKg($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Financing whereQtyTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Financing whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Financing whereTerms($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Financing whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
