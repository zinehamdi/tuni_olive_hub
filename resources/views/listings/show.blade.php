@extends('layouts.app')

@push('head')
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endpush

@section('content')
<div dir="rtl" class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4">
        <!-- Back Button -->
        <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-[#6A8F3B] hover:text-[#5a7a2f] mb-6 font-bold">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            {{ __('Back to Products') }}
        </a>

        <div class="grid lg:grid-cols-2 gap-8">
            <!-- Product Image Section -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div x-data="{ activeImage: 0 }">
                    <!-- Main Image Display -->
                    <div class="aspect-square bg-gradient-to-br from-[#6A8F3B] to-[#C8A356] flex items-center justify-center relative overflow-hidden">
                        @if($listing->media && count($listing->media) > 0)
                            @foreach($listing->media as $index => $media)
                                <img x-show="activeImage === {{ $index }}" 
                                     src="{{ asset('storage/' . $media) }}" 
                                     alt="{{ $listing->product->variety }}" 
                                     class="w-full h-full object-contain"
                                     x-transition:enter="transition ease-out duration-300"
                                     x-transition:enter-start="opacity-0"
                                     x-transition:enter-end="opacity-100">
                            @endforeach
                        @else
                            <svg class="w-48 h-48 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($listing->product->type === 'oil')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                                @endif
                            </svg>
                        @endif
                    </div>
                    
                    <!-- Image Thumbnails -->
                    @if($listing->media && count($listing->media) > 1)
                        <div class="p-4 bg-gray-50 flex gap-3 overflow-x-auto">
                            @foreach($listing->media as $index => $media)
                                <button @click="activeImage = {{ $index }}" 
                                        class="flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden border-2 transition-all"
                                        :class="activeImage === {{ $index }} ? 'border-[#6A8F3B] shadow-lg scale-105' : 'border-gray-300 opacity-70 hover:opacity-100'">
                                    <img src="{{ asset('storage/' . $media) }}" 
                                         alt="{{ __('Image') }} {{ $index + 1 }}" 
                                         class="w-full h-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Product Details Section -->
            <div>
                <div class="bg-white rounded-2xl shadow-xl p-8">
                    <!-- Product Name & Badges -->
                    <div class="mb-6">
                        <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ __($listing->product->variety) }}</h1>
                        <div class="flex items-center gap-2 flex-wrap" flex-wrap">
                            <span class="px-4 py-2 rounded-full bg-[#6A8F3B] text-white font-bold">
                                {{ $listing->product->type === 'olive' ? __('Olives') : __('Olive Oil') }}
                            </span>
                            @if($listing->product->quality)
                                <span class="px-4 py-2 rounded-full bg-[#C8A356] text-white font-bold">
                                    {{ $listing->product->quality }}
                                </span>
                            @endif
                            @if($listing->status === 'active')
                                <span class="px-4 py-2 rounded-full bg-green-500 text-white font-bold">{{ __('Active') }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- Price -->
                    <div class="mb-6 p-6 bg-gradient-to-r from-[#6A8F3B]/10 to-[#C8A356]/10 rounded-xl">
                        <div class="text-sm text-gray-600 mb-2">{{ __('Price') }}</div>
                        <div class="text-5xl font-bold text-[#6A8F3B]">
                            {{ number_format($listing->price, 2) }}
                            <span class="text-2xl text-gray-600">{{ app()->getLocale() === 'ar' ? 'دينار' : __('TND') }}</span>
                        </div>
                                        <!-- Price per Unit -->
                <div class="text-sm text-gray-600 mt-2">
                    {{ __('Per') }} 
                    @if($listing->unit === 'kg')
                        {{ app()->getLocale() === 'ar' ? 'كلغ' : __('Kilogram') }}
                    @elseif($listing->unit === 'ton')
                        {{ app()->getLocale() === 'ar' ? 'طن' : __('Ton') }}
                    @elseif($listing->unit === 'liter')
                        {{ app()->getLocale() === 'ar' ? 'لتر' : __('Liter') }}
                    @elseif($listing->unit === 'bottle')
                        {{ app()->getLocale() === 'ar' ? 'قارورة' : __('Bottle') }}
                    @else
                        {{ $listing->unit ?? __('Unit') }}
                    @endif
                </div>
                    </div>

                    <!-- Seller Info -->
                    <div class="mb-6 p-6 bg-gray-50 rounded-xl">
                        <h3 class="text-lg font-bold text-gray-900 mb-3">{{ __('Seller Information') }}</h3>
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-full bg-[#6A8F3B] flex items-center justify-center text-white font-bold text-xl">
                                {{ substr($listing->seller->name, 0, 1) }}
                            </div>
                            <div>
                                <div class="font-bold text-gray-900">@if($listing->seller->role !== 'admin') <a href="{{ route('user.profile', $listing->seller) }}" class="hover:text @else <span class="text-gray-700 font-semibold" @endif-[#6A8F3B] underline-offset-2 hover:underline">{{ $listing->seller->role === 'admin' ? __('Seller') : $listing->seller->name }}</a></div>
                                <div class="text-sm text-gray-600">
                                   <div class="text-sm text-gray-600">
                                      @if($listing->seller->role !== 'admin')
                                          @if($listing->seller->role === 'farmer')
                                              {{ __('Farmer') }}
                                          @elseif($listing->seller->role === 'carrier')
                                              {{ __('Carrier') }}
                                          @elseif($listing->seller->role === 'mill')
                                              {{ __('Mill') }}
                                          @elseif($listing->seller->role === 'packer')
                                              {{ __('Packer') }}
                                          @endif
                                      @else
                                          {{ __('User') }}
                                      @endif
                                  </div>
                                </div>
                            </div>
                        </div>
                        
                        @if($listing->seller->addresses->first())
                            @php $address = $listing->seller->addresses->first(); @endphp
                            <div class="mt-4 flex items-start gap-3 transition-all duration-300 rounded-lg p-2 -m-2" data-seller-location>
                                <svg class="w-5 h-5 text-[#C8A356] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <div>
                                    <div class="text-sm text-gray-600">{{ __('Location') }}</div>
                                    <div class="font-semibold text-gray-900">
                                        {{ $address->governorate ?? '' }}
                                        @if($address->delegation)
                                            - {{ $address->delegation }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Additional Details -->
                    <div class="space-y-4 mb-6">
                        @if($listing->min_order)
                            <div class="flex items-center gap-3">
                                <svg class="w-6 h-6 text-[#6A8F3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                <div>
                                    <div class="text-sm text-gray-600">{{ __('Minimum Order') }}</div>
                                    <div class="font-bold">{{ $listing->min_order }} {{ __('unit') }}</div>
                                </div>
                            </div>
                        @endif

                        @if($listing->payment_methods)
                            <div class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-[#6A8F3B] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <div>
                                    <div class="text-sm text-gray-600">{{ __('Payment Methods') }}</div>
                                    @php
$__map = [
  "cash"=>"نقداً","bank_transfer"=>"تحويل بنكي","check"=>"شيك",
  "cod"=>"الدفع عند التسليم","flouci"=>"فلوسي","d17"=>"D17",
  "stripe"=>"بطاقة بنكية","bank_lc"=>"اعتماد مستندي (LC)"
];
$__raw = $listing->payment_methods;
$__arr = is_array($__raw) ? $__raw
       : (is_string($__raw) ? (json_decode($__raw,true) ?: preg_split('/\s*,\s*/', $__raw,-1,PREG_SPLIT_NO_EMPTY)) : []);
$__arr = array_map(fn($k)=> $__map[trim($k)] ?? trim($k), $__arr);
@endphp
<div class="font-bold">{{ implode('، ', array_filter($__arr)) ?: 'غير محدد' }}</div>
                                </div>
                            </div>
                        @endif

                        @if($listing->delivery_options)
                            <div class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-[#6A8F3B] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                </svg>
                                <div>
                                    <div class="text-sm text-gray-600">{{ __('Delivery Options') }}</div>
                                    @php
$__map = [
  "pickup"=>"استلام من الموقع",
  "local_delivery"=>"توصيل محلي",
  "carrier"=>"عبر ناقل",
  "export"=>"تصدير"
];
$__raw = $listing->delivery_options;
$__arr = is_array($__raw) ? $__raw
       : (is_string($__raw) ? (json_decode($__raw,true) ?: preg_split('/\s*,\s*/', $__raw,-1,PREG_SPLIT_NO_EMPTY)) : []);
$__arr = array_map(fn($k)=> $__map[trim($k)] ?? trim($k), $__arr);
@endphp
<div class="font-bold">{{ implode('، ', array_filter($__arr)) ?: 'غير محدد' }}</div>
                                </div>
                            </div>
                        @endif

                        <div class="flex items-center gap-3">
                            <svg class="w-6 h-6 text-[#6A8F3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <div class="text-sm text-gray-600">{{ __('Publication Date') }}</div>
                                <div class="font-bold">{{ $listing->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-3" x-data="listingActions">
                        @auth
                            <!-- Contact Seller Button -->
                            <button @click="showContactModal = true" class="flex-1 px-6 py-4 bg-[#6A8F3B] text-white rounded-xl hover:bg-[#5a7a2f] transition font-bold text-lg shadow-lg flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                {{ __('Contact Seller') }}
                            </button>
                            
                            <!-- View Location Button - Always visible -->
                            @if($listing->seller->addresses->first())
                                <button @click="openLocationInfo" 
                                        class="px-6 py-4 bg-[#C8A356] text-white rounded-xl hover:bg-[#b08a3c] transition font-bold shadow-lg" 
                                        :title="'{{ __('View Location') }}'">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </button>
                            @endif
                            
                            <!-- Favorite Button -->
                            <button @click="toggleFavorite" class="px-6 py-4 rounded-xl transition font-bold shadow-lg" :class="isFavorite ? 'bg-red-500 text-white hover:bg-red-600' : 'bg-gray-200 text-gray-600 hover:bg-gray-300'">
                                <svg class="w-6 h-6" :fill="isFavorite ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </button>
                            
                            <!-- Contact Modal -->
                            <div x-show="showContactModal" 
                                 x-cloak
                                 @click.self="showContactModal = false"
                                 class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
                                 style="display: none;">
                                <div class="bg-white rounded-2xl p-8 max-w-md w-full shadow-2xl" @click.stop>
                                    <div class="flex justify-between items-center mb-6">
                                        <h3 class="text-2xl font-bold text-gray-900">{{ __('Contact Information') }}</h3>
                                        <button @click="showContactModal = false" class="text-gray-400 hover:text-gray-600">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                    
                                    <div class="space-y-4">
                                        <!-- Seller Info -->
                                        <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl">
                                            <div class="w-12 h-12 rounded-full bg-[#6A8F3B] flex items-center justify-center text-white font-bold text-xl">
                                                {{ substr($listing->seller->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-gray-900">@if($listing->seller->role !== 'admin') <a href="{{ route('user.profile', $listing->seller) }}" class="hover:text @else <span class="text-gray-700 font-semibold" @endif-[#6A8F3B] underline-offset-2 hover:underline">{{ $listing->seller->role === 'admin' ? __('Seller') : $listing->seller->name }}</a></div>
                                                <div class="text-sm text-gray-600">
@if($listing->seller->role !== 'admin')
                                                    @if($listing->seller->role === 'farmer')
                                                        {{ __('Farmer') }}
                                                    @elseif($listing->seller->role === 'carrier')
                                                        {{ __('Carrier') }}
                                                    @elseif($listing->seller->role === 'mill')
                                                        {{ __('Mill') }}
                                                    @elseif($listing->seller->role === 'packer')
@endif
                                                        {{ __('Packer') }}
                                                    @else
                                                        {{ __('User') }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Phone Number -->
                                        @if($listing->seller->phone)
                                            <div class="p-4 bg-[#6A8F3B]/10 rounded-xl">
                                                <div class="text-sm text-gray-600 mb-2">{{ __('Phone Number') }}</div>
                                                <a href="tel:{{ $listing->seller->phone }}" class="text-2xl font-bold text-[#6A8F3B] hover:text-[#5a7a2f] flex items-center gap-2">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                    </svg>
                                                    {{ $listing->seller->phone }}
                                                </a>
                                            </div>
                                        @else
                                            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-xl">
                                                <p class="text-yellow-800">{{ __('No phone number provided for the seller') }}</p>
                                            </div>
                                        @endif
                                        
                                        <!-- Location -->
                                        @if($listing->seller->addresses->first())
                                            @php $address = $listing->seller->addresses->first(); @endphp
                                            <div class="p-4 bg-gray-50 rounded-xl">
                                                <div class="text-sm text-gray-600 mb-2">{{ __('Location') }}</div>
                                                <div class="font-semibold text-gray-900 flex items-center gap-2">
                                                    <svg class="w-5 h-5 text-[#C8A356]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    </svg>
                                                    {{ $address->governorate ?? '' }}
                                                    @if($address->delegation)
                                                        - {{ $address->delegation }}
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="mt-6">
                                        <button @click="showContactModal = false" class="w-full px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition font-bold">
                                            {{ __('Close') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Map Modal -->
                            @if($listing->seller->addresses->first() && $listing->seller->addresses->first()->lat && $listing->seller->addresses->first()->lng)
                            <div x-show="showMapModal" 
                                 @click.self="if(map) { map.remove(); map = null; } showMapModal = false;"
                                 class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-[9999] p-4"
                                 style="display: none;"
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100">
                                <div class="bg-white rounded-2xl p-6 max-w-4xl w-full shadow-2xl relative" @click.stop>
                                    <div class="flex justify-between items-center mb-4">
                                        <h3 class="text-2xl font-bold text-gray-900">{{ __('Seller Location') }}</h3>
                                        <button @click="if(map) { map.remove(); map = null; } showMapModal = false;" class="text-gray-400 hover:text-gray-600">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                    
                                    <!-- Seller Info -->
                                    <div class="mb-4 p-4 bg-gray-50 rounded-xl flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-full bg-[#6A8F3B] flex items-center justify-center text-white font-bold text-xl">
                                            {{ substr($listing->seller->name, 0, 1) }}
                                        </div>
                                        <div class="flex-1">
                                            <div class="font-bold text-gray-900">@if($listing->seller->role !== 'admin') <a href="{{ route('user.profile', $listing->seller) }}" class="hover:text @else <span class="text-gray-700 font-semibold" @endif-[#6A8F3B] underline-offset-2 hover:underline">{{ $listing->seller->role === 'admin' ? __('Seller') : $listing->seller->name }}</a></div>
                                            <div class="text-sm text-gray-600">
                                                {{ $listing->seller->addresses->first()->governorate ?? '' }}
                                                @if($listing->seller->addresses->first()->delegation)
                                                    - {{ $listing->seller->addresses->first()->delegation }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Map Container -->
                                    <div id="map" class="w-full h-96 rounded-xl border-2 border-gray-200" style="min-height: 384px; position: relative; z-index: 1;"></div>
                                    
                                    <!-- Distance Info -->
                                    <div class="mt-4 flex items-center justify-center gap-4">
                                        <div x-show="distance" class="flex items-center gap-2 flex-wrap" text-[#6A8F3B] font-bold">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                            </svg>
                                            <span>{{ __('Distance:') }} <span x-text="distance"></span> {{ __('km from your location') }}</span>
                                        </div>
                                    </div>
                                    
                                    @php
                                        $address = $listing->seller->addresses->first();
                                        $hasGPS = $address && $address->lat && $address->lng;
                                    @endphp
                                    
                                    @if($hasGPS)
                                        <div class="mt-4 text-sm text-green-600 text-center font-medium">
                                            <svg class="w-4 h-4 inline-block" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                            </svg>
                                            {{ __('Precise location determined via GPS') }}
                                        </div>
                                    @else
                                        <div class="mt-4 text-sm text-gray-500 text-center">
                                            <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ __('Approximate location based on seller data') }}
                                        </div>
                                    @endif
                                    
                                    <!-- Action Buttons -->
                                    <div class="mt-4 grid grid-cols-2 gap-3">
                                        <button @click="openInMaps()" class="px-6 py-3 bg-[#6A8F3B] text-white rounded-xl hover:bg-[#5a7a2f] transition font-bold flex items-center justify-center gap-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                            </svg>
                                            {{ __('Open in Maps') }}
                                        </button>
                                        <button @click="if(map) { map.remove(); map = null; } showMapModal = false;" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition font-bold">
                                            {{ __('Close') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="flex-1 text-center px-6 py-4 bg-[#6A8F3B] text-white rounded-xl hover:bg-[#5a7a2f] transition font-bold text-lg shadow-lg">
                                {{ __('Login to Contact') }}
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <div class="mt-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-6">{{ __('Similar Products') }}</h2>
            <div class="grid md:grid-cols-3 lg:grid-cols-4 gap-6">
                @php
                    $relatedListings = \App\Models\Listing::with(['product', 'seller'])
                        ->where('status', 'active')
                        ->where('id', '!=', $listing->id)
                        ->where(function($query) use ($listing) {
                            $query->whereHas('product', function($q) use ($listing) {
                                $q->where('type', $listing->product->type);
                            });
                        })
                        ->latest()
                        ->limit(4)
                        ->get();
                @endphp

                @foreach($relatedListings as $related)
                    <a href="{{ route('listings.show', $related) }}" class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition">
                        <div class="h-40 bg-gradient-to-br from-[#6A8F3B] to-[#C8A356] flex items-center justify-center">
                            <svg class="w-16 h-16 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($related->product->type === 'oil')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                                @endif
                            </svg>
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-gray-900 mb-2">{{ __($related->product->variety) }}</h3>
                            <div class="text-lg font-bold text-[#6A8F3B]">
                                {{ number_format($related->product->price, 2) }} {{ __('TND') }}
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('listingActions', () => ({
        showContactModal: false,
        showMapModal: false,
        isFavorite: false,
        map: null,
        userLat: null,
        userLng: null,
        distance: null,
        
        init() {
            // Check if listing is favorited
            const favorites = JSON.parse(localStorage.getItem('favorites') || '[]');
            this.isFavorite = favorites.includes({{ $listing->id }});
            
            // Get user's location for distance calculation
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition((position) => {
                    this.userLat = position.coords.latitude;
                    this.userLng = position.coords.longitude;
                    this.calculateDistance();
                });
            }
        },
        
        calculateDistance() {
            if (!this.userLat || !this.userLng) return;
            
            @if($listing->seller->addresses->first() && $listing->seller->addresses->first()->lat && $listing->seller->addresses->first()->lng)
                const sellerLat = {{ $listing->seller->addresses->first()->lat }};
                const sellerLng = {{ $listing->seller->addresses->first()->lng }};
                
                // Haversine formula to calculate distance
                const R = 6371; // Earth's radius in km
                const dLat = (sellerLat - this.userLat) * Math.PI / 180;
                const dLng = (sellerLng - this.userLng) * Math.PI / 180;
                const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                          Math.cos(this.userLat * Math.PI / 180) * Math.cos(sellerLat * Math.PI / 180) *
                          Math.sin(dLng/2) * Math.sin(dLng/2);
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                this.distance = (R * c).toFixed(1); // Distance in km
            @endif
        },
        
        toggleFavorite() {
            const favorites = JSON.parse(localStorage.getItem('favorites') || '[]');
            const index = favorites.indexOf({{ $listing->id }});
            if (index > -1) {
                favorites.splice(index, 1);
                this.isFavorite = false;
            } else {
                favorites.push({{ $listing->id }});
                this.isFavorite = true;
            }
            localStorage.setItem('favorites', JSON.stringify(favorites));
        },
        
        openLocationInfo() {
            @if($listing->seller->addresses->first() && $listing->seller->addresses->first()->lat && $listing->seller->addresses->first()->lng)
                // Has GPS coordinates - open map
                this.openMap();
            @else
                // No GPS coordinates - show address info alert
                @if($listing->seller->addresses->first())
                    const address = '{{ $listing->seller->addresses->first()->governorate ?? '' }}' + 
                                  ('{!! $listing->seller->addresses->first()->delegation ?? '' !!}' ? ', {!! $listing->seller->addresses->first()->delegation ?? '' !!}' : '');
                    alert('{{ __('Location') }}:\n' + address + '\n\n{{ __('GPS coordinates not available for this location.') }}');
                @else
                    alert('{{ __('No location information available for this seller.') }}');
                @endif
            @endif
        },
        
        openMap() {
            this.showMapModal = true;
            setTimeout(() => {
                if (!this.map) {
                    @if($listing->seller->addresses->first() && $listing->seller->addresses->first()->lat && $listing->seller->addresses->first()->lng)
                        try {
                            const lat = {{ $listing->seller->addresses->first()->lat }};
                            const lng = {{ $listing->seller->addresses->first()->lng }};
                            
                            // Initialize map
                            this.map = L.map('map').setView([lat, lng], 13);
                            
                            // Add tile layer
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '© OpenStreetMap contributors',
                                maxZoom: 19
                            }).addTo(this.map);
                            
                            // Add seller marker (red)
                            L.marker([lat, lng]).addTo(this.map)
                                .bindPopup('<b>{{ addslashes($listing->seller->name) }}</b><br>{{ addslashes($listing->seller->addresses->first()->governorate ?? '') }}')
                                .openPopup();
                            
                            // Add user marker (blue) if location available
                            if (this.userLat && this.userLng) {
                                const userIcon = L.divIcon({
                                    className: 'custom-user-marker',
                                    html: '<div style="background: #3B82F6; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3);"></div>',
                                    iconSize: [20, 20]
                                });
                                L.marker([this.userLat, this.userLng], {icon: userIcon}).addTo(this.map)
                                    .bindPopup('<b>{{ __('Your Current Location') }}</b>');
                                
                                // Draw line between user and seller
                                L.polyline([[this.userLat, this.userLng], [lat, lng]], {
                                    color: '#6A8F3B',
                                    weight: 3,
                                    opacity: 0.7,
                                    dashArray: '10, 10'
                                }).addTo(this.map);
                                
                                // Fit map to show both markers
                                const bounds = L.latLngBounds([[this.userLat, this.userLng], [lat, lng]]);
                                this.map.fitBounds(bounds, {padding: [50, 50]});
                            }
                            
                            // Force map to recalculate size
                            setTimeout(() => {
                                this.map.invalidateSize();
                            }, 100);
                        } catch (error) {
                            console.error('Error initializing map:', error);
                        }
                    @endif
                }
            }, 100);
        },
        
        openInMaps() {
            @if($listing->seller->addresses->first() && $listing->seller->addresses->first()->lat && $listing->seller->addresses->first()->lng)
                const lat = {{ $listing->seller->addresses->first()->lat }};
                const lng = {{ $listing->seller->addresses->first()->lng }};
                
                // Detect device and open appropriate map app
                const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
                const isApple = /iPhone|iPad|iPod/i.test(navigator.userAgent);
                
                if (isMobile) {
                    if (isApple) {
                        window.open(`maps://maps.apple.com/?q=${lat},${lng}`, '_blank');
                    } else {
                        window.open(`geo:${lat},${lng}?q=${lat},${lng}`, '_blank');
                    }
                } else {
                    window.open(`https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}`, '_blank');
                }
            @endif
        },
        
        scrollToLocation() {
            const locationEl = document.querySelector('[data-seller-location]');
            if (locationEl) {
                locationEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                locationEl.classList.add('ring-4', 'ring-[#C8A356]', 'ring-opacity-50');
                setTimeout(() => {
                    locationEl.classList.remove('ring-4', 'ring-[#C8A356]', 'ring-opacity-50');
                }, 2000);
            }
        }
    }));
});
</script>
@endpush
