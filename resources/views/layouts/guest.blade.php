<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- SEO Meta Tags -->
        @php
            $locale = app()->getLocale();
            $defaultGuestTitle = match($locale) {
                'ar' => 'زين توب | الدخول والتسجيل',
                'fr' => 'ZinToop | Connexion et Inscription',
                default => 'ZinToop | Login & Registration',
            };
            $defaultGuestDesc = match($locale) {
                'ar' => 'سجل دخولك أو أنشئ حسابك المجاني في منصة زين توب للتواصل المباشر مع الفلاحين والمعاصر في تونس.',
                'fr' => 'Connectez-vous ou créez votre compte gratuit sur ZinToop pour contacter directement les producteurs en Tunisie.',
                default => 'Log in or create your free ZinToop account to connect directly with olive oil producers and mills in Tunisia.',
            };
            $defaultGuestKeywords = match($locale) {
                'ar' => 'الشملالي, الشتوي, سلالات الزيتون في تونس, أنواع الزيتون التونسي, أحسن أنواع زيت الزيتون في العالم, أحسن سلالات الزيتون في العالم, أكبر سوق زيت زيتون, أسعار زيت الزيتون في تونس, أسعار زيت الزيتون في العالم, أسهل طريقة لترويج وشراء زيت الزيتون, زيت الزيتون التونسي, سوق زيت الزيتون, زين توب',
                'fr' => 'variétés d\'olives tunisiennes, chemlali, chetoui, olives en tunisie, meilleures huiles d\'olive au monde, meilleures variétés d\'olivier, le plus grand marché d\'huile d\'olive, prix d\'huile d\'olive en tunisie, prix mondial d\'huile d\'olive, moyen le plus facile pour vendre et acheter l\'huile d\'olive, huile d\'olive tunisie, zintoop',
                default => 'chemlali, chetoui, tunisian olive varieties, varieties of olives in tunisia, best olive oil varieties in the world, best olive tree varieties, largest olive oil marketplace, olive oil prices in tunisia, global olive oil prices, easiest way to buy and sell olive oil, tunisian olive oil, zintoop',
            };
        @endphp
        <title>@yield('title', $defaultGuestTitle . ' - ' . config('app.name'))</title>
        <meta name="description" content="@yield('description', $defaultGuestDesc)">
        <meta name="keywords" content="@yield('keywords', $defaultGuestKeywords)">
        <meta name="author" content="{{ $locale === 'ar' ? 'زين توب' : 'ZinToop' }}">
        <meta name="robots" content="index, follow">
        
        <!-- Open Graph Meta Tags -->
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="{{ config('app.name') }}">
        <meta property="og:title" content="@yield('og_title', $defaultGuestTitle)">
        <meta property="og:description" content="@yield('og_description', $defaultGuestDesc)">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:image" content="@yield('og_image', asset('images/zintoop-logo.png'))">
        <meta property="og:locale" content="{{ $locale === 'ar' ? 'ar_TN' : ($locale === 'fr' ? 'fr_FR' : 'en_US') }}">
        
        <!-- Twitter Card Meta Tags -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="@yield('twitter_title', $defaultGuestTitle)">
        <meta name="twitter:description" content="@yield('twitter_description', $defaultGuestDesc)">
        <meta name="twitter:image" content="@yield('twitter_image', asset('images/zintoop-logo.png'))">
        
        <!-- Alternate Language Links -->
        <link rel="alternate" hreflang="ar" href="{{ url()->current() }}?lang=ar">
        <link rel="alternate" hreflang="fr" href="{{ url()->current() }}?lang=fr">
        <link rel="alternate" hreflang="en" href="{{ url()->current() }}?lang=en">
        <link rel="alternate" hreflang="x-default" href="{{ url()->current() }}">
        
        <!-- Canonical URL — always the clean base URL (hreflang handles language variants) -->
        <link rel="canonical" href="{{ url()->current() }}">
        
        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('images/zintoop-logo.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('images/zintoop-logo.png') }}">

        <!-- Scripts -->
        @if(app()->environment('production') || file_exists(public_path('build/manifest.json')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
        @stack('head')
    </head>
    <body class="font-sans text-gray-900 antialiased" style="font-family: 'Segoe UI', 'Tahoma', 'Geneva', 'Verdana', 'Arial', sans-serif;">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div>
                <a href="/">
                    <x-application-logo class="h-16 w-auto fill-current text-gray-500" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                @yield('content')
            </div>
        </div>
        @include("components.cookie-consent")
</body>
</html>
