@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-100 py-8">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">{{ __('Admin Dashboard') }}</h1>
            <p class="text-gray-600">{{ __('Platform moderation and statistics') }}</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Users -->
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">{{ __('Total Users') }}</h3>
                    <svg class="w-10 h-10 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div class="text-4xl font-bold">{{ number_format($stats['total_users']) }}</div>
                <div class="text-sm mt-2 opacity-90">+{{ $stats['new_users_week'] }} {{ __('this week') }}</div>
            </div>

            <!-- Total Listings -->
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">{{ __('Total Listings') }}</h3>
                    <svg class="w-10 h-10 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <div class="text-4xl font-bold">{{ number_format($stats['total_listings']) }}</div>
                <div class="text-sm mt-2 opacity-90">+{{ $stats['new_listings_week'] }} {{ __('this week') }}</div>
            </div>

            <!-- Active Listings -->
            <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl p-6 text-white shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">{{ __('Active Listings') }}</h3>
                    <svg class="w-10 h-10 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="text-4xl font-bold">{{ number_format($stats['active_listings']) }}</div>
                <div class="text-sm mt-2 opacity-90">{{ __('Published') }}</div>
            </div>

            <!-- Pending Listings -->
            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-6 text-white shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">{{ __('Pending Listings') }}</h3>
                    <svg class="w-10 h-10 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="text-4xl font-bold">{{ number_format($stats['pending_listings']) }}</div>
                <div class="text-sm mt-2 opacity-90">{{ __('Awaiting approval') }}</div>
            </div>
        </div>

        <!-- Users by Role -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('Users by Role') }}</h2>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div class="text-center p-4 bg-green-50 rounded-xl">
                    <div class="text-3xl font-bold text-green-600">{{ $stats['farmers'] }}</div>
                    <div class="text-sm text-gray-600 mt-1">{{ __('Farmers') }}</div>
                </div>
                <div class="text-center p-4 bg-blue-50 rounded-xl">
                    <div class="text-3xl font-bold text-blue-600">{{ $stats['carriers'] }}</div>
                    <div class="text-sm text-gray-600 mt-1">{{ __('Carriers') }}</div>
                </div>
                <div class="text-center p-4 bg-purple-50 rounded-xl">
                    <div class="text-3xl font-bold text-purple-600">{{ $stats['mills'] }}</div>
                    <div class="text-sm text-gray-600 mt-1">{{ __('Mills') }}</div>
                </div>
                <div class="text-center p-4 bg-indigo-50 rounded-xl">
                    <div class="text-3xl font-bold text-indigo-600">{{ $stats['packers'] }}</div>
                    <div class="text-sm text-gray-600 mt-1">{{ __('Packers') }}</div>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-xl">
                    <div class="text-3xl font-bold text-gray-600">{{ $stats['normal_users'] }}</div>
                    <div class="text-sm text-gray-600 mt-1">{{ __('Normal Users') }}</div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <a href="{{ route('admin.users') }}" class="bg-white rounded-2xl shadow-xl p-6 hover:shadow-2xl transition group">
                <div class="flex items-center gap-4">
                    <div class="p-4 bg-blue-100 rounded-xl group-hover:bg-blue-200 transition">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ __('Manage Users') }}</h3>
                        <p class="text-gray-600">{{ __('View, edit, and moderate users') }}</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.listings') }}" class="bg-white rounded-2xl shadow-xl p-6 hover:shadow-2xl transition group">
                <div class="flex items-center gap-4">
                    <div class="p-4 bg-green-100 rounded-xl group-hover:bg-green-200 transition">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ __('Manage Listings') }}</h3>
                        <p class="text-gray-600">{{ __('Approve, reject, or delete listings') }}</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.prices.souk.index') }}" class="bg-white rounded-2xl shadow-xl p-6 hover:shadow-2xl transition group">
                <div class="flex items-center gap-4">
                    <div class="p-4 bg-[#6A8F3B] bg-opacity-10 rounded-xl group-hover:bg-opacity-20 transition">
                        <span class="text-4xl">🫒</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ __('Souk Prices') }}</h3>
                        <p class="text-gray-600">إدارة أسعار الأسواق التونسية</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.prices.world.index') }}" class="bg-white rounded-2xl shadow-xl p-6 hover:shadow-2xl transition group">
                <div class="flex items-center gap-4">
                    <div class="p-4 bg-[#C8A356] bg-opacity-10 rounded-xl group-hover:bg-opacity-20 transition">
                        <span class="text-4xl">🌍</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ __('World Prices') }}</h3>
                        <p class="text-gray-600">إدارة الأسعار العالمية</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Pending Listings for Approval -->
        @if($pendingListings->count() > 0)
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900">{{ __('Pending Listings') }}</h2>
                <a href="{{ route('admin.listings', ['status' => 'pending']) }}" class="text-[#6A8F3B] hover:text-[#5a7a2f] font-semibold">
                    {{ __('View All') }} →
                </a>
            </div>
            
            <div class="space-y-4">
                @foreach($pendingListings as $listing)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition">
                    <div class="flex items-center gap-4 flex-1">
                        <div class="w-16 h-16 bg-gradient-to-br from-[#6A8F3B] to-[#C8A356] rounded-xl flex items-center justify-center">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-gray-900">{{ __($listing->product->variety) }}</h3>
                            <p class="text-sm text-gray-600">
                                {{ __('by') }} {{ $listing->seller->name }} • 
                                {{ $listing->product->price }} {{ __('TND') }}/{{ __('kg') }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">{{ $listing->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <form action="{{ route('admin.listings.approve', $listing) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">
                                {{ __('Approve') }}
                            </button>
                        </form>
                        <form action="{{ route('admin.listings.reject', $listing) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                                {{ __('Reject') }}
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Recent Activity -->
        <div class="grid md:grid-cols-2 gap-6">
            <!-- Recent Users -->
            <div class="bg-white rounded-2xl shadow-xl p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('Recent Users') }}</h2>
                <div class="space-y-3">
                    @foreach($recentUsers->take(5) as $user)
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition">
                        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div class="flex-1">
                            <div class="font-semibold text-gray-900">{{ $user->name }}</div>
                            <div class="text-xs text-gray-600">{{ ucfirst($user->role) }} • {{ $user->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Recent Listings -->
            <div class="bg-white rounded-2xl shadow-xl p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('Recent Listings') }}</h2>
                <div class="space-y-3">
                    @foreach($recentListings->take(5) as $listing)
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition">
                        <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="font-semibold text-gray-900">{{ __($listing->product->variety) }}</div>
                            <div class="text-xs text-gray-600">
                                {{ $listing->seller->name }} • 
                                <span class="px-2 py-1 bg-{{ $listing->status === 'active' ? 'green' : ($listing->status === 'pending' ? 'orange' : 'gray') }}-100 text-{{ $listing->status === 'active' ? 'green' : ($listing->status === 'pending' ? 'orange' : 'gray') }}-800 rounded">
                                    {{ ucfirst($listing->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
