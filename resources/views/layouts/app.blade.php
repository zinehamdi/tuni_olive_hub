<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <style nonce="{{ $cspNonce ?? '' }}">
        :root{--olive:#6b8e23;--gold:#b8860b;--sky:#38bdf8;--pepper:#b91c1c}
        .text-olive{color:var(--olive)} .bg-olive{background:var(--olive)}
        .text-gold{color:var(--gold)} .bg-gold{background:var(--gold)}
        .text-sky{color:var(--sky)} .bg-sky{background:var(--sky)}
        .text-pepper{color:var(--pepper)} .bg-pepper{background:var(--pepper)}
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('head')
    <script nonce="{{ $cspNonce ?? '' }}">
        document.documentElement.dir = '{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}';
    </script>
</head>
<body class="min-h-screen bg-white text-gray-900">
    <header class="border-b bg-white">
        <div class="max-w-6xl mx-auto px-4 py-3 flex items-center {{ app()->getLocale()==='ar' ? 'flex-row-reverse' : '' }} justify-between">
            <a href="{{ route('home') }}" class="font-semibold text-xl text-olive">زيت تونس</a>
            <nav class="flex items-center gap-3">
                <a href="{{ route('home') }}" class="text-sm">الرئيسية</a>
                <div class="inline-flex rounded border overflow-hidden">
                    <a class="px-2 py-1 text-sm {{ app()->getLocale()==='ar' ? 'bg-olive text-white' : '' }}" href="{{ route('lang.switch','ar') }}">AR</a>
                    <a class="px-2 py-1 text-sm {{ app()->getLocale()==='fr' ? 'bg-olive text-white' : '' }}" href="{{ route('lang.switch','fr') }}">FR</a>
                    <a class="px-2 py-1 text-sm {{ app()->getLocale()==='en' ? 'bg-olive text-white' : '' }}" href="{{ route('lang.switch','en') }}">EN</a>
                </div>
                @auth
                    <a href="{{ route('dashboard') }}" class="text-sm">لوحة التحكم</a>
                @else
                    <a href="{{ route('login') }}" class="text-sm">دخول</a>
                @endauth
            </nav>
        </div>
    </header>
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
