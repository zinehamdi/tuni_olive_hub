@php
    // Get ALL Tunisian Souk Prices (grouped by souk)
    $allSoukPrices = \App\Models\SoukPrice::where('is_active', true)
        ->where('date', '>=', now()->subDays(7))
        ->select('souk_name', 'product_type', 'variety', 'quality')
        ->selectRaw('AVG(price_avg) as avg_price')
        ->selectRaw('AVG(price_min) as min_price')
        ->selectRaw('AVG(price_max) as max_price')
        ->groupBy('souk_name', 'product_type', 'variety', 'quality')
        ->orderBy('souk_name')
        ->get();
    
    // Get world prices
    $worldAvgEUR = \App\Models\WorldOlivePrice::where('date', '>=', now()->subDays(7))
        ->where('quality', 'EVOO')
        ->avg('price');
    
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

<!-- Barcode-Style Price Ticker (All Screens) -->
<div class="relative overflow-hidden" style="background: repeating-linear-gradient(90deg, #DC2626 0px, #DC2626 8px, #FFFFFF 8px, #FFFFFF 16px); min-height: 50px;">
    <!-- White overlay for content -->
    <div class="absolute inset-0 bg-white/95"></div>
    
    <!-- Ticker Content -->
    <div class="relative z-10 w-full h-full flex items-center">
        <div class="ticker-wrapper">
            <div class="ticker-content">
                <!-- Header -->
                <div class="ticker-item">
                    <span class="text-xl">📊</span>
                    <span class="font-bold text-sm text-red-600">{{ __('Live Prices from Tunisian Souks') }}</span>
                </div>
                
                <!-- Separator -->
                <div class="ticker-separator">●</div>
                
                <!-- All Tunisian Souk Prices -->
                @foreach($allSoukPrices as $price)
                    <div class="ticker-item">
                        <span class="text-base">
                            @if($price->product_type === 'olive')
                                🫒
                            @else
                                <img src="{{ asset('images/olive-oil.png') }}" alt="Oil" class="w-4 h-4 object-contain inline-block">
                            @endif
                        </span>
                        <span class="text-xs font-bold text-red-600">
                            {{ app()->getLocale() === 'ar' ? ($soukNames[$price->souk_name] ?? $price->souk_name) : $price->souk_name }}
                        </span>
                        <span class="text-xs opacity-75">
                            @if($price->product_type === 'olive')
                                ({{ ucfirst($price->variety) }})
                            @else
                                ({{ app()->getLocale() === 'ar' ? 'زيت' : __('Oil') }})
                            @endif
                        </span>
                        <span class="bg-red-600 text-white px-2 py-0.5 rounded font-bold text-xs whitespace-nowrap">
                            {{ number_format($price->avg_price, 2) }} TND
                        </span>
                    </div>
                    
                    <div class="ticker-separator">|</div>
                @endforeach
                
                <!-- World Market -->
                @if($worldAvgEUR)
                    <div class="ticker-item">
                        <span class="text-base">🌍</span>
                        <span class="text-xs font-bold text-red-600">{{ __('World Market') }}</span>
                        <span class="bg-red-600 text-white px-2 py-0.5 rounded font-bold text-xs whitespace-nowrap">
                            {{ number_format($worldAvgEUR, 2) }} EUR/kg
                        </span>
                        <span class="text-xs opacity-75">({{ number_format($worldAvgTND, 2) }} TND)</span>
                    </div>
                    
                    <div class="ticker-separator">●</div>
                @endif
                
                <!-- Duplicate content for seamless loop -->
                <div class="ticker-item">
                    <span class="text-xl">📊</span>
                    <span class="font-bold text-sm text-red-600">{{ __('Live Prices from Tunisian Souks') }}</span>
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
                        <span class="text-xs font-bold text-red-600">
                            {{ app()->getLocale() === 'ar' ? ($soukNames[$price->souk_name] ?? $price->souk_name) : $price->souk_name }}
                        </span>
                        <span class="text-xs opacity-75">
                            @if($price->product_type === 'olive')
                                ({{ ucfirst($price->variety) }})
                            @else
                                ({{ app()->getLocale() === 'ar' ? 'زيت' : __('Oil') }})
                            @endif
                        </span>
                        <span class="bg-red-600 text-white px-2 py-0.5 rounded font-bold text-xs whitespace-nowrap">
                            {{ number_format($price->avg_price, 2) }} TND
                        </span>
                    </div>
                    
                    <div class="ticker-separator">|</div>
                @endforeach
                
                @if($worldAvgEUR)
                    <div class="ticker-item">
                        <span class="text-base">🌍</span>
                        <span class="text-xs font-bold text-red-600">{{ __('World Market') }}</span>
                        <span class="bg-red-600 text-white px-2 py-0.5 rounded font-bold text-xs whitespace-nowrap">
                            {{ number_format($worldAvgEUR, 2) }} EUR/kg
                        </span>
                        <span class="text-xs opacity-75">({{ number_format($worldAvgTND, 2) }} TND)</span>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- View All Button (Fixed on right) -->
        <a href="{{ route('prices.index') }}" 
           class="absolute right-0 top-0 bottom-0 flex items-center gap-1 bg-red-600 hover:bg-red-700 text-white px-3 transition z-20 shadow-lg">
            <span class="text-xs font-bold hidden sm:inline">{{ __('View All') }}</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>
    </div>
</div>

<style>
    /* Horizontal Scrolling Ticker (News Channel Style - All Screens) */
    .ticker-wrapper {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        overflow: hidden;
        padding-right: 80px; /* Space for button */
    }
    
    .ticker-content {
        display: flex;
        align-items: center;
        gap: 1rem;
        white-space: nowrap;
        animation: scroll-{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }} 60s linear infinite;
        padding-left: 100%;
    }
    
    .ticker-item {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        flex-shrink: 0;
    }
    
    .ticker-separator {
        color: rgba(220, 38, 38, 0.3);
        font-size: 0.75rem;
        flex-shrink: 0;
    }
    
    /* LTR: Scroll from right to left */
    @keyframes scroll-ltr {
        0% {
            transform: translateX(0);
        }
        100% {
            transform: translateX(-50%);
        }
    }
    
    /* RTL: Scroll from left to right */
    @keyframes scroll-rtl {
        0% {
            transform: translateX(0);
        }
        100% {
            transform: translateX(50%);
        }
    }
    
    /* Pause on hover */
    .ticker-wrapper:hover .ticker-content {
        animation-play-state: paused;
    }
</style>
