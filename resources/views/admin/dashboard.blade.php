@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col md:flex-row" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" x-data="{ sidebarOpen: false }">
    
    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-gray-900/50 z-40 md:hidden" x-transition></div>

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : (document.documentElement.dir === 'rtl' ? 'translate-x-full' : '-translate-x-full')" 
           class="fixed md:sticky top-0 md:top-[72px] bottom-0 md:h-[calc(100vh-72px)] w-72 bg-white shadow-2xl md:shadow-lg z-50 md:z-10 flex flex-col transition-transform duration-300 md:translate-x-0">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <span class="text-[#6A8F3B]">🛡️</span> {{ __('Admin Panel') }}
            </h2>
            <button @click="sidebarOpen = false" class="md:hidden text-gray-500 hover:text-gray-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <nav class="flex-1 p-4 space-y-2 overflow-y-auto scrollbar-hide">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 bg-[#6A8F3B]/10 text-[#6A8F3B] rounded-xl font-bold transition">
                <span class="text-xl">📊</span> {{ __('Dashboard') }}
            </a>
            <a href="{{ route('admin.analytics.visitors') }}" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-[#6A8F3B] rounded-xl font-bold transition group">
                <span class="text-xl group-hover:scale-110 transition">🌍</span> {{ __('Visitor Analytics') }}
            </a>
            <a href="{{ route('admin.analytics.marketing') }}" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-[#6A8F3B] rounded-xl font-bold transition group">
                <span class="text-xl group-hover:scale-110 transition">📈</span> {{ app()->getLocale() === 'ar' ? 'تحليلات التسويق' : __('Marketing Analytics') }}
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
            <a href="{{ route('admin.subscribers.index') }}" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-[#6A8F3B] rounded-xl font-bold transition group">
                <span class="text-xl group-hover:scale-110 transition">📧</span> {{ __('Subscribers') }}
            </a>

            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 w-full p-4 sm:p-8 min-w-0">
        <!-- Header -->
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl sm:text-4xl font-black text-gray-900 mb-2">{{ __('Admin Dashboard') }}</h1>
                <p class="text-gray-600 font-medium">{{ __('Platform moderation and statistics') }}</p>
            </div>
            <button @click="sidebarOpen = true" class="md:hidden p-3 bg-white rounded-xl shadow-md text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6 mb-8">
            <!-- Visitors -->
            <a href="{{ route('admin.analytics.visitors') }}" class="block group">
                <div class="bg-gradient-to-br from-purple-500 to-fuchsia-600 rounded-[2rem] p-6 text-white shadow-lg hover:shadow-xl transition-all hover:scale-105 relative overflow-hidden h-full">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-white/10 rounded-full blur-xl group-hover:bg-white/20 transition-all"></div>
                    <div class="flex items-center justify-between mb-4 relative z-10">
                        <h3 class="text-lg font-semibold">{{ __('Visitors') }}</h3>
                        <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        </div>
                    </div>
                    <div class="text-4xl font-bold relative z-10">{{ number_format($stats['today_visitors'] ?? 0) }}</div>
                    <div class="text-sm mt-2 opacity-90 relative z-10 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        {{ number_format($stats['total_visitors'] ?? 0) }} {{ __('Total') }}
                    </div>
                </div>
            </a>

            <!-- Total Users -->
            <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-[2rem] p-6 text-white shadow-lg hover:shadow-xl transition-shadow relative overflow-hidden">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
                <div class="flex items-center justify-between mb-4 relative z-10">
                    <h3 class="text-lg font-semibold">{{ __('Total Users') }}</h3>
                    <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    </div>
                </div>
                <div class="text-4xl font-bold relative z-10">{{ number_format($stats['total_users']) }}</div>
                <div class="text-sm mt-2 opacity-90 relative z-10 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    +{{ $stats['new_users_week'] }} {{ __('this week') }}
                </div>
            </div>

            <!-- Total Listings -->
            <div class="bg-gradient-to-br from-[#6A8F3B] to-[#5a7a2f] rounded-[2rem] p-6 text-white shadow-lg hover:shadow-xl transition-shadow relative overflow-hidden">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
                <div class="flex items-center justify-between mb-4 relative z-10">
                    <h3 class="text-lg font-semibold">{{ __('Total Listings') }}</h3>
                    <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                    </div>
                </div>
                <div class="text-4xl font-bold relative z-10">{{ number_format($stats['total_listings']) }}</div>
                <div class="text-sm mt-2 opacity-90 relative z-10 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    +{{ $stats['new_listings_week'] }} {{ __('this week') }}
                </div>
            </div>

            <!-- Active Listings -->
            <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-[2rem] p-6 text-white shadow-lg hover:shadow-xl transition-shadow relative overflow-hidden">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
                <div class="flex items-center justify-between mb-4 relative z-10">
                    <h3 class="text-lg font-semibold">{{ __('Active Listings') }}</h3>
                    <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                </div>
                <div class="text-4xl font-bold relative z-10">{{ number_format($stats['active_listings']) }}</div>
                <div class="text-sm mt-2 opacity-90 relative z-10 flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                    {{ __('Published') }}
                </div>
            </div>

            <!-- Pending Listings -->
            <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-[2rem] p-6 text-white shadow-lg hover:shadow-xl transition-shadow relative overflow-hidden">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
                <div class="flex items-center justify-between mb-4 relative z-10">
                    <h3 class="text-lg font-semibold">{{ __('Pending Listings') }}</h3>
                    <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                </div>
                <div class="text-4xl font-bold relative z-10">{{ number_format($stats['pending_listings']) }}</div>
                <div class="text-sm mt-2 opacity-90 relative z-10 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    {{ __('Awaiting approval') }}
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8 mb-8">
            <!-- Users by Role -->
            <div class="bg-white rounded-[2rem] shadow-xl border border-gray-100 p-6 sm:p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <span class="text-2xl">📊</span> {{ __('Users by Role') }}
                </h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <div class="text-center p-4 bg-green-50 rounded-2xl border border-green-100 hover:shadow-md transition">
                        <div class="text-3xl font-black text-green-600">{{ $stats['farmers'] }}</div>
                        <div class="text-sm font-bold text-gray-600 mt-1">{{ __('Farmers') }}</div>
                    </div>
                    <div class="text-center p-4 bg-blue-50 rounded-2xl border border-blue-100 hover:shadow-md transition">
                        <div class="text-3xl font-black text-blue-600">{{ $stats['carriers'] }}</div>
                        <div class="text-sm font-bold text-gray-600 mt-1">{{ __('Carriers') }}</div>
                    </div>
                    <div class="text-center p-4 bg-amber-50 rounded-2xl border border-amber-100 hover:shadow-md transition">
                        <div class="text-3xl font-black text-amber-600">{{ $stats['mills'] }}</div>
                        <div class="text-sm font-bold text-gray-600 mt-1">{{ __('Mills') }}</div>
                    </div>
                    <div class="text-center p-4 bg-purple-50 rounded-2xl border border-purple-100 hover:shadow-md transition">
                        <div class="text-3xl font-black text-purple-600">{{ $stats['packers'] }}</div>
                        <div class="text-sm font-bold text-gray-600 mt-1">{{ __('Packers') }}</div>
                    </div>
                    <div class="text-center p-4 bg-gray-50 rounded-2xl border border-gray-200 hover:shadow-md transition sm:col-span-2">
                        <div class="text-3xl font-black text-gray-600">{{ $stats['normal_users'] }}</div>
                        <div class="text-sm font-bold text-gray-600 mt-1">{{ __('Normal Users') }}</div>
                    </div>
                </div>
            </div>

            <!-- Marketing Analytics -->
            <div class="bg-white rounded-[2rem] shadow-xl border border-gray-100 p-6 sm:p-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                        <span class="text-2xl">📈</span> {{ app()->getLocale() === 'ar' ? 'إحصائيات التسويق' : __('Marketing Analytics') }}
                    </h2>
                    <a href="{{ route('admin.analytics.marketing') }}" class="text-xs font-bold text-[#6A8F3B] hover:underline flex items-center gap-1">
                        {{ app()->getLocale() === 'ar' ? 'عرض التفاصيل ←' : __('View Details →') }}
                    </a>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center p-4 bg-emerald-50 rounded-2xl border border-emerald-100 hover:shadow-md transition">
                        <div class="text-3xl font-black text-emerald-600">{{ $stats['marketing_purchases'] }}</div>
                        <div class="text-sm font-bold text-gray-600 mt-1">{{ app()->getLocale() === 'ar' ? 'المبيعات' : __('Purchases') }}</div>
                    </div>
                    <div class="text-center p-4 bg-indigo-50 rounded-2xl border border-indigo-100 hover:shadow-md transition">
                        <div class="text-3xl font-black text-indigo-600">{{ $stats['marketing_checkouts'] }}</div>
                        <div class="text-sm font-bold text-gray-600 mt-1">{{ app()->getLocale() === 'ar' ? 'بدء الشراء' : __('Checkouts') }}</div>
                    </div>
                    <div class="text-center p-4 bg-pink-50 rounded-2xl border border-pink-100 hover:shadow-md transition">
                        <div class="text-3xl font-black text-pink-600">{{ $stats['marketing_add_to_cart'] }}</div>
                        <div class="text-sm font-bold text-gray-600 mt-1">{{ app()->getLocale() === 'ar' ? 'إضافة للسلة' : __('Cart Adds') }}</div>
                    </div>
                    <div class="text-center p-4 bg-gradient-to-br from-[#C8A356]/20 to-[#A88932]/20 rounded-2xl border border-[#C8A356]/30 hover:shadow-md transition">
                        <div class="text-2xl font-black text-[#A88932]">{{ number_format($stats['marketing_revenue']) }} <span class="text-sm font-bold">TND</span></div>
                        <div class="text-sm font-bold text-gray-700 mt-1">{{ app()->getLocale() === 'ar' ? 'الإيرادات' : __('Revenue') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Items Section -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8 mb-8">
            <!-- Pending Listings -->
            <div class="bg-white rounded-[2rem] shadow-xl border border-gray-100 p-6 sm:p-8 flex flex-col">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                        <span class="text-2xl">⏳</span> {{ __('Pending Listings') }}
                        @if($pendingListings->count() > 0)
                            <span class="bg-orange-100 text-orange-600 px-3 py-1 rounded-full text-sm">{{ $pendingListings->count() }}</span>
                        @endif
                    </h2>
                    <a href="{{ route('admin.listings', ['status' => 'pending']) }}" class="text-[#6A8F3B] hover:text-[#5a7a2f] font-bold text-sm bg-[#6A8F3B]/10 px-4 py-2 rounded-xl transition hover:bg-[#6A8F3B]/20">
                        {{ __('View All') }}
                    </a>
                </div>
                
                @if($pendingListings->count() > 0)
                <div class="space-y-4 flex-1 overflow-y-auto max-h-96 pr-2 scrollbar-hide">
                    @foreach($pendingListings as $listing)
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100 hover:border-[#6A8F3B]/30 transition gap-4">
                        <div class="flex items-center gap-4 flex-1">
                            <div class="w-12 h-12 bg-gradient-to-br from-[#6A8F3B] to-[#C8A356] rounded-xl flex items-center justify-center shrink-0">
                                <span class="text-white font-bold">{{ mb_substr($listing->seller->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900">{{ __($listing->product->variety) }}</h3>
                                <p class="text-sm text-gray-600 font-medium">{{ $listing->seller->name }} • {{ $listing->product->price }} TND/kg</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <form action="{{ route('admin.listings.approve', $listing) }}" method="POST">
                                @csrf
                                <button type="submit" class="p-2 bg-green-100 text-green-700 hover:bg-green-500 hover:text-white rounded-xl transition" title="{{ __('Approve') }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                </button>
                            </form>
                            <form action="{{ route('admin.listings.reject', $listing) }}" method="POST">
                                @csrf
                                <button type="submit" class="p-2 bg-red-100 text-red-700 hover:bg-red-500 hover:text-white rounded-xl transition" title="{{ __('Reject') }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="flex-1 flex flex-col items-center justify-center text-gray-400 py-8">
                    <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <p class="font-bold">{{ __('No pending listings') }}</p>
                </div>
                @endif
            </div>

            <!-- Pending Marketing Appointments -->
            <div class="bg-white rounded-[2rem] shadow-xl border border-gray-100 p-6 sm:p-8 flex flex-col">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                        <span class="text-2xl">📅</span> {{ app()->getLocale() === 'ar' ? 'مواعيد التسويق' : __('Appointments') }}
                        @if(isset($pendingAppointments) && $pendingAppointments->count() > 0)
                            <span class="bg-blue-100 text-blue-600 px-3 py-1 rounded-full text-sm">{{ $pendingAppointments->count() }}</span>
                        @endif
                    </h2>
                    <a href="{{ route('admin.marketing.index') }}" class="text-[#6A8F3B] hover:text-[#5a7a2f] font-bold text-sm bg-[#6A8F3B]/10 px-4 py-2 rounded-xl transition hover:bg-[#6A8F3B]/20">
                        {{ __('Manage All') }}
                    </a>
                </div>
                
                @if(isset($pendingAppointments) && $pendingAppointments->count() > 0)
                <div class="space-y-4 flex-1 overflow-y-auto max-h-96 pr-2 scrollbar-hide">
                    @foreach($pendingAppointments as $appointment)
                    <div class="p-4 bg-blue-50/50 rounded-2xl border border-blue-100 hover:border-blue-300 transition">
                        <div class="flex items-start justify-between gap-4 mb-2">
                            <div>
                                <h3 class="font-bold text-gray-900">{{ $appointment->name }}</h3>
                                <p class="text-xs text-blue-600 font-bold mt-1">{{ $appointment->phone }}</p>
                            </div>
                            <span class="px-2.5 py-1 bg-orange-100 text-orange-700 text-[10px] font-black uppercase rounded-lg">
                                {{ app()->getLocale() === 'ar' ? 'معلق' : 'Pending' }}
                            </span>
                        </div>
                        <div class="text-sm text-gray-700 font-medium mb-3 line-clamp-1">{{ $appointment->business_info }}</div>
                        <div class="flex items-center justify-between text-xs pt-3 border-t border-blue-100/50">
                            <div class="font-semibold text-gray-500">{{ $appointment->appointment_date->format('M d, H:i') }}</div>
                            <div class="font-black text-[#6A8F3B]">{{ number_format($appointment->total_budget) }} TND</div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="flex-1 flex flex-col items-center justify-center text-gray-400 py-8">
                    <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    <p class="font-bold">{{ app()->getLocale() === 'ar' ? 'لا توجد مواعيد معلقة' : 'No pending appointments' }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="grid md:grid-cols-2 gap-8">
            <!-- Recent Users -->
            <div class="bg-white rounded-[2rem] shadow-xl border border-gray-100 p-6 sm:p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <span class="text-2xl">🔥</span> {{ __('Recent Users') }}
                </h2>
                <div class="space-y-3">
                    @foreach($recentUsers->take(5) as $user)
                    <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-2xl hover:bg-white hover:shadow-md transition border border-transparent hover:border-gray-100">
                        <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-xl flex items-center justify-center text-white font-bold shadow-sm">
                            {{ mb_substr($user->name, 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-bold text-gray-900 truncate">{{ $user->name }}</div>
                            <div class="flex items-center gap-2 text-xs mt-1">
                                <span class="font-bold px-2 py-0.5 bg-gray-200 text-gray-700 rounded-lg uppercase tracking-wider">{{ __($user->role) }}</span>
                                <span class="text-gray-500">{{ $user->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Recent Listings -->
            <div class="bg-white rounded-[2rem] shadow-xl border border-gray-100 p-6 sm:p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <span class="text-2xl">✨</span> {{ __('Recent Listings') }}
                </h2>
                <div class="space-y-3">
                    @foreach($recentListings->take(5) as $listing)
                    <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-2xl hover:bg-white hover:shadow-md transition border border-transparent hover:border-gray-100">
                        <div class="w-12 h-12 bg-gradient-to-br from-teal-500 to-emerald-600 rounded-xl flex items-center justify-center text-white shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-bold text-gray-900 truncate">{{ __($listing->product->variety) }}</div>
                            <div class="flex items-center gap-2 text-xs mt-1">
                                <span class="text-gray-600 font-medium truncate max-w-[100px]">{{ $listing->seller->name }}</span>
                                <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                                <span class="font-bold px-2 py-0.5 rounded-lg text-[10px] uppercase tracking-wider
                                    {{ $listing->status === 'active' ? 'bg-green-100 text-green-700' : ($listing->status === 'pending' ? 'bg-orange-100 text-orange-700' : 'bg-gray-200 text-gray-700') }}">
                                    {{ __($listing->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
