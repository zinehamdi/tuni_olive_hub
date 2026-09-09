@php
    $locale = app()->getLocale();

    // ─── Live Currency Conversion ─────────────────────────────────────────────
    $converter       = app(\App\Services\CurrencyConverter::class);
    $displayCurrency = $converter->displayCurrency($locale);
    $tndToDisplay    = ($displayCurrency === 'USD') ? $converter->getTndToUsd() : 1.0;
    $eurToDisplay    = ($displayCurrency === 'USD') ? $converter->getEurToUsd() : 3.15;

    // ─── 1. Souk Olive Prices (حب الزيتون / زيتون طاولة) ──────────────────────
    $soukOlivePrices = \Illuminate\Support\Facades\Cache::remember('price_ticker_souk_olive_prices', 300, function () {
        $recent = \App\Models\SoukPrice::where('is_active', true)
            ->where('product_type', 'olive')
            ->where('date', '>=', now()->subDays(14))
            ->orderBy('date', 'desc')
            ->get();
        if ($recent->isNotEmpty()) {
            return $recent;
        }
        return \App\Models\SoukPrice::where('is_active', true)
            ->where('product_type', 'olive')
            ->orderBy('date', 'desc')
            ->take(8)
            ->get();
    });

    // ─── 2. Souk Olive Oil Prices (زيت الزيتون التونسي) ────────────────────────
    $soukOilPrices = \Illuminate\Support\Facades\Cache::remember('price_ticker_souk_oil_prices', 300, function () {
        $recent = \App\Models\SoukPrice::where('is_active', true)
            ->where('product_type', '!=', 'olive')
            ->where('date', '>=', now()->subDays(14))
            ->orderBy('date', 'desc')
            ->get();
        if ($recent->isNotEmpty()) {
            return $recent;
        }
        return \App\Models\SoukPrice::where('is_active', true)
            ->where('product_type', '!=', 'olive')
            ->orderBy('date', 'desc')
            ->take(8)
            ->get();
    });

    // ─── 3. International / World Prices by Country (الأسواق العالمية) ─────────
    $worldPrices = \Illuminate\Support\Facades\Cache::remember('price_ticker_world_prices_list', 300, function () {
        return \App\Models\WorldOlivePrice::orderBy('date', 'desc')
            ->get()
            ->unique('country')
            ->values();
    });

    // ─── Translation & Metadata Maps ───────────────────────────────────────────
    $soukNames = [
        'Sfax'         => 'صفاقس',
        'Tunis'        => 'تونس',
        'Sousse'       => 'سوسة',
        'Monastir'     => 'المنستير',
        'Mahdia'       => 'المهدية',
        'Kairouan'     => 'القيروان',
        'Medenine'     => 'مدنين',
        'Zarzis'       => 'جرجيس',
        'Djerba'       => 'جربة',
        'Gabes'        => 'قابس',
        'Gafsa'        => 'قفصة',
        'Sidi Bouzid'  => 'سيدي بوزيد',
        'Kasserine'    => 'القصرين',
        'Zanouch'      => 'زانوش',
        'Sbiba'        => 'سبيبة',
        'Ghannouch'    => 'غنوش',
        'Bouhajla'     => 'بوحجلة',
    ];

    $countryMeta = [
        'Spain'    => ['flag' => '🇪🇸', 'ar' => 'إسبانيا',  'fr' => 'Espagne',  'en' => 'Spain'],
        'Italy'    => ['flag' => '🇮🇹', 'ar' => 'إيطاليا',  'fr' => 'Italie',   'en' => 'Italy'],
        'Greece'   => ['flag' => '🇬🇷', 'ar' => 'اليونان',  'fr' => 'Grèce',    'en' => 'Greece'],
        'Portugal' => ['flag' => '🇵🇹', 'ar' => 'البرتغال', 'fr' => 'Portugal', 'en' => 'Portugal'],
        'Turkey'   => ['flag' => '🇹🇷', 'ar' => 'تركيا',    'fr' => 'Turquie',  'en' => 'Turkey'],
        'Tunisia'  => ['flag' => '🇹🇳', 'ar' => 'تونس',     'fr' => 'Tunisie',  'en' => 'Tunisia'],
        'Morocco'  => ['flag' => '🇲🇦', 'ar' => 'المغرب',   'fr' => 'Maroc',    'en' => 'Morocco'],
        'Syria'    => ['flag' => '🇸🇾', 'ar' => 'سوريا',    'fr' => 'Syrie',    'en' => 'Syria'],
    ];

    $qualityTranslations = [
        'EVOO'     => ['ar' => 'بكر ممتاز', 'fr' => 'Extra Vierge', 'en' => 'EVOO'],
        'virgin'   => ['ar' => 'بكر',        'fr' => 'Vierge',       'en' => 'Virgin'],
        'lampante' => ['ar' => 'وقاد',       'fr' => 'Lampante',     'en' => 'Lampante'],
    ];

    // ─── Price formatters ────────────────────────────────────────────────────
    $formatSoukOlivePrice = function (float $tndAmount) use ($displayCurrency, $tndToDisplay, $locale) {
        $converted = round($tndAmount * $tndToDisplay, 2);
        $unitText = ($locale === 'ar') ? '/كغ' : '/kg';
        return ($displayCurrency === 'USD')
            ? '$' . number_format($converted, 2) . $unitText
            : number_format($converted, 2) . ' TND' . $unitText;
    };

    $formatSoukOilPrice = function (float $tndAmount, ?string $unit = 'L') use ($displayCurrency, $tndToDisplay, $locale) {
        $converted = round($tndAmount * $tndToDisplay, 2);
        $unitLabel = ($unit === 'kg') 
            ? (($locale === 'ar') ? '/كغ' : '/kg') 
            : (($locale === 'ar') ? '/لتر' : '/L');
        return ($displayCurrency === 'USD')
            ? '$' . number_format($converted, 2) . $unitLabel
            : number_format($converted, 2) . ' TND' . $unitLabel;
    };

    $formatWorldPrice = function (float $eurAmount) use ($displayCurrency, $eurToDisplay, $locale) {
        $unitText = ($locale === 'ar') ? '/كغ' : '/kg';
        if ($displayCurrency === 'USD') {
            $converted = round($eurAmount * $eurToDisplay, 2);
            return '$' . number_format($converted, 2) . $unitText;
        }
        return number_format($eurAmount, 2) . ' €' . $unitText;
    };
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
        animation: ticker-ltr 110s linear infinite;
    }
    [dir="rtl"] .ticker-track {
        animation: ticker-rtl 110s linear infinite;
    }
    .ticker-wrapper:hover .ticker-track {
        animation-play-state: paused;
    }
    .ticker-strip {
        display: flex;
        align-items: center;
        gap: 0.9rem;
        white-space: nowrap;
        flex-shrink: 0;
        padding-inline-end: 1.25rem;
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
        gap: 0.35rem;
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

{{-- ── Price Ticker Bar ────────────────────────────────────────────────────── --}}
<div class="relative overflow-hidden bg-gradient-to-r from-[#C8A356] via-[#d4b166] to-[#C8A356] backdrop-blur-md border-b border-[#b08a3c]/30 shadow-sm flex items-center"
     dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}"
     style="height: 34px; min-height: 34px; overflow: hidden;">

    <div class="relative z-10 w-full h-full flex items-center">
        <div class="ticker-wrapper">

            <div class="ticker-track">

                {{-- ── STRIP 1 & 2 rendered via a loop so they are byte-identical ── --}}
                @for ($stripIndex = 0; $stripIndex < 2; $stripIndex++)
                <div class="ticker-strip" @if($stripIndex === 1) aria-hidden="true" @endif>

                    {{-- ── 1. OLIVES SECTION (حب الزيتون 🫒) ── --}}
                    @if($soukOlivePrices->isNotEmpty())
                        <div class="ticker-item">
                            <span class="text-sm">🫒</span>
                            <span class="font-black text-xs uppercase tracking-wide text-gray-950 bg-black/10 px-2 py-0.5 rounded">
                                {{ $locale === 'ar' ? 'أسعار الزيتون' : ($locale === 'fr' ? 'Marché Olives' : 'Live Olives') }}
                            </span>
                        </div>
                        <div class="ticker-separator">●</div>

                        @foreach ($soukOlivePrices as $price)
                            @php
                                $soukDisplay = ($locale === 'ar')
                                    ? ($soukNames[$price->souk_name] ?? $price->souk_name)
                                    : \App\Helpers\TextHelper::localizeArabicString($price->souk_name);
                                $varietyDisplay = $price->variety
                                    ? ($locale === 'ar' ? $price->variety : \App\Helpers\TextHelper::localizeArabicString($price->variety))
                                    : ($locale === 'ar' ? 'طاولة' : 'Table');
                            @endphp
                            <div class="ticker-item">
                                <span class="text-sm font-black text-gray-950">{{ $soukDisplay }}</span>
                                <span class="text-xs font-bold text-gray-800">({{ $varietyDisplay }})</span>
                                <span class="bg-white/50 text-gray-950 px-2 py-0.5 rounded font-black text-xs whitespace-nowrap shadow-sm border border-black/5">
                                    {{ $formatSoukOlivePrice((float) $price->price_avg) }}
                                </span>
                                @if($price->trend === 'up')
                                    <span class="text-xs text-green-800 font-bold" title="Up">▲</span>
                                @elseif($price->trend === 'down')
                                    <span class="text-xs text-red-800 font-bold" title="Down">▼</span>
                                @endif
                            </div>
                            <div class="ticker-separator">|</div>
                        @endforeach
                    @endif

                    {{-- ── 2. OLIVE OIL SECTION (زيت الزيتون التونسي 🫗) ── --}}
                    @if($soukOilPrices->isNotEmpty())
                        <div class="ticker-item">
                            <img src="{{ asset('images/olive-oil.png') }}" alt="Oil" class="w-3.5 h-3.5 object-contain inline-block">
                            <span class="font-black text-xs uppercase tracking-wide text-gray-950 bg-black/10 px-2 py-0.5 rounded">
                                {{ $locale === 'ar' ? 'زيت الزيتون' : ($locale === 'fr' ? 'Huile d\'Olive' : 'Olive Oil') }}
                            </span>
                        </div>
                        <div class="ticker-separator">●</div>

                        @foreach ($soukOilPrices as $price)
                            @php
                                $soukDisplay = ($locale === 'ar')
                                    ? ($soukNames[$price->souk_name] ?? $price->souk_name)
                                    : \App\Helpers\TextHelper::localizeArabicString($price->souk_name);
                                $qualityDisplay = $price->quality
                                    ? ($qualityTranslations[$price->quality][$locale] ?? $price->quality)
                                    : ($locale === 'ar' ? 'زيت' : 'Oil');
                            @endphp
                            <div class="ticker-item">
                                <span class="text-sm font-black text-gray-950">{{ $soukDisplay }}</span>
                                <span class="text-xs font-bold text-gray-800">({{ $qualityDisplay }})</span>
                                <span class="bg-white/50 text-gray-950 px-2 py-0.5 rounded font-black text-xs whitespace-nowrap shadow-sm border border-black/5">
                                    {{ $formatSoukOilPrice((float) $price->price_avg, $price->unit ?? 'L') }}
                                </span>
                                @if($price->trend === 'up')
                                    <span class="text-xs text-green-800 font-bold" title="Up">▲</span>
                                @elseif($price->trend === 'down')
                                    <span class="text-xs text-red-800 font-bold" title="Down">▼</span>
                                @endif
                            </div>
                            <div class="ticker-separator">|</div>
                        @endforeach
                    @endif

                    {{-- ── 3. WORLD MARKETS SECTION (الأسواق العالمية 🌍) ── --}}
                    @if($worldPrices->isNotEmpty())
                        <div class="ticker-item">
                            <span class="text-sm">🌍</span>
                            <span class="font-black text-xs uppercase tracking-wide text-gray-950 bg-black/10 px-2 py-0.5 rounded">
                                {{ $locale === 'ar' ? 'الأسواق العالمية' : ($locale === 'fr' ? 'Marché Mondial' : 'World Markets') }}
                            </span>
                        </div>
                        <div class="ticker-separator">●</div>

                        @foreach ($worldPrices as $wPrice)
                            @php
                                $meta = $countryMeta[$wPrice->country] ?? ['flag' => '🌐', 'ar' => $wPrice->country, 'fr' => $wPrice->country, 'en' => $wPrice->country];
                                $countryName = $meta[$locale] ?? $wPrice->country;
                                $wQuality = $wPrice->quality ? ($qualityTranslations[$wPrice->quality][$locale] ?? $wPrice->quality) : '';
                            @endphp
                            <div class="ticker-item">
                                <span class="text-sm">{{ $meta['flag'] }}</span>
                                <span class="text-sm font-black text-gray-950">{{ $countryName }}</span>
                                @if($wQuality)
                                    <span class="text-xs font-bold text-gray-800">({{ $wQuality }})</span>
                                @endif
                                <span class="bg-white/50 text-gray-950 px-2 py-0.5 rounded font-black text-xs whitespace-nowrap shadow-sm border border-black/5">
                                    {{ $formatWorldPrice((float) $wPrice->price) }}
                                </span>
                                @if($wPrice->trend === 'up')
                                    <span class="text-xs text-green-800 font-bold" title="Up">▲</span>
                                @elseif($wPrice->trend === 'down')
                                    <span class="text-xs text-red-800 font-bold" title="Down">▼</span>
                                @endif
                            </div>
                            <div class="ticker-separator">|</div>
                        @endforeach
                    @endif

                </div>{{-- /.ticker-strip --}}
                @endfor

            </div>{{-- /.ticker-track --}}
        </div>{{-- /.ticker-wrapper --}}

        {{-- View All button (fixed right) --}}
        <a href="{{ route('prices.index') }}"
           class="absolute right-0 top-0 bottom-0 flex items-center gap-1 bg-gradient-to-l from-[#C8A356] via-[#b08a3c] to-transparent hover:from-[#b08a3c] text-gray-950 px-3.5 transition z-20 shadow-lg backdrop-blur-sm">
            <span class="text-xs font-black hidden sm:inline">{{ __('View All') }}</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
            </svg>
        </a>

    </div>
</div>
