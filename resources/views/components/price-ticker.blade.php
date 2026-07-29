@php
    $locale = app()->getLocale();

    // ─── Live Currency Conversion ─────────────────────────────────────────────
    // Souk prices are stored in TND. World prices are stored in EUR.
    // When locale = en/fr  → display in USD ($)
    // When locale = ar     → display in TND (دينار)
    $converter      = app(\App\Services\CurrencyConverter::class);
    $displayCurrency = $converter->displayCurrency($locale); // 'TND' or 'USD'
    $tndToDisplay   = ($displayCurrency === 'USD') ? $converter->getTndToUsd() : 1.0;
    $eurToDisplay   = ($displayCurrency === 'USD') ? $converter->getEurToUsd() : 3.15; // EUR→TND fallback

    // ─── Souk Data ────────────────────────────────────────────────────────────
    $allSoukPrices = \Illuminate\Support\Facades\Cache::remember('price_ticker_souk_prices', 300, function() {
        return \App\Models\SoukPrice::where('is_active', true)
            ->where('date', '>=', now()->subDays(7))
            ->select('souk_name', 'product_type', 'variety', 'quality')
            ->selectRaw('AVG(price_avg) as avg_price')
            ->selectRaw('AVG(price_min) as min_price')
            ->selectRaw('AVG(price_max) as max_price')
            ->groupBy('souk_name', 'product_type', 'variety', 'quality')
            ->orderBy('souk_name')
            ->get();
    });
    
    // Get world prices (stored in EUR)
    $worldAvgEUR = \Illuminate\Support\Facades\Cache::remember('price_ticker_world_avg', 300, function() {
        return \App\Models\WorldOlivePrice::where('date', '>=', now()->subDays(7))
            ->where('quality', 'EVOO')
            ->avg('price');
    });

    // ─── Souk name translations ───────────────────────────────────────────────
    $soukNames = [
        'Sfax'     => 'صفاقس',   'Tunis'    => 'تونس',
        'Sousse'   => 'سوسة',    'Monastir' => 'المنستير',
        'Mahdia'   => 'المهدية', 'Kairouan' => 'القيروان',
        'Medenine' => 'مدنين',   'Zarzis'   => 'جرجيس',
        'Djerba'   => 'جربة',    'Gabes'    => 'قابس',
    ];

    /**
     * Format a souk price (stored in TND) into the display currency.
     * Souk prices are always stored in TND.
     */
    $formatSoukPrice = function(float $tndAmount) use ($displayCurrency, $tndToDisplay) {
        $converted = round($tndAmount * $tndToDisplay, 2);
        if ($displayCurrency === 'USD') {
            return '$' . number_format($converted, 2);
        }
        return number_format($converted, 2) . ' TND';
    };

    /**
     * Format a world price (stored in EUR) into the display currency.
     */
    $formatWorldPrice = function(?float $eurAmount) use ($displayCurrency, $eurToDisplay) {
        if (!$eurAmount) return null;
        $converted = round($eurAmount * $eurToDisplay, 2);
        if ($displayCurrency === 'USD') {
            return '$' . number_format($converted, 2) . '/kg';
        }
        return number_format($converted, 2) . ' TND/kg';
    };
@endphp


