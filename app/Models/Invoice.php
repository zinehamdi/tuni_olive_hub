<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int|null $order_id
 * @property int|null $contract_id
 * @property int $seller_id
 * @property int $buyer_id
 * @property string $currency
 * @property string $subtotal
 * @property string $tax
 * @property string $total
 * @property string $status
 * @property string|null $pdf_url
 * @property array<array-key, mixed>|null $meta
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $buyer
 * @property-read \App\Models\Contract|null $contract
 * @property-read \App\Models\Order|null $order
 * @property-read \App\Models\User $seller
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereBuyerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereContractId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice wherePdfUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereSellerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereSubtotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereTax($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Invoice extends Model
{
    use HasFactory;
    protected $fillable = ['order_id','contract_id','seller_id','buyer_id','currency','subtotal','tax','total','status','pdf_url','meta'];
    protected $casts = [ 'meta'=>'array' ];
    public function order(){ return $this->belongsTo(Order::class); }
    public function contract(){ return $this->belongsTo(Contract::class); }
    public function seller(){ return $this->belongsTo(User::class, 'seller_id'); }
    public function buyer(){ return $this->belongsTo(User::class, 'buyer_id'); }
}
