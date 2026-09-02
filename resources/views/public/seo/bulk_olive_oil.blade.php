@extends('layouts.app')

@php
    $locale = app()->getLocale();
    
    $pageTitle = match($locale) {
        'ar' => 'زيت زيتون تونسي بالجملة وللتصدير | شراء زيت بكر ممتاز سائب ومباشر',
        'fr' => 'Huile d\'Olive Tunisienne en Vrac | Fournisseurs & Export B2B Extra Vierge',
        default => 'Bulk Tunisian Olive Oil Wholesale & Export | Extra Virgin EVOO Sourcing',
    };

    $pageDesc = match($locale) {
        'ar' => 'اشترِ زيت زيتون تونسي سائب وبالجملة مباشرة من كبرى المعاصر والمزارع في تونس. كميات تجارية للشحن الدولي (Flexitank / IBC / صهاريج) بجودة بكر ممتاز عالية ومطابقة للمواصفات الدولية.',
        'fr' => 'Achetez de l\'huile d\'olive tunisienne en vrac directement auprès des producteurs et moulins certifiés. Approvisionnement B2B en citerne, flexitank et fûts pour l\'exportation.',
        default => 'Source bulk Tunisian extra virgin olive oil directly from verified producers and mills. High-polyphenol Chemlali & Chetoui varieties available in flexitanks, IBCs, and ISO tanks for global export.',
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
        <a href="{{ route('catalog') }}" class="hover:text-[#6A8F3B] transition">{{ __('Catalog') }}</a>
        <span>/</span>
        <span class="text-gray-900 font-bold">{{ __('Bulk Olive Oil') }}</span>
    </nav>

    {{-- Hero Section --}}
    <div class="relative rounded-3xl overflow-hidden bg-gradient-to-r from-[#0C1A0F] via-[#142E18] to-[#0C1A0F] border border-[#C8A356]/30 shadow-2xl p-6 sm:p-10">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(200,163,86,0.2)_0%,transparent_60%)] pointer-events-none"></div>

        <div class="relative z-10 max-w-3xl space-y-4">
            <span class="inline-flex items-center px-3.5 py-1 rounded-full text-xs font-bold bg-[#C8A356]/20 text-[#F5E5C0] border border-[#C8A356]/40 backdrop-blur-md">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse mr-1.5 ml-1.5"></span>
                {{ __('B2B Sourcing & Wholesale Export') }}
            </span>
            <h1 class="text-3xl sm:text-5xl font-black text-white tracking-tight leading-tight">
                @if($locale === 'ar')
                    زيت زيتون تونسي <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#6A8F3B] to-[#C8A356]">بالجملة وللتصدير</span>
                @elseif($locale === 'fr')
                    Huile d'Olive Tunisienne <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#6A8F3B] to-[#C8A356]">en Vrac (Bulk)</span>
                @else
                    Bulk Tunisian <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#6A8F3B] to-[#C8A356]">Olive Oil Wholesale</span>
                @endif
            </h1>
            <p class="text-xs sm:text-sm text-gray-300 leading-relaxed">
                {{ $pageDesc }}
            </p>

            {{-- Trust Badges --}}
            <div class="flex flex-wrap items-center gap-4 pt-2 text-xs font-bold text-gray-200">
                <span class="flex items-center gap-1.5 bg-white/10 px-3 py-1.5 rounded-xl border border-white/15">
                    <span>🧪</span> {{ __('Lab Tested & Certified') }}
                </span>
                <span class="flex items-center gap-1.5 bg-white/10 px-3 py-1.5 rounded-xl border border-white/15">
                    <span>🚢</span> {{ __('FOB / CIF Shipping Ready') }}
                </span>
                <span class="flex items-center gap-1.5 bg-white/10 px-3 py-1.5 rounded-xl border border-white/15">
                    <span>🤝</span> {{ __('Zero Broker Fees') }}
                </span>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="text-xs font-bold text-gray-700">{{ __('Filter by Variety:') }}</span>
            <div class="flex gap-2">
                <a href="{{ request()->fullUrlWithQuery(['variety' => 'chemlali']) }}" class="px-3 py-1 rounded-lg text-xs font-bold {{ request('variety') === 'chemlali' ? 'bg-[#183b1c] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    {{ __('Chemlali') }}
                </a>
                <a href="{{ request()->fullUrlWithQuery(['variety' => 'chetoui']) }}" class="px-3 py-1 rounded-lg text-xs font-bold {{ request('variety') === 'chetoui' ? 'bg-[#183b1c] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    {{ __('Chetoui') }}
                </a>
                <a href="{{ request()->fullUrlWithQuery(['organic' => '1']) }}" class="px-3 py-1 rounded-lg text-xs font-bold {{ request('organic') === '1' ? 'bg-emerald-700 text-white' : 'bg-emerald-50 text-emerald-800 hover:bg-emerald-100' }}">
                    🌱 {{ __('Organic (Bio)') }}
                </a>
            </div>
        </div>

        <div>
            <a href="{{ route('services.appointment.consultation') }}" class="px-4 py-2 bg-gradient-to-r from-[#6A8F3B] to-[#5a7a2f] text-white text-xs font-bold rounded-xl shadow-sm hover:shadow transition flex items-center gap-1.5">
                <span>📋</span>
                <span>{{ __('Request Custom B2B Quote') }}</span>
            </a>
        </div>
    </div>

    {{-- Available Bulk Lots --}}
    <div class="space-y-4">
        <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
            <span>🫒</span>
            <span>{{ __('Live Bulk & Wholesale Listings in Tunisia') }}</span>
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($listings as $listing)
                <x-listing-card :listing="$listing" />
            @empty
                <div class="col-span-3 bg-gray-50 border border-gray-200 rounded-2xl p-12 text-center text-gray-500">
                    {{ __('No bulk listings matching this filter currently. Connect with our export desk for direct sourcing.') }}
                </div>
            @endforelse
        </div>

        <div class="pt-4">
            {{ $listings->links() }}
        </div>
    </div>

    {{-- Educational SEO Content & Specifications Box --}}
    <div class="bg-gray-50 border border-gray-200 rounded-3xl p-6 sm:p-8 space-y-6 text-gray-800 text-xs sm:text-sm leading-relaxed">
        <h2 class="text-lg sm:text-xl font-bold text-[#183b1c]">
            {{ __('Why Source Bulk Extra Virgin Olive Oil from Tunisia?') }}
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-5 rounded-2xl border border-gray-200 space-y-2">
                <b class="text-gray-900 block font-bold text-sm">🏆 {{ __('World-Class Polyphenols') }}</b>
                <p class="text-gray-600 text-xs">
                    Tunisian olive cultivars (especially Chetoui and Chemlali) are globally renowned for their exceptionally high antioxidant and polyphenol concentrations, providing extraordinary shelf stability and sensory excellence.
                </p>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-gray-200 space-y-2">
                <b class="text-gray-900 block font-bold text-sm">🚢 {{ __('Flexible Export Packaging') }}</b>
                <p class="text-gray-600 text-xs">
                    Shipments are structured to match your exact logistical requirements: Flexitanks (24,000 L / 21 metric tons), Food-Grade IBC containers (1,000 L), or 200L drums loaded from Sfax and Rades ports.
                </p>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-gray-200 space-y-2">
                <b class="text-gray-900 block font-bold text-sm">⚖️ {{ __('Direct Mill Verification') }}</b>
                <p class="text-gray-600 text-xs">
                    ZinToop allows international buyers to bypass traditional intermediaries, securing direct access to production batches with official chemical and sensory laboratory analysis certificates.
                </p>
            </div>
        </div>
    </div>

</div>

@push('head')
<script type="application/ld+json">
{!! json_encode([
    '@'.'context' => 'https://schema.org',
    '@'.'type' => 'AggregateOffer',
    'priceCurrency' => 'TND',
    'lowPrice' => '16.00',
    'highPrice' => '26.00',
    'offerCount' => (string)$listings->total(),
    'name' => $pageTitle
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endpush
@endsection
