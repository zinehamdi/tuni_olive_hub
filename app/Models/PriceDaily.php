<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
