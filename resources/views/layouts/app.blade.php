<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- SEO Meta Tags -->
    <title>{{ config('app.name') }} - @yield('title', 'منصة زيت الزيتون التونسي | Tunisian Olive Oil Platform')</title>
    <meta name="description" content="@yield('description', 'منصة تونسية رائدة لتجارة زيت الزيتون والزيتون. اكتشف منتجات زيت الزيتون التونسي الأصلي من المزارعين والمعاصر والمعبئين. Tunisian olive oil marketplace connecting farmers, mills, packers and buyers.')">
    <meta name="keywords" content="زيت الزيتون التونسي, زيتون تونس, olive oil tunisia, huile d'olive tunisienne, تجارة زيت الزيتون, معصرة زيتون, مزارع زيتون, tunisian olive oil, marketplace, e-commerce">
    <meta name="author" content="Tunisian Olive Oil Platform">
    <meta name="robots" content="index, follow">
    <meta name="googlebot" content="index, follow">
    
    <!-- Open Graph Meta Tags for Social Media -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:title" content="@yield('og_title', 'منصة زيت الزيتون التونسي | Tunisian Olive Oil Platform')">
    <meta property="og:description" content="@yield('og_description', 'اكتشف أفضل منتجات زيت الزيتون التونسي الأصلي مباشرة من المزارعين والمعاصر')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="@yield('og_image', asset('images/logotoop.PNG'))">
    <meta property="og:locale" content="{{ app()->getLocale() === 'ar' ? 'ar_TN' : (app()->getLocale() === 'fr' ? 'fr_FR' : 'en_US') }}">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('twitter_title', 'منصة زيت الزيتون التونسي')">
    <meta name="twitter:description" content="@yield('twitter_description', 'اكتشف أفضل منتجات زيت الزيتون التونسي')">
    <meta name="twitter:image" content="@yield('twitter_image', asset('images/logotoop.PNG'))">
    
    <!-- Alternate Language Links for SEO -->
    <link rel="alternate" hreflang="ar" href="{{ url()->current() }}?lang=ar">
    <link rel="alternate" hreflang="fr" href="{{ url()->current() }}?lang=fr">
    <link rel="alternate" hreflang="en" href="{{ url()->current() }}?lang=en">
    <link rel="alternate" hreflang="x-default" href="{{ url()->current() }}">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logotoop.PNG') }}">
    
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
        "name": "منصة زيت الزيتون التونسي",
        "alternateName": "Tunisian Olive Oil Platform",
        "url": "{{ url('/') }}",
        "description": "منصة تونسية لتجارة زيت الزيتون والزيتون",
        "potentialAction": {
            "@@type": "SearchAction",
            "target": "{{ url('/') }}?search={search_term_string}",
            "query-input": "required name=search_term_string"
        },
        "inLanguage": ["ar", "fr", "en"]
    }
    </script>
