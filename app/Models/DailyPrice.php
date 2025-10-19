<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $product_id
 * @property numeric $price
 * @property string $currency
 * @property \Illuminate\Support\Carbon $date
 * @property numeric|null $change_percentage
 * @property string $source
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $trend
 * @property-read \App\Models\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyPrice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyPrice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyPrice query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyPrice whereChangePercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyPrice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyPrice whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyPrice whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyPrice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyPrice whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyPrice wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyPrice whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyPrice whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyPrice whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DailyPrice extends Model
{
    protected $fillable = [
        'product_id',
        'price',
        'currency',
        'date',
        'change_percentage',
        'source',
        'notes'
    ];

    protected $casts = [
        'date' => 'date',
        'price' => 'decimal:2',
        'change_percentage' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getTrendAttribute()
    {
        if ($this->change_percentage > 0) {
            return 'up';
        } elseif ($this->change_percentage < 0) {
            return 'down';
        }
        return 'stable';
    }
}
