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

<div class="relative overflow-hidden bg-gradient-to-r from-[#1a3310] via-[#1f3d14] to-[#1a3310] border-t border-[#6A8F3B]/20 ads-ticker-bar flex items-center" style="height: 32px; max-height: 50px; overflow: hidden;">

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
                    ['icon' => '🫒', 'text_ar' => 'سوق زيت الزيتون التونسي — ربط مباشر بين الفلاحين والمعاصر والمشترين', 'text_en' => 'Tunisian Olive Oil Marketplace — Direct connection between farmers, mills & buyers', 'text_fr' => 'Marketplace Huile d\'Olive — Connexion directe producteurs, moulins et acheteurs', 'url' => route('catalog')],
                    ['icon' => '📦', 'text_ar' => 'خدمات التعبئة والتصدير والعلامة الخاصة للمنتجين التونسيين', 'text_en' => 'Private Label, Bottling & Export Support for Tunisian Olive Oil', 'text_fr' => 'Mise en bouteille, marque privée et export d\'huile d\'olive tunisienne', 'url' => route('services.pricing')],
                    ['icon' => '🚛', 'text_ar' => 'نقل آمن لزيت الزيتون بين الولايات التونسية برمز التحقق PIN', 'text_en' => 'Secure olive oil transport across Tunisian governorates with PIN verification', 'text_fr' => 'Transport sécurisé d\'huile d\'olive entre gouvernorats avec code PIN', 'url' => route('services.index')],
                    ['icon' => '📊', 'text_ar' => 'متابعة يومية لأسعار زيت الزيتون في المعاصر والأسواق التونسية', 'text_en' => 'Daily olive oil prices across Tunisian souks and mills', 'text_fr' => 'Cours et prix quotidiens de l\'huile d\'olive en Tunisie', 'url' => route('prices.index')],
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
                    'carrier' => __('Land and logistics carrier for transporting olives and oil between Tunisian states'),
                    'mill' => __('Olive mill equipped with the latest pressing mechanisms to produce premium oil'),
                    'packer' => __('Integrated canning and packaging unit for olive and olive oil products'),
                    'transiteur' => __('Customs clearance and olive oil export services to all countries of the world'),
                    'comptable' => __('Accounting, financial and administrative consulting for agricultural companies and cooperatives'),
                    'service_bureau' => __('Administrative services office to facilitate transactions and legal files'),
                    'agri_equipment' => __('Selling and providing modern agricultural equipment and machinery for the olive sector'),
                    'agri_materials' => __('Providing high-quality fertilizers, seedlings, and agricultural materials'),
                    'agri_study_office' => __('Agricultural studies and engineering consultations for project development and management'),
                ];

                $providerAds = [];
                foreach ($dbProviders as $p) {
                    $icon = $roleIcons[$p->role] ?? '👥';
                    
                    $desc = null;
                    if (is_array($p->meta_data)) {
                        $desc = \App\Helpers\TextHelper::localizeArabicString($p->meta_data['service_description'] ?? null);
                    } elseif (is_object($p->meta_data)) {
                        $desc = \App\Helpers\TextHelper::localizeArabicString($p->meta_data->service_description ?? null);
                    }
                    
                    if (empty($desc)) {
                        $desc = $defaultDescs[$p->role] ?? __('Registered agricultural and commercial service provider on our platform');
                    }

                    $descShort = \Illuminate\Support\Str::limit($desc, 60);
                    $logoUrl = $p->profile_picture ? Storage::url($p->profile_picture) : null;
                    
                    $providerAds[] = [
                        'icon' => $icon,
                        'logo' => $logoUrl,
                        'name' => \App\Helpers\TextHelper::localizeArabicString($p->name),
                        'desc' => $descShort,
                        'text' => \App\Helpers\TextHelper::localizeArabicString($p->name) . ' (' . $icon . '): ' . $descShort,
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
                    <div class="ads-ticker-text-wrapper flex flex-col {{ $locale === 'ar' ? 'text-right' : 'text-left' }} justify-center leading-none">
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
