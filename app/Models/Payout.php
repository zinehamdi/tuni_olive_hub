<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $payee_id
 * @property int $invoice_id
 * @property string $amount
 * @property string $currency
 * @property string $status
 * @property string $provider
 * @property string|null $provider_ref
 * @property array<array-key, mixed>|null $meta
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Invoice $invoice
 * @property-read \App\Models\User $payee
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payout newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payout newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payout query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payout whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payout whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payout whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payout whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payout whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payout whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payout wherePayeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payout whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payout whereProviderRef($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payout whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payout whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Payout extends Model
{
    use HasFactory;
    protected $fillable = ['payee_id','invoice_id','amount','currency','status','provider','provider_ref','meta'];
    protected $casts = [ 'meta'=>'array' ];
    public function payee(){ return $this->belongsTo(User::class, 'payee_id'); }
    public function invoice(){ return $this->belongsTo(Invoice::class); }
}
