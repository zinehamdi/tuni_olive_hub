@extends('layouts.app')

@section('title', __('Olive Prices') . ' - ' . config('app.name'))

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#F8F4EC] via-white to-[#EEF5E9] py-12">
    <div class="container mx-auto px-4">
        
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-5xl font-bold text-[#1B2A1B] mb-4">
                📊 {{ __('Olive & Oil Prices') }}
            </h1>
            <p class="text-xl text-gray-600">
                {{ __('Daily prices from Tunisian souks and world markets') }}
            </p>
            <div class="flex items-center justify-center mt-4 text-sm text-gray-500">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                </svg>
                {{ __('Last updated') }}: {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>

        <!-- Tunisian Souk Prices Section -->
        <div class="mb-16">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-3xl font-bold text-[#1B2A1B] flex items-center">
                    🇹🇳 {{ __('Tunisian Souk Prices') }}
                </h2>
                <a href="{{ route('prices.souks') }}" class="px-6 py-3 bg-[#6A8F3B] text-white rounded-xl hover:bg-[#5a7a2f] transition font-bold">
                    {{ __('View All Souks') }}
                </a>
            </div>

            <!-- Famous Souks Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse($soukPrices as $soukPrice)
                <div class="bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border-2 border-gray-100 hover:border-[#6A8F3B]">
                    <!-- Souk Header -->
                    <div class="bg-gradient-to-r from-[#6A8F3B] to-[#5a7a2f] px-6 py-4 text-white">
                        <h3 class="text-2xl font-bold">{{ $soukPrice->souk_name }}</h3>
                        <p class="text-sm opacity-90">{{ $soukPrice->governorate }}</p>
                    </div>

                    <!-- Price Content -->
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm font-bold text-gray-600 uppercase">
                                {{ $soukPrice->product_type === 'olive' ? '🫒 ' . __('Olives') : '🫗 ' . __('Olive Oil') }}
                            </span>
                            <span class="px-3 py-1 bg-[#F8F4EC] rounded-full text-xs font-bold text-[#6A8F3B]">
                                {{ __($soukPrice->variety) }}
                            </span>
                        </div>

                        <!-- Price Display -->
                        <div class="mb-4">
                            <div class="text-sm text-gray-500 mb-1">{{ __('Average Price') }}</div>
                            <div class="text-3xl font-bold text-[#1B2A1B]">
                                {{ number_format($soukPrice->price_avg, 2) }}
                                <span class="text-lg text-gray-600">{{ $soukPrice->currency }}/{{ $soukPrice->unit }}</span>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                {{ __('Range') }}: {{ number_format($soukPrice->price_min, 2) }} - {{ number_format($soukPrice->price_max, 2) }} {{ $soukPrice->currency }}
                            </div>
                        </div>

                        <!-- Trend Indicator -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                            <span class="text-sm text-gray-600">{{ __('Trend') }}</span>
                            <div class="flex items-center {{ $soukPrice->trend_color }}">
                                <span class="text-2xl mr-2">{{ $soukPrice->trend_icon }}</span>
                                <span class="font-bold">
                                    @if($soukPrice->change_percentage)
                                        {{ abs($soukPrice->change_percentage) }}%
                                    @else
                                        {{ __('Stable') }}
                                    @endif
                                </span>
                            </div>
                        </div>

                        <!-- Date -->
                        <div class="mt-3 text-xs text-gray-400">
                            📅 {{ $soukPrice->date->format('d/m/Y') }}
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-12">
                    <div class="text-6xl mb-4">📊</div>
                    <p class="text-xl text-gray-600">{{ __('No souk prices available yet') }}</p>
                    <p class="text-sm text-gray-500 mt-2">{{ __('Check back soon for updates') }}</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- World Market Prices Section -->
        <div class="mb-16">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-3xl font-bold text-[#1B2A1B] flex items-center">
                    🌍 {{ __('World Market Prices') }}
                </h2>
                <a href="{{ route('prices.world') }}" class="px-6 py-3 bg-[#C8A356] text-white rounded-xl hover:bg-[#b08a3c] transition font-bold">
                    {{ __('View All Markets') }}
                </a>
            </div>

            <!-- World Prices Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @forelse($worldPrices as $worldPrice)
                <div class="bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border-2 border-gray-100 hover:border-[#C8A356]">
                    <!-- Country Header -->
                    <div class="bg-gradient-to-r from-[#C8A356] to-[#b08a3c] px-6 py-4 text-white">
                        <h3 class="text-2xl font-bold">{{ $worldPrice->country }}</h3>
                        @if($worldPrice->region)
                        <p class="text-sm opacity-90">{{ $worldPrice->region }}</p>
                        @endif
                    </div>

                    <!-- Price Content -->
                    <div class="p-6">
                        <div class="mb-4">
                            <span class="px-3 py-1 bg-[#F8F4EC] rounded-full text-xs font-bold text-[#C8A356]">
                                {{ $worldPrice->quality }}
                            </span>
                        </div>

                        <!-- Price Display -->
                        <div class="mb-4">
                            <div class="text-sm text-gray-500 mb-1">{{ __('Price') }}</div>
                            <div class="text-3xl font-bold text-[#1B2A1B]">
                                {{ number_format($worldPrice->price, 2) }}
                                <span class="text-lg text-gray-600">{{ $worldPrice->currency }}/{{ $worldPrice->unit }}</span>
                            </div>
                        </div>

                        <!-- Trend Indicator -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                            <span class="text-sm text-gray-600">{{ __('Trend') }}</span>
                            <div class="flex items-center {{ $worldPrice->trend_color }}">
                                <span class="text-2xl mr-2">{{ $worldPrice->trend_icon }}</span>
                                <span class="font-bold">
                                    @if($worldPrice->change_percentage)
                                        {{ abs($worldPrice->change_percentage) }}%
                                    @else
                                        {{ __('Stable') }}
                                    @endif
                                </span>
                            </div>
                        </div>

                        <!-- Date -->
                        <div class="mt-3 text-xs text-gray-400">
                            📅 {{ $worldPrice->date->format('d/m/Y') }}
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-12">
                    <div class="text-6xl mb-4">🌍</div>
                    <p class="text-xl text-gray-600">{{ __('No world prices available yet') }}</p>
                    <p class="text-sm text-gray-500 mt-2">{{ __('Check back soon for updates') }}</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-2xl p-6 border-2 border-green-200">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-lg font-bold text-green-800">{{ __('Tunisian Average') }}</h3>
                    <span class="text-3xl">🇹🇳</span>
                </div>
                <div class="text-4xl font-bold text-green-900">
                    {{ $tunisianAvg ?? '--' }} <span class="text-xl">TND</span>
                </div>
                <p class="text-sm text-green-700 mt-2">{{ __('Per kg average') }}</p>
            </div>

            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-6 border-2 border-blue-200">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-lg font-bold text-blue-800">{{ __('World Average') }}</h3>
                    <span class="text-3xl">🌍</span>
                </div>
                <div class="text-4xl font-bold text-blue-900">
                    {{ $worldAvg ?? '--' }} <span class="text-xl">EUR</span>
                </div>
                <p class="text-sm text-blue-700 mt-2">{{ __('Per liter average') }}</p>
            </div>

            <div class="bg-gradient-to-br from-amber-50 to-amber-100 rounded-2xl p-6 border-2 border-amber-200">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-lg font-bold text-amber-800">{{ __('Market Trend') }}</h3>
                    <span class="text-3xl">📈</span>
                </div>
                <div class="text-4xl font-bold text-amber-900">
                    {{ $marketTrend ?? __('Stable') }}
                </div>
                <p class="text-sm text-amber-700 mt-2">{{ __('Overall movement') }}</p>
            </div>
        </div>

    </div>
</div>
@endsection
