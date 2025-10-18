<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $country
 * @property string|null $region
 * @property string|null $variety
 * @property string $quality
 * @property numeric $price
 * @property string $currency
 * @property string $unit
 * @property \Illuminate\Support\Carbon $date
 * @property numeric|null $change_percentage
 * @property string $trend
 * @property string|null $source
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $trend_color
 * @property-read mixed $trend_icon
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorldOlivePrice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorldOlivePrice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorldOlivePrice query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorldOlivePrice whereChangePercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorldOlivePrice whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorldOlivePrice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorldOlivePrice whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorldOlivePrice whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorldOlivePrice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorldOlivePrice whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorldOlivePrice wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorldOlivePrice whereQuality($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorldOlivePrice whereRegion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorldOlivePrice whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorldOlivePrice whereTrend($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorldOlivePrice whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorldOlivePrice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorldOlivePrice whereVariety($value)
 * @mixin \Eloquent
 */
class WorldOlivePrice extends Model
{
    protected $fillable = [
        'country',
        'region',
        'variety',
        'quality',
        'price',
        'currency',
        'unit',
        'date',
        'change_percentage',
        'trend',
        'source',
        'notes'
    ];

    protected $casts = [
        'date' => 'date',
        'price' => 'decimal:2',
        'change_percentage' => 'decimal:2',
    ];

    public static function getMajorProducers()
    {
        return [
            'Spain' => '🇪🇸 إسبانيا',
            'Italy' => '🇮🇹 إيطاليا',
            'Greece' => '🇬🇷 اليونان',
            'Tunisia' => '🇹🇳 تونس',
            'Turkey' => '🇹🇷 تركيا',
            'Morocco' => '🇲🇦 المغرب',
            'Portugal' => '🇵🇹 البرتغال',
            'Syria' => '🇸🇾 سوريا',
        ];
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