<style>
    /* Horizontal Scrolling Ticker (News Channel Style - All Screens) */
    .ticker-wrapper {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        overflow: hidden;
        position: relative;
        padding-inline-end: 90px;
    }
    .ticker-content {
        display: flex;
        align-items: center;
        gap: 1rem;
        white-space: nowrap;
        flex-shrink: 0;
        width: max-content;
        min-width: max-content;
        animation: price-scroll-marquee 50s linear infinite;
        will-change: transform;
    }
    [dir="rtl"] .ticker-content {
        animation: price-scroll-marquee-rtl 50s linear infinite;
    }
    .ticker-wrapper:hover .ticker-content {
        animation-play-state: paused;
    }
    .ticker-item {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        flex-shrink: 0;
    }
    .ticker-separator {
        color: rgba(107, 74, 28, 0.4);
        font-size: 0.75rem;
        flex-shrink: 0;
    }
    @keyframes price-scroll-marquee {
        0% { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }
    @keyframes price-scroll-marquee-rtl {
        0% { transform: translateX(0); }
        100% { transform: translateX(50%); }
    }
</style>

{{-- Horizontal Price Ticker Bar --}}
<div class="relative overflow-hidden bg-gradient-to-r from-[#C8A356] via-[#d4b166] to-[#C8A356] backdrop-blur-md border-b border-[#b08a3c]/30 shadow-sm flex items-center" style="height: 46px; min-height: 46px; overflow: hidden;">
    <!-- Ticker Content -->
    <div class="relative z-10 w-full h-full flex items-center">
        <div class="ticker-wrapper">
            <div class="ticker-content">
                <!-- Header -->
                <div class="ticker-item">
                    <span class="text-xl">📊</span>
                    <span class="font-extrabold text-base text-gray-900">{{ __('Live Prices from Tunisian Souks') }}</span>
                </div>
                
                <!-- Separator -->
                <div class="ticker-separator">●</div>
                
                <!-- All Tunisian Souk Prices -->
                @foreach($allSoukPrices as $price)
                    <div class="ticker-item">
                        <span class="text-lg">
                            @if($price->product_type === 'olive')
                                🫒
                            @else
                                <img src="{{ asset('images/olive-oil.png') }}" alt="Oil" class="w-5 h-5 object-contain inline-block">
                            @endif
                        </span>
                        <span class="text-sm font-black text-gray-950">
                            {{ $locale === 'ar' ? ($soukNames[$price->souk_name] ?? $price->souk_name) : $price->souk_name }}
                        </span>
                        <span class="text-xs font-bold text-gray-800">
                            @if($price->product_type === 'olive')
                                ({{ ucfirst($price->variety) }})
                            @else
                                ({{ __('nav.oil') }})
                            @endif
                        </span>
                        <span class="bg-white/40 text-gray-950 px-2.5 py-1 rounded-lg font-black text-sm whitespace-nowrap shadow-sm">
                            {{ $formatSoukPrice((float)$price->avg_price) }}
                        </span>
                    </div>
                    
                    <div class="ticker-separator">|</div>
                @endforeach
                
                <!-- World Market -->
@php $worldFormatted = $formatWorldPrice($worldAvgEUR ? (float)$worldAvgEUR : null); @endphp
                @if($worldFormatted)
                    <div class="ticker-item">
                        <span class="text-lg">🌍</span>
                        <span class="text-sm font-black text-gray-950">{{ __('World Market') }}</span>
                        <span class="bg-white/40 text-gray-950 px-2.5 py-1 rounded-lg font-black text-sm whitespace-nowrap shadow-sm">
                            {{ $worldFormatted }}
                        </span>
                    </div>
                    
                    <div class="ticker-separator">●</div>
                @endif
                
                <!-- Duplicate content for seamless loop -->
                <div class="ticker-item">
                    <span class="text-xl">📊</span>
                    <span class="font-bold text-sm text-gray-900">{{ __('Live Prices from Tunisian Souks') }}</span>
                </div>
                
                <div class="ticker-separator">●</div>
                
                @foreach($allSoukPrices as $price)
                    <div class="ticker-item">
                        <span class="text-base">
                            @if($price->product_type === 'olive')
                                🫒
                            @else
                                <img src="{{ asset('images/olive-oil.png') }}" alt="Oil" class="w-4 h-4 object-contain inline-block">
                            @endif
                        </span>
                        <span class="text-xs font-bold text-gray-900">
                            {{ $locale === 'ar' ? ($soukNames[$price->souk_name] ?? $price->souk_name) : $price->souk_name }}
                        </span>
                        <span class="text-xs opacity-75 text-gray-800">
                            @if($price->product_type === 'olive')
                                ({{ ucfirst($price->variety) }})
                            @else
                                ({{ __('nav.oil') }})
                            @endif
                        </span>
                        <span class="bg-white/30 text-gray-900 px-2 py-0.5 rounded font-bold text-xs whitespace-nowrap backdrop-blur-sm">
                            {{ $formatSoukPrice((float)$price->avg_price) }}
                        </span>
                    </div>
                    
                    <div class="ticker-separator">|</div>
                @endforeach
                
@if($worldFormatted)
                    <div class="ticker-item">
                        <span class="text-base">🌍</span>
                        <span class="text-xs font-bold text-gray-900">{{ __('World Market') }}</span>
                        <span class="bg-white/30 text-gray-900 px-2 py-0.5 rounded font-bold text-xs whitespace-nowrap backdrop-blur-sm">
                            {{ $worldFormatted }}
                        </span>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- View All Button (Fixed on right) -->
        <a href="{{ route('prices.index') }}" 
           class="absolute right-0 top-0 bottom-0 flex items-center gap-1 bg-gradient-to-l from-[#C8A356] via-[#b08a3c] to-transparent hover:from-[#b08a3c] text-gray-900 px-4 transition z-20 shadow-lg backdrop-blur-sm">
            <span class="text-xs font-bold hidden sm:inline">{{ __('View All') }}</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>
    </div>
</div>
