<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $buyer_id
 * @property int $seller_id
 * @property int $listing_id
 * @property string $qty
 * @property string $unit
 * @property string $price_unit
 * @property string $total
 * @property string $payment_method
 * @property string $payment_status
 * @property string $status
 * @property string|null $escrow_id
 * @property array<array-key, mixed>|null $meta
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $buyer
 * @property-read \App\Models\Listing $listing
 * @property-read \App\Models\User $seller
 * @method static \Database\Factories\OrderFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereBuyerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereEscrowId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereListingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order wherePaymentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order wherePriceUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereSellerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Order extends Model
{
    use HasFactory;
    protected $fillable = ['buyer_id','seller_id','listing_id','qty','unit','price_unit','total','payment_method','status','escrow_id','payment_status','meta'];

    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_READY = 'ready';
    public const STATUS_SHIPPING = 'shipping';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_CANCELLED = 'cancelled';

    public const PAY_PENDING = 'pending';
    public const PAY_AUTH = 'authorized';
    public const PAY_CAPTURED = 'captured';
    public const PAY_COLLECTED = 'collected';
    public const PAY_FAILED = 'failed';

    protected $casts = [
        'meta' => 'array',
    ];

    // State transitions (basic guardrails)
    public function canMoveTo(string $next): bool
    {
        $s = $this->status;
        $allowed = [
            self::STATUS_PENDING => [self::STATUS_CONFIRMED, self::STATUS_CANCELLED],
            self::STATUS_CONFIRMED => [self::STATUS_READY, self::STATUS_CANCELLED],
            self::STATUS_READY => [self::STATUS_SHIPPING, self::STATUS_CANCELLED],
            self::STATUS_SHIPPING => [self::STATUS_DELIVERED, self::STATUS_CANCELLED],
            self::STATUS_DELIVERED => [],
            self::STATUS_CANCELLED => [],
        ];
        return in_array($next, $allowed[$s] ?? [], true);
    }

    public function moveTo(string $next): void
    {
        if (!$this->canMoveTo($next)) {
            abort(422, 'Invalid order status transition.');
        }
        $this->status = $next;
        $this->save();
    }
    public function buyer() { return $this->belongsTo(User::class, 'buyer_id'); }
    public function seller() { return $this->belongsTo(User::class, 'seller_id'); }
    public function listing() { return $this->belongsTo(Listing::class); }
}
