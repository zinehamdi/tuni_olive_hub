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
