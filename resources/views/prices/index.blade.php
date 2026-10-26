@extends('layouts.app')

@php
    $locale = app()->getLocale();
    $pageTitle = match($locale) {
        'ar' => 'بورصة أسعار زيت الزيتون اليوم في تونس والعالم | أسعار مباشرة',
        'fr' => 'Prix de l\'Huile d\'Olive Aujourd\'hui en Tunisie & Monde | Cours Directs',
        default => 'Tunisian & International Olive Oil Prices Today | Live Market Rates',
    };
    $pageDesc = match($locale) {
        'ar' => 'تابع أسعار زيت الزيتون اليوم في تونس والأسواق العالمية (إسبانيا، إيطاليا، اليونان). تحديث يومي لأسعار الزيت البكر الممتاز والشملالي والشتوي.',
        'fr' => 'Consultez les prix de l\'huile d\'olive aujourd\'hui en Tunisie et dans le monde (Espagne, Italie, Grèce). Cours de l\'Extra Vierge, Chemlali et Chétoui.',
        default => 'Live daily olive oil price benchmark in Tunisia and worldwide (Spain, Italy, Greece). Real-time rates for Extra Virgin, Chemlali, and Chetoui oils.',
    };
@endphp

@section('title', $pageTitle)
@section('description', $pageDesc)

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
                    <span class="text-sm font-bold text-[#F5E5C0]">TND/{{ $locale === 'ar' ? 'كغ' : 'kg' }}</span>
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
                    <span class="text-sm font-bold text-[#F5E5C0]">TND/{{ $locale === 'ar' ? 'كغ' : 'kg' }}</span>
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

    {{-- ─── 1.5 SMART FARMER CALCULATOR: BAZ, GALBA & CUEILLETTE ─── --}}
    <div x-data="{
            isExpanded: true,
            volUnit: 'galba',
            galbaCount: 100,
            kgCount: 1400,
            prixBaz: {{ isset($tunisianAvg) ? number_format((float)$tunisianAvg, 2, '.', '') : '16.50' }},
            gtou3Mode: 'galba',
            gtou3Galba: 2.8,
            gtou3Pct: 20,
            recolteMode: 'galba',
            recolteGalba: 5.6,
            recolteMillimes: 400,
            tgargibMode: 'kg',
            tgargibMillimes: 200,
            tgargibGalba: 2.8,

            onGalbaChange() {
                const g = parseFloat(this.galbaCount) || 0;
                this.kgCount = Math.round(g * 14);
            },
            onKgChange() {
                const k = parseFloat(this.kgCount) || 0;
                this.galbaCount = +(k / 14).toFixed(1);
            },
            onGtou3GalbaChange() {
                const g = parseFloat(this.gtou3Galba) || 0;
                this.gtou3Pct = +((g / 14) * 100).toFixed(1);
            },
            onGtou3PctChange() {
                const p = parseFloat(this.gtou3Pct) || 0;
                this.gtou3Galba = +((p / 100) * 14).toFixed(2);
            },
            onRecolteGalbaChange() {
                const g = parseFloat(this.recolteGalba) || 0;
                this.recolteMillimes = Math.round((g / 14) * 1000);
            },
            onRecolteMillimesChange() {
                const m = parseFloat(this.recolteMillimes) || 0;
                this.recolteGalba = +((m / 1000) * 14).toFixed(3);
            },
            onTgargibMillimesChange() {
                const m = parseFloat(this.tgargibMillimes) || 0;
                this.tgargibGalba = +((m / 1000) * 14).toFixed(3);
            },
            onTgargibGalbaChange() {
                const g = parseFloat(this.tgargibGalba) || 0;
                this.tgargibMillimes = Math.round((g / 14) * 1000);
            },
            adjustGalba(delta) {
                this.galbaCount = Math.max(1, (parseFloat(this.galbaCount) || 0) + delta);
                this.onGalbaChange();
            },
            adjustKg(delta) {
                this.kgCount = Math.max(14, (parseFloat(this.kgCount) || 0) + delta);
                this.onKgChange();
            },

            get totalKg() {
                return this.volUnit === 'galba' ? ((parseFloat(this.galbaCount) || 0) * 14) : (parseFloat(this.kgCount) || 0);
            },
            get totalGalba() {
                return this.volUnit === 'galba' ? (parseFloat(this.galbaCount) || 0) : +((parseFloat(this.kgCount) || 0) / 14).toFixed(1);
            },
            get totalOilKg() {
                const rate = (this.gtou3Mode === 'galba') 
                    ? ((parseFloat(this.gtou3Galba) || 0) / 14) 
                    : ((parseFloat(this.gtou3Pct) || 0) / 100);
                return +(this.totalKg * rate).toFixed(1);
            },
            get totalOilLiters() {
                // Density of olive oil is ~0.916 kg/L (1 L ≈ 0.916 kg => Liters = kg / 0.916)
                return +(this.totalOilKg / 0.916).toFixed(1);
            },
            get grossRevenue() {
                const b = parseFloat(this.prixBaz) || 0;
                return +(this.totalOilKg * b).toFixed(2);
            },
            get totalRecolteCost() {
                const costPerKg = (this.recolteMode === 'galba') 
                    ? ((parseFloat(this.recolteGalba) || 0) / 14) 
                    : ((parseFloat(this.recolteMillimes) || 0) / 1000);
                return +(this.totalKg * costPerKg).toFixed(2);
            },
            get totalTgargibCost() {
                const costPerKg = (this.tgargibMode === 'galba') 
                    ? ((parseFloat(this.tgargibGalba) || 0) / 14) 
                    : ((parseFloat(this.tgargibMillimes) || 0) / 1000);
                return +(this.totalKg * costPerKg).toFixed(2);
            },
            get totalExpenses() {
                return +(this.totalRecolteCost + this.totalTgargibCost).toFixed(2);
            },
            get netProfit() {
                return +(this.grossRevenue - this.totalExpenses).toFixed(2);
            },
            get netPricePerKg() {
                return this.totalKg > 0 ? +(this.netProfit / this.totalKg).toFixed(3) : 0;
            },
            get netPricePerGalba() {
                return +(this.netPricePerKg * 14).toFixed(3);
            },
            get grossPricePerKg() {
                return this.totalKg > 0 ? +(this.grossRevenue / this.totalKg).toFixed(3) : 0;
            }
         }"
         class="bg-gradient-to-br from-white via-[#fbfdfa] to-[#f4f7f1] rounded-2xl sm:rounded-3xl shadow-xl border-2 border-[#6A8F3B]/30 overflow-hidden transition-all duration-300">

        {{-- Header bar with Toggle --}}
        <div class="bg-gradient-to-r from-[#142E18] via-[#1B2A1B] to-[#142E18] text-white p-4 sm:p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4 border-b border-[#C8A356]/40 cursor-pointer select-none"
             @click="isExpanded = !isExpanded">
            <div class="flex items-start sm:items-center gap-3 sm:gap-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-gradient-to-br from-[#C8A356] to-[#b08b3a] flex items-center justify-center text-xl sm:text-2xl shadow-lg border border-white/20 flex-shrink-0 mt-0.5 sm:mt-0">
                    🧮
                </div>
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                        <span class="px-2 sm:px-2.5 py-0.5 rounded-full text-[10px] sm:text-[11px] font-black bg-[#C8A356] text-gray-950 uppercase tracking-wide">
                            {{ __('محاكي الفلاح الذكي') }}
                        </span>
                        <span class="text-[11px] sm:text-xs text-[#F5E5C0] font-semibold">
                            🇹🇳 {{ __('1 قَلبة = 14 كغ زيتون') }}
                        </span>
                    </div>
                    <h2 class="text-base sm:text-2xl font-black text-white mt-1 leading-snug">
                        {{ __('حساب قيمة الزيتون بالقلبة والكيلو مقارنة بسعر الباز') }}
                    </h2>
                    <p class="text-[11px] sm:text-xs text-gray-300 mt-0.5 leading-relaxed">
                        {{ __('احسب مردودية صابتك: كمية الزيت، كلفة التقرقيب (المعصرة)، كلفة الجني، وسعر الكيلو الصافي في جيبك') }}
                    </p>
                </div>
            </div>

            <div class="flex items-center justify-end sm:justify-center self-end sm:self-auto flex-shrink-0">
                <button type="button" 
                        class="px-3.5 sm:px-4 py-1.5 sm:py-2 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white font-bold text-xs sm:text-sm flex items-center gap-2 transition flex-shrink-0">
                    <span x-text="isExpanded ? '{{ __('إخفاء') }}' : '{{ __('فتح المحاكي') }}'"></span>
                    <svg class="w-4 h-4 transition-transform duration-300" :class="isExpanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Collapsible Body --}}
        <div x-show="isExpanded" x-collapse class="p-3.5 sm:p-6 lg:p-8 space-y-6 sm:space-y-8">

            {{-- 1. INPUTS GRID: BIG, HIGH-CONTRAST, SUPER EASY TO EDIT & MOBILE RESPONSIVE --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3.5 sm:gap-6">

                {{-- FIELD 1: QUANTITE OLIVES (GALBA / KG) --}}
                <div class="bg-white rounded-2xl p-4 sm:p-5 border-2 border-gray-200 shadow-sm hover:border-[#6A8F3B] transition space-y-3">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <label class="text-xs font-black text-[#142E18] uppercase tracking-wide flex items-center gap-1.5">
                            <span class="text-base">📦</span>
                            <span>{{ __('1. كمية المحصول') }}</span>
                        </label>
                        {{-- Unit Switcher Pills --}}
                        <div class="flex items-center bg-gray-100 p-1 rounded-xl text-xs font-bold gap-1">
                            <button type="button" 
                                    @click="volUnit = 'galba'" 
                                    :class="volUnit === 'galba' ? 'bg-[#6A8F3B] text-white shadow-sm' : 'text-gray-600 hover:text-gray-900'"
                                    class="px-2.5 sm:px-3 py-1 rounded-lg transition text-[11px] sm:text-xs">
                                {{ __('بالقلبة (14كغ)') }}
                            </button>
                            <button type="button" 
                                    @click="volUnit = 'kg'" 
                                    :class="volUnit === 'kg' ? 'bg-[#6A8F3B] text-white shadow-sm' : 'text-gray-600 hover:text-gray-900'"
                                    class="px-2.5 sm:px-3 py-1 rounded-lg transition text-[11px] sm:text-xs">
                                {{ __('بالكيلو (Kg)') }}
                            </button>
                        </div>
                    </div>

                    {{-- Big Input with Stepper Buttons --}}
                    <div class="flex items-center gap-2">
                        <button type="button" 
                                @click="volUnit === 'galba' ? adjustGalba(-10) : adjustKg(-100)"
                                class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-800 font-black text-lg sm:text-xl flex items-center justify-center transition flex-shrink-0 shadow-inner">
                            -
                        </button>

                        <div class="relative flex-1">
                            <template x-if="volUnit === 'galba'">
                                <input type="number" 
                                       x-model.number="galbaCount" 
                                       @input="onGalbaChange()"
                                       step="5" min="1"
                                       class="w-full text-center text-xl sm:text-2xl font-black text-[#142E18] bg-gray-50 border-2 border-gray-300 rounded-xl py-2.5 sm:py-3 px-2 focus:bg-white focus:border-[#6A8F3B] focus:ring-4 focus:ring-[#6A8F3B]/20 transition shadow-inner">
                            </template>
                            <template x-if="volUnit === 'kg'">
                                <input type="number" 
                                       x-model.number="kgCount" 
                                       @input="onKgChange()"
                                       step="50" min="14"
                                       class="w-full text-center text-xl sm:text-2xl font-black text-[#142E18] bg-gray-50 border-2 border-gray-300 rounded-xl py-2.5 sm:py-3 px-2 focus:bg-white focus:border-[#6A8F3B] focus:ring-4 focus:ring-[#6A8F3B]/20 transition shadow-inner">
                            </template>
                        </div>

                        <button type="button" 
                                @click="volUnit === 'galba' ? adjustGalba(10) : adjustKg(100)"
                                class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-800 font-black text-lg sm:text-xl flex items-center justify-center transition flex-shrink-0 shadow-inner">
                            +
                        </button>
                    </div>

                    {{-- Conversion Equivalent Badge --}}
                    <div class="flex items-center justify-between text-xs font-bold text-[#166534] bg-emerald-50 border border-emerald-200 rounded-xl px-3 py-2">
                        <span>{{ __('المعادلة:') }}</span>
                        <span>
                            <strong class="text-sm" x-text="galbaCount"></strong> {{ __('قَلبة') }}
                            =
                            <strong class="text-sm" x-text="kgCount.toLocaleString()"></strong> {{ __('كغ زيتون') }}
                        </span>
                    </div>
                </div>

                {{-- FIELD 2: PRIX DU BAZ (TND / KG) --}}
                <div class="bg-white rounded-2xl p-4 sm:p-5 border-2 border-gray-200 shadow-sm hover:border-[#6A8F3B] transition space-y-3">
                    <div class="flex flex-wrap items-center justify-between gap-1.5">
                        <label class="text-xs font-black text-[#142E18] uppercase tracking-wide flex items-center gap-1.5">
                            <span class="text-base">🏷️</span>
                            <span>{{ __('2. سعر الباز (زيت الزيتون)') }}</span>
                        </label>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-300">
                            {{ __('سعر مرجع السوق') }}
                        </span>
                    </div>

                    {{-- Big Input --}}
                    <div class="relative">
                        <input type="number" 
                               x-model.number="prixBaz" 
                               step="0.25" min="5" max="40"
                               class="w-full text-center text-xl sm:text-2xl font-black text-amber-700 bg-gray-50 border-2 border-gray-300 rounded-xl py-2.5 sm:py-3 px-2 focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/20 transition shadow-inner">
                        <span class="absolute left-3 top-3 text-xs font-black text-gray-400">TND / {{ $locale === 'ar' ? 'كغ' : 'kg' }}</span>
                    </div>

                    <div class="flex items-center justify-between text-xs text-gray-600 bg-gray-50 rounded-xl px-3 py-2 border border-gray-200">
                        <span>{{ __('متوسط تونس اليوم:') }}</span>
                        <button type="button" 
                                @click="prixBaz = {{ isset($tunisianAvg) ? number_format((float)$tunisianAvg, 2, '.', '') : '16.50' }}"
                                class="text-xs font-bold text-[#6A8F3B] hover:underline flex items-center gap-1">
                            <span>{{ isset($tunisianAvg) ? number_format((float)$tunisianAvg, 2) : '16.50' }} {{ $locale === 'ar' ? 'د.ت / كغ' : 'TND / kg' }}</span>
                            <span class="text-[10px] bg-[#6A8F3B]/10 px-1.5 py-0.5 rounded font-black">{{ __('تطبيق') }}</span>
                        </button>
                    </div>
                </div>

                {{-- FIELD 3: GTOU3 / RENDEMENT (KG/GALBA or %) --}}
                <div class="bg-white rounded-2xl p-4 sm:p-5 border-2 border-gray-200 shadow-sm hover:border-[#6A8F3B] transition space-y-3">
                    <div class="flex flex-wrap items-center justify-between gap-1.5">
                        <label class="text-xs font-black text-[#142E18] uppercase tracking-wide flex items-center gap-1.5">
                            <span class="text-base">💧</span>
                            <span>{{ __('3. القطوع (المردودية)') }}</span>
                        </label>
                        {{-- Mode Switcher --}}
                        <div class="flex items-center bg-gray-100 p-1 rounded-xl text-xs font-bold gap-1">
                            <button type="button" 
                                    @click="gtou3Mode = 'galba'" 
                                    :class="gtou3Mode === 'galba' ? 'bg-[#C8A356] text-gray-950 shadow-sm' : 'text-gray-600 hover:text-gray-900'"
                                    class="px-2.5 py-1 rounded-lg transition text-[11px] sm:text-xs">
                                {{ __('كغ/قلبة') }}
                            </button>
                            <button type="button" 
                                    @click="gtou3Mode = 'pct'" 
                                    :class="gtou3Mode === 'pct' ? 'bg-[#C8A356] text-gray-950 shadow-sm' : 'text-gray-600 hover:text-gray-900'"
                                    class="px-2.5 py-1 rounded-lg transition text-[11px] sm:text-xs">
                                {{ __('عيارة %') }}
                            </button>
                        </div>
                    </div>

                    {{-- Big Input --}}
                    <div class="relative">
                        <template x-if="gtou3Mode === 'galba'">
                            <input type="number" 
                                   x-model.number="gtou3Galba" 
                                   @input="onGtou3GalbaChange()"
                                   step="0.1" min="0.5" max="7"
                                   class="w-full text-center text-xl sm:text-2xl font-black text-[#142E18] bg-gray-50 border-2 border-gray-300 rounded-xl py-2.5 sm:py-3 px-2 focus:bg-white focus:border-[#C8A356] focus:ring-4 focus:ring-[#C8A356]/20 transition shadow-inner">
                        </template>
                        <template x-if="gtou3Mode === 'pct'">
                            <input type="number" 
                                   x-model.number="gtou3Pct" 
                                   @input="onGtou3PctChange()"
                                   step="0.5" min="5" max="40"
                                   class="w-full text-center text-xl sm:text-2xl font-black text-[#142E18] bg-gray-50 border-2 border-gray-300 rounded-xl py-2.5 sm:py-3 px-2 focus:bg-white focus:border-[#C8A356] focus:ring-4 focus:ring-[#C8A356]/20 transition shadow-inner">
                        </template>
                        <span class="absolute left-3 top-3 text-xs font-black text-gray-400" x-text="gtou3Mode === 'galba' ? '{{ $locale === 'ar' ? 'كغ / قلبة' : 'kg / Galba' }}' : '%'"></span>
                    </div>

                    {{-- Equivalency helper --}}
                    <div class="flex items-center justify-between text-xs font-bold text-amber-900 bg-amber-50 border border-amber-200 rounded-xl px-3 py-2">
                        <span>{{ __('العيارة المقابلة:') }}</span>
                        <span>
                            <strong class="text-sm" x-text="gtou3Galba"></strong> {{ __('كغ/قلبة') }}
                            ≈
                            <strong class="text-sm" x-text="gtou3Pct"></strong>% {{ __('زيت') }}
                        </span>
                    </div>
                </div>

                {{-- FIELD 4: COUT RECOLTE / CUEILLETTE (GALBA or MILLIMES/KG) - DEFAULT 5.6 TND / GALBA --}}
                <div class="bg-white rounded-2xl p-4 sm:p-5 border-2 border-gray-200 shadow-sm hover:border-[#6A8F3B] transition space-y-3">
                    <div class="flex flex-wrap items-center justify-between gap-1.5">
                        <label class="text-xs font-black text-[#142E18] uppercase tracking-wide flex items-center gap-1.5">
                            <span class="text-base">🚜</span>
                            <span>{{ __('4. كلفة الجني (يد عاملة/ماكينات)') }}</span>
                        </label>
                        {{-- Mode Switcher --}}
                        <div class="flex items-center bg-gray-100 p-1 rounded-xl text-xs font-bold gap-1">
                            <button type="button" 
                                    @click="recolteMode = 'galba'" 
                                    :class="recolteMode === 'galba' ? 'bg-[#142E18] text-white shadow-sm' : 'text-gray-600 hover:text-gray-900'"
                                    class="px-2.5 py-1 rounded-lg transition text-[11px] sm:text-xs">
                                {{ __('بالقلبة (د.ت)') }}
                            </button>
                            <button type="button" 
                                    @click="recolteMode = 'kg'" 
                                    :class="recolteMode === 'kg' ? 'bg-[#142E18] text-white shadow-sm' : 'text-gray-600 hover:text-gray-900'"
                                    class="px-2.5 py-1 rounded-lg transition text-[11px] sm:text-xs">
                                {{ __('بالكيلو (مليم)') }}
                            </button>
                        </div>
                    </div>

                    {{-- Big Input --}}
                    <div class="relative">
                        <template x-if="recolteMode === 'galba'">
                            <input type="number" 
                                   x-model.number="recolteGalba" 
                                   @input="onRecolteGalbaChange()"
                                   step="0.2" min="0.5" max="15"
                                   class="w-full text-center text-xl sm:text-2xl font-black text-[#142E18] bg-gray-50 border-2 border-gray-300 rounded-xl py-2.5 sm:py-3 px-2 focus:bg-white focus:border-[#142E18] focus:ring-4 focus:ring-[#142E18]/20 transition shadow-inner">
                        </template>
                        <template x-if="recolteMode === 'kg'">
                            <input type="number" 
                                   x-model.number="recolteMillimes" 
                                   @input="onRecolteMillimesChange()"
                                   step="10" min="50" max="1000"
                                   class="w-full text-center text-xl sm:text-2xl font-black text-[#142E18] bg-gray-50 border-2 border-gray-300 rounded-xl py-2.5 sm:py-3 px-2 focus:bg-white focus:border-[#142E18] focus:ring-4 focus:ring-[#142E18]/20 transition shadow-inner">
                        </template>
                        <span class="absolute left-3 top-3 text-xs font-black text-gray-400" x-text="recolteMode === 'galba' ? '{{ $locale === 'ar' ? 'DT / قلبة' : 'TND / Galba' }}' : '{{ $locale === 'ar' ? 'مليم / كغ' : 'Millimes / kg' }}'"></span>
                    </div>

                    {{-- Fast presets for recolte --}}
                    <div class="flex flex-wrap items-center justify-between text-xs font-bold text-gray-700 bg-gray-50 rounded-xl px-3 py-2 border border-gray-200 gap-1.5">
                        <span>{{ __('معلوم الجني المتداول:') }}</span>
                        <div class="flex flex-wrap items-center gap-1.5">
                            <button type="button" @click="recolteGalba = 3.5; onRecolteGalbaChange()" 
                                    :class="recolteGalba == 3.5 ? 'bg-emerald-600 border-emerald-600 text-white font-black shadow-xs' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-100'"
                                    class="px-2 py-0.5 rounded border text-[11px] transition">3.5 {{ $locale === 'ar' ? 'د' : 'DT' }}</button>
                            <button type="button" @click="recolteGalba = 4.5; onRecolteGalbaChange()" 
                                    :class="recolteGalba == 4.5 ? 'bg-emerald-600 border-emerald-600 text-white font-black shadow-xs' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-100'"
                                    class="px-2 py-0.5 rounded border text-[11px] transition">4.5 {{ $locale === 'ar' ? 'د' : 'DT' }}</button>
                            <button type="button" @click="recolteGalba = 5.6; onRecolteGalbaChange()" 
                                    :class="recolteGalba == 5.6 ? 'bg-emerald-600 border-emerald-600 text-white font-black shadow-xs' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-100'"
                                    class="px-2 py-0.5 rounded border text-[11px] transition">5.6 {{ $locale === 'ar' ? 'د' : 'DT' }} (400{{ $locale === 'ar' ? 'م' : 'm' }})</button>
                            <button type="button" @click="recolteGalba = 7.0; onRecolteGalbaChange()" 
                                    :class="recolteGalba == 7.0 ? 'bg-emerald-600 border-emerald-600 text-white font-black shadow-xs' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-100'"
                                    class="px-2 py-0.5 rounded border text-[11px] transition">7.0 {{ $locale === 'ar' ? 'د' : 'DT' }}</button>
                        </div>
                    </div>

                    {{-- Equivalency helper --}}
                    <div class="flex items-center justify-between text-xs font-bold text-gray-700 bg-gray-100 rounded-xl px-3 py-2">
                        <span>{{ __('التكلفة المقابلة:') }}</span>
                        <span>
                            <strong class="text-sm" x-text="recolteGalba"></strong> {{ __('د/قلبة') }}
                            =
                            <strong class="text-sm" x-text="recolteMillimes"></strong> {{ __('مليم/كغ') }}
                        </span>
                    </div>
                </div>

                {{-- FIELD 5: FRAIS TGARGIB / TRITURATION (MILLIMES/KG or GALBA) --}}
                <div class="bg-white rounded-2xl p-4 sm:p-5 border-2 border-gray-200 shadow-sm hover:border-[#6A8F3B] transition space-y-3">
                    <div class="flex flex-wrap items-center justify-between gap-1.5">
                        <label class="text-xs font-black text-[#142E18] uppercase tracking-wide flex items-center gap-1.5">
                            <span class="text-base">⚙️</span>
                            <span>{{ __('5. سوم التقرقيب (المعصرة)') }}</span>
                        </label>
                        {{-- Mode Switcher --}}
                        <div class="flex items-center bg-gray-100 p-1 rounded-xl text-xs font-bold gap-1">
                            <button type="button" 
                                    @click="tgargibMode = 'kg'" 
                                    :class="tgargibMode === 'kg' ? 'bg-[#b91c1c] text-white shadow-sm' : 'text-gray-600 hover:text-gray-900'"
                                    class="px-2.5 py-1 rounded-lg transition text-[11px] sm:text-xs">
                                {{ __('مليم/كغ') }}
                            </button>
                            <button type="button" 
                                    @click="tgargibMode = 'galba'" 
                                    :class="tgargibMode === 'galba' ? 'bg-[#b91c1c] text-white shadow-sm' : 'text-gray-600 hover:text-gray-900'"
                                    class="px-2.5 py-1 rounded-lg transition text-[11px] sm:text-xs">
                                {{ __('بالقلبة (د.ت)') }}
                            </button>
                        </div>
                    </div>

                    {{-- Big Input --}}
                    <div class="relative">
                        <template x-if="tgargibMode === 'kg'">
                            <input type="number" 
                                   x-model.number="tgargibMillimes" 
                                   @input="onTgargibMillimesChange()"
                                   step="10" min="50" max="600"
                                   class="w-full text-center text-xl sm:text-2xl font-black text-rose-700 bg-gray-50 border-2 border-gray-300 rounded-xl py-2.5 sm:py-3 px-2 focus:bg-white focus:border-rose-500 focus:ring-4 focus:ring-rose-500/20 transition shadow-inner">
                        </template>
                        <template x-if="tgargibMode === 'galba'">
                            <input type="number" 
                                   x-model.number="tgargibGalba" 
                                   @input="onTgargibGalbaChange()"
                                   step="0.2" min="0.5" max="8"
                                   class="w-full text-center text-xl sm:text-2xl font-black text-rose-700 bg-gray-50 border-2 border-gray-300 rounded-xl py-2.5 sm:py-3 px-2 focus:bg-white focus:border-rose-500 focus:ring-4 focus:ring-rose-500/20 transition shadow-inner">
                        </template>
                        <span class="absolute left-3 top-3 text-xs font-black text-gray-400" x-text="tgargibMode === 'kg' ? '{{ $locale === 'ar' ? 'مليم / كغ' : 'Millimes / kg' }}' : '{{ $locale === 'ar' ? 'DT / قلبة' : 'TND / Galba' }}'"></span>
                    </div>

                    {{-- Fast presets --}}
                    <div class="flex flex-wrap items-center justify-between text-xs font-bold text-rose-900 bg-rose-50 border border-rose-200 rounded-xl px-3 py-2 gap-1.5">
                        <span>{{ __('معلوم العصير المتداول:') }}</span>
                        <div class="flex flex-wrap items-center gap-1.5">
                            <button type="button" @click="tgargibMillimes = 150; onTgargibMillimesChange()" 
                                    :class="tgargibMillimes == 150 ? 'bg-rose-600 border-rose-600 text-white font-black shadow-xs' : 'bg-white border-rose-200 text-rose-900 hover:bg-rose-100'"
                                    class="px-2 py-0.5 rounded border text-[11px] transition">150 {{ $locale === 'ar' ? 'م' : 'm' }}</button>
                            <button type="button" @click="tgargibMillimes = 200; onTgargibMillimesChange()" 
                                    :class="tgargibMillimes == 200 ? 'bg-rose-600 border-rose-600 text-white font-black shadow-xs' : 'bg-white border-rose-200 text-rose-900 hover:bg-rose-100'"
                                    class="px-2 py-0.5 rounded border text-[11px] transition">200 {{ $locale === 'ar' ? 'م' : 'm' }}</button>
                            <button type="button" @click="tgargibMillimes = 250; onTgargibMillimesChange()" 
                                    :class="tgargibMillimes == 250 ? 'bg-rose-600 border-rose-600 text-white font-black shadow-xs' : 'bg-white border-rose-200 text-rose-900 hover:bg-rose-100'"
                                    class="px-2 py-0.5 rounded border text-[11px] transition">250 {{ $locale === 'ar' ? 'م' : 'm' }}</button>
                        </div>
                    </div>
                </div>

                {{-- SUMMARY QUICK CARD --}}
                <div class="bg-gradient-to-br from-[#142E18] to-[#0C1A0F] text-white rounded-2xl p-4 sm:p-5 border-2 border-[#C8A356]/40 shadow-lg flex flex-col justify-between space-y-3">
                    <div>
                        <div class="flex items-center justify-between text-xs text-[#F5E5C0] font-bold mb-2">
                            <span>{{ __('مؤشر التكلفة الإجمالية / كغ') }}</span>
                            <span class="bg-white/10 px-2 py-0.5 rounded-full text-[10px]">{{ __('جني + عصير') }}</span>
                        </div>
                        <div class="text-2xl sm:text-3xl font-black text-[#F5E5C0] flex items-baseline gap-1">
                            <span x-text="((recolteMillimes + tgargibMillimes) / 1000).toFixed(3)"></span>
                            <span class="text-sm font-bold text-gray-300">TND / {{ $locale === 'ar' ? 'كغ' : 'kg' }}</span>
                        </div>
                        <p class="text-[11px] sm:text-xs text-gray-300 mt-1">
                            {{ __('كل كغ زيتون يتكلف عليك') }} 
                            <strong class="text-white" x-text="recolteMillimes + tgargibMillimes"></strong> 
                            {{ __('مليم بين جني ومعصرة') }}
                        </p>
                    </div>

                    <div class="border-t border-white/10 pt-2.5 mt-2 flex items-center justify-between text-xs text-gray-300">
                        <span>{{ __('التكلفة للقلبة الواحدة:') }}</span>
                        <span class="font-black text-amber-300 text-sm">
                            <span x-text="(( (recolteMillimes + tgargibMillimes) / 1000 ) * 14).toFixed(3)"></span> {{ $locale === 'ar' ? 'د.ت' : 'TND' }}
                        </span>
                    </div>
                </div>

            </div>

            {{-- 2. LIVE RESULTS SHOWCASE: BIG, CLEAR & ACTIONABLE --}}
            <div class="border-t-2 border-gray-200 pt-6 sm:pt-8 space-y-4 sm:space-y-6">

                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h3 class="text-base sm:text-xl font-black text-[#142E18] flex items-center gap-2">
                        <span>📊</span>
                        <span>{{ __('النتائج الفورية للمحصول وصافي الأرباح') }}</span>
                    </h3>
                    <span class="text-[11px] sm:text-xs font-bold text-gray-500 bg-white border border-gray-200 px-2.5 sm:px-3 py-1 rounded-full shadow-sm">
                        {{ __('حساب لحظي مباشر') }}
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-5">

                    {{-- MAIN KPI 1: PRIX NET DU KILO D'OLIVE --}}
                    <div class="bg-gradient-to-br from-emerald-600 to-emerald-800 text-white rounded-2xl p-4 sm:p-6 shadow-xl relative overflow-hidden flex flex-col justify-between border-2 border-emerald-400/50">
                        <div class="absolute -right-3 -bottom-3 text-7xl opacity-15 pointer-events-none">⚖️</div>
                        <div>
                            <span class="text-xs font-black uppercase tracking-wider text-emerald-100 flex items-center gap-1.5">
                                <span>🌿</span>
                                <span>{{ __('سعر الكيلو زيتون الصافي') }}</span>
                            </span>
                            <div class="text-3xl sm:text-4xl font-black text-white mt-1.5 sm:mt-2 flex items-baseline gap-1.5">
                                <span x-text="netPricePerKg"></span>
                                <span class="text-base font-bold text-emerald-200">TND/{{ $locale === 'ar' ? 'كغ' : 'kg' }}</span>
                            </div>
                        </div>
                        <div class="mt-3 sm:mt-4 pt-2.5 sm:pt-3 border-t border-emerald-400/30 text-[11px] sm:text-xs text-emerald-100 font-medium">
                            {{ __('الصافي في جيبك بعد خصم مصاريف الجني والتقرقيب') }}
                        </div>
                    </div>

                    {{-- MAIN KPI 2: PRIX NET DE LA GALBA --}}
                    <div class="bg-gradient-to-br from-[#142E18] to-[#1B2A1B] text-white rounded-2xl p-4 sm:p-6 shadow-xl relative overflow-hidden flex flex-col justify-between border-2 border-[#C8A356]/40">
                        <div class="absolute -right-3 -bottom-3 text-7xl opacity-15 pointer-events-none">🧺</div>
                        <div>
                            <span class="text-xs font-black uppercase tracking-wider text-[#F5E5C0] flex items-center gap-1.5">
                                <span>🧺</span>
                                <span>{{ __('سعر القَلبة زيتون صافية') }}</span>
                            </span>
                            <div class="text-3xl sm:text-4xl font-black text-[#F5E5C0] mt-1.5 sm:mt-2 flex items-baseline gap-1.5">
                                <span x-text="netPricePerGalba"></span>
                                <span class="text-base font-bold text-amber-200">TND/{{ $locale === 'ar' ? 'قلبة' : 'galba' }}</span>
                            </div>
                        </div>
                        <div class="mt-3 sm:mt-4 pt-2.5 sm:pt-3 border-t border-white/10 text-[11px] sm:text-xs text-gray-300 font-medium">
                            {{ __('قيمة الـ 14 كغ زيتون صافية وموازية لسعر الباز') }}
                        </div>
                    </div>

                    {{-- MAIN KPI 3: TOTAL OIL YIELD --}}
                    <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-md border-2 border-gray-200 flex flex-col justify-between">
                        <div>
                            <span class="text-xs font-black uppercase tracking-wider text-gray-500 flex items-center gap-1.5">
                                <span>🫒</span>
                                <span>{{ __('محصول الزيت المتوقع') }}</span>
                            </span>
                            <div class="text-2xl sm:text-3xl font-black text-[#142E18] mt-1.5 sm:mt-2 flex items-baseline gap-1.5">
                                <span x-text="totalOilKg.toLocaleString()"></span>
                                <span class="text-sm font-bold text-gray-600">{{ __('كغ زيت') }}</span>
                            </div>
                        </div>
                        <div class="mt-3 sm:mt-4 pt-2.5 sm:pt-3 border-t border-gray-100 text-xs text-gray-600 font-semibold flex items-center justify-between">
                            <span>{{ __('يعادل باللتر:') }}</span>
                            <span class="font-bold text-[#6A8F3B]">
                                <strong x-text="totalOilLiters.toLocaleString()"></strong> {{ $locale === 'ar' ? 'لتر' : 'L' }}
                                (<span x-text="Math.round(totalOilLiters / 20)"></span> {{ __('بيدون') }})
                            </span>
                        </div>
                    </div>

                    {{-- MAIN KPI 4: NET CASH PROFIT --}}
                    <div class="bg-gradient-to-br from-amber-500 to-amber-700 text-white rounded-2xl p-4 sm:p-6 shadow-xl relative overflow-hidden flex flex-col justify-between border-2 border-amber-300/50">
                        <div class="absolute -right-3 -bottom-3 text-7xl opacity-15 pointer-events-none">💰</div>
                        <div>
                            <span class="text-xs font-black uppercase tracking-wider text-amber-100 flex items-center gap-1.5">
                                <span>💰</span>
                                <span>{{ __('صافي الأرباح في جيبك') }}</span>
                            </span>
                            <div class="text-2xl sm:text-4xl font-black text-white mt-1.5 sm:mt-2 flex items-baseline gap-1.5">
                                <span x-text="netProfit.toLocaleString()"></span>
                                <span class="text-base font-bold text-amber-100">TND</span>
                            </div>
                        </div>
                        <div class="mt-3 sm:mt-4 pt-2.5 sm:pt-3 border-t border-amber-300/30 text-[11px] sm:text-xs text-amber-100 font-medium">
                            {{ __('بعد خلاص كامل مصاريف الجني والتقرقيب') }}
                        </div>
                    </div>

                </div>

                {{-- DETAILED BREAKDOWN ROW & SMART SELLING DECISION ADVISOR --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">

                    {{-- EXPENSES BREAKDOWN CARD --}}
                    <div class="bg-white rounded-2xl p-4 sm:p-6 border-2 border-gray-200 shadow-sm space-y-3 sm:space-y-4">
                        <h4 class="text-sm font-black text-[#142E18] flex items-center gap-2">
                            <span>🧾</span>
                            <span>{{ __('تفصيل المصاريف والمدخول الإجمالي') }}</span>
                        </h4>

                        <div class="space-y-2.5 sm:space-y-3 text-xs sm:text-sm font-bold">
                            <div class="flex items-center justify-between p-2.5 rounded-xl bg-gray-50 border border-gray-100">
                                <span class="text-gray-600">{{ __('المدخول الخام لبيع الزيت بسعر الباز:') }}</span>
                                <span class="text-[#142E18] font-black text-sm sm:text-base">
                                    <span x-text="grossRevenue.toLocaleString()"></span> TND
                                </span>
                            </div>

                            <div class="flex items-center justify-between p-2.5 rounded-xl bg-rose-50 text-rose-900 border border-rose-100">
                                <span>{{ __('مجموع كلفة الجني (يد عاملة + ماكينات):') }}</span>
                                <span class="font-black">
                                    - <span x-text="totalRecolteCost.toLocaleString()"></span> TND
                                </span>
                            </div>

                            <div class="flex items-center justify-between p-2.5 rounded-xl bg-rose-50 text-rose-900 border border-rose-100">
                                <span>{{ __('مجموع كلفة التقرقيب في المعصرة:') }}</span>
                                <span class="font-black">
                                    - <span x-text="totalTgargibCost.toLocaleString()"></span> TND
                                </span>
                            </div>

                            <div class="flex items-center justify-between p-2.5 sm:p-3 rounded-xl bg-gray-100 text-gray-800 border-2 border-gray-200 font-black">
                                <span>{{ __('مجموع المصاريف الإجمالية:') }}</span>
                                <span class="text-rose-700 text-sm sm:text-base">
                                    <span x-text="totalExpenses.toLocaleString()"></span> TND
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- SMART SELLING ADVISOR (DECISION HELPER) --}}
                    <div class="bg-gradient-to-br from-[#f0fdf4] to-[#ecfdf5] rounded-2xl p-4 sm:p-6 border-2 border-emerald-300 shadow-sm flex flex-col justify-between space-y-3 sm:space-y-4">
                        <div>
                            <div class="flex items-center gap-2 mb-1.5 sm:mb-2">
                                <span class="text-xl">💡</span>
                                <h4 class="text-xs sm:text-sm font-black text-[#166534] uppercase tracking-wide">
                                    {{ __('دليل قرار الفلاح الذكي (Sur Pied vs Trituration)') }}
                                </h4>
                            </div>
                            <p class="text-xs sm:text-sm text-gray-700 leading-relaxed font-medium">
                                {{ __('إذا جاك تاجر أو خضّار يحب يشري منك الصابة حب على رؤوس أشجارها (في السانية):') }}
                            </p>
                        </div>

                        <div class="bg-white rounded-xl p-3.5 sm:p-4 border border-emerald-200 shadow-sm space-y-2">
                            <div class="flex items-baseline justify-between text-xs sm:text-sm">
                                <span class="font-bold text-gray-600">{{ __('الحد الأدنى لبيع الكيلو حب:') }}</span>
                                <span class="font-black text-emerald-700 text-base sm:text-lg">
                                    <span x-text="netPricePerKg"></span> TND / {{ $locale === 'ar' ? 'كغ' : 'kg' }}
                                </span>
                            </div>
                            <div class="flex items-baseline justify-between text-xs sm:text-sm border-t border-gray-100 pt-2">
                                <span class="font-bold text-gray-600">{{ __('الحد الأدنى لبيع القَلبة حب:') }}</span>
                                <span class="font-black text-emerald-700 text-base sm:text-lg">
                                    <span x-text="netPricePerGalba"></span> TND / {{ $locale === 'ar' ? 'قلبة' : 'galba' }}
                                </span>
                            </div>
                        </div>

                        <div class="text-[11px] font-bold text-emerald-800 bg-emerald-100/60 rounded-xl p-2.5 sm:p-3 border border-emerald-200 leading-relaxed">
                            ⚠️ <strong>{{ __('قاعدة السوق:') }}</strong> 
                            {{ __('أي سعر حب يعطيهولك الخضار أقل من هذا المبلغ، يعني راك خاسر مقارنة بإنك تجني وتعصر وتبيع زيتها بسعر الباز!') }}
                        </div>
                    </div>

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
                        $unitLabel = $unit === 'liter' ? ($locale === 'ar' ? 'لتر' : 'L') : ($unit === 'kg' ? ($locale === 'ar' ? 'كغ' : 'kg') : $unit);
                        $currency = $row->currency ?? 'TND';
                        $quality = $row->quality ?? ($isOil ? 'EVOO' : 'ممتاز');
                        $variety = $row->variety ?? '';
                        $gov = $row->governorate ?? '';
                        $soukRaw = $row->souk_name ?? '';
                        $soukDisplay = __($soukRaw ?: 'سوق تونس');
                        $govDisplay = __($gov ?: 'تونس');
                        $searchText = implode(' ', [$soukDisplay, $soukRaw, $gov, $govDisplay, $variety, $quality, $isOil ? 'زيت زيتون oil' : 'زيتون حب olive']);

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
                                        {{ $soukDisplay }}
                                    </h3>
                                </div>
                                <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-[#C8A356] text-gray-950 shadow-sm">
                                    {{ $govDisplay }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between text-xs text-gray-300 relative z-10 mt-2">
                                <span class="font-medium flex items-center gap-1">
                                    @if($isOil)
                                        <span class="text-amber-300 font-bold">🫒 {{ __('زيت زيتون') }}</span>
                                    @else
                                        <span class="text-emerald-300 font-bold">🌿 {{ __('زيتون حب') }}</span>
                                    @endif
                                    @if($variety) <span class="opacity-75">({{ __($variety) }})</span> @endif
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
                                    {{ __($quality) }}
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
                                        {{ __($countryRaw) }}
                                    </h3>
                                </div>
                                <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-[#C8A356] text-gray-950 shadow-sm">
                                    {{ __($quality) }}
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
                                <span class="font-semibold text-gray-600">{{ __($variety) }}</span>
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
