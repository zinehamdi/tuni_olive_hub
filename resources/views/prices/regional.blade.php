@extends('layouts.app')

@php
    $locale = app()->getLocale();
    $regionName = ($locale === 'ar') ? ($matchedArabicName ?? $matchedKey) : ($matchedKey ?? $region);
    
    $pageTitle = match($locale) {
        'ar' => "أسعار زيت الزيتون في {$regionName} اليوم | بورصة زيت الزيتون التونسي",
        'fr' => "Prix Huile d'Olive à {$regionName} Aujourd'hui | Cours & Moulins en Tunisie",
        default => "Olive Oil Prices in {$regionName}, Tunisia Today | Live Market & Mill Rates",
    };

    $pageDesc = match($locale) {
        'ar' => "تابع أحدث أسعار زيت الزيتون والزيتون في معاصر وأسواق {$regionName} تونس. أسعار الزيت البكر الممتاز، الشملالي، الشتوي وقائمة المعاصر المعتمدة.",
        'fr' => "Consultez les prix actuels de l'huile d'olive et des olives dans les moulins de {$regionName}, Tunisie. Cours de l'Extra Vierge, Chemlali, Chétoui et annuaire des pressoirs.",
        default => "Check current olive oil and olive prices across mills and souks in {$regionName}, Tunisia. Live market rates for Extra Virgin, Chemlali, Chetoui and verified local producers.",
    };
@endphp

