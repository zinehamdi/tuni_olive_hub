@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col md:flex-row" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" x-data="{ sidebarOpen: false }">
    
    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-gray-900/50 z-40 md:hidden" x-transition></div>

    <!-- Sidebar -->
    <aside x-cloak
           x-effect="$el.style.transform = window.innerWidth >= 768 ? '' : (sidebarOpen ? 'translateX(0)' : '{{ app()->getLocale() === 'ar' ? 'translateX(100%)' : 'translateX(-100%)' }}')"
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
            <a href="{{ route('admin.analytics.visitors') }}" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-[#6A8F3B] rounded-xl font-bold transition group">
                <span class="text-xl group-hover:scale-110 transition">🌍</span> {{ __('Visitor Analytics') }}
            </a>
            <a href="{{ route('admin.analytics.marketing') }}" class="flex items-center gap-3 px-4 py-3 bg-[#6A8F3B]/10 text-[#6A8F3B] rounded-xl font-bold transition">
                <span class="text-xl">📈</span> {{ app()->getLocale() === 'ar' ? 'تحليلات التسويق' : __('Marketing Analytics') }}
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
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 w-full p-4 sm:p-8 min-w-0">
        <!-- Header -->
        <div class="mb-8 flex items-center justify-between">
            <div>
                <a href="{{ route('admin.dashboard') }}" class="text-[#6A8F3B] hover:text-[#5a7a2f] font-bold text-sm flex items-center gap-1 mb-2">
                    <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    {{ __('Back to Dashboard') }}
                </a>
                <h1 class="text-3xl sm:text-4xl font-black text-gray-900 mb-2">{{ app()->getLocale() === 'ar' ? 'تفاصيل إحصائيات التسويق' : __('Marketing Analytics Details') }}</h1>
                <p class="text-gray-600 font-medium">{{ app()->getLocale() === 'ar' ? 'تتبع عمليات إضافة السلة، بدء الشراء، والمبيعات الناتجة عن حملتك التسويقية' : __('Track add to carts, initiated checkouts, and sales from your campaigns') }}</p>
            </div>
            <button @click="sidebarOpen = true" class="md:hidden p-3 bg-white rounded-xl shadow-md text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>

        <!-- Conversion Stats Overview Card -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-[2rem] p-6 shadow-xl border border-gray-100 flex items-center justify-between">
                <div>
                    <div class="text-sm font-bold text-gray-500 mb-1">{{ app()->getLocale() === 'ar' ? 'المبيعات' : __('Purchases') }}</div>
                    <div class="text-3xl font-black text-emerald-600">{{ number_format($purchasesCount) }}</div>
                </div>
                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-2xl">💰</div>
            </div>
            <div class="bg-white rounded-[2rem] p-6 shadow-xl border border-gray-100 flex items-center justify-between">
                <div>
                    <div class="text-sm font-bold text-gray-500 mb-1">{{ app()->getLocale() === 'ar' ? 'بدء الشراء' : __('Checkouts') }}</div>
                    <div class="text-3xl font-black text-indigo-600">{{ number_format($checkoutsCount) }}</div>
                </div>
                <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center text-2xl">🛍️</div>
            </div>
            <div class="bg-white rounded-[2rem] p-6 shadow-xl border border-gray-100 flex items-center justify-between">
                <div>
                    <div class="text-sm font-bold text-gray-500 mb-1">{{ app()->getLocale() === 'ar' ? 'إضافة للسلة' : __('Cart Adds') }}</div>
                    <div class="text-3xl font-black text-pink-600">{{ number_format($addToCartCount) }}</div>
                </div>
                <div class="w-12 h-12 bg-pink-50 text-pink-600 rounded-2xl flex items-center justify-center text-2xl">🛒</div>
            </div>
            <div class="bg-white rounded-[2rem] p-6 shadow-xl border border-gray-100 flex items-center justify-between bg-gradient-to-br from-[#C8A356]/10 to-[#A88932]/10">
                <div>
                    <div class="text-sm font-bold text-gray-700 mb-1">{{ app()->getLocale() === 'ar' ? 'الإيرادات' : __('Revenue') }}</div>
                    <div class="text-3xl font-black text-[#A88932]">{{ number_format($totalRevenue) }} <span class="text-sm font-bold">TND</span></div>
                </div>
                <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center text-2xl">📈</div>
            </div>
        </div>

        <!-- Marketing Logs Table -->
        <div class="bg-white rounded-[2rem] shadow-xl border border-gray-100 overflow-hidden">
            <div class="p-6 sm:p-8 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                    <span class="text-2xl">📊</span> {{ app()->getLocale() === 'ar' ? 'سجل عمليات التحويل والتفاعل' : __('Marketing Conversion Logs') }}
                </h2>
                <span class="px-4 py-1.5 bg-[#6A8F3B]/10 text-[#6A8F3B] rounded-full text-xs font-bold">{{ app()->getLocale() === 'ar' ? 'آخر العمليات' : __('Latest events') }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                            <th class="px-6 py-4 font-bold text-center">{{ app()->getLocale() === 'ar' ? 'الحدث' : __('Event Type') }}</th>
                            <th class="px-6 py-4 font-bold">{{ app()->getLocale() === 'ar' ? 'الباقة / الخدمة' : __('Marketing Service') }}</th>
                            <th class="px-6 py-4 font-bold">{{ app()->getLocale() === 'ar' ? 'القيمة' : __('Value') }}</th>
                            <th class="px-6 py-4 font-bold">{{ app()->getLocale() === 'ar' ? 'المستخدم' : __('User') }}</th>
                            <th class="px-6 py-4 font-bold">{{ app()->getLocale() === 'ar' ? 'معرف الجلسة / الوقت' : __('Session / Time') }}</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm font-medium divide-y divide-gray-100">
                        @forelse($analytics as $a)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4 text-center">
                                @if($a->event_type === 'purchase')
                                    <span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-xs font-black">{{ app()->getLocale() === 'ar' ? 'شراء' : 'Purchase' }}</span>
                                @elseif($a->event_type === 'checkout_initiated')
                                    <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-xs font-black">{{ app()->getLocale() === 'ar' ? 'بدء شراء' : 'Checkout' }}</span>
                                @elseif($a->event_type === 'add_to_cart')
                                    <span class="bg-pink-100 text-pink-700 px-3 py-1 rounded-full text-xs font-black">{{ app()->getLocale() === 'ar' ? 'إضافة للسلة' : 'Add to Cart' }}</span>
                                @else
                                    <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-xs font-bold">{{ $a->event_type }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($a->service)
                                    <span class="text-gray-900 font-bold flex items-center gap-2">
                                        <span class="text-lg">{{ $a->service->icon_url }}</span>
                                        {{ app()->getLocale() === 'ar' ? $a->service->title_ar : (app()->getLocale() === 'fr' ? $a->service->title_fr : $a->service->title_en) }}
                                    </span>
                                @else
                                    <span class="text-gray-400 italic">{{ __('Unknown Service') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($a->value > 0)
                                    <span class="text-emerald-600 font-black text-base">{{ number_format($a->value) }} {{ $a->currency ?? 'TND' }}</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($a->user)
                                    <div class="flex flex-col">
                                        <span class="text-gray-900 font-bold">{{ $a->user->name }}</span>
                                        <span class="text-gray-500 text-xs">{{ $a->user->email }}</span>
                                    </div>
                                @elseif($a->likely_user)
                                    <div class="flex flex-col">
                                        <span class="text-[#6A8F3B] font-bold flex items-center gap-1">
                                            <span>👤</span>
                                            {{ $a->likely_user->name }} 
                                            <span class="text-[10px] bg-[#6A8F3B]/10 text-[#6A8F3B] px-1.5 py-0.5 rounded-full">{{ app()->getLocale() === 'ar' ? 'محتمل (ربط جلسة)' : 'Likely (Stitched)' }}</span>
                                        </span>
                                        <span class="text-gray-500 text-xs">{{ $a->likely_user->email }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-400 italic">{{ app()->getLocale() === 'ar' ? 'زائر غير مسجل' : __('Guest Visitor') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-gray-400 text-xs font-mono max-w-[120px] truncate" title="{{ $a->session_id }}">{{ $a->session_id }}</span>
                                    <span class="text-gray-500 text-xs mt-1">{{ $a->created_at->diffForHumans() }} ({{ $a->created_at->format('Y-m-d H:i') }})</span>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400 font-bold">
                                {{ __('No marketing events logged yet.') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-6 sm:p-8 bg-gray-50 border-t border-gray-100">
                {{ $analytics->links() }}
            </div>
        </div>

    </main>
</div>
@endsection
