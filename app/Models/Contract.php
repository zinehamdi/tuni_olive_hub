<?php
/**
 * @property int $id
 * @property int $export_offer_id
 * @property int $buyer_id
 * @property string $payment_term
 * @property bool $escrow
 * @property \Illuminate\Support\Carbon|null $signed_at
 * @property string $status
 */

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $export_offer_id
 * @property int $buyer_id
 * @property string $payment_term
 * @property bool $escrow
 * @property \Illuminate\Support\Carbon|null $signed_at
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $buyer
 * @property-read \App\Models\ExportOffer $exportOffer
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereBuyerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereEscrow($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereExportOfferId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract wherePaymentTerm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereSignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'export_offer_id','buyer_id','payment_term','escrow','signed_at','status'
    ];

    protected $casts = [
        'escrow' => 'boolean',
        'signed_at' => 'datetime',
    ];

    public function exportOffer(){ return $this->belongsTo(ExportOffer::class); }
    public function buyer(){ return $this->belongsTo(User::class, 'buyer_id'); }
}
