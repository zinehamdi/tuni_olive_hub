<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketingService extends Model
{
    protected $fillable = [
        'title_ar', 'title_en', 'title_fr',
        'price_tnd_weekly', 'currency', 'icon_url',
        'results_ar', 'results_en', 'results_fr'
    ];
}
