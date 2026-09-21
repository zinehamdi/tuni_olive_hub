@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col md:flex-row" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" x-data="{ sidebarOpen: false, isFullscreen: false }">
    
    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-gray-900/50 z-40 md:hidden" x-transition></div>

    <!-- Sidebar -->
    <aside x-cloak
           x-effect="$el.style.transform = window.innerWidth >= 768 ? '' : (sidebarOpen ? 'translateX(0)' : '{{ __('translateX(-100%)') }}')"
           class="fixed md:sticky top-0 md:top-[72px] bottom-0 md:h-[calc(100vh-72px)] w-72 bg-white shadow-2xl md:shadow-lg z-50 md:z-10 flex flex-col transition-transform duration-300 ltr:left-0 rtl:right-0">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <span class="text-[#6A8F3B]">🛡️</span> {{ __('Admin Panel') }}
            </h2>
            <button @click="sidebarOpen = false" class="md:hidden text-gray-500 hover:text-gray-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <nav class="flex-1 p-4 space-y-2 overflow-y-auto scrollbar-hide">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-[#6A8F3B] rounded-xl font-bold transition group">
                <span class="text-xl group-hover:scale-110 transition">📊</span> {{ __('Dashboard') }}
            </a>
            <a href="{{ route('admin.social_studio') }}" class="flex items-center gap-3 px-4 py-3 bg-[#6A8F3B]/10 text-[#6A8F3B] rounded-xl font-bold transition">
                <span class="text-xl">🎨</span> {{ app()->getLocale() === 'ar' ? 'استوديو التصميم والنشر' : (app()->getLocale() === 'fr' ? 'Studio Graphique' : 'Visual Brand Studio') }}
            </a>
            <a href="{{ route('admin.analytics.visitors') }}" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-[#6A8F3B] rounded-xl font-bold transition group">
                <span class="text-xl group-hover:scale-110 transition">🌍</span> {{ __('Visitor Analytics') }}
            </a>
            <a href="{{ route('admin.analytics.marketing') }}" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-[#6A8F3B] rounded-xl font-bold transition group">
                <span class="text-xl group-hover:scale-110 transition">📈</span> {{ app()->getLocale() === 'ar' ? 'تحليلات التسويق' : __('Marketing Analytics') }}
            </a>
            <a href="{{ route('admin.bot.index') }}" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-[#6A8F3B] rounded-xl font-bold transition group">
                <span class="text-xl group-hover:scale-110 transition">🤖</span> {{ app()->getLocale() === 'ar' ? 'أتمتة الزيتوني' : 'Ezzitouni Bot' }}
            </a>
            <a href="{{ route('admin.users') }}" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-[#6A8F3B] rounded-xl font-bold transition group">
                <span class="text-xl group-hover:scale-110 transition">👥</span> {{ __('Manage Users') }}
            </a>
            <a href="{{ route('admin.listings') }}" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-[#6A8F3B] rounded-xl font-bold transition group">
                <span class="text-xl group-hover:scale-110 transition">🏷️</span> {{ __('Manage Listings') }}
            </a>
            <a href="{{ route('admin.prices.souk.index') }}" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-[#6A8F3B] rounded-xl font-bold transition group">
                <span class="text-xl group-hover:scale-110 transition">🫒</span> {{ __('Souk Prices') }}
            </a>
            <a href="{{ route('admin.prices.world.index') }}" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-[#6A8F3B] rounded-xl font-bold transition group">
                <span class="text-xl group-hover:scale-110 transition">🌍</span> {{ __('World Prices') }}
            </a>
            <a href="{{ route('admin.articles.index') }}" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-[#6A8F3B] rounded-xl font-bold transition group">
                <span class="text-xl group-hover:scale-110 transition">📰</span> {{ __('Articles') }}
            </a>
            <a href="{{ route('admin.deals.index') }}" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-[#6A8F3B] rounded-xl font-bold transition group">
                <span class="text-xl group-hover:scale-110 transition">🤝</span> {{ __('Deals') }}
            </a>
            <a href="{{ route('admin.deals.requests.index') }}" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-[#6A8F3B] rounded-xl font-bold transition group">
                <span class="text-xl group-hover:scale-110 transition">📩</span> {{ __('Deal Requests') }}
            </a>
            <a href="{{ route('admin.subscribers.index') }}" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-[#6A8F3B] rounded-xl font-bold transition group">
                <span class="text-xl group-hover:scale-110 transition">📧</span> {{ __('Subscribers') }}
            </a>
            <a href="{{ route('admin.hero-slides.index') }}" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-[#6A8F3B] rounded-xl font-bold transition group">
                <span class="text-xl group-hover:scale-110 transition">🖼️</span> {{ __('Hero Slides') }}
            </a>
        </nav>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 w-full p-4 sm:p-6 min-w-0 flex flex-col">
        <!-- Header -->
        <div class="mb-4 flex flex-wrap items-center justify-between gap-4 bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 text-xs font-black rounded-lg">PRO STUDIO</span>
                    <span class="text-xs text-gray-500 font-semibold">ZinToop Visual Suite v2.5</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-gray-900 flex items-center gap-2">
                    <span>🎨</span> {{ app()->getLocale() === 'ar' ? 'استوديو ZinToop البصري الموحد' : (app()->getLocale() === 'fr' ? 'Studio Graphique ZinToop' : 'ZinToop Visual Brand Studio') }}
                </h1>
                <p class="text-sm text-gray-600 font-medium">
                    {{ app()->getLocale() === 'ar' ? 'توليد وتصميم إعلانات البورصة والسوشيال ميديا بجودة فائقة 2K مع دعم الصوت و3 لغات' : 'Generate & export HD 2K social media graphics with audio video and 3 languages.' }}
                </p>
            </div>
            
            <div class="flex items-center gap-2">
                <a href="{{ asset('social_templates_zintoop.html') }}?v={{ time() }}" target="_blank" class="px-4 py-2.5 bg-[#6A8F3B] hover:bg-[#5a7a2f] text-white rounded-xl font-bold text-sm flex items-center gap-2 shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <span>{{ app()->getLocale() === 'ar' ? 'فتح في نافذة كاملة' : (app()->getLocale() === 'fr' ? 'Plein Écran Dédié' : 'Open in New Tab') }}</span>
                </a>
                <button @click="sidebarOpen = true" class="md:hidden p-2.5 bg-gray-100 rounded-xl text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>

        <!-- Studio Embedded Frame Container -->
        <div class="flex-1 bg-[#080c14] rounded-2xl shadow-xl border border-gray-800 overflow-hidden relative min-h-[750px] lg:min-h-[860px]">
            <iframe 
                id="studioIframe"
                src="{{ asset('social_templates_zintoop.html') }}?v={{ time() }}" 
                class="w-full h-full border-0 absolute inset-0"
                allow="accelerometer; autoplay; camera; microphone; clipboard-write; clipboard-read; encrypted-media; gyroscope; picture-in-picture; web-share"
                title="ZinToop Brand Studio">
            </iframe>
        </div>
    </main>
</div>
@endsection
