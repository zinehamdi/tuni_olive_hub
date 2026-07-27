{{-- ─── Ads Ticker Bar ─── --}}
<style>
    .ads-ticker-bar {
        height: 48px;
        min-height: 48px;
        max-height: 80px;
        overflow: hidden;
    }
    .ads-ticker-wrapper {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        overflow: hidden;
        position: relative;
        padding-inline-end: 80px;
    }
    .ads-ticker-content {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        white-space: nowrap;
        flex-shrink: 0;
        width: max-content;
        min-width: max-content;
        animation: ads-scroll-marquee 110s linear infinite;
        will-change: transform;
    }
    [dir="rtl"] .ads-ticker-content {
        animation: ads-scroll-marquee-rtl 110s linear infinite;
    }
    .ads-ticker-wrapper:hover .ads-ticker-content {
        animation-play-state: paused;
    }
    .ads-ticker-logo {
        width: 26px;
        height: 26px;
        flex-shrink: 0;
    }
    .ads-ticker-item {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        flex-shrink: 0;
        text-decoration: none;
        background-color: #C6E1A5;
        color: #000000 !important;
        border-radius: 9999px;
        padding: 0.3rem 0.8rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.12);
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    .ads-ticker-item:hover {
        background-color: #b5d691;
        transform: translateY(-1px);
    }
    .ads-ticker-text-wrapper {
        white-space: nowrap;
        max-width: 240px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .ads-ticker-title {
        font-size: 0.68rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .ads-ticker-desc {
        font-size: 0.6rem;
        margin-top: 1px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        opacity: 0.85;
    }
    .ads-ticker-icon {
        font-size: 1rem;
    }
    .ads-ticker-sep {
        font-size: 0.6rem;
        color: rgba(106,143,59,0.35);
        flex-shrink: 0;
    }
    .ads-ticker-cta {
        padding-left: 0.75rem;
        padding-right: 0.75rem;
        font-size: 0.7rem;
    }

    @media (min-width: 768px) {
        .ads-ticker-bar {
            height: 72px;
            min-height: 72px;
            max-height: 80px;
        }
        .ads-ticker-logo {
            width: 42px;
            height: 42px;
        }
        .ads-ticker-item {
            gap: 0.6rem;
            padding: 0.45rem 1.25rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.15);
        }
        .ads-ticker-text-wrapper {
            max-width: 380px;
        }
        .ads-ticker-title {
            font-size: 0.95rem;
            line-height: 1.1;
        }
        .ads-ticker-desc {
            font-size: 0.78rem;
            line-height: 1.2;
        }
        .ads-ticker-icon {
            font-size: 1.5rem;
        }
        .ads-ticker-sep {
            font-size: 0.85rem;
        }
        .ads-ticker-cta {
            padding-left: 1.25rem;
            padding-right: 1.25rem;
            font-size: 0.875rem;
        }
    }

    @keyframes ads-scroll-marquee {
        0% { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }
    @keyframes ads-scroll-marquee-rtl {
        0% { transform: translateX(0); }
        100% { transform: translateX(50%); }
    }
</style>

<div class="relative overflow-hidden bg-gradient-to-r from-[#1a3310] via-[#1f3d14] to-[#1a3310] border-t border-[#6A8F3B]/20 ads-ticker-bar flex items-center" style="height: 48px; max-height: 80px; overflow: hidden;">

    <div class="relative z-10 w-full h-full flex items-center py-1">
        <div class="ads-ticker-wrapper">
            <div class="ads-ticker-content">

                @php
                $locale = app()->getLocale();
                $pricingUrl = route('services.pricing');

                // Fetch active registered service providers from Cache to avoid slow database queries
                $cachedProviders = \Illuminate\Support\Facades\Cache::remember('ticker_b2b_providers', 600, function() {
                    return \App\Models\User::whereIn('role', [
                            'carrier', 'mill', 'packer', 'transiteur', 'comptable', 'service_bureau', 'agri_equipment', 'agri_materials', 'agri_study_office'
                        ])
                        ->latest()
                        ->get();
                });

                // Shuffle in memory and take 30
                $dbProviders = collect($cachedProviders)->shuffle()->take(30);

                $platformAds = [
                    ['icon' => '🌐', 'text_ar' => 'هل تريد موقعاً احترافياً؟ نبني لك حضوراً رقمياً قوياً', 'text_en' => 'Need a professional website? We build powerful digital presences', 'text_fr' => 'Besoin d\'un site pro? Nous créons votre présence digitale', 'url' => $pricingUrl],
                    ['icon' => '📱', 'text_ar' => 'تطوير تطبيقات الموبايل iOS & Android — أسعار تنافسية', 'text_en' => 'Mobile App Development iOS & Android — Competitive pricing', 'text_fr' => 'Développement d\'apps mobiles iOS & Android — Prix compétitifs', 'url' => $pricingUrl],
                    ['icon' => '🚀', 'text_ar' => 'حملات إعلانية على ميتا وجوجل للمنتجين والمصدرين', 'text_en' => 'Meta & Google ad campaigns for olive oil producers & exporters', 'text_fr' => 'Campagnes Meta & Google pour producteurs d\'huile d\'olive', 'url' => $pricingUrl],
                ];

                $roleIcons = [
                    'carrier' => '🚛',
                    'mill' => '🏢',
                    'packer' => '📦',
                    'transiteur' => '🛃',
                    'comptable' => '📊',
                    'service_bureau' => '📝',
                    'agri_equipment' => '🚜',
                    'agri_materials' => '🌱',
                    'agri_study_office' => '📐',
                ];

                $defaultDescs = [
                    'carrier' => 'ناقل بري ولوجستي لنقل الزيتون والزيت بين الولايات التونسية',
                    'mill' => 'معصرة زيتون مجهزة بأحدث آليات العصر لإنتاج زيت رفيع',
                    'packer' => 'وحدة تعليب وتغليف متكاملة لمنتجات زيت الزيتون والزيتون',
                    'transiteur' => 'خدمات تخليص جمركي وتصدير زيت الزيتون لكافة دول العالم',
                    'comptable' => 'محاسبة واستشارات مالية وإدارية للشركات والتعاونيات الفلاحية',
                    'service_bureau' => 'مكتب خدمات إدارية وتسهيل المعاملات والملفات القانونية',
                    'agri_equipment' => 'بيع وتوفير المعدات والآلات الفلاحية الحديثة لقطاع الزيتون',
                    'agri_materials' => 'توفير الأسمدة والمشاتل والمواد الفلاحية ذات الجودة العالية',
                    'agri_study_office' => 'دراسات فلاحية واستشارات هندسية لتطوير وإدارة المشاريع',
                ];

                $providerAds = [];
                foreach ($dbProviders as $p) {
                    $icon = $roleIcons[$p->role] ?? '👥';
                    
                    $desc = null;
                    if (is_array($p->meta_data)) {
                        $desc = $p->meta_data['service_description'] ?? null;
                    } elseif (is_object($p->meta_data)) {
                        $desc = $p->meta_data->service_description ?? null;
                    }
                    
                    if (empty($desc)) {
                        $desc = $defaultDescs[$p->role] ?? 'مزود خدمات فلاحية وتجارية مسجل في منصتنا';
                    }

                    $descShort = \Illuminate\Support\Str::limit($desc, 60);
                    $logoUrl = $p->profile_picture ? Storage::url($p->profile_picture) : null;
                    
                    $providerAds[] = [
                        'icon' => $icon,
                        'logo' => $logoUrl,
                        'name' => $p->name,
                        'desc' => $descShort,
                        'text' => $p->name . ' (' . $icon . '): ' . $descShort,
                        'url' => route('services.index') . '?provider=' . $p->id . '#directory'
                    ];
                }

                $mergedAds = [];
                $max = max(count($platformAds), count($providerAds));
                for ($i = 0; $i < $max; $i++) {
                    if (isset($platformAds[$i])) {
                        $ad = $platformAds[$i];
                        $text = $locale === 'ar' ? $ad['text_ar'] : ($locale === 'fr' ? $ad['text_fr'] : $ad['text_en']);
                        $mergedAds[] = [
                            'icon' => $ad['icon'],
                            'logo' => asset('images/zintoop-logo.png'),
                            'name' => $locale === 'ar' ? 'منصة الزين' : 'ZinToop',
                            'desc' => $text,
                            'text' => $text,
                            'url' => $ad['url']
                        ];
                    }
                    if (isset($providerAds[$i])) {
                        $mergedAds[] = $providerAds[$i];
                    }
                }

                $displayAds = array_merge($mergedAds, $mergedAds);
                if (count($displayAds) < 8) {
                    $displayAds = array_merge($displayAds, $displayAds);
                }
                @endphp

                @foreach($displayAds as $ad)
                <a href="{{ $ad['url'] }}" class="ads-ticker-item group">
                    @if(!empty($ad['logo']))
                        <div class="relative ads-ticker-logo border border-black/10 rounded-full overflow-hidden shrink-0 flex items-center justify-center bg-white" style="width: 26px; height: 26px;">
                            <img src="{{ $ad['logo'] }}" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="hidden w-full h-full rounded-full bg-gradient-to-br from-emerald-500 to-teal-500 items-center justify-center text-white text-[10px] font-bold">
                                {{ strtoupper(substr($ad['name'] ?? '', 0, 1)) }}
                            </div>
                        </div>
                    @else
                        <div class="rounded-full bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center text-white text-[10px] font-bold shrink-0" style="width: 26px; height: 26px;">
                            {{ strtoupper(substr($ad['name'] ?? '', 0, 1)) }}
                        </div>
                    @endif
                    <div class="ads-ticker-text-wrapper flex flex-col text-right justify-center leading-none">
                        <span class="ads-ticker-title font-black text-black leading-tight">
                            {{ $ad['name'] ?? '' }}
                        </span>
                        <span class="ads-ticker-desc text-black/80 font-medium line-clamp-2">
                            {{ $ad['desc'] ?? $ad['text'] }}
                        </span>
                    </div>
                </a>
                <span class="ads-ticker-sep">✦</span>
                @endforeach

            </div>
        </div>

        {{-- CTA button pinned right --}}
        <a href="{{ route('services.pricing') }}"
           class="absolute right-0 top-0 bottom-0 flex items-center gap-1.5 bg-gradient-to-l from-[#6A8F3B] via-[#5a7a2f] to-transparent hover:from-[#5a7a2f] text-white font-bold transition-all duration-200 z-20 whitespace-nowrap ads-ticker-cta">
            <span class="hidden sm:inline">{{ $locale === 'ar' ? 'عروضنا' : 'Our Plans' }}</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
</div>
