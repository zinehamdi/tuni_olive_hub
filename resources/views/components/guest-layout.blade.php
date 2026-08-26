<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ __('ltr') }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

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
