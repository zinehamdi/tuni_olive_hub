<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\Obfuscator;

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

    protected static function booted()
    {
        static::saved(function ($listing) {
            \Illuminate\Support\Facades\Cache::forget('home_featured_listings');
            \App\Services\OgImageService::clearCache($listing);
        });

        static::deleted(function ($listing) {
            \Illuminate\Support\Facades\Cache::forget('home_featured_listings');
            \App\Services\OgImageService::clearCache($listing);
        });
    }
    
    /**
     * The attributes that are mass assignable.
     * الحقول القابلة للتعبئة الجماعية
     *
     * @var array<string>
     */
    protected $fillable = [
        'product_id',
        'seller_id',
        'status', 'packaging',
        'price',
        'currency',
        'quantity',
        'unit',
        'min_order',
        'payment_methods',
        'delivery_options',
        'media',
        'is_featured',
        'tree_count',
        'sale_mode',
        'price_mode'
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
        'is_featured' => 'boolean',
        'tree_count' => 'integer'
    ];

    /**
     * Determine if this listing is a standing orchard / saniya sale
     */
    public function getIsSaniyaAttribute(): bool
    {
        return $this->sale_mode === 'saniya' 
            || ($this->packaging && str_contains($this->packaging, 'سانية'))
            || $this->unit === 'سانية';
    }

    /**
     * Get clean unit label for estimated harvest quantity (e.g. طن / kg)
     */
    public function getQuantityUnitLabelAttribute(): string
    {
        $locale = app()->getLocale();
        $u = $this->unit;
        if ($this->is_saniya && ($u === 'سانية' || $u === 'saniya' || empty($u))) {
            $u = 'ton';
        }
        return match($u) {
            'kg', 'كغ', 'كيلو' => match($locale) { 'ar' => 'كغ', default => 'kg' },
            'ton', 'tonne', 'طن' => match($locale) { 'ar' => 'طن', 'fr' => 'tonne', default => 'ton' },
            'liter', 'لتر' => match($locale) { 'ar' => 'لتر', default => 'L' },
            'bottle', 'قارورة' => match($locale) { 'ar' => 'قارورة', default => 'bottle' },
            'can', 'صفيحة' => match($locale) { 'ar' => 'صفيحة', default => 'can' },
            'barrel', 'برميل' => match($locale) { 'ar' => 'برميل', default => 'barrel' },
            'piece', 'قطعة' => match($locale) { 'ar' => 'قطعة', default => 'piece' },
            default => $u ?? '',
        };
    }

    /**
     * Get clean unit label for price (e.g. / السانية بالكامل vs / طن)
     */
    public function getPriceUnitLabelAttribute(): string
    {
        $locale = app()->getLocale();
        if ($this->is_saniya && ($this->price_mode === 'whole' || $this->unit === 'سانية' || empty($this->price_mode))) {
            return match($locale) {
                'fr' => 'Le verger complet',
                'en' => 'Whole orchard',
                default => 'السانية بالكامل',
            };
        }
        
        $u = $this->unit;
        if ($this->is_saniya && ($u === 'سانية' || $u === 'saniya' || empty($u))) {
            $u = 'ton';
        }

        return match($u) {
            'kg', 'كغ', 'كيلو' => match($locale) { 'ar' => 'كلغ', default => 'kg' },
            'ton', 'tonne', 'طن' => match($locale) { 'ar' => 'طن', 'fr' => 'tonne', default => 'ton' },
            'liter', 'لتر' => match($locale) { 'ar' => 'لتر', default => 'L' },
            'bottle', 'قارورة' => match($locale) { 'ar' => 'قارورة', default => 'bottle' },
            'can', 'صفيحة' => match($locale) { 'ar' => 'صفيحة', default => 'can' },
            'barrel', 'برميل' => match($locale) { 'ar' => 'برميل', default => 'barrel' },
            'piece', 'قطعة' => match($locale) { 'ar' => 'قطعة', default => 'piece' },
            default => $u ?? '',
        };
    }

    /**
     * Get clean numeric float format for min_order without trailing zeros (.000)
     */
    public function getFormattedMinOrderAttribute()
    {
        if ($this->min_order === null || $this->min_order === '') {
            return null;
        }
        return (float) $this->min_order;
    }

    /**
     * Get clean numeric float format for quantity without trailing zeros (.000)
     */
    public function getFormattedQuantityAttribute()
    {
        if ($this->quantity === null || $this->quantity === '') {
            return null;
        }
        return (float) $this->quantity;
    }
    
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

    // ─────────────────────────────────────────────────────────────────────────
    // URL Obfuscation (Hashids)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Return the obfuscated Hashid as the route key.
     * route('listings.show', $listing) → /ar/listings/k9XqL
     */
    public function getRouteKey(): mixed
    {
        return Obfuscator::encode($this->id, 'listing');
    }

    /**
     * Resolve listing from route binding.
     * Supports both legacy numeric URLs (/ar/listings/42) and new Hashids (/ar/listings/k9XqL).
     */
    public function resolveRouteBinding($value, $field = null): ?static
    {
        $locale = app()->getLocale() ?: 'ar';

        if (is_numeric($value)) {
            // Legacy numeric ID — backward compatible
            $listing = $this->where('id', (int) $value)->first();
            if (!$listing) {
                // Deleted legacy listing -> 301 redirect to products section (eliminates soft 404 in GSC)
                throw new \Illuminate\Http\Exceptions\HttpResponseException(
                    redirect(url($locale . '/#products'), 301)
                );
            }
            return $listing;
        }

        $id = Obfuscator::decode((string) $value, 'listing');
        if ($id === null) {
            abort(404);
        }

        $listing = $this->where('id', $id)->first();
        if (!$listing) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                redirect(url($locale . '/#products'), 301)
            );
        }

        return $listing;
    }

    /**
     * Convenience accessor: $listing->hashid
     */
    public function getHashidAttribute(): string
    {
        return Obfuscator::encode($this->id, 'listing');
    }
}
