<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- SEO Meta Tags -->
        <title>@yield('title', __('Login') . ' - ' . config('app.name'))</title>
        <meta name="description" content="@yield('description', __('Join Tunisia\'s leading olive oil marketplace. Connect with farmers, mills, packers, and buyers. Discover authentic Tunisian olive oil products.'))">
        <meta name="keywords" content="@yield('keywords', 'olive oil login, tunisia marketplace, زيت الزيتون, تسجيل الدخول, huile d\'olive connexion')">
        <meta name="author" content="Tunisian Olive Oil Platform">
        <meta name="robots" content="index, follow">
        
        <!-- Open Graph Meta Tags -->
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="{{ config('app.name') }}">
        <meta property="og:title" content="@yield('og_title', __('Join TOOP - Tunisian Olive Oil Platform'))">
        <meta property="og:description" content="@yield('og_description', __('Connect with Tunisia\'s olive industry. Buy and sell premium olive oil and olives directly from producers.'))">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:image" content="@yield('og_image', asset('images/logotoop.PNG'))">
        <meta property="og:locale" content="{{ app()->getLocale() === 'ar' ? 'ar_TN' : (app()->getLocale() === 'fr' ? 'fr_FR' : 'en_US') }}">
        
        <!-- Twitter Card Meta Tags -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="@yield('twitter_title', __('Join TOOP - Tunisian Olive Oil Platform'))">
        <meta name="twitter:description" content="@yield('twitter_description', __('Connect with Tunisia\'s olive industry'))">
        <meta name="twitter:image" content="@yield('twitter_image', asset('images/logotoop.PNG'))">
        
        <!-- Alternate Language Links -->
        <link rel="alternate" hreflang="ar" href="{{ url()->current() }}?lang=ar">
        <link rel="alternate" hreflang="fr" href="{{ url()->current() }}?lang=fr">
        <link rel="alternate" hreflang="en" href="{{ url()->current() }}?lang=en">
        <link rel="alternate" hreflang="x-default" href="{{ url()->current() }}">
        
        <!-- Canonical URL -->
        <link rel="canonical" href="{{ url()->current() }}">
        
        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('images/logotoop.PNG') }}">
        <link rel="apple-touch-icon" href="{{ asset('images/logotoop.PNG') }}">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('head')
    </head>
    <body class="font-sans text-gray-900 antialiased" style="font-family: 'Segoe UI', 'Tahoma', 'Geneva', 'Verdana', 'Arial', sans-serif;">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                @yield('content')
            </div>
        </div>
    </body>
</html>
