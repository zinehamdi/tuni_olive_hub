@extends('layouts.app')
@section('title', __('Today\'s Prices') . ' | ' . __('ZinToop Exchange'))

@section('content')
@php
    $locale = app()->getLocale();
    $souks = $soukPrices ?? collect();
    $world = $worldPrices ?? collect();

    $countryFlags = [
        'Spain' => '🇪🇸', 'إسبانيا' => '🇪🇸', 'Espagne' => '🇪🇸',
        'Italy' => '🇮🇹', 'إيطاليا' => '🇮🇹', 'Italie' => '🇮🇹',
        'Greece' => '🇬🇷', 'اليونان' => '🇬🇷', 'Grèce' => '🇬🇷',
        'Turkey' => '🇹🇷', 'تركيا' => '🇹🇷', 'Turquie' => '🇹🇷',
        'Morocco' => '🇲🇦', 'المغرب' => '🇲🇦', 'Maroc' => '🇲🇦',
        'Tunisia' => '🇹🇳', 'تونس' => '🇹🇳', 'Tunisie' => '🇹🇳',
        'Portugal' => '🇵🇹', 'البرتغال' => '🇵🇹',
        'Egypt' => '🇪🇬', 'مصر' => '🇪🇬',
        'Syria' => '🇸🇾', 'سوريا' => '🇸🇾',
    ];

    $soukNames = [
        'Sfax' => 'صفاقس', 'Tunis' => 'تونس', 'Sousse' => 'سوسة',
        'Monastir' => 'المنستير', 'Mahdia' => 'المهدية', 'Kairouan' => 'القيروان',
        'Medenine' => 'مدنين', 'Zarzis' => 'جرجيس', 'Djerba' => 'جربة', 'Gabes' => 'قابس',
    ];

    $eurToTnd = 3.33;
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8"
     x-data="{ 
        activeTab: 'all', 
        searchQuery: '',
        matchesFilter(type, productType, text) {
            const matchesTab = this.activeTab === 'all' || 
                              (this.activeTab === 'souk' && type === 'souk') ||
                              (this.activeTab === 'world' && type === 'world') ||
                              (this.activeTab === 'oil' && productType === 'oil') ||
                              (this.activeTab === 'olive' && productType === 'olive');
            const q = this.searchQuery.toLowerCase().trim();
            const matchesSearch = !q || text.toLowerCase().includes(q);
            return matchesTab && matchesSearch;
        }
     }">

    {{-- ─── 1. World-Class Glassmorphism KPI Stock Banner ─── --}}
    <div class="relative rounded-3xl overflow-hidden bg-gradient-to-r from-[#0C1A0F] via-[#142E18] to-[#0C1A0F] border border-[#C8A356]/30 shadow-2xl p-6 sm:p-8">
        <!-- Background Ambient Lighting -->
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(200,163,86,0.2)_0%,transparent_60%)] pointer-events-none"></div>
        <div class="absolute -bottom-20 -left-20 w-80 h-80 rounded-full bg-[#6A8F3B]/20 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 mb-8 border-b border-white/10 pb-6">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-[#C8A356]/20 text-[#F5E5C0] border border-[#C8A356]/40 backdrop-blur-md">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse ml-1.5 mr-1.5"></span>
                        {{ __('بورصة زيت الزيتون اللحظية | Live Market') }}
                    </span>
                    <span class="text-xs text-gray-300 font-medium">
                        {{ now()->format('Y-m-d H:i') }}
                    </span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-black text-white tracking-tight">
                    {{ __('مؤشرات الأسعار المحلية والعالمية') }}
                </h1>
                <p class="text-sm text-gray-300 mt-1 max-w-2xl">
                    {{ __('تابع أسعار أسواق الزيت والزيتون في الأسواق التونسيّة والبورصة العالمية المحدثة مباشرة') }}
                </p>
            </div>
            <a href="{{ route('prices.souks') }}" class="px-5 py-3 rounded-2xl bg-gradient-to-r from-[#6A8F3B] to-[#5a7a2f] hover:from-[#5a7a2f] text-white font-bold text-sm shadow-xl transition transform hover:scale-105 flex items-center gap-2">
                <span>{{ __('سجل التحديثات التاريخي') }}</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7-7 7M5 5l7 7-7 7"/></svg>
            </a>
        </div>

        <!-- KPI Grid Cards -->
        <div class="relative z-10 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            
            <!-- KPI 1: Tunisian Olive Oil Average -->
            <div class="bg-white/10 backdrop-blur-md border border-white/15 rounded-2xl p-5 hover:bg-white/15 transition group">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-gray-300 flex items-center gap-1.5">
                        <span>🇹🇳</span>
                        <span>{{ __('متوسط الزيت التونسي (7 أيام)') }}</span>
                    </span>
                    <span class="text-lg">🫒</span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-black text-white">
                        {{ isset($tunisianAvg) ? number_format((float)$tunisianAvg, 2) : '—' }}
                    </span>
                    <span class="text-sm font-bold text-[#F5E5C0]">TND/لتر</span>
                </div>
                <div class="mt-2 flex items-center justify-between text-xs text-gray-400 border-t border-white/10 pt-2">
                    <span>{{ __('محدث يومياً من الأسواق') }}</span>
                    <span class="text-emerald-400 font-bold">● {{ __('نشط') }}</span>
                </div>
            </div>

            <!-- KPI 2: Tunisian Olives Average -->
            <div class="bg-white/10 backdrop-blur-md border border-white/15 rounded-2xl p-5 hover:bg-white/15 transition group">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-gray-300 flex items-center gap-1.5">
                        <span>🇹🇳</span>
                        <span>{{ __('متوسط الزيتون الحب (7 أيام)') }}</span>
                    </span>
                    <span class="text-lg">🌿</span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-black text-white">
                        {{ isset($tunisianOliveAvg) ? number_format((float)$tunisianOliveAvg, 2) : '—' }}
                    </span>
                    <span class="text-sm font-bold text-[#F5E5C0]">TND/كغ</span>
                </div>
                <div class="mt-2 flex items-center justify-between text-xs text-gray-400 border-t border-white/10 pt-2">
                    <span>{{ __('أسواق الإنتاج الرئيسيّة') }}</span>
                    <span class="text-emerald-400 font-bold">● {{ __('مباشر') }}</span>
                </div>
            </div>

            <!-- KPI 3: World Market Average -->
            <div class="bg-white/10 backdrop-blur-md border border-white/15 rounded-2xl p-5 hover:bg-white/15 transition group sm:col-span-2 lg:col-span-1">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-gray-300 flex items-center gap-1.5">
                        <span>🌍</span>
                        <span>{{ __('متوسط البورصة العالمية (EVOO)') }}</span>
                    </span>
                    <span class="text-xs font-bold bg-[#C8A356]/30 text-[#F5E5C0] px-2 py-0.5 rounded-md border border-[#C8A356]/40">
                        EUR & TND
                    </span>
                </div>
                <div class="flex items-baseline justify-between">
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-3xl font-black text-white">
                            {{ isset($worldAvg) ? number_format((float)$worldAvg, 2) : '—' }}
                        </span>
                        <span class="text-sm font-bold text-[#F5E5C0]">EUR/kg</span>
                    </div>
                    @if(isset($worldAvg))
                        <div class="text-right">
                            <span class="text-sm font-black text-emerald-400 block">
                                ≈ {{ number_format((float)$worldAvg * $eurToTnd, 2) }} TND
                            </span>
                        </div>
                    @endif
                </div>
                <div class="mt-2 flex items-center justify-between text-xs text-gray-400 border-t border-white/10 pt-2">
                    <span>{{ __('إسبانيا / إيطاليا / اليونان') }}</span>
                    <span class="text-amber-400 font-bold">1 EUR ≈ {{ $eurToTnd }} TND</span>
                </div>
            </div>

        </div>
    </div>

    {{-- ─── 2. Interactive Filter Tabs & Search Bar ─── --}}
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4 flex flex-col md:flex-row items-center justify-between gap-4">
        
        <!-- Filter Tabs Buttons -->
        <div class="flex items-center gap-1.5 overflow-x-auto w-full md:w-auto pb-2 md:pb-0 scrollbar-none">
            <button @click="activeTab = 'all'"
                    :class="activeTab === 'all' ? 'bg-[#1B2A1B] text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                    class="px-4 py-2.5 rounded-xl font-bold text-xs whitespace-nowrap transition-all flex items-center gap-1.5">
                <span>🌐</span>
                <span>{{ __('الكل') }}</span>
            </button>

            <button @click="activeTab = 'souk'"
                    :class="activeTab === 'souk' ? 'bg-[#6A8F3B] text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                    class="px-4 py-2.5 rounded-xl font-bold text-xs whitespace-nowrap transition-all flex items-center gap-1.5">
                <span>🇹🇳</span>
                <span>{{ __('أسواق تونس') }}</span>
            </button>

            <button @click="activeTab = 'world'"
                    :class="activeTab === 'world' ? 'bg-[#C8A356] text-gray-950 shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                    class="px-4 py-2.5 rounded-xl font-bold text-xs whitespace-nowrap transition-all flex items-center gap-1.5">
                <span>🌍</span>
                <span>{{ __('البورصة العالمية') }}</span>
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

        <!-- Search Bar -->
        <div class="relative w-full md:w-72">
            <input type="text" 
                   x-model="searchQuery" 
                   placeholder="{{ __('ابحث بالسوق، الصنف أو الدولة...') }}"
                   class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-xs font-semibold text-gray-900 focus:bg-white focus:ring-2 focus:ring-[#6A8F3B] focus:border-[#6A8F3B] transition pl-9">
            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
    </div>

    {{-- ─── 3. Tunisian Souk Prices Section ─── --}}
    <div x-show="activeTab === 'all' || activeTab === 'souk' || activeTab === 'oil' || activeTab === 'olive'" class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-xl sm:text-2xl font-black text-[#1B2A1B] flex items-center gap-2">
                <span>🇹🇳</span>
                <span>{{ __('أسواق الزيت والزيتون في تونس') }}</span>
            </h2>
            <span class="text-xs font-bold text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                {{ $souks->count() }} {{ __('سوق أسعار') }}
            </span>
        </div>

        @if($souks->count())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($souks as $row)
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

                        // Calculate range percentage for visual progress bar
                        $rangePct = 50;
                        if ($priceMin && $priceMax && $priceAvg && $priceMax > $priceMin) {
                            $rangePct = max(5, min(95, round((($priceAvg - $priceMin) / ($priceMax - $priceMin)) * 100)));
                        }
                    @endphp

                    <div x-show="matchesFilter('souk', '{{ $isOil ? 'oil' : 'olive' }}', '{{ addslashes($searchText) }}')"
                         x-transition
                         class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-2xl transition duration-300 flex flex-col justify-between relative group">
                        
                        <!-- Top Header Pill -->
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

                            <!-- Min - Avg - Max Horizontal Range Visualizer -->
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between text-[11px] font-bold text-gray-600">
                                    <span>{{ __('أدنى') }}: {{ $priceMin ? number_format($priceMin, 2) : '—' }}</span>
                                    <span class="text-[#6A8F3B] font-extrabold">{{ __('المتوسط') }}</span>
                                    <span>{{ __('أقصى') }}: {{ $priceMax ? number_format($priceMax, 2) : '—' }}</span>
                                </div>
                                
                                <!-- Range Progress Bar -->
                                <div class="w-full h-2.5 bg-gray-100 rounded-full overflow-hidden relative border border-gray-200">
                                    <div class="h-full bg-gradient-to-r from-emerald-400 via-[#6A8F3B] to-[#C8A356] rounded-full transition-all duration-500"
                                         style="width: {{ $rangePct }}%;"></div>
                                </div>
                            </div>

                            <!-- Trend & Quality Footer -->
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
        @else
            <div class="bg-white rounded-2xl p-8 text-center text-gray-500 border border-gray-200">
                {{ __('لا تتوفر أسعار للأسواق التونسية حالياً.') }}
            </div>
        @endif
    </div>

    {{-- ─── 4. World Prices Section (Stock Exchange Cards) ─── --}}
    <div x-show="activeTab === 'all' || activeTab === 'world' || activeTab === 'oil'" class="space-y-4 pt-4">
        <div class="flex items-center justify-between">
            <h2 class="text-xl sm:text-2xl font-black text-[#1B2A1B] flex items-center gap-2">
                <span>🌍</span>
                <span>{{ __('أسعار البورصة العالمية (World Markets)') }}</span>
            </h2>
            <span class="text-xs font-bold text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                {{ $world->count() }} {{ __('أسواق دولية') }}
            </span>
        </div>

        @if($world->count())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($world as $row)
                    @php
                        $date = optional(\Carbon\Carbon::parse($row->date ?? $row->created_at))->format('Y-m-d');
                        $priceEur = isset($row->price) ? (float)$row->price : 0;
                        $priceTnd = $priceEur ? round($priceEur * $eurToTnd, 2) : 0;
                        $variety = $row->variety ?? 'EVOO';
                        $quality = $row->quality ?? 'Extra Virgin';
                        $countryRaw = $row->country ?? 'Spain';
                        $flag = $countryFlags[$countryRaw] ?? '🌍';
                        $searchText = implode(' ', [$countryRaw, $flag, $variety, $quality, 'world']);
                    @endphp

                    <div x-show="matchesFilter('world', 'oil', '{{ addslashes($searchText) }}')"
                         x-transition
                         class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-2xl transition duration-300 flex flex-col justify-between relative group">
                        
                        <!-- Header Banner with Flag -->
                        <div class="bg-gradient-to-r from-[#0C1A0F] to-[#142E18] text-white p-4 relative overflow-hidden">
                            <div class="flex items-center justify-between relative z-10">
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl drop-shadow-md">{{ $flag }}</span>
                                    <h3 class="font-extrabold text-base text-white">
                                        {{ $countryRaw }}
                                    </h3>
                                </div>
                                <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-[#C8A356] text-gray-950 shadow-sm">
                                    {{ $quality }}
                                </span>
                            </div>
                        </div>

                        <!-- Card Content -->
                        <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                            
                            <!-- EUR Price Big Display -->
                            <div class="bg-gradient-to-br from-amber-50/50 via-white to-amber-50/20 rounded-2xl p-4 border border-amber-100 text-center">
                                <div class="text-xs font-bold text-gray-500 mb-1 flex items-center justify-center gap-1">
                                    <span>{{ __('السعر العالمي (EUR)') }}</span>
                                    <span class="text-xs">💶</span>
                                </div>
                                <div class="text-3xl font-black text-gray-950 flex items-baseline justify-center gap-1">
                                    <span>{{ number_format($priceEur, 2) }}</span>
                                    <span class="text-sm font-extrabold text-[#C8A356]">EUR/kg</span>
                                </div>
                            </div>

                            <!-- Equivalent TND Pill -->
                            <div class="bg-emerald-50 rounded-xl p-3 border border-emerald-200 flex items-center justify-between">
                                <span class="text-xs font-bold text-emerald-800 flex items-center gap-1">
                                    <span>🇹🇳</span>
                                    <span>{{ __('المقابل بالدينار') }}:</span>
                                </span>
                                <span class="text-base font-black text-emerald-950">
                                    {{ number_format($priceTnd, 2) }} TND/kg
                                </span>
                            </div>

                            <!-- Footer Info -->
                            <div class="flex items-center justify-between text-xs text-gray-400 pt-2 border-t border-gray-100">
                                <span class="font-semibold text-gray-600">{{ $variety }}</span>
                                <span>{{ $date }}</span>
                            </div>

                        </div>

                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-2xl p-8 text-center text-gray-500 border border-gray-200">
                {{ __('لا تتوفر أسعار عالمية حالياً.') }}
            </div>
        @endif
    </div>

</div>
@endsection
