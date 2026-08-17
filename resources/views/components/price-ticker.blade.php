@php
    $locale = app()->getLocale();

    // ─── Live Currency Conversion ─────────────────────────────────────────────
    $converter       = app(\App\Services\CurrencyConverter::class);
    $displayCurrency = $converter->displayCurrency($locale);
    $tndToDisplay    = ($displayCurrency === 'USD') ? $converter->getTndToUsd() : 1.0;
    $eurToDisplay    = ($displayCurrency === 'USD') ? $converter->getEurToUsd() : 3.15;

    // ─── Souk Data ────────────────────────────────────────────────────────────
    $allSoukPrices = \Illuminate\Support\Facades\Cache::remember('price_ticker_souk_prices', 300, function () {
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

    $worldAvgEUR = \Illuminate\Support\Facades\Cache::remember('price_ticker_world_avg', 300, function () {
        return \App\Models\WorldOlivePrice::where('date', '>=', now()->subDays(7))
            ->where('quality', 'EVOO')
            ->avg('price');
    });

    // ─── Souk name translations (AR display) ──────────────────────────────────
    $soukNames = [
        'Sfax'     => 'صفاقس',   'Tunis'    => 'تونس',
        'Sousse'   => 'سوسة',    'Monastir' => 'المنستير',
        'Mahdia'   => 'المهدية', 'Kairouan' => 'القيروان',
        'Medenine' => 'مدنين',   'Zarzis'   => 'جرجيس',
        'Djerba'   => 'جربة',    'Gabes'    => 'قابس',
    ];

    // ─── Price formatters ────────────────────────────────────────────────────
    $formatSoukPrice = function (float $tndAmount) use ($displayCurrency, $tndToDisplay) {
        $converted = round($tndAmount * $tndToDisplay, 2);
        return $displayCurrency === 'USD'
            ? '$' . number_format($converted, 2)
            : number_format($converted, 2) . ' TND';
    };

    $formatWorldPrice = function (?float $eurAmount) use ($displayCurrency, $eurToDisplay) {
        if (!$eurAmount) return null;
        $converted = round($eurAmount * $eurToDisplay, 2);
        return $displayCurrency === 'USD'
            ? '$' . number_format($converted, 2) . '/kg'
            : number_format($converted, 2) . ' TND/kg';
    };

    $worldFormatted = $formatWorldPrice($worldAvgEUR ? (float) $worldAvgEUR : null);
@endphp

<style>
    /* ── Price Ticker: Seamless Infinite Marquee ──────────────────────────────
     *
     * Technique: .ticker-track contains TWO identical .ticker-strip divs.
     * The track animates translateX(0 → -50%) — exactly one strip width.
     * When it snaps back to 0 the view is identical, so the loop is invisible.
     *
     *  EN / FR  (LTR) : right → left   (translateX 0 → -50%)
     *  AR       (RTL) : left  → right  (translateX 0 → +50%)
     */
    .ticker-wrapper {
        width: 100%;
        height: 100%;
        overflow: hidden;
        position: relative;
        padding-inline-end: 90px; /* room for "View All" button */
    }
    .ticker-track {
        display: flex;
        align-items: center;
        width: max-content;
        will-change: transform;
        animation: ticker-ltr 90s linear infinite;
    }
    [dir="rtl"] .ticker-track {
        animation: ticker-rtl 90s linear infinite;
    }
    .ticker-wrapper:hover .ticker-track {
        animation-play-state: paused;
    }
    .ticker-strip {
        display: flex;
        align-items: center;
        gap: 1rem;
        white-space: nowrap;
        flex-shrink: 0;
        padding-inline-end: 1rem;
        /* ── Force correct text direction inside the strip ─────────────────
         * Prevents text from being right-aligned when a parent has dir=rtl
         * but the ticker should display LTR (EN/FR mode). Overridden below
         * for the RTL variant. */
        direction: ltr;
        text-align: left;
    }
    [dir="rtl"] .ticker-strip {
        direction: rtl;
        text-align: right;
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
    @keyframes ticker-ltr {
        0%   { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }
    @keyframes ticker-rtl {
        0%   { transform: translateX(0); }
        100% { transform: translateX(50%); }
    }
</style>

@php
    /* Build the strip contents once; rendered twice via @include pattern below */
@endphp

{{-- ── Price Ticker Bar ────────────────────────────────────────────────────── --}}
<div class="relative overflow-hidden bg-gradient-to-r from-[#C8A356] via-[#d4b166] to-[#C8A356] backdrop-blur-md border-b border-[#b08a3c]/30 shadow-sm flex items-center"
     dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}"
     style="height: 32px; min-height: 32px; overflow: hidden;">

    <div class="relative z-10 w-full h-full flex items-center">
        <div class="ticker-wrapper">

            <div class="ticker-track">

                {{-- ── STRIP 1 & 2 rendered via a loop so they are byte-identical ── --}}
                @for ($stripIndex = 0; $stripIndex < 2; $stripIndex++)
                <div class="ticker-strip" @if($stripIndex === 1) aria-hidden="true" @endif>

                    {{-- Label --}}
                    <div class="ticker-item">
                        <span class="text-xl">📊</span>
                        <span class="font-extrabold text-sm text-gray-900">{{ __('Live Prices from Tunisian Souks') }}</span>
                    </div>
                    <div class="ticker-separator">●</div>

                    {{-- Souk prices --}}
                    @foreach ($allSoukPrices as $price)
                        <div class="ticker-item">
                            <span class="text-base">
                                @if ($price->product_type === 'olive') 🫒
                                @else <img src="{{ asset('images/olive-oil.png') }}" alt="Oil" class="w-4 h-4 object-contain inline-block">
                                @endif
                            </span>
                            <span class="text-sm font-black text-gray-950">
                                {{ $locale === 'ar' ? ($soukNames[$price->souk_name] ?? $price->souk_name) : \App\Helpers\TextHelper::localizeArabicString($price->souk_name) }}
                            </span>
                            <span class="text-xs font-bold text-gray-800">
                                @if ($price->product_type === 'olive')({{ \App\Helpers\TextHelper::localizeArabicString(ucfirst($price->variety)) }})
                                @else ({{ __('nav.oil') }})
                                @endif
                            </span>
                            <span class="bg-white/40 text-gray-950 px-2 py-0.5 rounded-lg font-black text-sm whitespace-nowrap shadow-sm">
                                {{ $formatSoukPrice((float) $price->avg_price) }}
                            </span>
                        </div>
                        <div class="ticker-separator">|</div>
                    @endforeach

                    {{-- World market --}}
                    @if ($worldFormatted)
                        <div class="ticker-item">
                            <span class="text-base">🌍</span>
                            <span class="text-sm font-black text-gray-950">{{ __('World Market') }}</span>
                            <span class="bg-white/40 text-gray-950 px-2 py-0.5 rounded-lg font-black text-sm whitespace-nowrap shadow-sm">
                                {{ $worldFormatted }}
                            </span>
                        </div>
                        <div class="ticker-separator">●</div>
                    @endif

                </div>{{-- /.ticker-strip --}}
                @endfor

            </div>{{-- /.ticker-track --}}
        </div>{{-- /.ticker-wrapper --}}

        {{-- View All button (fixed right) --}}
        <a href="{{ route('prices.index') }}"
           class="absolute right-0 top-0 bottom-0 flex items-center gap-1 bg-gradient-to-l from-[#C8A356] via-[#b08a3c] to-transparent hover:from-[#b08a3c] text-gray-900 px-4 transition z-20 shadow-lg backdrop-blur-sm">
            <span class="text-xs font-bold hidden sm:inline">{{ __('View All') }}</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>

    </div>
</div>
