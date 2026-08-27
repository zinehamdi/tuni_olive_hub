@extends('layouts.app')

@php
    $locale = app()->getLocale();
    
    $pageTitle = match($locale) {
        'ar' => 'دليل معاصر زيت الزيتون في تونس | معاصر حديثة وتقليدية موثقة',
        'fr' => 'Moulins à Huile d\'Olive en Tunisie | Pressoirs & Capacités de Trituration',
        default => 'Olive Oil Mills in Tunisia Directory | Modern Extraction & Pressing Units',
    };

    $pageDesc = match($locale) {
        'ar' => 'دليل شامل لمعاصر زيت الزيتون في تونس موزعة على 24 ولاية. اكتشف طاقات العصر والتخزين، نسب الاستخراج، وتواصل مع أصحاب المعاصر مباشرة.',
        'fr' => 'Annuaire complet des moulins à huile d\'olive et pressoirs en Tunisie. Capacités de trituration, stockage et contacts directs par région.',
        default => 'Directory of verified olive oil mills and continuous pressing plants in Tunisia. Check extraction capacities, storage tanks, and connect directly with mill owners.',
    };
@endphp

@section('title', $pageTitle)
@section('description', $pageDesc)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <nav class="flex text-xs font-semibold text-gray-500 gap-2 items-center" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-[#6A8F3B] transition">{{ __('Home') }}</a>
        <span>/</span>
        <span class="text-gray-900 font-bold">{{ __('Olive Mills') }}</span>
    </nav>

    <div class="relative rounded-3xl overflow-hidden bg-gradient-to-r from-[#0C1A0F] via-[#142E18] to-[#0C1A0F] border border-[#C8A356]/30 shadow-2xl p-6 sm:p-10">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(200,163,86,0.2)_0%,transparent_60%)] pointer-events-none"></div>

        <div class="relative z-10 max-w-3xl space-y-4">
            <span class="inline-flex items-center px-3.5 py-1 rounded-full text-xs font-bold bg-[#C8A356]/20 text-[#F5E5C0] border border-[#C8A356]/40 backdrop-blur-md">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse mr-1.5 ml-1.5"></span>
                {{ __('Milling & Cold Extraction Infrastructure') }}
            </span>
            <h1 class="text-3xl sm:text-5xl font-black text-white tracking-tight leading-tight">
                @if($locale === 'ar')
                    دليل <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#6A8F3B] to-[#C8A356]">معاصر زيت الزيتون</span> في تونس
                @elseif($locale === 'fr')
                    Moulins à Huile d'Olive <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#6A8F3B] to-[#C8A356]">en Tunisie</span>
                @else
                    Olive Oil Mills <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#6A8F3B] to-[#C8A356]">in Tunisia</span>
                @endif
            </h1>
            <p class="text-xs sm:text-sm text-gray-300 leading-relaxed">
                {{ $pageDesc }}
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        @forelse($mills as $mill)
            <div class="bg-white border border-gray-200 hover:border-[#6A8F3B]/40 rounded-2xl p-5 shadow-xs hover:shadow-md transition flex flex-col justify-between">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-900">
                            🏢 {{ __('Mill') }}
                        </span>
                        <span class="text-[11px] font-bold text-gray-500">
                            📍 {{ $mill->addresses->first()->city ?? $mill->addresses->first()->governorate ?? 'Tunisie' }}
                        </span>
                    </div>
                    <h3 class="font-bold text-gray-900 text-sm">
                        {{ $mill->name }}
                    </h3>
                    <p class="text-xs text-gray-500 line-clamp-2">
                        {{ $mill->bio ?? __('Modern olive pressing plant equipped with continuous lines for extra virgin cold extraction.') }}
                    </p>
                </div>

                <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between">
                    <span class="text-[10px] text-emerald-700 font-bold">
                        ✓ {{ __('Certified Extraction') }}
                    </span>
                    <a href="{{ route('services.index', ['provider' => $mill->id]) }}" class="px-3 py-1.5 bg-[#183b1c] hover:bg-[#6A8F3B] text-white text-xs font-bold rounded-lg transition">
                        {{ __('Contact') }}
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-4 bg-gray-50 border border-gray-200 rounded-2xl p-12 text-center text-gray-500">
                {{ __('No mills found matching criteria.') }}
            </div>
        @endforelse
    </div>

    <div class="pt-4">
        {{ $mills->links() }}
    </div>

</div>
@endsection
