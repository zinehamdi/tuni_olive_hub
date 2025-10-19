@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-100" dir="rtl">
    <!-- Success Message -->
    @if(session('success'))
        <div class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 animate-slide-down">
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 text-white px-8 py-4 rounded-2xl shadow-2xl flex items-center space-x-3 space-x-reverse">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-lg font-bold">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Error Message -->
    @if(session('error'))
        <div class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 animate-slide-down">
            <div class="bg-gradient-to-r from-red-500 to-red-600 text-white px-8 py-4 rounded-2xl shadow-2xl flex items-center space-x-3 space-x-reverse">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-lg font-bold">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Validation Errors -->
    @if($errors->any())
        <div class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 animate-slide-down max-w-md">
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-xl">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-red-500 ml-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="flex-1">
                        <p class="font-bold text-red-800 mb-2">يرجى تصحيح الأخطاء التالية:</p>
                        <ul class="list-disc list-inside text-red-700 text-sm space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <style>
        @keyframes slide-down {
            from { transform: translate(-50%, -100%); opacity: 0; }
            to { transform: translate(-50%, 0); opacity: 1; }
        }
        .animate-slide-down {
            animation: slide-down 0.5s ease-out;
        }
    </style>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Profile Card - At Top -->
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden mb-8">
            <div class="relative">
                <!-- Cover Image Slideshow -->
                @if(Auth::user()->cover_photos && is_array(Auth::user()->cover_photos) && count(Auth::user()->cover_photos) > 0)
                    @php
                        $validPhotos = array_values(array_filter(Auth::user()->cover_photos, fn($p) => is_string($p)));
                        $photoCount = count($validPhotos);
                    @endphp
                    @if($photoCount > 0)
                        <div class="h-64 relative overflow-hidden" x-data="{ currentSlide: 0, slides: {{ $photoCount }} }" x-init="setInterval(() => { currentSlide = (currentSlide + 1) % slides }, 4000)">
                            @foreach($validPhotos as $index => $photo)
                                <div x-show="currentSlide === {{ $index }}" 
                                     x-transition:enter="transition ease-out duration-1000"
                                     x-transition:enter-start="opacity-0 transform scale-105"
                                     x-transition:enter-end="opacity-100 transform scale-100"
                                     x-transition:leave="transition ease-in duration-1000"
                                     x-transition:leave-start="opacity-100"
                                     x-transition:leave-end="opacity-0"
                                     class="absolute inset-0">
                                    <img src="{{ Storage::url($photo) }}" alt="Cover {{ $index + 1 }}" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                                </div>
                            @endforeach
                            
                            <!-- Slideshow Indicators -->
                            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex gap-2 z-10">
                                @foreach($validPhotos as $index => $photo)
                                    <button @click="currentSlide = {{ $index }}" 
                                            :class="currentSlide === {{ $index }} ? 'bg-white' : 'bg-white/50'" 
                                            class="w-2 h-2 rounded-full transition-all duration-300 hover:bg-white"></button>
                                @endforeach
                            </div>
                            
                            <!-- Navigation Arrows -->
                            @if($photoCount > 1)
                                <button @click="currentSlide = (currentSlide - 1 + slides) % slides" class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-black/30 hover:bg-black/50 text-white rounded-full p-2 transition z-10">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                    </svg>
                                </button>
                                <button @click="currentSlide = (currentSlide + 1) % slides" class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-black/30 hover:bg-black/50 text-white rounded-full p-2 transition z-10">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            @endif
                        </div>
                    @else
                        <!-- Default gradient cover if no valid photos -->
                        <div class="h-64 bg-gradient-to-r from-[#6A8F3B] via-[#7a9f4b] to-[#C8A356]"></div>
                    @endif
                @else
                    <!-- Default gradient cover if no photos -->
                    <div class="h-64 bg-gradient-to-r from-[#6A8F3B] via-[#7a9f4b] to-[#C8A356]"></div>
                @endif
                
                <!-- Profile Content -->
                <div class="px-6 pb-6">
                    <div class="flex flex-col lg:flex-row gap-6 -mt-16 relative">
                        <!-- Left Side: Profile Picture -->
                        <div class="flex-shrink-0">
                            <div class="w-32 h-32 rounded-2xl border-4 border-white shadow-xl overflow-hidden bg-white">
                                @if(Auth::user()->profile_picture)
                                    <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Profile" class="w-full h-full object-cover" loading="lazy">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-[#6A8F3B] to-[#C8A356] flex items-center justify-center">
                                        <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Center: User Info -->
                        <div class="flex-1">
                            <div class="bg-gradient-to-br from-amber-50 to-orange-50 border-2 border-amber-200 rounded-2xl p-4 shadow-lg">
                                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                                    <div>
                                        <h1 class="text-2xl font-bold text-gray-900 mb-1">
                                            {{ Auth::user()->name }}
                                        </h1>
                                        <div class="flex flex-wrap items-center gap-2 mb-2">
                                            <!-- Role Badge -->
                                            <span class="px-3 py-1 rounded-full text-xs font-bold text-gray-900
                                                @if(Auth::user()->role === 'farmer') bg-green-200
                                                @elseif(Auth::user()->role === 'carrier') bg-blue-200
                                                @elseif(Auth::user()->role === 'mill') bg-amber-200
                                                @elseif(Auth::user()->role === 'packer') bg-purple-200
                                                @else bg-gray-200
                                                @endif">
                                                @if(Auth::user()->role === 'farmer') 🌱 {{ __('Farmer') }}
                                                @elseif(Auth::user()->role === 'carrier') 🚚 {{ __('Carrier') }}
                                                @elseif(Auth::user()->role === 'mill') ⚙️ {{ __('Mill') }}
                                                @elseif(Auth::user()->role === 'packer') 📦 {{ __('Packer') }}
                                                @else 👤 {{ __('User') }}
                                                @endif
                                            </span>
                                            
                                            <!-- Trust Score -->
                                            @if(Auth::user()->trust_score)
                                            <span class="px-2 py-1 bg-amber-200 text-amber-900 rounded-full text-xs font-semibold flex items-center gap-1">
                                                ⭐ {{ number_format(Auth::user()->trust_score, 1) }}
                                            </span>
                                            @endif
                                            
                                            <!-- Verification Badge -->
                                            @if(Auth::user()->is_verified)
                                            <span class="px-2 py-1 bg-blue-200 text-blue-900 rounded-full text-xs font-semibold flex items-center gap-1">
                                                ✓ {{ __('Verified') }}
                                            </span>
                                            @endif
                                        </div>
                                        
                                        <!-- Business Name -->
                                        <div class="space-y-1 text-gray-800">
                                            @if(Auth::user()->role === 'farmer' && Auth::user()->farm_name)
                                                <p class="flex items-center gap-2">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                                    </svg>
                                                    <span class="font-semibold">{{ Auth::user()->farm_name }}</span>
                                                </p>
                                            @elseif(Auth::user()->role === 'carrier' && Auth::user()->company_name)
                                                <p class="flex items-center gap-2">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                    </svg>
                                                    <span class="font-semibold">{{ Auth::user()->company_name }}</span>
                                                </p>
                                            @elseif(Auth::user()->role === 'mill' && Auth::user()->mill_name)
                                                <p class="flex items-center gap-2">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                    </svg>
                                                    <span class="font-semibold">{{ Auth::user()->mill_name }}</span>
                                                </p>
                                            @elseif(Auth::user()->role === 'packer' && Auth::user()->packer_name)
                                                <p class="flex items-center gap-2">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                    </svg>
                                                    <span class="font-semibold">{{ Auth::user()->packer_name }}</span>
                                                </p>
                                            @endif
                                            
                                            <p class="flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                                                {{ Auth::user()->email }}
                                            </p>
                                            <p class="flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                </svg>
                                                {{ Auth::user()->phone }}
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <!-- Profile Completion Circle -->
                                    <div class="flex flex-col items-center gap-2">
                                        <div class="relative w-20 h-20">
                                            <svg class="w-20 h-20 transform -rotate-90">
                                                <circle cx="40" cy="40" r="32" stroke="#fed7aa" stroke-width="6" fill="none" />
                                                <circle cx="40" cy="40" r="32" stroke="#f97316" stroke-width="6" fill="none"
                                                    stroke-dasharray="201"
                                                    stroke-dashoffset="{{ 201 - (201 * $profileCompletion / 100) }}"
                                                    stroke-linecap="round" />
                                            </svg>
                                            <div class="absolute inset-0 flex items-center justify-center">
                                                <span class="text-xl font-bold text-orange-600">{{ $profileCompletion }}%</span>
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-800 text-center font-semibold">{{ __('Profile Completion') }}</p>
                                        
                                        <!-- Edit Profile Button -->
                                        <a href="{{ route('profile.edit') }}" class="px-4 py-1.5 bg-gradient-to-r from-amber-500 to-orange-600 text-white font-bold rounded-lg hover:from-amber-600 hover:to-orange-700 transform hover:scale-105 transition-all duration-200 flex items-center gap-1.5 text-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            {{ __('Edit Profile') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Header Section with Quick Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Welcome Message -->
            <div class="lg:col-span-2">
                <h1 class="text-4xl font-bold text-gray-900 mb-2">
                    {{ __('Welcome') }}، {{ Auth::user()->name }} 👋
                </h1>
                <p class="text-gray-600 text-lg">{{ __('Manage your listings and products') }}</p>
            </div>

            <!-- Quick Actions Card -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-xl p-6 h-full">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">{{ __('Quick Actions') }}</h3>
                    <div class="space-y-3">
                        <a href="{{ route('listings.create') }}" class="flex items-center p-3 bg-gradient-to-r from-[#6A8F3B] to-[#5a7a2f] text-white rounded-xl hover:shadow-lg transition transform hover:scale-105">
                            <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span class="font-bold">{{ __('Add New Product') }}</span>
                        </a>
                        <a href="{{ route('home') }}" class="flex items-center p-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition transform hover:scale-105">
                            <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span class="font-bold">{{ __('Browse Marketplace') }}</span>
                        </a>
                        <a href="{{ route('profile.edit') }}" class="flex items-center p-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition transform hover:scale-105">
                            <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="font-bold">{{ __('Settings') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Listings -->
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium mb-1">{{ __('Total Listings') }}</p>
                        <p class="text-4xl font-bold">{{ $listings->total() }}</p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Active Listings -->
            <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium mb-1">{{ __('Active Listings') }}</p>
                        <p class="text-4xl font-bold">{{ $activeListings }}</p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Pending Listings -->
            <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-amber-100 text-sm font-medium mb-1">{{ __('Pending Listings') }}</p>
                        <p class="text-4xl font-bold">{{ $pendingListings }}</p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Profile Completion -->
            <div class="bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium mb-1">{{ __('Profile Completion') }}</p>
                        <p class="text-4xl font-bold">{{ $profileCompletion }}%</p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Listings Section (2/3 width) -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-[#6A8F3B] to-[#5a7a2f] px-6 py-5 flex items-center justify-between">
                        <h2 class="text-2xl font-bold text-white flex items-center">
                            <svg class="w-7 h-7 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            {{ __('My Listings') }}
                        </h2>
                        <a href="{{ route('listings.create') }}" class="bg-white text-[#6A8F3B] px-6 py-2 rounded-xl font-bold hover:shadow-lg transition flex items-center">
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            {{ __('Add New Product') }}
                        </a>
                    </div>

                    <!-- Search & Filter -->
                    <div class="px-6 py-4 bg-gray-50 border-b flex gap-4">
                        <div class="flex-1">
                            <input type="text" placeholder="🔍 {{ __('Search in your listings...') }}" class="w-full px-4 py-2 rounded-xl border-2 border-gray-200 focus:border-[#6A8F3B] focus:outline-none">
                        </div>
                        <select class="px-4 py-2 rounded-xl border-2 border-gray-200 focus:border-[#6A8F3B] focus:outline-none">
                            <option value="">{{ __('All Statuses') }}</option>
                            <option value="active">{{ __('Active') }}</option>
                            <option value="pending">{{ __('Pending') }}</option>
                            <option value="sold">{{ __('Sold') }}</option>
                        </select>
                    </div>

                    <!-- Listings Grid -->
                    <div class="p-6">
                        @if($listings->count() > 0)
                            <div class="space-y-4">
                                @foreach($listings as $listing)
                                    <div class="group border-2 border-gray-200 rounded-2xl overflow-hidden hover:border-[#6A8F3B] hover:shadow-xl transition-all duration-300">
                                        <div class="flex flex-col sm:flex-row">
                                            <!-- Product Image -->
                                            <div class="w-full sm:w-48 h-48 sm:h-40 bg-gradient-to-br from-[#6A8F3B] to-[#C8A356] flex items-center justify-center flex-shrink-0 relative overflow-hidden">
                                                @php
                                                    // Try to get image from product media first, then listing media
                                                    $productImage = null;
                                                    if($listing->product && $listing->product->media && is_array($listing->product->media) && count($listing->product->media) > 0) {
                                                        $productImage = $listing->product->media[0];
                                                    } elseif($listing->media && is_array($listing->media) && count($listing->media) > 0) {
                                                        $productImage = $listing->media[0];
                                                    }
                                                @endphp
                                                
                                                @if($productImage)
                                                    <!-- Display actual product image -->
                                                    <img src="{{ Storage::url($productImage) }}" 
                                                         alt="{{ $listing->product ? $listing->product->variety : __('Product Image') }}" 
                                                         class="w-full h-full object-cover"
                                                         loading="lazy">
                                                    <!-- Product Type Badge -->
                                                    @if($listing->product)
                                                        @if($listing->product->type === 'oil')
                                                            <span class="absolute bottom-2 right-2 bg-white/90 text-[#6A8F3B] px-2 py-1 rounded-lg text-xs font-bold shadow-md">🫗 {{ __('Oil') }}</span>
                                                        @else
                                                            <span class="absolute bottom-2 right-2 bg-white/90 text-[#6A8F3B] px-2 py-1 rounded-lg text-xs font-bold shadow-md">🫒 {{ __('Olives') }}</span>
                                                        @endif
                                                    @endif
                                                @else
                                                    <!-- Fallback to icon if no image -->
                                                    @if($listing->product)
                                                        @if($listing->product->type === 'oil')
                                                            <svg class="w-20 h-20 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                                            </svg>
                                                            <span class="absolute bottom-2 right-2 bg-white/90 text-[#6A8F3B] px-2 py-1 rounded-lg text-xs font-bold">🫗 {{ __('Oil') }}</span>
                                                        @else
                                                            <svg class="w-20 h-20 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                                                            </svg>
                                                            <span class="absolute bottom-2 right-2 bg-white/90 text-[#6A8F3B] px-2 py-1 rounded-lg text-xs font-bold">🫒 {{ __('Olives') }}</span>
                                                        @endif
                                                    @endif
                                                @endif
                                            </div>

                                            <!-- Product Details -->
                                            <div class="flex-1 p-5">
                                                <div class="flex justify-between items-start mb-3">
                                                    <div>
                                                        <h3 class="text-xl font-bold text-gray-900 group-hover:text-[#6A8F3B] transition">
                                                            {{ $listing->product ? $listing->product->variety : __('Product') }}
                                                            @if($listing->product && $listing->product->quality)
                                                                <span class="text-sm text-gray-500">- {{ $listing->product->quality }}</span>
                                                            @endif
                                                        </h3>
                                                        <p class="text-sm text-gray-500 mt-1">
                                                            {{ $listing->created_at->diffForHumans() }}
                                                        </p>
                                                    </div>
                                                    
                                                    <!-- Status Badge -->
                                                    @if($listing->status === 'active')
                                                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold">✓ {{ __('Active') }}</span>
                                                    @elseif($listing->status === 'pending')
                                                        <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-bold">⏳ {{ __('Pending') }}</span>
                                                    @elseif($listing->status === 'sold')
                                                        <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-xs font-bold">✓ {{ __('Sold') }}</span>
                                                    @else
                                                        <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-bold">✕ {{ __('Inactive') }}</span>
                                                    @endif
                                                </div>

                                                <!-- Product Info -->
                                                <div class="grid grid-cols-2 gap-4 mb-4">
                                                    <div class="flex items-center text-gray-700">
                                                        <svg class="w-5 h-5 ml-2 text-[#6A8F3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                        </svg>
                                                        <span class="font-medium">{{ __('Quantity:') }}</span>
                                                        <span class="mr-1">{{ $listing->product ? $listing->product->quantity : '-' }}</span>
                                                    </div>
                                                    <div class="flex items-center text-gray-700">
                                                        <svg class="w-5 h-5 ml-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        <span class="font-medium">{{ __('Price:') }}</span>
                                                        <span class="mr-1 text-green-600 font-bold">
                                                            {{ $listing->price ?? '-' }} {{ $listing->currency ?? 'TND' }}
                                                        </span>
                                                    </div>
                                                </div>

                                                <!-- Location Info -->
                                                @if(Auth::user()->addresses->first())
                                                    @php
                                                        $address = Auth::user()->addresses->first();
                                                    @endphp
                                                    <div class="flex items-center text-gray-600 mb-4">
                                                        <svg class="w-5 h-5 ml-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                        </svg>
                                                        <span class="text-sm">
                                                            📍 {{ $address->governorate ?? '' }}
                                                            @if($address->delegation), {{ $address->delegation }}@endif
                                                            @if($address->lat && $address->lng)
                                                                <span class="text-green-600 font-medium mr-2">✓ GPS</span>
                                                            @endif
                                                        </span>
                                                    </div>
                                                @endif

                                                <!-- Actions -->
                                                <div class="flex gap-2">
                                                    <a href="{{ url('/listings/' . $listing->id) }}" 
                                                       class="flex-1 text-center bg-[#6A8F3B] text-white px-4 py-2 rounded-lg hover:bg-[#5a7a2f] transition font-medium text-sm">
                                                        👁️ {{ __('View') }}
                                                    </a>
                                                    <a href="{{ url('/listings/' . $listing->id . '/edit') }}" 
                                                       class="flex-1 text-center bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition font-medium text-sm">
                                                        ✏️ {{ __('Edit') }}
                                                    </a>
                                                    <form action="{{ url('/listings/' . $listing->id) }}" method="POST" class="flex-1" onsubmit="return confirm('{{ __('Are you sure you want to delete this listing?') }}')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="w-full text-center bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition font-medium text-sm">
                                                            🗑️ {{ __('Delete') }}
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Pagination -->
                            <div class="mt-6">
                                {{ $listings->links() }}
                            </div>
                        @else
                            <!-- Empty State -->
                            <div class="text-center py-16">
                                <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                <h3 class="text-xl font-bold text-gray-600 mb-2">{{ __('No listings yet') }}</h3>
                                <p class="text-gray-500 mb-6">{{ __('Start by adding your first product!') }}</p>
                                <a href="{{ route('listings.create') }}" class="inline-flex items-center bg-[#6A8F3B] text-white px-8 py-3 rounded-xl font-bold hover:shadow-lg transition">
                                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    {{ __('Add New Product') }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar (1/3 width) -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Tips Card -->
                <div class="bg-gradient-to-br from-amber-50 to-orange-50 border-2 border-amber-200 rounded-2xl p-6">
                    <div class="flex items-start">
                        <div class="bg-amber-500 rounded-full p-2 ml-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 mb-2">💡 {{ __('Tip') }}</h4>
                            <p class="text-sm text-gray-700">
                                {{ __('Add clear photos and detailed descriptions to your products to increase sales opportunities!') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
