@php
    $locale = app()->getLocale();
    $isRTL = $locale === 'ar';
@endphp

<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-amber-50 via-white to-green-50 py-8 px-4 sm:px-6 lg:px-8" dir="{{ $isRTL ? 'rtl' : 'ltr' }}">
        <div class="max-w-7xl mx-auto">
            
            {{-- Profile Header Card --}}
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8">
                {{-- Cover Photo --}}
                <div class="h-48 bg-gradient-to-r from-green-600 to-amber-600 relative">
                    @if($user->cover_photos && count($user->cover_photos) > 0)
                        <img src="{{ Storage::url($user->cover_photos[0]) }}" 
                             alt="Cover" 
                             class="w-full h-full object-cover">
                    @endif
                    
                    {{-- Role Badge --}}
                    <div class="absolute top-4 {{ $isRTL ? 'left-4' : 'right-4' }}">
                        <span class="px-4 py-2 rounded-full text-white font-semibold text-sm
                            {{ $user->role === 'farmer' ? 'bg-green-600' : '' }}
                            {{ $user->role === 'carrier' ? 'bg-blue-600' : '' }}
                            {{ $user->role === 'mill' ? 'bg-amber-600' : '' }}
                            {{ $user->role === 'packer' ? 'bg-purple-600' : '' }}
                            {{ !in_array($user->role, ['farmer', 'carrier', 'mill', 'packer']) ? 'bg-gray-600' : '' }}
                        ">
                            @if($locale === 'ar')
                                {{ $user->role === 'farmer' ? 'مزارع' : '' }}
                                {{ $user->role === 'carrier' ? 'ناقل' : '' }}
                                {{ $user->role === 'mill' ? 'معصرة' : '' }}
                                {{ $user->role === 'packer' ? 'مُعبّئ' : '' }}
                                {{ $user->role === 'normal' ? 'مستخدم عادي' : '' }}
                                {{ $user->role === 'admin' ? 'مدير' : '' }}
                            @elseif($locale === 'fr')
                                {{ $user->role === 'farmer' ? 'Agriculteur' : '' }}
                                {{ $user->role === 'carrier' ? 'Transporteur' : '' }}
                                {{ $user->role === 'mill' ? 'Moulin' : '' }}
                                {{ $user->role === 'packer' ? 'Emballeur' : '' }}
                                {{ $user->role === 'normal' ? 'Utilisateur' : '' }}
                                {{ $user->role === 'admin' ? 'Admin' : '' }}
                            @else
                                {{ ucfirst($user->role) }}
                            @endif
                        </span>
                    </div>
                </div>
                
                {{-- Profile Info --}}
                <div class="px-6 py-8">
                    <div class="flex flex-col md:flex-row items-center md:items-start gap-6">
                        {{-- Profile Picture --}}
                        <div class="flex-shrink-0">
                            @if($user->profile_picture)
                                <img src="{{ Storage::url($user->profile_picture) }}" 
                                     alt="{{ $user->name }}" 
                                     class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-xl -mt-16">
                            @else
                                <div class="w-32 h-32 rounded-full bg-gradient-to-br from-green-400 to-amber-500 flex items-center justify-center text-white text-4xl font-bold border-4 border-white shadow-xl -mt-16">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        
                        {{-- User Info --}}
                        <div class="flex-1 text-center md:text-{{ $isRTL ? 'right' : 'left' }}">
                            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                                {{ $user->name }}
                            </h1>
                            
                            {{-- Role-specific name --}}
                            @if($user->role === 'farmer' && ($user->farm_name || $user->farm_name_ar))
                                <p class="text-lg text-gray-600 mb-4">
                                    🌾 {{ $user->farm_name ?? $user->farm_name_ar }}
                                </p>
                            @elseif($user->role === 'mill' && $user->mill_name)
                                <p class="text-lg text-gray-600 mb-4">
                                    🏭 {{ $user->mill_name }}
                                </p>
                            @elseif($user->role === 'carrier' && $user->company_name)
                                <p class="text-lg text-gray-600 mb-4">
                                    🚛 {{ $user->company_name }}
                                </p>
                            @elseif($user->role === 'packer' && $user->packer_name)
                                <p class="text-lg text-gray-600 mb-4">
                                    📦 {{ $user->packer_name }}
                                </p>
                            @endif
                            
                            {{-- Rating --}}
                            @if($user->rating_avg > 0)
                                <div class="flex items-center justify-center md:justify-{{ $isRTL ? 'end' : 'start' }} gap-2 mb-4">
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-5 h-5 {{ $i <= round($user->rating_avg) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        @endfor
                                    </div>
                                    <span class="text-gray-600 text-sm">
                                        {{ number_format($user->rating_avg, 1) }} ({{ $user->rating_count }} {{ __('reviews') }})
                                    </span>
                                </div>
                            @endif
                            
                            {{-- Statistics --}}
                            <div class="flex flex-wrap gap-6 justify-center md:justify-{{ $isRTL ? 'end' : 'start' }}">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-green-600">{{ $activeListings }}</div>
                                    <div class="text-sm text-gray-600">
                                        @if($locale === 'ar')
                                            عروض نشطة
                                        @elseif($locale === 'fr')
                                            Offres actives
                                        @else
                                            Active Listings
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-gray-700">{{ $totalListings }}</div>
                                    <div class="text-sm text-gray-600">
                                        @if($locale === 'ar')
                                            إجمالي العروض
                                        @elseif($locale === 'fr')
                                            Total des offres
                                        @else
                                            Total Listings
                                        @endif
                                    </div>
                                </div>
                                
                                @if($user->trust_score > 0)
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-blue-600">{{ number_format($user->trust_score, 1) }}%</div>
                                        <div class="text-sm text-gray-600">
                                            @if($locale === 'ar')
                                                درجة الثقة
                                            @elseif($locale === 'fr')
                                                Score de confiance
                                            @else
                                                Trust Score
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Contact & Details Section --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                {{-- Contact Information --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">
                            @if($locale === 'ar')
                                معلومات الاتصال
                            @elseif($locale === 'fr')
                                Coordonnées
                            @else
                                Contact Information
                            @endif
                        </h2>
                        
                        @if($contactInfo['phone'])
                            <div class="flex items-center gap-3 mb-3 text-gray-700">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                <a href="tel:{{ $contactInfo['phone'] }}" class="hover:text-green-600 transition-colors">
                                    {{ $contactInfo['phone'] }}
                                </a>
                            </div>
                        @endif
                        
                        @if($contactInfo['email'])
                            <div class="flex items-center gap-3 mb-3 text-gray-700">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <a href="mailto:{{ $contactInfo['email'] }}" class="hover:text-green-600 transition-colors break-all">
                                    {{ $contactInfo['email'] }}
                                </a>
                            </div>
                        @endif
                        
                        @if($addresses->count() > 0)
                            <div class="flex items-start gap-3 text-gray-700">
                                <svg class="w-5 h-5 text-green-600 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <div>
                                    @foreach($addresses as $address)
                                        <p class="mb-1">{{ $address->governorate }}, {{ $address->delegation }}</p>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    {{-- Role-Specific Information --}}
                    @if(count($roleInfo) > 0)
                        <div class="bg-white rounded-xl shadow-lg p-6 mt-6">
                            <h2 class="text-xl font-bold text-gray-900 mb-4">
                                @if($locale === 'ar')
                                    تفاصيل إضافية
                                @elseif($locale === 'fr')
                                    Détails supplémentaires
                                @else
                                    Additional Details
                                @endif
                            </h2>
                            
                            <div class="space-y-3 text-sm">
                                @foreach($roleInfo as $key => $value)
                                    @if($value)
                                        <div>
                                            <span class="font-semibold text-gray-700">
                                                @if($locale === 'ar')
                                                    {{ $key === 'tree_number' ? 'عدد الأشجار' : '' }}
                                                    {{ $key === 'olive_type' ? 'نوع الزيتون' : '' }}
                                                    {{ $key === 'fleet_size' ? 'حجم الأسطول' : '' }}
                                                    {{ $key === 'camion_capacity' ? 'سعة الشاحنة' : '' }}
                                                    {{ $key === 'capacity' ? 'السعة' : '' }}
                                                    {{ $key === 'packaging_types' ? 'أنواع التعبئة' : '' }}
                                                @elseif($locale === 'fr')
                                                    {{ $key === 'tree_number' ? 'Nombre d\'arbres' : '' }}
                                                    {{ $key === 'olive_type' ? 'Type d\'olive' : '' }}
                                                    {{ $key === 'fleet_size' ? 'Taille de la flotte' : '' }}
                                                    {{ $key === 'camion_capacity' ? 'Capacité du camion' : '' }}
                                                    {{ $key === 'capacity' ? 'Capacité' : '' }}
                                                    {{ $key === 'packaging_types' ? 'Types d\'emballage' : '' }}
                                                @else
                                                    {{ ucwords(str_replace('_', ' ', $key)) }}
                                                @endif
                                            </span>: 
                                            <span class="text-gray-600">{{ $value }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
                
                {{-- Active Listings --}}
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">
                            @if($locale === 'ar')
                                العروض النشطة
                            @elseif($locale === 'fr')
                                Offres actives
                            @else
                                Active Listings
                            @endif
                        </h2>
                        
                        @if($listings->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($listings as $listing)
                                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                        <h3 class="font-semibold text-lg text-gray-900 mb-2">
                                            {{ $listing->title }}
                                        </h3>
                                        
                                        @if($listing->product)
                                            <p class="text-sm text-gray-600 mb-2">
                                                {{ $listing->product->variety }} - {{ $listing->product->quality }}
                                            </p>
                                        @endif
                                        
                                        <div class="flex items-center justify-between">
                                            <span class="text-lg font-bold text-green-600">
                                                {{ number_format($listing->price, 2) }} {{ $listing->currency }}
                                            </span>
                                            
                                            <a href="{{ route('listings.show', $listing) }}" 
                                               class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                                @if($locale === 'ar')
                                                    عرض التفاصيل ←
                                                @elseif($locale === 'fr')
                                                    Voir les détails →
                                                @else
                                                    View Details →
                                                @endif
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            {{-- Pagination --}}
                            <div class="mt-6">
                                {{ $listings->links() }}
                            </div>
                        @else
                            <div class="text-center py-12 text-gray-500">
                                @if($locale === 'ar')
                                    لا توجد عروض نشطة حالياً
                                @elseif($locale === 'fr')
                                    Aucune offre active actuellement
                                @else
                                    No active listings at the moment
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>
