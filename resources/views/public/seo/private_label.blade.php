@extends('layouts.app')

@php
    $locale = app()->getLocale();
    
    $pageTitle = match($locale) {
        'ar' => 'تصنيع وتعبئة زيت الزيتون بالعلامة الخاصة في تونس | Private Label EVOO',
        'fr' => 'Huile d\'Olive en Marque Privée en Tunisie | Private Label & Co-Packing',
        default => 'Private Label Olive Oil Manufacturing in Tunisia | Custom Bottling & Export',
    };

    $pageDesc = match($locale) {
        'ar' => 'أطلق علامتك التجارية الخاصة لزيت الزيتون البكر الممتاز التونسي. خدمات تعبئة وتغليف مخصصة، تصميم الهوية والوسم، شهادات المطابقة الدولية والشحن المباشر إلى بلدك.',
        'fr' => 'Créez votre propre marque d\'huile d\'olive extra vierge tunisienne. Solutions complètes de co-packing, embouteillage personnalisé, étiquetage réglementaire et expédition internationale.',
        default => 'Launch your custom private label extra virgin olive oil brand from Tunisia. Full turnkey co-packing services: bottle selection, label compliance, lab certifications, and direct worldwide shipping.',
    };
@endphp

@section('title', $pageTitle)
@section('description', $pageDesc)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <nav class="flex text-xs font-semibold text-gray-500 gap-2 items-center" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-[#6A8F3B] transition">{{ __('Home') }}</a>
        <span>/</span>
        <a href="{{ route('services.pricing') }}" class="hover:text-[#6A8F3B] transition">{{ __('Services') }}</a>
        <span>/</span>
        <span class="text-gray-900 font-bold">{{ __('Private Label') }}</span>
    </nav>

    <div class="relative rounded-3xl overflow-hidden bg-gradient-to-r from-[#0C1A0F] via-[#142E18] to-[#0C1A0F] border border-[#C8A356]/30 shadow-2xl p-6 sm:p-10">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(200,163,86,0.2)_0%,transparent_60%)] pointer-events-none"></div>

        <div class="relative z-10 max-w-3xl space-y-4">
            <span class="inline-flex items-center px-3.5 py-1 rounded-full text-xs font-bold bg-[#C8A356]/20 text-[#F5E5C0] border border-[#C8A356]/40 backdrop-blur-md">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse mr-1.5 ml-1.5"></span>
                {{ __('Turnkey B2B Private Label & Co-Packing') }}
            </span>
            <h1 class="text-3xl sm:text-5xl font-black text-white tracking-tight leading-tight">
                @if($locale === 'ar')
                    تصنيع وتعبئة زيت الزيتون <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#6A8F3B] to-[#C8A356]">بالعلامة الخاصة</span>
                @elseif($locale === 'fr')
                    Huile d'Olive en <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#6A8F3B] to-[#C8A356]">Marque Privée (Private Label)</span>
                @else
                    Turnkey Private Label <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#6A8F3B] to-[#C8A356]">Olive Oil in Tunisia</span>
                @endif
            </h1>
            <p class="text-xs sm:text-sm text-gray-300 leading-relaxed">
                {{ $pageDesc }}
            </p>

            <div class="pt-2">
                <a href="{{ route('services.appointment.consultation') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-gradient-to-r from-[#6A8F3B] to-[#5a7a2f] hover:from-[#5a7a2f] text-white font-bold text-sm shadow-xl transition transform hover:scale-105">
                    <span>🚀</span>
                    <span>{{ __('Start Your Private Label Project') }}</span>
                </a>
            </div>
        </div>
    </div>

    {{-- 4 Step Turnkey Process --}}
    <div class="bg-white border border-gray-200 rounded-3xl p-6 sm:p-8 space-y-6">
        <h2 class="text-xl font-bold text-gray-900 text-center">
            {{ __('How Our Private Label Manufacturing Works') }}
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-gray-50 p-5 rounded-2xl border border-gray-200 space-y-2">
                <span class="w-8 h-8 rounded-full bg-[#183b1c] text-white flex items-center justify-center font-black text-sm">1</span>
                <b class="text-gray-900 block font-bold text-sm">{{ __('Select Cultivar & Quality') }}</b>
                <p class="text-gray-600 text-xs">{{ __('Choose between delicate Chemlali, robust high-polyphenol Chetoui, or certified Organic EVOO.') }}</p>
            </div>
            <div class="bg-gray-50 p-5 rounded-2xl border border-gray-200 space-y-2">
                <span class="w-8 h-8 rounded-full bg-[#183b1c] text-white flex items-center justify-center font-black text-sm">2</span>
                <b class="text-gray-900 block font-bold text-sm">{{ __('Bottle & Packaging Selection') }}</b>
                <p class="text-gray-600 text-xs">{{ __('Dark UV-resistant glass (Marasca/Dorica 250ml/500ml/750ml), premium ceramic, or 5L metallic tins.') }}</p>
            </div>
            <div class="bg-gray-50 p-5 rounded-2xl border border-gray-200 space-y-2">
                <span class="w-8 h-8 rounded-full bg-[#183b1c] text-white flex items-center justify-center font-black text-sm">3</span>
                <b class="text-gray-900 block font-bold text-sm">{{ __('Branding & Label Compliance') }}</b>
                <p class="text-gray-600 text-xs">{{ __('Custom label design, international barcode generation (619 / EAN-13), and FDA / EU regulatory compliance.') }}</p>
            </div>
            <div class="bg-gray-50 p-5 rounded-2xl border border-gray-200 space-y-2">
                <span class="w-8 h-8 rounded-full bg-[#183b1c] text-white flex items-center justify-center font-black text-sm">4</span>
                <b class="text-gray-900 block font-bold text-sm">{{ __('Export Logistics & Delivery') }}</b>
                <p class="text-gray-600 text-xs">{{ __('Palletized container loading, phytosanitary certificates, EUR.1, and customs clearance directly to your port.') }}</p>
            </div>
        </div>
    </div>

    {{-- Packaging Units Ready for Co-Packing --}}
    <div class="space-y-4">
        <h2 class="text-xl font-bold text-gray-900">
            {{ __('Accredited Bottling Partners in Tunisia') }}
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($packers as $packer)
                <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-xs">
                    <span class="text-xs text-gray-400 block mb-1">📍 {{ $packer->addresses->first()->governorate ?? 'Tunisie' }}</span>
                    <b class="text-gray-900 text-sm block">{{ $packer->name }}</b>
                    <span class="text-xs text-emerald-700 font-bold block mt-2">✓ {{ __('Co-Packing Accredited') }}</span>
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
