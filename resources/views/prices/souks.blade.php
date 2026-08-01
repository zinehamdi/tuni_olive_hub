@extends('layouts.app')

@section('title', __('أسعار الأسواق التونسية') . ' | ' . __('ZinToop Exchange'))

@section('content')
@php
    $locale = app()->getLocale();
    $items = isset($souks) ? $souks : collect();

    $soukNames = [
        'Sfax' => 'صفاقس', 'Tunis' => 'تونس', 'Sousse' => 'سوسة',
        'Monastir' => 'المنستير', 'Mahdia' => 'المهدية', 'Kairouan' => 'القيروان',
        'Medenine' => 'مدنين', 'Zarzis' => 'جرجيس', 'Djerba' => 'جربة', 'Gabes' => 'قابس',
    ];
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8"
     x-data="{ 
        activeTab: 'all', 
        searchQuery: '',
        matchesFilter(productType, text) {
            const matchesTab = this.activeTab === 'all' || 
                              (this.activeTab === 'oil' && productType === 'oil') ||
                              (this.activeTab === 'olive' && productType === 'olive');
            const q = this.searchQuery.toLowerCase().trim();
            const matchesSearch = !q || text.toLowerCase().includes(q);
            return matchesTab && matchesSearch;
        }
     }">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-gray-200 pb-5">
        <div>
            <h1 class="text-3xl font-black text-[#1B2A1B] flex items-center gap-3">
                <span>🇹🇳</span>
                <span>{{ __('أسعار الأسواق التونسية (Souks)') }}</span>
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                {{ __('تصفح أسعار الزيت والزيتون بالتفصيل في كافة الأسواق والولايات التونسية') }}
            </p>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold text-gray-500 bg-gray-100 px-3 py-1.5 rounded-full">
                {{ method_exists($items, 'total') ? $items->total() : $items->count() }} {{ __('سجل إجمالي') }}
            </span>
            <a href="{{ route('prices.index') }}" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-[#1B2A1B] text-xs font-bold transition flex items-center gap-1.5">
                <span>← {{ __('الرجوع للبورصة') }}</span>
            </a>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4 flex flex-col md:flex-row items-center justify-between gap-4">
        <!-- Filter Tabs -->
        <div class="flex items-center gap-1.5 overflow-x-auto w-full md:w-auto pb-2 md:pb-0 scrollbar-none">
            <button @click="activeTab = 'all'"
                    :class="activeTab === 'all' ? 'bg-[#1B2A1B] text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                    class="px-4 py-2.5 rounded-xl font-bold text-xs whitespace-nowrap transition-all flex items-center gap-1.5">
                <span>🌐</span>
                <span>{{ __('الكل') }}</span>
            </button>

            <button @click="activeTab = 'oil'"
                    :class="activeTab === 'oil' ? 'bg-[#142E18] text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                    class="px-4 py-2.5 rounded-xl font-bold text-xs whitespace-nowrap transition-all flex items-center gap-1.5">
                <span>🫒</span>
                <span>{{ __('زيت زيتون') }}</span>
            </button>

            <button @click="activeTab = 'olive'"
                    :class="activeTab === 'olive' ? 'bg-amber-600 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                    class="px-4 py-2.5 rounded-xl font-bold text-xs whitespace-nowrap transition-all flex items-center gap-1.5">
                <span>🌿</span>
                <span>{{ __('زيتون حب') }}</span>
            </button>
        </div>

        <!-- Search input -->
        <div class="relative w-full md:w-72">
            <input type="text" 
                   x-model="searchQuery"
                   placeholder="{{ __('ابحث عن سوق، ولاية، أو جودة...') }}"
                   class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-10 pr-4 py-2.5 text-xs focus:outline-none focus:border-[#6A8F3B] focus:ring-1 focus:ring-[#6A8F3B] transition">
            <div class="absolute left-3.5 top-3.5 text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
        </div>
    </div>

    <!-- Cards Grid -->
    @if($items->count())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($items as $row)
                @php
                    $date = optional(\Carbon\Carbon::parse($row->date ?? $row->created_at))->format('Y-m-d');
                    $isOil = ($row->product_type ?? '') === 'oil';
                    $trend = $row->trend ?? 'up';
                    $trendColor = $trend === 'up' ? 'text-emerald-600 bg-emerald-50 border-emerald-200' : ($trend === 'down' ? 'text-rose-600 bg-rose-50 border-rose-200' : 'text-gray-600 bg-gray-50 border-gray-200');
                    $trendIcon  = $trend === 'up' ? '📈' : ($trend === 'down' ? '📉' : '➡️');
                    $changePct  = isset($row->change_percentage) ? rtrim(rtrim(number_format((float)$row->change_percentage,2),'0'),'.').'%' : '+0.0%';
                    $priceMin = $row->price_min ? (float)$row->price_min : null;
                    $priceAvg = $row->price_avg ? (float)$row->price_avg : null;
                    $priceMax = $row->price_max ? (float)$row->price_max : null;
                    $unit = $row->unit ?? ($isOil ? 'liter' : 'kg');
                    $unitLabel = $unit === 'liter' ? 'لتر' : ($unit === 'kg' ? 'كغ' : $unit);
                    $currency = $row->currency ?? 'TND';
                    $quality = $row->quality ?? ($isOil ? 'EVOO' : 'ممتاز');
                    $variety = $row->variety ?? '';
                    $gov = $row->governorate ?? '';
                    $soukRaw = $row->souk_name ?? '';
                    $soukDisplay = $locale === 'ar' ? ($soukNames[$soukRaw] ?? $soukRaw) : $soukRaw;
                    $searchText = implode(' ', [$soukDisplay, $soukRaw, $gov, $variety, $quality, $isOil ? 'زيت زيتون oil' : 'زيتون حب olive']);

                    $rangePct = 50;
                    if ($priceMin && $priceMax && $priceAvg && $priceMax > $priceMin) {
                        $rangePct = max(5, min(95, round((($priceAvg - $priceMin) / ($priceMax - $priceMin)) * 100)));
                    }
                @endphp

                <div x-show="matchesFilter('{{ $isOil ? 'oil' : 'olive' }}', '{{ addslashes($searchText) }}')"
                     x-transition
                     class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-2xl transition duration-300 flex flex-col justify-between relative group">
                    
                    <!-- Card Top Header -->
                    <div class="bg-gradient-to-r from-[#142E18] to-[#1B2A1B] text-white p-4 relative overflow-hidden">
                        <div class="absolute -right-4 -bottom-4 w-24 h-24 opacity-15 pointer-events-none group-hover:scale-110 transition-transform">
                            <span class="text-6xl">🇹🇳</span>
                        </div>

                        <div class="flex items-center justify-between relative z-10 mb-1">
                            <div class="flex items-center gap-2">
                                <span class="text-xl">🇹🇳</span>
                                <h3 class="font-extrabold text-base text-white leading-tight">
                                    {{ $soukDisplay ?: 'سوق تونس' }}
                                </h3>
                            </div>
                            <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-[#C8A356] text-gray-950 shadow-sm">
                                {{ $gov ?: 'تونس' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between text-xs text-gray-300 relative z-10 mt-2">
                            <span class="font-medium flex items-center gap-1">
                                @if($isOil)
                                    <span class="text-amber-300 font-bold">🫒 {{ __('زيت زيتون') }}</span>
                                @else
                                    <span class="text-emerald-300 font-bold">🌿 {{ __('زيتون حب') }}</span>
                                @endif
                                @if($variety) <span class="opacity-75">({{ $variety }})</span> @endif
                            </span>
                            <span class="text-[10px] opacity-80 bg-white/10 px-2 py-0.5 rounded">{{ $date }}</span>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                        <!-- Big Price Display -->
                        <div class="bg-gradient-to-br from-gray-50 to-emerald-50/30 rounded-2xl p-4 border border-gray-100 text-center relative">
                            <div class="text-xs font-bold text-gray-500 mb-1 flex items-center justify-center gap-1">
                                <span>{{ __('المتوسط السعري') }}</span>
                                <span class="inline-block w-1.5 h-1.5 rounded-full bg-[#6A8F3B]"></span>
                            </div>
                            <div class="text-3xl font-black text-[#1B2A1B] flex items-baseline justify-center gap-1">
                                <span>{{ $priceAvg ? number_format($priceAvg, 2) : '—' }}</span>
                                <span class="text-sm font-extrabold text-[#6A8F3B]">{{ $currency }}/{{ $unitLabel }}</span>
                            </div>
                        </div>

                        <!-- Horizontal Range Visualizer -->
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between text-[11px] font-bold text-gray-600">
                                <span>{{ __('أدنى') }}: {{ $priceMin ? number_format($priceMin, 2) : '—' }}</span>
                                <span class="text-[#6A8F3B] font-extrabold">{{ __('المتوسط') }}</span>
                                <span>{{ __('أقصى') }}: {{ $priceMax ? number_format($priceMax, 2) : '—' }}</span>
                            </div>
                            <div class="w-full h-2.5 bg-gray-100 rounded-full overflow-hidden relative border border-gray-200">
                                <div class="h-full bg-gradient-to-r from-emerald-400 via-[#6A8F3B] to-[#C8A356] rounded-full transition-all duration-500"
                                     style="width: {{ $rangePct }}%;"></div>
                            </div>
                        </div>

                        <!-- Card Footer Info -->
                        <div class="flex items-center justify-between pt-3 border-t border-gray-100 text-xs">
                            <div class="flex items-center gap-1 px-2.5 py-1 rounded-full border text-xs font-bold {{ $trendColor }}">
                                <span>{{ $trendIcon }}</span>
                                <span>{{ $changePct }}</span>
                            </div>
                            <span class="px-2.5 py-1 rounded-full bg-amber-50 text-amber-800 border border-amber-200 text-[11px] font-bold">
                                {{ $quality }}
                            </span>
                        </div>
                    </div>

                </div>
            @endforeach
        </div>

        <!-- Paginate Links -->
        @if(method_exists($items, 'links'))
            <div class="mt-8">
                {{ $items->links() }}
            </div>
        @endif
    @else
        <div class="bg-white rounded-3xl p-12 text-center text-gray-500 border border-gray-100 shadow-sm">
            <span class="text-4xl block mb-2">🫙</span>
            <p class="font-bold">{{ __('لا توجد أسعار أسواق تونسية حالياً.') }}</p>
        </div>
    @endif

</div>
@endsection
