@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'مركز الخدمات ومزودي الخدمات' : (app()->getLocale() === 'fr' ? 'Hub des Services' : 'Services Hub'))

@section('content')

@php
    $servicesData = \App\Models\MarketingService::all()->mapWithKeys(function($item) {
        return [$item->id => [
            'id' => $item->id,
            'name_ar' => $item->title_ar,
            'name_en' => $item->title_en,
            'name_fr' => $item->title_fr,
            'price' => number_format($item->price_tnd_weekly, 0),
            'icon' => $item->icon_url
        ]];
    })->all();

    $roleTitlesGlobal = [
        'carrier' => ['ar' => 'خدمات النقل اللوجستي والبري', 'fr' => 'Services de Transport Logistique', 'en' => 'Logistic & Land Transport Services'],
        'mill' => ['ar' => 'خدمات معصرة زيتون', 'fr' => 'Services de Moulin / Pressoir', 'en' => 'Olive Mill Services'],
        'packer' => ['ar' => 'خدمات تعبئة وتغليف وتعبئة', 'fr' => 'Services d\'Embouteillage & Emballage', 'en' => 'Packaging & Bottling Services'],
        'transiteur' => ['ar' => 'خدمات مخلص جمركي وتصدير', 'fr' => 'Services de Transiteur', 'en' => 'Customs Broker & Export Services'],
        'comptable' => ['ar' => 'خدمات محاسبة فلاحية ومالية', 'fr' => 'Services de Comptable', 'en' => 'Accounting & Financial Services'],
        'service_bureau' => ['ar' => 'خدمات مكتب إداري وتسهيلات', 'fr' => 'Services de Bureau de services', 'en' => 'Administrative Service Bureau'],
        'agri_equipment' => ['ar' => 'شركات معدات وآلات فلاحية', 'fr' => 'Matériel Agricole', 'en' => 'Agri Equipment & Machinery'],
        'agri_materials' => ['ar' => 'شركات أسمدة ومواد فلاحية', 'fr' => 'Matières & Engrais Agricoles', 'en' => 'Agri Materials & Fertilizers'],
        'agri_study_office' => ['ar' => 'مكتب دراسات واستشارات فلاحية', 'fr' => 'Bureau d\'études agricoles', 'en' => 'Agri Study Office & Consulting'],
    ];

    $roleLabelsGlobal = [
        'carrier' => ['ar' => 'ناقل بري وبحري', 'fr' => 'Transporteur', 'en' => 'Carrier', 'icon' => '🚛'],
        'mill' => ['ar' => 'معصرة زيتون', 'fr' => 'Pressoir / Moulin', 'en' => 'Olive Mill', 'icon' => '🏢'],
        'packer' => ['ar' => 'تعبئة وتغليف', 'fr' => 'Embouteillage & Emballage', 'en' => 'Packaging & Bottling', 'icon' => '📦'],
        'transiteur' => ['ar' => 'مخلص جمركي', 'fr' => 'Transiteur', 'en' => 'Customs Broker', 'icon' => '🛃'],
        'comptable' => ['ar' => 'محاسب', 'fr' => 'Comptable', 'en' => 'Accountant', 'icon' => '📊'],
        'service_bureau' => ['ar' => 'مكتب خدمات إدارية', 'fr' => 'Bureau de services', 'en' => 'Service Bureau', 'icon' => '📝'],
        'agri_equipment' => ['ar' => 'معدات وآليات فلاحية', 'fr' => 'Matériel Agricole', 'en' => 'Agri Equipment', 'icon' => '🚜'],
        'agri_materials' => ['ar' => 'مواد فلاحية وأسمدة', 'fr' => 'Matières & Engrais Agricoles', 'en' => 'Agri Materials & Fertilizers', 'icon' => '🌱'],
        'agri_study_office' => ['ar' => 'مكتب دراسات فلاحية', 'fr' => 'Bureau d\'études agricoles', 'en' => 'Agri Study Office', 'icon' => '📐'],
    ];

    $providerTypeLabelsGlobal = [
        'freelancer' => ['ar' => 'مستقل', 'fr' => 'Indépendant', 'en' => 'Freelancer', 'color' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
        'bureau' => ['ar' => 'مكتب', 'fr' => 'Bureau / Cabinet', 'en' => 'Office / Agency', 'color' => 'bg-sky-50 text-sky-700 border-sky-200'],
        'societe' => ['ar' => 'شركة', 'fr' => 'Société / Entreprise', 'en' => 'Company', 'color' => 'bg-purple-50 text-purple-700 border-purple-200'],
    ];

    $defaultDescsGlobal = [
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

    $providersJsonData = collect($providers)->mapWithKeys(function($p) use ($roleLabelsGlobal, $providerTypeLabelsGlobal, $defaultDescsGlobal) {
        $providerType = $p->meta_data['provider_type'] ?? '';
        $roleData = $roleLabelsGlobal[$p->role] ?? ['ar' => 'مزود خدمة', 'fr' => 'Prestataire', 'en' => 'Service Provider', 'icon' => '👥'];
        $typeData = $providerTypeLabelsGlobal[$providerType] ?? null;
        $validPhotos = array_values(array_filter($p->cover_photos ?? [], fn($photo) => is_string($photo) && !empty($photo)));
        $provServices = $p->meta_data['services'] ?? [];
        
        $desc = null;
        if (is_array($p->meta_data)) {
            $desc = $p->meta_data['service_description'] ?? null;
        } elseif (is_object($p->meta_data)) {
            $desc = $p->meta_data->service_description ?? null;
        }
        
        if (empty($desc)) {
            $desc = $defaultDescsGlobal[$p->role] ?? 'مزود خدمات فلاحية وتجارية مسجل في منصتنا';
        }

        return [$p->id => [
            'name' => $p->name,
            'role' => $roleData[app()->getLocale()] ?? $roleData['ar'],
            'icon' => $roleData['icon'],
            'type' => $typeData[app()->getLocale()] ?? $typeData['ar'] ?? '',
            'typeColor' => $typeData['color'] ?? '',
            'governorate' => $p->addresses->first()->governorate ?? 'تونس',
            'profile_picture' => $p->profile_picture ? Storage::url($p->profile_picture) : '',
            'description' => str_replace(["\r", "\n"], ' ', $desc),
            'services' => $provServices,
            'price_type' => $p->meta_data['price_type'] ?? 'quote',
            'price' => $p->meta_data['service_price'] ?? null,
            'phone' => $p->phone,
            'url' => route('user.profile', $p->id),
            'cover' => count($validPhotos) > 0 ? Storage::url($validPhotos[0]) : ''
        ]];
    })->all();
@endphp

<div x-data="{ 
    modalOpen: false, 
    activeServiceId: '', 
    activeServiceName: '', 
    activeServicePrice: '', 
    activeServiceIcon: '📦',
    activeProvider: null,
    showProviderModal: false,
    showRegisterGateModal: false,
    isGuest: {{ auth()->guest() ? 'true' : 'false' }},
    providers: {{ json_encode($providersJsonData) }},
    handleFullProfile(url) {
        if (this.isGuest) {
            this.showRegisterGateModal = true;
        } else {
            window.location.href = url;
        }
    },
    handleWhatsApp(phone) {
        if (this.isGuest) {
            this.showRegisterGateModal = true;
        } else {
            window.open('https://wa.me/' + phone.replace(/[^0-9]/g, ''), '_blank');
        }
    },
    openModal(id, name, price, icon) {
        if (this.isGuest) {
            this.showRegisterGateModal = true;
            return;
        }
        this.activeServiceId = id;
        this.activeServiceName = name;
        this.activeServicePrice = price;
        this.activeServiceIcon = icon;
        this.modalOpen = true;
    },
    init() {
        const services = {{ json_encode($servicesData) }};
        
        const urlParams = new URLSearchParams(window.location.search);
        const serviceId = urlParams.get('service');
        if (serviceId && services[serviceId]) {
            const s = services[serviceId];
            const locale = '{{ app()->getLocale() }}';
            const name = locale === 'ar' ? s.name_ar : (locale === 'fr' ? s.name_fr : s.name_en);
            setTimeout(() => {
                this.openModal(s.id, name, s.price, s.icon);
            }, 100);
        }

        const providerId = urlParams.get('provider');
        if (providerId && this.providers[providerId]) {
            setTimeout(() => {
                if (this.isGuest) {
                    this.showRegisterGateModal = true;
                } else {
                    this.activeProvider = this.providers[providerId];
                    this.showProviderModal = true;
                }
                
                const el = document.getElementById('directory');
                if (el) {
                    el.scrollIntoView({ behavior: 'smooth' });
                }
            }, 300);
        }
    }
}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

        {{-- ─── 1. World-Class Glassmorphism Hero Banner ─── --}}
        <div class="relative rounded-3xl overflow-hidden bg-gradient-to-r from-[#0C1A0F] via-[#142E18] to-[#0C1A0F] border border-[#C8A356]/30 shadow-2xl p-6 sm:p-10 text-right">
            <!-- Background Ambient Lighting -->
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(200,163,86,0.2)_0%,transparent_60%)] pointer-events-none"></div>
            <div class="absolute -bottom-20 -left-20 w-80 h-80 rounded-full bg-[#6A8F3B]/20 blur-3xl pointer-events-none"></div>

            <div class="relative z-10">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-[#C8A356]/20 text-[#F5E5C0] border border-[#C8A356]/40 backdrop-blur-md mb-4">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse ml-1.5 mr-1.5"></span>
                    {{ app()->getLocale() === 'ar' ? 'مركز الخدمات المتكامل للقطاع الفلاحي والتصدير' : 'Unified Services & Providers Hub' }}
                </span>
                <h1 class="text-2xl sm:text-4xl font-black text-white leading-tight tracking-tight">
                    {{ app()->getLocale() === 'ar' ? 'مركز الخدمات ومزودي الخدمات' : 'Services & Providers Hub' }}
                </h1>
                <p class="text-xs sm:text-sm text-gray-300 mt-2 max-w-3xl leading-relaxed">
                    {{ app()->getLocale() === 'ar' 
                        ? 'اكتشف خدماتنا الرقمية للتسويق والتصدير، وتصفح دليل مزودي الخدمات المحليين من ناقلين، معاصر، محاسبين، ومعدات فلاحية.' 
                        : 'Discover our digital marketing & export services, and explore the directory of local service providers.' }}
                </p>
            </div>
        </div>

        {{-- ─── REGISTRATION CALL-TO-ACTION BANNER ─── --}}
        <div class="relative z-10">
                <div class="relative overflow-hidden rounded-3xl p-[1px] bg-gradient-to-r from-[#6A8F3B] via-[#C8A356] to-[#6A8F3B] shadow-2xl">
                    <div class="bg-gradient-to-br from-[#0c180d] to-[#122413] rounded-3xl p-8 relative flex flex-col md:flex-row items-center justify-between gap-6">
                        <div class="absolute right-0 top-0 w-80 h-80 bg-[#6A8F3B]/10 rounded-full blur-3xl pointer-events-none"></div>
                        <div class="relative z-10 text-right">
                            <h3 class="text-2xl font-black text-white mb-2">
                                {{ app()->getLocale() === 'ar' ? 'هل أنت مزود خدمات فلاحية أو تجارية؟' : 'Are you an agricultural or commercial service provider?' }}
                            </h3>
                            <p class="text-gray-300 text-sm max-w-xl">
                                {{ app()->getLocale() === 'ar' 
                                    ? 'سجل معنا الآن كـ ناقل، معصرة، وحدة تعبئة، محاسب، أو شركة معدات، واعرض خدماتك أمام آلاف الفلاحين والمنتجين مجاناً!' 
                                    : 'Register now as a carrier, mill, packer, accountant, or equipment provider and display your services to thousands of farmers.' }}
                            </p>
                        </div>
                        <a href="{{ route('services.register') }}" class="relative z-10 px-8 py-4 bg-gradient-to-r from-[#6A8F3B] to-[#5a7a2f] text-white font-black rounded-2xl shadow-xl hover:scale-105 transition duration-300 flex items-center gap-2">
                            <span>📝</span>
                            {{ app()->getLocale() === 'ar' ? 'سجل كمزود خدمة الآن' : 'Register as a Provider Now' }}
                        </a>
                    </div>
                </div>
            </div>

        {{-- ─── SECTION 2: EXTERNAL SERVICE PROVIDERS DIRECTORY ─── --}}
        <div id="directory" class="relative z-10 space-y-6">
                <div class="flex items-center gap-3 mb-8">
                    <span class="text-3xl">👥</span>
                <div>
                    <h2 class="text-2xl font-black text-[#1B2A1B]">
                        {{ app()->getLocale() === 'ar' ? 'دليل مزودي الخدمات المحليين' : 'Local Service Providers Directory' }}
                    </h2>
                    <p class="text-gray-500 text-xs mt-1">{{ app()->getLocale() === 'ar' ? 'تواصل مباشرة مع مقدمي الخدمات الفلاحية واللوجستية في تونس' : 'Contact agricultural & logistics providers in Tunisia directly' }}</p>
                </div>
                </div>

            {{-- Filter Bar --}}
            <div class="bg-white border border-gray-150 rounded-2xl p-6 shadow-sm">
                    <form method="GET" action="{{ route('services.index') }}#directory" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                        <!-- Search input -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">{{ app()->getLocale() === 'ar' ? 'بحث باسم المزود' : 'Search Provider' }}</label>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ app()->getLocale() === 'ar' ? 'مثال: شركة النقل السريع...' : 'e.g. Transport Co...' }}" class="w-full bg-gray-50 border border-gray-250 rounded-xl px-4 py-2.5 text-gray-800 text-sm focus:outline-none focus:border-[#6A8F3B]">
                        </div>

                        <!-- Provider Type -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">{{ app()->getLocale() === 'ar' ? 'نوع الخدمة' : 'Service Type' }}</label>
                            <select name="type" class="w-full bg-gray-50 border border-gray-250 rounded-xl px-4 py-2.5 text-gray-800 text-sm focus:outline-none focus:border-[#6A8F3B] appearance-none bg-[url('data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2020%2020%22%20fill%3D%22%23ffffff%22%3E%3Cpath%20fill-rule%3D%22evenodd%22%20d%3D%22M5.293%207.293a1%201%200%20011.414%200L10%2010.586l3.293-3.293a1%201%200%20111.414%201.414l-4%204a1%201%200%2001-1.414%200l-4-4a1%201%200%20010-1.414z%22%20clip-rule%3D%22evenodd%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem_1.25rem] bg-no-repeat rtl:bg-[left_1rem_center] ltr:bg-[right_1rem_center] rtl:pl-10 ltr:pr-10">
                                <option value="" >{{ app()->getLocale() === 'ar' ? '-- جميع الخدمات --' : '-- All Services --' }}</option>
                                <option value="carrier"  {{ request('type') === 'carrier' ? 'selected' : '' }}>🚛 {{ app()->getLocale() === 'ar' ? 'ناقل بري وبحري' : 'Carrier' }}</option>
                                <option value="mill"  {{ request('type') === 'mill' ? 'selected' : '' }}>🏢 {{ app()->getLocale() === 'ar' ? 'معصرة زيتون' : 'Olive Mill' }}</option>
                                <option value="packer"  {{ request('type') === 'packer' ? 'selected' : '' }}>📦 {{ app()->getLocale() === 'ar' ? 'وحدة تعبئة وتغليف' : 'Packer' }}</option>
                                <option value="transiteur"  {{ request('type') === 'transiteur' ? 'selected' : '' }}>🛃 {{ app()->getLocale() === 'ar' ? 'مخلص جمركي' : 'Customs Broker' }}</option>
                                <option value="comptable"  {{ request('type') === 'comptable' ? 'selected' : '' }}>📊 {{ app()->getLocale() === 'ar' ? 'محاسب' : 'Accountant' }}</option>
                                <option value="service_bureau"  {{ request('type') === 'service_bureau' ? 'selected' : '' }}>📝 {{ app()->getLocale() === 'ar' ? 'مكتب خدمات إدارية' : 'Service Bureau' }}</option>
                                <option value="agri_equipment"  {{ request('type') === 'agri_equipment' ? 'selected' : '' }}>🚜 {{ app()->getLocale() === 'ar' ? 'معدات وآليات فلاحية' : 'Agri-Equipment' }}</option>
                                <option value="agri_materials"  {{ request('type') === 'agri_materials' ? 'selected' : '' }}>🌱 {{ app()->getLocale() === 'ar' ? 'مواد فلاحية وأسمدة' : 'Agri-Materials' }}</option>
                                <option value="agri_study_office"  {{ request('type') === 'agri_study_office' ? 'selected' : '' }}>📐 {{ app()->getLocale() === 'ar' ? 'مكتب دراسات فلاحية' : 'Agri-Study Office' }}</option>
                            </select>
                        </div>

                        <!-- Governorate -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">{{ app()->getLocale() === 'ar' ? 'الولاية' : 'Governorate' }}</label>
                            <select name="governorate" class="w-full bg-gray-50 border border-gray-250 rounded-xl px-4 py-2.5 text-gray-800 text-sm focus:outline-none focus:border-[#6A8F3B] appearance-none bg-[url('data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2020%2020%22%20fill%3D%22%23ffffff%22%3E%3Cpath%20fill-rule%3D%22evenodd%22%20d%3D%22M5.293%207.293a1%201%200%20011.414%200L10%2010.586l3.293-3.293a1%201%200%20111.414%201.414l-4%204a1%201%200%2001-1.414%200l-4-4a1%201%200%20010-1.414z%22%20clip-rule%3D%22evenodd%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem_1.25rem] bg-no-repeat rtl:bg-[left_1rem_center] ltr:bg-[right_1rem_center] rtl:pl-10 ltr:pr-10">
                                <option value="" >{{ app()->getLocale() === 'ar' ? '-- جميع الولايات --' : '-- All Governorates --' }}</option>
                                @foreach(config('governorates', []) as $gov)
                                    <option value="{{ $gov }}"  {{ request('governorate') === $gov ? 'selected' : '' }}>{{ $gov }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 py-3 px-4 bg-[#6A8F3B] hover:bg-[#5a7a2f] text-white font-bold rounded-xl shadow-lg transition duration-200 text-sm flex items-center justify-center gap-1 cursor-pointer">
                                🔍 {{ app()->getLocale() === 'ar' ? 'تصفية' : 'Filter' }}
                            </button>
                        <a href="{{ route('services.index') }}#directory" class="py-3 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition duration-200 text-sm flex items-center justify-center cursor-pointer">
                            🔄
                        </a>
                        </div>
                    </form>
                </div>

                {{-- Providers Cards Grid --}}
                {{-- Build Normalized Cards Array --}}
                @php
                    $allCards = [];
                    foreach ($providers as $provider) {
                        $services = $provider->meta_data['services'] ?? [];
                        if (!empty($services)) {
                            foreach ($services as $srvIndex => $srv) {
                                $allCards[] = [
                                    'provider' => $provider,
                                    'is_default' => false,
                                    'title' => $srv['title'] ?? '',
                                    'price' => $srv['price'] ?? null,
                                    'price_type' => $srv['price_type'] ?? 'fixed',
                                    'description' => $srv['description'] ?? '',
                                    'image' => $srv['image'] ?? null,
                                    'id' => $provider->id . '-' . $srvIndex
                                ];
                            }
                        } else {
                            $allCards[] = [
                                'provider' => $provider,
                                'is_default' => true,
                                'title' => $roleTitlesGlobal[$provider->role][app()->getLocale()] ?? ($roleTitlesGlobal[$provider->role]['ar'] ?? 'خدمات عامة متكاملة'),
                                'price' => $provider->meta_data['service_price'] ?? null,
                                'price_type' => $provider->meta_data['price_type'] ?? 'quote',
                                'description' => $provider->meta_data['service_description'] ?? '',
                                'image' => null,
                                'id' => $provider->id . '-default'
                            ];
                        }
                    }
                @endphp

                {{-- Providers Cards Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @forelse($allCards as $card)
                    @php
                        $provider = $card['provider'];
                        $providerType = $provider->meta_data['provider_type'] ?? '';
                        
                        $roleLabels = [
                            'carrier' => ['ar' => 'ناقل بري وبحري', 'fr' => 'Transporteur', 'en' => 'Carrier', 'icon' => '🚛'],
                            'mill' => ['ar' => 'معصرة زيتون', 'fr' => 'Pressoir / Moulin', 'en' => 'Olive Mill', 'icon' => '🏢'],
                            'packer' => ['ar' => 'تعبئة وتغليف', 'fr' => 'Embouteillage & Emballage', 'en' => 'Packaging & Bottling', 'icon' => '📦'],
                            'transiteur' => ['ar' => 'مخلص جمركي', 'fr' => 'Transiteur', 'en' => 'Customs Broker', 'icon' => '🛃'],
                            'comptable' => ['ar' => 'محاسب', 'fr' => 'Comptable', 'en' => 'Accountant', 'icon' => '📊'],
                            'service_bureau' => ['ar' => 'مكتب خدمات إدارية', 'fr' => 'Bureau de services', 'en' => 'Service Bureau', 'icon' => '📝'],
                            'agri_equipment' => ['ar' => 'معدات وآليات فلاحية', 'fr' => 'Matériel Agricole', 'en' => 'Agri Equipment', 'icon' => '🚜'],
                            'agri_materials' => ['ar' => 'مواد فلاحية وأسمدة', 'fr' => 'Matières & Engrais Agricoles', 'en' => 'Agri Materials & Fertilizers', 'icon' => '🌱'],
                            'agri_study_office' => ['ar' => 'مكتب دراسات فلاحية', 'fr' => 'Bureau d\'études agricoles', 'en' => 'Agri Study Office', 'icon' => '📐'],
                        ];
                        $roleData = $roleLabels[$provider->role] ?? ['ar' => 'مزود خدمة', 'fr' => 'Prestataire', 'en' => 'Service Provider', 'icon' => '👥'];
                        
                        $providerTypeLabels = [
                            'freelancer' => ['ar' => 'مستقل', 'fr' => 'Indépendant', 'en' => 'Freelancer', 'color' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                            'bureau' => ['ar' => 'مكتب', 'fr' => 'Bureau / Cabinet', 'en' => 'Office / Agency', 'color' => 'bg-sky-50 text-sky-700 border-sky-200'],
                            'societe' => ['ar' => 'شركة', 'fr' => 'Société / Entreprise', 'en' => 'Company', 'color' => 'bg-purple-50 text-purple-700 border-purple-200'],
                        ];
                        $typeData = $providerTypeLabels[$providerType] ?? null;
                        $validPhotos = array_values(array_filter($provider->cover_photos ?? [], fn($p) => is_string($p) && !empty($p)));
                        $provServices = $provider->meta_data['services'] ?? [];
                    @endphp

                    <div class="group bg-white border border-gray-150 hover:border-[#6A8F3B]/30 shadow-md rounded-2xl hover:-translate-y-1 hover:shadow-xl transition-all duration-300 flex flex-col overflow-hidden text-right cursor-pointer relative"
                        @click="if (isGuest) { showRegisterGateModal = true; } else { activeProvider = providers[{{ $provider->id }}] || {}; showProviderModal = true; }">
                        
                        <!-- Premium Dark Green Top Header Banner -->
                        <div class="bg-gradient-to-r from-[#0C1A0F] to-[#142E18] text-white px-4 py-3 flex items-center justify-between text-xs font-semibold relative overflow-hidden shrink-0">
                            <!-- Subtle Ambient Glow -->
                            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(200,163,86,0.15),transparent_50%)]"></div>
                            
                            <!-- Right Side: Governorate & Flag -->
                            <div class="relative z-10 flex items-center gap-1">
                                <span class="text-white/90 font-black">{{ $provider->addresses->first()->governorate ?? 'تونس' }}</span>
                                <span>🇹🇳</span>
                            </div>
                            
                            <!-- Left Side: Role Icon & Provider Name -->
                            <div class="relative z-10 flex items-center gap-1.5 min-w-0 max-w-[60%] justify-end">
                                <span class="truncate text-white/90 font-bold">{{ $provider->name }}</span>
                                <span class="shrink-0 text-sm">{{ $roleData['icon'] }}</span>
                            </div>
                        </div>

                        <!-- Card Content Body -->
                        <div class="p-4 flex flex-col flex-grow space-y-3">
                            <!-- Service Image -->
                            <div class="w-full h-28 rounded-xl overflow-hidden relative bg-gray-50 border border-gray-150 flex items-center justify-center shrink-0">
                                 @if(!empty($card['image']))
                                    <img src="{{ asset($card['image']) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                @elseif($provider->profile_picture)
                                    <div class="relative w-12 h-12 rounded-full overflow-hidden shrink-0 flex items-center justify-center">
                                        <img src="{{ Storage::url($provider->profile_picture) }}" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="hidden w-full h-full rounded-full bg-gradient-to-br from-emerald-500 to-teal-500 items-center justify-center text-white text-base font-bold">
                                            {{ strtoupper(substr($provider->name, 0, 1)) }}
                                        </div>
                                    </div>
                                @else
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center text-white text-base font-bold">
                                        {{ strtoupper(substr($provider->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>

                            <!-- Title & Provider Type Badge -->
                            <div class="flex items-start justify-between gap-2">
                                @if($typeData)
                                    <span class="px-2 py-0.5 rounded-full border text-[9px] font-bold shrink-0 self-start {{ $typeData['color'] }}">
                                        {{ $typeData[app()->getLocale()] ?? $typeData['ar'] }}
                                    </span>
                                @endif
                                <h4 class="font-bold text-gray-900 text-xs md:text-sm leading-tight text-right line-clamp-1 flex-grow">{{ $card['title'] }}</h4>
                            </div>

                            <!-- Description -->
                            <p class="text-gray-500 text-[11px] leading-relaxed line-clamp-2 text-right flex-grow">
                                {{ $card['description'] ?: 'لا يوجد وصف تفصيلي متوفر لهذه الخدمة.' }}
                            </p>

                            <!-- Price Display Container (Matches Prices page style) -->
                            <div class="bg-gray-50 border border-gray-150 rounded-xl p-3 text-center shrink-0">
                                <span class="inline-flex items-center gap-1.5 text-[10px] font-bold text-gray-500 mb-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#6A8F3B]"></span>
                                    {{ app()->getLocale() === 'ar' ? 'السعر التقديري' : (app()->getLocale() === 'fr' ? 'Prix estimé' : 'Estimated Price') }}
                                </span>
                                <div class="text-base font-black text-[#C8A356] tracking-tight">
                                    @if(($card['price_type'] ?? 'fixed') === 'fixed' && !empty($card['price']))
                                        {{ number_format($card['price'], 0) }} <span class="text-[10px] font-normal text-gray-500">TND</span>
                                    @else
                                        <span class="text-xs font-bold text-gray-600">{{ app()->getLocale() === 'ar' ? 'السعر حسب الطلب' : (app()->getLocale() === 'fr' ? 'Sur demande' : 'Upon Request') }}</span>
                                    @endif
                                </div>
                            </div>

                            <!-- CTA Button to reveal big card details -->
                            <button type="button" 
                                    @click.stop="if (isGuest) { showRegisterGateModal = true; } else { activeProvider = providers[{{ $provider->id }}] || {}; showProviderModal = true; }"
                                    class="w-full py-2 bg-gradient-to-r from-[#6A8F3B] to-[#5a7a2f] hover:from-[#5a7a2f] hover:to-[#4e6a28] text-white text-[11px] font-bold rounded-xl shadow-md hover:shadow-lg transition-all duration-200 cursor-pointer flex items-center justify-center gap-1.5 shrink-0">
                                <span>🔍</span>
                                {{ app()->getLocale() === 'ar' ? 'عرض تفاصيل المزود' : (app()->getLocale() === 'fr' ? 'Détails du prestataire' : 'Show Provider Details') }}
                            </button>
                        </div>
                    </div>
                    @empty
                <div class="col-span-full bg-gray-50 border border-gray-200 rounded-3xl p-12 text-center text-gray-500 shadow-inner">
                    <span class="text-4xl">📭</span>
                    <p class="mt-2 font-bold">{{ app()->getLocale() === 'ar' ? 'لا يوجد مزودي خدمات يطابقون خيارات التصفية حالياً.' : (app()->getLocale() === 'fr' ? 'Aucun prestataire ne correspond aux filtres.' : 'No service providers match the filters.') }}</p>
                </div>
                    @endforelse
                </div>

                <!-- Big Provider Card Modal -->
                <div x-show="showProviderModal" x-cloak class="fixed inset-0 z-[10001] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" x-transition>
                    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl relative border border-gray-100 text-right" @click.away="showProviderModal = false">
                        
                        <!-- Close button -->
                        <button @click="showProviderModal = false" class="absolute top-4 left-4 text-gray-400 hover:text-gray-600 p-1.5 rounded-full hover:bg-gray-100 transition z-20">
                            ✕
                        </button>

                        <div class="group flex flex-col" x-show="activeProvider">
                            <!-- Cover Photo -->
                            <template x-if="activeProvider && activeProvider.cover">
                                <div class="w-full h-40 rounded-2xl overflow-hidden mb-4 relative">
                                    <img :src="activeProvider.cover" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                                </div>
                            </template>
                            
                            <!-- Provider Header -->
                            <div class="flex items-center gap-3 mb-4 justify-end text-right">
                                <div class="text-right flex-1">
                                    <h3 class="font-bold text-gray-900 text-lg leading-tight" x-text="activeProvider ? activeProvider.name : ''"></h3>
                                    <div class="flex flex-wrap items-center gap-1.5 mt-1 justify-end">
                                        <span class="text-xs text-gray-500 whitespace-nowrap">
                                            📍 <span x-text="activeProvider ? activeProvider.governorate : ''"></span>
                                        </span>
                                        <template x-if="activeProvider && activeProvider.type">
                                            <span class="px-2 py-0.5 rounded-md border text-[10px] font-bold" :class="activeProvider.typeColor" x-text="activeProvider.type"></span>
                                        </template>
                                        <span class="px-2 py-0.5 rounded-md bg-gray-100 text-gray-600 text-[10px] font-bold">
                                            <span x-text="activeProvider ? activeProvider.icon : ''"></span> <span x-text="activeProvider ? activeProvider.role : ''"></span>
                                        </span>
                                    </div>
                                </div>
                                <!-- Profile Pic -->
                                <div class="relative w-14 h-14 rounded-full overflow-hidden shrink-0 border-2 border-[#6A8F3B]/20 flex items-center justify-center">
                                    <template x-if="activeProvider && activeProvider.profile_picture">
                                        <img :src="activeProvider.profile_picture" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    </template>
                                    <div class="w-full h-full rounded-full bg-gradient-to-br from-emerald-500 to-teal-500 items-center justify-center text-white text-lg font-bold"
                                         :class="activeProvider && activeProvider.profile_picture ? 'hidden' : 'flex'">
                                        <span x-text="activeProvider ? activeProvider.name.substring(0,1).toUpperCase() : ''"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            <p class="text-gray-600 text-sm leading-relaxed font-medium mb-4" x-text="activeProvider && activeProvider.description ? activeProvider.description : 'لا يوجد وصف تفصيلي للمزود'"></p>

                            <!-- Services Tags inside big card -->
                            <div class="mb-4" x-show="activeProvider && activeProvider.services && activeProvider.services.length > 0">
                                <h4 class="text-xs font-bold text-gray-400 mb-2">{{ app()->getLocale() === 'ar' ? 'قائمة الخدمات المتوفرة:' : 'Services list:' }}</h4>
                                <div class="flex flex-wrap gap-1.5 justify-end">
                                    <template x-for="ps in (activeProvider ? activeProvider.services : [])">
                                        <span class="px-2.5 py-1 rounded-xl bg-indigo-50 border border-indigo-100 text-xs text-indigo-700 font-semibold" x-text="ps.title"></span>
                                    </template>
                                </div>
                            </div>
                            
                            <!-- Price display -->
                            <div class="bg-gray-50 border border-gray-100 rounded-xl p-4 mb-5 flex items-center justify-between">
                                <span class="text-xs font-bold text-gray-500">{{ app()->getLocale() === 'ar' ? 'سعر الخدمة المقدر:' : 'Estimated Price:' }}</span>
                                <div class="text-lg font-bold text-[#C8A356]">
                                    <span x-show="activeProvider && activeProvider.price_type === 'fixed' && activeProvider.price"><span x-text="activeProvider ? activeProvider.price : ''"></span> TND</span>
                                    <span x-show="!activeProvider || activeProvider.price_type !== 'fixed' || !activeProvider.price">{{ app()->getLocale() === 'ar' ? 'السعر حسب الطلب' : 'Upon Request' }}</span>
                                </div>
                            </div>

                            <!-- CTA Buttons -->
                            <div class="flex gap-2" x-show="activeProvider">
                                <button type="button" @click="handleFullProfile(activeProvider ? activeProvider.url : '#')" class="flex-grow text-center py-3 rounded-xl border border-gray-200 hover:bg-gray-50 text-gray-700 font-bold text-sm transition duration-200 cursor-pointer">
                                    👁 {{ app()->getLocale() === 'ar' ? 'عرض الملف الكامل' : 'View Full Profile' }}
                                </button>
                                <button type="button" @click="handleWhatsApp(activeProvider ? activeProvider.phone : '')" class="flex-grow text-center py-3 rounded-xl bg-green-500 hover:bg-green-600 text-white font-bold text-sm flex items-center justify-center gap-1.5 transition duration-200 cursor-pointer">
                                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.246 2.248 3.484 5.232 3.484 8.412-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.309 1.656zm6.224-3.52s.126.074.39.231c1.56.93 3.351 1.421 5.22 1.422 5.513 0 10-4.487 10-10 0-2.673-1.04-5.186-2.93-7.076-1.89-1.889-4.403-2.928-7.07-2.929-5.515 0-10.002 4.487-10.002 10 0 1.763.461 3.486 1.332 5.012l.145.255-1.111 4.056 4.126-1.082z"/></svg>
                                    <span>{{ app()->getLocale() === 'ar' ? 'واتساب' : 'WhatsApp' }}</span>
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    {{-- ─── GUEST REGISTRATION GATE MODAL ─── --}}
    <div x-show="showRegisterGateModal" 
         class="fixed inset-0 flex items-center justify-center p-4 bg-black/80 backdrop-blur-md" 
         x-transition:enter="transition ease-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="transition ease-in duration-200" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0"
         style="display: none; z-index: 10001;">
         
        <div @click.away="showRegisterGateModal = false" 
             class="bg-white border border-gray-100 w-full max-w-md rounded-3xl p-6 sm:p-8 shadow-2xl relative text-right overflow-hidden"
             x-transition:enter="transition ease-out duration-300" 
             x-transition:enter-start="opacity-0 scale-95 translate-y-4" 
             x-transition:enter-end="opacity-100 scale-100 translate-y-0" 
             x-transition:leave="transition ease-in duration-200" 
             x-transition:leave-start="opacity-100 scale-100 translate-y-0" 
             x-transition:leave-end="opacity-0 scale-95 translate-y-4">
             
            <!-- Close Button -->
            <button @click="showRegisterGateModal = false" class="absolute top-4 left-4 text-gray-400 hover:text-gray-600 p-1.5 rounded-full hover:bg-gray-100 transition z-20 cursor-pointer">
                ✕
            </button>

            <!-- Modal Icon & Header -->
            <div class="flex flex-col items-center text-center mb-6">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-[#6A8F3B] to-[#5a7a2f] text-white flex items-center justify-center text-3xl shadow-lg mb-4">
                    🔒
                </div>
                <h3 class="text-xl font-black text-gray-900 leading-snug">
                    {{ app()->getLocale() === 'ar' ? 'انضم إلى منصة زين للتواصل المباشر' : 'Join ZinToop for Direct Contact' }}
                </h3>
                <p class="text-xs text-gray-500 mt-2 leading-relaxed max-w-xs">
                    {{ app()->getLocale() === 'ar' ? 'للتواصل المباشر مع هذا المزود ومشاهدة رقم الهاتف والملف الكامل، أنشئ حسابك المجاني في ثوانٍ.' : 'Create a free account in seconds to view direct phone numbers and full profiles.' }}
                </p>
            </div>

            <!-- Conversion Highlights -->
            <div class="bg-emerald-50/70 border border-emerald-100 rounded-2xl p-4 mb-6 space-y-2.5 text-xs text-emerald-800 text-right font-medium">
                <div class="flex items-center gap-2">
                    <span class="text-base">✅</span>
                    <span>{{ app()->getLocale() === 'ar' ? 'تواصل مباشر عبر الواتساب والمكالمات بدون عمولات' : 'Direct WhatsApp & phone calls without commissions' }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-base">✅</span>
                    <span>{{ app()->getLocale() === 'ar' ? 'إضافة طلباتك وعروضك في سوق زيت الزيتون التونسي' : 'Post your offers & requests in the Tunisian marketplace' }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-base">✅</span>
                    <span>{{ app()->getLocale() === 'ar' ? 'حساب مجاني 100% يتيح لك الاستفادة من جميع الخدمات' : '100% free account with full access to all features' }}</span>
                </div>
            </div>

            <!-- Action CTA Buttons -->
            <div class="space-y-3">
                <a href="{{ route('register') }}" class="w-full py-3.5 bg-[#6A8F3B] hover:bg-[#5a7a2f] text-white font-black rounded-xl shadow-lg shadow-[#6A8F3B]/30 transition duration-200 text-sm flex items-center justify-center gap-2">
                    🚀 {{ app()->getLocale() === 'ar' ? 'إنشاء حساب مجاني في ثوانٍ' : 'Create Free Account' }}
                </a>
                <a href="{{ route('login') }}" class="w-full py-3 bg-gray-50 hover:bg-gray-100 text-gray-700 font-bold rounded-xl border border-gray-200 transition duration-200 text-xs flex items-center justify-center gap-1.5">
                    🔐 {{ app()->getLocale() === 'ar' ? 'لديك حساب بالفعل؟ تسجيل الدخول' : 'Already have an account? Log In' }}
                </a>
            </div>
        </div>
    </div>

    {{-- ─── APPOINTMENT MODAL (ZINTOOP DIGITAL SERVICES) ─── --}}
    <div x-show="modalOpen" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-md" 
         x-transition:enter="transition ease-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="transition ease-in duration-200" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0"
         style="display: none;">
         
        <div @click.away="modalOpen = false" 
             class="bg-white border border-gray-100 w-full max-w-lg rounded-3xl p-8 shadow-2xl relative overflow-hidden text-right"
             x-transition:enter="transition ease-out duration-300" 
             x-transition:enter-start="opacity-0 scale-95 translate-y-4" 
             x-transition:enter-end="opacity-100 scale-100 translate-y-0" 
             x-transition:leave="transition ease-in duration-200" 
             x-transition:leave-start="opacity-100 scale-100 translate-y-0" 
             x-transition:leave-end="opacity-0 scale-95 translate-y-4">
             
            <!-- Premium decorative background light -->
            <div class="absolute -right-16 -top-16 w-48 h-48 bg-[#6A8F3B]/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -left-16 -bottom-16 w-48 h-48 bg-[#C8A356]/10 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Header -->
            <div class="flex items-center justify-between mb-6 relative z-10">
                <div class="flex items-center gap-3">
                    <span class="text-3xl" x-text="activeServiceIcon">📦</span>
                    <div>
                        <h3 class="text-xl font-black text-gray-900" x-text="activeServiceName">باقة التسويق</h3>
                        <p class="text-xs text-gray-500 mt-0.5">
                            {{ app()->getLocale() === 'ar' ? 'أدخل تفاصيلك لتأكيد الحجز وبدء الحملة فوراً' : 'Fill details to confirm your instant booking' }}
                        </p>
                    </div>
                </div>
                <button @click="modalOpen = false" class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:text-gray-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Price display -->
            <div class="bg-gray-50 border border-gray-150 rounded-2xl p-4 mb-6 flex items-center justify-between relative z-10">
                <span class="text-sm font-semibold text-gray-600">
                    <span x-show="activeServiceId == 5">{{ app()->getLocale() === 'ar' ? 'الميزانية المقدرة:' : 'Estimated Budget:' }}</span>
                    <span x-show="activeServiceId != 5">{{ app()->getLocale() === 'ar' ? 'الميزانية الأسبوعية المقدرة:' : 'Estimated Weekly Budget:' }}</span>
                </span>
                <div class="text-2xl font-black text-[#C8A356] flex items-baseline gap-1">
                    <span x-text="activeServicePrice">0</span>
                    <span class="text-xs font-normal text-gray-500">TND</span>
                </div>
            </div>

            <!-- Form -->
            <form :action="'/services/appointment/' + activeServiceId" method="POST" class="space-y-4 relative z-10">
                @csrf
                
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">{{ app()->getLocale() === 'ar' ? 'تفاصيل الطلب أو الاستفسار' : 'Inquiry / Order Details' }} <span class="text-red-400">*</span></label>
                    <textarea name="business_info" 
                              required
                              rows="3" 
                              placeholder="{{ app()->getLocale() === 'ar' ? 'اكتب تفاصيل طلبك هنا لنقوم بالتواصل معك داخل المنصة...' : 'Type your request details here for direct platform contact...' }}"
                              class="w-full bg-gray-50 border border-gray-250 rounded-xl px-4 py-3 text-gray-800 text-sm placeholder-gray-400 focus:outline-none focus:border-[#6A8F3B] focus:ring-2 focus:ring-[#6A8F3B]/20 transition-all"></textarea>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-[#6A8F3B] to-[#5a7a2f] hover:from-[#5a7a2f] hover:to-[#4e6a28] text-white font-bold py-3.5 px-6 rounded-xl shadow-lg hover:shadow-xl hover:shadow-[#6A8F3B]/20 active:scale-[0.98] transition-all duration-200 text-sm flex items-center justify-center gap-2 cursor-pointer mt-4">
                    <span>📩</span>
                    {{ app()->getLocale() === 'ar' ? 'إرسال الطلب المباشر عبر المنصة' : 'Send Direct Request' }}
                </button>
                
                <p class="text-[10px] text-center text-gray-400 mt-3 leading-relaxed">
                    {{ app()->getLocale() === 'ar' 
                        ? 'سيتم تسجيل طلبك مباشرة في حسابك بالمنصة وسيتواصل معك فريقنا أو المزود داخل صندوق الرسائل.' 
                        : 'Your request will be submitted to your account inbox directly.' }}
                </p>
            </form>

    </div>
</div>
</div> {{-- Close main Alpine x-data wrapper --}}
@endsection
