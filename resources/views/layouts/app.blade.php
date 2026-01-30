<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- SEO Meta Tags -->
    <title>{{ config('app.name') }} - @yield('title', __(app()->getLocale() === 'ar' ? 'brand.name_ar' : 'brand.name_latin'))</title>
    <meta name="description" content="@yield('description', __(app()->getLocale() === 'ar' ? 'brand.descriptor' : 'brand.descriptor'))">
    <meta name="keywords" content="زيت الزيتون التونسي, زيتون تونس, olive oil tunisia, huile d'olive tunisienne, تجارة زيت الزيتون, معصرة زيتون, مزارع زيتون, zinToop, marketplace, e-commerce">
    <meta name="author" content="{{ __(app()->getLocale() === 'ar' ? 'brand.name_ar' : 'brand.name_latin') }}">
    <meta name="robots" content="index, follow">
    <meta name="googlebot" content="index, follow">

    <!-- Open Graph Meta Tags for Social Media -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:title" content="@yield('og_title', __(app()->getLocale() === 'ar' ? 'brand.name_ar' : 'brand.name_latin'))">
    <meta property="og:description" content="@yield('og_description', __(app()->getLocale() === 'ar' ? 'brand.descriptor' : 'brand.descriptor'))">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="@yield('og_image', asset('images/zintoop-logo.png'))">
    <meta property="og:locale" content="{{ app()->getLocale() === 'ar' ? 'ar_TN' : (app()->getLocale() === 'fr' ? 'fr_FR' : 'en_US') }}">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('twitter_title', __(app()->getLocale() === 'ar' ? 'brand.name_ar' : 'brand.name_latin'))">
    <meta name="twitter:description" content="@yield('twitter_description', __(app()->getLocale() === 'ar' ? 'brand.descriptor' : 'brand.descriptor'))">
    <meta name="twitter:image" content="@yield('twitter_image', asset('images/zintoop-logo.png'))">

    <!-- Alternate Language Links for SEO -->
    <link rel="alternate" hreflang="ar" href="{{ url()->current() }}?lang=ar">
    <link rel="alternate" hreflang="fr" href="{{ url()->current() }}?lang=fr">
    <link rel="alternate" hreflang="en" href="{{ url()->current() }}?lang=en">
    <link rel="alternate" hreflang="x-default" href="{{ url()->current() }}">

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/zintoop-logo.png') }}">

    @vite(['resources/css/app.css','resources/js/app.js'])
    <style nonce="{{ $cspNonce ?? '' }}">
        :root{--olive:#6b8e23;--gold:#b8860b;--sky:#38bdf8;--pepper:#b91c1c}
        .text-olive{color:var(--olive)} .bg-olive{background:var(--olive)}
        .text-gold{color:var(--gold)} .bg-gold{background:var(--gold)}
        .text-sky{color:var(--sky)} .bg-sky{background:var(--sky)}
        .text-pepper{color:var(--pepper)} .bg-pepper{background:var(--pepper)}
        [x-cloak] { display: none !important; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('head')
    <script nonce="{{ $cspNonce ?? '' }}">
        document.documentElement.dir = '{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}';
    </script>

    <!-- Structured Data for SEO -->
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "WebSite",
        "name": "{{ __(app()->getLocale() === 'ar' ? 'brand.name_ar' : 'brand.name_latin') }}",
        "alternateName": "ZinToop",
        "url": "{{ url('/') }}",
        "description": "{{ __(app()->getLocale() === 'ar' ? 'brand.descriptor' : 'brand.descriptor') }}",
        "potentialAction": {
            "@@type": "SearchAction",
            "target": "{{ url('/') }}?search={search_term_string}",
            "query-input": "required name=search_term_string"
        },
        "inLanguage": ["ar", "fr", "en"]
    }
    </script>
</head>
<body class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-50 text-gray-900 antialiased">
    <!-- Modern Navigation with Glass Effect -->
    <nav class="relative z-50" x-data="{ mobileMenuOpen: false, scrolled: false }" @scroll.window="scrolled = window.scrollY > 20">
        <!-- Main Nav Bar -->
        <div class="bg-gradient-to-r from-[#5a7a2f] via-[#6A8F3B] to-[#5a7a2f] text-white shadow-xl" :class="scrolled ? 'shadow-2xl' : 'shadow-xl'">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16 sm:h-18">
                    <!-- Logo -->
                    <a href="{{ route('home') }}" class="flex-shrink-0 group flex items-center gap-3">
                        <div class="relative">
                            <div class="absolute inset-0 bg-white/20 rounded-full blur-md group-hover:bg-white/30 transition-all duration-300"></div>
                            <img src="{{ asset('images/zintoop-logo.png') }}" alt="{{ __(app()->getLocale() === 'ar' ? 'brand.name_ar' : 'brand.name_latin') }}" class="relative h-11 sm:h-12 w-auto rounded-full object-contain group-hover:scale-105 transition-transform duration-300 ring-2 ring-white/30">
                        </div>
                        <div class="hidden sm:block">
                            <span class="text-lg font-bold tracking-tight">ZinToop</span>
                            <span class="block text-[10px] text-white/70 font-medium -mt-0.5">{{ __('brand.descriptor_short') ?? 'Olive Oil Platform' }}</span>
                        </div>
                    </a>

                    <!-- Desktop Navigation -->
                    <div class="hidden lg:flex items-center gap-1 flex-1 justify-center {{ app()->getLocale()==='ar' ? 'mr-6' : 'ml-6' }}">
                        <a href="{{ route('home') }}" class="group px-4 py-2 rounded-xl hover:bg-white/15 transition-all duration-200 font-medium flex items-center gap-2 text-sm">
                            <div class="w-8 h-8 rounded-lg bg-white/10 group-hover:bg-white/20 flex items-center justify-center transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                            </div>
                            <span>{{ __('nav.home') }}</span>
                        </a>
                        <a href="{{ route('prices.index') }}" class="group px-4 py-2 rounded-xl hover:bg-white/15 transition-all duration-200 font-medium flex items-center gap-2 text-sm">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-amber-400/30 to-amber-600/30 group-hover:from-amber-400/40 group-hover:to-amber-600/40 flex items-center justify-center transition-all">
                                <span class="text-base">📊</span>
                            </div>
                            <span>{{ __('nav.prices') }}</span>
                        </a>
                        <a href="{{ route('how-it-works') }}" class="group px-4 py-2 rounded-xl hover:bg-white/15 transition-all duration-200 font-medium flex items-center gap-2 text-sm">
                            <div class="w-8 h-8 rounded-lg bg-white/10 group-hover:bg-white/20 flex items-center justify-center transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <span>{{ __('nav.how_it_works') }}</span>
                        </a>
                        <a href="{{ route('about') }}" class="group px-4 py-2 rounded-xl hover:bg-white/15 transition-all duration-200 font-medium flex items-center gap-2 text-sm">
                            <div class="w-8 h-8 rounded-lg bg-white/10 group-hover:bg-white/20 flex items-center justify-center transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                            </div>
                            <span>{{ __('nav.about') }}</span>
                        </a>
                        @auth
                            <a href="{{ route('dashboard') }}" class="group px-4 py-2 rounded-xl hover:bg-white/15 transition-all duration-200 font-medium flex items-center gap-2 text-sm">
                                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-[#C8A356]/30 to-[#b08a3c]/30 group-hover:from-[#C8A356]/40 group-hover:to-[#b08a3c]/40 flex items-center justify-center transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                                </div>
                                <span>{{ __('nav.dashboard') }}</span>
                            </a>
                        @endauth
                    </div>

                    <!-- Right Side Actions -->
                    <div class="flex items-center gap-2 sm:gap-3">
                        <!-- Language Switcher - Compact Pills -->
                        <div class="hidden sm:flex items-center gap-0.5 bg-white/10 backdrop-blur-sm rounded-full p-0.5">
                            <a href="{{ route('lang.switch','ar') }}" class="px-2.5 py-1 text-xs font-bold rounded-full {{ app()->getLocale()==='ar' ? 'bg-white text-[#6A8F3B] shadow-sm' : 'text-white/80 hover:text-white hover:bg-white/10' }} transition-all duration-200">AR</a>
                            <a href="{{ route('lang.switch','fr') }}" class="px-2.5 py-1 text-xs font-bold rounded-full {{ app()->getLocale()==='fr' ? 'bg-white text-[#6A8F3B] shadow-sm' : 'text-white/80 hover:text-white hover:bg-white/10' }} transition-all duration-200">FR</a>
                            <a href="{{ route('lang.switch','en') }}" class="px-2.5 py-1 text-xs font-bold rounded-full {{ app()->getLocale()==='en' ? 'bg-white text-[#6A8F3B] shadow-sm' : 'text-white/80 hover:text-white hover:bg-white/10' }} transition-all duration-200">EN</a>
                        </div>

                        @auth
                            <!-- User Menu -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="group flex items-center gap-2 px-3 py-1.5 bg-white/95 text-[#6A8F3B] rounded-full hover:bg-white transition-all duration-200 font-semibold shadow-lg shadow-black/10 hover:shadow-xl text-sm">
                                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-[#6A8F3B] to-[#5a7a2f] flex items-center justify-center text-white text-xs font-bold">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </div>
                                    <span class="hidden sm:inline max-w-[100px] truncate">{{ Auth::user()->name }}</span>
                                    <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </button>
                                <div x-show="open" x-cloak @click.away="open = false" 
                                     x-transition:enter="transition ease-out duration-200" 
                                     x-transition:enter-start="opacity-0 scale-95 -translate-y-2" 
                                     x-transition:enter-end="opacity-100 scale-100 translate-y-0" 
                                     x-transition:leave="transition ease-in duration-150" 
                                     x-transition:leave-start="opacity-100 scale-100" 
                                     x-transition:leave-end="opacity-0 scale-95" 
                                     class="absolute {{ app()->getLocale()==='ar' ? 'left-0' : 'right-0' }} mt-3 w-60 bg-white/95 backdrop-blur-xl rounded-2xl shadow-2xl shadow-black/20 border border-gray-100/50 py-2 z-[100] overflow-hidden">
                                    
                                    <!-- User Info Header -->
                                    <div class="px-4 py-3 bg-gradient-to-r from-[#6A8F3B]/10 to-[#C8A356]/10 border-b border-gray-100">
                                        <p class="font-bold text-gray-900 truncate">{{ Auth::user()->name }}</p>
                                        <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                                    </div>
                                    
                                    <div class="py-1">
                                        <a href="{{ route('dashboard') }}" class="group px-4 py-2.5 text-gray-700 hover:bg-[#6A8F3B]/10 transition-all duration-200 flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-[#6A8F3B]/10 group-hover:bg-[#6A8F3B]/20 flex items-center justify-center transition-all">
                                                <svg class="w-4 h-4 text-[#6A8F3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                                            </div>
                                            <span class="font-medium text-sm">{{ __('nav.dashboard') }}</span>
                                        </a>
                                        <a href="{{ route('messages.inbox') }}" class="group px-4 py-2.5 text-gray-700 hover:bg-blue-50 transition-all duration-200 flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-blue-100 group-hover:bg-blue-200 flex items-center justify-center transition-all relative">
                                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                            </div>
                                            <span class="font-medium text-sm">{{ __('Inbox') }}</span>
                                        </a>
                                        <a href="{{ route('profile.edit') }}" class="group px-4 py-2.5 text-gray-700 hover:bg-[#6A8F3B]/10 transition-all duration-200 flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-[#6A8F3B]/10 group-hover:bg-[#6A8F3B]/20 flex items-center justify-center transition-all">
                                                <svg class="w-4 h-4 text-[#6A8F3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                            </div>
                                            <span class="font-medium text-sm">{{ __('nav.profile') }}</span>
                                        </a>
                                        @if(Auth::user()->role === 'admin')
                                        <a href="{{ route('admin.dashboard') }}" class="group px-4 py-2.5 text-gray-700 hover:bg-amber-50 transition-all duration-200 flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-amber-100 group-hover:bg-amber-200 flex items-center justify-center transition-all">
                                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                                            </div>
                                            <span class="font-medium text-sm">{{ __('nav.admin_panel') }}</span>
                                        </a>
                                        @endif
                                    </div>
                                    
                                    <div class="border-t border-gray-100 mt-1 pt-1">
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="group w-full text-{{ app()->getLocale()==='ar' ? 'right' : 'left' }} px-4 py-2.5 text-red-600 hover:bg-red-50 transition-all duration-200 flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-lg bg-red-50 group-hover:bg-red-100 flex items-center justify-center transition-all">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                                                </div>
                                                <span class="font-medium text-sm">{{ __('nav.logout') }}</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="flex items-center gap-2 px-4 py-2 bg-white/95 text-[#6A8F3B] rounded-full hover:bg-white transition-all duration-200 font-semibold shadow-lg shadow-black/10 hover:shadow-xl text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" /></svg>
                                <span class="hidden sm:inline">{{ __('nav.login') }}</span>
                            </a>
                        @endauth

                        <!-- Mobile Menu Button -->
                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 rounded-xl hover:bg-white/15 transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Mobile Menu -->
                <div x-show="mobileMenuOpen" x-cloak @click.away="mobileMenuOpen = false" 
                     x-transition:enter="transition ease-out duration-300" 
                     x-transition:enter-start="opacity-0 max-h-0" 
                     x-transition:enter-end="opacity-100 max-h-screen" 
                     x-transition:leave="transition ease-in duration-200" 
                     x-transition:leave-start="opacity-100" 
                     x-transition:leave-end="opacity-0" 
                     class="lg:hidden py-4 border-t border-white/20 bg-gradient-to-b from-transparent to-black/10">
                    <div class="flex flex-col gap-1 px-2">
                        <a href="{{ route('home') }}" class="px-4 py-3 hover:bg-white/15 rounded-xl transition-all duration-200 flex items-center gap-3 font-medium">
                            <div class="w-9 h-9 rounded-lg bg-white/10 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                            </div>
                            {{ __('nav.home') }}
                        </a>
                        <a href="{{ route('prices.index') }}" class="px-4 py-3 hover:bg-white/15 rounded-xl transition-all duration-200 flex items-center gap-3 font-medium">
                            <div class="w-9 h-9 rounded-lg bg-amber-500/20 flex items-center justify-center"><span class="text-lg">📊</span></div>
                            {{ __('nav.prices') }}
                        </a>
                        <a href="{{ route('how-it-works') }}" class="px-4 py-3 hover:bg-white/15 rounded-xl transition-all duration-200 flex items-center gap-3 font-medium">
                            <div class="w-9 h-9 rounded-lg bg-white/10 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            {{ __('nav.how_it_works') }}
                        </a>
                        <a href="{{ route('about') }}" class="px-4 py-3 hover:bg-white/15 rounded-xl transition-all duration-200 flex items-center gap-3 font-medium">
                            <div class="w-9 h-9 rounded-lg bg-white/10 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            </div>
                            {{ __('nav.about') }}
                        </a>
                        @auth
                            <div class="mt-2 pt-2 border-t border-white/20">
                                <a href="{{ route('dashboard') }}" class="px-4 py-3 hover:bg-white/15 rounded-xl transition-all duration-200 flex items-center gap-3 font-medium">
                                    <div class="w-9 h-9 rounded-lg bg-[#C8A356]/20 flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                                    </div>
                                    {{ __('nav.dashboard') }}
                                </a>
                                <a href="{{ route('messages.inbox') }}" class="px-4 py-3 hover:bg-white/15 rounded-xl transition-all duration-200 flex items-center gap-3 font-medium">
                                    <div class="w-9 h-9 rounded-lg bg-blue-500/20 flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                    </div>
                                    {{ __('Inbox') }}
                                </a>
                                <a href="{{ route('profile.edit') }}" class="px-4 py-3 hover:bg-white/15 rounded-xl transition-all duration-200 flex items-center gap-3 font-medium">
                                    <div class="w-9 h-9 rounded-lg bg-white/10 flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                    </div>
                                    {{ __('nav.profile') }}
                                </a>
                                @if(Auth::user()->role === 'admin')
                                <a href="{{ route('admin.dashboard') }}" class="px-4 py-3 hover:bg-white/15 rounded-xl transition-all duration-200 flex items-center gap-3 font-medium">
                                    <div class="w-9 h-9 rounded-lg bg-amber-500/20 flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                                    </div>
                                    {{ __('nav.admin_panel') }}
                                </a>
                                @endif
                            </div>
                            <div class="mt-2 pt-2 border-t border-white/20">
                                <form method="POST" action="{{ route('logout') }}" class="px-2">
                                    @csrf
                                    <button type="submit" class="w-full px-4 py-3 bg-red-500/20 hover:bg-red-500/30 rounded-xl transition-all duration-200 flex items-center gap-3 text-red-300 font-medium">
                                        <div class="w-9 h-9 rounded-lg bg-red-500/20 flex items-center justify-center">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" /></svg>
                                        </div>
                                        {{ __('nav.logout') }}
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="mt-2 pt-2 border-t border-white/20 px-2">
                                <a href="{{ route('login') }}" class="w-full px-4 py-3 bg-white text-[#6A8F3B] rounded-xl transition-all duration-200 flex items-center justify-center gap-3 font-bold shadow-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" /></svg>
                                    {{ __('nav.login') }}
                                </a>
                            </div>
                        @endauth

                        <!-- Language Switcher -->
                        <div class="mt-3 px-4 flex items-center justify-center gap-1 bg-white/10 rounded-xl p-1.5 mx-2">
                            <a href="{{ route('lang.switch','ar') }}" class="flex-1 text-center px-3 py-2 {{ app()->getLocale()==='ar' ? 'bg-white text-[#6A8F3B] shadow-sm' : 'text-white/80 hover:text-white hover:bg-white/10' }} rounded-lg font-bold text-sm transition-all duration-200">العربية</a>
                            <a href="{{ route('lang.switch','fr') }}" class="flex-1 text-center px-3 py-2 {{ app()->getLocale()==='fr' ? 'bg-white text-[#6A8F3B] shadow-sm' : 'text-white/80 hover:text-white hover:bg-white/10' }} rounded-lg font-bold text-sm transition-all duration-200">Français</a>
                            <a href="{{ route('lang.switch','en') }}" class="flex-1 text-center px-3 py-2 {{ app()->getLocale()==='en' ? 'bg-white text-[#6A8F3B] shadow-sm' : 'text-white/80 hover:text-white hover:bg-white/10' }} rounded-lg font-bold text-sm transition-all duration-200">English</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sticky Price Ticker -->
    <div class="sticky top-0 z-40 shadow-md">
        @include('components.price-ticker')
    </div>

    @isset($header)
    <header class="bg-white/80 backdrop-blur-lg shadow-sm border-b border-gray-100">
        <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
            {{ $header }}
        </div>
    </header>
    @endisset

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <!-- Modern Footer -->
    <footer class="bg-gradient-to-b from-gray-50 to-gray-100 border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <!-- Footer Links Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-6 mb-8">
                <div>
                    <h4 class="font-bold text-gray-900 mb-3 text-sm uppercase tracking-wide">{{ __('nav.company') ?? 'Company' }}</h4>
                    <div class="flex flex-col gap-2">
                        <a class="text-gray-600 hover:text-[#6A8F3B] transition-colors text-sm" href="{{ route('about') }}">{{ __('nav.about') }}</a>
                        <a class="text-gray-600 hover:text-[#6A8F3B] transition-colors text-sm" href="{{ route('how-it-works') }}">{{ __('nav.how_it_works') }}</a>
                        <a class="text-gray-600 hover:text-[#6A8F3B] transition-colors text-sm" href="{{ route('public.contact') }}">{{ __('nav.contact') }}</a>
                    </div>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900 mb-3 text-sm uppercase tracking-wide">{{ __('nav.services') ?? 'Services' }}</h4>
                    <div class="flex flex-col gap-2">
                        <a class="text-gray-600 hover:text-[#6A8F3B] transition-colors text-sm" href="{{ route('pricing') }}">{{ __('nav.pricing') }}</a>
                        <a class="text-gray-600 hover:text-[#6A8F3B] transition-colors text-sm" href="{{ route('prices.index') }}">{{ __('nav.prices') }}</a>
                    </div>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900 mb-3 text-sm uppercase tracking-wide">{{ __('nav.legal') ?? 'Legal' }}</h4>
                    <div class="flex flex-col gap-2">
                        <a class="text-gray-600 hover:text-[#6A8F3B] transition-colors text-sm" href="{{ route('terms') }}">{{ __('nav.terms') }}</a>
                        <a class="text-gray-600 hover:text-[#6A8F3B] transition-colors text-sm" href="{{ route('privacy') }}">{{ __('nav.privacy') }}</a>
                    </div>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900 mb-3 text-sm uppercase tracking-wide">{{ __('nav.policies') ?? 'Policies' }}</h4>
                    <div class="flex flex-col gap-2">
                        <a class="text-gray-600 hover:text-[#6A8F3B] transition-colors text-sm" href="{{ route('seller-policy') }}">{{ __('nav.seller_policy') }}</a>
                        <a class="text-gray-600 hover:text-[#6A8F3B] transition-colors text-sm" href="{{ route('commission-policy') }}">{{ __('nav.commission_policy') }}</a>
                        <a class="text-gray-600 hover:text-[#6A8F3B] transition-colors text-sm" href="{{ route('licensing-policy') }}">{{ __('nav.licensing_policy') }}</a>
                    </div>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900 mb-3 text-sm uppercase tracking-wide">{{ __('nav.language') ?? 'Language' }}</h4>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('lang.switch','ar') }}" class="px-3 py-1.5 {{ app()->getLocale()==='ar' ? 'bg-[#6A8F3B] text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }} rounded-lg text-xs font-semibold transition-all">العربية</a>
                        <a href="{{ route('lang.switch','fr') }}" class="px-3 py-1.5 {{ app()->getLocale()==='fr' ? 'bg-[#6A8F3B] text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }} rounded-lg text-xs font-semibold transition-all">FR</a>
                        <a href="{{ route('lang.switch','en') }}" class="px-3 py-1.5 {{ app()->getLocale()==='en' ? 'bg-[#6A8F3B] text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }} rounded-lg text-xs font-semibold transition-all">EN</a>
                    </div>
                </div>
            </div>
            
            <!-- Footer Bottom -->
            <div class="pt-6 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/zintoop-logo.png') }}" alt="ZinToop" class="h-8 w-8 rounded-full">
                    <div>
                        <span class="font-bold text-gray-900">ZinToop</span>
                        <span class="text-gray-500 text-xs block">{{ __(app()->getLocale() === 'ar' ? 'brand.descriptor' : 'brand.descriptor') }}</span>
                    </div>
                </div>
                <p class="text-gray-500 text-sm">© {{ date('Y') }} {{ config('app.name') }}. {{ __('All rights reserved.') ?? 'All rights reserved.' }}</p>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
