<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\SoukPrice;
use App\Models\WorldOlivePrice;
use App\Models\DailyPrice;
use Illuminate\Support\Facades\DB;

class PriceController extends Controller
{
    public function index()
    {
        // Get latest souk prices (8 most recent)
        $soukPrices = SoukPrice::where('is_active', true)
            ->where('date', '>=', now()->subDays(7))
            ->orderBy('date', 'desc')
            ->limit(8)
            ->get();

        // Get latest world prices (4 most recent)
        $worldPrices = WorldOlivePrice::where('date', '>=', now()->subDays(7))
            ->orderBy('date', 'desc')
            ->limit(4)
            ->get();

// Calculate Tunisian average — oil only
$tunisianAvg = SoukPrice::where('is_active', true)
    ->where('product_type', 'oil')
    ->where('date', '>=', now()->subDays(7))
    ->avg('price_avg');
        $tunisianOliveAvg = SoukPrice::where('is_active', true)->where('product_type','olive')->where('date','>=', now()->subDays(7))->avg('price_avg');
        // Calculate world average
        $worldAvg = WorldOlivePrice::where('date', '>=', now()->subDays(7))
            ->avg('price');

        // Determine market trend
        $marketTrend = $this->getMarketTrend();

        return view('prices.index', compact(
            'soukPrices',
            'worldPrices',
            'tunisianAvg',
            'worldAvg',
            'marketTrend',
            'tunisianOliveAvg'
        ));
    }

    public function souks()
    {
        $souks = SoukPrice::where('is_active', true)
            ->orderBy('date', 'desc')
            ->orderBy('souk_name')
            ->paginate(20);

        return view('prices.souks', compact('souks'));
    }

    public function world()
    {
        $worldPrices = WorldOlivePrice::orderBy('date', 'desc')
            ->orderBy('country')
            ->paginate(20);

        return view('prices.world', compact('worldPrices'));
    }

    private function getMarketTrend()
    {
        $upCount = SoukPrice::where('is_active', true)
            ->where('trend', 'up')
            ->where('date', '>=', now()->subDays(7))
            ->count();

        $downCount = SoukPrice::where('is_active', true)
            ->where('trend', 'down')
            ->where('date', '>=', now()->subDays(7))
            ->count();

        if ($upCount > $downCount) {
            return __('Rising');
        } elseif ($downCount > $upCount) {
            return __('Falling');
        }
        return __('Stable');
    }
}
