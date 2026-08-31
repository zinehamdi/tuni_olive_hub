<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @php
            $locale = app()->getLocale();
            $isRegister = request()->is('*register*');
            $isLogin = request()->is('*login*');

            if ($isRegister) {
                $defaultGuestTitle = match($locale) {
                    'ar' => 'زين توب | التسجيل وإنشاء حساب مجاني',
                    'fr' => 'ZinToop | Inscription Gratuite & Création de Compte',
                    default => 'ZinToop | Free Registration & Account Creation',
                };
                $defaultGuestDesc = match($locale) {
                    'ar' => 'سجل الآن مجاناً في منصة زين توب لتسويق وشراء زيت الزيتون التونسي والتواصل المباشر مع الفلاحين وأصحاب المعاصر والمشترين بدون وسيط.',
                    'fr' => 'Inscrivez-vous gratuitement sur ZinToop pour acheter ou vendre de l\'huile d\'olive tunisienne et contacter directement les producteurs sans intermédiaire.',
                    default => 'Join ZinToop for free to buy and sell Tunisian extra virgin olive oil directly with certified farmers and mills without broker fees.',
                };
            } elseif ($isLogin) {
                $defaultGuestTitle = match($locale) {
                    'ar' => 'زين توب | تسجيل الدخول',
                    'fr' => 'ZinToop | Connexion',
                    default => 'ZinToop | Member Login',
                };
                $defaultGuestDesc = match($locale) {
                    'ar' => 'سجل دخولك إلى حسابك في منصة زين توب لإدارة عروض زيت الزيتون وطلبات الشراء والتواصل المباشر.',
                    'fr' => 'Connectez-vous à votre compte ZinToop pour gérer vos annonces, commandes et contacts directs.',
                    default => 'Log in to your ZinToop account to manage your listings, purchase inquiries, and direct communication.',
                };
            } else {
                $defaultGuestTitle = match($locale) {
                    'ar' => 'زين توب | السوق التونسي الأول لزيت الزيتون',
                    'fr' => 'ZinToop | 1ère Marketplace d\'Huile d\'Olive en Tunisie',
                    default => 'ZinToop | Leading Tunisian Olive Oil Marketplace',
                };
                $defaultGuestDesc = match($locale) {
                    'ar' => 'زين توب - المنصة الأولى لزيت الزيتون والدليل التجاري في تونس. تواصل مباشرة مع الفلاحين والمعاصر بدون عمولات.',
                    'fr' => 'ZinToop - Première plateforme d\'huile d\'olive et annuaire B2B en Tunisie.',
                    default => 'ZinToop - Premier Tunisian Olive Oil Marketplace & B2B Directory.',
                };
            }

            $pageTitle = $title ?? $defaultGuestTitle;
            $pageDesc = $description ?? $defaultGuestDesc;
            $defaultOgImg = $isRegister ? asset('images/zintoop-register-card.png') : asset('images/zintoop-logo.png');
            $ogImg = $ogImage ?? $defaultOgImg;
        @endphp

        <title>{{ $pageTitle }}</title>
        <meta name="description" content="{{ $pageDesc }}">
        <meta name="facebook-domain-verification" content="8b9o5r7q1jz9762hqdi15atqy5iwae" />

        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="ZinToop">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:title" content="{{ $pageTitle }}">
        <meta property="og:description" content="{{ $pageDesc }}">
        <meta property="og:image" content="{{ $ogImg }}">
        <meta property="og:image:secure_url" content="{{ $ogImg }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:image:alt" content="ZinToop Tunisian Olive Oil Platform">
        <meta property="og:locale" content="{{ $locale === 'ar' ? 'ar_TN' : ($locale === 'fr' ? 'fr_FR' : 'en_US') }}">
        <meta property="fb:app_id" content="2280950462734613">

        <!-- Twitter Card -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:url" content="{{ url()->current() }}">
        <meta name="twitter:title" content="{{ $pageTitle }}">
        <meta name="twitter:description" content="{{ $pageDesc }}">
        <meta name="twitter:image" content="{{ $ogImg }}">

        <!-- Canonical URL -->
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
    <body class="font-sans text-gray-900 antialiased bg-gradient-to-br from-gray-50 to-gray-100" style="font-family: 'Segoe UI', 'Tahoma', 'Geneva', 'Verdana', 'Arial', sans-serif;">
        <x-guest-navbar />
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-24 sm:pt-28 pb-8 px-4">
            <!-- Logo -->
            <div class="mb-8">
                <a href="/" class="flex flex-col items-center gap-3">
                    <img src="{{ asset('images/zintoop-logo.png') }}" alt="{{ __(__('brand.name_latin')) }}" style="height: 128px; width: 128px;" class="rounded-full object-contain bg-white p-3 shadow-lg hover:scale-105 transition border border-gray-100 h-24 w-24 sm:h-32 sm:w-32">
                    <div class="text-center">
                        <h1 class="text-2xl font-bold text-gray-900">منصة زيت الزيتون التونسي</h1>
                        <h2 class="text-2xl font-bold text-gray-900">Tunisian Olive Oil Platform</h2>
                    </div>
                </a>
            </div>

            <!-- Content -->
            <div class="w-full">
                {{ $slot }}
            </div>

            <!-- Footer -->
            <div class="mt-8 text-center text-sm text-gray-500">
                <p>© 2025 منصة زيت الزيتون التونسي</p>
            </div>
        </div>
    </body>
</html>
