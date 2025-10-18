@php
    // Get latest average prices
    $soukAvgTND = \App\Models\SoukPrice::where('is_active', true)
        ->where('product_type', 'olive')
        ->where('date', '>=', now()->subDays(7))
        ->avg('price_avg');
    
    $soukOilAvgTND = \App\Models\SoukPrice::where('is_active', true)
        ->where('product_type', 'oil')
        ->where('date', '>=', now()->subDays(7))
        ->avg('price_avg');
    
    $worldAvgEUR = \App\Models\WorldOlivePrice::where('date', '>=', now()->subDays(7))
        ->where('quality', 'EVOO')
        ->avg('price');
    
    // Currency conversion rate (approximate)
    $tndToEur = 0.30; // 1 TND ≈ 0.30 EUR
    $eurToTnd = 3.33; // 1 EUR ≈ 3.33 TND
    
    $soukAvgEUR = $soukAvgTND ? round($soukAvgTND * $tndToEur, 2) : null;
    $soukOilAvgEUR = $soukOilAvgTND ? round($soukOilAvgTND * $tndToEur, 2) : null;
    $worldAvgTND = $worldAvgEUR ? round($worldAvgEUR * $eurToTnd, 2) : null;
@endphp

<!-- Layer 2: Price Ticker Bar -->
<div class="bg-gradient-to-r from-[#C8A356] via-[#d4b166] to-[#C8A356] text-gray-900 border-t border-[#b08a3c]/30">
    <div class="max-w-7xl mx-auto px-2 sm:px-4 py-2.5">
        <!-- Desktop View: All prices in a row -->
        <div class="hidden sm:flex items-center justify-between overflow-x-auto scrollbar-hide">
            <div class="flex items-center gap-1 flex-shrink-0">
                <span class="text-lg">📊</span>
                <span class="font-bold text-sm">{{ __('Today\'s Prices') }}:</span>
            </div>
            
            <div class="flex items-center gap-4 sm:gap-6 flex-shrink-0 mx-auto">
                <!-- Tunisian Olive Prices -->
                @if($soukAvgTND)
                <div class="flex items-center gap-2">
                    <span class="text-base">🫒</span>
                    <div class="flex items-center gap-2">
                        <span class="text-xs sm:text-sm opacity-90">{{ __('Olives') }}</span>
                        <span class="bg-white/20 px-2 py-0.5 rounded font-bold text-xs sm:text-sm whitespace-nowrap">
                            {{ number_format($soukAvgTND, 2) }} TND/kg
                        </span>
                        <span class="text-xs opacity-75 hidden lg:inline">({{ number_format($soukAvgEUR, 2) }} EUR)</span>
                    </div>
                </div>
                @endif

                <!-- Separator -->
                <span class="text-gray-700/40">|</span>

                <!-- Tunisian Oil Prices -->
                @if($soukOilAvgTND)
                <div class="flex items-center gap-2">
                    <img src="{{ asset('images/olive-oil.png') }}" alt="Olive Oil" class="w-5 h-5 object-contain">
                    <div class="flex items-center gap-2">
                        <span class="text-xs sm:text-sm opacity-90">{{ app()->getLocale() === 'ar' ? 'زيت/سعر الباز' : __('Oil') }}</span>
                        <span class="bg-white/20 px-2 py-0.5 rounded font-bold text-xs sm:text-sm whitespace-nowrap">
                            {{ number_format($soukOilAvgTND, 2) }} TND/kg
                        </span>
                        <span class="text-xs opacity-75 hidden lg:inline">({{ number_format($soukOilAvgEUR, 2) }} EUR)</span>
                    </div>
                </div>
                @endif

                <!-- Separator -->
                <span class="text-gray-700/40">|</span>

                <!-- World Market -->
                @if($worldAvgEUR)
                <div class="flex items-center gap-2">
                    <span class="text-base">🌍</span>
                    <div class="flex items-center gap-2">
                        <span class="text-xs sm:text-sm opacity-90">{{ __('World') }}</span>
                        <span class="bg-white/20 px-2 py-0.5 rounded font-bold text-xs sm:text-sm whitespace-nowrap">
                            {{ number_format($worldAvgEUR, 2) }} EUR/kg
                        </span>
                        <span class="text-xs opacity-75 hidden lg:inline">({{ number_format($worldAvgTND, 2) }} TND)</span>
                    </div>
                </div>
                @endif
            </div>

            <!-- View All Link -->
            <a href="{{ route('prices.index') }}" class="flex items-center gap-1 hover:bg-black/10 px-3 py-1 rounded transition flex-shrink-0 ml-4">
                <span class="text-xs sm:text-sm font-semibold whitespace-nowrap">{{ __('View All') }}</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>

        <!-- Mobile View: Rotating Ticker -->
        <div class="sm:hidden flex items-center justify-center gap-2 relative overflow-hidden" style="min-height: 36px;">
            <span class="text-lg">📊</span>
            
            <div class="relative flex-1 flex items-center justify-center">
                <!-- Rotating price items -->
                <div class="price-ticker-mobile">
                    @if($soukAvgTND)
                    <div class="price-item flex items-center justify-center gap-2">
                        <span class="text-base">🫒</span>
                        <span class="text-xs font-semibold">{{ __('Olives') }}:</span>
                        <span class="bg-white/20 px-2 py-0.5 rounded font-bold text-xs whitespace-nowrap">
                            {{ number_format($soukAvgTND, 2) }} TND/kg
                        </span>
                    </div>
                    @endif

                    @if($soukOilAvgTND)
                    <div class="price-item flex items-center justify-center gap-2">
                        <img src="{{ asset('images/olive-oil.png') }}" alt="Oil" class="w-4 h-4 object-contain">
                        <span class="text-xs font-semibold">{{ app()->getLocale() === 'ar' ? 'زيت/سعر الباز' : __('Oil') }}:</span>
                        <span class="bg-white/20 px-2 py-0.5 rounded font-bold text-xs whitespace-nowrap">
                            {{ number_format($soukOilAvgTND, 2) }} TND/kg
                        </span>
                    </div>
                    @endif

                    @if($worldAvgEUR)
                    <div class="price-item flex items-center justify-center gap-2">
                        <span class="text-base">🌍</span>
                        <span class="text-xs font-semibold">{{ __('World') }}:</span>
                        <span class="bg-white/20 px-2 py-0.5 rounded font-bold text-xs whitespace-nowrap">
                            {{ number_format($worldAvgEUR, 2) }} EUR/kg
                        </span>
                    </div>
                    @endif
                </div>
            </div>
            
            <a href="{{ route('prices.index') }}" class="flex items-center gap-1 hover:bg-black/10 px-2 py-1 rounded transition flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>
</div>

<style>
    /* Mobile Price Ticker Animation */
    @media (max-width: 639px) {
        .price-ticker-mobile {
            position: relative;
            width: 100%;
        }
        
        .price-ticker-mobile .price-item {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            opacity: 0;
            animation: tickerFade 9s infinite;
        }
        
        .price-ticker-mobile .price-item:nth-child(1) {
            animation-delay: 0s;
        }
        
        .price-ticker-mobile .price-item:nth-child(2) {
            animation-delay: 3s;
        }
        
        .price-ticker-mobile .price-item:nth-child(3) {
            animation-delay: 6s;
        }
        
        @keyframes tickerFade {
            0% { opacity: 0; transform: translateY(10px); }
            5% { opacity: 1; transform: translateY(0); }
            30% { opacity: 1; transform: translateY(0); }
            35% { opacity: 0; transform: translateY(-10px); }
            100% { opacity: 0; transform: translateY(-10px); }
        }
    }
</style>
