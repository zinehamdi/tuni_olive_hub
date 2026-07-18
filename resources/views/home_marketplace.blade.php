@extends('layouts.app')

@section('title', 'سوق زيت الزيتون التونسي | Tunisian Olive Oil Marketplace')
@section('description', 'اكتشف أفضل منتجات زيت الزيتون التونسي من المزارعين والمعاصر والمعبئين. جودة عالية، أسعار تنافسية، توصيل سريع. Discover premium Tunisian olive oil products from farmers, mills and packers.')
@section('og_title', 'سوق زيت الزيتون التونسي - جودة أصلية من المزارع إلى منزلك')
@section('og_description', 'تسوق زيت الزيتون التونسي الأصلي بجودة عالية من المزارعين والمعاصر مباشرة')

@section('content')
<div dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" class="min-h-screen bg-gradient-to-b from-gray-50 to-white" 
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
                        <img src="{{ asset('images/logotoop.PNG') }}" alt="Tunisian Olive Oil Platform" class="h-full w-full object-cover scale-125 translate-y-2 group-hover:scale-[1.35] transition-transform">
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

    <!-- Hero Section with Slideshow and Search -->
    <section class="relative bg-black text-white py-8 px-4 overflow-hidden"
             x-data="{ 
                 slides: [
                     '{{ asset('images/hero_slide_1.png') }}',
                     '{{ asset('images/hero_slide_2.png') }}',
                     '{{ asset('images/hero_slide_3.png') }}'
                 ],
                 currentSlide: 0,
                 init() {
                     setInterval(() => {
                         this.currentSlide = (this.currentSlide + 1) % this.slides.length;
                     }, 6000);
                 }
             }">
        <!-- Slideshow Backgrounds with Zoom Effect -->
        <template x-for="(slide, index) in slides" :key="index">
            <div class="absolute inset-0 bg-cover bg-center transition-all duration-[6000ms] ease-linear"
                 :class="currentSlide === index ? 'opacity-100 scale-110' : 'opacity-0 scale-100'"
                 :style="`background-image: url('${slide}'); transition-property: opacity, transform;`"></div>
        </template>
        
        <!-- Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-b from-[#6A8F3B]/70 via-black/40 to-black/80 z-0 pointer-events-none"></div>
        <div class="max-w-7xl mx-auto relative z-10">
            <div class="text-center mb-8">
                <!-- Hero Animation Inline -->
                <div class="relative flex flex-col items-center justify-center py-12 px-4 text-center space-y-8 bg-transparent w-full max-w-4xl mx-auto">
                    <!-- English Slogan -->
                    <div class="animate-fade-in">
                        <p class="text-white font-black text-lg md:text-2xl uppercase tracking-[0.25em] drop-shadow-lg">
                            Zin Tunisian Olive Oil Platform
                        </p>
                    </div>

                    <!-- Main Brand: ZinToop -->
                    <div class="relative group">
                        <h1 id="zintoop-brand" class="text-7xl md:text-9xl font-black tracking-tighter text-transparent bg-clip-text bg-gradient-to-r from-[#C8A356] via-[#FFF9E0] to-[#C8A356] bg-[length:200%_auto] animate-shine drop-shadow-2xl">
                            ZinToop
                        </h1>
                        <!-- Subtle Glow -->
                        <div class="absolute -inset-8 bg-[#C8A356]/20 blur-3xl rounded-full -z-10 animate-pulse"></div>
                    </div>

                    <!-- Arabic Slogan -->
                    <div class="animate-fade-in-delayed">
                        <p class="text-[#C8A356] font-black text-3xl md:text-5xl drop-shadow-lg" dir="rtl">
                            منصة الزين لزيت الزيتون التونسي
                        </p>
                    </div>
                </div>

                <style>
                    @keyframes shine {
                        0% { background-position: 200% center; }
                        100% { background-position: -200% center; }
                    }
                    .animate-shine {
                        animation: shine 4s linear infinite;
                    }
                    @keyframes fadeIn {
                        from { opacity: 0; transform: translateY(10px); }
                        to { opacity: 1; transform: translateY(0); }
                    }
                    .animate-fade-in {
                        animation: fadeIn 1s ease-out forwards;
                    }
                    .animate-fade-in-delayed {
                        animation: fadeIn 1.5s ease-out forwards;
                        opacity: 0;
                    }
                </style>
                
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
                        <span class="font-black uppercase tracking-widest text-[10px]">تعرف على سلالات الزيتون التونسي</span>
                        <div class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center group-hover:bg-white/40 transition">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Search Bar with Location -->
            <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-2xl p-2" data-bot-explain="{{ app()->getLocale() === 'ar' ? 'هنا يمكنك البحث عن المنتجات، وتحديد موقعك لرؤية الأقرب إليك!' : 'Here you can search for products and locate yourself to find nearby offers!' }}">
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
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8 max-w-4xl mx-auto" data-bot-explain="{{ app()->getLocale() === 'ar' ? 'هذه بعض الإحصائيات السريعة حول العروض المتوفرة في المنصة' : 'These are some quick stats about the available offers' }}">
                <div class="bg-white/10 backdrop-blur rounded-xl p-4 text-center">
                    <div class="text-3xl font-bold" x-text="totalListings"></div>
                    <div class="text-sm text-white/80">{{ __('Active listings') }}</div>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-xl p-4 text-center">
                    <div class="text-3xl font-bold" x-text="oilCount"></div>
                    <div class="text-sm text-white/80">{{ __('Olive Oil') }}</div>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-xl p-4 text-center">
                    <div class="text-3xl font-bold" x-text="oliveCount"></div>
                    <div class="text-sm text-white/80">{{ __('Olives') }}</div>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-xl p-4 text-center">
                    <div class="text-3xl font-bold" x-text="filteredListings.length"></div>
                    <div class="text-sm text-white/80">{{ __('Search results') }}</div>
                </div>
            </div>
        </div>

    </section>

    <!-- Live Deals & Opportunities Section -->
    @if(isset($deals) && $deals->count() > 0)
    <section class="max-w-7xl mx-auto px-4 pt-10 pb-2 overflow-hidden">
        <div class="flex flex-col md:flex-row items-center justify-between mb-6 gap-4">
            <div class="relative">
                <div class="absolute -top-4 {{ app()->getLocale() === 'ar' ? 'right-0' : 'left-0' }} w-10 h-1 bg-[#6A8F3B] rounded-full"></div>
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
                        <div class="absolute top-0 {{ app()->getLocale() === 'ar' ? 'right-0' : 'left-0' }} bottom-0 w-1.5 
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

    <!-- Latest Articles / Ads Section -->
    @if(isset($articles) && $articles->count() > 0)
    <section class="max-w-7xl mx-auto px-4 pt-12 pb-4">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-3xl font-bold text-gray-900">{{ __('Latest Articles') }}</h2>
        </div>
        
        <!-- Articles Carousel / Grid -->
        <div class="flex md:grid md:grid-cols-3 gap-6 overflow-x-auto md:overflow-x-visible snap-x snap-mandatory pb-8 scrollbar-hide">
            @foreach($articles as $article)
            <div class="w-full md:min-w-0 snap-center flex-shrink-0 px-1">
                <div class="group bg-white rounded-3xl shadow-[0_20px_50px_-20px_rgba(0,0,0,0.15)] border border-gray-100/50 hover:shadow-[0_30px_60px_-12px_rgba(106,143,59,0.25)] transition-all duration-500 overflow-hidden h-full flex flex-col">
                    <a href="{{ route('articles.show', $article->id) }}" class="aspect-[16/9] bg-gradient-to-br from-[#6A8F3B] to-[#5a7a2f] relative overflow-hidden block">
                        <img src="{{ Str::startsWith($article->image, ['http://', 'https://']) ? $article->image : (Str::startsWith($article->image, 'storage/') ? asset($article->image) : (Storage::disk('public')->exists($article->image) ? Storage::url($article->image) : asset('images/' . $article->image))) }}" 
                             onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&q=80'" alt="Article" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        @if(isset($article->category[app()->getLocale()]))
                        <div class="absolute top-4 {{ app()->getLocale()==='ar' ? 'right-4' : 'left-4' }} bg-white/95 backdrop-blur px-3 py-1 rounded-full text-[10px] font-bold text-[#6A8F3B] shadow-sm">{{ $article->category[app()->getLocale()] }}</div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    </a>
                    <div class="p-6 flex-1 flex flex-col">
                        <div class="flex items-center gap-2 text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-3">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            {{ $article->created_at->format('M d, Y') }}
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-[#6A8F3B] transition-colors leading-tight">{{ $article->title[app()->getLocale()] ?? '' }}</h3>
                        <p class="text-gray-500 text-sm line-clamp-2 mb-6 leading-relaxed">{{ $article->content[app()->getLocale()] ?? '' }}</p>
                        
                        <div class="mt-auto">
                            <a href="{{ route('articles.show', $article->id) }}" 
                                    class="inline-flex items-center gap-2 text-[#6A8F3B] font-bold text-sm group/btn hover:underline transition-all">
                                <span>{{ __('Show More') }}</span>
                                <svg class="w-4 h-4 transition-transform duration-300 group-hover/btn:{{ app()->getLocale()==='ar' ? '-translate-x-1' : 'translate-x-1' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ app()->getLocale()==='ar' ? 'M15 19l-7-7 7-7' : 'M9 5l7 7-7 7' }}" />
                                </svg>
                            </a>
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
        <div class="flex flex-col lg:flex-row gap-6">
            
            <!-- Sidebar Filters -->
            <aside class="lg:w-64 flex-shrink-0 order-1 lg:order-1">
                <!-- Mobile Filter Toggle Button - Elegant and Positioned Above Grid -->
                <div class="lg:hidden mb-6 sticky top-20 z-30">
                    <button @click="showFilters = !showFilters" 
                            class="w-full flex items-center justify-between px-6 py-4 bg-white border border-gray-100 text-[#6A8F3B] rounded-2xl shadow-[0_10px_30px_-10px_rgba(0,0,0,0.1)] hover:shadow-xl transition-all duration-300 font-bold group">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-[#6A8F3B]/10 flex items-center justify-center group-hover:bg-[#6A8F3B] group-hover:text-white transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                            </div>
                            <span>{{ __('Filter Results') }}</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-300" :class="showFilters ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                </div>

                <div class="bg-white rounded-2xl shadow-xl border border-gray-50 p-6 lg:sticky lg:top-4 max-h-[80vh] lg:max-h-[calc(100vh-2rem)] overflow-y-auto mb-8 lg:mb-0"
                     x-show="showFilters"
                     x-transition:enter="lg:transition-none transition ease-out duration-200"
                     x-transition:enter-start="lg:opacity-100 lg:translate-y-0 opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="lg:transition-none transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="lg:opacity-100 lg:translate-y-0 opacity-0 -translate-y-2">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-[#6A8F3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        {{ __('Filter Results') }}
                    </h3>

                    <!-- Location Filter -->
                    <div class="mb-6 p-4 bg-gradient-to-r from-[#6A8F3B]/10 to-[#C8A356]/10 rounded-xl" data-bot-explain="{{ app()->getLocale() === 'ar' ? 'استخدم هذا الفلتر لعرض المنتجات القريبة منك فقط!' : 'Use this filter to show only products near you!' }}">
                        <label class="font-bold text-gray-900 mb-3 flex items-center gap-2">
                            <svg class="w-5 h-5 text-[#6A8F3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            </svg>
                            {{ __('Distance') }}
                        </label>
                        <select x-model="filters.distance" @change="filterListings" class="w-full px-3 py-2 border-2 border-[#6A8F3B] rounded-lg focus:ring-2 focus:ring-[#6A8F3B] focus:border-transparent font-semibold">
                            <option value="all">{{ __('All distances') }}</option>
                            <option value="10">{{ __('Less than 10 km') }}</option>
                            <option value="25">{{ __('Less than 25 km') }}</option>
                            <option value="50">{{ __('Less than 50 km') }}</option>
                            <option value="100">{{ __('Less than 100 km') }}</option>
                        </select>
                        <button @click="getMyLocation" class="mt-2 w-full px-3 py-2 bg-[#C8A356] text-white rounded-lg hover:bg-[#b08a3c] transition text-sm font-bold">
                            {{ __('Get my location') }}
                        </button>
                    </div>

                    <!-- Product Type Filter -->
                    <div class="mb-6">
                        <label class="block font-bold text-gray-900 mb-3">{{ __('Product Type') }}</label>
                        <div class="space-y-3">
                            <!-- All Products -->
                            <label class="flex items-center gap-3 p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-[#6A8F3B] hover:bg-[#6A8F3B]/5 transition-all"
                                   :class="filters.type === 'all' ? 'border-[#6A8F3B] bg-[#6A8F3B]/10' : ''">
                                <input type="radio" x-model="filters.type" value="all" @change="filterListings" class="text-[#6A8F3B] focus:ring-[#6A8F3B] w-5 h-5">
                                <div class="flex items-center gap-3 flex-1">
                                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-[#6A8F3B] to-[#5a7a2f] flex items-center justify-center text-white flex-shrink-0">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-bold text-gray-900 text-sm">{{ __('All') }}</div>
                                    </div>
                                    <div class="text-xs font-semibold text-gray-500 bg-gray-100 px-2 py-1 rounded-full" x-text="totalListings"></div>
                                </div>
                            </label>

                            <!-- Olive Oil -->
                            <label class="flex items-center gap-3 p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-[#6A8F3B] hover:bg-[#6A8F3B]/5 transition-all"
                                   :class="filters.type === 'oil' ? 'border-[#6A8F3B] bg-[#6A8F3B]/10' : ''">
                                <input type="radio" x-model="filters.type" value="oil" @change="filterListings" class="text-[#6A8F3B] focus:ring-[#6A8F3B] w-5 h-5">
                                <div class="flex items-center gap-3 flex-1">
                                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-[#C8A356] to-[#b08a3c] flex items-center justify-center flex-shrink-0">
                                        <span class="text-2xl">🫗</span>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-bold text-gray-900 text-sm">{{ __('Olive Oil') }}</div>
                                    </div>
                                    <div class="text-xs font-semibold text-gray-500 bg-gray-100 px-2 py-1 rounded-full" x-text="oilCount"></div>
                                </div>
                            </label>

                            <!-- Olives -->
                            <label class="flex items-center gap-3 p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-[#6A8F3B] hover:bg-[#6A8F3B]/5 transition-all"
                                   :class="filters.type === 'olive' ? 'border-[#6A8F3B] bg-[#6A8F3B]/10' : ''">
                                <input type="radio" x-model="filters.type" value="olive" @change="filterListings" class="text-[#6A8F3B] focus:ring-[#6A8F3B] w-5 h-5">
                                <div class="flex items-center gap-3 flex-1">
                                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-[#10B981] to-[#059669] flex items-center justify-center flex-shrink-0">
                                        <span class="text-2xl">🫒</span>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-bold text-gray-900 text-sm">{{ __('Olives') }}</div>
                                    </div>
                                    <div class="text-xs font-semibold text-gray-500 bg-gray-100 px-2 py-1 rounded-full" x-text="oliveCount"></div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Quality Filter -->
                    <div class="mb-6">
                        <label class="block font-bold text-gray-900 mb-3">{{ __('Quality') }}</label>
                        <div class="space-y-3">
                            <!-- Premium -->
                            <label class="flex items-center gap-3 p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-[#6A8F3B] hover:bg-[#6A8F3B]/5 transition-all"
                                   :class="filters.qualities.includes('premium') ? 'border-[#6A8F3B] bg-[#6A8F3B]/10' : ''">
                                <input type="checkbox" x-model="filters.qualities" value="premium" @change="filterListings" class="text-[#6A8F3B] focus:ring-[#6A8F3B] rounded w-5 h-5">
                                <div class="flex items-center gap-3 flex-1">
                                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-[#C8A356] to-[#b08a3c] flex items-center justify-center text-white flex-shrink-0">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-bold text-gray-900 text-sm">{{ __('Premium') }}</div>
                                    </div>
                                </div>
                            </label>

                            <!-- Extra -->
                            <label class="flex items-center gap-3 p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-[#6A8F3B] hover:bg-[#6A8F3B]/5 transition-all"
                                   :class="filters.qualities.includes('extra') ? 'border-[#6A8F3B] bg-[#6A8F3B]/10' : ''">
                                <input type="checkbox" x-model="filters.qualities" value="extra" @change="filterListings" class="text-[#6A8F3B] focus:ring-[#6A8F3B] rounded w-5 h-5">
                                <div class="flex items-center gap-3 flex-1">
                                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-[#6A8F3B] to-[#5a7a2f] flex items-center justify-center text-white flex-shrink-0">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-bold text-gray-900 text-sm">{{ __('Extra') }}</div>
                                    </div>
                                </div>
                            </label>

                            <!-- Standard -->
                            <label class="flex items-center gap-3 p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-[#6A8F3B] hover:bg-[#6A8F3B]/5 transition-all"
                                   :class="filters.qualities.includes('standard') ? 'border-[#6A8F3B] bg-[#6A8F3B]/10' : ''">
                                <input type="checkbox" x-model="filters.qualities" value="standard" @change="filterListings" class="text-[#6A8F3B] focus:ring-[#6A8F3B] rounded w-5 h-5">
                                <div class="flex items-center gap-3 flex-1">
                                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-[#3B82F6] to-[#2563EB] flex items-center justify-center text-white flex-shrink-0">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-bold text-gray-900 text-sm">{{ __('Standard') }}</div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Price Range Filter -->
                    <div class="mb-6">
                        <label class="block font-bold text-gray-900 mb-3">{{ __('Price Range') }}</label>
                        <div class="space-y-3">
                            <div>
                                <input type="number" x-model="filters.priceMin" @input="filterListings" placeholder="{{ __('Min') }}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-[#6A8F3B] focus:border-transparent">
                            </div>
                            <div>
                                <input type="number" x-model="filters.priceMax" @input="filterListings" placeholder="{{ __('Max') }}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-[#6A8F3B] focus:border-transparent">
                            </div>
                        </div>
                    </div>

                    <!-- Sort Options -->
                    <div class="mb-6">
                        <label class="block font-bold text-gray-900 mb-3">{{ __('Sort by') }}</label>
                        <select x-model="filters.sortBy" @change="filterListings" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-[#6A8F3B] focus:border-transparent">
                            <option value="nearest">{{ __('Nearest to me') }}</option>
                            <option value="newest">{{ __('Newest') }}</option>
                            <option value="oldest">{{ __('Oldest') }}</option>
                            <option value="price_low">{{ __('Price: Low to High') }}</option>
                            <option value="price_high">{{ __('Price: High to Low') }}</option>
                        </select>
                    </div>

                    <!-- Reset Button -->
                    <button @click="resetFilters" class="w-full px-4 py-3 bg-gradient-to-r from-gray-600 to-gray-700 text-white rounded-lg hover:from-gray-700 hover:to-gray-800 transition font-bold shadow-lg">
                        {{ __('Reset Filters') }}
                    </button>
                </div>
            </aside>

            <!-- Product Listings Grid -->
            <main class="flex-1 order-2 lg:order-2">
                <!-- Marketplace Mission Section -->
                <div class="mb-10 bg-gradient-to-br from-[#6A8F3B]/5 to-[#C8A356]/5 border border-[#6A8F3B]/10 rounded-2xl p-6 text-center relative overflow-hidden">
                    <div class="relative z-10 max-w-3xl mx-auto">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white shadow-sm border border-gray-100 mb-4">
                            <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-gray-600">
                                {{ app()->getLocale() === 'ar' ? 'سوق حر ومجاني 100%' : (app()->getLocale() === 'fr' ? 'Marché 100% Gratuit' : '100% Free Marketplace') }}
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
                <div x-show="viewMode === 'grid'" class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">
                    <template x-for="listing in filteredListings" :key="listing.id">
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1" data-bot-explain="{{ app()->getLocale() === 'ar' ? 'انقر على عرض التفاصيل لرؤية المزيد عن هذا المنتج وكيفية الشراء' : 'Click View Details to learn more about this product and how to buy' }}">
                            <!-- Product Image -->
                            <div class="h-48 bg-gradient-to-br from-[#6A8F3B] to-[#C8A356] flex items-center justify-center relative overflow-hidden">
                                  <img :src="(listing.media && listing.media.length > 0) ? '/storage/' + listing.media[0] : (listing.product?.type === 'oil' ? oilFallbackImage : fallbackImage)"
                                     :alt="listing.product?.variety || ''"
                                     class="w-full h-full object-cover"
                                     loading="lazy">
                                <div class="absolute top-3 right-3 flex gap-2">
                                    <span class="px-3 py-2 rounded-full text-white text-sm font-extrabold tracking-wide"
                                        :class="listing.product?.type === 'olive' ? 'bg-[#0f9d58]' : 'bg-[#C8A356]'"
                                        x-text="listing.product?.type === 'olive' ? '{{ __('Olives') }}' : '{{ __('Olive Oil') }}'"></span>
                                </div>
                                <!-- Distance Badge -->
                                <div x-show="listing.distance != null && listing.distance !== undefined" class="absolute top-3 left-3">
                                    <span class="px-3 py-1 rounded-full text-white text-xs font-bold bg-[#C8A356] backdrop-blur flex items-center gap-1">
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
                                    <span x-show="listing.status === 'active'" class="px-2 py-1 rounded-full bg-green-500 text-white text-xs font-semibold">{{ __('Active') }}</span>
                                </div>

                                <div class="text-2xl font-bold text-[#6A8F3B] mb-4">
                                    <template x-if="Number(listing.price || listing.product?.price || 0) === 0">
                                        <span class="bg-gradient-to-r from-[#6A8F3B] to-[#C8A356] text-transparent bg-clip-text text-xl animate-pulse">السعر عند الطلب</span>
                                    </template>
                                    <template x-if="Number(listing.price || listing.product?.price || 0) > 0">
                                        <div>
                                            <span x-text="Number(listing.price || listing.product?.price || 0).toFixed(2)"></span>
                                            <span class="text-sm text-gray-600" x-text="listing.currency === 'USD' ? '$' : (listing.currency === 'EUR' ? '€' : '{{ app()->getLocale() === 'ar' ? 'دينار' : __('TND') }}')"></span>
                                        </div>
                                    </template>
                                </div>

                                <div class="space-y-2 text-sm text-gray-600 mb-4">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <span x-text="listing.seller?.role === 'admin' ? '{{ __('Seller') }}' : listing.seller?.name || ''"></span>
                                    </div>
                                    <div x-show="listing.seller?.location || listing.seller?.farm_location" class="flex items-center gap-2 text-[#6A8F3B] font-semibold">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        </svg>
                                        <span x-text="listing.seller?.location || listing.seller?.farm_location || ''"></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span x-text="formatDate(listing.created_at)"></span>
                                    </div>
                                </div>

                                <div class="flex gap-2">
                                    <a :href="'/listings/' + listing.id" class="flex-1 text-center px-4 py-2 bg-[#6A8F3B] text-white rounded-lg hover:bg-[#5a7a2f] transition font-bold">
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
                    <template x-for="listing in filteredListings" :key="listing.id">
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 flex flex-col md:flex-row">
                            <!-- Product Image -->
                            <div class="w-full md:w-48 h-48 bg-gradient-to-br from-[#6A8F3B] to-[#C8A356] flex items-center justify-center flex-shrink-0 relative overflow-hidden">
                                  <img :src="(listing.media && listing.media.length > 0) ? '/storage/' + listing.media[0] : (listing.product?.type === 'oil' ? oilFallbackImage : fallbackImage)"
                                     :alt="listing.product?.variety || ''"
                                     class="w-full h-full object-cover"
                                     loading="lazy">
                                <!-- Distance Badge -->
                                <div x-show="listing.distance != null && listing.distance !== undefined" class="absolute top-3 left-3">
                                    <span class="px-3 py-1 rounded-full text-white text-xs font-bold bg-[#C8A356] backdrop-blur flex items-center gap-1">
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
                                            <h3 class="text-2xl font-bold text-gray-900 leading-tight" x-text="getEnhancedTitle(listing)"></h3>
                                                <span class="px-3 py-2 rounded-full text-white text-sm font-extrabold tracking-wide" 
                                                    :class="listing.product?.type === 'olive' ? 'bg-[#0f9d58]' : 'bg-[#C8A356]'"
                                                    x-text="listing.product?.type === 'olive' ? '{{ app()->getLocale() === 'ar' ? 'زيتون' : __('Olives') }}' : '{{ app()->getLocale() === 'ar' ? 'زيت زيتون' : __('Olive Oil') }}'"></span>
                                        <span x-show="listing.product?.quality" class="px-3 py-1 rounded-full bg-gradient-to-r from-[#C8A356] to-[#b8954e] shadow-sm text-white text-xs font-semibold" x-text="translate(listing.product?.quality)"></span>
                                        <span x-show="listing.packaging" class="px-3 py-1 rounded-full bg-gradient-to-r from-[#6A8F3B] to-[#5a7a2f] shadow-sm text-white text-xs font-semibold" x-text="translate(listing.packaging)"></span>
                                    </div>
                                    <div class="flex items-center gap-4 text-sm text-gray-600 flex-wrap">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            <span x-text="listing.seller?.role === 'admin' ? '{{ __('Seller') }}' : listing.seller?.name || ''"></span>
                                        </div>
                                        <div x-show="listing.seller?.location || listing.seller?.farm_location" class="flex items-center gap-2 text-[#6A8F3B] font-semibold">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            </svg>
                                            <span x-text="listing.seller?.location || listing.seller?.farm_location || ''"></span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span x-text="formatDate(listing.created_at)"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center md:text-left">
                                    <div class="text-3xl font-bold text-[#6A8F3B] mb-4">
                                        <template x-if="Number(listing.price || listing.product?.price || 0) === 0">
                                            <span class="bg-gradient-to-r from-[#6A8F3B] to-[#C8A356] text-transparent bg-clip-text text-2xl animate-pulse">السعر عند الطلب</span>
                                        </template>
                                        <template x-if="Number(listing.price || listing.product?.price || 0) > 0">
                                            <div>
                                                <span x-text="Number(listing.price || listing.product?.price || 0).toFixed(2)"></span>
                                                <span class="text-sm text-gray-600" x-text="listing.currency === 'USD' ? '$' : (listing.currency === 'EUR' ? '€' : '{{ app()->getLocale() === 'ar' ? 'دينار' : __('TND') }}')"></span>
                                            </div>
                                        </template>
                                    </div>
                                    <div class="flex gap-2">
                                        <a :href="'/listings/' + listing.id" class="px-6 py-2 bg-[#6A8F3B] text-white rounded-lg hover:bg-[#5a7a2f] transition font-bold whitespace-nowrap">
                                            {{ __('View Details') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
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
            </main>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="bg-gradient-to-br from-[#6A8F3B] to-[#5a7a2f] text-white py-16 px-4 mt-12" data-bot-explain="{{ app()->getLocale() === 'ar' ? 'هل أنت منتج أو صاحب معصرة؟ انضم إلينا وابدأ البيع مجاناً!' : 'Are you a producer or mill owner? Join us and start selling for free!' }}">
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



</div>

<script>
document.addEventListener('alpine:init', () => {

    Alpine.data('marketplace', () => ({
        listings: @json($featuredListings ?? []),
        filteredListings: [],
        searchQuery: '',
        viewMode: 'grid',
        fallbackImage: 'https://toop.kairouanhub.com/storage/listings/23/28bc3509-9426-4f36-9e71-fd694f3cbc45.webp',
        oilFallbackImage: '{{ asset('images/oliveoiltandefault.jpg') }}',
        mobileMenuOpen: false,
        showFilters: window.innerWidth >= 1024,
        articleModalOpen: false,
        locale: '{{ app()->getLocale() }}',
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
        translations: {
            'Extra Virgin Olive Oil': '{{ __("Extra Virgin Olive Oil") }}',
            'Chemlali Olive Oil': '{{ __("Chemlali Olive Oil") }}',
            'Chetoui Olive Oil': '{{ __("Chetoui Olive Oil") }}',
            'Organic Extra Virgin': '{{ __("Organic Extra Virgin") }}',
            'Cold Pressed Olive Oil': '{{ __("Cold Pressed Olive Oil") }}',
            'Premium Blend Olive Oil': '{{ __("Premium Blend Olive Oil") }}',
            'Chemlali Olives': '{{ __("Chemlali Olives") }}',
            'Chetoui Olives': '{{ __("Chetoui Olives") }}',
            'Meski Olives': '{{ __("Meski Olives") }}',
            'Zalmati Olives': '{{ __("Zalmati Olives") }}',
            'Fresh Olives': '{{ __("Fresh Olives") }}',
            'Table Olives': '{{ __("Table Olives") }}',
            // Lowercase keys for direct matches from API/data
            'chemlali': '{{ __("chemlali") }}',
            'gerboui': '{{ __("gerboui") }}',
            'بكر ممتاز (evoo)': '{{ app()->getLocale() === "ar" ? "بكر ممتاز (EVOO)" : (app()->getLocale() === "fr" ? "Vierge Extra (EVOO)" : "Extra Virgin (EVOO)") }}',
            'بكر (virgin)': '{{ app()->getLocale() === "ar" ? "بكر (Virgin)" : (app()->getLocale() === "fr" ? "Vierge" : "Virgin") }}',
            'بكر عادي (ordinary virgin)': '{{ app()->getLocale() === "ar" ? "بكر عادي (Ordinary Virgin)" : (app()->getLocale() === "fr" ? "Vierge Courante" : "Ordinary Virgin") }}',
            'وقاد (lampante)': '{{ app()->getLocale() === "ar" ? "وقاد (Lampante)" : "Lampante" }}',
            'بيولوجي (organic)': '{{ app()->getLocale() === "ar" ? "بيولوجي (Organic)" : (app()->getLocale() === "fr" ? "Biologique (Bio)" : "Organic") }}',
            'صبّة (vrac)': '{{ app()->getLocale() === "ar" ? "صبّة (Vrac)" : (app()->getLocale() === "fr" ? "En Vrac" : "Bulk (Vrac)") }}',
            'معلّب (packaged)': '{{ app()->getLocale() === "ar" ? "معلّب (Packaged)" : (app()->getLocale() === "fr" ? "Emballé" : "Packaged") }}',
            'جملة (gros)': '{{ app()->getLocale() === "ar" ? "جملة (Gros)" : (app()->getLocale() === "fr" ? "En Gros" : "Wholesale") }}',
            'تفصيل (détail)': '{{ app()->getLocale() === "ar" ? "تفصيل (Détail)" : (app()->getLocale() === "fr" ? "Détail" : "Retail") }}',
            'chetoui': '{{ __("chetoui") }}',
            'meski': '{{ __("meski") }}',
            'zalmati': '{{ __("zalmati") }}',
            'koroneiki': '{{ __("koroneiki") }}',
            'jemlati': '{{ __("jemlati") }}',
            'barouni': '{{ __("barouni") }}',
            "This website is secure and does not have redirected links that bother users.": "{{ __('This website is secure and does not have redirected links that bother users.') }}",
            "ZinToop connects direct olive oil producers with buyers across Tunisia effortlessly.": "{{ __('ZinToop connects direct olive oil producers with buyers across Tunisia effortlessly.') }}",
            "Use the search bar and location filters to find the best quality oil near you.": "{{ __('Use the search bar and location filters to find the best quality oil near you.') }}",
            "Our vision is to digitize the olive oil sector and ensure fair trade for every producer.": "{{ __('Our vision is to digitize the olive oil sector and ensure fair trade for every producer.') }}"
        },

        translate(text) {
            if (!text) return '';
            const lower = text.toLowerCase();
            return this.translations[text] || this.translations[lower] || text;
        },

        getEnhancedTitle(listing) {
            const isAr = '{{ app()->getLocale() }}' === 'ar';
            const isFr = '{{ app()->getLocale() }}' === 'fr';
            
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
            if (listing.seller?.addresses?.length > 0 && listing.seller.addresses[0]?.governorate) {
                city = (isAr ? ' من ' : (isFr ? ' de ' : ' from ')) + (listing.seller.addresses[0]?.governorate || '');
            }
            
            return `${typeLabel} ${variety}${quality}${organic}${city}`;
        },

        init() {
            // Always show filters on desktop (>= 1024px)
            if (window.innerWidth >= 1024) {
                this.showFilters = true;
            }

            this.$watch('articleModalOpen', value => {
                if (!value && !this.dealRequestModalOpen) document.body.classList.remove('overflow-hidden');
            });

            this.$watch('dealRequestModalOpen', value => {
                if (!value && !this.articleModalOpen) document.body.classList.remove('overflow-hidden');
            });

            
            this.filteredListings = this.listings;
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
                // Try to get seller's address with coordinates
                if (listing.seller?.addresses?.length > 0) {
                    const address = listing.seller.addresses[0];
                    if (address && address.lat && address.lng) {
                        listing.distance = this.calculateDistance(
                            this.userLocation.lat,
                            this.userLocation.lng,
                            address.lat,
                            address.lng
                        );
                    }
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
                results = results.filter(listing =>
                    this.filters.qualities.includes(listing.product?.quality?.toLowerCase())
                );
            }

            // Price range filter
            if (this.filters.priceMin !== '') {
                results = results.filter(listing =>
                    Number(listing.price || listing.product?.price || 0) >= Number(this.filters.priceMin)
                );
            }
            if (this.filters.priceMax !== '') {
                results = results.filter(listing =>
                    Number(listing.price || listing.product?.price || 0) <= Number(this.filters.priceMax)
                );
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
                    results.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
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
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "ItemList",
    "name": "منتجات زيت الزيتون التونسي",
    "description": "مجموعة من منتجات زيت الزيتون والزيتون التونسي الأصلي",
    "numberOfItems": {{ count($featuredListings) }},
    "itemListElement": [
        @foreach($featuredListings as $index => $listing)
        {
            "@@type": "ListItem",
            "position": {{ $index + 1 }},
            "item": {
                "@@type": "Product",
                "name": "{{ $listing->product->variety ?? 'زيت الزيتون' }}",
                "description": "{!! $listing->product->type === 'oil' ? 'زيت زيتون' : 'زيتون' !!} - {{ $listing->product->quality ?? 'جودة عالية' }}",
                "offers": {
                    "@@type": "Offer",
                    "price": "{{ $listing->price }}",
                    "priceCurrency": "TND",
                    "availability": "https://schema.org/InStock",
                    "seller": {
                        "@@type": "Organization",
                        "name": "{{ $listing->seller->name ?? 'بائع' }}"
                    }
                },
                "image": "{!! isset($listing->media[0]) ? asset('storage/' . $listing->media[0]) : asset('images/logotoop.PNG') !!}",
                "brand": {
                    "@@type": "Brand",
                    "name": "Tunisian Olive Oil"
                },
                "aggregateRating": {
                    "@@type": "AggregateRating",
                    "ratingValue": "4.5",
                    "reviewCount": "1"
                }
            }
        }{{ !$loop->last ? ',' : '' }}
        @endforeach
    ]
}
</script>

<!-- Organization Schema -->
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "Organization",
    "name": "منصة زيت الزيتون التونسي",
    "alternateName": "Tunisian Olive Oil Platform",
    "url": "{{ url('/') }}",
    "logo": "{{ asset('images/logotoop.PNG') }}",
    "description": "منصة تونسية متخصصة في تجارة زيت الزيتون والزيتون التونسي الأصلي",
    "contactPoint": {
        "@@type": "ContactPoint",
        "contactType": "Customer Service",
        "availableLanguage": ["Arabic", "French", "English"]
    },
    "sameAs": [
        "{{ url('/') }}"
    ],
    "areaServed": {
        "@@type": "Country",
        "name": "Tunisia"
    }
}
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
