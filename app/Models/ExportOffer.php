<?php
/**
 * @property int $id
 * @property int $seller_id
 * @property string $variety
 * @property string $spec
 * @property float $qty_tons
 * @property string $incoterm
 * @property string $port_from
 * @property string $port_to
 * @property string $currency
 * @property float $unit_price
 * @property array $docs
 * @property string $status
 */

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $seller_id
 * @property string $variety
 * @property string|null $spec
 * @property numeric $qty_tons
 * @property string $incoterm
 * @property string $port_from
 * @property string $port_to
 * @property string $currency
 * @property numeric $unit_price
 * @property array<array-key, mixed>|null $docs
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Contract> $contracts
 * @property-read int|null $contracts_count
 * @property-read \App\Models\User $seller
 * @method static \Database\Factories\ExportOfferFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExportOffer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExportOffer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExportOffer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExportOffer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExportOffer whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExportOffer whereDocs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExportOffer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExportOffer whereIncoterm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExportOffer wherePortFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExportOffer wherePortTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExportOffer whereQtyTons($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExportOffer whereSellerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExportOffer whereSpec($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExportOffer whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExportOffer whereUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExportOffer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExportOffer whereVariety($value)
 * @mixin \Eloquent
 */
class ExportOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id','variety','spec','qty_tons','incoterm','port_from','port_to','currency','unit_price','docs','status'
    ];

    protected $casts = [
        'qty_tons' => 'decimal:3',
        'unit_price' => 'decimal:3',
        'docs' => 'array',
    ];

    public function seller(){ return $this->belongsTo(User::class, 'seller_id'); }
    public function contracts(){ return $this->hasMany(Contract::class); }
}
