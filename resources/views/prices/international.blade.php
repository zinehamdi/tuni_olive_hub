@extends('layouts.app')

@php
    $locale = app()->getLocale();
    
    $pageTitle = match($locale) {
        'ar' => 'أسعار زيت الزيتون العالمية اليوم | بورصة إسبانيا، إيطاليا، اليونان وتونس',
        'fr' => 'Cours Mondial & Prix International de l\'Huile d\'Olive | Espagne, Italie, Tunisie',
        default => 'International Olive Oil Prices Today | Spain, Italy, Greece & Tunisia Benchmark',
    };

    $pageDesc = match($locale) {
        'ar' => 'متابعة حية للأسعار والبورصات العالمية لزيت الزيتون البكر الممتاز: إسبانيا (خاين)، إيطاليا (باري)، اليونان، تونس، وتركيا. مقارنة الأسعار باليورو والدينار والدولار.',
        'fr' => 'Suivi en direct des cours mondiaux de l\'huile d\'olive extra vierge : Espagne (Jaén), Italie (Bari), Grèce, Tunisie et Turquie. Analyse des tendances et parité EUR/TND.',
        default => 'Live international extra virgin olive oil benchmark prices across Spain (Jaén), Italy (Bari), Greece, Tunisia, and Turkey. Global market trends, price comparisons and export insights.',
    };

    $countryFlags = [
        'Spain' => '🇪🇸', 'إسبانيا' => '🇪🇸', 'Espagne' => '🇪🇸',
        'Italy' => '🇮🇹', 'إيطاليا' => '🇮🇹', 'Italie' => '🇮🇹',
        'Greece' => '🇬🇷', 'اليونان' => '🇬🇷', 'Grèce' => '🇬🇷',
        'Turkey' => '🇹🇷', 'تركيا' => '🇹🇷', 'Turquie' => '🇹🇷',
        'Morocco' => '🇲🇦', 'المغرب' => '🇲🇦', 'Maroc' => '🇲🇦',
        'Tunisia' => '🇹🇳', 'تونس' => '🇹🇳', 'Tunisie' => '🇹🇳',
        'Portugal' => '🇵🇹', 'البرتغال' => '🇵🇹',
    ];

    $eurToTnd = 3.33;
    $usdToTnd = 3.12;

    $customHreflang = [
        'ar' => url('ar/' . rawurlencode('أسعار-زيت-الزيتون-العالمية')),
        'fr' => url('fr/prix-huile-olive-international'),
        'en' => url('en/international-olive-oil-prices'),
        'x-default' => url('ar/' . rawurlencode('أسعار-زيت-الزيتون-العالمية')),
    ];
    $customCanonical = $customHreflang[$locale] ?? url('en/international-olive-oil-prices');
@endphp