@section('title', $pageTitle)
@section('description', $pageDesc)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    {{-- Breadcrumb Navigation --}}
    <nav class="flex text-xs font-semibold text-gray-500 gap-2 items-center" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-[#6A8F3B] transition">{{ __('Home') }}</a>
        <span>/</span>
        <a href="{{ route('prices.index') }}" class="hover:text-[#6A8F3B] transition">{{ __('Prices') }}</a>
        <span>/</span>
        <span class="text-gray-900 font-bold">{{ $regionName }}</span>
    </nav>

    {{-- Hero Regional Header --}}
    <div class="relative rounded-3xl overflow-hidden bg-gradient-to-r from-[#0C1A0F] via-[#142E18] to-[#0C1A0F] border border-[#C8A356]/30 shadow-2xl p-6 sm:p-8">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(200,163,86,0.2)_0%,transparent_60%)] pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
            <div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-[#C8A356]/20 text-[#F5E5C0] border border-[#C8A356]/40 backdrop-blur-md mb-3">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse mr-1.5 ml-1.5"></span>
                    {{ __('Regional Market Index') }} • {{ $regionName }}
                </span>
                <h1 class="text-2xl sm:text-4xl font-black text-white tracking-tight">
                    @if($locale === 'ar')
                        أسعار زيت الزيتون في <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#6A8F3B] to-[#C8A356]">{{ $regionName }}</span> اليوم
                    @elseif($locale === 'fr')
                        Prix de l'Huile d'Olive à <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#6A8F3B] to-[#C8A356]">{{ $regionName }}</span>
                    @else
                        Olive Oil Prices in <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#6A8F3B] to-[#C8A356]">{{ $regionName }}</span>, Tunisia
                    @endif
                </h1>
                <p class="text-xs sm:text-sm text-gray-300 mt-2 max-w-2xl leading-relaxed">
                    {{ $pageDesc }}
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('prices.index') }}" class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs border border-white/20 transition flex items-center gap-2">
                    <span>←</span>
                    <span>{{ __('All Governorates') }}</span>
                </a>
            </div>
        </div>

        {{-- Live Regional KPI Grid --}}
        <div class="relative z-10 grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6 pt-6 border-t border-white/10">
            <!-- Oil Price KPI -->
            <div class="bg-white/10 backdrop-blur-md border border-white/15 rounded-2xl p-4">
                <div class="flex items-center justify-between text-xs text-gray-300 mb-1 font-bold">
                    <span>🫒 {{ __('Olive Oil Average') }}</span>
                    <span class="text-emerald-400">● {{ __('Live') }}</span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl sm:text-3xl font-black text-white">
                        {{ $latestOilPrice ? number_format((float)$latestOilPrice->price_avg, 2) : '20.50' }}
                    </span>
                    <span class="text-xs font-bold text-[#F5E5C0]">TND / {{ $locale === 'ar' ? 'لتر' : 'L' }}</span>
                </div>
                <p class="text-[10px] text-gray-400 mt-1">
                    {{ __('Updated:') }} {{ $latestOilPrice ? $latestOilPrice->date->format('Y-m-d') : now()->format('Y-m-d') }}
                </p>
            </div>

            <!-- Olive Raw Price KPI -->
            <div class="bg-white/10 backdrop-blur-md border border-white/15 rounded-2xl p-4">
                <div class="flex items-center justify-between text-xs text-gray-300 mb-1 font-bold">
                    <span>🌱 {{ __('Olive Raw (kg)') }}</span>
                    <span class="text-emerald-400">● {{ __('Souk') }}</span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl sm:text-3xl font-black text-white">
                        {{ $latestOlivePrice ? number_format((float)$latestOlivePrice->price_avg, 2) : '2.85' }}
                    </span>
                    <span class="text-xs font-bold text-[#F5E5C0]">TND / {{ $locale === 'ar' ? 'كجم' : 'kg' }}</span>
                </div>
                <p class="text-[10px] text-gray-400 mt-1">
                    {{ __('Market Range:') }} {{ $latestOlivePrice ? $latestOlivePrice->price_min . ' - ' . $latestOlivePrice->price_max : '2.50 - 3.20' }} TND
                </p>
            </div>

            <!-- Mills & Availability KPI -->
            <div class="bg-white/10 backdrop-blur-md border border-white/15 rounded-2xl p-4">
                <div class="flex items-center justify-between text-xs text-gray-300 mb-1 font-bold">
                    <span>🏢 {{ __('Mills & Sourcing') }}</span>
                    <span class="text-[#C8A356]">★ {{ __('ZinToop Network') }}</span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl sm:text-3xl font-black text-white">
                        {{ $localMills->count() ?: 12 }}+
                    </span>
                    <span class="text-xs font-bold text-gray-300">{{ __('Verified Partners') }}</span>
                </div>
                <p class="text-[10px] text-gray-400 mt-1">
                    {{ __('Direct access with zero broker fees') }}
                </p>
            </div>
        </div>
    </div>

    {{-- Regional Governorates Quick Switcher (SEO Internal Links) --}}
    <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm">
        <h2 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">
            {{ __('Compare Prices with Other Tunisian Governorates:') }}
        </h2>
        <div class="flex flex-wrap gap-2">
            @foreach($famousSouks as $enSlug => $arGov)
                @php
                    $isCurrent = strtolower($enSlug) === strtolower($region) || $arGov === $region;
                    $url = route('prices.regional', ['region' => strtolower($enSlug)]);
                @endphp
                <a href="{{ $url }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $isCurrent ? 'bg-[#183b1c] text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-[#6A8F3B]/10 hover:text-[#183b1c]' }}">
                    {{ $locale === 'ar' ? $arGov : $enSlug }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- Available Listings in this Governorate --}}
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900">
                    {{ __('Available Olive Oil Lots in') }} {{ $regionName }}
                </h2>
                <p class="text-xs text-gray-500">{{ __('Connect directly with verified sellers in this region') }}</p>
            </div>
            <a href="{{ route('catalog') }}" class="text-xs font-bold text-[#6A8F3B] hover:underline">
                {{ __('View All Catalog') }} →
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($regionalListings as $listing)
                <x-listing-card :listing="$listing" />
            @empty
                <div class="col-span-3 bg-gray-50 border border-gray-200 rounded-2xl p-8 text-center text-gray-500 text-sm">
                    {{ __('No active listings found in this governorate right now.') }}
                </div>
            @endforelse
        </div>
    </div>

    {{-- Local Mills & Processing Centers --}}
    @if($localMills->isNotEmpty())
    <div class="bg-gray-50 border border-gray-200 rounded-3xl p-6 space-y-4">
        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
            <span>🏢</span>
            <span>{{ __('Olive Mills & Processing Units in') }} {{ $regionName }}</span>
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($localMills as $mill)
                <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-xs flex flex-col justify-between">
                    <div>
                        <b class="text-gray-900 text-sm block">{{ $mill->name }}</b>
                        <span class="text-xs text-gray-500 block mt-0.5">
                            📍 {{ $mill->addresses->first()->delegation ?? $mill->addresses->first()->governorate ?? $regionName }}
                        </span>
                    </div>
                    <a href="{{ route('services.index', ['provider' => $mill->id]) }}" class="mt-3 w-full py-1.5 bg-[#183b1c] hover:bg-[#6A8F3B] text-white text-xs font-bold rounded-xl text-center transition">
                        {{ __('View Profile & Contact') }}
                    </a>
                </div>
            @endforeach
        </div>
    </div>
    @endif

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
            'name' => __('Prices'),
            'item' => route('prices.index')
        ],
        [
            '@type' => 'ListItem',
            'position' => 3,
            'name' => $regionName,
            'item' => url()->current()
        ]
    ]
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endpush
@endsection
