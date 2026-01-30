@extends('layouts.app')

@section('title', 'سوق زيت الزيتون التونسي | Tunisian Olive Oil Marketplace')
@section('description', 'اكتشف أفضل منتجات زيت الزيتون التونسي من المزارعين والمعاصر والمعبئين. جودة عالية، أسعار تنافسية، توصيل سريع. Discover premium Tunisian olive oil products from farmers, mills and packers.')
@section('og_title', 'سوق زيت الزيتون التونسي - جودة أصلية من المزارع إلى منزلك')
@section('og_description', 'تسوق زيت الزيتون التونسي الأصلي بجودة عالية من المزارعين والمعاصر مباشرة')

@section('content')
<div dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" class="min-h-screen bg-gradient-to-b from-gray-50 to-white" x-data="marketplace">
    
    <!-- Header with Login/Register -->
    <header class="bg-white/95 backdrop-blur border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-4">
            <div class="flex items-center justify-between gap-4">
                <!-- Logo -->
                <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                    <div class="h-20 w-20 rounded-full overflow-hidden shadow-lg group-hover:shadow-xl transition-all">
                        <img src="{{ asset('images/logotoop.PNG') }}" alt="Tunisian Olive Oil Platform" class="h-full w-full object-cover scale-125 translate-y-2 group-hover:scale-[1.35] transition-transform">
                    </div>
                    <div>
                        <div class="text-xl font-bold text-gray-900">{{ __('Tunisian Olive Oil Platform') }}</div>
                        <div class="text-xs text-gray-600">Tunisian Olive Oil Platform</div>
                    </div>
                </a>

                <!-- Navigation Menu -->
                <nav class="hidden md:flex items-center gap-6">
                    <a href="{{ url('/') }}" class="text-gray-700 hover:text-[#6A8F3B] font-semibold transition">
                        {{ __('Home') }}
                    </a>
                    <a href="#products" class="text-gray-700 hover:text-[#6A8F3B] font-semibold transition">
                        {{ __('Products') }}
                    </a>
                    @auth
                        <a href="{{ route('listings.create') }}" class="text-gray-700 hover:text-[#6A8F3B] font-semibold transition">
                            {{ __('Add Listing') }}
                        </a>
                    @endauth
                    <a href="{{ route('about') }}" class="text-gray-700 hover:text-[#6A8F3B] font-semibold transition">
                        {{ __('About') }}
                    </a>
                </nav>

                <!-- Mobile Menu Toggle -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

            <!-- Mobile Menu -->
            <div x-show="mobileMenuOpen" x-transition class="md:hidden mt-4 pb-4 border-t pt-4">
                <nav class="space-y-2">
                    <a href="{{ url('/') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-lg font-semibold">{{ __('Home') }}</a>
                    <a href="#products" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-lg font-semibold">{{ __('Products') }}</a>
                    @auth
                        <a href="{{ route('listings.create') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-lg font-semibold">{{ __('Add Listing') }}</a>
                    @endauth
                    <a href="{{ route('about') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-lg font-semibold">{{ __('About') }}</a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Hero Section with Search -->
    <section class="relative bg-gradient-to-br from-[#6A8F3B] to-[#5a7a2f] text-white py-16 px-4 overflow-hidden">
        <!-- Background Image -->
        <div class="absolute inset-0 bg-cover bg-no-repeat" style="background-image: url('{{ asset('images/dealbackground.png') }}'); background-position: center 30%;"></div>
        <!-- Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-br from-[#6A8F3B]/40 to-[#5a7a2f]/40"></div>
        <!-- Dark Overlay -->
        <div class="absolute inset-0 bg-black/10"></div>
        <div class="max-w-7xl mx-auto relative z-10">
            <div class="text-center mb-8">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">{{ __('Tunisian Olive Oil Platform') }}</h1>
                <p class="text-xl text-white/90 mb-8">{{ __('Discover the best offers from direct producers in your area') }}</p>
            </div>

            <!-- Search Bar with Location -->
            <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-2xl p-2">
                <div class="flex flex-col md:flex-row gap-2">
                    <div class="flex-1 relative">
                        <input type="text" 
                               x-model="searchQuery"
                               @input="filterListings"
                               placeholder="{{ __('Search for product (oil, olive, shemlali...)') }}"
                               class="w-full px-4 py-3 pr-12 rounded-xl border-2 border-gray-200 focus:border-[#6A8F3B] focus:outline-none text-gray-900">
                        <svg class="absolute right-4 top-4 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <button @click="getMyLocation" class="px-6 py-3 bg-[#C8A356] text-white rounded-xl hover:bg-[#b08a3c] transition font-bold flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="hidden md:inline">{{ __('Near Me') }}</span>
                    </button>
                    <button @click="filterListings(); setTimeout(() => { document.getElementById('products').scrollIntoView({ behavior: 'smooth', block: 'start' }); }, 100);" 
                            class="px-8 py-3 bg-[#6A8F3B] text-white rounded-xl hover:bg-[#5a7a2f] transition font-bold">
                        {{ __('Search') }}
                    </button>
                </div>
                
                <!-- Location Status -->
                <div x-show="userLocation.lat" class="mt-2 px-4 py-2 bg-green-50 text-green-700 rounded-lg text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ __('Location identified - searching by proximity') }}</span>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8 max-w-4xl mx-auto">
                <div class="bg-white/10 backdrop-blur rounded-xl p-4 text-center">
                    <div class="text-3xl font-bold" x-text="totalListings"></div>
                    <div class="text-sm text-white/80">{{ __('Active listings') }}</div>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-xl p-4 text-center">
                    <div class="text-3xl font-bold" x-text="oilCount"></div>
                    <div class="text-sm text-white/80">{{ __('Olive Oil') }}</div>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-xl p-4 text-center">
                    <div class="text-3xl font-bold" x-text="oliveCount"></div>
                    <div class="text-sm text-white/80">{{ __('Olives') }}</div>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-xl p-4 text-center">
                    <div class="text-3xl font-bold" x-text="filteredListings.length"></div>
                    <div class="text-sm text-white/80">{{ __('Search results') }}</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section id="products" class="max-w-7xl mx-auto px-4 py-12">
        <div class="flex flex-col lg:flex-row gap-6">
            
            <!-- Mobile Filter Toggle Button -->
            <div class="lg:hidden mb-4">
                <button @click="showFilters = !showFilters" 
                        class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-[#6A8F3B] text-white rounded-xl hover:bg-[#5a7a2f] transition font-bold shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    <span x-text="showFilters ? '{{ __('Hide Filters') }}' : '{{ __('Show Filters') }}'"></span>
                    <svg class="w-4 h-4 transition-transform" :class="showFilters ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
            </div>

            <!-- Sidebar Filters -->
            <aside class="lg:w-64 flex-shrink-0">
                <div class="bg-white rounded-2xl shadow-lg p-6 lg:sticky lg:top-4 max-h-[70vh] lg:max-h-[calc(100vh-2rem)] overflow-y-auto lg:block"
                     x-show="showFilters"
                     x-transition:enter="lg:transition-none transition ease-out duration-200"
                     x-transition:enter-start="lg:opacity-100 lg:translate-y-0 opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="lg:transition-none transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="lg:opacity-100 lg:translate-y-0 opacity-0 -translate-y-2"
                     style="display: none;">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-[#6A8F3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        {{ __('Filter Results') }}
                    </h3>
                        {{ __('Filter Results') }}
                    </h3>

                    <!-- Location Filter -->
                    <div class="mb-6 p-4 bg-gradient-to-r from-[#6A8F3B]/10 to-[#C8A356]/10 rounded-xl">
                        <label class="font-bold text-gray-900 mb-3 flex items-center gap-2">
                            <svg class="w-5 h-5 text-[#6A8F3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            </svg>
                            {{ __('Distance') }}
                        </label>
                        <select x-model="filters.distance" @change="filterListings" class="w-full px-3 py-2 border-2 border-[#6A8F3B] rounded-lg focus:ring-2 focus:ring-[#6A8F3B] focus:border-transparent font-semibold">
                            <option value="all">{{ __('All distances') }}</option>
                            <option value="10">{{ __('Less than 10 km') }}</option>
                            <option value="25">{{ __('Less than 25 km') }}</option>
                            <option value="50">{{ __('Less than 50 km') }}</option>
                            <option value="100">{{ __('Less than 100 km') }}</option>
                        </select>
                        <button @click="getMyLocation" class="mt-2 w-full px-3 py-2 bg-[#C8A356] text-white rounded-lg hover:bg-[#b08a3c] transition text-sm font-bold">
                            {{ __('Get my location') }}
                        </button>
                    </div>

                    <!-- Product Type Filter -->
                    <div class="mb-6">
                        <label class="block font-bold text-gray-900 mb-3">{{ __('Product Type') }}</label>
                        <div class="space-y-3">
                            <!-- All Products -->
                            <label class="flex items-center gap-3 p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-[#6A8F3B] hover:bg-[#6A8F3B]/5 transition-all"
                                   :class="filters.type === 'all' ? 'border-[#6A8F3B] bg-[#6A8F3B]/10' : ''">
                                <input type="radio" x-model="filters.type" value="all" @change="filterListings" class="text-[#6A8F3B] focus:ring-[#6A8F3B] w-5 h-5">
                                <div class="flex items-center gap-3 flex-1">
                                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-[#6A8F3B] to-[#5a7a2f] flex items-center justify-center text-white flex-shrink-0">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-bold text-gray-900 text-sm">{{ __('All') }}</div>
                                    </div>
                                    <div class="text-xs font-semibold text-gray-500 bg-gray-100 px-2 py-1 rounded-full" x-text="totalListings"></div>
                                </div>
                            </label>

                            <!-- Olive Oil -->
                            <label class="flex items-center gap-3 p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-[#6A8F3B] hover:bg-[#6A8F3B]/5 transition-all"
                                   :class="filters.type === 'oil' ? 'border-[#6A8F3B] bg-[#6A8F3B]/10' : ''">
                                <input type="radio" x-model="filters.type" value="oil" @change="filterListings" class="text-[#6A8F3B] focus:ring-[#6A8F3B] w-5 h-5">
                                <div class="flex items-center gap-3 flex-1">
                                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-[#C8A356] to-[#b08a3c] flex items-center justify-center flex-shrink-0">
                                        <span class="text-2xl">🫗</span>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-bold text-gray-900 text-sm">{{ __('Olive Oil') }}</div>
                                    </div>
                                    <div class="text-xs font-semibold text-gray-500 bg-gray-100 px-2 py-1 rounded-full" x-text="oilCount"></div>
                                </div>
                            </label>

                            <!-- Olives -->
                            <label class="flex items-center gap-3 p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-[#6A8F3B] hover:bg-[#6A8F3B]/5 transition-all"
                                   :class="filters.type === 'olive' ? 'border-[#6A8F3B] bg-[#6A8F3B]/10' : ''">
                                <input type="radio" x-model="filters.type" value="olive" @change="filterListings" class="text-[#6A8F3B] focus:ring-[#6A8F3B] w-5 h-5">
                                <div class="flex items-center gap-3 flex-1">
                                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-[#10B981] to-[#059669] flex items-center justify-center flex-shrink-0">
                                        <span class="text-2xl">🫒</span>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-bold text-gray-900 text-sm">{{ __('Olives') }}</div>
                                    </div>
                                    <div class="text-xs font-semibold text-gray-500 bg-gray-100 px-2 py-1 rounded-full" x-text="oliveCount"></div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Quality Filter -->
                    <div class="mb-6">
                        <label class="block font-bold text-gray-900 mb-3">{{ __('Quality') }}</label>
                        <div class="space-y-3">
                            <!-- Premium -->
                            <label class="flex items-center gap-3 p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-[#6A8F3B] hover:bg-[#6A8F3B]/5 transition-all"
                                   :class="filters.qualities.includes('premium') ? 'border-[#6A8F3B] bg-[#6A8F3B]/10' : ''">
                                <input type="checkbox" x-model="filters.qualities" value="premium" @change="filterListings" class="text-[#6A8F3B] focus:ring-[#6A8F3B] rounded w-5 h-5">
                                <div class="flex items-center gap-3 flex-1">
                                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-[#C8A356] to-[#b08a3c] flex items-center justify-center text-white flex-shrink-0">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-bold text-gray-900 text-sm">{{ __('Premium') }}</div>
                                    </div>
                                </div>
                            </label>

                            <!-- Extra -->
                            <label class="flex items-center gap-3 p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-[#6A8F3B] hover:bg-[#6A8F3B]/5 transition-all"
                                   :class="filters.qualities.includes('extra') ? 'border-[#6A8F3B] bg-[#6A8F3B]/10' : ''">
                                <input type="checkbox" x-model="filters.qualities" value="extra" @change="filterListings" class="text-[#6A8F3B] focus:ring-[#6A8F3B] rounded w-5 h-5">
                                <div class="flex items-center gap-3 flex-1">
                                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-[#6A8F3B] to-[#5a7a2f] flex items-center justify-center text-white flex-shrink-0">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-bold text-gray-900 text-sm">{{ __('Extra') }}</div>
                                    </div>
                                </div>
                            </label>

                            <!-- Standard -->
                            <label class="flex items-center gap-3 p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-[#6A8F3B] hover:bg-[#6A8F3B]/5 transition-all"
                                   :class="filters.qualities.includes('standard') ? 'border-[#6A8F3B] bg-[#6A8F3B]/10' : ''">
                                <input type="checkbox" x-model="filters.qualities" value="standard" @change="filterListings" class="text-[#6A8F3B] focus:ring-[#6A8F3B] rounded w-5 h-5">
                                <div class="flex items-center gap-3 flex-1">
                                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-[#3B82F6] to-[#2563EB] flex items-center justify-center text-white flex-shrink-0">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-bold text-gray-900 text-sm">{{ __('Standard') }}</div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Price Range Filter -->
                    <div class="mb-6">
                        <label class="block font-bold text-gray-900 mb-3">{{ __('Price Range') }}</label>
                        <div class="space-y-3">
                            <div>
                                <input type="number" x-model="filters.priceMin" @input="filterListings" placeholder="{{ __('Min') }}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-[#6A8F3B] focus:border-transparent">
                            </div>
                            <div>
                                <input type="number" x-model="filters.priceMax" @input="filterListings" placeholder="{{ __('Max') }}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-[#6A8F3B] focus:border-transparent">
                            </div>
                        </div>
                    </div>

                    <!-- Sort Options -->
                    <div class="mb-6">
                        <label class="block font-bold text-gray-900 mb-3">{{ __('Sort by') }}</label>
                        <select x-model="filters.sortBy" @change="filterListings" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-[#6A8F3B] focus:border-transparent">
                            <option value="nearest">{{ __('Nearest to me') }}</option>
                            <option value="newest">{{ __('Newest') }}</option>
                            <option value="oldest">{{ __('Oldest') }}</option>
                            <option value="price_low">{{ __('Price: Low to High') }}</option>
                            <option value="price_high">{{ __('Price: High to Low') }}</option>
                        </select>
                    </div>

                    <!-- Reset Button -->
                    <button @click="resetFilters" class="w-full px-4 py-3 bg-gradient-to-r from-gray-600 to-gray-700 text-white rounded-lg hover:from-gray-700 hover:to-gray-800 transition font-bold shadow-lg">
                        {{ __('Reset Filters') }}
                    </button>
                </div>
            </aside>

            <!-- Product Listings Grid -->
            <main class="flex-1">
                <!-- View Toggle & Results Count -->
                <div class="flex justify-between items-center mb-6 flex-wrap gap-4">
                    <div class="text-gray-700">
                        <span class="font-bold text-2xl" x-text="filteredListings.length"></span>
                        <span class="text-lg">{{ __('products available') }}</span>
                        <span x-show="userLocation.lat" class="text-sm text-[#6A8F3B] font-semibold mr-2">
                            ({{ __('near you') }})
                        </span>
                    </div>
                    <div class="flex gap-2">
                        <button @click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'bg-[#6A8F3B] text-white' : 'bg-gray-200 text-gray-700'" class="p-3 rounded-lg transition shadow">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                        </button>
                        <button @click="viewMode = 'list'" :class="viewMode === 'list' ? 'bg-[#6A8F3B] text-white' : 'bg-gray-200 text-gray-700'" class="p-3 rounded-lg transition shadow">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Products Grid View -->
                <div x-show="viewMode === 'grid'" class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">
                    <template x-for="listing in filteredListings" :key="listing.id">
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                            <!-- Product Image -->
                            <div class="h-48 bg-gradient-to-br from-[#6A8F3B] to-[#C8A356] flex items-center justify-center relative overflow-hidden">
                                <!-- Actual Image if available -->
                                <template x-if="listing.media && listing.media.length > 0">
                                    <img :src="'/storage/' + listing.media[0]" 
                                         :alt="listing.product.variety" 
                                         class="w-full h-full object-cover"
                                         loading="lazy">
                                </template>
                                <!-- Fallback SVG icon if no image -->
                                <template x-if="!listing.media || listing.media.length === 0">
                                    <svg class="w-24 h-24 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path x-show="listing.product.type === 'oil'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                        <path x-show="listing.product.type === 'olive'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                                    </svg>
                                </template>
                                <div class="absolute top-3 right-3 flex gap-2">
                                    <span class="px-3 py-1 rounded-full text-white text-xs font-bold bg-white/20 backdrop-blur" 
                                          x-text="listing.product.type === 'olive' ? '{{ __('Olives') }}' : '{{ __('Olive Oil') }}'"></span>
                                </div>
                                <!-- Distance Badge -->
                                <div x-show="listing.distance != null && listing.distance !== undefined" class="absolute top-3 left-3">
                                    <span class="px-3 py-1 rounded-full text-white text-xs font-bold bg-[#C8A356] backdrop-blur flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        </svg>
                                        <span x-text="(listing.distance || 0).toFixed(1) + ' {{ __('km') }}'"></span>
                                    </span>
                                </div>
                            </div>

                            <!-- Product Details -->
                            <div class="p-5">
                                <h3 class="text-xl font-bold text-gray-900 mb-2" x-text="translate(listing.product.variety)"></h3>
                                
                                <div class="flex items-center gap-2 mb-3 flex-wrap">
                                    <span x-show="listing.product.quality" class="px-2 py-1 rounded-full bg-[#C8A356] text-white text-xs font-semibold" x-text="listing.product.quality"></span>
                                    <span x-show="listing.status === 'active'" class="px-2 py-1 rounded-full bg-green-500 text-white text-xs font-semibold">{{ __('Active') }}</span>
                                </div>

                                <div class="text-2xl font-bold text-[#6A8F3B] mb-4">
                                    <span x-text="Number(listing.price || listing.product?.price || 0).toFixed(2)"></span>
                                    <span class="text-sm text-gray-600">{{ __('TND') }}</span>
                                </div>

                                <div class="space-y-2 text-sm text-gray-600 mb-4">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <span x-text="listing.seller.role === 'admin' ? '{{ __('Seller') }}' : listing.seller.name"></span>
                                    </div>
                                    <div x-show="listing.seller.location || listing.seller.farm_location" class="flex items-center gap-2 text-[#6A8F3B] font-semibold">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        </svg>
                                        <span x-text="listing.seller.location || listing.seller.farm_location"></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span x-text="formatDate(listing.created_at)"></span>
                                    </div>
                                </div>

                                <div class="flex gap-2">
                                    <a :href="'/listings/' + listing.id" class="flex-1 text-center px-4 py-2 bg-[#6A8F3B] text-white rounded-lg hover:bg-[#5a7a2f] transition font-bold">
                                        {{ __('View Details') }}
                                    </a>
                                    <button class="px-4 py-2 bg-[#C8A356] text-white rounded-lg hover:bg-[#b08a3c] transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Products List View -->
                <div x-show="viewMode === 'list'" class="space-y-4">
                    <template x-for="listing in filteredListings" :key="listing.id">
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 flex flex-col md:flex-row">
                            <!-- Product Image -->
                            <div class="w-full md:w-48 h-48 bg-gradient-to-br from-[#6A8F3B] to-[#C8A356] flex items-center justify-center flex-shrink-0 relative overflow-hidden">
                                <!-- Actual Image if available -->
                                <template x-if="listing.media && listing.media.length > 0">
                                    <img :src="'/storage/' + listing.media[0]" 
                                         :alt="listing.product.variety" 
                                         class="w-full h-full object-cover"
                                         loading="lazy">
                                </template>
                                <!-- Fallback SVG icon if no image -->
                                <template x-if="!listing.media || listing.media.length === 0">
                                    <svg class="w-20 h-20 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path x-show="listing.product.type === 'oil'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                        <path x-show="listing.product.type === 'olive'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                                    </svg>
                                </template>
                                <!-- Distance Badge -->
                                <div x-show="listing.distance != null && listing.distance !== undefined" class="absolute top-3 left-3">
                                    <span class="px-3 py-1 rounded-full text-white text-xs font-bold bg-[#C8A356] backdrop-blur flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        </svg>
                                        <span x-text="(listing.distance || 0).toFixed(1) + ' كم'"></span>
                                    </span>
                                </div>
                            </div>

                            <!-- Product Details -->
                            <div class="flex-1 p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2 flex-wrap">
                                        <h3 class="text-2xl font-bold text-gray-900" x-text="translate(listing.product.variety)"></h3>
                                        <span class="px-3 py-1 rounded-full text-white text-xs font-bold bg-[#6A8F3B]" x-text="listing.product.type === 'olive' ? '{{ __('Olives') }}' : '{{ __('Olive Oil') }}'"></span>
                                        <span x-show="listing.product.quality" class="px-3 py-1 rounded-full bg-[#C8A356] text-white text-xs font-semibold" x-text="listing.product.quality"></span>
                                    </div>
                                    <div class="flex items-center gap-4 text-sm text-gray-600 flex-wrap">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            <span x-text="listing.seller.role === 'admin' ? '{{ __('Seller') }}' : listing.seller.name"></span>
                                        </div>
                                        <div x-show="listing.seller.location || listing.seller.farm_location" class="flex items-center gap-2 text-[#6A8F3B] font-semibold">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            </svg>
                                            <span x-text="listing.seller.location || listing.seller.farm_location"></span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span x-text="formatDate(listing.created_at)"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center md:text-left">
                                    <div class="text-3xl font-bold text-[#6A8F3B] mb-4">
                                        <span x-text="Number(listing.price || listing.product?.price || 0).toFixed(2)"></span>
                                        <span class="text-sm text-gray-600">{{ __('TND') }}</span>
                                    </div>
                                    <div class="flex gap-2">
                                        <a :href="'/listings/' + listing.id" class="px-6 py-2 bg-[#6A8F3B] text-white rounded-lg hover:bg-[#5a7a2f] transition font-bold whitespace-nowrap">
                                            {{ __('View Details') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Empty State -->
                <div x-show="filteredListings.length === 0" class="text-center py-16">
                    <svg class="w-24 h-24 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-2xl font-bold text-gray-700 mb-2">{{ __('No results found') }}</h3>
                    <p class="text-gray-500 mb-6">{{ __('Try changing your search or filter criteria') }}</p>
                    <button @click="resetFilters" class="px-6 py-3 bg-[#6A8F3B] text-white rounded-xl hover:bg-[#5a7a2f] transition font-bold shadow-lg">
                        {{ __('Reset Search') }}
                    </button>
                </div>
            </main>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="bg-gradient-to-br from-[#6A8F3B] to-[#5a7a2f] text-white py-16 px-4 mt-12">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">{{ __('Do you have a product to sell?') }}</h2>
            <p class="text-xl text-white/90 mb-8">{{ __('Join thousands of sellers and list your product today') }}</p>
            <a href="{{ route('listings.create') }}" class="inline-block px-8 py-4 bg-white text-[#6A8F3B] rounded-xl hover:bg-gray-100 transition font-bold text-lg shadow-lg">
                {{ __('Add your listing for free') }}
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12 px-4">
        <div class="max-w-7xl mx-auto grid md:grid-cols-4 gap-8">
            <div>
                <h3 class="text-xl font-bold mb-4">{{ __('Tunisian Olive Oil Platform') }}</h3>
                <p class="text-gray-400">{{ __('Platform connecting producers and buyers') }}</p>
            </div>
            <div>
                <h4 class="font-bold mb-4">{{ __('Quick Links') }}</h4>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="{{ url('/') }}" class="hover:text-white transition">{{ __('Home') }}</a></li>
                    <li><a href="#products" class="hover:text-white transition">{{ __('Products') }}</a></li>
                    <li><a href="{{ route('about') }}" class="hover:text-white transition">{{ __('About') }}</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold mb-4">{{ __('Account') }}</h4>
                <ul class="space-y-2 text-gray-400">
                    @auth
                        <li><a href="{{ route('dashboard') }}" class="hover:text-white transition">{{ __('Dashboard') }}</a></li>
                        <li><a href="{{ route('profile.edit') }}" class="hover:text-white transition">{{ __('Profile') }}</a></li>
                    @else
                        <li><a href="{{ route('login') }}" class="hover:text-white transition">{{ __('Login') }}</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-white transition">{{ __('Register') }}</a></li>
                    @endauth
                </ul>
            </div>
            <div>
                <h4 class="font-bold mb-4">{{ __('Contact Us') }}</h4>
                <p class="text-gray-400">{{ __('Email') }}: info@olivemarketplace.tn</p>
                <p class="text-gray-400">{{ __('Phone') }}: +216 XX XXX XXX</p>
            </div>
        </div>
        <div class="max-w-7xl mx-auto mt-8 pt-8 border-t border-gray-800 text-center text-gray-400">
            <p>© {{ now()->year }} {{ __('Tunisian Olive Oil Platform') }}. {{ __('All Rights Reserved') }}.</p>
        </div>
    </footer>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('marketplace', () => ({
        listings: @json($featuredListings ?? []),
        filteredListings: [],
        searchQuery: '',
        viewMode: 'grid',
        mobileMenuOpen: false,
        showFilters: false,
        userLocation: {
            lat: null,
            lng: null
        },
        filters: {
            type: 'all',
            qualities: [],
            priceMin: '',
            priceMax: '',
            sortBy: 'newest',
            distance: 'all'
        },
        translations: {
            'Extra Virgin Olive Oil': '{{ __("Extra Virgin Olive Oil") }}',
            'Chemlali Olive Oil': '{{ __("Chemlali Olive Oil") }}',
            'Chetoui Olive Oil': '{{ __("Chetoui Olive Oil") }}',
            'Organic Extra Virgin': '{{ __("Organic Extra Virgin") }}',
            'Cold Pressed Olive Oil': '{{ __("Cold Pressed Olive Oil") }}',
            'Premium Blend Olive Oil': '{{ __("Premium Blend Olive Oil") }}',
            'Chemlali Olives': '{{ __("Chemlali Olives") }}',
            'Chetoui Olives': '{{ __("Chetoui Olives") }}',
            'Meski Olives': '{{ __("Meski Olives") }}',
            'Zalmati Olives': '{{ __("Zalmati Olives") }}',
            'Fresh Olives': '{{ __("Fresh Olives") }}',
            'Table Olives': '{{ __("Table Olives") }}'
        },

        translate(text) {
            return this.translations[text] || text;
        },

        init() {
            // Always show filters on desktop (>= 1024px)
            if (window.innerWidth >= 1024) {
                this.showFilters = true;
            }
            
            this.filteredListings = this.listings;
            // Try to get saved location from localStorage
            const savedLocation = localStorage.getItem('userLocation');
            if (savedLocation) {
                this.userLocation = JSON.parse(savedLocation);
                this.calculateDistances();
                this.filterListings();
            }
        },

        get totalListings() {
            return this.listings.length;
        },

        get oilCount() {
            return this.listings.filter(l => l.product?.type === 'oil').length;
        },

        get oliveCount() {
            return this.listings.filter(l => l.product?.type === 'olive').length;
        },

        getMyLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        this.userLocation = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        };
                        localStorage.setItem('userLocation', JSON.stringify(this.userLocation));
                        this.calculateDistances();
                        this.filters.sortBy = 'nearest';
                        this.filterListings();
                    },
                    (error) => {
                        alert('لم نتمكن من تحديد موقعك. الرجاء السماح بالوصول إلى الموقع.');
                    }
                );
            } else {
                alert('المتصفح لا يدعم تحديد الموقع.');
            }
        },

        calculateDistances() {
            if (!this.userLocation.lat) return;

            this.listings.forEach(listing => {
                // Try to get seller's address with coordinates
                if (listing.seller?.addresses && listing.seller.addresses.length > 0) {
                    const address = listing.seller.addresses[0];
                    if (address.lat && address.lng) {
                        listing.distance = this.calculateDistance(
                            this.userLocation.lat,
                            this.userLocation.lng,
                            address.lat,
                            address.lng
                        );
                    }
                }
            });
        },

        calculateDistance(lat1, lon1, lat2, lon2) {
            // Haversine formula for calculating distance between two coordinates
            const R = 6371; // Radius of the earth in km
            const dLat = this.deg2rad(lat2 - lat1);
            const dLon = this.deg2rad(lon2 - lon1);
            const a =
                Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(this.deg2rad(lat1)) * Math.cos(this.deg2rad(lat2)) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            const distance = R * c; // Distance in km
            return distance;
        },

        deg2rad(deg) {
            return deg * (Math.PI / 180);
        },

        filterListings() {
            let results = [...this.listings];

            // Search filter
            if (this.searchQuery.trim()) {
                const query = this.searchQuery.toLowerCase();
                results = results.filter(listing =>
                    listing.product?.variety?.toLowerCase().includes(query) ||
                    listing.product?.quality?.toLowerCase().includes(query) ||
                    listing.seller?.name?.toLowerCase().includes(query) ||
                    listing.seller?.location?.toLowerCase().includes(query) ||
                    listing.seller?.farm_location?.toLowerCase().includes(query)
                );
            }

            // Type filter
            if (this.filters.type !== 'all') {
                results = results.filter(listing => listing.product?.type === this.filters.type);
            }

            // Quality filter
            if (this.filters.qualities.length > 0) {
                results = results.filter(listing =>
                    this.filters.qualities.includes(listing.product?.quality?.toLowerCase())
                );
            }

            // Price range filter
            if (this.filters.priceMin !== '') {
                results = results.filter(listing =>
                    Number(listing.price || listing.product?.price || 0) >= Number(this.filters.priceMin)
                );
            }
            if (this.filters.priceMax !== '') {
                results = results.filter(listing =>
                    Number(listing.price || listing.product?.price || 0) <= Number(this.filters.priceMax)
                );
            }

            // Distance filter
            if (this.filters.distance !== 'all' && this.userLocation.lat) {
                const maxDistance = Number(this.filters.distance);
                results = results.filter(listing =>
                    listing.distance && listing.distance <= maxDistance
                );
            }

            // Sort
            switch (this.filters.sortBy) {
                case 'nearest':
                    if (this.userLocation.lat) {
                        results.sort((a, b) => (a.distance || 9999) - (b.distance || 9999));
                    }
                    break;
                case 'newest':
                    results.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
                    break;
                case 'oldest':
                    results.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
                    break;
                case 'price_low':
                    results.sort((a, b) => Number(a.price || a.product?.price || 0) - Number(b.price || b.product?.price || 0));
                    break;
                case 'price_high':
                    results.sort((a, b) => Number(b.price || b.product?.price || 0) - Number(a.price || a.product?.price || 0));
                    break;
            }

            this.filteredListings = results;
        },

        resetFilters() {
            this.searchQuery = '';
            this.filters = {
                type: 'all',
                qualities: [],
                priceMin: '',
                priceMax: '',
                sortBy: this.userLocation.lat ? 'nearest' : 'newest',
                distance: 'all'
            };
            this.filterListings();
        },

        formatDate(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffTime = Math.abs(now - date);
            const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));

            const locale = '{{ app()->getLocale() }}';
            
            if (locale === 'ar') {
                if (diffDays === 0) return 'اليوم';
                if (diffDays === 1) return 'أمس';
                if (diffDays < 7) return `منذ ${diffDays} أيام`;
                if (diffDays < 30) return `منذ ${Math.floor(diffDays / 7)} أسابيع`;
                return `منذ ${Math.floor(diffDays / 30)} أشهر`;
            } else if (locale === 'fr') {
                if (diffDays === 0) return "Aujourd'hui";
                if (diffDays === 1) return 'Hier';
                if (diffDays < 7) return `il y a ${diffDays} jours`;
                if (diffDays < 30) return `il y a ${Math.floor(diffDays / 7)} semaines`;
                return `il y a ${Math.floor(diffDays / 30)} mois`;
            } else {
                if (diffDays === 0) return 'Today';
                if (diffDays === 1) return 'Yesterday';
                if (diffDays < 7) return `${diffDays} days ago`;
                if (diffDays < 30) return `${Math.floor(diffDays / 7)} weeks ago`;
                return `${Math.floor(diffDays / 30)} months ago`;
            }
        }
    }));
});
</script>

<!-- Structured Data for Products (SEO) -->
@if(isset($featuredListings) && count($featuredListings) > 0)
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "ItemList",
    "name": "منتجات زيت الزيتون التونسي",
    "description": "مجموعة من منتجات زيت الزيتون والزيتون التونسي الأصلي",
    "numberOfItems": {{ count($featuredListings) }},
    "itemListElement": [
        @foreach($featuredListings as $index => $listing)
        {
            "@@type": "ListItem",
            "position": {{ $index + 1 }},
            "item": {
                "@@type": "Product",
                "name": "{{ $listing->product->variety ?? 'زيت الزيتون' }}",
                "description": "{!! $listing->product->type === 'oil' ? 'زيت زيتون' : 'زيتون' !!} - {{ $listing->product->quality ?? 'جودة عالية' }}",
                "offers": {
                    "@@type": "Offer",
                    "price": "{{ $listing->price }}",
                    "priceCurrency": "TND",
                    "availability": "https://schema.org/InStock",
                    "seller": {
                        "@@type": "Organization",
                        "name": "{{ $listing->seller->name ?? 'بائع' }}"
                    }
                },
                "image": "{!! isset($listing->media[0]) ? asset('storage/' . $listing->media[0]) : asset('images/logotoop.PNG') !!}",
                "brand": {
                    "@@type": "Brand",
                    "name": "Tunisian Olive Oil"
                },
                "aggregateRating": {
                    "@@type": "AggregateRating",
                    "ratingValue": "4.5",
                    "reviewCount": "1"
                }
            }
        }{{ !$loop->last ? ',' : '' }}
        @endforeach
    ]
}
</script>

<!-- Organization Schema -->
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "Organization",
    "name": "منصة زيت الزيتون التونسي",
    "alternateName": "Tunisian Olive Oil Platform",
    "url": "{{ url('/') }}",
    "logo": "{{ asset('images/logotoop.PNG') }}",
    "description": "منصة تونسية متخصصة في تجارة زيت الزيتون والزيتون التونسي الأصلي",
    "contactPoint": {
        "@@type": "ContactPoint",
        "contactType": "Customer Service",
        "availableLanguage": ["Arabic", "French", "English"]
    },
    "sameAs": [
        "{{ url('/') }}"
    ],
    "areaServed": {
        "@@type": "Country",
        "name": "Tunisia"
    }
}
</script>
@endif

@endsection
