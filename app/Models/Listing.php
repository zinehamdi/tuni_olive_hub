<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Listing Model - نموذج العرض
 * 
 * Represents a product listing in the marketplace
 * يمثل عرض منتج في السوق
 *
 * @package App\Models
 * @property int $id
 * @property int $product_id
 * @property int $seller_id
 * @property string $status
 * @property string|null $price
 * @property string $currency
 * @property string|null $quantity
 * @property string|null $unit
 * @property string|null $min_order
 * @property array<array-key, mixed>|null $payment_methods
 * @property array<array-key, mixed>|null $delivery_options
 * @property array<array-key, mixed>|null $media
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\User $seller
 * @method static \Database\Factories\ListingFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Listing newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Listing newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Listing query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Listing whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Listing whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Listing whereDeliveryOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Listing whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Listing whereMedia($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Listing whereMinOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Listing wherePaymentMethods($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Listing wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Listing whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Listing whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Listing whereSellerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Listing whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Listing whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Listing whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Listing extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     * الحقول القابلة للتعبئة الجماعية
     *
     * @var array<string>
     */
    protected $fillable = [
        'product_id',
        'seller_id',
        'status',
        'price',
        'currency',
        'quantity',
        'unit',
        'min_order',
        'payment_methods',
        'delivery_options',
        'media'
    ];
    
    /**
     * The attributes that should be cast.
     * الحقول التي يجب تحويلها
     *
     * @var array<string, string>
     */
    protected $casts = [
        'payment_methods' => 'array',
        'delivery_options' => 'array',
        'media' => 'array',
    ];
    
    /**
     * Get the product associated with this listing
     * الحصول على المنتج المرتبط بهذا العرض
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    /**
     * Get the seller (user) who created this listing
     * الحصول على البائع (المستخدم) الذي أنشأ هذا العرض
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}
