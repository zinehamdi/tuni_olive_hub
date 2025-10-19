<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $seller_id
 * @property string $type
 * @property string $variety
 * @property string|null $quality
 * @property bool $is_organic
 * @property string|null $volume_liters
 * @property string|null $weight_kg
 * @property string $price
 * @property string $stock
 * @property array<array-key, mixed>|null $media
 * @property bool $is_premium
 * @property bool $export_ready
 * @property array<array-key, mixed>|null $certs
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Listing> $listings
 * @property-read int|null $listings_count
 * @property-read \App\Models\User $seller
 * @method static \Database\Factories\ProductFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCerts($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereExportReady($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereIsOrganic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereIsPremium($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereMedia($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereQuality($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereSellerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereVariety($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereVolumeLiters($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereWeightKg($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product withoutTrashed()
 * @mixin \Eloquent
 */
class Product extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'seller_id','type','variety','quality','is_organic','volume_liters','weight_kg','price','stock','media','is_premium','export_ready','certs'
    ];
    protected $casts = [
        'media' => 'array',
        'is_organic' => 'boolean',
        'is_premium' => 'boolean',
        'export_ready' => 'boolean',
        'certs' => 'array',
    ];
    public function seller() { return $this->belongsTo(User::class, 'seller_id'); }
    public function listings() { return $this->hasMany(Listing::class); }
}
