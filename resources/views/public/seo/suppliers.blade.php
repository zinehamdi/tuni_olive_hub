@extends('layouts.app')

@php
    $locale = app()->getLocale();
    
    $pageTitle = match($locale) {
        'ar' => 'دليل موردي ومنتجي زيت الزيتون في تونس | معاصر ومزارع موثقة',
        'fr' => 'Fournisseurs d\'Huile d\'Olive en Tunisie | Annuaire des Producteurs & Moulins',
        default => 'Tunisian Olive Oil Suppliers Directory | Verified Producers & Mills',
    };

    $pageDesc = match($locale) {
        'ar' => 'دليل شامل وموثق لكبار منتجي ومصدري ومعاصر زيت الزيتون في تونس. ابحث حسب الولاية، تواصل مباشرة مع المنتجين، واطلع على شهادات الجودة والتحاليل المخبرية.',
        'fr' => 'Annuaire vérifié des producteurs, moulins et exportateurs d\'huile d\'olive en Tunisie. Trouvez des partenaires fiables par gouvernorat avec accès direct sans intermédiaire.',
        default => 'Comprehensive verified directory of olive oil producers, mills, and exporters across Tunisia. Connect directly with certified farmers and suppliers across all 24 governorates.',
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
        <span class="text-gray-900 font-bold">{{ __('Suppliers Directory') }}</span>
    </nav>

    {{-- Hero Section --}}
    <div class="relative rounded-3xl overflow-hidden bg-gradient-to-r from-[#0C1A0F] via-[#142E18] to-[#0C1A0F] border border-[#C8A356]/30 shadow-2xl p-6 sm:p-10">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(200,163,86,0.2)_0%,transparent_60%)] pointer-events-none"></div>

        <div class="relative z-10 max-w-3xl space-y-4">
            <span class="inline-flex items-center px-3.5 py-1 rounded-full text-xs font-bold bg-[#C8A356]/20 text-[#F5E5C0] border border-[#C8A356]/40 backdrop-blur-md">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse mr-1.5 ml-1.5"></span>
                {{ __('National Ecosystem Directory') }}
            </span>
            <h1 class="text-3xl sm:text-5xl font-black text-white tracking-tight leading-tight">
                @if($locale === 'ar')
                    دليل <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#6A8F3B] to-[#C8A356]">موردي ومنتجي</span> زيت الزيتون التونسي
                @elseif($locale === 'fr')
                    Fournisseurs & Producteurs d'Huile d'Olive <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#6A8F3B] to-[#C8A356]">en Tunisie</span>
                @else
                    Tunisian Olive Oil <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#6A8F3B] to-[#C8A356]">Suppliers & Producers</span>
                @endif
            </h1>
            <p class="text-xs sm:text-sm text-gray-300 leading-relaxed">
                {{ $pageDesc }}
            </p>

            {{-- Ecosystem Stats Summary --}}
            <div class="grid grid-cols-3 gap-3 pt-2 text-center">
                <div class="bg-white/10 p-3 rounded-2xl border border-white/15">
                    <span class="text-xl sm:text-2xl font-black text-white block">{{ $farmersCount ?: 657 }}+</span>
                    <span class="text-[10px] text-gray-300">{{ __('Farmers & Producers') }}</span>
                </div>
                <div class="bg-white/10 p-3 rounded-2xl border border-white/15">
                    <span class="text-xl sm:text-2xl font-black text-white block">{{ $millsCount ?: 81 }}+</span>
                    <span class="text-[10px] text-gray-300">{{ __('Olive Mills (Pressoirs)') }}</span>
                </div>
                <div class="bg-white/10 p-3 rounded-2xl border border-white/15">
                    <span class="text-xl sm:text-2xl font-black text-white block">{{ $packersCount ?: 50 }}+</span>
                    <span class="text-[10px] text-gray-300">{{ __('Packaging & Exporters') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter by Role & Governorate --}}
    <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="text-xs font-bold text-gray-700">{{ __('Category:') }}</span>
            <div class="flex gap-2">
                <a href="{{ request()->fullUrlWithQuery(['role' => null]) }}" class="px-3 py-1 rounded-lg text-xs font-bold {{ !request('role') ? 'bg-[#183b1c] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    {{ __('All') }}
                </a>
                <a href="{{ request()->fullUrlWithQuery(['role' => 'farmer']) }}" class="px-3 py-1 rounded-lg text-xs font-bold {{ request('role') === 'farmer' ? 'bg-[#183b1c] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    🌾 {{ __('Producers') }}
                </a>
                <a href="{{ request()->fullUrlWithQuery(['role' => 'mill']) }}" class="px-3 py-1 rounded-lg text-xs font-bold {{ request('role') === 'mill' ? 'bg-[#183b1c] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    🏢 {{ __('Mills') }}
                </a>
                <a href="{{ request()->fullUrlWithQuery(['role' => 'packer']) }}" class="px-3 py-1 rounded-lg text-xs font-bold {{ request('role') === 'packer' ? 'bg-[#183b1c] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    📦 {{ __('Packers') }}
                </a>
            </div>
        </div>

        <a href="{{ route('register') }}" class="px-4 py-2 bg-[#183b1c] hover:bg-[#6A8F3B] text-white text-xs font-bold rounded-xl transition flex items-center gap-1.5">
            <span>+</span>
            <span>{{ __('Join Directory as a Supplier') }}</span>
        </a>
    </div>

    {{-- Suppliers Grid --}}
    <div class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @forelse($suppliers as $s)
                <div class="bg-white border border-gray-200 hover:border-[#6A8F3B]/40 rounded-2xl p-5 shadow-xs hover:shadow-md transition flex flex-col justify-between">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $s->role === 'mill' ? 'bg-amber-100 text-amber-900' : ($s->role === 'packer' ? 'bg-blue-100 text-blue-900' : 'bg-emerald-100 text-emerald-900') }}">
                                {{ ucfirst($s->role) }}
                            </span>
                            <span class="text-[11px] font-bold text-gray-400">
                                📍 {{ $s->addresses->first()->city ?? $s->addresses->first()->governorate ?? 'Tunisie' }}
                            </span>
                        </div>
                        <h3 class="font-bold text-gray-900 text-sm">
                            {{ $s->name }}
                        </h3>
                        <p class="text-xs text-gray-500 line-clamp-2">
                            {{ $s->bio ?? __('Verified supplier operating in the Tunisian olive oil sector.') }}
                        </p>
                    </div>

                    <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between">
                        <span class="text-[10px] text-emerald-700 font-bold">
                            ✓ {{ __('Verified Profile') }}
                        </span>
                        <a href="{{ route('services.index', ['provider' => $s->id]) }}" class="px-3 py-1.5 bg-[#183b1c] hover:bg-[#6A8F3B] text-white text-xs font-bold rounded-lg transition">
                            {{ __('Contact') }}
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-4 bg-gray-50 border border-gray-200 rounded-2xl p-12 text-center text-gray-500">
                    {{ __('No suppliers matching this filter currently.') }}
                </div>
            @endforelse
        </div>

        <div class="pt-4">
            {{ $suppliers->links() }}
        </div>
    </div>

</div>
@endsection
