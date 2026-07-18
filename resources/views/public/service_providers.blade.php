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
@endphp

<div x-data="{ 
    modalOpen: false, 
    activeServiceId: '', 
    activeServiceName: '', 
    activeServicePrice: '', 
    activeServiceIcon: '📦',
    activeProvider: null,
    showProviderModal: false,
    openModal(id, name, price, icon) {
        this.activeServiceId = id;
        this.activeServiceName = name;
        this.activeServicePrice = price;
        this.activeServiceIcon = icon;
        this.modalOpen = true;
        
        fetch('{{ url('/services/appointment') }}/' + id)
            .then(res => console.log('Logged add_to_cart'))
            .catch(err => console.error(err));
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
    }
}">
    <div class="min-h-screen bg-cover bg-center" style="background-image: url('{{ asset('images/growpagepicture.png') }}'); background-color: #f8f9fa;">
        <div class="min-h-screen bg-black/60 pb-20">

            {{-- ─── HERO ─── --}}
            <div class="relative overflow-hidden pt-32 pb-12 px-4 text-center">
                <div class="relative max-w-4xl mx-auto text-center">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-[#6A8F3B]/20 border border-[#6A8F3B]/40 text-[#a8d060] text-xs font-semibold mb-6 backdrop-blur-sm">
                        <span class="w-2 h-2 rounded-full bg-[#6A8F3B] animate-pulse"></span>
                        {{ app()->getLocale() === 'ar' ? 'مركز الخدمات المتكامل للقطاع الفلاحي والتصدير' : 'Unified Services & Providers Hub' }}
                    </div>
                    <h1 class="text-4xl md:text-5xl font-black text-white mb-4 leading-tight">
                        {{ app()->getLocale() === 'ar' ? 'مركز الخدمات ومزودي الخدمات' : 'Services & Providers Hub' }}
                    </h1>
                    <p class="text-gray-300 text-base md:text-lg max-w-2xl mx-auto mb-8 font-medium leading-relaxed">
                        {{ app()->getLocale() === 'ar' 
                            ? 'اكتشف خدماتنا الرقمية للتسويق والتصدير، وتصفح دليل مزودي الخدمات المحليين من ناقلين، معاصر، محاسبين، ومعدات فلاحية.' 
                            : 'Discover our digital marketing & export services, and explore the directory of local service providers.' }}
                    </p>
                </div>
            </div>

            {{-- ─── REGISTRATION CALL-TO-ACTION BANNER ─── --}}
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-16 relative z-10">
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
            <div id="directory" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="flex items-center gap-3 mb-8">
                    <span class="text-3xl">👥</span>
                    <div>
                        <h2 class="text-2xl font-black text-white">
                            {{ app()->getLocale() === 'ar' ? 'دليل مزودي الخدمات المحليين' : 'Local Service Providers Directory' }}
                        </h2>
                        <p class="text-white/50 text-xs mt-1">{{ app()->getLocale() === 'ar' ? 'تواصل مباشرة مع مقدمي الخدمات الفلاحية واللوجستية في تونس' : 'Contact agricultural & logistics providers in Tunisia directly' }}</p>
                    </div>
                </div>

                {{-- Filter Bar --}}
                <div class="bg-white/10 border border-white/20 rounded-3xl p-6 mb-8 backdrop-blur-md">
                    <form method="GET" action="{{ route('services.index') }}#directory" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                        <!-- Search input -->
                        <div>
                            <label class="block text-xs font-bold text-white/70 mb-2">{{ app()->getLocale() === 'ar' ? 'بحث باسم المزود' : 'Search Provider' }}</label>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ app()->getLocale() === 'ar' ? 'مثال: شركة النقل السريع...' : 'e.g. Transport Co...' }}" class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-[#6A8F3B]">
                        </div>

                        <!-- Provider Type -->
                        <div>
                            <label class="block text-xs font-bold text-white/70 mb-2">{{ app()->getLocale() === 'ar' ? 'نوع الخدمة' : 'Service Type' }}</label>
                            <select name="type" class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-[#6A8F3B] appearance-none bg-[url('data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2020%2020%22%20fill%3D%22%23ffffff%22%3E%3Cpath%20fill-rule%3D%22evenodd%22%20d%3D%22M5.293%207.293a1%201%200%20011.414%200L10%2010.586l3.293-3.293a1%201%200%20111.414%201.414l-4%204a1%201%200%2001-1.414%200l-4-4a1%201%200%20010-1.414z%22%20clip-rule%3D%22evenodd%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem_1.25rem] bg-no-repeat rtl:bg-[left_1rem_center] ltr:bg-[right_1rem_center] rtl:pl-10 ltr:pr-10">
                                <option value="" class="bg-[#122413]">{{ app()->getLocale() === 'ar' ? '-- جميع الخدمات --' : '-- All Services --' }}</option>
                                <option value="carrier" class="bg-[#122413]" {{ request('type') === 'carrier' ? 'selected' : '' }}>🚛 {{ app()->getLocale() === 'ar' ? 'ناقل بري وبحري' : 'Carrier' }}</option>
                                <option value="mill" class="bg-[#122413]" {{ request('type') === 'mill' ? 'selected' : '' }}>🏢 {{ app()->getLocale() === 'ar' ? 'معصرة زيتون' : 'Olive Mill' }}</option>
                                <option value="packer" class="bg-[#122413]" {{ request('type') === 'packer' ? 'selected' : '' }}>📦 {{ app()->getLocale() === 'ar' ? 'وحدة تعبئة وتغليف' : 'Packer' }}</option>
                                <option value="transiteur" class="bg-[#122413]" {{ request('type') === 'transiteur' ? 'selected' : '' }}>🛃 {{ app()->getLocale() === 'ar' ? 'مخلص جمركي' : 'Customs Broker' }}</option>
                                <option value="comptable" class="bg-[#122413]" {{ request('type') === 'comptable' ? 'selected' : '' }}>📊 {{ app()->getLocale() === 'ar' ? 'محاسب' : 'Accountant' }}</option>
                                <option value="service_bureau" class="bg-[#122413]" {{ request('type') === 'service_bureau' ? 'selected' : '' }}>📝 {{ app()->getLocale() === 'ar' ? 'مكتب خدمات إدارية' : 'Service Bureau' }}</option>
                                <option value="agri_equipment" class="bg-[#122413]" {{ request('type') === 'agri_equipment' ? 'selected' : '' }}>🚜 {{ app()->getLocale() === 'ar' ? 'معدات وآليات فلاحية' : 'Agri-Equipment' }}</option>
                                <option value="agri_materials" class="bg-[#122413]" {{ request('type') === 'agri_materials' ? 'selected' : '' }}>🌱 {{ app()->getLocale() === 'ar' ? 'مواد فلاحية وأسمدة' : 'Agri-Materials' }}</option>
                                <option value="agri_study_office" class="bg-[#122413]" {{ request('type') === 'agri_study_office' ? 'selected' : '' }}>📐 {{ app()->getLocale() === 'ar' ? 'مكتب دراسات فلاحية' : 'Agri-Study Office' }}</option>
                            </select>
                        </div>

                        <!-- Governorate -->
                        <div>
                            <label class="block text-xs font-bold text-white/70 mb-2">{{ app()->getLocale() === 'ar' ? 'الولاية' : 'Governorate' }}</label>
                            <select name="governorate" class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-[#6A8F3B] appearance-none bg-[url('data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2020%2020%22%20fill%3D%22%23ffffff%22%3E%3Cpath%20fill-rule%3D%22evenodd%22%20d%3D%22M5.293%207.293a1%201%200%20011.414%200L10%2010.586l3.293-3.293a1%201%200%20111.414%201.414l-4%204a1%201%200%2001-1.414%200l-4-4a1%201%200%20010-1.414z%22%20clip-rule%3D%22evenodd%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem_1.25rem] bg-no-repeat rtl:bg-[left_1rem_center] ltr:bg-[right_1rem_center] rtl:pl-10 ltr:pr-10">
                                <option value="" class="bg-[#122413]">{{ app()->getLocale() === 'ar' ? '-- جميع الولايات --' : '-- All Governorates --' }}</option>
                                @foreach(config('governorates', []) as $gov)
                                    <option value="{{ $gov }}" class="bg-[#122413]" {{ request('governorate') === $gov ? 'selected' : '' }}>{{ $gov }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 py-3 px-4 bg-[#6A8F3B] hover:bg-[#5a7a2f] text-white font-bold rounded-xl shadow-lg transition duration-200 text-sm flex items-center justify-center gap-1 cursor-pointer">
                                🔍 {{ app()->getLocale() === 'ar' ? 'تصفية' : 'Filter' }}
                            </button>
                            <a href="{{ route('services.index') }}#directory" class="py-3 px-4 bg-white/10 hover:bg-white/20 text-white font-bold rounded-xl transition duration-200 text-sm flex items-center justify-center cursor-pointer">
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
                                'title' => $provider->role === 'carrier' ? 'خدمات النقل اللوجستي والبري' : 'خدمات عامة متكاملة',
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
                        $validPhotos = array_values(array_filter($provider->cover_photos ?? [], fn($p) => is_string($p) && !empty($p) && \Illuminate\Support\Facades\Storage::disk('public')->exists($p)));
                        $provServices = $provider->meta_data['services'] ?? [];
                    @endphp

                    <div class="group bg-white border border-gray-100 shadow-md rounded-2xl p-4 hover:-translate-y-1 hover:shadow-xl transition-all duration-300 flex flex-col overflow-hidden text-right cursor-pointer"
                        @click="activeProvider = { 
                            name: {{ json_encode($provider->name) }}, 
                            role: {{ json_encode($roleData[app()->getLocale()] ?? $roleData['ar']) }}, 
                            icon: {{ json_encode($roleData['icon']) }}, 
                            type: {{ json_encode($typeData[app()->getLocale()] ?? $typeData['ar'] ?? '') }}, 
                            typeColor: {{ json_encode($typeData['color'] ?? '') }}, 
                            governorate: {{ json_encode($provider->addresses->first()->governorate ?? 'تونس') }}, 
                            profile_picture: {{ json_encode($provider->profile_picture && \Illuminate\Support\Facades\Storage::disk('public')->exists($provider->profile_picture) ? Storage::url($provider->profile_picture) : '') }}, 
                            description: {{ json_encode(str_replace(["\r", "\n"], ' ', $provider->meta_data['service_description'] ?? '')) }}, 
                            services: {{ json_encode($provServices) }}, 
                            price_type: {{ json_encode($card['price_type']) }}, 
                            price: {{ json_encode($card['price']) }}, 
                            phone: {{ json_encode($provider->phone) }}, 
                            url: {{ json_encode(route('user.profile', $provider->id)) }}, 
                            cover: {{ json_encode(count($validPhotos) > 0 ? Storage::url($validPhotos[0]) : '') }} 
                        }; showProviderModal = true">
                        
                        <!-- Little Card Image -->
                        <div class="w-full h-24 rounded-xl overflow-hidden mb-3 relative bg-gray-50 border border-gray-100/50 flex items-center justify-center">
                            @if(!empty($card['image']))
                                <img src="{{ asset($card['image']) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            @elseif($provider->profile_picture && \Illuminate\Support\Facades\Storage::disk('public')->exists($provider->profile_picture))
                                <img src="{{ Storage::url($provider->profile_picture) }}" class="w-12 h-12 rounded-full object-cover">
                            @else
                                <span class="text-3xl">{{ $roleData['icon'] }}</span>
                            @endif
                        </div>

                        <!-- Card Header -->
                        <h4 class="font-bold text-gray-900 text-xs mb-1 leading-tight line-clamp-1">{{ $card['title'] }}</h4>
                        
                        <!-- Provider Name & Tag -->
                        <div class="flex flex-wrap items-center gap-1 mt-1 mb-2 justify-end">
                            <span class="text-[10px] text-gray-500 whitespace-nowrap">
                                📍 {{ $provider->addresses->first()->governorate ?? 'تونس' }}
                            </span>
                            @if($typeData)
                                <span class="px-1.5 py-0.5 rounded border text-[9px] font-bold {{ $typeData['color'] }}">
                                    {{ $typeData[app()->getLocale()] ?? $typeData['ar'] }}
                                </span>
                            @endif
                            <span class="px-1.5 py-0.5 rounded bg-gray-100 text-gray-600 text-[9px] font-bold truncate max-w-[120px]">
                                {{ $roleData['icon'] }} {{ $provider->name }}
                            </span>
                        </div>

                        <!-- Description -->
                        <p class="text-gray-500 text-[11px] leading-relaxed mb-3 flex-grow line-clamp-2">
                            {{ $card['description'] ?: 'لا يوجد وصف تفصيلي متوفر لهذه الخدمة.' }}
                        </p>

                        <!-- Price display -->
                        <div class="bg-gray-50 border border-gray-100 rounded-xl p-2 mb-3 flex items-center justify-between">
                            <span class="text-[9px] font-bold text-gray-500">{{ app()->getLocale() === 'ar' ? 'السعر المقدر:' : 'Estimated Price:' }}</span>
                            <div class="text-[11px] font-bold text-[#C8A356]">
                                @if(($card['price_type'] ?? 'fixed') === 'fixed' && !empty($card['price']))
                                    {{ number_format($card['price'], 0) }} TND
                                @else
                                    {{ app()->getLocale() === 'ar' ? 'السعر حسب الطلب' : 'Upon Request' }}
                                @endif
                            </div>
                        </div>

                        <!-- CTA Button to reveal big card -->
                        <button type="button" class="w-full py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-[10px] font-bold rounded-lg transition duration-200">
                            🔍 {{ app()->getLocale() === 'ar' ? 'عرض تفاصيل المزود' : 'Show Provider Details' }}
                        </button>
                    </div>
                    @empty
                    <div class="col-span-full bg-white/5 border border-white/10 rounded-3xl p-12 text-center text-gray-400">
                        <span class="text-4xl">📭</span>
                        <p class="mt-2 font-bold">{{ app()->getLocale() === 'ar' ? 'لا يوجد مزودي خدمات يطابقون خيارات التصفية حالياً.' : 'No service providers match the filters.' }}</p>
                    </div>
                    @endforelse
                </div>

                <!-- Big Provider Card Modal -->
                <div x-show="showProviderModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" x-transition>
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
                                <template x-if="activeProvider && activeProvider.profile_picture">
                                    <img :src="activeProvider.profile_picture" class="w-14 h-14 rounded-full object-cover border-2 border-[#6A8F3B]/20">
                                </template>
                                <template x-if="activeProvider && !activeProvider.profile_picture">
                                    <div class="w-14 h-14 rounded-full bg-[#6A8F3B]/10 text-[#6A8F3B] flex items-center justify-center text-2xl font-bold">
                                        <span x-text="activeProvider ? activeProvider.icon : ''"></span>
                                    </div>
                                </template>
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
                                <a :href="activeProvider ? activeProvider.url : '#'" class="flex-grow text-center py-3 rounded-xl border border-gray-200 hover:bg-gray-50 text-gray-700 font-bold text-sm transition duration-200">
                                    👁 {{ app()->getLocale() === 'ar' ? 'عرض الملف الكامل' : 'View Full Profile' }}
                                </a>
                                <a :href="activeProvider ? 'https://wa.me/' + activeProvider.phone.replace(/[^0-9]/g, '') : '#'" target="_blank" class="flex-grow text-center py-3 rounded-xl bg-green-500 hover:bg-green-600 text-white font-bold text-sm flex items-center justify-center gap-1.5 transition duration-200">
                                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.246 2.248 3.484 5.232 3.484 8.412-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.309 1.656zm6.224-3.52s.126.074.39.231c1.56.93 3.351 1.421 5.22 1.422 5.513 0 10-4.487 10-10 0-2.673-1.04-5.186-2.93-7.076-1.89-1.889-4.403-2.928-7.07-2.929-5.515 0-10.002 4.487-10.002 10 0 1.763.461 3.486 1.332 5.012l.145.255-1.111 4.056 4.126-1.082z"/></svg>
                                    <span>{{ app()->getLocale() === 'ar' ? 'واتساب' : 'WhatsApp' }}</span>
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
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
             class="bg-[#0b180c] border border-white/10 w-full max-w-lg rounded-3xl p-8 shadow-2xl relative overflow-hidden text-right"
             x-transition:enter="transition ease-out duration-300" 
             x-transition:enter-start="opacity-0 scale-95 translate-y-4" 
             x-transition:enter-end="opacity-100 scale-100 translate-y-0" 
             x-transition:leave="transition ease-in duration-200" 
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
                <span class="text-sm font-semibold text-white/60">
                    <span x-show="activeServiceId == 5">{{ app()->getLocale() === 'ar' ? 'الميزانية المقدرة:' : 'Estimated Budget:' }}</span>
                    <span x-show="activeServiceId != 5">{{ app()->getLocale() === 'ar' ? 'الميزانية الأسبوعية المقدرة:' : 'Estimated Weekly Budget:' }}</span>
                </span>
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
</div>
@endsection
