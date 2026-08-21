<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ __('ltr') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- SEO Meta Tags -->
    @php
        $locale = app()->getLocale();
        $defaultBrandName = $locale === 'ar' ? 'زين توب' : 'ZinToop';
        $defaultTitle = match($locale) {
            'ar' => 'زين توب | السوق التونسي الأول لزيت الزيتون والخدمات الفلاحية',
            'fr' => 'ZinToop | 1ère Marketplace d\'Huile d\'Olive en Tunisie',
            default => 'ZinToop | Leading Tunisian Olive Oil & Agricultural Services Marketplace',
        };
        $defaultDesc = match($locale) {
            'ar' => 'زين توب - المنصة الأولى لزيت الزيتون والدليل التجاري في تونس. تواصل مباشرة مع الفلاحين والمعاصر بدون عمولات.',
            'fr' => 'ZinToop - Première plateforme d\'huile d\'olive et annuaire B2B en Tunisie. Connectez-vous directement avec les producteurs sans commissions.',
            default => 'ZinToop - Premier Tunisian Olive Oil Marketplace & B2B Directory. Connect directly with farmers and oil mills without commissions.',
        };
        $defaultKeywords = match($locale) {
            'ar' => 'الشملالي, الشتوي, سلالات الزيتون في تونس, أنواع الزيتون التونسي, أحسن أنواع زيت الزيتون في العالم, أحسن سلالات الزيتون في العالم, أكبر سوق زيت زيتون, أسعار زيت الزيتون في تونس, أسعار زيت الزيتون في العالم, أسهل طريقة لترويج وشراء زيت الزيتون, زيت زيتون, معصرة زيتون, تونس, فلاح, زيت بكر ممتاز, سوق الزيتون, زين توب',
            'fr' => 'variétés d\'olives tunisiennes, chemlali, chetoui, olives en tunisie, meilleures huiles d\'olive au monde, meilleures variétés d\'olivier, le plus grand marché d\'huile d\'olive, prix d\'huile d\'olive en tunisie, prix mondial d\'huile d\'olive, moyen le plus facile pour vendre et acheter l\'huile d\'olive, huile d\'olive tunisie, moulin à huile, zintoop',
            default => 'chemlali, chetoui, tunisian olive varieties, varieties of olives in tunisia, best olive oil varieties in the world, best olive tree varieties, largest olive oil marketplace, olive oil prices in tunisia, global olive oil prices, easiest way to buy and sell olive oil, tunisian olive oil, extra virgin olive oil, zintoop, tunisian olive oil market, import olive oil, fastest olive oil export, best olive oil producers, best olive oil prices',
        };
    @endphp
    <title>{{ config('app.name') }} - @yield('title', $defaultTitle)</title>
    <meta name="description" content="@yield('description', $defaultDesc)">
    <meta name="keywords" content="@yield('keywords', $defaultKeywords)">
    <meta name="facebook-domain-verification" content="8b9o5r7q1jz9762hqdi15atqy5iwae" />

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="{{ trim($__env->yieldContent('og_type', 'website')) }}">
    <meta property="og:site_name" content="ZinToop">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ trim($__env->yieldContent('og_title', $defaultTitle)) }}">
    <meta property="og:description" content="{{ trim($__env->yieldContent('og_description', $defaultDesc)) }}">
    <meta property="og:image" content="{{ trim($__env->yieldContent('og_image', asset('images/zintoop-logo.png'))) }}">
    <meta property="og:image:secure_url" content="{{ trim($__env->yieldContent('og_image', asset('images/zintoop-logo.png'))) }}">
    <meta property="og:locale" content="{{ __('en_US') }}">
    <meta property="fb:app_id" content="{{ env('FACEBOOK_CLIENT_ID') }}">
    @yield('og_product_tags')
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="{{ trim($__env->yieldContent('twitter_title', $defaultTitle)) }}">
    <meta name="twitter:description" content="{{ trim($__env->yieldContent('twitter_description', $defaultDesc)) }}">
    <meta name="twitter:image" content="{{ trim($__env->yieldContent('twitter_image', asset('images/zintoop-logo.png'))) }}">

    @php
        $pathWithoutLocale = preg_replace('#^/(ar|fr|en)#', '', request()->getPathInfo()) ?: '/';
    @endphp
    <!-- JSON-LD Schema (Structured Data for Google) -->
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => $defaultBrandName,
        'alternateName' => 'ZinToop',
        'url' => url('/'),
        'description' => $defaultDesc,
        'inLanguage' => app()->getLocale(),
        'potentialAction' => [
            '@type' => 'SearchAction',
            'target' => url(app()->getLocale()) . '?search={search_term_string}',
            'query-input' => 'required name=search_term_string',
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => 'ZinToop',
        'url' => 'https://zintoop.com',
        'logo' => asset('images/zintoop-logo.png'),
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>

    <!-- Alternate Language Links for SEO (path-based, not query-param) -->
    <link rel="alternate" hreflang="ar" href="{{ url('ar' . $pathWithoutLocale) }}">
    <link rel="alternate" hreflang="fr" href="{{ url('fr' . $pathWithoutLocale) }}">
    <link rel="alternate" hreflang="en" href="{{ url('en' . $pathWithoutLocale) }}">
    <link rel="alternate" hreflang="x-default" href="{{ url('ar' . $pathWithoutLocale) }}">

    <!-- Canonical URL — the current locale version of this page -->
    <link rel="canonical" href="{{ url(app()->getLocale() . $pathWithoutLocale) }}">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/zintoop-logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/zintoop-192.png') }}">
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
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
        document.documentElement.dir = '{{ __('ltr') }}';
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
<body class="min-h-screen text-gray-900 antialiased">
    <!-- ZinToop Welcoming Cover Overlay (2.5s) -->
    <x-welcome-splash />

    <!-- Modern Navigation with Glass Effect -->
    <nav class="fixed top-0 left-0 right-0 z-50" dir="ltr" 
         x-data="{ 
             mobileMenuOpen: false, 
             scrolled: false, 
             unreadCount: 0, 
             touchStartX: 0,
             touchStartY: 0,
             notifications: [], 
             unreadNotificationsCount: 0,
             async fetchUnread() { 
                 try { 
                     const res = await fetch('{{ route('messages.unread') }}', {
                         headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                     }); 
                     if (!res.ok) return;
                     const data = await res.json(); 
                     this.unreadCount = data.count || 0; 
                 } catch(e) {} 
             },
             async fetchNotifications() {
                 try {
                     const res = await fetch('{{ route('notifications.index') }}', {
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
                     await fetch('{{ route('notifications.mark-read') }}', { 
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
             },
             async markOneAsRead(notificationId) {
                 const notification = this.notifications.find(n => n.id === notificationId);
                 if (notification && !notification.read_at) {
                     notification.read_at = new Date().toISOString();
                     this.unreadNotificationsCount = Math.max(0, this.unreadNotificationsCount - 1);
                     try {
                         await fetch(`/notifications/${notificationId}/read`, {
                             method: 'POST',
                             headers: {
                                 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                 'Accept': 'application/json',
                                 'X-Requested-With': 'XMLHttpRequest'
                             }
                         });
                     } catch(e) {}
                 }
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
                         
                         // Trigger modern visual toast alert!
                         const msg = notification.body || notification.message || '{{ __('New notification') }}';
                         showToast(msg, 'success');
                     });
             }
         @endauth" 
         @scroll.window="scrolled = window.scrollY > 20">
        {{-- Platform Update Notification Banner (hidden after deploy) --}}

        <!-- Main Nav Bar -->
        <div class="bg-gradient-to-r from-[#5a7a2f] via-[#6A8F3B] to-[#5a7a2f] text-white transition-shadow duration-300" :class="scrolled ? 'shadow-2xl' : 'shadow-xl'">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-14 sm:h-[60px]">
                    <!-- Modern Animated Logo -->
                    <a href="{{ url('/') }}" class="flex-shrink-0 group flex items-center gap-3 no-underline">
                        <div class="relative w-10 h-10 flex items-center justify-center transition-transform duration-500 group-hover:scale-110">
                            <!-- Background Glow -->
                            <div class="absolute inset-0 bg-[#6A8F3B]/20 rounded-xl blur-lg group-hover:bg-[#C8A356]/30 transition-all duration-500"></div>
                            
                            <!-- Rounded Image Logo -->
                            <img src="{{ asset('images/zintoop-logo.png') }}" class="relative w-8 h-8 rounded-full object-cover drop-shadow-md logo-animate border border-white/20" alt="ZinToop Logo">

                        </div>
                        <div class="flex flex-col">
                            <span class="text-2xl font-black text-white tracking-tighter leading-none group-hover:text-[#C8A356] transition-colors duration-300">ZinToop</span>
                            <span class="text-[9px] uppercase font-bold tracking-[0.2em] text-white/60 group-hover:text-white transition-colors duration-300">{{ __('Marketplace') }}</span>
                        </div>
                    </a>

                    <!-- Desktop Navigation -->
                    <div class="hidden md:flex items-center gap-1 flex-1 justify-center relative {{ __('ml-6') }}"
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
                        <div class="absolute -bottom-16 transition-all duration-300 ease-out flex flex-col items-center pointer-events-none z-[60] w-max opacity-0 translate-y-2 scale-95"
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

                        <a href="{{ route('home') }}" @mouseenter="moveBot($event, {{ Js::from(__('nav.tooltip_home')) }})" class="group px-4 py-2 rounded-xl hover:bg-white/15 transition-all duration-200 font-medium flex items-center gap-2 text-sm">
                            <div class="w-8 h-8 rounded-lg bg-white/10 group-hover:bg-white/20 flex items-center justify-center transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                            </div>
                            <span>{{ __('Home') }}</span>
                        </a>
                        <a href="{{ route('prices.index') }}" class="group px-4 py-2 rounded-xl hover:bg-white/15 transition-all duration-200 font-medium flex items-center gap-2 text-sm">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-amber-400/30 to-amber-600/30 group-hover:from-amber-400/40 group-hover:to-amber-600/40 flex items-center justify-center transition-all">
                                <span class="text-base">📊</span>
                            </div>
                            <span>{{ __('Prices') }}</span>
                        </a>
                        <a href="{{ route('listings.create') }}" class="group px-4 py-2 rounded-xl hover:bg-white/15 transition-all duration-200 font-medium flex items-center gap-2 text-sm">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-[#6A8F3B]/30 to-[#5a7a2f]/30 group-hover:from-[#6A8F3B]/50 group-hover:to-[#5a7a2f]/50 flex items-center justify-center transition-all">
                                <span class="text-base">🫒</span>
                            </div>
                            <span>{{ __('Sell Your Oil / Olives') }}</span>
                        </a>
                        <a href="{{ route('services.index') }}" class="group px-4 py-2 rounded-xl hover:bg-white/15 transition-all duration-200 font-medium flex items-center gap-2 text-sm">
                            <div class="w-8 h-8 rounded-lg bg-white/10 group-hover:bg-white/20 flex items-center justify-center transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                            </div>
                            <span>{{ __('Service Hub') }}</span>
                        </a>
                        <a href="{{ route('services.pricing') }}" class="group px-4 py-2 rounded-xl hover:bg-white/15 transition-all duration-200 font-medium flex items-center gap-2 text-sm">
                            <div class="w-8 h-8 rounded-lg bg-white/10 group-hover:bg-white/20 flex items-center justify-center transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                            </div>
                            <span>{{ __('Our Digital Services') }}</span>
                        </a>
                        @auth
                            <a href="{{ route('dashboard') }}" class="group px-4 py-2 rounded-xl hover:bg-white/15 transition-all duration-200 font-medium flex items-center gap-2 text-sm">
                                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-[#C8A356]/30 to-[#b08a3c]/30 group-hover:from-[#C8A356]/40 group-hover:to-[#b08a3c]/40 flex items-center justify-center transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                                </div>
                                <span>{{ __('Dashboard') }}</span>
                            </a>
                        @endauth
                    </div>

                    <!-- Right Side Actions -->
                    <div class="flex items-center gap-2 sm:gap-3">

                        <!-- Notification Bell -->
                        @auth
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open; markNotificationsAsRead()" class="p-2 text-white/90 hover:text-white hover:bg-white/10 rounded-full transition relative">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                <span x-show="unreadNotificationsCount > 0" x-cloak class="absolute top-1 right-1 w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center border-2 border-[#6A8F3B]" x-text="unreadNotificationsCount"></span>
                            </button>
                            
                            <div x-show="open" x-cloak @click.away="open = false" 
                                 x-transition:enter="transition ease-out duration-200" 
                                 x-transition:enter-start="opacity-0 scale-95 -translate-y-2" 
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0" 
                                 class="fixed left-4 right-4 top-16 sm:absolute sm:left-auto sm:right-0 sm:top-auto sm:mt-3 w-auto sm:w-80 bg-white rounded-2xl shadow-2xl border border-gray-100 py-2 z-[110] overflow-hidden">
                                <div class="px-4 py-2 border-b border-gray-100 flex items-center justify-between">
                                    <h3 class="font-bold text-gray-900 text-sm">{{ __('Notifications') }}</h3>
                                    <button @click="markNotificationsAsRead()" class="text-[10px] text-[#6A8F3B] hover:text-[#5a7a2f] hover:underline font-bold transition">
                                        {{ __('Mark all as read') }}
                                    </button>
                                </div>
                                <div class="max-h-96 overflow-y-auto">
                                    <template x-for="n in notifications" :key="n.id">
                                        <a :href="
                                                (n.data.type === 'transport_deal' && n.data.load_id)
                                                    ? '/mobile/trip?id=' + n.data.load_id
                                                : (n.data.type === 'message')
                                                    ? '/messages'
                                                : (n.data.type === 'appointment' || (n.data.url === '/dashboard' && {{ Auth::user()->role === 'admin' ? 'true' : 'false' }}))
                                                    ? '{{ route('admin.marketing.index') }}'
                                                : (n.data.url || '/dashboard')
                                            " 
                                           @click="markOneAsRead(n.id)"
                                           :class="!n.read_at ? 'bg-green-100 hover:bg-green-200/60 font-bold {{ app()->getLocale() === "ar" ? "border-r-4 border-r-[#6A8F3B]" : "border-l-4 border-l-[#6A8F3B]" }}' : 'bg-white hover:bg-gray-50 {{ app()->getLocale() === "ar" ? "border-r-4 border-r-transparent" : "border-l-4 border-l-transparent" }}'"
                                           class="block px-4 py-3 transition border-b border-gray-50 last:border-0 relative group">
                                            <div class="flex gap-3">
                                                <div :class="n.data.type === 'message' ? 'bg-blue-50' : (n.data.type === 'deal_request' ? 'bg-amber-50' : 'bg-emerald-50')" class="w-8 h-8 rounded-full flex items-center justify-center shrink-0">
                                                    <span x-text="n.data.type === 'message' ? '💬' : (n.data.type === 'deal_request' ? '🤝' : '🔔')"></span>
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
                                             <div x-show="!n.read_at" class="absolute top-1/2 -translate-y-1/2 {{ __('right-3') }} w-2.5 h-2.5 bg-[#6A8F3B] rounded-full shadow-sm shadow-[#6A8F3B]/30"></div>
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
                                @php
                                    $currentFlag = match(app()->getLocale()) {
                                        'ar' => '🇹🇳',
                                        'fr' => '🇫🇷',
                                        'en' => '🇬🇧',
                                        'es' => '🇪🇸',
                                        'zh' => '🇨🇳',
                                        'ja' => '🇯🇵',
                                        default => '🇹🇳'
                                    };
                                @endphp
                                <span class="text-sm leading-none">{{ $currentFlag }}</span>
                                <span>{{ strtoupper(app()->getLocale()) }}</span>
                                <svg class="w-3 h-3 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </button>
                            
                            <div x-show="open" x-cloak @click.away="open = false" 
                                 x-transition:enter="transition ease-out duration-200" 
                                 x-transition:enter-start="opacity-0 scale-95 -translate-y-2" 
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0" 
                                 class="absolute {{ __('right-0') }} mt-2 w-36 bg-white rounded-xl shadow-xl border border-gray-100 py-1 z-[110] overflow-hidden">
                                @php $switchPath = preg_replace('#^/(ar|fr|en)#', '', request()->getPathInfo()) ?: '/'; @endphp
                                <a href="{{ url('ar' . $switchPath) }}" class="flex items-center gap-2 px-4 py-2 text-xs font-bold text-gray-700 hover:bg-[#6A8F3B]/10 hover:text-[#6A8F3B] transition {{ app()->getLocale()==='ar' ? 'bg-gray-50 text-[#6A8F3B]' : '' }}">
                                    <span class="text-sm">🇹🇳</span> العربية (AR)
                                </a>
                                <a href="{{ url('fr' . $switchPath) }}" class="flex items-center gap-2 px-4 py-2 text-xs font-bold text-gray-700 hover:bg-[#6A8F3B]/10 hover:text-[#6A8F3B] transition {{ app()->getLocale()==='fr' ? 'bg-gray-50 text-[#6A8F3B]' : '' }}">
                                    <span class="text-sm">🇫🇷</span> Français (FR)
                                </a>
                                <a href="{{ url('en' . $switchPath) }}" class="flex items-center gap-2 px-4 py-2 text-xs font-bold text-gray-700 hover:bg-[#6A8F3B]/10 hover:text-[#6A8F3B] transition {{ app()->getLocale()==='en' ? 'bg-gray-50 text-[#6A8F3B]' : '' }}">
                                    <span class="text-sm">🇬🇧</span> English (EN)
                                </a>
                            </div>
                        </div>

                        @auth
                            <!-- User Menu -->
                            <div class="relative hidden md:block" x-data="{ open: false }">
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
                                     class="absolute {{ __('right-0') }} mt-3 w-60 bg-white/95 backdrop-blur-xl rounded-2xl shadow-2xl shadow-black/20 border border-gray-100/50 py-2 z-[100] overflow-hidden">
                                    
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
                                            <span class="font-medium text-sm">{{ __('Dashboard') }}</span>
                                        </a>
                                        <a href="{{ route('messages.inbox') }}" class="group px-4 py-2.5 text-gray-700 hover:bg-blue-50 transition-all duration-200 flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-blue-100 group-hover:bg-blue-200 flex items-center justify-center transition-all relative">
                                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                                <span x-show="unreadCount > 0" x-cloak class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center" x-text="unreadCount > 9 ? '9+' : unreadCount"></span>
                                            </div>
                                            <span class="font-medium text-sm">{{ __('Inbox') }}</span>
                                            <span x-show="unreadCount > 0" x-cloak class="ml-auto px-2 py-0.5 bg-red-500 text-white text-xs font-bold rounded-full" x-text="unreadCount > 99 ? '99+' : unreadCount"></span>
                                        </a>

                                        <a href="{{ route('profile.edit') }}" class="group px-4 py-2.5 text-gray-700 hover:bg-gray-100 transition-all duration-200 flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-gray-100 group-hover:bg-gray-200 flex items-center justify-center transition-all">
                                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            </div>
                                            <span class="font-medium text-sm">{{ __('Settings') }}</span>
                                        </a>
                                        @if(Auth::user()->role === 'admin')
                                        <a href="{{ route('admin.dashboard') }}" class="group px-4 py-2.5 text-gray-700 hover:bg-amber-50 transition-all duration-200 flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-amber-100 group-hover:bg-amber-200 flex items-center justify-center transition-all">
                                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                                            </div>
                                            <span class="font-medium text-sm">{{ __('Admin Panel') }}</span>
                                        </a>
                                        @endif
                                    </div>
                                    
                                    <div class="border-t border-gray-100 mt-1 pt-1">
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="group w-full text-{{ __('left') }} px-4 py-2.5 text-red-600 hover:bg-red-50 transition-all duration-200 flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-lg bg-red-50 group-hover:bg-red-100 flex items-center justify-center transition-all">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                                                </div>
                                                <span class="font-medium text-sm">{{ __('Logout') }}</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="hidden md:flex items-center gap-2 px-4 py-2 bg-white/95 text-[#6A8F3B] rounded-full hover:bg-white transition-all duration-200 font-semibold shadow-lg shadow-black/10 hover:shadow-xl text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" /></svg>
                                <span>{{ __('Login') }}</span>
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
            </div>
        </div>

                <!-- Mobile Menu Backdrop Overlay -->
                <div x-show="mobileMenuOpen" x-cloak @click="mobileMenuOpen = false" 
                     x-transition:enter="transition-opacity ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition-opacity ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="md:hidden fixed inset-0 bg-black/60 z-[80]"></div>

                <!-- Mobile Menu Sliding Drawer -->
                <div x-show="mobileMenuOpen" x-cloak 
                     @touchstart="touchStartX = $event.touches[0].clientX; touchStartY = $event.touches[0].clientY"
                     @touchend="let diffX = $event.changedTouches[0].clientX - touchStartX; let diffY = Math.abs($event.changedTouches[0].clientY - touchStartY); if (diffX > 50 && diffY < 40) mobileMenuOpen = false"
                     x-transition:enter="transition ease-out duration-300 transform" 
                     x-transition:enter-start="translate-x-full" 
                     x-transition:enter-end="translate-x-0" 
                     x-transition:leave="transition ease-in duration-200 transform" 
                     x-transition:leave-start="translate-x-0" 
                     x-transition:leave-end="translate-x-full" 
                     class="md:hidden fixed top-0 bottom-16 right-0 w-72 bg-[#1B2A1B] shadow-2xl z-[90] flex flex-col justify-between overflow-hidden">
                    
                    <!-- Drawer Content (Scrollable) -->
                    <div class="flex-1 overflow-y-auto flex flex-col gap-1 px-4 py-6 relative z-10 bg-[#1B2A1B]">
                        <a href="{{ route('services.index') }}" class="px-4 py-3 hover:bg-white/15 rounded-xl transition-all duration-200 flex items-center gap-3 font-medium text-white/90 hover:text-white group">
                            <div class="w-9 h-9 rounded-lg bg-white/10 flex items-center justify-center text-white/80 group-hover:text-white">
                                <span class="text-lg">🛠</span>
                            </div>
                            <span>{{ __('Service Hub') }}</span>
                        </a>
                        <a href="{{ route('services.pricing') }}" class="px-4 py-3 hover:bg-white/15 rounded-xl transition-all duration-200 flex items-center gap-3 font-medium text-white/90 hover:text-white group">
                            <div class="w-9 h-9 rounded-lg bg-white/10 flex items-center justify-center text-white/80 group-hover:text-white">
                                <span class="text-lg">📣</span>
                            </div>
                            <span>{{ __('Our Digital Services') }}</span>
                        </a>
                        <a href="{{ route('how-it-works') }}" class="px-4 py-3 hover:bg-white/15 rounded-xl transition-all duration-200 flex items-center gap-3 font-medium text-white/90 hover:text-white group">
                            <div class="w-9 h-9 rounded-lg bg-white/10 flex items-center justify-center text-white/80 group-hover:text-white">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <span>{{ __('How It Works') }}</span>
                        </a>
                        <a href="{{ route('about') }}" class="px-4 py-3 hover:bg-white/15 rounded-xl transition-all duration-200 flex items-center gap-3 font-medium text-white/90 hover:text-white group">
                            <div class="w-9 h-9 rounded-lg bg-white/10 flex items-center justify-center text-white/80 group-hover:text-white">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            </div>
                            <span>{{ __('About') }}</span>
                        </a>
                        @auth
                            <div class="mt-2 pt-2 border-t border-white/20">
                                <a href="{{ route('profile.edit') }}" class="px-4 py-3 hover:bg-white/15 rounded-xl transition-all duration-200 flex items-center gap-3 font-medium text-white/90 hover:text-white group">
                                    <div class="w-9 h-9 rounded-lg bg-white/10 flex items-center justify-center text-white/80 group-hover:text-white">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </div>
                                    <span>{{ __('Settings') }}</span>
                                </a>
                                @if(Auth::user()->role === 'admin')
                                <a href="{{ route('admin.dashboard') }}" class="px-4 py-3 hover:bg-white/15 rounded-xl transition-all duration-200 flex items-center gap-3 font-medium text-white/90 hover:text-white group">
                                    <div class="w-9 h-9 rounded-lg bg-amber-500/20 flex items-center justify-center text-amber-300 group-hover:text-amber-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                                    </div>
                                    <span>{{ __('Admin Panel') }}</span>
                                </a>
                                @endif
                            </div>
                            <div class="mt-2 pt-2 border-t border-white/20">
                                <form method="POST" action="{{ route('logout') }}" class="px-2">
                                    @csrf
                                    <button type="submit" class="w-full px-4 py-3 bg-red-500/20 hover:bg-red-500/30 rounded-xl transition-all duration-200 flex items-center gap-3 text-red-200 hover:text-white font-medium">
                                        <div class="w-9 h-9 rounded-lg bg-red-500/20 flex items-center justify-center text-red-300">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" /></svg>
                                        </div>
                                        <span>{{ __('Logout') }}</span>
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="mt-2 pt-2 border-t border-white/20 px-2 flex flex-col gap-2">
                                <a href="{{ route('register') }}" class="w-full px-4 py-3 bg-[#C8A356] hover:bg-[#b08e45] text-white shadow-lg rounded-xl transition-all duration-200 flex items-center justify-center gap-3 font-bold border border-[#C8A356]/50">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                                    <span>{{ __('Register') }}</span>
                                </a>
                                <a href="{{ route('login') }}" class="w-full px-4 py-3 bg-white/10 hover:bg-white/20 text-white rounded-xl transition-all duration-200 flex items-center justify-center gap-3 font-bold">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" /></svg>
                                    <span>{{ __('Login') }}</span>
                                </a>
                            </div>
                        @endauth

                        <!-- Language Switcher -->
                        <div class="mt-4 px-4 flex items-center justify-center gap-1 bg-white/10 rounded-xl p-1.5 mx-2">
                            @php $mobileSwitchPath = preg_replace('#^/(ar|fr|en)#', '', request()->getPathInfo()) ?: '/'; @endphp
                            <a href="{{ url('ar' . $mobileSwitchPath) }}" class="flex-1 text-center px-3 py-2 {{ __('text-white/80 hover:text-white hover:bg-white/10') }} rounded-lg font-bold text-xs transition-all duration-200">العربية</a>
                            <a href="{{ url('fr' . $mobileSwitchPath) }}" class="flex-1 text-center px-3 py-2 {{ app()->getLocale()==='fr' ? 'bg-white text-[#6A8F3B] shadow-sm' : 'text-white/80 hover:text-white hover:bg-white/10' }} rounded-lg font-bold text-xs transition-all duration-200">Français</a>
                            <a href="{{ url('en' . $mobileSwitchPath) }}" class="flex-1 text-center px-3 py-2 {{ app()->getLocale()==='en' ? 'bg-white text-[#6A8F3B] shadow-sm' : 'text-white/80 hover:text-white hover:bg-white/10' }} rounded-lg font-bold text-xs transition-all duration-200">English</a>
                        </div>
                    </div>

                    <!-- Bottom Close Button (Fixed at bottom of Drawer, never scrolls) -->
                    <div class="p-4 border-t border-white/10 bg-[#1D321D] flex justify-center z-20">
                        <button @click="mobileMenuOpen = false" class="w-12 h-12 bg-white/10 hover:bg-white/20 active:bg-white/30 rounded-full text-white flex items-center justify-center border border-white/10 transition-all duration-200 shadow-lg">
                            <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </nav>

        <!-- Mobile Bottom Navigation Bar -->
        <div class="md:hidden fixed bottom-0 left-0 right-0 z-50 bg-white/95 dark:bg-gray-900/95 backdrop-blur-md border-t border-gray-200/50 dark:border-gray-800/50 shadow-[0_-8px_30px_rgba(0,0,0,0.08)] pb-safe" dir="ltr">
            <div class="flex justify-around items-center h-16 px-2">
                <!-- Home Tab -->
                <a href="{{ route('home') }}" class="flex flex-col items-center justify-center flex-1 py-1 transition-all duration-200 {{ request()->routeIs('home') ? 'text-[#C8A356]' : 'text-[#1B2A1B]/60 dark:text-white/60 hover:text-[#6A8F3B]' }}">
                    <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span class="text-[9px] font-bold tracking-tight">{{ __('Home') }}</span>
                </a>

                <!-- Prices Tab -->
                <a href="{{ route('prices.index') }}" class="flex flex-col items-center justify-center flex-1 py-1 transition-all duration-200 {{ request()->routeIs('prices.index') ? 'text-[#C8A356]' : 'text-[#1B2A1B]/60 dark:text-white/60 hover:text-[#6A8F3B]' }}">
                    <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z" />
                    </svg>
                    <span class="text-[9px] font-bold tracking-tight">{{ __('Prices') }}</span>
                </a>

                <!-- Sell Tab (Middle Highlighted Button) -->
                <div class="flex-1 flex justify-center -mt-6">
                    <a href="{{ route('listings.create') }}" class="w-14 h-14 bg-[#6A8F3B] hover:bg-[#5a7a2f] text-white rounded-full flex flex-col items-center justify-center shadow-lg shadow-[#6A8F3B]/30 hover:scale-105 transition-all duration-200 border-4 border-white dark:border-gray-900">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                    </a>
                </div>

                <!-- Messages Tab -->
                <a href="{{ route('messages.inbox') }}" class="flex flex-col items-center justify-center flex-1 py-1 transition-all duration-200 relative {{ request()->routeIs('messages.inbox') ? 'text-[#C8A356]' : 'text-[#1B2A1B]/60 dark:text-white/60 hover:text-[#6A8F3B]' }}">
                    <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <span class="text-[9px] font-bold tracking-tight">{{ __('Inbox') }}</span>
                    <span x-show="unreadCount > 0" x-cloak class="absolute top-0 right-4 w-4 h-4 bg-red-500 text-white text-[8px] font-bold rounded-full flex items-center justify-center border border-white" x-text="unreadCount > 9 ? '9+' : unreadCount"></span>
                </a>

                <!-- Dashboard/Profile Tab -->
                @auth
                    <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center flex-1 py-1 transition-all duration-200 {{ request()->routeIs('dashboard') ? 'text-[#C8A356]' : 'text-[#1B2A1B]/60 dark:text-white/60 hover:text-[#6A8F3B]' }}">
                        <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span class="text-[9px] font-bold tracking-tight">{{ __('Dashboard') }}</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="flex flex-col items-center justify-center flex-1 py-1 transition-all duration-200 {{ request()->routeIs('login') ? 'text-[#C8A356]' : 'text-[#1B2A1B]/60 dark:text-white/60 hover:text-[#6A8F3B]' }}">
                        <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        <span class="text-[9px] font-bold tracking-tight">{{ __('Login') }}</span>
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Spacer for fixed navbar -->
    <div class="h-14 sm:h-[60px]"></div>

    <!-- Price Ticker - Fixed below navbar -->
    <div class="fixed top-14 sm:top-[60px] left-0 right-0 z-40 shadow-md">
        @include('components.price-ticker')
        @include('components.ads-ticker')
    </div>

    <!-- Spacer for both tickers (price ~32px + ads ~32px) -->
    <div class="h-16"></div>

    @isset($header)
    <header class="bg-white/80 backdrop-blur-lg shadow-sm border-b border-gray-100">
        <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
            {{ $header }}
        </div>
    </header>
    @endisset

    <!-- Main Content -->
    <main class="{{ request()->routeIs('admin.*') ? 'w-full' : 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6' }}">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <!-- Unified Black Footer -->
    <footer class="bg-gray-900 text-white py-6 px-4 mt-8">
        <div class="max-w-7xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-6">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <img src="{{ asset('images/zintoop-logo.png') }}" alt="ZinToop" style="height: 32px; width: 64px;" class="h-8 w-16 rounded-xl object-contain bg-white p-0.5 border border-gray-100 shadow-sm">
                    <h3 class="text-xl font-bold">ZinToop</h3>
                </div>
                <p class="text-gray-400 text-sm">{{ __('Platform connecting producers and buyers') }}</p>
            </div>
            <div>
                <h4 class="font-bold mb-4">{{ __('Quick Links') }}</h4>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="{{ url('/') }}" class="hover:text-white transition">{{ __('Home') }}</a></li>
                    <li><a href="{{ url('/#products') }}" class="hover:text-white transition">{{ __('Products') }}</a></li>
                    <li><a href="{{ route('how-it-works') }}" class="hover:text-white transition">{{ __('nav.how_it_works') }}</a></li>
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
                    @php $footerSwitchPath = preg_replace('#^/(ar|fr|en)#', '', request()->getPathInfo()) ?: '/'; @endphp
                    <a href="{{ url('ar' . $footerSwitchPath) }}" class="px-2 py-1 {{ app()->getLocale()==='ar' ? 'bg-[#6A8F3B] text-white' : 'bg-gray-800 text-gray-300 hover:bg-gray-700' }} rounded text-xs transition-all">العربية</a>
                    <a href="{{ url('fr' . $footerSwitchPath) }}" class="px-2 py-1 {{ app()->getLocale()==='fr' ? 'bg-[#6A8F3B] text-white' : 'bg-gray-800 text-gray-300 hover:bg-gray-700' }} rounded text-xs transition-all">FR</a>
                    <a href="{{ url('en' . $footerSwitchPath) }}" class="px-2 py-1 {{ app()->getLocale()==='en' ? 'bg-[#6A8F3B] text-white' : 'bg-gray-800 text-gray-300 hover:bg-gray-700' }} rounded text-xs transition-all">EN</a>
                </div>
            </div>
        </div>
        <div class="max-w-7xl mx-auto mt-6 pt-6 border-t border-gray-800 text-center text-gray-400 flex flex-col md:flex-row justify-between items-center gap-4 text-sm">
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
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('{{ asset('service-worker.js') }}').catch(() => {});
            });
        }
    </script>
    @include("components.cookie-consent")
    {{-- @include("components.lead-capture") --}}

    {{-- Global Toast Component --}}
    <div x-data="{ 
            init() {
                @if(session('success'))
                    setTimeout(() => { window.showToast('{{ addslashes(session('success')) }}', 'success'); }, 200);
                @endif
                @if(session('error'))
                    setTimeout(() => { window.showToast('{{ addslashes(session('error')) }}', 'error'); }, 200);
                @endif
                @if(session('status'))
                    setTimeout(() => { window.showToast('{{ addslashes(session('status')) }}', 'success'); }, 200);
                @endif
            }
         }"
         x-show="$store.toast.show" 
         x-transition:enter="transition ease-out duration-300 transform" 
         x-transition:enter-start="translate-y-4 opacity-0 sm:translate-y-0 sm:translate-x-4" 
         x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0" 
         x-transition:leave="transition ease-in duration-200" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         class="fixed bottom-5 right-5 z-[9999] max-w-md w-full sm:w-auto p-4" 
         style="display: none;">
        
        <div :class="$store.toast.type === 'success' ? 'bg-[#6A8F3B]' : 'bg-rose-600'" 
             class="flex items-center gap-3 px-5 py-4 rounded-2xl text-white shadow-2xl backdrop-blur-md">
            
            <div class="flex-shrink-0">
                <svg x-show="$store.toast.type === 'success'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <svg x-show="$store.toast.type === 'error'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            
            <div class="flex-1">
                <p class="font-bold text-sm text-right leading-tight" x-text="$store.toast.message"></p>
            </div>
            
            <button @click="$store.toast.show = false" class="p-1 hover:bg-white/10 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

    <!-- Spacer for fixed bottom bar on mobile -->
    <div class="h-16 md:hidden"></div>
</body>
</html>