@section('title', $pageTitle)
@section('description', $pageDesc)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    {{-- Breadcrumbs --}}
    <nav class="flex text-xs font-semibold text-gray-500 gap-2 items-center" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-[#6A8F3B] transition">{{ __('Home') }}</a>
        <span>/</span>
        <a href="{{ route('prices.index') }}" class="hover:text-[#6A8F3B] transition">{{ __('Tunisian Prices') }}</a>
        <span>/</span>
        <span class="text-gray-900 font-bold">{{ __('International Prices') }}</span>
    </nav>

    {{-- Hero Section --}}
    <div class="relative rounded-3xl overflow-hidden bg-gradient-to-r from-[#0C1A0F] via-[#142E18] to-[#0C1A0F] border border-[#C8A356]/30 shadow-2xl p-6 sm:p-10">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(200,163,86,0.2)_0%,transparent_60%)] pointer-events-none"></div>

        <div class="relative z-10 max-w-3xl space-y-4">
            <span class="inline-flex items-center px-3.5 py-1 rounded-full text-xs font-bold bg-[#C8A356]/20 text-[#F5E5C0] border border-[#C8A356]/40 backdrop-blur-md">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse mr-1.5 ml-1.5"></span>
                {{ __('Global Olive Oil Benchmark Index') }}
            </span>
            <h1 class="text-3xl sm:text-5xl font-black text-white tracking-tight leading-tight">
                @if($locale === 'ar')
                    أسعار زيت الزيتون <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#6A8F3B] to-[#C8A356]">العالمية والدولية</span>
                @elseif($locale === 'fr')
                    Cours Mondial & Prix <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#6A8F3B] to-[#C8A356]">International</span> de l'Huile d'Olive
                @else
                    International & Global <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#6A8F3B] to-[#C8A356]">Olive Oil Prices</span>
                @endif
            </h1>
            <p class="text-xs sm:text-sm text-gray-300 leading-relaxed">
                {{ $pageDesc }}
            </p>

            <div class="flex items-center gap-3 pt-2">
                <a href="{{ route('prices.index') }}" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs border border-white/20 transition flex items-center gap-2">
                    <span>🇹🇳</span>
                    <span>{{ __('View Local Tunisian Market Prices') }} →</span>
                </a>
                <a href="{{ route('seo.bulk') }}" class="px-4 py-2 rounded-xl bg-[#6A8F3B] hover:bg-[#5a7a2f] text-white font-bold text-xs shadow-md transition flex items-center gap-1.5">
                    <span>🚢</span>
                    <span>{{ __('Export Sourcing B2B') }}</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Global Market Cards --}}
    <div class="space-y-4">
        <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
            <span>🌍</span>
            <span>{{ __('Major Producing Mediterranean Markets (EVOO / kg)') }}</span>
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @forelse($worldPrices as $wp)
                @php
                    $flag = $countryFlags[$wp->country] ?? '🌐';
                    $eurPrice = (float)$wp->price;
                    $tndPrice = $eurPrice * $eurToTnd;
                @endphp
                <div class="bg-white border border-gray-200 hover:border-[#6A8F3B]/40 rounded-2xl p-5 shadow-xs hover:shadow-md transition space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-lg">{{ $flag }}</span>
                        <span class="text-xs font-bold text-gray-400">{{ $wp->date ? $wp->date->format('Y-m-d') : now()->format('Y-m-d') }}</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-base">{{ $wp->country }}</h3>
                        <span class="text-xs text-gray-500">{{ $wp->market_name ?? __('Reference Wholesale Benchmark') }}</span>
                    </div>
                    <div class="pt-2 border-t border-gray-100 flex items-baseline justify-between">
                        <div>
                            <span class="text-2xl font-black text-gray-900">€{{ number_format($eurPrice, 2) }}</span>
                            <span class="text-xs text-gray-400">/ kg</span>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-bold text-[#183b1c] block">≈ {{ number_format($tndPrice, 2) }} TND</span>
                            <span class="text-[10px] text-gray-400">{{ __('TND Equiv.') }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-4 bg-gray-50 border border-gray-200 rounded-2xl p-12 text-center text-gray-500">
                    {{ __('Global price benchmark data updating...') }}
                </div>
            @endforelse
        </div>
    </div>

    {{-- Educational Market Analysis Box --}}
    <div class="bg-gray-50 border border-gray-200 rounded-3xl p-6 sm:p-8 space-y-4 text-gray-800 text-xs sm:text-sm leading-relaxed">
        <h2 class="text-lg font-bold text-[#183b1c]">
            {{ __('Understanding International Olive Oil Price Dynamics') }}
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <b class="text-gray-900 block font-bold">🇪🇸 {{ __('Spain (Jaén Poolred Index)') }}</b>
                <p class="text-gray-600 text-xs">
                    Spain represents over 40% of global olive oil output. Prices established at the Jaén and Cordoba wholesale pools serve as the reference baseline for global commodity trading and bulk export pricing.
                </p>
            </div>
            <div class="space-y-2">
                <b class="text-gray-900 block font-bold">🇹🇳 {{ __('Tunisia Export Positioning') }}</b>
                <p class="text-gray-600 text-xs">
                    Tunisian EVOO provides an exceptional price-to-quality ratio, offering superior polyphenol concentrations and certified organic availability at competitive FOB rates from Sfax and Rades ports.
                </p>
            </div>
        </div>
    </div>

</div>

@push('head')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => 'ZinToop',
            'item' => url('/')
        ],
        [
            '@type' => 'ListItem',
            'position' => 2,
            'name' => __('Tunisian Prices'),
            'item' => route('prices.index')
        ],
        [
            '@type' => 'ListItem',
            'position' => 3,
            'name' => __('International Prices'),
            'item' => url()->current()
        ]
    ]
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endpush
@endsection