</head>
<body class="min-h-screen bg-white text-gray-900">
    <!-- Main Navigation (Scrolls normally) -->
    <nav class="shadow-lg" x-data="{ mobileMenuOpen: false }">
        <!-- Layer 1: Main Navigation -->
        <div class="bg-gradient-to-r from-[#6A8F3B] to-[#5a7a2f] text-white">
            <div class="max-w-7xl mx-auto px-2 sm:px-4">
                <div class="flex items-center justify-between h-20">
                    
                    <!-- Logo (Left/Right based on language) -->
                    <a href="{{ route('home') }}" class="flex-shrink-0 group">
                        <img src="{{ asset('images/barecodeTN.png') }}" 
                             alt="Tuni Olive Hub - Made in Tunisia" 
                             class="h-16 w-auto object-contain group-hover:scale-105 transition-transform duration-300">
                    </a>

                    <!-- Desktop Navigation Links -->
                    <div class="hidden md:flex items-center gap-6 flex-1 {{ app()->getLocale()==='ar' ? 'mr-8' : 'ml-8' }}">
                        <a href="{{ route('home') }}" 
                           class="px-4 py-2 rounded-lg hover:bg-white/10 transition font-medium flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            {{ __('Home') }}
                        </a>
                        
                        <a href="{{ route('prices.index') }}" 
                           class="px-4 py-2 rounded-lg hover:bg-white/10 transition font-medium flex items-center gap-2">
                            <span class="text-xl">📊</span>
                            {{ __('Prices') }}
                        </a>
                        
                        @auth
                            <a href="{{ route('dashboard') }}" 
                               class="px-4 py-2 rounded-lg hover:bg-white/10 transition font-medium flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                </svg>
                                {{ __('Dashboard') }}
                            </a>
                        @endif
                    </div>

                    <!-- Right Side: Language + Auth -->
                    <div class="flex items-center gap-4">
                        <!-- Language Switcher -->
                        <div class="flex items-center gap-1 bg-white/10 rounded-lg p-1">
                            <a href="{{ route('lang.switch','ar') }}" 
                               class="px-3 py-1.5 text-sm font-semibold rounded {{ app()->getLocale()==='ar' ? 'bg-white text-[#6A8F3B]' : 'text-white hover:bg-white/10' }} transition">
                                AR
                            </a>
                            <a href="{{ route('lang.switch','fr') }}" 
                               class="px-3 py-1.5 text-sm font-semibold rounded {{ app()->getLocale()==='fr' ? 'bg-white text-[#6A8F3B]' : 'text-white hover:bg-white/10' }} transition">
                                FR
                            </a>
                            <a href="{{ route('lang.switch','en') }}" 
                               class="px-3 py-1.5 text-sm font-semibold rounded {{ app()->getLocale()==='en' ? 'bg-white text-[#6A8F3B]' : 'text-white hover:bg-white/10' }} transition">
                                EN
                            </a>
                        </div>

                        <!-- Auth Button -->
                        @auth
                            <!-- User Dropdown Menu -->
                            <div class="relative hidden sm:block" x-data="{ open: false }">
                                <button @click="open = !open" 
                                        class="flex items-center gap-2 px-4 py-2 bg-white text-[#6A8F3B] rounded-lg hover:bg-white/90 transition font-semibold">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span>{{ Auth::user()->name }}</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                
                                <!-- Dropdown Menu -->
                                <div x-show="open"
                                     x-cloak
                                     @click.away="open = false"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 scale-95"
                                     class="absolute {{ app()->getLocale()==='ar' ? 'left-0' : 'right-0' }} mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-200 py-2 z-[100]">
                                    <a href="{{ route('dashboard') }}" 
                                       class="px-4 py-3 text-gray-700 hover:bg-gray-50 transition flex items-center gap-3">
                                        <svg class="w-5 h-5 text-[#6A8F3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                        </svg>
                                        <span class="font-semibold">{{ __('Dashboard') }}</span>
                                    </a>
                                    <a href="{{ route('profile.edit') }}" 
                                       class="px-4 py-3 text-gray-700 hover:bg-gray-50 transition flex items-center gap-3">
                                        <svg class="w-5 h-5 text-[#6A8F3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <span class="font-semibold">{{ __('Profile') }}</span>
                                    </a>
                                    @if(Auth::user()->role === 'admin')
                                    <a href="{{ route('admin.dashboard') }}" 
                                       class="px-4 py-3 text-gray-700 hover:bg-gray-50 transition flex items-center gap-3">
                                        <svg class="w-5 h-5 text-[#6A8F3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                        <span class="font-semibold">{{ __('Admin Panel') }}</span>
                                    </a>
                                    @endif
                                    <hr class="my-2">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" 
                                                class="w-full text-left px-4 py-3 text-red-600 hover:bg-red-50 transition flex items-center gap-3">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                            </svg>
                                            <span class="font-semibold">{{ __('Logout') }}</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}" 
                               class="hidden sm:flex items-center gap-2 px-4 py-2 bg-white text-[#6A8F3B] rounded-lg hover:bg-white/90 transition font-semibold">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                </svg>
                                {{ __('Login') }}
                            </a>
                        @endauth

                        <!-- Mobile Menu Button -->
                        <button @click="mobileMenuOpen = !mobileMenuOpen" 
                                class="md:hidden p-2 rounded-lg hover:bg-white/10 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Mobile Menu (Hidden by default) -->
                <div x-show="mobileMenuOpen"
                     x-cloak
                     @click.away="mobileMenuOpen = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="md:hidden py-4 border-t border-white/20">
                    <div class="flex flex-col gap-2">
                        <a href="{{ route('home') }}" class="px-4 py-3 hover:bg-white/10 rounded-lg transition flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            {{ __('Home') }}
                        </a>
                        <a href="{{ route('prices.index') }}" class="px-4 py-3 hover:bg-white/10 rounded-lg transition flex items-center gap-3">
                            <span class="text-xl">📊</span>
                            {{ __('Prices') }}
                        </a>
                        @auth
                            <a href="{{ route('dashboard') }}" class="px-4 py-3 hover:bg-white/10 rounded-lg transition flex items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                </svg>
                                {{ __('Dashboard') }}
                            </a>
                            <a href="{{ route('profile.edit') }}" class="px-4 py-3 hover:bg-white/10 rounded-lg transition flex items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                {{ __('Profile') }}
                            </a>
                            @if(Auth::user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}" class="px-4 py-3 hover:bg-white/10 rounded-lg transition flex items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                {{ __('Admin Panel') }}
                            </a>
                            @endif
                            <div class="border-t border-white/20 my-2"></div>
                            <form method="POST" action="{{ route('logout') }}" class="px-4">
                                @csrf
                                <button type="submit" class="w-full text-left py-3 text-red-400 hover:text-red-300 transition flex items-center gap-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    {{ __('Logout') }}
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="px-4 py-3 hover:bg-white/10 rounded-lg transition flex items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                </svg>
                                {{ __('Login') }}
                            </a>
                        @endauth
                        
                        <!-- Language in Mobile -->
                        <div class="px-4 py-2 flex items-center gap-2">
                            <a href="{{ route('lang.switch','ar') }}" class="px-3 py-2 {{ app()->getLocale()==='ar' ? 'bg-white text-[#6A8F3B]' : 'bg-white/10' }} rounded font-semibold text-sm">AR</a>
                            <a href="{{ route('lang.switch','fr') }}" class="px-3 py-2 {{ app()->getLocale()==='fr' ? 'bg-white text-[#6A8F3B]' : 'bg-white/10' }} rounded font-semibold text-sm">FR</a>
                            <a href="{{ route('lang.switch','en') }}" class="px-3 py-2 {{ app()->getLocale()==='en' ? 'bg-white text-[#6A8F3B]' : 'bg-white/10' }} rounded font-semibold text-sm">EN</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sticky Price Ticker (stays at top when scrolling) -->
    <div class="sticky top-0 z-50">
        @include('components.price-ticker')
    </div>

    <!-- Page Header (if provided) -->
    @isset($header)
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            {{ $header }}
        </div>
    </header>
    @endisset

    <main class="max-w-6xl mx-auto px-4 py-6">
        {{ $slot ?? '' }}
        @yield('content')
    </main>
    <footer class="border-t py-4 text-center text-xs text-gray-600">
        <span>© {{ date('Y') }} {{ config('app.name') }}</span>
    </footer>
    @stack('scripts')
 </body>
</html>
