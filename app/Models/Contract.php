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
