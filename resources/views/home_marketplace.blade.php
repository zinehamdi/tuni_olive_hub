@extends('layouts.app')

@php
    $curLocale = app()->getLocale();
    $homeTitle = match($curLocale) {
        'ar' => 'السوق التونسي الأول لزيت الزيتون والخدمات الفلاحية',
        'fr' => '1ère Marketplace d\'Huile d\'Olive en Tunisie - B2B & Vrac',
        default => 'Leading Tunisian Olive Oil Marketplace & B2B Directory',
    };
    $homeDesc = match($curLocale) {
        'ar' => 'زين توب - المنصة الأولى لزيت الزيتون والدليل التجاري في تونس. تواصل مباشرة مع الفلاحين والمعاصر بدون عمولات.',
        'fr' => 'ZinToop - Première plateforme B2B et annuaire d\'huile d\'olive en Tunisie. Connectez-vous directement avec les producteurs et moulins sans commission.',
        default => 'ZinToop - Premier Tunisian Olive Oil Marketplace & B2B Directory. Source extra virgin and bulk olive oil directly from verified producers and mills without commissions.',
    };
@endphp

@section('title', $homeTitle)
@section('description', $homeDesc)
@section('og_title', $homeTitle)
@section('og_description', $homeDesc)

@section('content')
<div dir="{{ __('ltr') }}" class="min-h-screen" 
     x-data="marketplace"
     @keydown.escape.window="articleModalOpen = false"
     @mobile-menu-toggled.window="mobileMenuOpen = $event.detail.open">
    
    @if(session('status'))
        <div class="fixed top-24 left-1/2 -translate-x-1/2 z-[150] w-full max-w-md px-4" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
            <div class="bg-gray-900 text-white p-6 rounded-[2rem] shadow-2xl border-4 border-[#6A8F3B] flex items-center gap-4 animate-bounce-short">
                <div class="w-12 h-12 rounded-full bg-[#6A8F3B] flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                </div>
                <p class="font-bold text-sm">{{ session('status') }}</p>
                <button @click="show = false" class="ml-auto text-white/50 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
        </div>
    @endif

    <!-- Header with Login/Register -->
    <header class="hidden bg-white/95 backdrop-blur border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-4">
            <div class="flex items-center justify-between gap-4">
                <!-- Logo -->
                <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                    <div class="h-20 w-20 rounded-full overflow-hidden shadow-lg group-hover:shadow-xl transition-all">
                        <img src="{{ asset('images/zintoop-logo.png') }}" alt="Tunisian Olive Oil Platform" class="h-full w-full object-cover scale-125 translate-y-2 group-hover:scale-[1.35] transition-transform">
                    </div>
                    <div>
                        <div class="text-xl font-bold text-gray-900">{{ __('Tunisian Olive Oil Platform') }}</div>
                        <div class="text-xs text-gray-600">Tunisian Olive Oil Platform</div>
                    </div>
                </a>

                <!-- Navigation Menu -->
                <nav class="hidden md:flex items-center gap-6">
                    <a href="{{ url('/') }}" class="text-gray-700 hover:text-[#6A8F3B] font-semibold transition">
                        {{ __('Home') }}
                    </a>
                    <a href="#products" class="text-gray-700 hover:text-[#6A8F3B] font-semibold transition">
                        {{ __('Products') }}
                    </a>
                    @auth
                        <a href="{{ route('listings.create') }}" class="text-gray-700 hover:text-[#6A8F3B] font-semibold transition">
                            {{ __('Add Listing') }}
                        </a>
                    @endauth
                    <a href="{{ route('about') }}" class="text-gray-700 hover:text-[#6A8F3B] font-semibold transition">
                        {{ __('About') }}
                    </a>
                </nav>

                <!-- Mobile Menu Toggle -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

            <!-- Mobile Menu -->
            <div x-show="mobileMenuOpen" x-transition class="md:hidden mt-4 pb-4 border-t pt-4">
                <nav class="space-y-2">
                    <a href="{{ url('/') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-lg font-semibold">{{ __('Home') }}</a>
                    <a href="#products" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-lg font-semibold">{{ __('Products') }}</a>
                    @auth
                        <a href="{{ route('listings.create') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-lg font-semibold">{{ __('Add Listing') }}</a>
                    @endauth
                    <a href="{{ route('about') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-lg font-semibold">{{ __('About') }}</a>
                </nav>
            </div>
        </div>
    </header>

    @php
        $slideUrls = (isset($heroSlides) && $heroSlides->isNotEmpty())
            ? $heroSlides->map(fn($s) => $s->image_url)->toArray()
            : [asset('images/hero_slide_1.png'), asset('images/hero_slide_2.png'), asset('images/hero_slide_3.png')];
    @endphp

    <!-- Hero Section with Slideshow and Search -->
    <section class="relative bg-black text-white py-8 px-4 overflow-hidden"
             x-data="{ 
                 slides: {{ json_encode($slideUrls) }},
                 currentSlide: 0,
                 init() {
                     setInterval(() => {
                         this.currentSlide = (this.currentSlide + 1) % this.slides.length;
                     }, 6000);
                 }
             }">
        <!-- Static Initial Slide (Instant LCP) -->
        <div class="absolute inset-0 bg-cover bg-center transition-all duration-[6000ms] ease-linear"
             :class="currentSlide === 0 ? 'opacity-100 scale-110' : 'opacity-0 scale-100'"
             style="background-image: url('{{ $slideUrls[0] ?? asset('images/hero_slide_1.png') }}'); transition-property: opacity, transform;"></div>

        <!-- Dynamic Subsequent Slides -->
        <template x-for="(slide, index) in slides" :key="index">
            <div x-show="index > 0"
                 class="absolute inset-0 bg-cover bg-center transition-all duration-[6000ms] ease-linear"
                 :class="currentSlide === index ? 'opacity-100 scale-110' : 'opacity-0 scale-100'"
                 :style="`background-image: ${currentSlide === index ? 'url(\'' + slide + '\')' : 'none'}; transition-property: opacity, transform;`"></div>
        </template>
        
        <!-- Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-b from-[#6A8F3B]/70 via-black/40 to-black/80 z-0 pointer-events-none"></div>
        <div class="max-w-7xl mx-auto relative z-10">
            <div class="text-center mb-8">
                <!-- Hero Animation Inline -->
                <div class="relative flex flex-col items-center justify-center py-8 px-4 text-center space-y-6 bg-transparent w-full max-w-4xl mx-auto">
                    <!-- Palestine Solidarity: One-Side Rounded Royal Green Glassmorphic Pill -->
                    <div class="inline-flex items-center gap-2.5 px-5 py-2 rounded-s-full rounded-e-md bg-gradient-to-r from-[#032e16]/95 via-[#064e24]/90 to-[#032e16]/95 backdrop-blur-md border border-emerald-400/40 text-white shadow-[0_8px_25px_rgba(4,46,22,0.45)] hover:border-emerald-300/70 transition-all duration-300 transform hover:scale-[1.02]">
                        <span class="inline-flex items-center justify-center text-base filter drop-shadow">🇵🇸</span>
                        <span class="font-black text-emerald-200 tracking-wide text-xs sm:text-sm">نحن نقف مع فلسطين</span>
                        <span class="text-emerald-400/50">•</span>
                        <span class="text-white/95 font-semibold text-[11px] sm:text-xs">We Stand with Palestine</span>
                        <span class="text-emerald-400/50 hidden md:inline">•</span>
                        <span class="text-white/85 font-medium text-[11px] sm:text-xs hidden md:inline">Nous sommes avec la Palestine</span>
                        <span class="text-emerald-300 text-xs">🌿</span>
                    </div>

                    <!-- English Slogan -->
                    <div>
                        <p class="text-white font-black text-base md:text-2xl uppercase tracking-[0.25em] drop-shadow-lg">
                            Zin Tunisian Olive Oil Platform
                        </p>
                    </div>

                    <!-- Main Brand: ZinToop -->
                    <div class="relative group">
                        <h1 id="zintoop-brand" class="text-6xl sm:text-7xl md:text-9xl font-black tracking-tighter text-[#C8A356] bg-clip-text bg-gradient-to-r from-[#C8A356] via-[#FFF9E0] to-[#C8A356] drop-shadow-2xl">
                            ZinToop
                        </h1>
                        <!-- Subtle Glow -->
                        <div class="absolute -inset-8 bg-[#C8A356]/20 blur-3xl rounded-full -z-10"></div>
                    </div>

                    <!-- Arabic Slogan -->
                    <div>
                        <p class="text-[#C8A356] font-black text-2xl sm:text-3xl md:text-5xl drop-shadow-lg" dir="rtl">
                            منصة الزين لزيت الزيتون التونسي
                        </p>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex flex-wrap justify-center gap-4 mb-8">
                    <!-- Browse Products Shortcut -->
                    <button @click="document.getElementById('products').scrollIntoView({ behavior: 'smooth' })" 
                            class="group flex items-center gap-3 px-6 py-3 bg-[#C8A356] text-white rounded-full border-2 border-white/20 hover:bg-[#b08a3c] transition-all shadow-xl hover:shadow-[0_20px_40px_-10px_rgba(200,163,86,0.5)] transform hover:-translate-y-1 active:scale-95">
                        <span class="font-black uppercase tracking-widest text-[10px]">{{ __('Browse Products') }}</span>
                        <div class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center group-hover:bg-white/40 transition">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                            </svg>
                        </div>
                    </button>


                    <!-- Olive Varieties Article Shortcut -->
                    <a href="{{ route('article.varieties') }}" 
                       class="group flex items-center gap-3 px-6 py-3 bg-[#6A8F3B] text-white rounded-full border-2 border-white/20 hover:bg-[#5a7a2f] transition-all shadow-xl hover:shadow-[0_20px_40px_-10px_rgba(106,143,59,0.5)] transform hover:-translate-y-1 active:scale-95">
                        <span class="font-black uppercase tracking-widest text-[10px]">تعرف على أصناف الزيتون التونسي</span>
                        <div class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center group-hover:bg-white/40 transition">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Search Bar with Location -->
            <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-2xl p-2" data-bot-explain="{{ __('Here you can search for products and locate yourself to find nearby offers!') }}">
                <div class="flex flex-col md:flex-row gap-2">
                    <div class="flex-1 relative">
                        <input type="text" 
                               x-model="searchQuery"
                               @input="filterListings"
                               placeholder="{{ __('Search for product (oil, olive, shemlali...)') }}"
                               class="w-full px-4 py-3 pr-12 rounded-xl border-2 border-gray-200 focus:border-[#6A8F3B] focus:outline-none text-gray-900">
                        <svg class="absolute right-4 top-4 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <button @click="getMyLocation" class="px-6 py-3 bg-[#C8A356] text-white rounded-xl hover:bg-[#b08a3c] transition font-bold flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="hidden md:inline">{{ __('Near Me') }}</span>
                    </button>
                    <button @click="filterListings(); setTimeout(() => { document.getElementById('products').scrollIntoView({ behavior: 'smooth', block: 'start' }); }, 100);" 
                            class="px-8 py-3 bg-[#6A8F3B] text-white rounded-xl hover:bg-[#5a7a2f] transition font-bold">
                        {{ __('Search') }}
                    </button>
                </div>
                
                <!-- Location Status -->
                <div x-show="userLocation.lat" class="mt-2 px-4 py-2 bg-green-50 text-green-700 rounded-lg text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ __('Location identified - searching by proximity') }}</span>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mt-8 max-w-6xl mx-auto" data-bot-explain="{{ __('These are some quick stats about the platform') }}">
                <div class="bg-white/10 backdrop-blur rounded-xl p-4 text-center">
                    @php
                        $visitsCount = (int)($platformStats['visits'] ?? 0);
                        $formattedVisits = $visitsCount >= 1000 ? (number_format($visitsCount / 1000, 1) . 'K') : number_format($visitsCount);
                    @endphp
                    <div class="text-3xl font-bold">{{ $formattedVisits }}</div>
                    <div class="text-sm text-white/80">{{ __('Total Visits') }}</div>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-xl p-4 text-center">
                    <div class="text-3xl font-bold">{{ number_format($platformStats['users'] ?? 0) }}</div>
                    <div class="text-sm text-white/80">{{ __('Registered Users') }}</div>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-xl p-4 text-center">
                    <div class="text-3xl font-bold">{{ number_format($platformStats['producers'] ?? 0) }}</div>
                    <div class="text-sm text-white/80">{{ __('Farmers') }}</div>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-xl p-4 text-center">
                    <div class="text-3xl font-bold">{{ number_format($platformStats['mills'] ?? 0) }}</div>
                    <div class="text-sm text-white/80">{{ __('Olive Mills') }}</div>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-xl p-4 text-center">
                    <div class="text-3xl font-bold">{{ number_format($platformStats['packers'] ?? 0) }}</div>
                    <div class="text-sm text-white/80">{{ __('Packers') }}</div>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-xl p-4 text-center">
                    <div class="text-3xl font-bold">{{ number_format($platformStats['listings'] ?? 0) }}</div>
                    <div class="text-sm text-white/80">{{ __('Active listings') }}</div>
                </div>
            </div>
        </div>

    </section>

    <!-- Live Deals & Opportunities Section -->
    @if(isset($deals) && $deals->count() > 0)
    <section id="deals" class="max-w-7xl mx-auto px-4 pt-10 pb-2 overflow-hidden">
        <div class="flex flex-col md:flex-row items-center justify-between mb-6 gap-4">
            <div class="relative">
                <div class="absolute -top-4 {{ __('left-0') }} w-10 h-1 bg-[#6A8F3B] rounded-full"></div>
                <h2 class="text-2xl font-black text-gray-900 tracking-tight">{{ __('Live Marketplace Opportunities') }}</h2>
            </div>
            <div class="hidden md:flex items-center gap-2 px-3 py-1 bg-gray-100 rounded-full">
                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                <span class="text-[10px] font-black uppercase text-gray-500 tracking-widest">{{ __('Live Feed') }}</span>
            </div>
        </div>

        <!-- Deals Horizontal Container -->
        <div class="relative group" 
             x-data="{ 
                scroll() { 
                    const el = this.$refs.dealScroll;
                    if (el.scrollLeft >= (el.scrollWidth - el.clientWidth)) {
                        el.scrollLeft = 0;
                    } else {
                        el.scrollLeft += 1;
                    }
                },
                interval: null,
                startScroll() {
                    this.interval = setInterval(() => this.scroll(), 30);
                },
                stopScroll() {
                    clearInterval(this.interval);
                }
             }" 
             x-init="startScroll()" 
             @mouseenter="stopScroll()" 
             @mouseleave="startScroll()"
             @touchstart="stopScroll()"
             @touchend="startScroll()">
            <div x-ref="dealScroll" class="flex gap-6 overflow-x-auto pb-6 pt-2 scrollbar-hide snap-x snap-mandatory">
                @foreach($deals as $deal)
                <div class="min-w-[320px] md:min-w-[420px] snap-center">
                    <!-- Main Card (Shrunk) -->
                    <div class="h-full bg-gradient-to-br from-[#1b381c] to-[#0d1f0e] rounded-[1.5rem] border border-[#6A8F3B]/30 hover:border-[#6A8F3B]/60 shadow-md hover:shadow-xl transition-all duration-300 p-5 flex flex-col hover:-translate-y-1 relative overflow-hidden">
                        
                        <!-- Color Stripe -->
                        <div class="absolute top-0 {{ __('left-0') }} bottom-0 w-1.5 
                            {{ $deal->type === 'demand' ? 'bg-amber-400' : ($deal->type === 'service' ? 'bg-blue-400' : 'bg-emerald-400') }}"></div>

                        <!-- Header -->
                        <div class="flex items-center justify-between mb-3">
                            <span class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest border 
                                {{ $deal->type === 'demand' ? 'text-amber-300 bg-amber-400/10 border-amber-400/20' : ($deal->type === 'service' ? 'text-blue-300 bg-blue-400/10 border-blue-400/20' : 'text-emerald-300 bg-emerald-400/10 border-emerald-400/20') }}">
                                {{ __($deal->type) }}
                            </span>
                            <span class="text-[10px] font-bold text-white/40">{{ $deal->created_at->diffForHumans() }}</span>
                        </div>

                        <!-- Title (Smaller) -->
                        <h3 class="text-base font-bold text-white mb-2 line-clamp-2 min-h-[2.5rem] leading-snug group-hover:text-[#a8d060] transition-colors">
                            {{ $deal->title[app()->getLocale()] ?? $deal->title['ar'] ?? 'N/A' }}
                        </h3>

                        <!-- Description (Smaller) -->
                        <p class="text-white/70 text-xs leading-relaxed mb-4 flex-1 line-clamp-2 font-medium">
                            {{ $deal->description[app()->getLocale()] ?? $deal->description['ar'] ?? '' }}
                        </p>

                        <!-- Action Button (Smaller) -->
                        <button @click="openDealRequest({{ $deal->id }}, '{{ addslashes($deal->title[app()->getLocale()] ?? $deal->title['ar']) }}', '{{ $deal->type }}')" 
                                class="w-full py-2.5 bg-gradient-to-r from-[#6A8F3B] to-[#5a7a2f] hover:from-[#5a7a2f] hover:to-[#4a6425] text-white rounded-xl font-bold text-[11px] transition-all transform active:scale-95 flex items-center justify-center gap-2 shadow-md shadow-[#6A8F3B]/10 hover:shadow-[#6A8F3B]/20">
                            <span>{{ __('Interested? Send Request') }}</span>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Shadow Gradients for indication of more items -->
            <div class="absolute inset-y-0 left-0 w-12 bg-gradient-to-r from-white/80 to-transparent pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="absolute inset-y-0 right-0 w-12 bg-gradient-to-l from-white/80 to-transparent pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity"></div>
        </div>
    </section>

    <!-- Deal Request Modal -->
    <div x-show="dealRequestModalOpen" 
         x-cloak
         class="fixed inset-0 z-[110] flex items-center justify-center p-4 sm:p-6"
         role="dialog"
         aria-modal="true">
        
        <div x-show="dealRequestModalOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"
             @click="dealRequestModalOpen = false"></div>

        <div x-show="dealRequestModalOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="relative w-full max-w-xl bg-white rounded-[2.5rem] shadow-[0_30px_100px_-20px_rgba(0,0,0,0.3)] overflow-hidden">
            
            <div class="p-8">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-2xl font-black text-gray-900">{{ __('Request Information') }}</h3>
                    <button @click="dealRequestModalOpen = false" class="text-gray-400 hover:text-gray-900 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div class="mb-6 p-4 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
                    <span class="text-xs font-black uppercase text-gray-400 tracking-wider">{{ __('Deal') }}</span>
                    <p class="font-bold text-gray-900 mt-1" x-text="currentDeal.title"></p>
                </div>

                <form :action="'/deals/' + currentDeal.id + '/request'" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <input type="text" name="name" required placeholder="{{ __('Your Name') }}" class="w-full px-5 py-4 rounded-2xl bg-gray-100 border-none focus:ring-2 focus:ring-[#6A8F3B] font-medium transition">
                        <input type="text" name="phone" required placeholder="{{ __('Phone Number') }}" class="w-full px-5 py-4 rounded-2xl bg-gray-100 border-none focus:ring-2 focus:ring-[#6A8F3B] font-medium transition">
                    </div>
                    <input type="email" name="email" required placeholder="{{ __('Email Address') }}" class="w-full px-5 py-4 rounded-2xl bg-gray-100 border-none focus:ring-2 focus:ring-[#6A8F3B] font-medium transition">
                    
                    <div>
                        <label class="block text-xs font-black uppercase text-gray-400 tracking-wider mb-3">{{ __('Your Requirements') }}</label>
                        <div class="flex flex-wrap gap-2">
                            <label class="cursor-pointer">
                                <input type="checkbox" name="requirements[]" value="quantity_low" class="sr-only peer">
                                <span class="px-4 py-2 rounded-xl bg-gray-100 peer-checked:bg-[#6A8F3B] peer-checked:text-white text-xs font-bold transition-all inline-block">{{ __('Small Quantity') }}</span>
                            </label>
                            <label class="cursor-pointer">
                                <input type="checkbox" name="requirements[]" value="quantity_high" class="sr-only peer">
                                <span class="px-4 py-2 rounded-xl bg-gray-100 peer-checked:bg-[#6A8F3B] peer-checked:text-white text-xs font-bold transition-all inline-block">{{ __('Wholesale/Large') }}</span>
                            </label>
                            <label class="cursor-pointer">
                                <input type="checkbox" name="requirements[]" value="urgent" class="sr-only peer">
                                <span class="px-4 py-2 rounded-xl bg-gray-100 peer-checked:bg-[#6A8F3B] peer-checked:text-white text-xs font-bold transition-all inline-block">{{ __('Urgent Need') }}</span>
                            </label>
                        </div>
                    </div>

                    <textarea name="message" rows="3" placeholder="{{ __('Additional notes or questions...') }}" class="w-full px-5 py-4 rounded-2xl bg-gray-100 border-none focus:ring-2 focus:ring-[#6A8F3B] font-medium transition"></textarea>

                    <button type="submit" class="w-full py-4 bg-[#6A8F3B] text-white rounded-2xl font-bold hover:shadow-2xl transition-all transform active:scale-95 flex items-center justify-center gap-2">
                        <span>{{ __('Submit Request') }}</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    </button>
                    
                    <p class="text-[10px] text-gray-400 text-center uppercase tracking-widest font-bold pt-2">
                        {{ __('Admin will review and contact you shortly') }}
                    </p>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Latest Articles / Knowledge Hub Carousel Section -->
    @if(isset($articles) && $articles->count() > 0)
    <section class="max-w-7xl mx-auto px-4 pt-12 pb-6" x-data="{
        scroll(direction) {
            const container = this.$refs.articlesSlider;
            const scrollAmount = container.clientWidth * 0.75;
            const isRTL = document.documentElement.dir === 'rtl';
            const multiplier = isRTL ? -direction : direction;
            container.scrollBy({ left: multiplier * scrollAmount, behavior: 'smooth' });
        }
    }">
        <div class="flex items-center justify-between mb-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#6A8F3B]/10 text-[#6A8F3B] text-xs font-bold uppercase tracking-wider mb-2">
                    <span>📚</span>
                    <span>{{ __('Guides & Market Insights') }}</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">{{ __('Latest Articles') }}</h2>
            </div>
            
            <!-- Navigation Controls (Desktop & Mobile) -->
            <div class="flex items-center gap-2">
                <button @click="scroll(-1)" 
                        type="button"
                        aria-label="Previous articles"
                        class="w-10 h-10 rounded-full bg-white border border-gray-200 hover:border-[#6A8F3B] hover:bg-[#6A8F3B] hover:text-white text-gray-700 shadow-sm flex items-center justify-center transition-all duration-300 transform active:scale-95">
                    <svg class="w-5 h-5 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <button @click="scroll(1)" 
                        type="button"
                        aria-label="Next articles"
                        class="w-10 h-10 rounded-full bg-white border border-gray-200 hover:border-[#6A8F3B] hover:bg-[#6A8F3B] hover:text-white text-gray-700 shadow-sm flex items-center justify-center transition-all duration-300 transform active:scale-95">
                    <svg class="w-5 h-5 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Articles Swipeable Carousel -->
        <div x-ref="articlesSlider" 
             class="flex gap-5 sm:gap-6 overflow-x-auto snap-x snap-mandatory scroll-smooth pb-6 pt-2 px-1 scrollbar-hide -mx-4 px-4 sm:mx-0 sm:px-0">
            @foreach($articles as $article)
            <div class="w-[84vw] sm:w-[340px] md:w-[370px] snap-start flex-shrink-0 flex flex-col">
                <div class="group bg-white rounded-3xl shadow-[0_15px_40px_-15px_rgba(0,0,0,0.1)] border border-gray-100/80 hover:border-[#6A8F3B]/30 hover:shadow-[0_25px_50px_-12px_rgba(106,143,59,0.2)] transition-all duration-500 overflow-hidden h-full flex flex-col">
                    <a href="{{ route('articles.show', $article->id) }}" class="aspect-[16/9] bg-gradient-to-br from-[#183b1c] to-[#6A8F3B] relative overflow-hidden block">
                        <img src="{{ Str::startsWith($article->image, ['http://', 'https://']) ? $article->image : (Str::startsWith($article->image, 'images/') ? asset($article->image) : (Str::startsWith($article->image, 'storage/') ? asset($article->image) : asset('images/' . $article->image))) }}" 
                             onerror="this.onerror=null;this.src='{{ asset('images/hero_slide_1.png') }}'" 
                             alt="{{ $article->title[app()->getLocale()] ?? 'Article' }}" 
                             loading="lazy"
                             decoding="async"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                        @if(isset($article->category[app()->getLocale()]))
                        <div class="absolute top-3.5 {{ app()->getLocale() === 'ar' ? 'right-3.5' : 'left-3.5' }} bg-white/95 backdrop-blur-md px-3 py-1 rounded-full text-[11px] font-extrabold text-[#183b1c] shadow-md border border-white/50">
                            {{ $article->category[app()->getLocale()] }}
                        </div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    </a>
                    <div class="p-5 sm:p-6 flex-1 flex flex-col">
                        <div class="flex items-center gap-2 text-[11px] text-gray-400 font-bold uppercase tracking-wider mb-2.5">
                            <svg class="w-3.5 h-3.5 text-[#6A8F3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            <span>{{ $article->created_at ? $article->created_at->format('M d, Y') : now()->format('M d, Y') }}</span>
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 mb-2.5 group-hover:text-[#6A8F3B] transition-colors leading-snug line-clamp-2">
                            {{ $article->title[app()->getLocale()] ?? '' }}
                        </h3>
                        <p class="text-gray-500 text-xs sm:text-sm line-clamp-2 mb-4 leading-relaxed">
                            {{ Str::limit(strip_tags($article->content[app()->getLocale()] ?? ''), 120) }}
                        </p>
                        
                        <div class="mt-auto pt-3 border-t border-gray-100 flex items-center justify-between">
                            <a href="{{ route('articles.show', $article->id) }}" 
                               class="inline-flex items-center gap-1.5 text-[#6A8F3B] font-bold text-xs sm:text-sm group/btn hover:text-[#183b1c] transition-colors">
                                <span>{{ __('Read Article') }}</span>
                                <svg class="w-4 h-4 rtl:rotate-180 transition-transform duration-300 group-hover/btn:translate-x-1 rtl:group-hover/btn:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                            <span class="text-[10px] font-bold text-gray-400 bg-gray-100 px-2 py-0.5 rounded-md">
                                #{{ $article->id }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    <!-- Main Content -->
    <section id="products" class="max-w-7xl mx-auto px-4 py-12">
        
        <!-- Product Listings Wrapper -->
        <div class="w-full">
                <!-- Marketplace Mission Section -->
                <div class="mb-10 bg-gradient-to-br from-[#6A8F3B]/5 to-[#C8A356]/5 border border-[#6A8F3B]/10 rounded-2xl p-6 text-center relative overflow-hidden">
                    <div class="relative z-10 max-w-3xl mx-auto">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white shadow-sm border border-gray-100 mb-4">
                            <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-gray-600">
                                {{ __('100% Free Marketplace') }}
                            </span>
                        </div>
                        
                        <h2 class="text-xl md:text-2xl font-extrabold text-gray-900 mb-4 leading-tight">
                            {{ __('Your free catalog and guide of Tunisian olive oil producers and brands') }} 🇹🇳
                        </h2>
                        
                        <p class="text-gray-600 text-sm leading-relaxed mb-6">
                            {{ __('A platform for farmers, millers, and packers to share their products with') }} <span class="font-bold text-[#6A8F3B]">{{ __('ZERO fees') }}</span>. 
                            {!! __('Deals made directly between users carry <span class="font-bold">no commissions</span>. We aim to promote and guide buyers to authentic producers.') !!}
                        </p>

                        <div class="flex flex-wrap justify-center gap-3">
                            <div class="flex items-center gap-2 px-3 py-1.5 bg-white rounded-lg shadow-sm border border-gray-50">
                                <span class="text-lg">🚜</span>
                                <span class="text-xs font-semibold text-gray-500">{{ __('For Farmers') }}</span>
                            </div>
                            <div class="flex items-center gap-2 px-3 py-1.5 bg-white rounded-lg shadow-sm border border-gray-50">
                                <span class="text-lg">⚙️</span>
                                <span class="text-xs font-semibold text-gray-500">{{ __('For Millers') }}</span>
                            </div>
                            <div class="flex items-center gap-2 px-3 py-1.5 bg-white rounded-lg shadow-sm border border-gray-50">
                                <span class="text-lg">📦</span>
                                <span class="text-xs font-semibold text-gray-500">{{ __('For Packers') }}</span>
                            </div>
                        </div>
                    </div>
                </div>



        <!-- Mobile Filter Toggle Button - Elegant and Positioned Above Grid -->
        <div class="lg:hidden mb-6">
            <div class="flex items-center gap-2">
                <button @click="showFilters = !showFilters" 
                        class="flex-1 flex items-center justify-between px-5 py-3.5 bg-white border border-gray-150 text-[#6A8F3B] rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 font-bold group">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-[#6A8F3B]/10 flex items-center justify-center group-hover:bg-[#6A8F3B] group-hover:text-white transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                        </div>
                        <span>{{ __('Filter Results') }}</span>
                        <template x-if="hasActiveFilters">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#6A8F3B] animate-pulse"></span>
                        </template>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-300" :class="showFilters ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <button x-show="hasActiveFilters" 
                        @click="resetFilters(); if (window.innerWidth < 1024) showFilters = false;" 
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-90"
                        x-transition:enter-end="opacity-100 scale-100"
                        class="px-4 py-3.5 bg-gray-600 hover:bg-gray-700 text-white rounded-2xl font-bold text-xs shadow-sm transition active:scale-95 flex items-center gap-1.5 whitespace-nowrap cursor-pointer">
                    <span>🔄</span>
                    <span>{{ __('Reset') }}</span>
                </button>
            </div>
        </div>


        <!-- Horizontal Filter Bar -->
        <div class="filter-container bg-white/95 backdrop-blur-md border border-gray-150 rounded-2xl p-4 shadow-md mb-4 lg:sticky lg:top-[124px] lg:z-30 relative z-20"
             x-show="showFilters"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 items-end text-right" dir="{{ __('ltr') }}">
                
                <!-- 1. Distance Filter -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2 flex items-center gap-1.5 justify-end">
                        <svg class="w-4 h-4 text-[#6A8F3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        </svg>
                        {{ __('Distance') }}
                    </label>
                    <select x-model="filters.distance" @change="filterListings" class="w-full bg-gray-50 border border-gray-250 rounded-xl px-4 py-2 text-gray-800 text-sm focus:outline-none focus:border-[#6A8F3B] appearance-none bg-[url('data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2020%2020%22%20fill%3D%22%23718096%22%3E%3Cpath%20fill-rule%3D%22evenodd%22%20d%3D%22M5.293%207.293a1%201%200%20011.414%200L10%2010.586l3.293-3.293a1%201%200%20111.414%201.414l-4%204a1%201%200%2001-1.414%200l-4-4a1%201%200%20010-1.414z%22%20clip-rule%3D%22evenodd%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem_1.25rem] bg-no-repeat rtl:bg-[left_1rem_center] ltr:bg-[right_1rem_center] rtl:pl-10 ltr:pr-10">
                        <option value="all">{{ __('All distances') }}</option>
                        <option value="10">{{ __('Less than 10 km') }}</option>
                        <option value="25">{{ __('Less than 25 km') }}</option>
                        <option value="50">{{ __('Less than 50 km') }}</option>
                        <option value="100">{{ __('Less than 100 km') }}</option>
                    </select>
                </div>

                <!-- 2. Product Type Filter -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2 flex items-center gap-1.5 justify-end">
                        <span>🚜</span>
                        {{ __('Product Type') }}
                    </label>
                    <select x-model="filters.type" @change="filterListings" class="w-full bg-gray-50 border border-gray-250 rounded-xl px-4 py-2 text-gray-800 text-sm focus:outline-none focus:border-[#6A8F3B] appearance-none bg-[url('data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2020%2020%22%20fill%3D%22%23718096%22%3E%3Cpath%20fill-rule%3D%22evenodd%22%20d%3D%22M5.293%207.293a1%201%200%20011.414%200L10%2010.586l3.293-3.293a1%201%200%20111.414%201.414l-4%204a1%201%200%2001-1.414%200l-4-4a1%201%200%20010-1.414z%22%20clip-rule%3D%22evenodd%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem_1.25rem] bg-no-repeat rtl:bg-[left_1rem_center] ltr:bg-[right_1rem_center] rtl:pl-10 ltr:pr-10">
                        <option value="all">{{ __('All') }}</option>
                        <option value="oil">🫗 {{ __('Olive Oil') }}</option>
                        <option value="olive">🫒 {{ __('Olives') }}</option>
                    </select>
                </div>

                <!-- 3. Quality Filter -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2 flex items-center gap-1.5 justify-end">
                        <span>⭐</span>
                        {{ __('Quality') }}
                    </label>
                    <select @change="
                        const val = $event.target.value;
                        filters.qualities = val === 'all' ? [] : [val];
                        filterListings();
                    " class="w-full bg-gray-50 border border-gray-250 rounded-xl px-4 py-2.5 text-gray-800 text-sm focus:outline-none focus:border-[#6A8F3B] appearance-none bg-[url('data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2020%2020%22%20fill%3D%22%23718096%22%3E%3Cpath%20fill-rule%3D%22evenodd%22%20d%3D%22M5.293%207.293a1%201%200%20011.414%200L10%2010.586l3.293-3.293a1%201%200%20111.414%201.414l-4%204a1%201%200%2001-1.414%200l-4-4a1%201%200%20010-1.414z%22%20clip-rule%3D%22evenodd%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem_1.25rem] bg-no-repeat rtl:bg-[left_1rem_center] ltr:bg-[right_1rem_center] rtl:pl-10 ltr:pr-10 font-semibold">
                        <option value="all">{{ __('All qualities') }}</option>
                        <option value="bio">🌿 {{ __('Bio') }}</option>
                        <option value="evoo">⭐ {{ __('Extra Virgin (EVOO)') }}</option>
                        <option value="virgin">🏆 {{ __('Virgin') }}</option>
                        <option value="raffinee">📦 {{ __('Raffinée') }}</option>
                        <option value="pomace">🫒 {{ __('Pomace') }}</option>
                    </select>
                </div>

                <!-- 4. Price Range Filter -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2 flex items-center gap-1.5 justify-end">
                        <span>💵</span>
                        {{ __('Price Range') }}
                    </label>
                    <div class="flex gap-2">
                        <input type="number" x-model="filters.priceMin" @input="filterListings" placeholder="{{ __('Min') }}" class="w-full bg-gray-50 border border-gray-250 rounded-xl px-3 py-2 text-gray-800 text-sm focus:outline-none focus:border-[#6A8F3B] text-center">
                        <input type="number" x-model="filters.priceMax" @input="filterListings" placeholder="{{ __('Max') }}" class="w-full bg-gray-50 border border-gray-250 rounded-xl px-3 py-2 text-gray-800 text-sm focus:outline-none focus:border-[#6A8F3B] text-center">
                    </div>
                </div>

                <!-- 5. Sort Options -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2 flex items-center gap-1.5 justify-end">
                        <span>📊</span>
                        {{ __('Sort by') }}
                    </label>
                    <select x-model="filters.sortBy" @change="filterListings" class="w-full bg-gray-50 border border-gray-250 rounded-xl px-4 py-2 text-gray-800 text-sm focus:outline-none focus:border-[#6A8F3B] appearance-none bg-[url('data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2020%2020%22%20fill%3D%22%23718096%22%3E%3Cpath%20fill-rule%3D%22evenodd%22%20d%3D%22M5.293%207.293a1%201%200%20011.414%200L10%2010.586l3.293-3.293a1%201%200%20111.414%201.414l-4%204a1%201%200%2001-1.414%200l-4-4a1%201%200%20010-1.414z%22%20clip-rule%3D%22evenodd%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem_1.25rem] bg-no-repeat rtl:bg-[left_1rem_center] ltr:bg-[right_1rem_center] rtl:pl-10 ltr:pr-10">
                        <option value="nearest">{{ __('Nearest to me') }}</option>
                        <option value="newest">{{ __('Newest') }}</option>
                        <option value="oldest">{{ __('Oldest') }}</option>
                        <option value="price_low">{{ __('Price: Low to High') }}</option>
                        <option value="price_high">{{ __('Price: High to Low') }}</option>
                    </select>
                </div>

                <!-- 6. Control Actions -->
                <div class="flex gap-2">
                    <button @click="getMyLocation" class="flex-1 py-2.5 bg-[#C8A356] hover:bg-[#b08a3c] text-white rounded-xl font-bold text-xs transition duration-200 cursor-pointer shadow hover:shadow-md active:scale-95 flex items-center justify-center gap-1">
                        <span>📍</span>
                        <span class="truncate">{{ __('Location') }}</span>
                    </button>
                    <button @click="resetFilters" class="flex-1 py-2.5 bg-gray-600 hover:bg-gray-700 text-white rounded-xl font-bold text-xs transition duration-200 cursor-pointer shadow hover:shadow-md active:scale-95 flex items-center justify-center gap-1">
                        <span>🔄</span>
                        <span class="truncate">{{ __('Reset') }}</span>
                    </button>
                </div>

            </div>
        </div>


                <!-- View Toggle & Results Count -->
                <div class="flex justify-between items-center mb-6 flex-wrap gap-4">
                    <div class="text-gray-700">
                        <span class="font-bold text-2xl" x-text="filteredListings.length"></span>
                        <span class="text-lg">{{ __('products available') }}</span>
                        <span x-show="userLocation.lat" class="text-sm text-[#6A8F3B] font-semibold mr-2">
                            ({{ __('near you') }})
                        </span>
                    </div>
                    <div class="flex gap-2">
                        <button @click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'bg-[#6A8F3B] text-white' : 'bg-gray-200 text-gray-700'" class="p-3 rounded-lg transition shadow">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                        </button>
                        <button @click="viewMode = 'list'" :class="viewMode === 'list' ? 'bg-[#6A8F3B] text-white' : 'bg-gray-200 text-gray-700'" class="p-3 rounded-lg transition shadow">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Products Grid View -->
                <div x-show="viewMode === 'grid'" class="grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <template x-for="listing in visibleListings" :key="listing.id">
                        <div :id="'listing-item-' + listing.id" class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 listing-card-item" data-bot-explain="{{ __('Click View Details to learn more about this product and how to buy') }}">
                            <!-- Product Image / Seller Logo / Initial Badge -->
                            <div class="h-48 flex items-center justify-center relative overflow-hidden">
                                <template x-if="listing.media && listing.media.length > 0">
                                    <img :src="'/storage/' + listing.media[0]"
                                         :alt="listing.product?.variety || ''"
                                         class="w-full h-full object-cover"
                                         loading="lazy">
                                </template>

                                <template x-if="!listing.media || listing.media.length === 0">
                                    <div class="w-full h-full flex flex-col items-center justify-center p-4 relative overflow-hidden"
                                         :style="(listing.category === 'olive' || listing.product?.type === 'olive') 
                                                 ? 'background: linear-gradient(135deg, #143618 0%, #2A5C2E 50%, #47824B 100%);' 
                                                 : 'background: linear-gradient(135deg, #7A5A1B 0%, #C8A356 50%, #4F6C28 100%);'">
                                        <div class="absolute -right-8 -bottom-8 w-36 h-36 rounded-full bg-white/10 blur-xl"></div>
                                        <div class="absolute -left-8 -top-8 w-36 h-36 rounded-full bg-white/10 blur-xl"></div>
                                        
                                        <div class="relative z-10 flex flex-col items-center text-center">
                                            <div class="min-w-[7rem] px-5 h-16 rounded-xl bg-[#FDFBF7] border-2 border-[#C8A356] flex items-center justify-center shadow-2xl mb-1.5 overflow-hidden relative">
                                                <template x-if="listing.seller?.profile_picture">
                                                    <img :src="listing.seller.profile_picture.startsWith('http') ? listing.seller.profile_picture : '/storage/' + listing.seller.profile_picture" :alt="listing.seller.name" class="absolute inset-0 w-full h-full object-cover">
                                                </template>
                                                <template x-if="!listing.seller?.profile_picture">
                                                    <span class="text-[#143618] text-2xl font-black tracking-widest uppercase drop-shadow-sm whitespace-nowrap" style="font-family: 'Playfair Display', Georgia, serif;" x-text="getInitials(getSellerDisplayName(listing.seller) || 'ZT')"></span>
                                                </template>
                                            </div>
                                            <span class="text-white/90 text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-black/20 backdrop-blur-sm border border-white/10"
                                                  x-text="(listing.category === 'olive' || listing.product?.type === 'olive') ? '🫒 ' + ('{{ app()->getLocale() === 'ar' ? 'زيتون' : __('Olives') }}') : '🫗 ' + ('{{ app()->getLocale() === 'ar' ? 'زيت زيتون' : __('Olive Oil') }}')">
                                            </span>
                                        </div>
                                    </div>
                                </template>

                                <!-- Quick Add/Change Photo Button for Owner -->
                                <template x-if="currentUserId > 0 && (listing.seller_id === currentUserId || listing.seller?.id === currentUserId)">
                                    <button type="button" @click.stop="openQuickUploadModal(listing)" 
                                            title="{{ __('Upload product photo') }}"
                                            class="absolute bottom-2.5 left-2.5 px-3 py-1.5 bg-black/60 hover:bg-black/80 backdrop-blur text-white text-xs font-bold rounded-xl shadow-lg border border-white/20 transition flex items-center gap-1.5 z-20">
                                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span>📷 {{ __('إضافة صورة') }}</span>
                                    </button>
                                </template>

                                <div class="absolute top-3 right-3 flex items-center gap-1.5 z-10">
                                    <template x-if="listing.is_featured">
                                        <span class="px-2.5 py-1 rounded-full text-amber-950 text-xs font-black bg-gradient-to-r from-amber-300 via-amber-400 to-yellow-500 shadow-md backdrop-blur border border-amber-200 flex items-center gap-1">
                                            ⭐ {{ app()->getLocale() === 'ar' ? 'مميز' : __('Featured') }}
                                        </span>
                                    </template>
                                    <span class="px-3 py-1.5 rounded-full text-white text-xs font-extrabold tracking-wide shadow-md backdrop-blur"
                                        :class="listing.product?.type === 'olive' ? 'bg-[#0f9d58]' : 'bg-[#C8A356]'"
                                        x-text="listing.product?.type === 'olive' ? '{{ __('Olives') }}' : '{{ __('Olive Oil') }}'"></span>
                                </div>
                                <!-- Distance Badge -->
                                <div x-show="listing.distance != null && listing.distance !== undefined" class="absolute top-3 left-3">
                                    <span class="px-3 py-1 rounded-full text-white text-xs font-bold bg-[#C8A356] backdrop-blur flex items-center gap-1 shadow-md">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        </svg>
                                        <span x-text="(listing.distance || 0).toFixed(1) + ' ' + '{{ __('km') }}'"></span>
                                    </span>
                                </div>
                            </div>

                            <!-- Product Details -->
                            <div class="p-5">
                                <h3 class="text-xl font-bold text-gray-900 mb-2 leading-tight" x-text="getEnhancedTitle(listing)"></h3>
                                
                                <div class="flex items-center gap-2 mb-3 flex-wrap">
                                    <span x-show="listing.product?.quality" class="px-2 py-1 rounded-full bg-gradient-to-r from-[#C8A356] to-[#b8954e] shadow-sm text-white text-xs font-semibold" x-text="translate(listing.product?.quality)"></span>
                                    <span x-show="listing.packaging" class="px-2 py-1 rounded-full bg-gradient-to-r from-[#6A8F3B] to-[#5a7a2f] shadow-sm text-white text-xs font-semibold" x-text="translate(listing.packaging)"></span>
                                    <span x-show="listing.tree_count" class="px-2.5 py-1 rounded-full bg-emerald-800 text-white text-xs font-bold shadow-sm" x-text="'🌳 ' + listing.tree_count + ' ' + ('{{ app()->getLocale() === 'ar' ? 'شجرة' : __('trees') }}')"></span>
                                    <span x-show="listing.status === 'active'" class="px-2 py-1 rounded-full bg-green-500 text-white text-xs font-semibold">{{ __('Active') }}</span>
                                </div>

                                <div class="text-2xl font-bold text-[#6A8F3B] mb-4 flex items-baseline gap-1">
                                    <template x-if="Number(listing.price || listing.product?.price || 0) === 0">
                                        <span class="bg-gradient-to-r from-[#6A8F3B] to-[#C8A356] text-transparent bg-clip-text text-xl animate-pulse">{{ app()->getLocale() === 'ar' ? 'السعر عند الطلب' : __('Price on request') }}</span>
                                    </template>
                                    <template x-if="Number(listing.price || listing.product?.price || 0) > 0">
                                        <span class="flex items-baseline gap-1">
                                            <span x-text="formatPrice(listing.price || listing.product?.price, listing.currency)" class="font-black"></span>
                                            <span x-text="formatUnit(listing)" class="text-sm font-semibold text-gray-400"></span>
                                        </span>
                                    </template>
                                </div>

                                <div class="space-y-2 text-sm text-gray-600 mb-4">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <span x-text="getSellerDisplayName(listing.seller)"></span>
                                    </div>
                                    <div x-show="listing.location || listing.governorate || listing.seller?.location || listing.seller?.farm_location" class="flex items-center gap-2 text-[#6A8F3B] font-semibold">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        </svg>
                                        <span x-text="translateLocation(listing.location || (listing.delegation ? (listing.governorate + ' - ' + listing.delegation) : listing.governorate) || listing.seller?.location || listing.seller?.farm_location || '')"></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span x-text="formatDate(listing.created_at)"></span>
                                    </div>
                                </div>

                                <!-- Delivery Option Pills -->
                                <div x-data="{ pills: getDeliveryPills(listing) }" x-show="pills.length > 0" class="flex flex-wrap gap-1 mb-3">
                                    <template x-for="pill in pills" :key="pill">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200" x-text="pill"></span>
                                    </template>
                                </div>

                                <div class="flex gap-2">
                                    <a :href="'/listings/' + listing.id" @click="saveBrowseState(listing)" class="flex-1 text-center px-4 py-2 bg-[#6A8F3B] text-white rounded-lg hover:bg-[#5a7a2f] transition font-bold">
                                        {{ __('View Details') }}
                                    </a>
                                    <button class="px-4 py-2 bg-[#C8A356] text-white rounded-lg hover:bg-[#b08a3c] transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Products List View -->
                <div x-show="viewMode === 'list'" class="space-y-4">
                    <template x-for="listing in visibleListings" :key="listing.id">
                        <div :id="'listing-item-list-' + listing.id" class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 flex flex-col md:flex-row listing-card-item">
                            <!-- Product Image / Seller Logo / Initial Badge -->
                            <div class="w-full md:w-48 h-48 flex items-center justify-center flex-shrink-0 relative overflow-hidden">
                                <template x-if="listing.media && listing.media.length > 0">
                                    <img :src="'/storage/' + listing.media[0]"
                                         :alt="listing.product?.variety || ''"
                                         class="w-full h-full object-cover"
                                         loading="lazy">
                                </template>

                                <template x-if="!listing.media || listing.media.length === 0">
                                    <div class="w-full h-full flex flex-col items-center justify-center p-4 relative overflow-hidden"
                                         :style="(listing.category === 'olive' || listing.product?.type === 'olive') 
                                                 ? 'background: linear-gradient(135deg, #143618 0%, #2A5C2E 50%, #47824B 100%);' 
                                                 : 'background: linear-gradient(135deg, #7A5A1B 0%, #C8A356 50%, #4F6C28 100%);'">
                                        <div class="absolute -right-8 -bottom-8 w-36 h-36 rounded-full bg-white/10 blur-xl"></div>
                                        <div class="absolute -left-8 -top-8 w-36 h-36 rounded-full bg-white/10 blur-xl"></div>
                                        
                                        <div class="relative z-10 flex flex-col items-center text-center">
                                            <div class="min-w-[7rem] px-5 h-16 rounded-xl bg-[#FDFBF7] border-2 border-[#C8A356] flex items-center justify-center shadow-2xl mb-1.5 overflow-hidden relative">
                                                <template x-if="listing.seller?.profile_picture">
                                                    <img :src="listing.seller.profile_picture.startsWith('http') ? listing.seller.profile_picture : '/storage/' + listing.seller.profile_picture" :alt="listing.seller.name" class="absolute inset-0 w-full h-full object-cover">
                                                </template>
                                                <template x-if="!listing.seller?.profile_picture">
                                                    <span class="text-[#143618] text-2xl font-black tracking-widest uppercase drop-shadow-sm whitespace-nowrap" style="font-family: 'Playfair Display', Georgia, serif;" x-text="getInitials(getSellerDisplayName(listing.seller) || 'ZT')"></span>
                                                </template>
                                            </div>
                                            <span class="text-white/90 text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-black/20 backdrop-blur-sm border border-white/10"
                                                  x-text="(listing.category === 'olive' || listing.product?.type === 'olive') ? '🫒 ' + ('{{ app()->getLocale() === 'ar' ? 'زيتون' : __('Olives') }}') : '🫗 ' + ('{{ app()->getLocale() === 'ar' ? 'زيت زيتون' : __('Olive Oil') }}')">
                                            </span>
                                        </div>
                                    </div>
                                </template>

                                <!-- Quick Add/Change Photo Button for Owner -->
                                <template x-if="currentUserId > 0 && (listing.seller_id === currentUserId || listing.seller?.id === currentUserId)">
                                    <button type="button" @click.stop="openQuickUploadModal(listing)" 
                                            title="{{ __('Upload product photo') }}"
                                            class="absolute bottom-2.5 left-2.5 px-3 py-1.5 bg-black/60 hover:bg-black/80 backdrop-blur text-white text-xs font-bold rounded-xl shadow-lg border border-white/20 transition flex items-center gap-1.5 z-20">
                                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span>📷 {{ __('إضافة صورة') }}</span>
                                    </button>
                                </template>
                                
                                <!-- Distance Badge -->
                                <div x-show="listing.distance != null && listing.distance !== undefined" class="absolute top-3 left-3">
                                    <span class="px-3 py-1 rounded-full text-white text-xs font-bold bg-[#C8A356] backdrop-blur flex items-center gap-1 shadow-md">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        </svg>
                                        <span x-text="(listing.distance || 0).toFixed(1) + ' ' + '{{ __('km') }}'"></span>
                                    </span>
                                </div>
                            </div>

                            <!-- Product Details -->
                            <div class="flex-1 p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2 flex-wrap">
                                        <template x-if="listing.is_featured">
                                            <span class="px-2.5 py-1 rounded-full text-amber-950 text-xs font-black bg-gradient-to-r from-amber-300 via-amber-400 to-yellow-500 shadow border border-amber-200 flex items-center gap-1">
                                                ⭐ {{ app()->getLocale() === 'ar' ? 'مميز' : __('Featured') }}
                                            </span>
                                        </template>
                                        <h3 class="text-2xl font-bold text-gray-900 leading-tight" x-text="getEnhancedTitle(listing)"></h3>
                                        <span class="px-3 py-2 rounded-full text-white text-sm font-extrabold tracking-wide" 
                                              :class="listing.product?.type === 'olive' ? 'bg-[#0f9d58]' : 'bg-[#C8A356]'"
                                              x-text="listing.product?.type === 'olive' ? '{{ app()->getLocale() === 'ar' ? 'زيتون' : __('Olives') }}' : '{{ app()->getLocale() === 'ar' ? 'زيت زيتون' : __('Olive Oil') }}'"></span>
                                        <span x-show="listing.product?.quality" class="px-3 py-1 rounded-full bg-gradient-to-r from-[#C8A356] to-[#b8954e] shadow-sm text-white text-xs font-semibold" x-text="translate(listing.product?.quality)"></span>
                                        <span x-show="listing.packaging" class="px-3 py-1 rounded-full bg-gradient-to-r from-[#6A8F3B] to-[#5a7a2f] shadow-sm text-white text-xs font-semibold" x-text="translate(listing.packaging)"></span>
                                        <span x-show="listing.tree_count" class="px-2.5 py-1 rounded-full bg-emerald-800 text-white text-xs font-bold shadow-sm" x-text="'🌳 ' + listing.tree_count + ' ' + ('{{ app()->getLocale() === 'ar' ? 'شجرة' : __('trees') }}')"></span>
                                    </div>
                                    <div class="flex items-center gap-4 text-sm text-gray-600 flex-wrap">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            <span x-text="getSellerDisplayName(listing.seller)"></span>
                                        </div>
                                        <div x-show="listing.location || listing.governorate || listing.seller?.location || listing.seller?.farm_location" class="flex items-center gap-2 text-[#6A8F3B] font-semibold">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            </svg>
                                            <span x-text="translateLocation(listing.location || (listing.delegation ? (listing.governorate + ' - ' + listing.delegation) : listing.governorate) || listing.seller?.location || listing.seller?.farm_location || '')"></span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span x-text="formatDate(listing.created_at)"></span>
                                        </div>
                                    </div>

                                    <!-- Delivery Option Pills (list view) -->
                                    <div x-data="{ pills: getDeliveryPills(listing) }" x-show="pills.length > 0" class="flex flex-wrap gap-1 mt-2">
                                        <template x-for="pill in pills" :key="pill">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200" x-text="pill"></span>
                                        </template>
                                    </div>
                                </div>
                                <div class="text-center md:text-left">
                                    <div class="text-3xl font-bold text-[#6A8F3B] mb-4 flex items-baseline gap-1">
                                        <template x-if="Number(listing.price || listing.product?.price || 0) === 0">
                                            <span class="bg-gradient-to-r from-[#6A8F3B] to-[#C8A356] text-transparent bg-clip-text text-2xl animate-pulse">{{ app()->getLocale() === 'ar' ? 'السعر عند الطلب' : __('Price on request') }}</span>
                                        </template>
                                        <template x-if="Number(listing.price || listing.product?.price || 0) > 0">
                                            <span class="flex items-baseline gap-1">
                                                <span x-text="formatPrice(listing.price || listing.product?.price, listing.currency)" class="font-black"></span>
                                                <span x-text="formatUnit(listing)" class="text-sm font-semibold text-gray-400"></span>
                                            </span>
                                        </template>
                                    </div>
                                    <div class="flex gap-2">
                                        <a :href="'/listings/' + listing.id" @click="saveBrowseState(listing)" class="px-6 py-2 bg-[#6A8F3B] text-white rounded-lg hover:bg-[#5a7a2f] transition font-bold whitespace-nowrap">
                                            {{ __('View Details') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Infinite Scroll Sentinel & Load More Trigger -->
                <div x-show="visibleCount < filteredListings.length" 
                     x-ref="loadMoreTrigger"
                     class="w-full py-8 text-center flex items-center justify-center">
                    <button @click="loadMore()" 
                            type="button"
                            class="inline-flex items-center gap-2.5 px-6 py-3 rounded-full bg-white hover:bg-gray-50 active:scale-95 shadow-md border border-gray-200 text-gray-700 text-xs font-bold transition">
                        <svg class="w-4 h-4 animate-spin text-[#6A8F3B]" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>{{ __('Displaying') }} <span x-text="Math.min(visibleCount, filteredListings.length)"></span> / <span x-text="filteredListings.length"></span> {{ __('products (Click or scroll to load more)') }}</span>
                    </button>
                </div>

                <!-- Empty State -->
                <div x-show="filteredListings.length === 0" class="text-center py-16">
                    <svg class="w-24 h-24 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-2xl font-bold text-gray-700 mb-2">{{ __('No results found') }}</h3>
                    <p class="text-gray-500 mb-6">{{ __('Try changing your search or filter criteria') }}</p>
                    <button @click="resetFilters" class="px-6 py-3 bg-[#6A8F3B] text-white rounded-xl hover:bg-[#5a7a2f] transition font-bold shadow-lg">
                        {{ __('Reset Search') }}
                    </button>
                </div>
            </div>
    </section>

    <!-- CTA Section -->
    <section class="bg-gradient-to-br from-[#6A8F3B] to-[#5a7a2f] text-white py-16 px-4 mt-12" data-bot-explain="{{ __('Are you a producer or mill owner? Join us and start selling for free!') }}">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">{{ __('Do you have a product to sell?') }}</h2>
            <p class="text-xl text-white/90 mb-8">{{ __('Join thousands of sellers and list your product today') }}</p>
            <a href="{{ route('listings.create') }}" class="inline-block px-8 py-4 bg-white text-[#6A8F3B] rounded-xl hover:bg-gray-100 transition font-bold text-lg shadow-lg">
                {{ __('Add your listing for free') }}
            </a>
        </div>
    </section>

    <!-- Article Reader Modal -->
    <div x-show="articleModalOpen" 
         x-cloak
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6"
         role="dialog"
         aria-modal="true">
        <div x-show="articleModalOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="absolute inset-0 bg-black/60 backdrop-blur-sm"
             @click="articleModalOpen = false"></div>

        <div x-show="articleModalOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="relative w-full max-w-3xl bg-white rounded-[2rem] shadow-[0_30px_100px_-20px_rgba(0,0,0,0.3)] overflow-hidden max-h-[90vh] flex flex-col">
            <button @click="articleModalOpen = false" class="absolute top-6 right-6 z-10 w-10 h-10 bg-white/90 backdrop-blur rounded-full flex items-center justify-center text-gray-500 hover:text-gray-900 shadow-lg transition-all hover:scale-110 active:scale-95">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
            <div class="overflow-y-auto flex-1 scrollbar-hide">
                <div class="aspect-video relative">
                    <img :src="currentArticle.image" alt="" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-white via-white/20 to-transparent"></div>
                </div>
                <div class="px-8 pb-12 -mt-16 relative z-10">
                    <div class="bg-white rounded-3xl p-8 shadow-[0_-20px_50px_-20px_rgba(0,0,0,0.1)]">
                        <h2 class="text-3xl font-extrabold text-gray-900 mb-6 leading-tight" x-text="currentArticle.title[locale]"></h2>
                        <div class="prose prose-lg max-w-none text-gray-600 leading-relaxed space-y-6" x-html="currentArticle.content[locale]"></div>
                        <div class="mt-12 pt-8 border-t border-gray-100">
                            <button @click="articleModalOpen = false" class="px-8 py-3 bg-[#6A8F3B] text-white rounded-xl font-bold hover:shadow-lg transition-all hover:-translate-y-0.5">{{ __('Close') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Nearest Transporter Finder Modal -->
    <div x-show="transporterModalOpen" 
         x-cloak
         class="fixed inset-0 z-[110] flex items-center justify-center p-3 sm:p-6"
         role="dialog"
         aria-modal="true">
        <!-- Backdrop -->
        <div x-show="transporterModalOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="absolute inset-0 bg-black/60 backdrop-blur-sm"
             @click="transporterModalOpen = false"></div>

        <!-- Modal Dialog -->
        <div x-show="transporterModalOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="relative w-full max-w-2xl bg-white rounded-3xl sm:rounded-[2rem] shadow-[0_30px_100px_-20px_rgba(0,0,0,0.3)] overflow-hidden max-h-[90vh] flex flex-col min-h-0 z-10 border border-blue-100 my-auto">
            
            <!-- Modal Header (Dynamic Step title) -->
            <div class="px-3.5 py-2.5 sm:px-6 sm:py-3.5 bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 text-white flex items-center justify-between flex-shrink-0">
                <div class="flex items-center gap-2 min-w-0">
                    <span class="text-base sm:text-xl">🚚</span>
                    <div class="min-w-0 text-start">
                        <div class="flex items-center gap-2">
                            <h3 class="text-xs sm:text-base font-black text-white leading-tight truncate" 
                                x-text="transporterStep === 1 ? @js(__('1. Define Route & Cargo 📋')) : @js(__('2. Select Carrier & Send Request 🚚'))">
                            </h3>
                            <span class="px-1.5 py-0.5 rounded text-[10px] font-black bg-white/20 text-white flex-shrink-0" 
                                  x-text="transporterStep + '/2'"></span>
                        </div>
                        <p class="text-[11px] text-white/80 hidden sm:block truncate" 
                           x-text="transporterStep === 1 ? @js(__('Set pickup, dropoff and weight to calculate accurate price')) : @js(__('Choose from the closest available trucks for your route'))">
                        </p>
                    </div>
                </div>
                <button @click="transporterModalOpen = false" class="w-7 h-7 sm:w-8 sm:h-8 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center text-white transition active:scale-95 flex-shrink-0 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <!-- ═══════════════════════════════════════════════════ -->
            <!-- STEP 1: DEFINE COURSE PARAMETERS FORM -->
            <!-- ═══════════════════════════════════════════════════ -->
            <div x-show="transporterStep === 1" class="p-3.5 sm:p-6 overflow-y-auto flex-1 min-h-0 space-y-4">
                <!-- Validation Error Alert -->
                <div x-show="transporterValidationError" x-cloak class="p-3 bg-red-50 border border-red-200 rounded-xl text-xs font-bold text-red-700 flex items-center gap-2">
                    <span class="text-base">⚠️</span>
                    <span x-text="transporterValidationError"></span>
                </div>

                <div class="space-y-3.5">
                    <!-- 1. Pickup (Origin: Governorate + Delegation on same line) -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="text-xs font-black text-gray-800 flex items-center gap-1">
                                <span>🟢</span>
                                <span>{{ __('Pickup Location (Origin)') }}</span>
                                <span class="text-red-500">*</span>
                            </label>
                            <button type="button" 
                                    @click="locateTransporterGps()" 
                                    :disabled="locatingTransporterGps"
                                    class="text-[11px] font-bold text-emerald-600 hover:text-emerald-700 flex items-center gap-1 transition active:scale-95 cursor-pointer">
                                <span x-show="!locatingTransporterGps">📍 {{ __('My GPS Location') }}</span>
                                <span x-show="locatingTransporterGps" class="flex items-center gap-1">
                                    <svg class="w-3 h-3 animate-spin text-emerald-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    {{ __('Locating...') }}
                                </span>
                            </button>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <!-- Pickup Governorate -->
                            <select x-model="transporterPickupGov" 
                                    @change="transporterPickupDel = ''; transporterValidationError = ''; if(transporterPickupGov && transporterDropoffGov) calculateCourseEstimate()" 
                                    class="w-full text-xs sm:text-sm font-bold py-2.5 px-2.5 bg-gray-50 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none">
                                <option value="">-- {{ __('Governorate') }} --</option>
                                @foreach(['صفاقس', 'تونس', 'سوسة', 'القيروان', 'المنستير', 'المهدية', 'نابل', 'بنزرت', 'باجة', 'جندوبة', 'الكاف', 'سليانة', 'زغوان', 'سيدي بوزيد', 'القصرين', 'قفصة', 'قابس', 'مدنين', 'تطاوين', 'توزر', 'قبلي', 'أريانة', 'بن عروس', 'منوبة'] as $g)
                                    <option value="{{ $g }}">{{ __($g) }}</option>
                                @endforeach
                            </select>

                            <!-- Pickup Delegation (same line!) -->
                            <select x-model="transporterPickupDel" 
                                    :disabled="!transporterPickupGov"
                                    @change="transporterValidationError = ''; if(transporterPickupGov && transporterDropoffGov) calculateCourseEstimate()" 
                                    class="w-full text-xs sm:text-sm font-bold py-2.5 px-2.5 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition"
                                    :class="!transporterPickupGov ? 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed' : 'bg-gray-50 text-gray-900 border-gray-300 focus:bg-white'">
                                <option value="">-- {{ __('Delegation') }} --</option>
                                <template x-for="del in (tunisianDelegations[transporterPickupGov] || [])" :key="del">
                                    <option :value="del" x-text="del"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <!-- 2. Dropoff (Target: Governorate + Delegation on same line) -->
                    <div>
                        <label class="block text-xs font-black text-gray-800 mb-1">
                            <span>🚩</span>
                            <span>{{ __('Dropoff Location (Destination)') }}</span>
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-2">
                            <!-- Dropoff Governorate -->
                            <select x-model="transporterDropoffGov" 
                                    @change="transporterDropoffDel = ''; transporterValidationError = ''; if(transporterPickupGov && transporterDropoffGov) calculateCourseEstimate()" 
                                    class="w-full text-xs sm:text-sm font-bold py-2.5 px-2.5 bg-gray-50 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none">
                                <option value="">-- {{ __('Governorate') }} --</option>
                                @foreach(['تونس', 'صفاقس', 'سوسة', 'القيروان', 'المنستير', 'المهدية', 'نابل', 'بنزرت', 'باجة', 'جندوبة', 'الكاف', 'سليانة', 'زغوان', 'سيدي بوزيد', 'القصرين', 'قفصة', 'قابس', 'مدنين', 'تطاوين', 'توزر', 'قبلي', 'أريانة', 'بن عروس', 'منوبة'] as $g)
                                    <option value="{{ $g }}">{{ __($g) }}</option>
                                @endforeach
                            </select>

                            <!-- Dropoff Delegation (same line!) -->
                            <select x-model="transporterDropoffDel" 
                                    :disabled="!transporterDropoffGov"
                                    @change="transporterValidationError = ''; if(transporterPickupGov && transporterDropoffGov) calculateCourseEstimate()" 
                                    class="w-full text-xs sm:text-sm font-bold py-2.5 px-2.5 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition"
                                    :class="!transporterDropoffGov ? 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed' : 'bg-gray-50 text-gray-900 border-gray-300 focus:bg-white'">
                                <option value="">-- {{ __('Delegation') }} --</option>
                                <template x-for="del in (tunisianDelegations[transporterDropoffGov] || [])" :key="del">
                                    <option :value="del" x-text="del"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <!-- 2.5 Cargo Type Selector (Oil Cistern vs Olives) -->
                    <div>
                        <label class="text-xs font-black text-gray-800 flex items-center gap-1 mb-1.5">
                            <span>📦</span>
                            <span>{{ __('نوع المادة المنقولة') }}</span>
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" 
                                    @click="transporterCargoType = 'oil'; if(transporterPickupGov && transporterDropoffGov) calculateCourseEstimate()"
                                    :class="transporterCargoType === 'oil' 
                                        ? 'bg-amber-500 text-white font-black border-amber-600 shadow-xs ring-2 ring-amber-400/30' 
                                        : 'bg-gray-50 hover:bg-gray-100 text-gray-700 font-bold border-gray-200'"
                                    class="p-2.5 rounded-xl border text-xs flex items-center justify-center gap-2 transition cursor-pointer">
                                <span class="text-lg">🫙</span>
                                <div class="text-start leading-tight">
                                    <div class="font-black text-xs">{{ __('زيت زيتون') }}</div>
                                    <div class="text-[10px] opacity-90">{{ __('صهريج / براميل') }}</div>
                                </div>
                            </button>
                            <button type="button" 
                                    @click="transporterCargoType = 'olive'; if(transporterPickupGov && transporterDropoffGov) calculateCourseEstimate()"
                                    :class="transporterCargoType === 'olive' 
                                        ? 'bg-emerald-700 text-white font-black border-emerald-800 shadow-xs ring-2 ring-emerald-500/30' 
                                        : 'bg-gray-50 hover:bg-gray-100 text-gray-700 font-bold border-gray-200'"
                                    class="p-2.5 rounded-xl border text-xs flex items-center justify-center gap-2 transition cursor-pointer">
                                <span class="text-lg">🫒</span>
                                <div class="text-start leading-tight">
                                    <div class="font-black text-xs">{{ __('زيتون فلاحي') }}</div>
                                    <div class="text-[10px] opacity-90">{{ __('حب / صناديق') }}</div>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- 3. Cargo Weight & Vehicle Classification -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="text-xs font-black text-gray-800 flex items-center gap-1">
                                <span>⚖️</span>
                                <span>{{ __('Cargo Weight & Transport Category') }}</span>
                                <span class="text-red-500">*</span>
                            </label>
                            <span class="text-[11px] font-bold text-blue-700 bg-blue-50 border border-blue-200 px-2 py-0.5 rounded-lg" x-text="getVehicleCategoryLabel(transporterWeight)"></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="flex-1 relative flex items-center bg-gray-50 border border-gray-300 focus-within:border-blue-500 focus-within:bg-white rounded-xl px-3 py-2 shadow-2xs">
                                <input type="number" step="0.25" min="0.25" max="50" 
                                       x-model.number="transporterWeight" 
                                       @change="transporterValidationError = ''; if(transporterPickupGov && transporterDropoffGov) calculateCourseEstimate()" 
                                       class="w-full text-sm font-black text-gray-900 outline-none bg-transparent" 
                                       placeholder="1">
                                <span class="text-xs font-black text-gray-500 ms-1">{{ __('Ton') }}</span>
                            </div>
                        </div>
                        <!-- Quick Weight Presets (Adapts to Oil Cisterns vs Olive Cargo) -->
                        <div class="flex items-center gap-1.5 mt-2 flex-wrap">
                            <span class="text-[10px] text-gray-400 font-bold">{{ __('Quick Options:') }}</span>
                            <button type="button" 
                                    @click="transporterWeight = 0.5; transporterValidationError = ''; if(transporterPickupGov && transporterDropoffGov) calculateCourseEstimate()" 
                                    :class="transporterWeight == 0.5 ? 'bg-blue-600 text-white font-black shadow-2xs' : 'bg-gray-100 text-gray-700 font-bold hover:bg-gray-200'"
                                    class="px-2.5 py-1 rounded-lg text-[11px] transition cursor-pointer flex items-center gap-1">
                                <span>🚐</span>
                                <span x-text="transporterCargoType === 'oil' ? '500 كغ (براميل)' : '500 كغ'"></span>
                            </button>
                            <button type="button" 
                                    @click="transporterWeight = 1.0; transporterValidationError = ''; if(transporterPickupGov && transporterDropoffGov) calculateCourseEstimate()" 
                                    :class="transporterWeight == 1.0 ? 'bg-blue-600 text-white font-black shadow-2xs' : 'bg-gray-100 text-gray-700 font-bold hover:bg-gray-200'"
                                    class="px-2.5 py-1 rounded-lg text-[11px] transition cursor-pointer flex items-center gap-1">
                                <span>🚐</span>
                                <span x-text="transporterCargoType === 'oil' ? '1 طن (براميل / صهريج صغير)' : '1 طن (بارتنر / كادي)'"></span>
                            </button>
                            <button type="button" 
                                    @click="transporterWeight = 2.0; transporterValidationError = ''; if(transporterPickupGov && transporterDropoffGov) calculateCourseEstimate()" 
                                    :class="transporterWeight == 2.0 ? 'bg-blue-600 text-white font-black shadow-2xs' : 'bg-gray-100 text-gray-700 font-bold hover:bg-gray-200'"
                                    class="px-2.5 py-1 rounded-lg text-[11px] transition cursor-pointer flex items-center gap-1">
                                <span>🛻</span>
                                <span x-text="transporterCargoType === 'oil' ? '2 طن (صهريج إيسيزو / ديماكس)' : '2 طن (ستافات / ديماكس)'"></span>
                            </button>
                            <button type="button" 
                                    @click="transporterWeight = 5.0; transporterValidationError = ''; if(transporterPickupGov && transporterDropoffGov) calculateCourseEstimate()" 
                                    :class="transporterWeight == 5.0 ? 'bg-blue-600 text-white font-black shadow-2xs' : 'bg-gray-100 text-gray-700 font-bold hover:bg-gray-200'"
                                    class="px-2.5 py-1 rounded-lg text-[11px] transition cursor-pointer flex items-center gap-1">
                                <span>🚚</span>
                                <span x-text="transporterCargoType === 'oil' ? '5 طن (شاحنة صهريج زيت)' : '5 طن (شاحنة متوسطة)'"></span>
                            </button>
                            <button type="button" 
                                    @click="transporterWeight = 10.0; transporterValidationError = ''; if(transporterPickupGov && transporterDropoffGov) calculateCourseEstimate()" 
                                    :class="transporterWeight == 10.0 ? 'bg-blue-600 text-white font-black shadow-2xs' : 'bg-gray-100 text-gray-700 font-bold hover:bg-gray-200'"
                                    class="px-2.5 py-1 rounded-lg text-[11px] transition cursor-pointer flex items-center gap-1">
                                <span>🚛</span>
                                <span x-text="transporterCargoType === 'oil' ? '10 طن (صهريج متوسط)' : '10 طن (شاحنة ثقيلة)'"></span>
                            </button>
                            <button type="button" 
                                    @click="transporterWeight = 25.0; transporterValidationError = ''; if(transporterPickupGov && transporterDropoffGov) calculateCourseEstimate()" 
                                    :class="transporterWeight == 25.0 ? 'bg-blue-600 text-white font-black shadow-2xs' : 'bg-gray-100 text-gray-700 font-bold hover:bg-gray-200'"
                                    class="px-2.5 py-1 rounded-lg text-[11px] transition cursor-pointer flex items-center gap-1">
                                <span>🛢️</span>
                                <span x-text="transporterCargoType === 'oil' ? '25 طن (صهريج ضخم Inox)' : '25 طن (شاحنة مقطورة)'"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Live Estimation Box (Appears when both locations are chosen) -->
                    <div x-show="transporterPickupGov && transporterDropoffGov" x-transition class="p-3 bg-gradient-to-r from-blue-50 via-indigo-50 to-blue-50 border border-blue-200 rounded-2xl flex items-center justify-between gap-3 shadow-2xs">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-sm shadow-sm flex-shrink-0">
                                🛣️
                            </div>
                            <div class="min-w-0">
                                <div class="text-xs font-black text-blue-950 truncate">
                                    <span x-text="transporterPickupGov + (transporterPickupDel ? ' (' + transporterPickupDel + ')' : '')"></span> ➔ <span x-text="transporterDropoffGov + (transporterDropoffDel ? ' (' + transporterDropoffDel + ')' : '')"></span>
                                </div>
                                <div class="text-[11px] text-gray-600 font-bold">
                                    {{ __('Estimated Distance:') }} ~<span x-text="transporterCourseDistance"></span> {{ __('km') }}
                                </div>
                            </div>
                        </div>
                        <div class="text-end flex-shrink-0">
                            <span class="text-[10px] font-bold text-emerald-700 block">{{ __('Estimated Fair Price') }}</span>
                            <span class="text-base font-black text-emerald-900 leading-tight">~<span x-text="transporterCourseCost"></span> {{ __('TND') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Next Step Button -->
                <div class="pt-2">
                    <button type="button" 
                            @click="proceedToTransportersList()" 
                            class="w-full py-3 px-4 bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 hover:from-blue-700 hover:to-indigo-700 active:scale-98 text-white rounded-xl sm:rounded-2xl font-black text-xs sm:text-sm shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                        <span>🔍 {{ __('Search Nearest Available Transporters') }}</span>
                        <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg>
                    </button>
                </div>
            </div>

            <!-- ═══════════════════════════════════════════════════ -->
            <!-- STEP 2: TRANSPORTERS LIST AREA -->
            <!-- ═══════════════════════════════════════════════════ -->
            <!-- Step 2 Course Summary & Edit Bar -->
            <div x-show="transporterStep === 2" class="p-2 sm:p-3 bg-slate-50 border-b border-gray-200 flex-shrink-0 flex items-center justify-between gap-2">
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-1.5 text-xs font-black text-gray-900 truncate">
                        <span>🟢 <span x-text="transporterPickupGov + (transporterPickupDel ? ' (' + transporterPickupDel + ')' : '')"></span></span>
                        <span>➔</span>
                        <span class="text-gray-300">|</span>
                        <span>⚖️ <span x-text="transporterWeight"></span> {{ __('Ton') }}</span>
                        <span class="text-gray-300">|</span>
                        <span class="px-1.5 py-0.5 rounded text-[10px] font-black" 
                              :class="transporterCargoType === 'oil' ? 'bg-amber-100 text-amber-900 border border-amber-300' : 'bg-emerald-100 text-emerald-900 border border-emerald-300'" 
                              x-text="transporterCargoType === 'oil' ? '🫙 صهريج زيت' : '🫒 زيتون'"></span>
                    </div>
                    <div class="flex items-center gap-2 text-[10px] text-gray-500 font-bold mt-0.5">
                        <span>🛣️ ~<span x-text="transporterCourseDistance"></span> {{ __('km') }}</span>
                        <span>•</span>
                        <span class="text-emerald-700 font-black">💰 ~<span x-text="transporterCourseCost"></span> {{ __('TND') }}</span>
                        <span>•</span>
                        <span class="text-blue-700 font-black">⏳ {{ __('Quota:') }} <span x-text="remainingTransporterQuota + '/3'"></span></span>
                    </div>
                </div>
                <button type="button" 
                        @click="backToCourseParameters()" 
                        class="px-2.5 py-1.5 bg-white hover:bg-gray-100 border border-gray-300 rounded-lg text-[11px] font-bold text-gray-700 transition active:scale-95 flex items-center gap-1 flex-shrink-0 cursor-pointer shadow-2xs">
                    <span>✏️</span>
                    <span>{{ __('Edit Route') }}</span>
                </button>
            </div>

            <!-- Transporters List (Step 2) -->
            <div x-show="transporterStep === 2" class="p-2 sm:p-4 overflow-y-auto flex-1 min-h-0 space-y-1.5 sm:space-y-2.5 overscroll-contain" style="-webkit-overflow-scrolling: touch; max-height: calc(90vh - 180px);">
                <!-- Accepted Transporter Celebration Alert -->
                <div x-show="acceptedMissionData" x-cloak class="p-3 bg-gradient-to-r from-emerald-600 to-teal-700 rounded-xl text-white shadow-md flex items-center justify-between gap-2 border-2 border-emerald-400">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="text-2xl animate-bounce">🎉</span>
                        <div class="min-w-0">
                            <p class="text-xs sm:text-sm font-black truncate" x-text="'{{ __('Accepted by') }}: ' + acceptedMissionData.carrier_name + ' 🚚'"></p>
                            <p class="text-[10px] sm:text-xs text-emerald-100 truncate">{{ __('Transporter is on the way! Open chat to send GPS.') }}</p>
                        </div>
                    </div>
                    <a :href="acceptedMissionData.chat_url" class="px-3 py-1.5 bg-white text-emerald-800 rounded-lg font-black text-xs hover:bg-emerald-50 active:scale-95 shadow transition whitespace-nowrap">
                        {{ __('Open Chat') }} 💬
                    </a>
                </div>

                <!-- Loading State -->
                <div x-show="loadingTransporters" class="py-12 text-center text-blue-600 text-xs font-bold flex flex-col items-center justify-center gap-2">
                    <svg class="w-7 h-7 animate-spin text-blue-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span>{{ __('Searching for nearest suitable trucks...') }}</span>
                </div>

                <!-- Empty State -->
                <div x-show="!loadingTransporters && transportersList.length === 0" class="text-center py-12 text-gray-500 space-y-1.5">
                    <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center mx-auto text-xl text-gray-400">🚚</div>
                    <p class="font-bold text-xs sm:text-sm text-gray-700">{{ __('No transporters found matching your criteria.') }}</p>
                </div>

                <!-- Transporters List (Sleek Compact Horizontal Bande - Fits 4+ on Screen) -->
                <div x-show="!loadingTransporters && transportersList.length > 0" class="space-y-1.5 sm:space-y-2.5">
                    <template x-for="carrier in transportersList" :key="carrier.id">
                        <div class="group relative bg-white hover:bg-slate-50 border border-slate-200 hover:border-blue-400 rounded-xl p-2 sm:p-3 transition-all duration-150 shadow-2xs hover:shadow-xs border-s-4 border-s-blue-600 flex items-center justify-between gap-2 sm:gap-3">
                            <!-- Left: Identity & Badges -->
                            <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1">
                                <!-- Avatar / Initial Circle -->
                                <template x-if="carrier.avatar">
                                    <img :src="carrier.avatar" class="w-9 h-9 sm:w-11 sm:h-11 rounded-full object-cover ring-1 ring-blue-100 flex-shrink-0 shadow-2xs">
                                </template>
                                <template x-if="!carrier.avatar">
                                    <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-gradient-to-br from-blue-600 to-indigo-700 text-white flex items-center justify-center font-black text-xs sm:text-base flex-shrink-0 shadow-2xs ring-1 ring-blue-100" x-text="carrier.name.charAt(0).toUpperCase()"></div>
                                </template>

                                <!-- Details Container -->
                                <div class="min-w-0 flex-1 text-start">
                                    <!-- 1. Transporter Name -->
                                    <div class="flex items-center gap-1 min-w-0">
                                        <h4 class="font-black text-slate-900 text-xs sm:text-base tracking-tight leading-snug group-hover:text-blue-600 transition-colors truncate" x-text="carrier.name"></h4>
                                        <span x-show="carrier.is_verified" class="inline-flex items-center text-blue-600 flex-shrink-0 text-[10px]" title="{{ __('Verified') }}">
                                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    </div>

                                    <!-- 2. Badges: Distance & Vehicle/Capacity -->
                                    <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                                        <!-- Distance in Km -->
                                        <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded bg-blue-50 text-blue-900 text-[10px] sm:text-[11px] font-black border border-blue-200/60">
                                            <span>📍</span>
                                            <span x-text="carrier.distance_km != null ? carrier.distance_km + ' {{ __('km') }}' : '{{ __('Same Governorate') }}'"></span>
                                        </span>
                                        <!-- Vehicle type / Capacity -->
                                        <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-950 text-[10px] sm:text-[11px] font-black border border-emerald-200/60"
                                              x-text="carrier.vehicle_type_label || carrier.capacity_label">
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Request Action Button or Cancel Button -->
                            <div class="flex-shrink-0">
                                <!-- If request already sent to this carrier -->
                                <template x-if="sentTransporterRequests.includes(carrier.id)">
                                    <div class="flex items-center gap-1 sm:gap-1.5">
                                        <span class="inline-flex items-center justify-center gap-1 px-2 py-1.5 sm:px-3 sm:py-2 bg-amber-50 text-amber-900 border border-amber-300 rounded-lg sm:rounded-xl text-[10px] sm:text-xs font-black shadow-2xs whitespace-nowrap cursor-default">
                                            <span class="text-xs">⏳</span>
                                            <span>{{ __('Pending') }}</span>
                                        </span>
                                        <!-- Cancel Request Button -->
                                        <button type="button" 
                                                @click="cancelTransporterRequest(carrier.id)"
                                                :disabled="cancellingCarrierId === carrier.id"
                                                class="px-2 py-1.5 sm:px-2.5 sm:py-2 bg-red-50 hover:bg-red-100 text-red-700 hover:text-red-800 border border-red-200 rounded-lg sm:rounded-xl text-[10px] sm:text-xs font-bold transition flex items-center gap-0.5 cursor-pointer shadow-2xs"
                                                title="{{ __('Cancel request and pick another carrier') }}">
                                            <span x-show="cancellingCarrierId !== carrier.id">❌ {{ __('Cancel') }}</span>
                                            <span x-show="cancellingCarrierId === carrier.id" class="flex items-center gap-1 text-[10px]">
                                                <svg class="w-3 h-3 animate-spin text-red-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                {{ __('Cancelling...') }}
                                            </span>
                                        </button>
                                    </div>
                                </template>

                                <!-- If not yet sent -->
                                <template x-if="!sentTransporterRequests.includes(carrier.id)">
                                    <button type="button" 
                                            @click="sendTransporterRequest(carrier.id)"
                                            :disabled="requestingCarrierId === carrier.id || remainingTransporterQuota <= 0"
                                            :class="remainingTransporterQuota <= 0 ? 'bg-gray-200 text-gray-400 cursor-not-allowed border border-gray-300 shadow-none' : 'bg-blue-600 hover:bg-blue-700 active:scale-95 text-white shadow-2xs hover:shadow-xs cursor-pointer'"
                                            class="inline-flex items-center justify-center gap-1 px-2.5 py-1.5 sm:px-4 sm:py-2 rounded-lg sm:rounded-xl text-[11px] sm:text-xs font-black transition-all whitespace-nowrap">
                                        <template x-if="requestingCarrierId === carrier.id">
                                            <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        </template>
                                        <template x-if="requestingCarrierId !== carrier.id">
                                            <span class="text-xs">🚚</span>
                                        </template>
                                        <span>{{ __('Send Request') }}</span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="p-3 sm:p-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between flex-shrink-0">
                <div>
                    <button x-show="transporterStep === 2" 
                            type="button"
                            @click="backToCourseParameters()" 
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 border border-slate-300 rounded-xl text-xs font-black text-gray-700 transition flex items-center gap-1.5 shadow-2xs cursor-pointer">
                        <span>⬅️</span>
                        <span>{{ __('Edit Route') }}</span>
                    </button>
                </div>
                <button @click="transporterModalOpen = false" class="px-5 py-2 bg-white border border-gray-300 rounded-xl text-xs font-bold text-gray-700 hover:bg-gray-100 transition shadow-2xs cursor-pointer">
                    {{ __('Close') }}
                </button>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════ -->
    <!-- SMART FLOATING ACTION BUTTON (FAB) FOR TRANSPORTER -->
    <!-- ═══════════════════════════════════════════════════ -->
    <div class="fixed top-32 left-3 sm:top-36 sm:left-6 z-40 transition-all duration-300"
         :class="isScrolled ? 'opacity-40 hover:opacity-100 focus-within:opacity-100' : 'opacity-95 hover:opacity-100'">
        <!-- 1. Accepted State (Glowing Emerald Button) -->
        <template x-if="acceptedMissionData">
            <a :href="acceptedMissionData.chat_url"
               class="group inline-flex items-center gap-2 px-3 py-1.5 sm:px-3.5 sm:py-2 bg-emerald-600/90 hover:bg-emerald-600 text-white rounded-full shadow-lg border border-emerald-300/60 backdrop-blur-md hover:scale-105 active:scale-95 transition-all cursor-pointer animate-pulse"
               title="{{ __('Carrier accepted - Click to chat') }}">
                <span class="text-sm sm:text-base">🎉</span>
                <div class="text-start leading-tight">
                    <div class="text-[11px] sm:text-xs font-black flex items-center gap-1">
                        <span class="hidden sm:inline">{{ __('Carrier accepted:') }}</span>
                        <span x-text="acceptedMissionData.carrier_name"></span>
                    </div>
                    <div class="text-[9px] text-emerald-100 font-bold">
                        {{ __('Open Chat') }} 💬
                    </div>
                </div>
            </a>
        </template>

        <!-- 2. Waiting in Progress State (Pulsing Amber Button) -->
        <template x-if="!acceptedMissionData && sentTransporterRequests.length > 0">
            <button @click="openTransporterFinder()"
                    class="group inline-flex items-center gap-2 px-3 py-1.5 sm:px-3.5 sm:py-2 bg-amber-500/90 hover:bg-amber-500 text-white rounded-full shadow-lg border border-amber-300/60 backdrop-blur-md hover:scale-105 active:scale-95 transition-all cursor-pointer"
                    title="{{ __('View pending transport requests') }}">
                <div class="relative flex items-center justify-center">
                    <span class="w-2 h-2 rounded-full bg-white animate-ping absolute"></span>
                    <span class="text-xs sm:text-sm">⏳</span>
                </div>
                <div class="text-start leading-tight">
                    <div class="text-[11px] sm:text-xs font-black flex items-center gap-1">
                        <span>{{ __('Waiting for carrier...') }}</span>
                        <span class="px-1.5 py-0.2 bg-black/20 rounded-full text-[9px] font-mono" x-text="sentTransporterRequests.length + '/3'"></span>
                    </div>
                    <div class="text-[9px] text-amber-100 font-bold hidden sm:block">
                        {{ __('Click for options / cancel') }}
                    </div>
                </div>
            </button>
        </template>

        <!-- 3. Idle Default State (Sleek Blue Floating Button) -->
        <template x-if="!acceptedMissionData && sentTransporterRequests.length === 0">
            <button @click="openTransporterFinder()"
                    class="group inline-flex items-center gap-1.5 px-3 py-1.5 sm:px-3.5 sm:py-2 bg-blue-600/85 hover:bg-blue-600 text-white rounded-full shadow-md border border-white/30 backdrop-blur-md hover:scale-105 active:scale-95 transition-all cursor-pointer"
                    title="{{ __('Call a Transporter') }}">
                <span class="text-sm">🚚</span>
                <span class="text-[11px] sm:text-xs font-black tracking-wide">{{ __('Call Transporter') }}</span>
                <span class="w-4 h-4 rounded-full bg-white/20 flex items-center justify-center text-[9px] group-hover:translate-x-0.5 rtl:group-hover:-translate-x-0.5 transition-transform">
                    ➔
                </span>
            </button>
        </template>
    </div>



    <!-- Quick Photo Upload Modal -->
    <div x-show="quickUploadModalOpen" x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="quickUploadModalOpen = false" 
             class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4 text-right" dir="rtl">
            
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <span>📷 {{ __('إضافة / تغيير صورة المنتج') }}</span>
                </h3>
                <button @click="quickUploadModalOpen = false" class="text-gray-400 hover:text-gray-600 font-bold text-xl">&times;</button>
            </div>

            <p class="text-xs text-gray-600">اختر صورة واضحة لمنتجك (<strong class="text-[#6A8F3B]" x-text="targetListing?.product?.variety || ''"></strong>) لجذب المشترين زيادة المبيعات!</p>

            <form @submit.prevent="submitQuickUpload($event)" class="space-y-4">
                <div class="border-2 border-dashed border-emerald-400 rounded-xl p-6 text-center bg-emerald-50/40 hover:bg-emerald-50 transition cursor-pointer relative">
                    <input type="file" name="image" accept="image/*" required @change="handleQuickFileChange($event)" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full">
                    
                    <template x-if="!quickFilePreview">
                        <div>
                            <svg class="mx-auto h-12 w-12 text-[#6A8F3B]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <p class="mt-2 text-sm text-[#6A8F3B] font-bold">اضغط هنا لاختيار صورة من جهازك</p>
                            <p class="text-[11px] text-gray-500">يقبل JPG, PNG, WEBP</p>
                        </div>
                    </template>

                    <template x-if="quickFilePreview">
                        <div class="relative h-44 w-full rounded-lg overflow-hidden border">
                            <img :src="quickFilePreview" class="w-full h-full object-cover">
                        </div>
                    </template>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" :disabled="uploadingQuickFile" class="flex-1 py-3 px-4 bg-[#6A8F3B] hover:bg-[#5a7a2f] text-white font-bold rounded-xl shadow-lg transition flex items-center justify-center gap-2">
                        <span x-show="!uploadingQuickFile">✓ رفع وحفظ الصورة</span>
                        <span x-show="uploadingQuickFile">جاري الرفع...</span>
                    </button>
                    <button type="button" @click="quickUploadModalOpen = false" class="py-3 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition">إلغاء</button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
document.addEventListener('alpine:init', () => {

    Alpine.data('marketplace', () => ({
        currentUserId: {{ Auth::id() ?? 0 }},
        quickUploadModalOpen: false,
        targetListing: null,
        quickFilePreview: null,
        uploadingQuickFile: false,
        listings: @json($listingsPayload ?? $featuredListings ?? []),
        filteredListings: [],
        visibleCount: 24,
        get visibleListings() {
            return this.filteredListings.slice(0, this.visibleCount);
        },
        loadMore() {
            if (this.visibleCount < this.filteredListings.length) {
                this.visibleCount = Math.min(this.visibleCount + 24, this.filteredListings.length);
            }
        },
        setupInfiniteScroll() {
            if (!window.IntersectionObserver) return;
            const observer = new IntersectionObserver((entries) => {
                if (entries[0] && entries[0].isIntersecting) {
                    this.loadMore();
                }
            }, { rootMargin: '600px 0px' });

            this.$nextTick(() => {
                if (this.$refs.loadMoreTrigger) {
                    observer.observe(this.$refs.loadMoreTrigger);
                }
            });
        },
        searchQuery: '',
        viewMode: 'grid',
        fallbackImage: 'https://toop.kairouanhub.com/storage/listings/23/28bc3509-9426-4f36-9e71-fd694f3cbc45.webp',
        oilFallbackImage: '{{ asset('images/oliveoiltandefault.jpg') }}',
        mobileMenuOpen: false,
        showFilters: window.innerWidth >= 1024,
        articleModalOpen: false,
        locale: '{{ app()->getLocale() }}',
        transporterModalOpen: false,
        transporterStep: 1,
        transporterValidationError: '',
        transportersList: [],
        loadingTransporters: false,
        transporterWeight: 1.0,
        transporterCargoType: 'oil',
        transporterPickupGov: '',
        transporterPickupDel: '',
        transporterDropoffGov: '',
        transporterDropoffDel: '',
        transporterCourseDistance: 0,
        transporterCourseCost: 0,
        sentTransporterRequests: [],
        remainingTransporterQuota: 3,
        requestingCarrierId: null,
        cancellingCarrierId: null,
        acceptedMissionData: null,
        transporterPollTimer: null,
        locatingTransporterGps: false,
        transporterGpsCoords: null,
        isScrolled: false,

        tunisianDelegations: {
            'صفاقس': ['صفاقس المدينة', 'صفاقس الغربية', 'صفاقس الجنوبية', 'ساقية الزيت', 'ساقية الدائر', 'طينة', 'عقارب', 'جبنيانة', 'العامرة', 'الحنشة', 'منزل شاكر', 'الغريبة', 'بئر علي بن خليفة', 'الصخيرة', 'المحرس', 'قرقنة'],
            'تونس': ['المدينة', 'باب بحر', 'باب سويقة', 'العمران', 'العمران الأعلى', 'المنزه', 'الوردية', 'الكبارية', 'سيدي حسين', 'المرسى', 'الكرم', 'حلق الوادي', 'باردو', 'سيدي البشير', 'قرطاج'],
            'أريانة': ['أريانة المدينة', 'سكرة', 'رواد', 'قلعة الأندلس', 'سيدي ثابت', 'حي التضامن', 'المنيهلة'],
            'بن عروس': ['بن عروس', 'رادس', 'الزهراء', 'حمام الأنف', 'حمام الشط', 'بومهل البساتين', 'المروج', 'فوشانة', 'المحمدية', 'مرناق', 'مقرين'],
            'منوبة': ['منوبة', 'الدندان', 'دوار هيشر', 'وادي الليل', 'طبربة', 'الجديدة', 'البطان', 'برج العامري'],
            'نابل': ['نابل', 'الحمامات', 'دار شعبان الفهري', 'بني خيار', 'قربة', 'منزل تميم', 'الميدة', 'قليبية', 'الهوارية', 'تاكلسة', 'سليمان', 'قرمبالية', 'بوعرقوب', 'بني خلاد', 'منزل بوزلفة'],
            'زغوان': ['زغوان', 'الزريبة', 'بئر مشارقة', 'الفحص', 'الناظور', 'صواف'],
            'بنزرت': ['بنزرت الشمالية', 'بنزرت الجنوبية', 'جرزونة', 'منزل بورقيبة', 'تينجة', 'ماطر', 'غزالة', 'جومين', 'سجنان', 'رأس الجبل', 'العالية', 'غار الملح', 'أوتيك', 'ماجل بلعباس'],
            'باجة': ['باجة الشمالية', 'باجة الجنوبية', 'عمدون', 'نفزة', 'تبرسق', 'تستور', 'قبلاط', 'مجاز الباب'],
            'جندوبة': ['جندوبة', 'جندوبة الشمالية', 'بوسالم', 'طبرقة', 'عين دراهم', 'فرنانة', 'غار الدماء', 'وادي مليز', 'بلطة بوعوان'],
            'الكاف': ['الكاف الشرقية', 'الكاف الغربية', 'نبر', 'الطويرف', 'ساقية سيدي يوسف', 'تاجروين', 'قلعة سنان', 'القلعة الخصبة', 'الجريصة', 'القصور', 'الدهماني'],
            'سليانة': ['سليانة الشمالية', 'سليانة الجنوبية', 'بوعرادة', 'قعفور', 'الكريب', 'بورويس', 'مكثر', 'الروحية', 'كسرى', 'برقو', 'العروسة'],
            'سوسة': ['سوسة المدينة', 'سوسة جوهرة', 'سوسة الرياض', 'سوسة سيدي عبد الحميد', 'حمام سوسة', 'أكودة', 'القلعة الكبرى', 'القلعة الصغرى', 'مساكن', 'سيدي بوعلي', 'كندار', 'النفيضة', 'بوفيشة', 'الزاوية القصيبة الثريات', 'سيدي الهاني'],
            'المنستير': ['المنستير', 'الوردانين', 'الساحلين', 'زرمدين', 'بني حسان', 'جمال', 'بنبلة', 'المكنين', 'طبلبة', 'البقالطة', 'صيادة لمطة بوحجر', 'قصر هلال', 'قصيبة المديوني', 'طوزة'],
            'المهدية': ['المهدية', 'رجيش', 'بومرداس', 'أولاد الشامخ', 'شربان', 'هبيرة', 'السواسي', 'الجم', 'الشابة', 'ملولش', 'سيدي علوان', 'قصور الساف'],
            'القيروان': ['القيروان الشمالية', 'القيروان الجنوبية', 'الشبيكة', 'السبيخة', 'الوسلاتية', 'حفوز', 'العلا', 'نصر الله', 'بوحجلة', 'الشراردة', 'حاجب العيون', 'منزل المهيري', 'عين جلولة'],
            'القصرين': ['القصرين الشمالية', 'القصرين الجنوبية', 'الزهور', 'سبيطلة', 'سبيبة', 'جدليان', 'العيون', 'تالة', 'حيدرة', 'فوسانة', 'فريانة', 'ماجل بلعباس', 'حاسي الفريد'],
            'سيدي بوزيد': ['سيدي بوزيد الغربية', 'سيدي بوزيد الشرقية', 'جلمة', 'سبالة أولاد عسكر', 'بئر الحفي', 'سيدي علي بن عون', 'منزل بوزيان', 'المكناسي', 'سوق الجديد', 'المزونة', 'الرقاب', 'أولاد حفوز'],
            'قابس': ['قابس المدينة', 'قابس الغربية', 'قابس الجنوبية', 'غنوش', 'المطوية', 'وذرف', 'الحامة', 'مطماطة', 'مطماطة الجديدة', 'مارث', 'منزل الحبيب'],
            'مدنين': ['مدنين الشمالية', 'مدنين الجنوبية', 'بني خداش', 'بنقردان', 'جرجيس', 'حومة السوق', 'ميدون', 'أجيم', 'سيدي مخلوف'],
            'تطاوين': ['تطاوين الشمالية', 'تطاوين الجنوبية', 'الصمار', 'البئر الأحمر', 'غمراسن', 'الذهيبة', 'رمادة'],
            'قفصة': ['قفصة الشمالية', 'قفصة الجنوبية', 'سيدي عيش', 'القصر', 'القطار', 'بلخير', 'أم العرائس', 'الرديف', 'المتلوي', 'المظيلة', 'السند'],
            'توزر': ['توزر', 'دقاش', 'تمغزة', 'نفطة', 'حزوة'],
            'قبلي': ['قبلي الشمالية', 'قبلي الجنوبية', 'دوز الشمالية', 'دوز الجنوبية', 'سوق الأحد', 'الفوار', 'رجيم معتوق'],
        },

        getVehicleCategoryLabel(w) {
            const num = parseFloat(w) || 0;
            const isOil = (this.transporterCargoType === 'oil');

            if (isOil) {
                if (num <= 1.0) {
                    return '🚐 ' + @js(__('Fourgonnette / Fûts (jusqu\'à 1T)'));
                } else if (num <= 2.5) {
                    return '🛻 ' + @js(__('Camionnette / Isuzu petite citerne (1T - 2.5T)'));
                } else if (num <= 5.0) {
                    return '🚚 ' + @js(__('Camionnette moyenne avec citerne (2.5T - 5T)'));
                } else if (num <= 12.0) {
                    return '🚛 ' + @js(__('Camion citerne à huile (5T - 12T)'));
                } else {
                    return '🛢️ ' + @js(__('Citerne Alimentaire Inox (> 12T)'));
                }
            } else {
                if (num <= 1.0) {
                    return '🚐 ' + @js(__('Fourgonnette (Partner / Caddy jusqu\'à 1T)'));
                } else if (num <= 2.5) {
                    return '🛻 ' + @js(__('Camionnette légère / Stafette (1T - 2.5T)'));
                } else if (num <= 5.0) {
                    return '🚚 ' + @js(__('Camionnette moyenne (2.5T - 5T)'));
                } else if (num <= 12.0) {
                    return '🚛 ' + @js(__('Poids Lourd (5T - 12T)'));
                } else {
                    return '🚛 ' + @js(__('Poids lourd avec remorque (> 12T)'));
                }
            }
        },

        openTransporterFinder() {
            if (!this.currentUserId || this.currentUserId === 0) {
                window.location.href = '{{ route('login', ['intended' => url()->current(), 'action' => 'transporter']) }}';
                return;
            }

            this.transporterModalOpen = true;
            this.transporterValidationError = '';

            // If user already has active pending requests, open directly on Step 2 (Tracking)!
            if (this.sentTransporterRequests.length > 0) {
                this.transporterStep = 2;
                this.calculateCourseEstimate();
                this.fetchNearestTransporters();
                this.checkTransporterStatus();
            } else {
                this.transporterStep = 1;
                if (this.userLocation?.lat) {
                    this.transporterGpsCoords = { lat: this.userLocation.lat, lng: this.userLocation.lng };
                }
                if (this.userLocation?.governorate && !this.transporterPickupGov) {
                    this.transporterPickupGov = this.userLocation.governorate;
                }
            }

            // Start polling if not already active
            if (this.transporterPollTimer) clearInterval(this.transporterPollTimer);
            this.transporterPollTimer = setInterval(() => {
                if (!this.acceptedMissionData) {
                    this.checkTransporterStatus();
                }
            }, 4000);
        },

        proceedToTransportersList() {
            this.transporterValidationError = '';
            if (!this.transporterPickupGov || !this.transporterPickupGov.trim()) {
                this.transporterValidationError = @js(__('Please select pickup governorate first.'));
                return;
            }
            if (!this.transporterDropoffGov || !this.transporterDropoffGov.trim()) {
                this.transporterValidationError = @js(__('Please select dropoff governorate first.'));
                return;
            }
            if (!this.transporterWeight || this.transporterWeight <= 0) {
                this.transporterValidationError = @js(__('Please enter cargo weight in tons.'));
                return;
            }

            // Step 1 is valid -> proceed to Step 2
            this.transporterStep = 2;
            this.calculateCourseEstimate();
            this.fetchNearestTransporters();
            this.checkTransporterStatus();

            // Clear previous interval if any and start fresh status polling
            if (this.transporterPollTimer) clearInterval(this.transporterPollTimer);
            this.transporterPollTimer = setInterval(() => {
                if (!this.acceptedMissionData) {
                    this.checkTransporterStatus();
                }
            }, 4000);
        },

        backToCourseParameters() {
            this.transporterStep = 1;
            this.transporterValidationError = '';
        },

        async resolveTunisianLocationFromCoords(lat, lng) {
            let detectedGov = null;
            let detectedDel = null;

            // 1. Try reverse geocoding via OpenStreetMap Nominatim with short timeout
            try {
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 3500);
                const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}&accept-language=ar`, {
                    headers: { 'Accept': 'application/json' },
                    signal: controller.signal
                });
                clearTimeout(timeoutId);
                if (res.ok) {
                    const data = await res.json();
                    const addr = data.address || {};

                    // Extract raw governorate string
                    const rawGov = (addr.state || addr.province || addr.region || '').replace(/^ولاية\s+/, '').trim();
                    const govKeys = Object.keys(this.tunisianDelegations);
                    if (rawGov) {
                        detectedGov = govKeys.find(g => rawGov.includes(g) || g.includes(rawGov));
                    }

                    // Extract candidates for delegation
                    const rawCandidates = [
                        addr.county, addr.town, addr.suburb, addr.city, addr.municipality,
                        addr.village, addr.state_district, addr.neighbourhood, addr.city_district
                    ].filter(Boolean).map(c => c.replace(/^(معتمدية|بلدية|مدينة)\s+/, '').trim());

                    if (detectedGov && this.tunisianDelegations[detectedGov]) {
                        const delegations = this.tunisianDelegations[detectedGov];
                        for (const candidate of rawCandidates) {
                            const match = delegations.find(d => candidate.includes(d) || d.includes(candidate));
                            if (match) {
                                detectedDel = match;
                                break;
                            }
                        }
                    } else if (!detectedGov) {
                        for (const [g, dels] of Object.entries(this.tunisianDelegations)) {
                            for (const candidate of rawCandidates) {
                                const match = dels.find(d => candidate.includes(d) || d.includes(candidate));
                                if (match) {
                                    detectedGov = g;
                                    detectedDel = match;
                                    break;
                                }
                            }
                            if (detectedGov) break;
                        }
                    }
                }
            } catch (e) {
                console.warn('Nominatim geocode fallback to centroids:', e);
            }

            // 2. Centroid distance fallback if governorate not yet resolved
            if (!detectedGov) {
                const govCentroids = {
                    'تونس': { lat: 36.8065, lng: 10.1815 },
                    'أريانة': { lat: 36.8665, lng: 10.1647 },
                    'بن عروس': { lat: 36.7533, lng: 10.2222 },
                    'منوبة': { lat: 36.8080, lng: 10.0972 },
                    'نابل': { lat: 36.4561, lng: 10.7376 },
                    'زغوان': { lat: 36.4029, lng: 10.1429 },
                    'بنزرت': { lat: 37.2744, lng: 9.8739 },
                    'باجة': { lat: 36.7256, lng: 9.1817 },
                    'جندوبة': { lat: 36.5011, lng: 8.7802 },
                    'الكاف': { lat: 36.1822, lng: 8.7149 },
                    'سليانة': { lat: 36.0847, lng: 9.3708 },
                    'سوسة': { lat: 35.8256, lng: 10.6369 },
                    'المنستير': { lat: 35.7833, lng: 10.8333 },
                    'المهدية': { lat: 35.5047, lng: 11.0622 },
                    'صفاقس': { lat: 34.7406, lng: 10.7603 },
                    'القيروان': { lat: 35.6781, lng: 10.0963 },
                    'القصرين': { lat: 35.1676, lng: 8.8365 },
                    'سيدي بوزيد': { lat: 35.0382, lng: 9.4849 },
                    'قابس': { lat: 33.8815, lng: 10.0982 },
                    'مدنين': { lat: 33.3549, lng: 10.5055 },
                    'تطاوين': { lat: 32.9297, lng: 10.4518 },
                    'قفصة': { lat: 34.4250, lng: 8.7842 },
                    'توزر': { lat: 33.9197, lng: 8.1335 },
                    'قبلي': { lat: 33.7050, lng: 8.9690 }
                };

                let minDistance = Infinity;
                for (const [gov, c] of Object.entries(govCentroids)) {
                    const d = Math.hypot(lat - c.lat, lng - c.lng);
                    if (d < minDistance) {
                        minDistance = d;
                        detectedGov = gov;
                    }
                }
            }

            // 3. Delegation fallback: first delegation of the matched governorate
            if (detectedGov && !detectedDel && this.tunisianDelegations[detectedGov]) {
                detectedDel = this.tunisianDelegations[detectedGov][0];
            }

            return { governorate: detectedGov, delegation: detectedDel };
        },

        locateTransporterGps() {
            if (!navigator.geolocation) {
                alert(@js(__('GPS is not supported on this browser.')));
                return;
            }
            this.locatingTransporterGps = true;
            navigator.geolocation.getCurrentPosition(
                async (pos) => {
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;
                    this.transporterGpsCoords = { lat, lng };

                    const loc = await this.resolveTunisianLocationFromCoords(lat, lng);
                    if (loc.governorate) {
                        this.transporterPickupGov = loc.governorate;
                        this.transporterPickupDel = loc.delegation || '';
                        this.transporterValidationError = '';
                    }

                    this.locatingTransporterGps = false;

                    if (this.transporterPickupGov && this.transporterDropoffGov) {
                        this.calculateCourseEstimate();
                    }
                },
                (err) => {
                    this.locatingTransporterGps = false;
                    console.warn('GPS location error:', err);
                    if (this.userLocation?.governorate && !this.transporterPickupGov) {
                        this.transporterPickupGov = this.userLocation.governorate;
                        if (this.tunisianDelegations[this.transporterPickupGov]) {
                            this.transporterPickupDel = this.tunisianDelegations[this.transporterPickupGov][0];
                        }
                        this.transporterValidationError = '';
                        if (this.transporterDropoffGov) this.calculateCourseEstimate();
                    } else {
                        alert(@js(__('Unable to access GPS location.')));
                    }
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        },

        async calculateCourseEstimate() {
            if (!this.transporterPickupGov || !this.transporterDropoffGov) return;

            try {
                const params = new URLSearchParams({
                    weight: this.transporterWeight || 1.0,
                    unit: 'ton',
                    cargo_type: this.transporterCargoType || 'oil',
                    pickup_governorate: this.transporterPickupGov,
                    dropoff_governorate: this.transporterDropoffGov,
                });
                if (this.transporterPickupDel) {
                    params.append('pickup_delegation', this.transporterPickupDel);
                }
                if (this.transporterDropoffDel) {
                    params.append('dropoff_delegation', this.transporterDropoffDel);
                }
                if (this.transporterGpsCoords?.lat) {
                    params.append('pickup_lat', this.transporterGpsCoords.lat);
                    params.append('pickup_lng', this.transporterGpsCoords.lng);
                }
                const res = await fetch(`{{ route('transporter_requests.estimate', ['locale' => app()->getLocale()]) }}?${params.toString()}`);
                const data = await res.json();
                if (data.success) {
                    this.transporterCourseDistance = data.distance_km;
                    this.transporterCourseCost = data.estimated_cost;
                }
            } catch (e) {
                console.error('Course estimate error:', e);
            }
        },

        async checkTransporterStatus() {
            if (!this.currentUserId) return;
            try {
                const res = await fetch(`{{ route('transporter_requests.status', ['locale' => app()->getLocale()]) }}`);
                const data = await res.json();
                if (data.success) {
                    if (data.pending_carrier_ids) {
                        this.sentTransporterRequests = data.pending_carrier_ids;
                    }
                    if (typeof data.remaining_quota !== 'undefined') {
                        this.remainingTransporterQuota = data.remaining_quota;
                    }

                    // If active route data exists and we don't have our own pickup/dropoff chosen yet, restore them!
                    if (data.active_route && !this.transporterPickupGov) {
                        this.transporterPickupGov = data.active_route.pickup_gov || '';
                        this.transporterPickupDel = data.active_route.pickup_del || '';
                        this.transporterDropoffGov = data.active_route.dropoff_gov || '';
                        this.transporterDropoffDel = data.active_route.dropoff_del || '';
                        if (data.active_route.weight) {
                            this.transporterWeight = data.active_route.weight;
                        }
                        if (data.active_route.distance_km) {
                            this.transporterCourseDistance = data.active_route.distance_km;
                        }
                        if (data.active_route.cost) {
                            this.transporterCourseCost = data.active_route.cost;
                        }
                    }

                    if (data.accepted === true) {
                        this.acceptedMissionData = data;
                        if (this.transporterPollTimer) {
                            clearInterval(this.transporterPollTimer);
                        }
                        // Auto redirect to chat after 2.5 seconds if modal is open
                        if (this.transporterModalOpen) {
                            setTimeout(() => {
                                if (data.chat_url) {
                                    window.location.href = data.chat_url;
                                }
                            }, 2500);
                        }
                    }
                }
            } catch (e) {
                console.error('Check transporter status error:', e);
            }
        },

        async sendTransporterRequest(carrierId) {
            if (!this.transporterPickupGov || !this.transporterDropoffGov || !this.transporterWeight || this.transporterWeight <= 0) {
                this.transporterStep = 1;
                this.transporterValidationError = '{{ __("يرجى ملء جميع بيانات المسار والحمولة أولاً.") }}';
                return;
            }

            if (this.remainingTransporterQuota <= 0) {
                alert('{{ __("You have reached the maximum limit of 3 pending requests in 10 minutes. Please wait for a carrier to respond.") }}');
                return;
            }

            this.requestingCarrierId = carrierId;
            try {
                const payload = {
                    carrier_id: carrierId,
                    weight: this.transporterWeight,
                    unit: 'ton',
                    cargo_type: this.transporterCargoType || 'oil',
                    pickup_governorate: this.transporterPickupGov,
                    pickup_delegation: this.transporterPickupDel || null,
                    dropoff_governorate: this.transporterDropoffGov,
                    dropoff_delegation: this.transporterDropoffDel || null,
                };
                if (this.transporterGpsCoords?.lat) {
                    payload.pickup_lat = this.transporterGpsCoords.lat;
                    payload.pickup_lng = this.transporterGpsCoords.lng;
                }

                const res = await fetch(`{{ route('transporter_requests.store', ['locale' => app()->getLocale()]) }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();

                if (data.success) {
                    if (!this.sentTransporterRequests.includes(carrierId)) {
                        this.sentTransporterRequests.push(carrierId);
                    }
                    if (typeof data.remaining_quota !== 'undefined') {
                        this.remainingTransporterQuota = data.remaining_quota;
                    }
                    // Start polling status immediately if not active
                    if (!this.transporterPollTimer) {
                        this.transporterPollTimer = setInterval(() => this.checkTransporterStatus(), 4000);
                    }
                } else {
                    alert(data.message || '{{ __("Unable to send request.") }}');
                }
            } catch (e) {
                console.error('Failed to send transporter request:', e);
                alert('{{ __("Network error while sending request.") }}');
            } finally {
                this.requestingCarrierId = null;
            }
        },

        async cancelTransporterRequest(carrierId) {
            this.cancellingCarrierId = carrierId;
            try {
                const res = await fetch(`{{ route('transporter_requests.cancel', ['locale' => app()->getLocale()]) }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ carrier_id: carrierId })
                });
                const data = await res.json();
                if (data.success) {
                    this.sentTransporterRequests = this.sentTransporterRequests.filter(id => id !== carrierId);
                    if (typeof data.remaining_quota !== 'undefined') {
                        this.remainingTransporterQuota = data.remaining_quota;
                    }
                    this.checkTransporterStatus();
                } else {
                    alert(data.message || '{{ __("Unable to cancel request.") }}');
                }
            } catch (e) {
                console.error('Failed to cancel transporter request:', e);
                alert('{{ __("Network error while cancelling request.") }}');
            } finally {
                this.cancellingCarrierId = null;
            }
        },

        async fetchNearestTransporters() {
            this.loadingTransporters = true;
            try {
                const params = new URLSearchParams();
                if (this.transporterGpsCoords?.lat) {
                    params.append('lat', this.transporterGpsCoords.lat);
                    params.append('lng', this.transporterGpsCoords.lng);
                }
                if (this.transporterPickupGov) {
                    params.append('governorate', this.transporterPickupGov);
                }
                if (this.transporterWeight) {
                    params.append('required_qty', this.transporterWeight);
                    params.append('unit', 'ton');
                }
                const res = await fetch(`{{ route('carriers.search', ['locale' => app()->getLocale()]) }}?${params.toString()}`);
                const data = await res.json();
                if (data.success && data.carriers) {
                    this.transportersList = data.carriers;
                }
            } catch (e) {
                console.error('Failed to fetch nearest transporters:', e);
            } finally {
                this.loadingTransporters = false;
            }
        },

        /**
         * Get the preferred display/business name for a seller on listing cards
         */
        getSellerDisplayName(seller) {
            if (!seller) return '';
            if (seller.role === 'admin') return '{{ __('Seller') }}';
            return seller.display_name || seller.mill_name || seller.packer_name || seller.company_name || seller.farm_name || seller.name || '';
        },

        // ── Live currency rates (injected from CurrencyConverter, cached 3h) ──
        // Rate: how many USD = 1 unit of source currency
        tndToUsd: {{ number_format(app(\App\Services\CurrencyConverter::class)->getTndToUsd(), 6, '.', '') }},
        eurToUsd: {{ number_format(app(\App\Services\CurrencyConverter::class)->getEurToUsd(), 6, '.', '') }},
        sarToUsd: 0.2667, // SAR is pegged — rarely changes

        /**
         * Convert a listing price from its stored currency to the display currency.
         *
         * Rules:
         *   locale = 'ar' → display in TND
         *   locale = 'en'|'fr' → display in USD
         *
         * The listing may be stored in TND, USD, EUR, SAR, etc.
         * We normalise everything through USD then to the display currency.
         *
         * @param {number|string} amount
         * @param {string}        storedCurrency  e.g. 'TND', 'USD', 'EUR'
         * @returns {string}  formatted price string, e.g. '$3.97' or '12.50 TND'
         */
        formatPrice(amount, storedCurrency) {
            const num = parseFloat(amount);
            if (!num || num <= 0) return '';

            const src = (storedCurrency || 'TND').toUpperCase();
            const displayAr = this.locale === 'ar';

            // Step 1: normalise to USD
            let amountInUsd;
            if (src === 'USD') {
                amountInUsd = num;
            } else if (src === 'TND') {
                amountInUsd = num * this.tndToUsd;
            } else if (src === 'EUR') {
                amountInUsd = num * this.eurToUsd;
            } else if (src === 'SAR') {
                amountInUsd = num * this.sarToUsd;
            } else {
                // Unknown currency — display as-is with its code
                return num.toFixed(2) + ' ' + src;
            }

            if (displayAr) {
                // Arabic → TND
                // Use original num directly if already TND to avoid floating-point drift
                const tnd = (src === 'TND') ? num : (amountInUsd / this.tndToUsd);
                const rounded = Math.round(tnd);

                // If integer, never display .00 (no decimals)
                if (Math.abs(tnd - rounded) < 0.0001) {
                    return rounded + ' دينار';
                }

                // If fractional, display 3 decimal places (Tunisian millimes standard)
                return tnd.toFixed(3) + ' دينار';
            } else {
                // English / French → USD
                const roundedUsd = Math.round(amountInUsd);
                if (Math.abs(amountInUsd - roundedUsd) < 0.0001) {
                    return '$' + roundedUsd.toLocaleString();
                }
                return '$' + amountInUsd.toFixed(2);
            }
        },

        /**
         * Returns a short locale-aware unit label like "/ kg", "/ L", "/ ton"
         * to display beside the price. Returns empty string for whole-farm units.
         */
        formatUnit(listing) {
            const isAr = this.locale === 'ar';
            const isFr = this.locale === 'fr';
            
            const isSaniya = listing.sale_mode === 'saniya' 
                || (listing.packaging && listing.packaging.includes('سانية'))
                || listing.unit === 'سانية'
                || listing.price_mode === 'whole';

            if (isSaniya && (listing.price_mode === 'whole' || listing.unit === 'سانية' || !listing.price_mode)) {
                return isAr ? '/ السانية بالكامل' : (isFr ? '/ Verger complet' : '/ Whole orchard');
            }

            let unit = listing.unit || listing.product?.unit || '';
            if (isSaniya && (unit === 'سانية' || unit === 'saniya' || !unit)) {
                unit = 'ton';
            }

            const map = {
                'kg':     isAr ? '/ كغ' : '/ kg',
                'liter':  isAr ? '/ لتر' : (isFr ? '/ L' : '/ L'),
                'ton':    isAr ? '/ طن' : (isFr ? '/ tonne' : '/ ton'),
                'tonne':  isAr ? '/ طن' : (isFr ? '/ tonne' : '/ ton'),
                'bottle': isAr ? '/ قارورة' : (isFr ? '/ bouteille' : '/ bottle'),
                'can':    isAr ? '/ صفيحة' : (isFr ? '/ bidon' : '/ can'),
                'barrel': isAr ? '/ برميل' : (isFr ? '/ fût' : '/ barrel'),
                'piece':  isAr ? '/ قطعة' : (isFr ? '/ pièce' : '/ piece'),
                'كغ':    '/ كغ',
                'لتر':  isAr ? '/ لتر' : '/ L',
                'طن':    isAr ? '/ طن' : (isFr ? '/ tonne' : '/ ton'),
            };
            return map[unit] !== undefined ? map[unit] : (unit ? '/ ' + unit : '');
        },

        getDisplayPrice(amount, storedCurrency) {
            const num = parseFloat(amount);
            if (!num || num <= 0) return 0;

            const src = (storedCurrency || 'TND').toUpperCase();
            const displayAr = this.locale === 'ar';

            let amountInUsd;
            if (src === 'USD') {
                amountInUsd = num;
            } else if (src === 'TND') {
                amountInUsd = num * this.tndToUsd;
            } else if (src === 'EUR') {
                amountInUsd = num * this.eurToUsd;
            } else if (src === 'SAR') {
                amountInUsd = num * this.sarToUsd;
            } else {
                return num;
            }

            if (displayAr) {
                return amountInUsd / this.tndToUsd;
            } else {
                return amountInUsd;
            }
        },
        currentArticle: {
            id: null,
            title: {},
            content: {},
            image: ''
        },
        openArticle(id, title, content, image) {
            this.currentArticle = { id, title, content, image };
            this.articleModalOpen = true;
            document.body.classList.add('overflow-hidden');
        },
        userLocation: {
            lat: null,
            lng: null
        },
        filters: {
            type: 'all',
            qualities: [],
            priceMin: '',
            priceMax: '',
            sortBy: 'newest',
            distance: 'all'
        },
        // Deal Request Modal
        dealRequestModalOpen: false,
        currentDeal: { id: null, title: '', type: '' },
        


        openDealRequest(id, title, type) {
            this.currentDeal = { id, title, type };
            this.dealRequestModalOpen = true;
            document.body.classList.add('overflow-hidden');
        },
        openQuickUploadModal(listing) {
            this.targetListing = listing;
            this.quickFilePreview = null;
            this.quickUploadModalOpen = true;
        },
        handleQuickFileChange(event) {
            const file = event.target.files[0];
            if (file) {
                this.quickFilePreview = URL.createObjectURL(file);
            }
        },
        async submitQuickUpload(event) {
            if (!this.targetListing) return;
            const formData = new FormData(event.target);
            this.uploadingQuickFile = true;
            try {
                const response = await fetch(`/listings/${this.targetListing.id}/quick-upload`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                const result = await response.json();
                if (result.success) {
                    this.targetListing.media = result.media;
                    const found = this.listings.find(l => l.id === this.targetListing.id);
                    if (found) found.media = result.media;
                    this.quickUploadModalOpen = false;
                } else {
                    alert(result.error || 'حدث خطأ أثناء رفع الصورة');
                }
            } catch (e) {
                alert('حدث خطأ في الاتصال بالسيرفر');
            } finally {
                this.uploadingQuickFile = false;
            }
        },
        arabicToLatinInitial(char) {
            const map = {
                'ا': 'A', 'أ': 'A', 'إ': 'A', 'آ': 'A',
                'ب': 'B', 'ت': 'T', 'ث': 'T', 'ج': 'J',
                'ح': 'H', 'خ': 'K', 'د': 'D', 'ذ': 'D',
                'ر': 'R', 'ز': 'Z', 'س': 'S', 'ش': 'S',
                'ص': 'S', 'ض': 'D', 'ط': 'T', 'ظ': 'Z',
                'ع': 'A', 'غ': 'G', 'ف': 'F', 'ق': 'K',
                'ك': 'K', 'ل': 'L', 'م': 'M', 'ن': 'N',
                'ه': 'H', 'ة': 'H', 'و': 'W', 'ي': 'Y',
                'ى': 'Y', 'ئ': 'Y', 'ؤ': 'Y'
            };
            return map[char] || char.toUpperCase();
        },
        getInitials(text) {
            if (!text) return 'ZT';
            const clean = text.trim();
            const parts = clean.split(/\s+/);
            
            if (parts.length === 1) {
                return clean.substring(0, 4).split('').map(char => this.arabicToLatinInitial(char)).join('').toUpperCase();
            }
            
            return parts.map(word => this.arabicToLatinInitial(word.charAt(0))).join('').toUpperCase();
        },
        translations: {
            'Extra Virgin Olive Oil': @json(__('Extra Virgin Olive Oil')),
            'Chemlali Olive Oil': @json(__('Chemlali Olive Oil')),
            'Chetoui Olive Oil': @json(__('Chetoui Olive Oil')),
            'Chaibi Olive Oil': @json(__('Chaibi Olive Oil')),
            'Organic Extra Virgin': @json(__('Organic Extra Virgin')),
            'Cold Pressed Olive Oil': @json(__('Cold Pressed Olive Oil')),
            'Premium Blend Olive Oil': @json(__('Premium Blend Olive Oil')),
            'Chemlali Olives': @json(__('Chemlali Olives')),
            'Chetoui Olives': @json(__('Chetoui Olives')),
            'Chaibi Olives': @json(__('Chaibi Olives')),
            'Meski Olives': @json(__('Meski Olives')),
            'Zalmati Olives': @json(__('Zalmati Olives')),
            'Fresh Olives': @json(__('Fresh Olives')),
            'Table Olives': @json(__('Table Olives')),
            // Varieties
            'chemlali': @json(__('chemlali')),
            'chaibi':   @json(__('chaibi')),
            'gerboui':  @json(__('gerboui')),
            'chetoui':  @json(__('chetoui')),
            'meski':    @json(__('meski')),
            'zalmati':  @json(__('zalmati')),
            'koroneiki':@json(__('koroneiki')),
            'jemlati':  @json(__('jemlati')),
            'barouni':  @json(__('barouni')),
            // Quality grades (Arabic keys)
            'بكر ممتاز (evoo)': @json(app()->getLocale() === 'ar' ? 'بكر ممتاز (EVOO)' : (app()->getLocale() === 'fr' ? 'Vierge Extra (EVOO)' : 'Extra Virgin (EVOO)')),
            'بكر (virgin)': @json(app()->getLocale() === 'ar' ? 'بكر (Virgin)' : (app()->getLocale() === 'fr' ? 'Vierge' : 'Virgin')),
            'بكر عادي (ordinary virgin)': @json(app()->getLocale() === 'ar' ? 'بكر عادي' : (app()->getLocale() === 'fr' ? 'Vierge Courante' : 'Ordinary Virgin')),
            'وقاد (lampante)': @json(app()->getLocale() === 'ar' ? 'وقاد (Lampante)' : 'Lampante'),
            'بيولوجي (organic)': @json(app()->getLocale() === 'ar' ? 'بيولوجي (Organic)' : (app()->getLocale() === 'fr' ? 'Biologique (Bio)' : 'Organic')),
            // Packaging
            'صبّة (vrac)': @json(app()->getLocale() === 'ar' ? 'صبّة (Vrac)' : (app()->getLocale() === 'fr' ? 'En Vrac' : 'Bulk (Vrac)')),
            'معلّب (packaged)': @json(app()->getLocale() === 'ar' ? 'معلّب (Packaged)' : (app()->getLocale() === 'fr' ? 'Emballé' : 'Packaged')),
            'جملة (gros)': @json(app()->getLocale() === 'ar' ? 'جملة (Gros)' : (app()->getLocale() === 'fr' ? 'En Gros' : 'Wholesale')),
            'تفصيل (détail)': @json(app()->getLocale() === 'ar' ? 'تفصيل (Détail)' : (app()->getLocale() === 'fr' ? 'Détail' : 'Retail')),
            'جملة / صب (vrac)': @json(app()->getLocale() === 'ar' ? 'جملة / صبّة' : (app()->getLocale() === 'fr' ? 'Gros / En Vrac' : 'Wholesale / Bulk')),
            'جملة / صب': @json(app()->getLocale() === 'ar' ? 'جملة / صبّة' : (app()->getLocale() === 'fr' ? 'Gros / En Vrac' : 'Wholesale / Bulk')),
            // Packaging — sale mode labels
            'سانية للتخضير': @json(app()->getLocale() === 'ar' ? 'سانية للتخضير' : (app()->getLocale() === 'fr' ? 'Vente sur pied' : 'Standing Crop Sale')),
            'سانية للخضارة': @json(app()->getLocale() === 'ar' ? 'سانية للتخضير' : (app()->getLocale() === 'fr' ? 'Vente sur pied' : 'Standing Crop Sale')),
            'زيتون حب': @json(app()->getLocale() === 'ar' ? 'زيتون حب' : (app()->getLocale() === 'fr' ? 'Olives en grain' : 'Olive Drupes')),
            // Misc UI
            "This website is secure and does not have redirected links that bother users.": @json(__('This website is secure and does not have redirected links that bother users.')),
            "ZinToop connects direct olive oil producers with buyers across Tunisia effortlessly.": @json(__('ZinToop connects direct olive oil producers with buyers across Tunisia effortlessly.')),
            "Use the search bar and location filters to find the best quality oil near you.": @json(__('Use the search bar and location filters to find the best quality oil near you.')),
            "Our vision is to digitize the olive oil sector and ensure fair trade for every producer.": @json(__('Our vision is to digitize the olive oil sector and ensure fair trade for every producer.'))
        },

        /**
         * Comprehensive Arabic ↔ Latin dictionary for Tunisian locations.
         * Keys are Arabic, values are [en, fr] pair.
         * Used by translateLocation() and translate() for substring replacement.
         *
         * Covers all 24 governorates + ~120 major delegations.
         */
        locationDict: {
            // ── Governorates ──────────────────────────────────────────────────
            'تونس':        ['Tunis',        'Tunis'],
            'أريانة':      ['Ariana',       'Ariana'],
            'بن عروس':     ['Ben Arous',    'Ben Arous'],
            'منوبة':       ['Manouba',      'Manouba'],
            'نابل':        ['Nabeul',       'Nabeul'],
            'زغوان':       ['Zaghouan',     'Zaghouan'],
            'بنزرت':       ['Bizerte',      'Bizerte'],
            'باجة':        ['Béja',         'Béja'],
            'جندوبة':      ['Jendouba',     'Jendouba'],
            'الكاف':       ['Le Kef',       'Le Kef'],
            'سليانة':      ['Siliana',      'Siliana'],
            'سوسة':        ['Sousse',       'Sousse'],
            'المنستير':    ['Monastir',     'Monastir'],
            'المهدية':     ['Mahdia',       'Mahdia'],
            'صفاقس':       ['Sfax',         'Sfax'],
            'القيروان':    ['Kairouan',     'Kairouan'],
            'القصرين':     ['Kasserine',    'Kasserine'],
            'سيدي بوزيد':  ['Sidi Bouzid',  'Sidi Bouzid'],
            'قابس':        ['Gabes',        'Gabès'],
            'مدنين':       ['Medenine',     'Médenine'],
            'تطاوين':      ['Tataouine',    'Tataouine'],
            'قفصة':        ['Gafsa',        'Gafsa'],
            'توزر':        ['Tozeur',       'Tozeur'],
            'قبلي':        ['Kebili',       'Kébili'],
            // ── Major Delegations / Cities ────────────────────────────────────
            'العاصمة':     ['Tunis City',   'Ville de Tunis'],
            'باب بحر':     ['Bab Bhar',     'Bab Bhar'],
            'باب سويقة':   ['Bab Souika',   'Bab Souika'],
            'المرسى':      ['La Marsa',     'La Marsa'],
            'سيدي بوسعيد': ['Sidi Bou Said','Sidi Bou Saïd'],
            'قرطاج':       ['Carthage',     'Carthage'],
            'حمام الشط':   ['Hammam Lif',   'Hammam Lif'],
            'رادس':        ['Rades',        'Radès'],
            'مقرين':       ['Mourouj',      'Mourouj'],
            'حمام الأنف':  ['Hammam Lif',   'Hammam Lif'],
            'الزهراء':     ['Zahra',        'Zahra'],
            'برج العامري': ['Borj El Amri', 'Borj El Amri'],
            'دوار هيشر':   ['Douar Hicher', 'Douar Hicher'],
            'الوردية':     ['El Ouardia',   'El Ouardia'],
            'حمام الأنف':  ['Hammam-Lif',   'Hammam-Lif'],
            'الكرم':       ['El Kram',      'El Kram'],
            'الحمامات':    ['Hammamet',     'Hammamet'],
            'نابل':        ['Nabeul',       'Nabeul'],
            'قربة':        ['Korba',        'Korba'],
            'كرمبالية':    ['Kelibia',      'Kélibia'],
            'منزل تميم':   ['Menzel Temime','Menzel Temime'],
            'منزل بوزلفة': ['Menzel Bou Zelfa','Menzel Bou Zelfa'],
            'بئر بورقبة':  ['Bir Bou Rekba','Bir Bou Rekba'],
            'سوسة المدينة':['Sousse City',  'Sousse Ville'],
            'القلعة الكبرى':['Kalaa Kebira','Kalaa Kebira'],
            'القلعة الصغرى':['Kalaa Sghira','Kalaa Sghira'],
            'أكودة':       ['Akouda',       'Akouda'],
            'هرقلة':       ['Hergla',       'Hergla'],
            'النفيضة':     ['Enfidha',      'Enfidha'],
            'زاوية سوسة':  ['Zaouia Sousse','Zaouia Sousse'],
            'سيدي الهاني': ['Sidi El Heni', 'Sidi El Héni'],
            'المسعدين':    ['Msaken',       'M\'saken'],
            'القنطاوي':    ['Kantaoui',     'Kantaoui'],
            'المنستير مدينة':['Monastir City','Monastir Ville'],
            'قصر هلال':    ['Ksar Hellal',  'Ksar Hellal'],
            'طبلبة':       ['Teboulba',     'Téboulba'],
            'جمال':        ['Jemmal',       'Jemmal'],
            'المكنين':     ['Moknine',      'Moknine'],
            'بنبلة':       ['Bembla',       'Bembla'],
            'خنيس':        ['Khniss',       'Khniss'],
            'ساقية الداير':['Saket El Daïer','Saket El Daïer'],
            'بو حجر':      ['Bouhjar',      'Bouhjar'],
            'المهدية مدينة':['Mahdia City', 'Mahdia Ville'],
            'قصور الساف':  ['Ksour Essef',  'Ksour Essef'],
            'الجم':        ['El Jem',       'El Djem'],
            'الشابة':      ['Chebba',       'Chebba'],
            'بومرداس':     ['Bou Merdès',   'Bou Merdès'],
            'سيدي علوان':  ['Sidi Alouane', 'Sidi Alouane'],
            'صفاقس المدينة':['Sfax City',   'Sfax Ville'],
            'صفاقس الجنوب':['Sfax South',   'Sfax Sud'],
            'الساحل':      ['Sahel',        'Sahel'],
            'العين':       ['El Ain',       'El Ain'],
            'جبنيانة':     ['Djebeniana',   'Djebeniana'],
            'الحنشة':      ['El Hencha',    'El Hencha'],
            'منزل شاكر':   ['Menzel Chaker','Menzel Chaker'],
            'الصخيرة':     ['Skhira',       'Skhira'],
            'محرس':        ['Mahres',       'Mahres'],
            'قرقنة':       ['Kerkennah',    'Kerkennah'],
            'العلا':       ['Al-Ala',       'El Ala'],
            'حفوز':        ['Haffouz',      'Haffouz'],
            'الشبيكة':     ['Chebia',       'Chebia'],
            'السبيخة':     ['Sbikha',       'Sbikha'],
            'نصر الله':    ['Nasrallah',    'Nasrallah'],
            'بوحجلة':      ['Bouhajla',     'Bouhajla'],
            'المناصف':     ['Mnassef',      'M\'nassef'],
            'الوسلاتية':   ['Oueslatia',    'Oueslatia'],
            'كرواة':       ['Krouaa',       'Krouaa'],
            'الشريعة':     ['Cheraia',      'Chéraia'],
            'العيون':      ['El Ayoun',     'El Ayoun'],
            'حيدرة':       ['Haidra',       'Haïdra'],
            'قصر الزيت':   ['Ksour Zbib',   'Ksour Zbib'],
            'جلمة':        ['Jelma',        'Jelma'],
            'مكثر':        ['Makthar',      'Makthar'],
            'سليانة مدينة':['Siliana City', 'Siliana Ville'],
            'روحية':       ['Rohia',        'Rohia'],
            'سيدي بورويس': ['Sidi Bou Rouis','Sidi Bou Rouis'],
            'قعفور':       ['Gaafour',      'Gaâfour'],
            'الكريب':      ['El Krib',      'El Krib'],
            'باجة مدينة':  ['Beja City',    'Béja Ville'],
            'نفزة':        ['Nefza',        'Nefza'],
            'تبرسق':       ['Teboursouk',   'Téboursouk'],
            'قبة الكوش':   ['Testour',      'Testour'],
            'تستور':       ['Testour',      'Testour'],
            'عين الدراهم': ['Ain Draham',   'Aïn Draham'],
            'جندوبة مدينة':['Jendouba City','Jendouba Ville'],
            'بوسالم':      ['Bou Salem',    'Bou Salem'],
            'طبرقة':       ['Tabarka',      'Tabarka'],
            'الكاف مدينة': ['Le Kef City',  'Le Kef Ville'],
            'الدهماني':    ['Dahmani',      'Dahmani'],
            'نبر':         ['Nebeur',       'Nebeur'],
            'الجريصة':     ['Jerissa',      'Jérissa'],
            'القصرين مدينة':['Kasserine City','Kasserine Ville'],
            'سبيطلة':      ['Sbeitla',      'Sbeïtla'],
            'فريانة':      ['Feriana',      'Fériana'],
            'تالة':        ['Thala',        'Thala'],
            'حيدرة':       ['Haidra',       'Haïdra'],
            'أم العرائس':  ['Oum El Arayess','Oum El Arayès'],
            'سيدي بوزيد مدينة':['Sidi Bouzid City','Sidi Bouzid Ville'],
            'بئر الحفي':   ['Bir El Hafay', 'Bir El Hafey'],
            'سيدي علي بن عون':['Sidi Ali Ben Aoun','Sidi Ali Ben Aoun'],
            'المزونة':     ['Mezzouna',     'Mezzouna'],
            'الرقاب':      ['Regueb',       'Regueb'],
            'قابس مدينة':  ['Gabès City',   'Gabès Ville'],
            'مطماطة':      ['Matmata',      'Matmata'],
            'مدنين مدينة': ['Médenine City','Médenine Ville'],
            'جرجيس':       ['Zarzis',       'Zarzis'],
            'جربة':        ['Djerba',       'Djerba'],
            'جربة الحومة': ['Djerba Houmt Souk','Djerba Houmt Souk'],
            'بن قردان':    ['Ben Guerdane', 'Ben Guerdane'],
            'تطاوين مدينة':['Tataouine City','Tataouine Ville'],
            'غمراسن':      ['Ghomrassen',   'Ghomrassen'],
            'بني خداش':    ['Beni Khedache','Beni Khédache'],
            'الصمار':      ['Smar',         'Smar'],
            'قفصة مدينة':  ['Gafsa City',   'Gafsa Ville'],
            'المتلوي':      ['Metlaoui',     'Métlaoui'],
            'الرديف':      ['Redeyef',      'Redeyef'],
            'أم العرائس':  ['Oum El Arayess','Oum El Arayès'],
            'ذهيبة':       ['Dhibat',       'Dhiba'],
            'توزر مدينة':  ['Tozeur City',  'Tozeur Ville'],
            'نفطة':        ['Nefta',        'Nefta'],
            'قبلي مدينة':  ['Kebili City',  'Kébili Ville'],
            'دوز':         ['Douz',         'Douz'],
            'برج الخضراء': ['Borj El Khadra','Borj El Khadra'],
        },

        /**
         * Detect if a string contains Arabic characters.
         */
        hasArabic(text) {
            return /[\u0600-\u06FF\u0750-\u077F]/.test(text);
        },

        /**
         * Translate a location/address string to the display language.
         *
         * Works on:
         *   - Pure Arabic strings:   "القيروان" → "Kairouan"
         *   - Mixed strings:         "العلا القيروان" → "Al-Ala, Kairouan"
         *   - Already Latin strings: returned unchanged
         *
         * For Arabic locale: returns the original Arabic text as-is.
         */
        translateLocation(text) {
            if (!text) return '';
            const isAr = this.locale === 'ar';
            const isFr = this.locale === 'fr';

            // Arabic locale — show original Arabic
            if (isAr) return text;

            // No Arabic content — return as-is (already Latin)
            if (!this.hasArabic(text)) return text;

            // Try a full-string match first (most common: single governorate)
            const fullKey = text.trim();
            if (this.locationDict[fullKey]) {
                return isFr ? this.locationDict[fullKey][1] : this.locationDict[fullKey][0];
            }

            // Multi-word: split on spaces and commas, translate each Arabic token
            let result = text;
            // Sort keys by length descending so longer phrases match before shorter ones
            const sortedKeys = Object.keys(this.locationDict).sort((a, b) => b.length - a.length);
            for (const key of sortedKeys) {
                if (result.includes(key)) {
                    const replacement = isFr ? this.locationDict[key][1] : this.locationDict[key][0];
                    result = result.split(key).join(replacement);
                }
            }

            // If result still has Arabic (unknown terms), leave them as-is
            return result.trim();
        },

        /**
         * Enhanced translate() — handles both the existing key-value lookup
         * AND Arabic substring replacement for mixed-language text.
         */
        translate(text) {
            if (!text) return '';
            const lower = text.toLowerCase().trim();

            // 1. Exact match in translations dict
            if (this.translations[text]) return this.translations[text];
            if (this.translations[lower]) return this.translations[lower];

            // 2. If no Arabic, return as-is
            if (!this.hasArabic(text)) return text;

            // 3. Arabic location token replacement (for mixed strings like "from القيروان")
            return this.translateLocation(text);
        },

        /**
         * Returns an array of delivery option pill objects {icon, label}
         * for the current locale, parsed from listing.delivery_options.
         */
        getDeliveryPills(listing) {
            if (!listing.delivery_options) return [];
            const isAr = this.locale === 'ar';
            const isFr = this.locale === 'fr';
            const labels = {
                pickup:         isAr ? '📍 استلام' : (isFr ? '📍 Sur place'       : '📍 Pickup'),
                local_delivery: isAr ? '🚚 توصيل'   : (isFr ? '🚚 Livraison'      : '🚚 Local Delivery'),
                export:         isAr ? '🚢 تصدير'   : (isFr ? '🚢 Export'         : '🚢 Export'),
                carrier:        isAr ? '📦 ناقل'    : (isFr ? '📦 Transporteur'   : '📦 Carrier'),
            };
            let opts = listing.delivery_options;
            if (typeof opts === 'string') {
                try { opts = JSON.parse(opts); } catch(e) {
                    opts = opts.split(',').map(s => s.trim()).filter(Boolean);
                }
            }
            if (!Array.isArray(opts)) return [];
            return opts.filter(k => labels[k]).map(k => labels[k]);
        },

        getEnhancedTitle(listing) {
            const isAr = this.locale === 'ar';
            const isFr = this.locale === 'fr';

            let typeLabel = '';
            if (listing.product?.type === 'olive') {
                typeLabel = isAr ? 'زيتون' : (isFr ? 'Olives' : 'Olives');
            } else {
                typeLabel = isAr ? 'زيت زيتون' : (isFr ? 'Huile d\'Olive' : 'Olive Oil');
            }

            const variety = this.translate(listing.product?.variety) || '';
            const quality = listing.product?.quality ? ' ' + this.translate(listing.product.quality) : '';

            let organic = '';
            if (listing.product?.is_organic && listing.product?.quality !== 'بيولوجي (Organic)') {
                organic = isAr ? ' عضوي' : (isFr ? ' Biologique' : ' Organic');
            }

            let city = '';
            const rawCity = listing.governorate || listing.address?.governorate || (listing.seller?.addresses?.length > 0 ? listing.seller.addresses[0]?.governorate : '') || listing.seller?.farm_location || listing.seller?.location || '';
            if (rawCity) {
                const translatedCity = this.translateLocation(rawCity);
                city = (isAr ? ' من ' : (isFr ? ' de ' : ' from ')) + translatedCity;
            }

            return `${typeLabel} ${variety}${quality}${organic}${city}`.trim();
        },


        get hasActiveFilters() {
            return this.filters.type !== 'all' ||
                   (this.filters.qualities && this.filters.qualities.length > 0) ||
                   (this.filters.priceMin !== '' && this.filters.priceMin !== null) ||
                   (this.filters.priceMax !== '' && this.filters.priceMax !== null) ||
                   this.filters.distance !== 'all' ||
                   (this.searchQuery && this.searchQuery.trim() !== '');
        },

        init() {
            // Always show filters on desktop (>= 1024px)
            if (window.innerWidth >= 1024) {
                this.showFilters = true;
            }

            // Track scroll state for floating action button translucency
            this.isScrolled = window.scrollY > 40;
            let lastScrollY = window.scrollY;
            const handleMobileScroll = () => {
                this.isScrolled = window.scrollY > 40;
                if (window.innerWidth < 1024 && this.showFilters) {
                    const currentScrollY = window.scrollY;
                    if (currentScrollY > lastScrollY + 5) {
                        const activeEl = document.activeElement;
                        const isInputFocused = activeEl && ['INPUT', 'SELECT', 'TEXTAREA'].includes(activeEl.tagName) && activeEl.closest('.filter-container');
                        if (!isInputFocused) {
                            this.showFilters = false;
                        }
                    }
                    lastScrollY = currentScrollY;
                } else {
                    lastScrollY = window.scrollY;
                }
            };
            window.addEventListener('scroll', handleMobileScroll, { passive: true });
            window.addEventListener('touchmove', handleMobileScroll, { passive: true });

            this.$watch('articleModalOpen', value => {
                if (!value && !this.dealRequestModalOpen) document.body.classList.remove('overflow-hidden');
            });

            this.$watch('dealRequestModalOpen', value => {
                if (!value && !this.articleModalOpen) document.body.classList.remove('overflow-hidden');
            });

            
            this.filteredListings = this.listings;
            this.setupInfiniteScroll();
            // Try to get saved location from localStorage
            try {
                const savedLocation = localStorage.getItem('userLocation');
                if (savedLocation && savedLocation !== 'undefined') {
                    this.userLocation = JSON.parse(savedLocation);
                    this.calculateDistances();
                    this.filterListings();
                }
            } catch (e) {
                console.error("Failed to parse userLocation from localStorage:", e);
                localStorage.removeItem('userLocation');
            }

            // Resume intended actions after login (e.g. ?action=transporter)
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('action') === 'transporter') {
                this.openTransporterFinder();
            } else if (this.currentUserId) {
                // Check if user has active transporter requests or accepted missions to show on FAB
                this.checkTransporterStatus();
            }

            // Restore scroll and pagination if user returned from viewing a product
            const savedScrollY = sessionStorage.getItem('marketplace_scroll_y');
            const savedCount = sessionStorage.getItem('marketplace_visible_count');
            const savedListingId = sessionStorage.getItem('marketplace_last_listing_id');

            if (savedCount) {
                const countNum = parseInt(savedCount, 10);
                if (countNum > this.visibleCount) {
                    this.visibleCount = Math.min(countNum, this.listings.length);
                }
            }

            if (savedScrollY) {
                this.$nextTick(() => {
                    setTimeout(() => {
                        window.scrollTo({
                            top: parseInt(savedScrollY, 10),
                            behavior: 'instant'
                        });
                        if (savedListingId) {
                            const card = document.getElementById('listing-item-' + savedListingId) || document.getElementById('listing-item-list-' + savedListingId);
                            if (card) {
                                card.classList.add('ring-4', 'ring-[#6A8F3B]', 'shadow-2xl');
                                setTimeout(() => {
                                    card.classList.remove('ring-4', 'ring-[#6A8F3B]', 'shadow-2xl');
                                }, 2500);
                            }
                        }
                        sessionStorage.removeItem('marketplace_scroll_y');
                        sessionStorage.removeItem('marketplace_last_listing_id');
                    }, 150);
                });
            }
        },

        saveBrowseState(listing) {
            try {
                sessionStorage.setItem('marketplace_scroll_y', window.scrollY);
                sessionStorage.setItem('marketplace_visible_count', this.visibleCount);
                if (listing && listing.id) {
                    sessionStorage.setItem('marketplace_last_listing_id', listing.id);
                }
            } catch (e) {}
        },

        get totalListings() {
            return this.listings.length;
        },

        get oilCount() {
            return this.listings.filter(l => l.product?.type === 'oil').length;
        },

        get oliveCount() {
            return this.listings.filter(l => l.product?.type === 'olive').length;
        },

        getMyLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        this.userLocation = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        };
                        localStorage.setItem('userLocation', JSON.stringify(this.userLocation));
                        this.calculateDistances();
                        this.filters.sortBy = 'nearest';
                        this.filterListings();
                    },
                    (error) => {
                        alert('لم نتمكن من تحديد موقعك. الرجاء السماح بالوصول إلى الموقع.');
                    }
                );
            } else {
                alert('المتصفح لا يدعم تحديد الموقع.');
            }
        },

        calculateDistances() {
            if (!this.userLocation.lat) return;

            this.listings.forEach(listing => {
                // Prioritize listing-specific coordinates, fallback to seller address
                const lat = listing.lat ?? listing.address?.lat ?? (listing.seller?.addresses?.length > 0 ? listing.seller.addresses[0]?.lat : null);
                const lng = listing.lng ?? listing.address?.lng ?? (listing.seller?.addresses?.length > 0 ? listing.seller.addresses[0]?.lng : null);
                if (lat && lng) {
                    listing.distance = this.calculateDistance(
                        this.userLocation.lat,
                        this.userLocation.lng,
                        lat,
                        lng
                    );
                }
            });
        },

        calculateDistance(lat1, lon1, lat2, lon2) {
            // Haversine formula for calculating distance between two coordinates
            const R = 6371; // Radius of the earth in km
            const dLat = this.deg2rad(lat2 - lat1);
            const dLon = this.deg2rad(lon2 - lon1);
            const a =
                Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(this.deg2rad(lat1)) * Math.cos(this.deg2rad(lat2)) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            const distance = R * c; // Distance in km
            return distance;
        },

        deg2rad(deg) {
            return deg * (Math.PI / 180);
        },

        filterListings() {
            let results = [...this.listings];

            // Search filter
            if (this.searchQuery.trim()) {
                const query = this.searchQuery.toLowerCase();
                results = results.filter(listing =>
                    listing.product?.variety?.toLowerCase().includes(query) ||
                    listing.product?.quality?.toLowerCase().includes(query) ||
                    listing.seller?.name?.toLowerCase().includes(query) ||
                    listing.seller?.display_name?.toLowerCase().includes(query) ||
                    listing.seller?.mill_name?.toLowerCase().includes(query) ||
                    listing.seller?.packer_name?.toLowerCase().includes(query) ||
                    listing.seller?.company_name?.toLowerCase().includes(query) ||
                    listing.seller?.farm_name?.toLowerCase().includes(query) ||
                    listing.seller?.location?.toLowerCase().includes(query) ||
                    listing.seller?.farm_location?.toLowerCase().includes(query)
                );
            }

            // Type filter
            if (this.filters.type !== 'all') {
                results = results.filter(listing => listing.product?.type === this.filters.type);
            }

            // Quality filter
            if (this.filters.qualities.length > 0) {
                results = results.filter(listing => {
                    const itemQuality = (listing.quality || listing.product?.quality || '').toLowerCase();
                    if (!itemQuality) return false;

                    return this.filters.qualities.some(qKey => {
                        const key = qKey.toLowerCase();
                        if (itemQuality === key || itemQuality.includes(key)) return true;

                        if (key === 'bio' && (itemQuality.includes('organic') || itemQuality.includes('بيولوجي') || itemQuality.includes('bio'))) return true;
                        if (key === 'evoo' && (itemQuality.includes('evoo') || itemQuality.includes('extra') || itemQuality.includes('ممتاز') || itemQuality.includes('vierge extra') || itemQuality.includes('premium'))) return true;
                        if (key === 'virgin' && (itemQuality.includes('virgin') || itemQuality.includes('بكر') || itemQuality.includes('vierge')) && !itemQuality.includes('extra') && !itemQuality.includes('evoo') && !itemQuality.includes('ممتاز')) return true;
                        if (key === 'raffinee' && (itemQuality.includes('raffinee') || itemQuality.includes('refined') || itemQuality.includes('مكرر'))) return true;
                        if (key === 'pomace' && (itemQuality.includes('pomace') || itemQuality.includes('فيتورة'))) return true;

                        return false;
                    });
                });
            }

            // Price range filter (converts to card display currency for accurate user filtering)
            if (this.filters.priceMin !== '' && this.filters.priceMin !== null) {
                const minVal = Number(this.filters.priceMin);
                results = results.filter(listing => {
                    const rawPrice = listing.price || listing.product?.price || 0;
                    const displayPrice = this.getDisplayPrice(rawPrice, listing.currency);
                    return displayPrice >= minVal;
                });
            }
            if (this.filters.priceMax !== '' && this.filters.priceMax !== null) {
                const maxVal = Number(this.filters.priceMax);
                results = results.filter(listing => {
                    const rawPrice = listing.price || listing.product?.price || 0;
                    const displayPrice = this.getDisplayPrice(rawPrice, listing.currency);
                    return displayPrice <= maxVal;
                });
            }

            // Distance filter
            if (this.filters.distance !== 'all' && this.userLocation.lat) {
                const maxDistance = Number(this.filters.distance);
                results = results.filter(listing =>
                    listing.distance && listing.distance <= maxDistance
                );
            }

            // Sort
            switch (this.filters.sortBy) {
                case 'nearest':
                    if (this.userLocation.lat) {
                        results.sort((a, b) => (a.distance || 9999) - (b.distance || 9999));
                    }
                    break;
                case 'newest':
                default:
                    // Maintain initial server-side rank (Session fresh #1, Top 6 Featured ⭐, then chronological)
                    results.sort((a, b) => {
                        const aIndex = this.listings.findIndex(l => l.id === a.id);
                        const bIndex = this.listings.findIndex(l => l.id === b.id);
                        return aIndex - bIndex;
                    });
                    break;
                case 'oldest':
                    results.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
                    break;
                case 'price_low':
                    results.sort((a, b) => Number(a.price || a.product?.price || 0) - Number(b.price || b.product?.price || 0));
                    break;
                case 'price_high':
                    results.sort((a, b) => Number(b.price || b.product?.price || 0) - Number(a.price || a.product?.price || 0));
                    break;
            }

            this.filteredListings = results;
            this.visibleCount = 24;
        },

        resetFilters() {
            this.searchQuery = '';
            this.filters = {
                type: 'all',
                qualities: [],
                priceMin: '',
                priceMax: '',
                sortBy: this.userLocation.lat ? 'nearest' : 'newest',
                distance: 'all'
            };
            this.filterListings();
        },

        formatDate(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffTime = Math.abs(now - date);
            const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));

            const locale = '{{ app()->getLocale() }}';
            
            if (locale === 'ar') {
                if (diffDays === 0) return 'اليوم';
                if (diffDays === 1) return 'أمس';
                if (diffDays < 7) return `منذ ${diffDays} أيام`;
                if (diffDays < 30) return `منذ ${Math.floor(diffDays / 7)} أسابيع`;
                return `منذ ${Math.floor(diffDays / 30)} أشهر`;
            } else if (locale === 'fr') {
                if (diffDays === 0) return "Aujourd'hui";
                if (diffDays === 1) return 'Hier';
                if (diffDays < 7) return `il y a ${diffDays} jours`;
                if (diffDays < 30) return `il y a ${Math.floor(diffDays / 7)} semaines`;
                return `il y a ${Math.floor(diffDays / 30)} mois`;
            } else {
                if (diffDays === 0) return 'Today';
                if (diffDays === 1) return 'Yesterday';
                if (diffDays < 7) return `${diffDays} days ago`;
                if (diffDays < 30) return `${Math.floor(diffDays / 7)} weeks ago`;
                return `${Math.floor(diffDays / 30)} months ago`;
            }
        }
    }));
});
</script>

<!-- Structured Data for Products (SEO) -->
@if(isset($featuredListings) && count($featuredListings) > 0)
@php
    $itemListElements = [];
    foreach($featuredListings->take(10) as $index => $listing) {
        $productVariety = $listing->product->variety ?? 'زيت الزيتون';
        $productQuality = $listing->product->quality ?? 'جودة عالية';
        $productType = ($listing->product->type ?? 'oil') === 'oil' ? 'زيت زيتون' : 'زيتون';
        $img = isset($listing->media[0]) ? asset('storage/' . $listing->media[0]) : asset('images/zintoop-logo.png');
        
        $itemListElements[] = [
            '@'.'type' => 'ListItem',
            'position' => $index + 1,
            'item' => [
                '@'.'type' => 'Product',
                'name' => $productVariety,
                'description' => $productType . ' - ' . $productQuality,
                'image' => $img,
                'brand' => [
                    '@'.'type' => 'Brand',
                    'name' => 'ZinToop'
                ],
                'offers' => [
                    '@'.'type' => 'Offer',
                    'price' => (string)$listing->price,
                    'priceCurrency' => 'TND',
                    'availability' => 'https://schema.org/InStock',
                    'validFrom' => $listing->created_at ? $listing->created_at->format('Y-m-d') : now()->format('Y-m-d'),
                    'seller' => [
                        '@'.'type' => 'Organization',
                        'name' => $listing->seller->display_name ?? $listing->seller->name ?? 'ZinToop Verified Seller'
                    ],
                    'shippingDetails' => [
                        '@'.'type' => 'OfferShippingDetails',
                        'shippingDestination' => [
                            '@'.'type' => 'DefinedRegion',
                            'addressCountry' => 'TN'
                        ],
                        'shippingRate' => [
                            '@'.'type' => 'MonetaryAmount',
                            'value' => '0',
                            'currency' => 'TND'
                        ]
                    ],
                    'hasMerchantReturnPolicy' => [
                        '@'.'type' => 'MerchantReturnPolicy',
                        'applicableCountry' => 'TN',
                        'returnPolicyCategory' => 'https://schema.org/MerchantReturnNotPermitted',
                        'merchantReturnDays' => 0
                    ]
                ]
            ]
        ];
    }
@endphp
<script type="application/ld+json">
{!! json_encode([
    '@'.'context' => 'https://schema.org',
    '@'.'type' => 'ItemList',
    'name' => 'منتجات زيت الزيتون التونسي - ZinToop',
    'description' => 'عروض ومنتجات زيت الزيتون والزيتون التونسي الأصلي على منصة ZinToop',
    'numberOfItems' => count($itemListElements),
    'itemListElement' => $itemListElements
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endif

<style>
    html {
        scroll-behavior: smooth;
    }
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    
    @keyframes bounce-short {
        0%, 100% { transform: translate(-50%, 0); }
        50% { transform: translate(-50%, -10px); }
    }
    @keyframes bounce-slow {
        0%, 100% { transform: translate(-50%, 0); }
        50% { transform: translate(-50%, 15px); }
    }
    .animate-bounce-slow {
        animation: bounce-slow 3s ease-in-out infinite;
    }
</style>

@endsection
