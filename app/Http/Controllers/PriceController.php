<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\SoukPrice;
use App\Models\WorldOlivePrice;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Support\Str;

class PriceController extends Controller
{
    /**
     * 🇹🇳 National Tunisian Olive Oil Price Hub
     * Aggregates Sfax, Kairouan, Sousse, Monastir, Mahdia, etc.
     */
    public function index()
    {
        // Get latest active souk prices (order by date desc)
        $soukPrices = SoukPrice::where('is_active', true)
            ->orderBy('date', 'desc')
            ->limit(16)
            ->get();

        $worldPrices = WorldOlivePrice::orderBy('date', 'desc')
            ->limit(6)
            ->get();

        // Calculate Tunisian average — oil only from latest available batch
        $tunisianAvg = SoukPrice::where('is_active', true)
            ->where('product_type', 'oil')
            ->where('date', '>=', now()->subDays(30))
            ->avg('price_avg');

        if (!$tunisianAvg) {
            $tunisianAvg = SoukPrice::where('is_active', true)
                ->where('product_type', 'oil')
                ->avg('price_avg') ?: 20.50;
        }

        $tunisianOliveAvg = SoukPrice::where('is_active', true)
            ->where('product_type', 'olive')
            ->where('date', '>=', now()->subDays(30))
            ->avg('price_avg');

        if (!$tunisianOliveAvg) {
            $tunisianOliveAvg = SoukPrice::where('is_active', true)
                ->where('product_type', 'olive')
                ->avg('price_avg') ?: 2.90;
        }

        $worldAvg = WorldOlivePrice::where('date', '>=', now()->subDays(30))
            ->avg('price');

        if (!$worldAvg) {
            $worldAvg = WorldOlivePrice::avg('price') ?: 7.50;
        }

        // Determine market trend
        $marketTrend = $this->getMarketTrend();

        // All available governorates for regional navigation within the national hub
        $famousSouks = SoukPrice::getFamousSouks();

        return view('prices.index', compact(
            'soukPrices',
            'worldPrices',
            'tunisianAvg',
            'worldAvg',
            'marketTrend',
            'tunisianOliveAvg',
            'famousSouks'
        ));
    }

    /**
     * 🌍 International & Global Olive Oil Benchmark Price Hub
     * Compares Spain (Jaén), Italy (Bari), Greece, Tunisia, Turkey
     */
    public function international(Request $request)
    {
        $locale = app()->getLocale();
        $path = $request->path();

        // Enforce 301 redirect from alias slugs to the exact language-specific canonical slug
        if ($locale === 'ar' && !str_contains($path, rawurlencode('أسعار-زيت-الزيتون-العالمية')) && !str_contains(urldecode($path), 'أسعار-زيت-الزيتون-العالمية')) {
            return redirect('/ar/' . rawurlencode('أسعار-زيت-الزيتون-العالمية'), 301);
        }
        if ($locale === 'fr' && !str_contains($path, 'prix-huile-olive-international')) {
            return redirect('/fr/prix-huile-olive-international', 301);
        }
        if ($locale === 'en' && !str_contains($path, 'international-olive-oil-prices')) {
            return redirect('/en/international-olive-oil-prices', 301);
        }

        $worldPrices = WorldOlivePrice::orderBy('date', 'desc')
            ->limit(16)
            ->get();

        $worldAvg = WorldOlivePrice::where('date', '>=', now()->subDays(30))
            ->avg('price') ?: WorldOlivePrice::avg('price') ?: 7.50;

        return view('prices.international', compact('worldPrices', 'worldAvg'));
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

    private function getMarketTrend(): string
    {
        $upCount = SoukPrice::where('is_active', true)
            ->where('trend', 'up')
            ->where('date', '>=', now()->subDays(30))
            ->count();

        $downCount = SoukPrice::where('is_active', true)
            ->where('trend', 'down')
            ->where('date', '>=', now()->subDays(30))
            ->count();

        if ($upCount > $downCount) {
            return __('Rising');
        } elseif ($downCount > $upCount) {
            return __('Falling');
        }
        return __('Stable');
    }
}
