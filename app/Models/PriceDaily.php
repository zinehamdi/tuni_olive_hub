<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $date
 * @property string|null $global_oil_usd_ton
 * @property string|null $tunis_baz_tnd_kg
 * @property string|null $organic_tnd_l
 * @property array<array-key, mixed>|null $by_governorate_json
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceDaily newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceDaily newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceDaily query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceDaily whereByGovernorateJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceDaily whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceDaily whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceDaily whereGlobalOilUsdTon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceDaily whereOrganicTndL($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceDaily whereTunisBazTndKg($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriceDaily whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PriceDaily extends Model
{
    protected $table = 'prices_daily';
    public $incrementing = false;
    protected $primaryKey = 'date';
    protected $keyType = 'string';
    protected $fillable = ['date','global_oil_usd_ton','tunis_baz_tnd_kg','organic_tnd_l','by_governorate_json'];
    protected $casts = [
        'by_governorate_json' => 'array',
    ];
}
