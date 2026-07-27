@php
    // Get ALL Tunisian Souk Prices with Cache to eliminate page load lag
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
    
    // Get world prices with Cache
    $worldAvgEUR = \Illuminate\Support\Facades\Cache::remember('price_ticker_world_avg', 300, function() {
        return \App\Models\WorldOlivePrice::where('date', '>=', now()->subDays(7))
            ->where('quality', 'EVOO')
            ->avg('price');
    });
    
    // Currency conversion
    $tndToEur = 0.30;
    $eurToTnd = 3.33;
    $worldAvgTND = $worldAvgEUR ? round($worldAvgEUR * $eurToTnd, 2) : null;
    
    // Souk name translations
    $soukNames = [
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
    ];
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
    }
    .ticker-content {
        display: flex;
        align-items: center;
        gap: 1rem;
        white-space: nowrap;
        animation: price-scroll-marquee 50s linear infinite;
        will-change: transform;
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
                            {{ app()->getLocale() === 'ar' ? ($soukNames[$price->souk_name] ?? $price->souk_name) : $price->souk_name }}
                        </span>
                        <span class="text-xs font-bold text-gray-800">
                            @if($price->product_type === 'olive')
                                ({{ ucfirst($price->variety) }})
                            @else
                                ({{ __('nav.oil') }})
                            @endif
                        </span>
                        <span class="bg-white/40 text-gray-950 px-2.5 py-1 rounded-lg font-black text-sm whitespace-nowrap shadow-sm">
                            {{ number_format($price->avg_price, 2) }} TND
                        </span>
                    </div>
                    
                    <div class="ticker-separator">|</div>
                @endforeach
                
                <!-- World Market -->
                @if($worldAvgEUR)
                    <div class="ticker-item">
                        <span class="text-lg">🌍</span>
                        <span class="text-sm font-black text-gray-950">{{ __('World Market') }}</span>
                        <span class="bg-white/40 text-gray-950 px-2.5 py-1 rounded-lg font-black text-sm whitespace-nowrap shadow-sm">
                            {{ number_format($worldAvgEUR, 2) }} EUR/kg
                        </span>
                        <span class="text-xs font-bold text-gray-800">({{ number_format($worldAvgTND, 2) }} TND)</span>
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
                            {{ app()->getLocale() === 'ar' ? ($soukNames[$price->souk_name] ?? $price->souk_name) : $price->souk_name }}
                        </span>
                        <span class="text-xs opacity-75 text-gray-800">
                            @if($price->product_type === 'olive')
                                ({{ ucfirst($price->variety) }})
                            @else
                                ({{ __('nav.oil') }})
                            @endif
                        </span>
                        <span class="bg-white/30 text-gray-900 px-2 py-0.5 rounded font-bold text-xs whitespace-nowrap backdrop-blur-sm">
                            {{ number_format($price->avg_price, 2) }} TND
                        </span>
                    </div>
                    
                    <div class="ticker-separator">|</div>
                @endforeach
                
                @if($worldAvgEUR)
                    <div class="ticker-item">
                        <span class="text-base">🌍</span>
                        <span class="text-xs font-bold text-gray-900">{{ __('World Market') }}</span>
                        <span class="bg-white/30 text-gray-900 px-2 py-0.5 rounded font-bold text-xs whitespace-nowrap backdrop-blur-sm">
                            {{ number_format($worldAvgEUR, 2) }} EUR/kg
                        </span>
                        <span class="text-xs opacity-75 text-gray-800">({{ number_format($worldAvgTND, 2) }} TND)</span>
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
