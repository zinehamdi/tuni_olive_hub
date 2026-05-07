@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'خدماتنا وأسعارها' : (app()->getLocale() === 'fr' ? 'Nos Services & Tarifs' : 'Our Services & Pricing'))

@section('content')

<div class="min-h-screen" style="background: linear-gradient(135deg, #3d742c 0%, #1a3310 40%, #0f2d0f 70%, #1c2a0e 100%);">

    {{-- ─── HERO ─── --}}
    <div class="relative overflow-hidden pt-24 pb-20 px-4 bg-cover bg-center" style="background-image: url('{{ asset('images/agritech_hero_bg_real.png') }}');">
        <div class="absolute inset-0 pointer-events-none bg-black/50"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-[#0f1f0a]/30 via-[#0f1f0a]/60 to-[#0f1f0a] pointer-events-none"></div>
        <div class="relative max-w-4xl mx-auto text-center">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-[#6A8F3B]/20 border border-[#6A8F3B]/40 text-[#a8d060] text-xs font-semibold mb-6 backdrop-blur-sm">
                <span class="w-2 h-2 rounded-full bg-[#6A8F3B] animate-pulse"></span>
                {{ app()->getLocale() === 'ar' ? 'خدمات رقمية احترافية' : (app()->getLocale() === 'fr' ? 'Services Numériques Pro' : 'Professional Digital Services') }}
            </div>
            <h1 class="text-4xl md:text-5xl font-black text-white mb-4 leading-tight">
                @if(app()->getLocale() === 'ar')
                    خدمات <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#6A8F3B] to-[#C8A356]">التسويق والتطوير</span>
                @elseif(app()->getLocale() === 'fr')
                    Services de <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#6A8F3B] to-[#C8A356]">Marketing & Développement</span>
                @else
                    Marketing & <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#6A8F3B] to-[#C8A356]">Development Services</span>
                @endif
            </h1>
            <p class="text-white/60 text-lg max-w-2xl mx-auto">
                {{ app()->getLocale() === 'ar'
                    ? 'حلول رقمية متكاملة لتعزيز حضورك وتنمية أعمالك في قطاع زيت الزيتون'
                    : (app()->getLocale() === 'fr'
                        ? 'Solutions numériques complètes pour développer votre présence dans le secteur de l\'huile d\'olive'
                        : 'Full-stack digital solutions to grow your presence in the olive oil industry') }}
            </p>
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
                <div class="group relative bg-white/5 border border-white/10 rounded-3xl p-7 hover:bg-white/8 hover:border-[#6A8F3B]/50 hover:scale-[1.02] transition-all duration-300 backdrop-blur-sm flex flex-col overflow-hidden">
                    <!-- Background Decorative Icon -->
                    <div class="absolute -right-6 -top-6 w-40 h-40 opacity-[0.15] pointer-events-none group-hover:scale-110 group-hover:rotate-12 transition-transform duration-700 invert">
                        <img src="{{ asset('icons/eye-browse-svg.svg') }}" class="w-full h-full object-contain">
                    </div>
                    <div class="mb-4">
                        <span class="text-3xl">{{ $service->icon_url }}</span>
                        <h3 class="text-lg font-bold text-white mt-3">
                            {{ app()->getLocale() === 'ar' ? $service->title_ar : (app()->getLocale() === 'fr' ? $service->title_fr : $service->title_en) }}
                        </h3>
                    </div>
                    <div class="mb-6 flex-grow">
                        <div class="text-3xl font-black text-white">{{ number_format($service->price_tnd_weekly, 0) }} <span class="text-base font-normal text-white/50">{{ $service->currency }}<span class="text-xs">/{{ app()->getLocale() === 'ar' ? 'أسبوع' : 'week' }}</span></span></div>
                        <div class="mt-4 p-3 bg-white/5 rounded-xl border border-white/5">
                            <p class="text-sm text-[#a8d060] font-semibold">{{ app()->getLocale() === 'ar' ? 'النتائج المتوقعة:' : 'Expected Results:' }}</p>
                            <p class="text-white/70 text-xs mt-1 leading-relaxed">
                                {{ app()->getLocale() === 'ar' ? $service->results_ar : (app()->getLocale() === 'fr' ? $service->results_fr : $service->results_en) }}
                            </p>
                        </div>
                    </div>
                    
                    <a href="{{ route('services.appointment', $service->id) }}" class="w-full text-center py-3 rounded-xl border border-white/20 bg-white/5 text-white/90 hover:bg-[#6A8F3B] hover:border-[#6A8F3B] transition-all duration-200 text-sm font-semibold flex items-center justify-center gap-2">
                        <span>📅</span>
                        {{ app()->getLocale() === 'ar' ? 'احجز موعد' : 'Book Appointment' }}
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
                    ['icon'=>'🌐','price'=>'1,900','name_ar'=>'موقع ويب','name_en'=>'Website','name_fr'=>'Site Web','desc_ar'=>'تصميم واجهة + CMS','desc_en'=>'UI design + CMS','features_ar'=>['تصميم متجاوب','لوحة تحكم إدارية','تحسين SEO','شهر دعم مجاني'],'features_en'=>['Responsive design','Admin dashboard','SEO optimized','1 month free support']],
                    ['icon'=>'🛒','price'=>'3,500','name_ar'=>'متجر إلكتروني','name_en'=>'E-Commerce Store','name_fr'=>'Boutique en ligne','desc_ar'=>'منصة بيع متكاملة','desc_en'=>'Full sales platform','features_ar'=>['نظام دفع إلكتروني','إدارة المخزون','تقارير المبيعات','3 أشهر دعم'],'features_en'=>['Payment gateway','Inventory management','Sales analytics','3 months support']],
                    ['icon'=>'📱','price'=>'5,900','name_ar'=>'تطبيق موبايل','name_en'=>'Mobile App','name_fr'=>'App Mobile','desc_ar'=>'iOS & Android','desc_en'=>'iOS & Android','features_ar'=>['نظام تسجيل دخول','إشعارات فورية','لوحة إدارة ويب','6 أشهر دعم'],'features_en'=>['Auth system','Push notifications','Web admin panel','6 months support']],
                    ['icon'=>'🤖','price'=>'2,200','name_ar'=>'نظام ERP / CRM','name_en'=>'ERP / CRM System','name_fr'=>'ERP / CRM','desc_ar'=>'إدارة العمليات التجارية','desc_en'=>'Business operations','features_ar'=>['إدارة العملاء','تتبع الطلبات','تقارير مالية','تكامل APIs'],'features_en'=>['Client management','Order tracking','Financial reports','API integrations']],
                ];
                $locale = app()->getLocale();
                @endphp

                @foreach($devPlans as $plan)
                <div class="group bg-white/5 border border-white/10 rounded-2xl p-6 hover:bg-white/8 hover:border-blue-500/30 hover:scale-[1.03] transition-all duration-300 backdrop-blur-sm flex flex-col relative overflow-hidden">
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
                    <h3 class="text-base font-bold text-white">{{ $locale === 'ar' ? $plan['name_ar'] : ($locale === 'fr' ? $plan['name_fr'] : $plan['name_en']) }}</h3>
                    <p class="text-white/40 text-xs mt-0.5 mb-4">{{ $locale === 'ar' ? $plan['desc_ar'] : $plan['desc_en'] }}</p>
                    <div class="text-2xl font-black text-white mb-1">{{ $plan['price'] }} <span class="text-sm font-normal text-white/40">TND</span></div>
                    <p class="text-white/30 text-[11px] mb-5">{{ $locale === 'ar' ? 'سعر المشروع' : 'project price' }}</p>
                    <ul class="space-y-2 mb-6 flex-1 text-xs text-white/60">
                        @foreach(($locale === 'ar' ? $plan['features_ar'] : $plan['features_en']) as $f)
                        <li class="flex items-start gap-1.5">
                            <svg class="w-3.5 h-3.5 text-blue-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            {{ $f }}
                        </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('public.contact') }}" class="block w-full text-center py-2.5 rounded-xl border border-blue-500/30 text-blue-400 hover:bg-blue-500/10 hover:text-blue-300 transition-all duration-200 text-xs font-semibold">
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

@endsection
