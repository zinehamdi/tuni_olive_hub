{{-- ─── Ads Ticker Bar ─── --}}
<div class="relative overflow-hidden bg-gradient-to-r from-[#1a3310] via-[#1f3d14] to-[#1a3310] border-t border-[#6A8F3B]/20" style="min-height:48px;">

    <div class="relative z-10 w-full h-full flex items-center py-1.5">
        <div class="ads-ticker-wrapper">
            <div class="ads-ticker-content">

                @php
                $locale = app()->getLocale();
                $pricingUrl = route('services.pricing');
                $ads = [
                    ['icon'=>'📣','text_ar'=>'خدمات التسويق الرقمي لزيت الزيتون التونسي — اكتشف باقاتنا','text_en'=>'Digital Marketing for Tunisian Olive Oil — Explore our plans','text_fr'=>'Marketing Digital pour l\'huile d\'olive — Découvrez nos offres'],
                    ['icon'=>'🌐','text_ar'=>'هل تريد موقعاً احترافياً؟ نبني لك حضوراً رقمياً قوياً','text_en'=>'Need a professional website? We build powerful digital presences','text_fr'=>'Besoin d\'un site pro? Nous créons votre présence digitale'],
                    ['icon'=>'📱','text_ar'=>'تطوير تطبيقات الموبايل iOS & Android — أسعار تنافسية','text_en'=>'Mobile App Development iOS & Android — Competitive pricing','text_fr'=>'Développement d\'apps mobiles iOS & Android — Prix compétitifs'],
                    ['icon'=>'🚀','text_ar'=>'حملات إعلانية على ميتا وجوجل للمنتجين والمصدرين','text_en'=>'Meta & Google ad campaigns for olive oil producers & exporters','text_fr'=>'Campagnes Meta & Google pour producteurs d\'huile d\'olive'],
                    ['icon'=>'🛒','text_ar'=>'إنشاء متجر إلكتروني متكامل بنظام دفع آمن','text_en'=>'Full e-commerce store with secure payment gateway','text_fr'=>'Boutique en ligne complète avec paiement sécurisé'],
                    ['icon'=>'📊','text_ar'=>'تقارير تحليلية وإدارة محتوى السوشيال ميديا باحترافية','text_en'=>'Analytics reports & professional social media management','text_fr'=>'Rapports analytiques & gestion réseaux sociaux pro'],
                ];
                @endphp

                @foreach(array_merge($ads, $ads) as $ad)
                <a href="{{ $pricingUrl }}" class="ads-ticker-item group">
                    <span class="text-base">{{ $ad['icon'] }}</span>
                    <span class="text-sm md:text-base font-semibold text-[#a8d060] group-hover:text-white transition-colors duration-200 whitespace-nowrap">
                        {{ $locale === 'ar' ? $ad['text_ar'] : ($locale === 'fr' ? $ad['text_fr'] : $ad['text_en']) }}
                    </span>
                </a>
                <span class="ads-ticker-sep">✦</span>
                @endforeach

            </div>
        </div>

        {{-- CTA button pinned right --}}
        <a href="{{ route('services.pricing') }}"
           class="absolute right-0 top-0 bottom-0 flex items-center gap-1.5 px-4 bg-gradient-to-l from-[#6A8F3B] via-[#5a7a2f] to-transparent hover:from-[#5a7a2f] text-white text-xs md:text-sm font-bold transition-all duration-200 z-20 whitespace-nowrap">
            <span class="hidden sm:inline">{{ $locale === 'ar' ? 'عروضنا' : 'Our Plans' }}</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
</div>

<style>
    .ads-ticker-wrapper {
        width: 100%;
        overflow: hidden;
        display: flex;
        align-items: center;
        padding-right: 70px;
    }
    .ads-ticker-content {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        white-space: nowrap;
        animation: ads-scroll-{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }} 50s linear infinite;
        padding-left: 100%;
    }
    .ads-ticker-item {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        flex-shrink: 0;
        text-decoration: none;
    }
    .ads-ticker-sep {
        color: rgba(106,143,59,0.35);
        font-size: 0.6rem;
        flex-shrink: 0;
    }
    @keyframes ads-scroll-ltr {
        0%   { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }
    @keyframes ads-scroll-rtl {
        0%   { transform: translateX(0); }
        100% { transform: translateX(50%); }
    }
    .ads-ticker-wrapper:hover .ads-ticker-content {
        animation-play-state: paused;
    }
</style>
