<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('head')
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gradient-to-br from-gray-50 to-gray-100" style="font-family: 'Segoe UI', 'Tahoma', 'Geneva', 'Verdana', 'Arial', sans-serif;">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 px-4">
            <!-- Logo -->
            <div class="mb-8">
                <a href="/" class="flex flex-col items-center gap-3">
                    <img src="{{ asset('images/zintoop-logo.png') }}" alt="{{ __(app()->getLocale() === 'ar' ? 'brand.name_ar' : 'brand.name_latin') }}" class="h-16 w-16 sm:h-20 sm:w-20 rounded-full object-cover shadow-lg hover:scale-105 transition">
                    <div class="text-center">
                        <h1 class="text-2xl font-bold text-gray-900">منصة زيت الزيتون التونسي</h1>
                        <p class="text-sm text-gray-600">Tunisian Olive Oil Platform</p>
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
