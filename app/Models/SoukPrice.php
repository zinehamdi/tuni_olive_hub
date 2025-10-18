<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $souk_name
 * @property string $governorate
 * @property string $variety
 * @property string $product_type
 * @property string|null $quality
 * @property numeric $price_min
 * @property numeric $price_max
 * @property numeric $price_avg
 * @property string $currency
 * @property string $unit
 * @property \Illuminate\Support\Carbon $date
 * @property numeric|null $change_percentage
 * @property string $trend
 * @property string|null $notes
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $price_range
 * @property-read mixed $trend_color
 * @property-read mixed $trend_icon
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SoukPrice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SoukPrice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SoukPrice query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SoukPrice whereChangePercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SoukPrice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SoukPrice whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SoukPrice whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SoukPrice whereGovernorate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SoukPrice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SoukPrice whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SoukPrice whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SoukPrice wherePriceAvg($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SoukPrice wherePriceMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SoukPrice wherePriceMin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SoukPrice whereProductType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SoukPrice whereQuality($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SoukPrice whereSoukName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SoukPrice whereTrend($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SoukPrice whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SoukPrice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SoukPrice whereVariety($value)
 * @mixin \Eloquent
 */
class SoukPrice extends Model
{
    protected $fillable = [
        'souk_name',
        'governorate',
        'variety',
        'product_type',
        'quality',
        'price_min',
        'price_max',
        'price_avg',
        'currency',
        'unit',
        'date',
        'change_percentage',
        'trend',
        'notes',
        'is_active'
    ];

    protected $casts = [
        'date' => 'date',
        'price_min' => 'decimal:2',
        'price_max' => 'decimal:2',
        'price_avg' => 'decimal:2',
        'change_percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Get famous Tunisian souks
    public static function getFamousSouks()
    {
        return [
            'Sfax' => 'صفاقس',
            'Tunis' => 'تونس',
            'Sousse' => 'سوسة',
            'Monastir' => 'المنستير',
            'Mahdia' => 'المهدية',
            'Kairouan' => 'القيروان',
            'Medenine' => 'مدنين',
            'Zarzis' => 'جرجيس',
            'Djerba' => 'جربة',
            'Gabes' => 'قابس',
            'Sidi Bouzid' => 'سيدي بوزيد',
            'Gafsa' => 'قفصة',
        ];
    }

    public function getPriceRangeAttribute()
    {
        return "{$this->price_min} - {$this->price_max} {$this->currency}";
    }

    public function getTrendIconAttribute()
    {
        return match($this->trend) {
            'up' => '📈',
            'down' => '📉',
            default => '➡️',
        };
    }

    public function getTrendColorAttribute()
    {
        return match($this->trend) {
            'up' => 'text-green-600',
            'down' => 'text-red-600',
            default => 'text-gray-600',
        };
    }
}
