@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'خدماتنا وأسعارها' : (app()->getLocale() === 'fr' ? 'Nos Services & Tarifs' : 'Our Services & Pricing'))

@section('content')

<div x-data="{ 
    modalOpen: false, 
    activeServiceId: '', 
    activeServiceName: '', 
    activeServicePrice: '', 
    activeServiceIcon: '📦',
    openModal(id, name, price, icon) {
        this.activeServiceId = id;
        this.activeServiceName = name;
        this.activeServicePrice = price;
        this.activeServiceIcon = icon;
        this.modalOpen = true;
        
        // Log add_to_cart immediately in the background to capture clicks
        fetch('/services/appointment/' + id)
            .then(res => console.log('Logged add_to_cart'))
            .catch(err => console.error(err));
    }
}">
<div class="min-h-screen bg-cover bg-center" style="background-image: url('{{ asset('images/growpagepicture.png') }}'); background-color: #f8f9fa;">
    <div class="min-h-screen bg-black/60 pb-20">

    {{-- ─── HERO ─── --}}
    <div class="relative overflow-hidden pt-32 pb-16 px-4 text-center">
        <div class="relative max-w-4xl mx-auto text-center">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-[#6A8F3B]/20 border border-[#6A8F3B]/40 text-[#a8d060] text-xs font-semibold mb-6 backdrop-blur-sm">
                <span class="w-2 h-2 rounded-full bg-[#6A8F3B] animate-pulse"></span>
                {{ app()->getLocale() === 'ar' ? 'نمو وتطوير قطاع زيت الزيتون' : (app()->getLocale() === 'fr' ? 'Croissance & Tech Huile d\'Olive' : 'Olive Oil Growth & Tech Solutions') }}
            </div>
            <h1 class="text-4xl md:text-5xl font-black text-white mb-4 leading-tight">
                @if(app()->getLocale() === 'ar')
                    حلول رقمية لتنمية <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#6A8F3B] to-[#C8A356]">صادراتك ومبيعاتك</span>
                @elseif(app()->getLocale() === 'fr')
                    Solutions de <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#6A8F3B] to-[#C8A356]">Croissance & Export</span>
                @else
                    Digital Solutions for <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#6A8F3B] to-[#C8A356]">Export & Sales Growth</span>
                @endif
            </h1>
            <p class="text-white/60 text-base md:text-lg max-w-2xl mx-auto">
                {{ app()->getLocale() === 'ar'
                    ? 'زد أرباحك، وسّع نطاق عملائك، وابنِ حضوراً محلياً ودولياً متميزاً مع أفضل المتخصصين في تكنولوجيا زيت الزيتون.'
                    : (app()->getLocale() === 'fr'
                        ? 'Augmentez vos profits, élargissez votre clientèle et développez votre présence locale et internationale.'
                        : 'Increase your profits, expand your customer base, and build a premium local and international presence.') }}
            </p>
        </div>
    </div>

    {{-- ─── FREE DIGITAL SHOP OFFER (LEAD MAGNET) ─── --}}
    <div class="max-w-4xl mx-auto px-4 mb-12">
        <div class="relative overflow-hidden rounded-3xl p-[1px] bg-gradient-to-r from-[#6A8F3B] via-[#C8A356] to-[#6A8F3B] shadow-2xl">
            <div class="bg-gradient-to-br from-[#0f2010] to-[#122413] rounded-3xl p-6 md:p-8 relative">
                <div class="absolute -right-10 -bottom-10 w-44 h-44 bg-[#6A8F3B]/10 rounded-full blur-3xl"></div>
                <div class="relative z-10 flex flex-col lg:flex-row items-center gap-6 justify-between">
                    <div class="text-right max-w-xl">
                        <span class="px-3 py-1 rounded-full bg-[#C8A356] text-black text-[10px] font-black tracking-wider uppercase mb-3 inline-block">🚀 عرض محدود مجاني</span>
                        <h2 class="text-2xl md:text-3xl font-black text-white leading-tight">
                            {{ app()->getLocale() === 'ar' ? 'أطلق متجرك الموثق على ZinToop مجاناً!' : 'Launch your Verified ZinToop Shop for FREE!' }}
                        </h2>
                        <p class="text-white/70 text-sm mt-2 leading-relaxed">
                            {{ app()->getLocale() === 'ar'
                                ? 'احصل على صفحة احترافية وموقع رقمي متكامل لمعصرتك أو مزرعتك لعرض منتجاتك وجذب المشترين. سنقوم بإعداد كل شيء لك خلال 24 ساعة بدون أي تكلفة!'
                                : 'Get a premium digital storefront for your mill or farm to attract global buyers. We will set up everything for you in 24 hours at zero cost!' }}
                        </p>
                    </div>
                    
                    {{-- Quick WhatsApp Lead Form --}}
                    <div x-data="{ name: '', type: 'farmer', phone: '' }" class="w-full lg:w-80 bg-white/5 border border-white/15 p-5 rounded-2xl backdrop-blur-md">
                        <div class="space-y-3">
                            <div>
                                <input type="text" x-model="name" placeholder="{{ app()->getLocale() === 'ar' ? 'الاسم أو اسم النشاط التجارية...' : 'Business Name...' }}" class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-white text-xs placeholder-white/40 focus:outline-none focus:border-[#6A8F3B]">
                            </div>
                            <div>
                                <select x-model="type" class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-white text-xs focus:outline-none focus:border-[#6A8F3B]">
                                    <option value="farmer" class="bg-[#122413]">{{ app()->getLocale() === 'ar' ? 'مزرعة زيتون' : 'Olive Farm' }}</option>
                                    <option value="mill" class="bg-[#122413]">{{ app()->getLocale() === 'ar' ? 'معصرة زيت زيتون' : 'Olive Mill' }}</option>
                                    <option value="packer" class="bg-[#122413]">{{ app()->getLocale() === 'ar' ? 'شركة تعبئة / علامة تجارية' : 'Bottler / Private Label' }}</option>
                                </select>
                            </div>
                            <div>
                                <input type="text" x-model="phone" placeholder="{{ app()->getLocale() === 'ar' ? 'رقم الهاتف (واتساب)...' : 'WhatsApp Phone...' }}" class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-white text-xs placeholder-white/40 focus:outline-none focus:border-[#6A8F3B]">
                            </div>
                            <a :href="'https://wa.me/21625777926?text=' + encodeURIComponent('{{ app()->getLocale() === 'ar' ? 'مرحباً ZinToop، أريد الحصول على متجري الموثق مجاناً! الاسم: ' : 'Hi ZinToop, I want my free storefront! Name: ' }}' + name + ' | {{ app()->getLocale() === 'ar' ? 'النشاط: ' : 'Type: ' }}' + type + ' | {{ app()->getLocale() === 'ar' ? 'الهاتف: ' : 'Phone: ' }}' + phone)"
                               target="_blank"
                               @click="if(!name || !phone) { alert('{{ app()->getLocale() === 'ar' ? 'يرجى ملء جميع الحقول!' : 'Please fill all fields!' }}'); $event.preventDefault(); }"
                               class="w-full py-3 bg-gradient-to-r from-[#6A8F3B] to-[#5a7a2f] text-white text-xs font-bold rounded-xl hover:scale-102 transition-all duration-200 shadow-md flex items-center justify-center gap-2 no-underline cursor-pointer">
                                <span>💬</span>
                                {{ app()->getLocale() === 'ar' ? 'أرسل الطلب عبر واتساب' : 'Apply via WhatsApp' }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── BILLING TOGGLE ─── --}}
    @if(session('success'))
        <div class="max-w-4xl mx-auto px-4 mt-8" x-data="{ show: true }" x-show="show" x-transition>
            <div class="bg-green-500/10 border border-green-500/50 text-green-400 p-4 rounded-xl flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="font-bold">{{ session('success') }}</span>
                </div>
                <button @click="show = false" class="text-green-400 hover:text-green-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if(typeof fbq !== 'undefined') {
                    fbq('track', 'Schedule');
                    fbq('track', 'Lead');
                }
            });
        </script>
    @endif

    <div class="flex justify-center mb-10 mt-8 px-4" x-data="{ annual: false }">
        <div class="inline-flex items-center gap-4 bg-white/5 border border-white/10 rounded-2xl p-1.5 backdrop-blur-sm">
            <button @click="annual = false"
                :class="!annual ? 'bg-[#6A8F3B] text-white shadow-lg' : 'text-white/50 hover:text-white'"
                class="px-5 py-2 rounded-xl text-sm font-semibold transition-all duration-300">
                {{ app()->getLocale() === 'ar' ? 'شهري' : (app()->getLocale() === 'fr' ? 'Mensuel' : 'Monthly') }}
            </button>
            <button @click="annual = true"
                :class="annual ? 'bg-[#6A8F3B] text-white shadow-lg' : 'text-white/50 hover:text-white'"
                class="px-5 py-2 rounded-xl text-sm font-semibold transition-all duration-300 flex items-center gap-2">
                {{ app()->getLocale() === 'ar' ? 'سنوي' : (app()->getLocale() === 'fr' ? 'Annuel' : 'Annual') }}
                <span class="px-2 py-0.5 bg-[#C8A356] text-black text-[10px] font-black rounded-full">-20%</span>
            </button>
        </div>

        {{-- ─── PRICING CARDS ─── --}}
        <div class="hidden"><!-- alpine bridge --></div>
    </div>

    <div x-data="{ annual: false }" class="max-w-7xl mx-auto px-4 pb-20">

        {{-- Toggle (hidden, controlled by outer) --}}
        {{-- We use a self-contained alpine scope here --}}

        {{-- SECTION: Marketing --}}
        <div class="mb-16">
            <div class="flex items-center gap-3 mb-8">
                <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-[#C8A356] to-amber-600 shadow-lg">
                    <span class="text-xl">📣</span>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white">
                        {{ app()->getLocale() === 'ar' ? 'خدمات التسويق الرقمي' : (app()->getLocale() === 'fr' ? 'Marketing Digital' : 'Digital Marketing') }}
                    </h2>
                    <p class="text-white/40 text-xs">{{ app()->getLocale() === 'ar' ? 'حضور قوي على الإنترنت' : 'Strong online presence' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @php
                $marketingServices = \App\Models\MarketingService::all();
                @endphp
                @foreach($marketingServices as $service)
                {{-- Service Card --}}
                <div class="group relative bg-white border border-gray-100 shadow-xl rounded-3xl p-7 hover:-translate-y-2 hover:shadow-2xl hover:shadow-[#6A8F3B]/20 transition-all duration-300 flex flex-col overflow-hidden">
                    <!-- Background Decorative Icon -->
                    <div class="absolute -right-6 -top-6 w-40 h-40 opacity-[0.15] pointer-events-none group-hover:scale-110 group-hover:rotate-12 transition-transform duration-700 invert">
                        <img src="{{ asset('icons/eye-browse-svg.svg') }}" class="w-full h-full object-contain">
                    </div>
                    <div class="mb-4">
                        <span class="text-3xl">{{ $service->icon_url }}</span>
                        <h3 class="text-lg font-bold text-gray-900 mt-3">
                            {{ app()->getLocale() === 'ar' ? $service->title_ar : (app()->getLocale() === 'fr' ? $service->title_fr : $service->title_en) }}
                        </h3>
                    </div>
                    <div class="mb-6 flex-grow">
                        <div class="text-3xl font-black text-[#C8A356]">{{ number_format($service->price_tnd_weekly, 0) }} <span class="text-base font-normal text-gray-500">{{ $service->currency }}<span class="text-xs">/{{ app()->getLocale() === 'ar' ? 'أسبوع' : 'week' }}</span></span></div>
                        <div class="mt-4 p-3 bg-gray-50 rounded-xl border border-gray-100">
                            <p class="text-sm text-[#6A8F3B] font-semibold">{{ app()->getLocale() === 'ar' ? 'النتائج المتوقعة:' : 'Expected Results:' }}</p>
                            <p class="text-gray-600 text-xs mt-1 leading-relaxed">
                                {{ app()->getLocale() === 'ar' ? $service->results_ar : (app()->getLocale() === 'fr' ? $service->results_fr : $service->results_en) }}
                            </p>
                        </div>
                    </div>
                    
                    <button @click="openModal('{{ $service->id }}', '{{ app()->getLocale() === 'ar' ? $service->title_ar : (app()->getLocale() === 'fr' ? $service->title_fr : $service->title_en) }}', '{{ number_format($service->price_tnd_weekly, 0) }}', '{{ $service->icon_url }}')" 
                            class="w-full text-center py-3 rounded-xl border border-[#6A8F3B]/20 bg-[#6A8F3B]/5 text-[#6A8F3B] hover:bg-[#6A8F3B] hover:text-white transition-all duration-200 text-sm font-semibold flex items-center justify-center gap-2 cursor-pointer">
                        <span>📅</span>
                        {{ app()->getLocale() === 'ar' ? 'احجز موعد' : 'Book Appointment' }}
                    </button>
                </div>
                @endforeach
            </div>
        </div>

        {{-- ─── VIRAL INTERACTIVE CALCULATOR (YIELD & PROFIT) ─── --}}
        <div class="mb-16">
            <div class="relative overflow-hidden rounded-3xl p-[1px] bg-gradient-to-r from-[#6A8F3B] via-[#C8A356] to-[#6A8F3B] shadow-2xl">
                <div class="bg-gradient-to-br from-[#0c180d] to-[#122413] rounded-3xl p-6 md:p-10 relative">
                    <div class="absolute right-0 top-0 w-80 h-80 bg-[#6A8F3B]/10 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="relative z-10 max-w-4xl mx-auto">
                        <div class="text-center mb-8">
                            <span class="text-4xl">🧮</span>
                            <h2 class="text-2xl md:text-3xl font-black text-white mt-2">
                                {{ app()->getLocale() === 'ar' ? 'حاسبة عصر وإنتاج زيت الزيتون التونسي' : 'Tunisian Olive Oil Yield & Profit Calculator' }}
                            </h2>
                            <p class="text-white/60 text-xs md:text-sm mt-2 max-w-xl mx-auto">
                                {{ app()->getLocale() === 'ar'
                                    ? 'أدخل وزن الزيتون ونسبة استخلاص معصرتك لتقدير إنتاجك من الزيت والأرباح الصافية المتوقعة فوراً!'
                                    : 'Estimate your oil yield in liters and your projected net profit based on standard milling parameters.' }}
                            </p>
                        </div>

                        {{-- Alpine Calculator --}}
                        <div x-data="{
                            oliveWeight: 2000,
                            yieldPct: 18,
                            oilPrice: 15,
                            millingCostPerKg: 0.150,
                            get oilLiters() {
                                return (this.oliveWeight * (this.yieldPct / 100)).toFixed(0);
                            },
                            get totalValue() {
                                return (this.oilLiters * this.oilPrice).toFixed(0);
                            },
                            get totalMilling() {
                                return (this.oliveWeight * this.millingCostPerKg).toFixed(0);
                            },
                            get netProfit() {
                                return (this.totalValue - this.totalMilling).toFixed(0);
                            }
                        }" class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            
                            {{-- Inputs --}}
                            <div class="bg-white/5 border border-white/10 rounded-2xl p-6 space-y-6">
                                {{-- Input 1: Olive Weight --}}
                                <div>
                                    <div class="flex justify-between items-center mb-2">
                                        <label class="text-white text-xs font-semibold">{{ app()->getLocale() === 'ar' ? 'وزن الزيتون (كجم):' : 'Olive Weight (kg):' }}</label>
                                        <span class="text-[#a8d060] font-black text-sm" x-text="oliveWeight + ' كجم'"></span>
                                    </div>
                                    <input type="range" min="100" max="20000" step="100" x-model="oliveWeight" class="w-full accent-[#6A8F3B] bg-white/10 rounded-lg h-2 cursor-pointer">
                                </div>

                                {{-- Input 2: Yield Percentage --}}
                                <div>
                                    <div class="flex justify-between items-center mb-2">
                                        <label class="text-white text-xs font-semibold">{{ app()->getLocale() === 'ar' ? 'نسبة الاستخلاص / المردودية (%):' : 'Oil Extraction Yield (%):' }}</label>
                                        <span class="text-[#a8d060] font-black text-sm" x-text="yieldPct + ' %'"></span>
                                    </div>
                                    <input type="range" min="10" max="30" step="1" x-model="yieldPct" class="w-full accent-[#6A8F3B] bg-white/10 rounded-lg h-2 cursor-pointer">
                                </div>

                                {{-- Input 3: Price per Liter --}}
                                <div>
                                    <div class="flex justify-between items-center mb-2">
                                        <label class="text-white text-xs font-semibold">{{ app()->getLocale() === 'ar' ? 'سعر لتر الزيت (د.ت):' : 'Oil Price per Liter (TND):' }}</label>
                                        <span class="text-[#a8d060] font-black text-sm" x-text="oilPrice + ' د.ت'"></span>
                                    </div>
                                    <input type="range" min="10" max="30" step="0.5" x-model="oilPrice" class="w-full accent-[#6A8F3B] bg-white/10 rounded-lg h-2 cursor-pointer">
                                </div>
                            </div>

                            {{-- Outputs & Share --}}
                            <div class="bg-[#172d19] border border-[#6A8F3B]/30 rounded-2xl p-6 flex flex-col justify-between">
                                <div class="space-y-4">
                                    <div class="flex justify-between items-center border-b border-white/5 pb-2">
                                        <span class="text-white/60 text-xs">{{ app()->getLocale() === 'ar' ? 'الإنتاج المتوقع من الزيت:' : 'Estimated Oil Yield:' }}</span>
                                        <span class="text-white font-black text-lg" x-text="oilLiters + ' لتر'"></span>
                                    </div>
                                    <div class="flex justify-between items-center border-b border-white/5 pb-2">
                                        <span class="text-white/60 text-xs">{{ app()->getLocale() === 'ar' ? 'القيمة الإجمالية للمبيعات:' : 'Total Gross Value:' }}</span>
                                        <span class="text-white font-black text-lg" x-text="totalValue + ' د.ت'"></span>
                                    </div>
                                    <div class="flex justify-between items-center border-b border-white/5 pb-2">
                                        <span class="text-white/60 text-xs">{{ app()->getLocale() === 'ar' ? 'تكلفة العصر المقدرة (0.150 د.ت/كجم):' : 'Estimated Milling Cost:' }}</span>
                                        <span class="text-red-400 font-bold text-sm" x-text="'- ' + totalMilling + ' د.ت'"></span>
                                    </div>
                                    <div class="flex justify-between items-center bg-[#C8A356]/10 p-3 rounded-xl border border-[#C8A356]/20">
                                        <span class="text-[#C8A356] font-bold text-sm">{{ app()->getLocale() === 'ar' ? 'صافي الأرباح المقدرة:' : 'Net Projected Profit:' }}</span>
                                        <span class="text-white font-black text-2xl" x-text="netProfit + ' د.ت'"></span>
                                    </div>
                                </div>

                                {{-- Social WhatsApp Share Loop --}}
                                <div class="mt-6">
                                    <button @click="
                                        const msg = encodeURIComponent('{{ app()->getLocale() === 'ar' ? 'لقد قمت بحساب أرباحي المتوقعة لعصر زيت الزيتون عبر حاسبة ZinToop! الوزن: ' : 'Calculated my olive oil profit using ZinToop Calculator! Olives: ' }}' + oliveWeight + ' kg | {{ app()->getLocale() === 'ar' ? 'الإنتاج المتوقع: ' : 'Yield: ' }}' + oilLiters + ' Liters | {{ app()->getLocale() === 'ar' ? 'الأرباح الصافية المقدرة: ' : 'Net profit: ' }}' + netProfit + ' TND. {{ app()->getLocale() === 'ar' ? 'احسب أرباحك الآن مجاناً عبر الرابط: ' : 'Calculate yours free: ' }}' + window.location.href);
                                        window.open('https://wa.me/?text=' + msg, '_blank');
                                    " class="w-full py-3.5 bg-green-600 hover:bg-green-500 text-white font-bold rounded-xl transition-all duration-200 flex items-center justify-center gap-3 text-sm shadow-lg shadow-green-600/30">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                        {{ app()->getLocale() === 'ar' ? 'مشاركة الحساب عبر واتساب' : 'Share Results via WhatsApp' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION: Consulting & Trade --}}
        <div class="mb-16">
            <div class="flex items-center gap-3 mb-8">
                <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-green-600 shadow-lg">
                    <span class="text-xl">⚖️</span>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white">
                        {{ app()->getLocale() === 'ar' ? 'الاستشارات وخدمات التصدير' : (app()->getLocale() === 'fr' ? 'Conseil & Export' : 'Consulting & Export Services') }}
                    </h2>
                    <p class="text-white/40 text-xs">{{ app()->getLocale() === 'ar' ? 'مرافقة احترافية للأسواق العالمية' : 'Professional guidance for global markets' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                @php
                $consultingPlans = [
                    ['icon' => '🌍', 'price' => '150', 'name_ar' => 'استشارات التصدير والقوانين', 'name_en' => 'Export Legal Consultation', 'name_fr' => 'Consultation Juridique Export', 'desc_ar' => 'مرافقة خطوة بخطوة في قوانين التصدير والشروط الجمركية للأسواق المستهدفة.', 'desc_en' => 'Step-by-step guidance on export laws and customs requirements for target markets.', 'features_ar' => ['توجيه حول التراخيص والشهادات المطلوبة', 'تحليل التعريفات الجمركية', 'خارطة طريق خطوة بخطوة للتصدير'], 'features_en' => ['Guidance on required licenses & certificates', 'Customs tariffs analysis', 'Step-by-step export roadmap']],
                    ['icon' => '✍️', 'price' => '250', 'name_ar' => 'صياغة العقود التجارية الدولية', 'name_en' => 'International Trade Contracts', 'name_fr' => 'Contrats de Commerce International', 'desc_ar' => 'إعداد ومراجعة العقود التجارية لضمان حقوقك وتسهيل عمليات الشحن والدفع.', 'desc_en' => 'Drafting and reviewing commercial contracts to secure your rights and facilitate shipping.', 'features_ar' => ['عقود الشحن والتسليم (Incoterms)', 'شروط الدفع البنكي الآمنة', 'مراجعة بنود الجودة والضمان'], 'features_en' => ['Shipping & Delivery contracts (Incoterms)', 'Secure bank payment terms', 'Review of quality and warranty clauses']],
                    ['icon' => '📊', 'price' => '300', 'name_ar' => 'دراسة الأسواق والتحليل الاحترافي', 'name_en' => 'Market Study & Price Analysis', 'name_fr' => 'Étude de Marché & Analyse de Prix', 'desc_ar' => 'تقارير احترافية دقيقة حول الأسعار العالمية والفرص المتاحة لزيت الزيتون.', 'desc_en' => 'Accurate professional reports on global prices and opportunities for olive oil.', 'features_ar' => ['تحليل الأسعار المنافسة في السوق', 'تحديد أفضل الأسواق المستهدفة للربح', 'توقعات الأسعار وتقلبات العرض والطلب'], 'features_en' => ['Analysis of competitive market prices', 'Identifying the most profitable target markets', 'Price forecasting and supply/demand trends']],
                ];
                $locale = app()->getLocale();
                @endphp

                @foreach($consultingPlans as $plan)
                <div class="group bg-white border border-gray-100 shadow-xl rounded-2xl p-6 hover:-translate-y-2 hover:shadow-2xl hover:shadow-emerald-500/20 transition-all duration-300 flex flex-col relative overflow-hidden">
                    <div class="absolute -right-6 -top-6 w-40 h-40 opacity-[0.05] pointer-events-none group-hover:scale-110 transition-transform duration-700">
                        @if($plan['icon'] === '🌍')
                            <img src="{{ asset('icons/cloud-acceleration-svg.svg') }}" class="w-full h-full object-contain">
                        @elseif($plan['icon'] === '✍️')
                            <img src="{{ asset('icons/target-svg.svg') }}" class="w-full h-full object-contain">
                        @else
                            <img src="{{ asset('icons/interface-control-svggraphscreen.svg') }}" class="w-full h-full object-contain">
                        @endif
                    </div>
                    <div class="text-3xl mb-3">{{ $plan['icon'] }}</div>
                    <h3 class="text-base font-bold text-gray-900">{{ $locale === 'ar' ? $plan['name_ar'] : ($locale === 'fr' ? $plan['name_fr'] : $plan['name_en']) }}</h3>
                    <p class="text-gray-500 text-xs mt-0.5 mb-4">{{ $locale === 'ar' ? $plan['desc_ar'] : $plan['desc_en'] }}</p>
                    <div class="text-2xl font-black text-emerald-600 mb-1">{{ $plan['price'] }} <span class="text-sm font-normal text-gray-400">TND</span></div>
                    <p class="text-gray-400 text-[11px] mb-5">{{ $locale === 'ar' ? 'سعر الخدمة' : 'service price' }}</p>
                    <ul class="space-y-2 mb-6 flex-1 text-xs text-gray-600">
                        @foreach(($locale === 'ar' ? $plan['features_ar'] : $plan['features_en']) as $f)
                        <li class="flex items-start gap-1.5">
                            <svg class="w-3.5 h-3.5 text-emerald-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            {{ $f }}
                        </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('public.contact') }}" class="block w-full text-center py-2.5 rounded-xl border border-emerald-500/30 bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white transition-all duration-200 text-xs font-bold">
                        {{ $locale === 'ar' ? 'اطلب الخدمة الآن' : 'Request Service Now' }}
                    </a>
                </div>
                @endforeach
            </div>
        </div>

        {{-- SECTION: Development --}}
        <div class="mb-16">
            <div class="flex items-center gap-3 mb-8">
                <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 shadow-lg">
                    <span class="text-xl">⚙️</span>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white">
                        {{ app()->getLocale() === 'ar' ? 'خدمات التطوير التقني' : (app()->getLocale() === 'fr' ? 'Développement Technique' : 'Technical Development') }}
                    </h2>
                    <p class="text-white/40 text-xs">{{ app()->getLocale() === 'ar' ? 'حلول برمجية متخصصة' : 'Specialized software solutions' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">

                @php
                $devPlans = [
                    ['icon' => '🌐', 'price' => '1,900', 'name_ar' => 'واجهة المعصرة الرقمية', 'name_en' => 'Digital Mill Storefront', 'name_fr' => 'Site Web de Pressoir', 'desc_ar' => 'تصميم موقع ويب مخصص للمزارع والمعاصر لعرض قدراتها وإنتاجها.', 'desc_en' => 'Tailored web page for olive mills & farms to display milling capabilities.', 'features_ar' => ['تصميم متجاوب متكامل للكمبيوتر والموبايل', 'عرض متميز لقدرات العصر والتخزين والتعبئة', 'خريطة الموقع لسهولة الوصول المباشر', 'لوحة تحكم إدارية لتحديث الصور والأسعار', 'تحسين كامل لمحركات البحث SEO للظهور محلياً'], 'features_en' => ['Fully responsive web design', 'Showcase milling & storage capabilities', 'Google Maps integration for buyers', 'Simple admin panel to update listings', 'Optimized for local & international SEO']],
                    ['icon' => '🛒', 'price' => '3,500', 'name_ar' => 'الباقة الرقمية للتصدير', 'name_en' => 'Global Exporter Digital Suite', 'name_fr' => 'Pack Export Digital', 'desc_ar' => 'متجر إلكتروني متكامل للعلامات التجارية موجه للأسواق العالمية والمشترين الدوليين.', 'desc_en' => 'Complete e-commerce platform built to drive international bulk & bottle sales.', 'features_ar' => ['متجر إلكتروني متعدد اللغات والعملات', 'ربط بوابات الدفع الإلكتروني الدولية والمحلية', 'تكامل شهادات التحليل المخبري واللوائح الفنية', 'عرض متميز لقصة العلامة وجودة المنتج', 'دعم فني وتحديثات مستمرة لمدة 3 أشهر'], 'features_en' => ['Multi-currency & multi-language e-commerce', 'Secure international payment gateways', 'Laboratory test results & certificate embeds', 'Premium branding & heritage storytelling', '3 months continuous tech support']],
                    ['icon' => '📱', 'price' => '5,900', 'name_ar' => 'تطبيق الموبايل الخاص', 'name_en' => 'ZinToop VIP Brand Mobile App', 'name_fr' => 'App Mobile Privée', 'desc_ar' => 'تطبيق جوال مخصص لعلامتك التجارية الفاخرة لزيت الزيتون على iOS و Android.', 'desc_en' => 'Custom mobile application for premium olive oil brands on iOS & Android.', 'features_ar' => ['تطبيق جوال مصمم ومبرمج بالكامل للعلامة', 'نظام تتبع شحنات المشتري وتفاصيل العقد', 'إشعارات فورية للمشترين بالعروض الجديدة والأسعار', 'لوحة تحكم ويب متكاملة وسهلة الإدارة للعلامة', 'صيانة ودعم فني وتحديثات مستمرة لمدة 6 أشهر'], 'features_en' => ['Fully customized mobile app build', 'Real-time order & shipment tracking', 'Direct push notifications for new pricing lists', 'Robust web admin dashboard', '6 months full maintenance & tech support']],
                    ['icon' => '🤖', 'price' => '2,200', 'name_ar' => 'نظام إدارة إنتاج زيت الزيتون (ERP)', 'name_en' => 'Olive Supply Chain & ERP System', 'name_fr' => 'ERP Pressoir & Gestion', 'desc_ar' => 'برمجيات متكاملة لإدارة معاصر زيت الزيتون، المخازن، والعمليات اللوجستية.', 'desc_en' => 'Specialized software system to track harvest weights, extraction rates, and tanks.', 'features_ar' => ['إدارة استلام الزيتون وحساب نسب الاستخلاص بدقة', 'تتبع تفصيلي لخزانات الزيت وتخزين المنتجات', 'نظام الفواتير الرقمي والتقارير المالية والضريبية', 'تتبع المبيعات المحلية وعقود الشحن والتصدير', 'تكامل حي مع أسعار ZinToop لتحديث قيم السوق'], 'features_en' => ['Track olive weights & extraction ratios', 'Detailed oil tank & storage tracking', 'Invoicing, financial & tax reporting modules', 'Local sales & shipping contract logging', 'Live API integrations with market price tickers']],
                ];
                $locale = app()->getLocale();
                @endphp

                @foreach($devPlans as $plan)
                <div class="group bg-white border border-gray-100 shadow-xl rounded-2xl p-6 hover:-translate-y-2 hover:shadow-2xl hover:shadow-blue-500/20 transition-all duration-300 flex flex-col relative overflow-hidden">
                    <!-- Background Decorative Icon -->
                    <div class="absolute -right-6 -top-6 w-40 h-40 opacity-[0.15] pointer-events-none group-hover:scale-110 transition-transform duration-700 invert">
                        @if($plan['icon'] === '🌐')
                            <img src="{{ asset('icons/cloud-acceleration-svg.svg') }}" class="w-full h-full object-contain">
                        @elseif($plan['icon'] === '🛒')
                            <img src="{{ asset('icons/online-delivery-svg.svg') }}" class="w-full h-full object-contain">
                        @elseif($plan['icon'] === '📱')
                            <img src="{{ asset('icons/target-svg.svg') }}" class="w-full h-full object-contain">
                        @else
                            <img src="{{ asset('icons/ddos-protection-svg.svg') }}" class="w-full h-full object-contain">
                        @endif
                    </div>
                    <div class="text-3xl mb-3">{{ $plan['icon'] }}</div>
                    <h3 class="text-base font-bold text-gray-900">{{ $locale === 'ar' ? $plan['name_ar'] : ($locale === 'fr' ? $plan['name_fr'] : $plan['name_en']) }}</h3>
                    <p class="text-gray-500 text-xs mt-0.5 mb-4">{{ $locale === 'ar' ? $plan['desc_ar'] : $plan['desc_en'] }}</p>
                    <div class="text-2xl font-black text-blue-600 mb-1">{{ $plan['price'] }} <span class="text-sm font-normal text-gray-400">TND</span></div>
                    <p class="text-gray-400 text-[11px] mb-5">{{ $locale === 'ar' ? 'سعر المشروع' : 'project price' }}</p>
                    <ul class="space-y-2 mb-6 flex-1 text-xs text-gray-600">
                        @foreach(($locale === 'ar' ? $plan['features_ar'] : $plan['features_en']) as $f)
                        <li class="flex items-start gap-1.5">
                            <svg class="w-3.5 h-3.5 text-blue-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            {{ $f }}
                        </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('public.contact') }}" class="block w-full text-center py-2.5 rounded-xl border border-blue-500/30 bg-blue-50 text-blue-600 hover:bg-blue-500 hover:text-white transition-all duration-200 text-xs font-bold">
                        {{ $locale === 'ar' ? 'احجز استشارة' : 'Book a Consultation' }}
                    </a>
                </div>
                @endforeach

            </div>
        </div>

        {{-- ─── CUSTOM QUOTE BANNER ─── --}}
        <div class="relative overflow-hidden rounded-3xl p-[1px] bg-gradient-to-r from-[#6A8F3B] via-[#C8A356] to-[#6A8F3B]">
            <div class="relative bg-gradient-to-r from-[#0f2010] to-[#1a3310] rounded-3xl px-8 py-10 text-center">
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-transparent via-[#6A8F3B]/50 to-transparent"></div>
                <span class="text-4xl mb-4 block">💬</span>
                <h3 class="text-2xl font-black text-white mb-3">
                    {{ $locale === 'ar' ? 'مشروع مخصص؟' : 'Need a Custom Quote?' }}
                </h3>
                <p class="text-white/50 mb-7 max-w-xl mx-auto text-sm">
                    {{ $locale === 'ar'
                        ? 'لديك متطلبات خاصة؟ تواصل معنا وسنبني لك حلاً مخصصاً بسعر مناسب.'
                        : 'Have specific requirements? Contact us and we\'ll build a tailored solution that fits your budget.' }}
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="{{ route('public.contact') }}"
                       class="px-8 py-3.5 bg-gradient-to-r from-[#6A8F3B] to-[#5a7a2f] text-white font-bold rounded-xl hover:scale-105 transition-all duration-200 shadow-lg shadow-[#6A8F3B]/30 text-sm">
                        {{ $locale === 'ar' ? '📞 تواصل معنا' : '📞 Get in Touch' }}
                    </a>
                    <a href="https://wa.me/21625777926" target="_blank"
                       class="px-8 py-3.5 bg-green-600/20 border border-green-500/30 text-green-400 font-bold rounded-xl hover:bg-green-600/30 transition-all duration-200 text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        WhatsApp
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ─── INSTANT LEAD CAPTURE MODAL ─── --}}
<div x-show="modalOpen" 
     class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
     x-cloak>
    
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/75 backdrop-blur-md" @click="modalOpen = false"></div>

    <!-- Modal Card -->
    <div class="relative w-full max-w-lg bg-gradient-to-br from-[#0c180d] to-[#122413] border border-white/10 rounded-[2.5rem] shadow-2xl p-6 sm:p-8 overflow-hidden z-10"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-4">
        
        <!-- Premium decorative background light -->
        <div class="absolute -right-16 -top-16 w-48 h-48 bg-[#6A8F3B]/25 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-16 -bottom-16 w-48 h-48 bg-[#C8A356]/15 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Header -->
        <div class="flex items-center justify-between mb-6 relative z-10">
            <div class="flex items-center gap-3">
                <span class="text-3xl" x-text="activeServiceIcon">📦</span>
                <div>
                    <h3 class="text-xl font-black text-white" x-text="activeServiceName">باقة التسويق</h3>
                    <p class="text-xs text-white/50 mt-0.5">
                        {{ app()->getLocale() === 'ar' ? 'أدخل تفاصيلك لتأكيد الحجز وبدء الحملة فوراً' : 'Fill details to confirm your instant booking' }}
                    </p>
                </div>
            </div>
            <button @click="modalOpen = false" class="w-8 h-8 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-white/60 hover:text-white hover:bg-white/10 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Price display -->
        <div class="bg-white/5 border border-white/10 rounded-2xl p-4 mb-6 flex items-center justify-between relative z-10">
            <span class="text-sm font-semibold text-white/60">{{ app()->getLocale() === 'ar' ? 'الميزانية الأسبوعية المقدرة:' : 'Estimated Weekly Budget:' }}</span>
            <div class="text-2xl font-black text-[#C8A356] flex items-baseline gap-1">
                <span x-text="activeServicePrice">0</span>
                <span class="text-xs font-normal text-white/50">TND</span>
            </div>
        </div>

        <!-- Form -->
        <form :action="'/services/appointment/' + activeServiceId" method="POST" class="space-y-4 relative z-10">
            @csrf
            
            <div>
                <label class="block text-xs font-semibold text-white/70 mb-2">{{ app()->getLocale() === 'ar' ? 'الاسم بالكامل أو اسم النشاط التجاري' : 'Full Name / Business Name' }} <span class="text-red-400">*</span></label>
                <input type="text" 
                       name="name" 
                       required 
                       placeholder="{{ app()->getLocale() === 'ar' ? 'مثال: معصرة الزيتون الكبرى...' : 'e.g. Olive Oil Mill Co.' }}"
                       class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-white text-sm placeholder-white/30 focus:outline-none focus:border-[#6A8F3B] focus:ring-2 focus:ring-[#6A8F3B]/20 transition-all">
            </div>

            <div>
                <label class="block text-xs font-semibold text-white/70 mb-2">{{ app()->getLocale() === 'ar' ? 'رقم الهاتف أو الواتساب' : 'Phone / WhatsApp Number' }} <span class="text-red-400">*</span></label>
                <input type="tel" 
                       name="phone" 
                       required 
                       placeholder="{{ app()->getLocale() === 'ar' ? 'مثال: 216XXXXXXXX+' : 'e.g. +216XXXXXXXX' }}"
                       class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-white text-sm placeholder-white/30 focus:outline-none focus:border-[#6A8F3B] focus:ring-2 focus:ring-[#6A8F3B]/20 transition-all">
            </div>

            <div>
                <label class="block text-xs font-semibold text-white/70 mb-2">{{ app()->getLocale() === 'ar' ? 'معلومات إضافية عن نشاطك (اختياري)' : 'Additional Info (Optional)' }}</label>
                <textarea name="business_info" 
                          rows="2" 
                          placeholder="{{ app()->getLocale() === 'ar' ? 'مثال: رابط صفحة الفيسبوك أو تفاصيل المنتج...' : 'e.g. Facebook page link or product details...' }}"
                          class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-white text-sm placeholder-white/30 focus:outline-none focus:border-[#6A8F3B] focus:ring-2 focus:ring-[#6A8F3B]/20 transition-all"></textarea>
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                    class="w-full bg-gradient-to-r from-[#6A8F3B] to-[#5a7a2f] hover:from-[#5a7a2f] hover:to-[#4e6a28] text-white font-bold py-3.5 px-6 rounded-xl shadow-lg hover:shadow-xl hover:shadow-[#6A8F3B]/20 active:scale-[0.98] transition-all duration-200 text-sm flex items-center justify-center gap-2 cursor-pointer mt-6">
                <span>💬</span>
                {{ app()->getLocale() === 'ar' ? 'احجز الآن وتواصل عبر واتساب' : 'Book & Chat on WhatsApp' }}
            </button>
            
            <p class="text-[10px] text-center text-white/40 mt-3 leading-relaxed">
                {{ app()->getLocale() === 'ar' 
                    ? 'بمجرد النقر، سيتم تسجيل طلبك في نظامنا وفتح محادثة مباشرة وآمنة معك على الواتساب لإكمال التجهيز.' 
                    : 'Clicking registers your lead instantly and opens a secure live chat on WhatsApp to finalize setup.' }}
            </p>
        </form>

    </div>
</div>

</div> {{-- Close Alpine Wrapper --}}
@endsection
