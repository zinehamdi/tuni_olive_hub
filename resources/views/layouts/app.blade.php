<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- SEO Meta Tags -->
    @php
        $defaultBrandName = app()->getLocale() === 'ar' ? __('brand.name_ar') : __('brand.name_latin');
        $defaultOgTitle = $defaultBrandName . ' | Tunisian Olive Oil Marketplace';
    @endphp
    <title>{{ config('app.name') }} - @yield('title', $defaultBrandName)</title>
    <meta name="description" content="@yield('description', 'ZinToop - The leading Tunisian Olive Oil Marketplace. Connect directly with farmers and mills.')">
    <meta name="facebook-domain-verification" content="8b9o5r7q1jz9762hqdi15atqy5iwae" />

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="{{ config('app.name', 'ZinToop') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('og_title', $defaultOgTitle)">
    <meta property="og:description" content="@yield('og_description', __('Discover premium Tunisian olive oil directly from producers. No commissions, just pure quality.'))">
    <meta property="og:image" content="@yield('og_image', asset('images/zintooplogo3d.jpg'))">
    <meta property="og:locale" content="{{ app()->getLocale() === 'ar' ? 'ar_TN' : (app()->getLocale() === 'fr' ? 'fr_FR' : 'en_US') }}">
    <meta property="fb:app_id" content="{{ env('FB_APP_ID', '') }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="@yield('twitter_title', 'ZinToop | Tunisian Olive Oil Marketplace')">
    <meta name="twitter:description" content="@yield('twitter_description', 'Discover premium Tunisian olive oil directly from producers.')">
    <meta name="twitter:image" content="@yield('twitter_image', asset('images/zintooplogo3d.jpg'))">

    <!-- Alternate Language Links for SEO -->
    <link rel="alternate" hreflang="ar" href="{{ url()->current() }}?lang=ar">
    <link rel="alternate" hreflang="fr" href="{{ url()->current() }}?lang=fr">
    <link rel="alternate" hreflang="en" href="{{ url()->current() }}?lang=en">
    <link rel="alternate" hreflang="x-default" href="{{ url()->current() }}">

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="{{ asset('images/zintooplogo3d.jpg') }}">
    <link rel="apple-touch-icon" href="/icons/zintoop-192.png">
    <link rel="manifest" href="/manifest.webmanifest">
    <meta name="theme-color" content="#6A8F3B">
    <meta name="vapid-public-key" content="{{ config('webpush.vapid.public_key') }}">

    @if(app()->environment('production') || file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css','resources/js/app.js'])
    @endif
    <style nonce="{{ $cspNonce ?? '' }}">
        :root{--olive:#6b8e23;--gold:#b8860b;--sky:#38bdf8;--pepper:#b91c1c}
        .text-olive{color:var(--olive)} .bg-olive{background:var(--olive)}
        .text-gold{color:var(--gold)} .bg-gold{background:var(--gold)}
        .text-sky{color:var(--sky)} .bg-sky{background:var(--sky)}
        .text-pepper{color:var(--pepper)} .bg-pepper{background:var(--pepper)}
        [x-cloak] { display: none !important; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

        /* Modern Animated Logo Styles */
        .logo-animate { animation: float 4s ease-in-out infinite; }
        .drop-pulse { animation: pulse-gold 3s ease-in-out infinite; }
        .z-draw { 
            stroke-dasharray: 200; 
            stroke-dashoffset: 0;
            transition: all 0.6s ease;
        }
        .group:hover .z-draw { 
            stroke: #C8A356; 
            filter: drop-shadow(0 0 5px rgba(200, 163, 86, 0.5));
        }
        .group:hover .drop-pulse {
            fill: url(#gold_gradient);
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-4px); }
        }
        @keyframes pulse-gold {
            0%, 100% { filter: drop-shadow(0 0 2px rgba(106, 143, 59, 0.4)); }
            50% { filter: drop-shadow(0 0 8px rgba(200, 163, 86, 0.6)); }
        }
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

    @if(config('services.facebook.pixel_id'))
    <!-- Meta Pixel Code -->
    <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '{{ config('services.facebook.pixel_id') }}');
    fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id={{ config('services.facebook.pixel_id') }}&ev=PageView&noscript=1"
    /></noscript>
    <!-- End Meta Pixel Code -->
    @endif
</head>
<body class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-50 text-gray-900 antialiased">
    <!-- Modern Navigation with Glass Effect -->
    <nav class="fixed top-0 left-0 right-0 z-50" 
         x-data="{ 
             mobileMenuOpen: false, 
             scrolled: false, 
             unreadCount: 0, 
             notifications: [], 
             unreadNotificationsCount: 0,
             async fetchUnread() { 
                 try { 
                     const res = await fetch('/messages/unread-count', {
                         headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                     }); 
                     if (!res.ok) return;
                     const data = await res.json(); 
                     this.unreadCount = data.count || 0; 
                 } catch(e) {} 
             },
             async fetchNotifications() {
                 try {
                     const res = await fetch('/notifications', {
                         headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                     });
                     if (!res.ok) return;
                     const data = await res.json();
                     this.notifications = data;
                     this.unreadNotificationsCount = data.filter(n => !n.read_at).length;
                 } catch(e) {}
             },
             async markNotificationsAsRead() {
                 if (this.unreadNotificationsCount === 0) return;
                 try {
                     await fetch('/notifications/mark-read', { 
                         method: 'POST', 
                         headers: { 
                             'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                             'Accept': 'application/json',
                             'X-Requested-With': 'XMLHttpRequest'
                         } 
                     });
                     this.unreadNotificationsCount = 0;
                     this.notifications.forEach(n => n.read_at = n.read_at || new Date().toISOString());
                 } catch(e) {}
             }
         }" 
         x-init="@auth 
             fetchUnread(); 
             fetchNotifications();
             setInterval(() => { fetchUnread(); fetchNotifications(); }, 60000);
             if (window.Echo) {
                 window.Echo.private('App.Models.User.' + {{ auth()->id() }})
                     .notification((notification) => {
                         this.notifications.unshift({
                             id: notification.id,
                             data: notification,
                             read_at: null,
                             created_at: new Date().toISOString()
                         });
                         this.unreadNotificationsCount++;
                     });
             }
         @endauth" 
         @scroll.window="scrolled = window.scrollY > 20">
        <!-- Main Nav Bar -->
        <div class="bg-gradient-to-r from-[#5a7a2f] via-[#6A8F3B] to-[#5a7a2f] text-white transition-shadow duration-300" :class="scrolled ? 'shadow-2xl' : 'shadow-xl'">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16 sm:h-18">
                    <!-- Modern Animated Logo -->
                    <a href="{{ url('/') }}" class="flex-shrink-0 group flex items-center gap-3 no-underline">
                        <div class="relative w-12 h-12 flex items-center justify-center transition-transform duration-500 group-hover:scale-110">
                            <!-- Background Glow -->
                            <div class="absolute inset-0 bg-[#6A8F3B]/20 rounded-xl blur-lg group-hover:bg-[#C8A356]/30 transition-all duration-500"></div>
                            
                            <!-- SVG Logo -->
                            <svg class="relative w-10 h-10 drop-shadow-md logo-animate" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <!-- Outer Drop Shape -->
                                <path d="M50 95C72.0914 95 90 77.0914 90 55C90 32.9086 50 5 50 5C50 5 10 32.9086 10 55C10 77.0914 27.9086 95 50 95Z" fill="url(#logo_gradient)" class="drop-pulse"/>
                                
                                <!-- Stylized Z -->
                                <path d="M35 40H65L35 70H65" stroke="white" stroke-width="8" stroke-linecap="round" stroke-linejoin="round" class="z-draw"/>
                                
                                <defs>
                                    <linearGradient id="logo_gradient" x1="10" y1="5" x2="90" y2="95" gradientUnits="userSpaceOnUse">
                                        <stop offset="0%" stop-color="#6A8F3B" />
                                        <stop offset="100%" stop-color="#4A662A" />
                                    </linearGradient>
                                    <!-- Secondary Golden Gradient for Hover -->
                                    <linearGradient id="gold_gradient" x1="10" y1="5" x2="90" y2="95" gradientUnits="userSpaceOnUse">
                                        <stop offset="0%" stop-color="#C8A356" />
                                        <stop offset="100%" stop-color="#9A7A3A" />
                                    </linearGradient>
                                </defs>
                            </svg>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-2xl font-black text-white tracking-tighter leading-none group-hover:text-[#C8A356] transition-colors duration-300">ZinToop</span>
                            <span class="text-[9px] uppercase font-bold tracking-[0.2em] text-white/60 group-hover:text-white transition-colors duration-300">Marketplace</span>
                        </div>
                    </a>

                    <!-- Desktop Navigation -->
                    <div class="hidden md:flex items-center gap-1 flex-1 justify-center relative {{ app()->getLocale()==='ar' ? 'mr-6' : 'ml-6' }}"
                         x-data="{ 
                             showBot: false, 
                             botLeft: 0, 
                             botMessage: '', 
                             moveBot(e, msg) { 
                                 this.showBot = true; 
                                 this.botMessage = msg; 
                                 this.botLeft = e.currentTarget.offsetLeft + (e.currentTarget.offsetWidth / 2); 
                             }, 
                             hideBot() { 
                                 this.showBot = false; 
                             } 
                         }" 
                         @mouseleave="hideBot()">
                         
                        <!-- Navbar Hover Bot -->
                        <div class="absolute -bottom-16 transition-all duration-300 ease-out flex flex-col items-center pointer-events-none z-[60] w-max"
                             :class="showBot ? 'opacity-100 translate-y-0 scale-100' : 'opacity-0 translate-y-2 scale-95'"
                             :style="`left: 0; transform: translateX(calc(${botLeft}px - 50%));`">
                             
                            <!-- Chat Bubble -->
                            <div class="bg-white text-[#1a3310] px-3 py-1.5 rounded-xl shadow-2xl border border-[#6A8F3B]/30 text-xs font-bold whitespace-nowrap mb-1.5 relative">
                                <span x-text="botMessage"></span>
                                <div class="absolute -top-1 left-1/2 transform -translate-x-1/2 w-2 h-2 bg-white rotate-45 border-l border-t border-[#6A8F3B]/30"></div>
                            </div>
                            
                            <!-- Bot Image -->
                            <div class="relative">
                                <div class="absolute inset-0 bg-[#6A8F3B] rounded-full blur-sm opacity-40"></div>
                                <img src="{{ asset('images/ezzitouni_bot.png') }}" class="relative w-10 h-10 rounded-full border-2 border-white shadow-lg object-cover bg-white">
                            </div>
                        </div>

                        <a href="{{ route('home') }}" @mouseenter="moveBot($event, '{{ app()->getLocale() === 'ar' ? 'اكتشف أحدث العروض والمنتجات القريبة منك 🫒' : 'Discover the latest offers near you 🫒' }}')" class="group px-4 py-2 rounded-xl hover:bg-white/15 transition-all duration-200 font-medium flex items-center gap-2 text-sm">
                            <div class="w-8 h-8 rounded-lg bg-white/10 group-hover:bg-white/20 flex items-center justify-center transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                            </div>
                            <span>{{ __('nav.home') }}</span>
                        </a>
                        <a href="{{ route('prices.index') }}" @mouseenter="moveBot($event, '{{ app()->getLocale() === 'ar' ? 'تابع أسعار السوق المحلية لحظة بلحظة 📊' : 'Track local market prices in real-time 📊' }}')" class="group px-4 py-2 rounded-xl hover:bg-white/15 transition-all duration-200 font-medium flex items-center gap-2 text-sm">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-amber-400/30 to-amber-600/30 group-hover:from-amber-400/40 group-hover:to-amber-600/40 flex items-center justify-center transition-all">
                                <span class="text-base">📊</span>
                            </div>
                            <span>{{ __('nav.prices') }}</span>
                        </a>
                        <a href="{{ route('listings.create') }}" @mouseenter="moveBot($event, '{{ app()->getLocale() === 'ar' ? 'أضف منتجك أو زيتونك للبيع مجاناً الآن! 💰' : 'Add your product or olives for sale free! 💰' }}')" class="group px-4 py-2 rounded-xl hover:bg-white/15 transition-all duration-200 font-medium flex items-center gap-2 text-sm">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-[#6A8F3B]/30 to-[#5a7a2f]/30 group-hover:from-[#6A8F3B]/50 group-hover:to-[#5a7a2f]/50 flex items-center justify-center transition-all">
                                <span class="text-base">🫒</span>
                            </div>
                            <span>{{ app()->getLocale() === 'ar' ? 'بيع زيتك/زيتونك' : __('Sell Your Oil') }}</span>
                        </a>
                        <a href="{{ route('how-it-works') }}" @mouseenter="moveBot($event, '{{ app()->getLocale() === 'ar' ? 'تعرف على كيفية عمل المنصة خطوة بخطوة 📖' : 'Learn how the platform works step by step 📖' }}')" class="group px-4 py-2 rounded-xl hover:bg-white/15 transition-all duration-200 font-medium flex items-center gap-2 text-sm">
                            <div class="w-8 h-8 rounded-lg bg-white/10 group-hover:bg-white/20 flex items-center justify-center transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <span>{{ __('nav.how_it_works') }}</span>
                        </a>
                        <a href="{{ route('about') }}" @mouseenter="moveBot($event, '{{ app()->getLocale() === 'ar' ? 'من نحن وما هي رسالتنا لقطاع الزيتون التونسي 🇹🇳' : 'Who we are and our mission for Tunisian olive sector 🇹🇳' }}')" class="group px-4 py-2 rounded-xl hover:bg-white/15 transition-all duration-200 font-medium flex items-center gap-2 text-sm">
                            <div class="w-8 h-8 rounded-lg bg-white/10 group-hover:bg-white/20 flex items-center justify-center transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                            </div>
                            <span>{{ __('nav.about') }}</span>
                        </a>
                        @auth
                            <a href="{{ route('dashboard') }}" @mouseenter="moveBot($event, '{{ app()->getLocale() === 'ar' ? 'لوحة التحكم الخاصة بك لإدارة حسابك وعروضك ⚙️' : 'Your dashboard to manage your account and offers ⚙️' }}')" class="group px-4 py-2 rounded-xl hover:bg-white/15 transition-all duration-200 font-medium flex items-center gap-2 text-sm">
                                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-[#C8A356]/30 to-[#b08a3c]/30 group-hover:from-[#C8A356]/40 group-hover:to-[#b08a3c]/40 flex items-center justify-center transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                                </div>
                                <span>{{ __('nav.dashboard') }}</span>
                            </a>
                        @endauth
                    </div>

                    <!-- Right Side Actions -->
                    <div class="flex items-center gap-2 sm:gap-3">

                        <!-- Notification Bell -->
                        @auth
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open; if(open) markNotificationsAsRead()" class="p-2 text-white/90 hover:text-white hover:bg-white/10 rounded-full transition relative">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                <span x-show="unreadNotificationsCount > 0" x-cloak class="absolute top-1 right-1 w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center border-2 border-[#6A8F3B]" x-text="unreadNotificationsCount"></span>
                            </button>
                            
                            <div x-show="open" x-cloak @click.away="open = false" 
                                 x-transition:enter="transition ease-out duration-200" 
                                 x-transition:enter-start="opacity-0 scale-95 -translate-y-2" 
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0" 
                                 class="absolute {{ app()->getLocale()==='ar' ? 'left-0' : 'right-0' }} mt-3 w-80 bg-white rounded-2xl shadow-2xl border border-gray-100 py-2 z-[110] overflow-hidden">
                                <div class="px-4 py-2 border-b border-gray-100 flex items-center justify-between">
                                    <h3 class="font-bold text-gray-900 text-sm">{{ __('Notifications') }}</h3>
                                    <span class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">{{ __('Recent') }}</span>
                                </div>
                                <div class="max-h-96 overflow-y-auto">
                                    <template x-for="n in notifications" :key="n.id">
                                        <a :href="(n.data.type === 'transport_deal' && n.data.load_id) ? '/mobile/trip?id=' + n.data.load_id : (n.data.url || (n.data.type === 'message' ? '/messages' : '/dashboard'))" 
                                           class="block px-4 py-3 hover:bg-gray-50 transition border-b border-gray-50 last:border-0 relative group">
                                            <div class="flex gap-3">
                                                <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center shrink-0">
                                                    <span x-text="n.data.type === 'message' ? '💬' : '🔔'"></span>
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="text-xs text-gray-900 font-medium line-clamp-2" x-text="
                                                        (function(text) {
                                                            const locale = '{{ app()->getLocale() }}';
                                                            if (locale === 'ar') {
                                                                return text.replace('You have a new transport deal for', 'لديك عرض نقل جديد لـ')
                                                                           .replace(' of ', ' من ')
                                                                           .replace('Liters', 'لتر')
                                                                           .replace('Tonnes', 'طن')
                                                                           .replace('Chemlali', 'شملالي')
                                                                           .replace('Chétoui', 'شتوي');
                                                            } else if (locale === 'fr') {
                                                                return text.replace('You have a new transport deal for', 'Vous avez une nouvelle offre de transport pour')
                                                                           .replace(' of ', ' de ');
                                                            } else {
                                                                return text;
                                                            }
                                                        })(n.data.body || n.data.message || '{{ __('New notification') }}')
                                                    "></p>
                                                    <p class="text-[10px] text-gray-400 mt-1" x-text="new Date(n.created_at).toLocaleString()"></p>
                                                </div>
                                            </div>
                                            <div x-show="!n.read_at" class="absolute top-1/2 -translate-y-1/2 {{ app()->getLocale()==='ar' ? 'left-2' : 'right-2' }} w-2 h-2 bg-blue-500 rounded-full"></div>
                                        </a>
                                    </template>
                                    <div x-show="notifications.length === 0" class="px-4 py-8 text-center text-gray-400 italic text-sm">
                                        {{ __('No notifications yet') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endauth

                        <!-- Language Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center gap-1.5 px-3 py-1.5 bg-white/10 backdrop-blur-sm text-white rounded-full hover:bg-white/20 transition-all duration-200 font-bold text-xs uppercase border border-white/20 shadow-sm">
                                <span>{{ strtoupper(app()->getLocale()) }}</span>
                                <svg class="w-3 h-3 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </button>
                            
                            <div x-show="open" x-cloak @click.away="open = false" 
                                 x-transition:enter="transition ease-out duration-200" 
                                 x-transition:enter-start="opacity-0 scale-95 -translate-y-2" 
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0" 
                                 class="absolute {{ app()->getLocale()==='ar' ? 'left-0' : 'right-0' }} mt-2 w-24 bg-white rounded-xl shadow-xl border border-gray-100 py-1 z-[110] overflow-hidden">
                                <a href="{{ route('lang.switch','ar') }}" class="block px-4 py-2 text-xs font-bold text-gray-700 hover:bg-[#6A8F3B]/10 hover:text-[#6A8F3B] transition {{ app()->getLocale()==='ar' ? 'bg-gray-50' : '' }}">العربية (AR)</a>
                                <a href="{{ route('lang.switch','fr') }}" class="block px-4 py-2 text-xs font-bold text-gray-700 hover:bg-[#6A8F3B]/10 hover:text-[#6A8F3B] transition {{ app()->getLocale()==='fr' ? 'bg-gray-50' : '' }}">Français (FR)</a>
                                <a href="{{ route('lang.switch','en') }}" class="block px-4 py-2 text-xs font-bold text-gray-700 hover:bg-[#6A8F3B]/10 hover:text-[#6A8F3B] transition {{ app()->getLocale()==='en' ? 'bg-gray-50' : '' }}">English (EN)</a>
                            </div>
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
                                                <span x-show="unreadCount > 0" x-cloak class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center" x-text="unreadCount > 9 ? '9+' : unreadCount"></span>
                                            </div>
                                            <span class="font-medium text-sm">{{ __('Inbox') }}</span>
                                            <span x-show="unreadCount > 0" x-cloak class="ml-auto px-2 py-0.5 bg-red-500 text-white text-xs font-bold rounded-full" x-text="unreadCount > 99 ? '99+' : unreadCount"></span>
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
                        <button @click="mobileMenuOpen = !mobileMenuOpen; $dispatch('mobile-menu-toggled', { open: mobileMenuOpen })" class="md:hidden p-2 rounded-xl hover:bg-white/15 transition-all duration-200">
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
                     class="md:hidden py-4 border-t border-white/20 bg-gradient-to-b from-transparent to-black/10">
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
                        <a href="{{ route('listings.create') }}" class="px-4 py-3 hover:bg-white/15 rounded-xl transition-all duration-200 flex items-center gap-3 font-medium">
                            <div class="w-9 h-9 rounded-lg bg-[#6A8F3B]/30 flex items-center justify-center"><span class="text-lg">🫒</span></div>
                            {{ app()->getLocale() === 'ar' ? 'بيع زيتك/زيتونك' : __('Sell Your Oil') }}
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
                                    <div class="w-9 h-9 rounded-lg bg-blue-500/20 flex items-center justify-center relative">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                        <span x-show="unreadCount > 0" x-cloak class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center" x-text="unreadCount > 9 ? '9+' : unreadCount"></span>
                                    </div>
                                    {{ __('Inbox') }}
                                    <span x-show="unreadCount > 0" x-cloak class="ml-auto px-2 py-0.5 bg-red-500 text-white text-xs font-bold rounded-full" x-text="unreadCount > 99 ? '99+' : unreadCount"></span>
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

    <!-- Spacer for fixed navbar -->
    <div class="h-16 sm:h-[72px]"></div>

    <!-- Price Ticker - Fixed below navbar -->
    <div class="fixed top-16 sm:top-[72px] left-0 right-0 z-40 shadow-md">
        @include('components.price-ticker')
        @include('components.ads-ticker')
    </div>

    <!-- Spacer for both tickers (price ~50px + ads ~38px) -->
    <div class="h-24"></div>

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

    <!-- Unified Black Footer -->
    <footer class="bg-gray-900 text-white py-12 px-4 mt-12">
        <div class="max-w-7xl mx-auto grid md:grid-cols-4 gap-8">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <img src="{{ asset('images/zintooplogo3d.jpg') }}" alt="ZinToop" class="h-10 w-10 rounded-full object-cover">
                    <h3 class="text-xl font-bold">ZinToop</h3>
                </div>
                <p class="text-gray-400">{{ __('Platform connecting producers and buyers') }}</p>
            </div>
            <div>
                <h4 class="font-bold mb-4">{{ __('Quick Links') }}</h4>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="{{ url('/') }}" class="hover:text-white transition">{{ __('Home') }}</a></li>
                    <li><a href="{{ url('/#products') }}" class="hover:text-white transition">{{ __('Products') }}</a></li>
                    <li><a href="{{ route('about') }}" class="hover:text-white transition">{{ __('About') }}</a></li>
                    <li><a href="{{ route('pricing') }}" class="hover:text-white transition">{{ __('nav.pricing') }}</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold mb-4">{{ __('Account') }}</h4>
                <ul class="space-y-2 text-gray-400">
                    @auth
                        <li><a href="{{ route('dashboard') }}" class="hover:text-white transition">{{ __('Dashboard') }}</a></li>
                        <li><a href="{{ route('profile.edit') }}" class="hover:text-white transition">{{ __('Profile') }}</a></li>
                    @else
                        <li><a href="{{ route('login') }}" class="hover:text-white transition">{{ __('Login') }}</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-white transition">{{ __('Register') }}</a></li>
                    @endauth
                </ul>
            </div>
            <div>
                <h4 class="font-bold mb-4">{{ __('Contact Us') }}</h4>
                <p class="text-gray-400 mb-2">{{ __('Email') }}: <span dir="ltr" style="unicode-bidi: embed;">contact@zintoop.com</span></p>
                <p class="text-gray-400 mb-4">{{ __('Phone') }}: <span dir="ltr" style="unicode-bidi: embed;">+216 25 777 926</span></p>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('lang.switch','ar') }}" class="px-2 py-1 {{ app()->getLocale()==='ar' ? 'bg-[#6A8F3B] text-white' : 'bg-gray-800 text-gray-300 hover:bg-gray-700' }} rounded text-xs transition-all">العربية</a>
                    <a href="{{ route('lang.switch','fr') }}" class="px-2 py-1 {{ app()->getLocale()==='fr' ? 'bg-[#6A8F3B] text-white' : 'bg-gray-800 text-gray-300 hover:bg-gray-700' }} rounded text-xs transition-all">FR</a>
                    <a href="{{ route('lang.switch','en') }}" class="px-2 py-1 {{ app()->getLocale()==='en' ? 'bg-[#6A8F3B] text-white' : 'bg-gray-800 text-gray-300 hover:bg-gray-700' }} rounded text-xs transition-all">EN</a>
                </div>
            </div>
        </div>
        <div class="max-w-7xl mx-auto mt-8 pt-8 border-t border-gray-800 text-center text-gray-400 flex flex-col md:flex-row justify-between items-center gap-4">
            <p>© 2024 - {{ now()->year }} ZinToop. {{ __('All Rights Reserved') }}.</p>
            <div class="flex flex-wrap justify-center gap-4 text-sm">
                <a href="{{ route('terms') }}" class="hover:text-white transition">{{ __('nav.terms') }}</a>
                <a href="{{ route('privacy') }}" class="hover:text-white transition">{{ __('nav.privacy') }}</a>
                <a href="{{ route('seller-policy') }}" class="hover:text-white transition">{{ __('nav.seller_policy') }}</a>
                <a href="{{ route('commission-policy') }}" class="hover:text-white transition">{{ __('nav.commission_policy') }}</a>
            </div>
        </div>
    </footer>

    @stack('scripts')
    @include('components.ezzitouni-chat')
</body>
</html>
