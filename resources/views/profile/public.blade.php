@php
    $locale = app()->getLocale();
    $isRTL = $locale === 'ar';
    // Normalize stored paths to avoid double "storage/" prefixes and support full URLs
    $normalizePath = function ($path) {
        if (!$path) {
            return null;
        }

        if (str_starts_with($path, 'http')) {
            return $path;
        }

        $cleaned = ltrim(preg_replace('/^storage\\//', '', $path), '/');

        return \Illuminate\Support\Facades\Storage::url($cleaned);
    };

    $coverPhotos = collect($user->cover_photos ?? [])->map(function($p) use ($normalizePath) {
        if (is_array($p)) {
            $candidate = $p['path'] ?? $p['url'] ?? ($p[0] ?? null);
            return $normalizePath($candidate);
        }
        return $normalizePath($p);
    })->filter()->values();

    $profilePhotoUrl = $normalizePath($user->profile_picture) ?? asset('images/zintoop-logo.png');
@endphp
@php
    $isOwner = auth()->check() && auth()->id() === $user->id;
    // Basic contact info fallback to avoid undefined variables in view
    $contactInfo = [
        'phone' => $user->phone ?? $user->phone_number ?? null,
        'email' => $user->email ?? null,
    ];
@endphp

<x-app-layout>
    <div class="min-h-screen bg-gray-100" dir="{{ $isRTL ? 'rtl' : 'ltr' }}">
        
        {{-- Facebook-Style Profile Header --}}
        <div class="bg-white shadow-sm">
            <div class="max-w-5xl mx-auto">
                {{-- Cover Photo Section --}}
                <div class="relative">
                    {{-- Cover Photo Container --}}
                    <div class="h-[200px] sm:h-[280px] md:h-[350px] rounded-b-xl overflow-hidden relative">
                        {{-- Default gradient background (shows when no cover photo) --}}
                        <div class="absolute inset-0 bg-gradient-to-br from-emerald-500 via-teal-500 to-cyan-500"></div>
                        
                        @if($coverPhotos->count() > 0)
                            <img src="{{ $coverPhotos[0] }}" 
                                 alt="Cover" 
                                 class="absolute inset-0 w-full h-full object-cover z-10"
                                 loading="lazy">
                        @else
                            {{-- Simple decorative overlay for default cover --}}
                            <div class="absolute inset-0 bg-gradient-to-tr from-amber-400/20 to-transparent"></div>
                        @endif
                        
                        {{-- Cover Photo Actions (for owner) --}}
                        @if($isOwner)
                            <a href="{{ route('profile.edit') }}#photos" 
                               class="absolute bottom-4 {{ $isRTL ? 'left-4' : 'right-4' }} z-10 px-4 py-2 bg-white/90 hover:bg-white text-gray-800 rounded-lg font-medium text-sm flex items-center gap-2 shadow-lg backdrop-blur-sm transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ __('Edit Cover') }}
                            </a>
                        @endif
                    </div>
                    
                    {{-- Profile Picture - Higher z-index to appear above cover --}}
                    <div class="absolute {{ $isRTL ? 'right-4 sm:right-8' : 'left-4 sm:left-8' }} -bottom-16 sm:-bottom-20 z-20">
                        <div class="relative group">
                            @if($profilePhotoUrl)
                                <img src="{{ $profilePhotoUrl }}" 
                                     alt="{{ $user->name }}" 
                                     class="w-32 h-32 sm:w-40 sm:h-40 rounded-full object-cover ring-4 ring-white shadow-2xl bg-white"
                                     loading="lazy"
                                     onerror="this.src='{{ asset('images/zintoop-logo.png') }}'">
                            @else
                                <div class="w-32 h-32 sm:w-40 sm:h-40 rounded-full bg-gradient-to-br from-emerald-500 via-teal-500 to-cyan-500 flex items-center justify-center text-white text-4xl sm:text-5xl font-bold ring-4 ring-white shadow-2xl">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                            
                            {{-- Online Indicator --}}
                            <div class="absolute bottom-2 {{ $isRTL ? 'left-2' : 'right-2' }} w-5 h-5 sm:w-6 sm:h-6 bg-green-500 rounded-full ring-3 ring-white"></div>
                            
                            {{-- Edit button for owner --}}
                            @if($isOwner)
                                <a href="{{ route('profile.edit') }}" 
                                   class="absolute inset-0 flex items-center justify-center bg-black/50 rounded-full opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                
                {{-- Profile Info Section --}}
                <div class="pt-20 sm:pt-24 pb-4 px-4 sm:px-8">
                    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                        {{-- Left: Name & Info --}}
                        <div class="flex-1">
                            {{-- Name --}}
                            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 flex items-center gap-2 flex-wrap">
                                {{ $user->name }}
                                {{-- Verified Badge (optional) --}}
                                @if($user->trust_score > 80)
                                    <svg class="w-6 h-6 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                @endif
                            </h1>
                            
                            {{-- Role-specific name / Bio --}}
                            <p class="text-gray-500 mt-1 text-base sm:text-lg">
                                @if($user->role === 'farmer' && ($user->farm_name || $user->farm_name_ar))
                                    🌾 {{ $user->farm_name ?? $user->farm_name_ar }}
                                @elseif($user->role === 'mill' && $user->mill_name)
                                    🏭 {{ $user->mill_name }}
                                @elseif($user->role === 'carrier' && $user->company_name)
                                    🚛 {{ $user->company_name }}
                                @elseif($user->role === 'packer' && $user->packer_name)
                                    📦 {{ $user->packer_name }}
                                @else
                                    {{ $user->role === 'farmer' ? __('Olive grower') : ($user->role === 'mill' ? __('Oil mill') : ($user->role === 'carrier' ? __('Transporter') : ($user->role === 'packer' ? __('Packaging') : __('Member')))) }}
                                @endif
                            </p>
                            
                            {{-- Rating --}}
                            @if($user->rating_avg > 0)
                                <div class="flex items-center gap-2 mt-2">
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 {{ $i <= round($user->rating_avg) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor
                                    </div>
                                    <span class="text-gray-600 text-sm">
                                        {{ number_format($user->rating_avg, 1) }} · {{ $user->rating_count }} {{ __('reviews') }}
                                    </span>
                                </div>
                            @endif
                        </div>
                        
                        {{-- Right: Action Buttons --}}
                        <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                            {{-- Like, Follow & Message Buttons (for non-owners) --}}
                            @if(!$isOwner)
                                <div x-data="userInteraction()" x-init="init()" class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                                    {{-- Like Button with confirmation --}}
                                    <button @click="confirmLike()"
                                            :disabled="loading"
                                            :class="liked ? 'bg-gradient-to-r from-rose-500 to-pink-500 text-white shadow-lg shadow-rose-500/30' : 'bg-white text-gray-700 hover:bg-rose-50 border border-gray-200'"
                                            class="px-2.5 sm:px-4 py-2 sm:py-2.5 rounded-lg sm:rounded-xl font-semibold text-xs sm:text-sm flex items-center gap-1.5 sm:gap-2 transition-all duration-300 transform hover:scale-105 disabled:opacity-50">
                                        <svg :class="liked ? 'text-white fill-current' : 'text-rose-500'" class="w-4 h-4 sm:w-5 sm:h-5 transition-transform duration-300" :style="liked ? 'transform: scale(1.2)' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path :fill="liked ? 'currentColor' : 'none'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                        <span class="hidden sm:inline" x-text="liked ? '{{ __('Liked') }}' : '{{ __('Like') }}'"></span>
                                        <span class="px-1 sm:px-1.5 py-0.5 rounded-full text-[10px] sm:text-xs" :class="liked ? 'bg-white/20' : 'bg-gray-100'" x-text="likeCount"></span>
                                    </button>
                                    
                                    {{-- Follow Button with confirmation --}}
                                    <button @click="confirmFollow()"
                                            :disabled="loading"
                                            :class="followed ? 'bg-gradient-to-r from-blue-500 to-indigo-500 text-white shadow-lg shadow-blue-500/30' : 'bg-white text-gray-700 hover:bg-blue-50 border border-gray-200'"
                                            class="px-2.5 sm:px-4 py-2 sm:py-2.5 rounded-lg sm:rounded-xl font-semibold text-xs sm:text-sm flex items-center gap-1.5 sm:gap-2 transition-all duration-300 transform hover:scale-105 disabled:opacity-50">
                                        <svg :class="followed ? 'text-white' : 'text-blue-500'" class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path x-show="followed" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            <path x-show="!followed" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                        </svg>
                                        <span class="hidden sm:inline" x-text="followed ? '{{ __('Following') }}' : '{{ __('Follow') }}'"></span>
                                        <span class="px-1 sm:px-1.5 py-0.5 rounded-full text-[10px] sm:text-xs" :class="followed ? 'bg-white/20' : 'bg-gray-100'" x-text="followerCount"></span>
                                    </button>
                                    
                                    {{-- Message Button --}}
                                    <a href="{{ auth()->check() ? route('messages.show', $user) : route('login') }}"
                                       class="px-2.5 sm:px-4 py-2 sm:py-2.5 rounded-lg sm:rounded-xl font-semibold text-xs sm:text-sm flex items-center gap-1.5 sm:gap-2 transition-all duration-300 transform hover:scale-105 bg-gradient-to-r from-emerald-500 to-teal-500 text-white shadow-lg shadow-emerald-500/30">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>
                                        <span class="hidden xs:inline">{{ __('Message') }}</span>
                                    </a>
                                </div>
                            @endif
                            
                            {{-- Role Badge --}}
                            <span class="px-4 py-2 rounded-full text-white font-semibold text-sm shadow-lg transform hover:scale-105 transition-transform
                                {{ $user->role === 'farmer' ? 'bg-gradient-to-r from-emerald-500 via-green-500 to-teal-500 shadow-emerald-500/30' : '' }}
                                {{ $user->role === 'carrier' ? 'bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500 shadow-blue-500/30' : '' }}
                                {{ $user->role === 'mill' ? 'bg-gradient-to-r from-amber-500 via-orange-500 to-yellow-500 shadow-amber-500/30' : '' }}
                                {{ $user->role === 'packer' ? 'bg-gradient-to-r from-purple-500 via-fuchsia-500 to-pink-500 shadow-purple-500/30' : '' }}
                                {{ !in_array($user->role, ['farmer', 'carrier', 'mill', 'packer']) ? 'bg-gradient-to-r from-gray-500 via-slate-500 to-gray-600 shadow-gray-500/30' : '' }}
                            ">
                                {{ $user->role === 'farmer' ? '🌾 ' . __('Farmer') : '' }}
                                {{ $user->role === 'carrier' ? '🚛 ' . __('Carrier') : '' }}
                                {{ $user->role === 'mill' ? '🏭 ' . __('Mill') : '' }}
                                {{ $user->role === 'packer' ? '📦 ' . __('Packer') : '' }}
                                {{ $user->role === 'normal' ? __('Member') : '' }}
                                {{ $user->role === 'admin' ? '⭐ ' . __('Admin') : '' }}
                            </span>
                            
                            @if(!$isOwner && $contactInfo['phone'])
                                <a href="tel:{{ $contactInfo['phone'] }}" 
                                   class="px-5 py-2.5 bg-[#6A8F3B] hover:bg-[#5a7a2f] text-white rounded-lg font-semibold text-sm flex items-center gap-2 shadow-md transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    {{ __('Contact') }}
                                </a>
                            @endif
                            
                            @if($isOwner)
                                <a href="{{ route('profile.edit') }}" 
                                   class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg font-semibold text-sm flex items-center gap-2 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                    {{ __('Edit Profile') }}
                                </a>
                            @endif
                        </div>
                    </div>
                    
                    {{-- Stats Bar --}}
                    <div class="flex flex-wrap items-center gap-6 mt-6 pt-4 border-t border-gray-200">
                        <div class="flex items-center gap-2 group cursor-pointer">
                            <span class="text-xl font-bold bg-gradient-to-r from-emerald-500 to-teal-500 bg-clip-text text-transparent group-hover:scale-110 transition-transform">{{ $activeListings }}</span>
                            <span class="text-gray-600 text-sm">{{ __('Active Listings') }}</span>
                        </div>
                        <div class="flex items-center gap-2 group cursor-pointer">
                            <span class="text-xl font-bold bg-gradient-to-r from-slate-600 to-gray-700 bg-clip-text text-transparent group-hover:scale-110 transition-transform">{{ $totalListings }}</span>
                            <span class="text-gray-600 text-sm">{{ __('Total Listings') }}</span>
                        </div>
                        @if($user->trust_score > 0)
                            <div class="flex items-center gap-2 group cursor-pointer">
                                <span class="text-xl font-bold bg-gradient-to-r from-blue-500 to-indigo-500 bg-clip-text text-transparent group-hover:scale-110 transition-transform">{{ number_format($user->trust_score, 1) }}%</span>
                                <span class="text-gray-600 text-sm">{{ __('Trust Score') }}</span>
                            </div>
                        @endif
                        @if($coverPhotos->count() > 1)
                            <div class="flex items-center gap-2 group cursor-pointer">
                                <span class="text-xl font-bold bg-gradient-to-r from-amber-500 to-orange-500 bg-clip-text text-transparent group-hover:scale-110 transition-transform">{{ $coverPhotos->count() }}</span>
                                <span class="text-gray-600 text-sm">{{ __('Photos') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Main Content Area --}}
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

            {{-- Owner quick actions --}}
            @if($isOwner)
                <div class="bg-white rounded-2xl shadow-md border border-gray-100 px-4 py-3 mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">{{ __('Share fresh updates to boost your profile') }}</p>
                        <p class="text-sm text-gray-500">{{ __('Add a story (image or video) or update your gallery photos.') }}</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a href="#stories" class="px-4 py-2 bg-[#6A8F3B] text-white rounded-lg text-sm font-semibold hover:bg-[#5a7a2f] shadow-sm">{{ __('Add Story') }}</a>
                        <a href="{{ route('profile.edit') }}#photos" class="px-4 py-2 bg-white text-[#6A8F3B] border border-[#6A8F3B]/40 rounded-lg text-sm font-semibold hover:bg-[#6A8F3B]/5 shadow-sm">{{ __('Manage Photos') }}</a>
                    </div>
                </div>
            @endif

            {{-- Modern Stories Section --}}
            <div x-data="{
                    stories: [],
                    current: null,
                    currentIndex: 0,
                    loading: true,
                    error: false,
                    userId: {{ $user->id }},
                    progress: 0,
                    timer: null,
                    fetchStories() {
                        fetch(`/user/${this.userId}/stories`)
                            .then(r => r.json())
                            .then(data => { this.stories = data; })
                            .catch(() => { this.error = true; })
                            .finally(() => { this.loading = false; });
                    },
                    openStory(story, index) {
                        this.current = story;
                        this.currentIndex = index;
                        this.startProgress();
                    },
                    closeStory() {
                        this.current = null;
                        this.stopProgress();
                    },
                    nextStory() {
                        if (this.currentIndex < this.stories.length - 1) {
                            this.currentIndex++;
                            this.current = this.stories[this.currentIndex];
                            this.startProgress();
                        } else {
                            this.closeStory();
                        }
                    },
                    prevStory() {
                        if (this.currentIndex > 0) {
                            this.currentIndex--;
                            this.current = this.stories[this.currentIndex];
                            this.startProgress();
                        }
                    },
                    startProgress() {
                        this.progress = 0;
                        this.stopProgress();
                        if (this.current?.media_type === 'image') {
                            this.timer = setInterval(() => {
                                this.progress += 2;
                                if (this.progress >= 100) this.nextStory();
                            }, 100);
                        }
                    },
                    stopProgress() {
                        if (this.timer) clearInterval(this.timer);
                    }
                }"
                x-init="fetchStories()"
                @keydown.escape.window="closeStory()"
                @keydown.arrow-right.window="current && nextStory()"
                @keydown.arrow-left.window="current && prevStory()"
                id="stories"
                class="relative mb-8">

                {{-- Stories Header --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#6A8F3B] to-[#C8A356] flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                                {{ __('Stories') }}
                                <span class="px-2.5 py-1 text-[10px] uppercase tracking-wide rounded-full bg-gradient-to-r from-green-500/10 to-amber-500/10 text-green-700 font-bold border border-green-200">{{ __('Live') }}</span>
                            </h2>
                            <p class="text-sm text-gray-500">{{ __('Fresh updates • Auto-expire in 48h') }}</p>
                        </div>
                    </div>
                    @if($isOwner)
                        <form method="POST" action="{{ route('stories.store') }}" enctype="multipart/form-data" class="flex flex-wrap items-center gap-2">
                            @csrf
                            <label class="group inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-[#6A8F3B] to-[#5a7a2f] text-white rounded-xl text-sm font-semibold cursor-pointer hover:shadow-lg hover:scale-105 transition-all duration-300">
                                <svg class="w-5 h-5 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                <span>{{ __('Add Story') }}</span>
                                <input type="file" name="media" accept="image/*,video/*" class="hidden" required onchange="this.form.submit()">
                            </label>
                            <input type="text" name="caption" maxlength="200" placeholder="{{ __('Caption (optional)') }}" class="border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm w-full sm:w-56 focus:ring-2 focus:ring-[#6A8F3B]/30 focus:border-[#6A8F3B] transition-all">
                        </form>
                    @endif
                </div>

                {{-- Error State --}}
                <template x-if="error">
                    <div class="bg-red-50 border border-red-200 rounded-2xl p-4 flex items-center gap-3 text-red-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span class="text-sm font-medium">{{ __('Could not load stories. Please try again later.') }}</span>
                    </div>
                </template>

                {{-- Loading State --}}
                <template x-if="loading && !error">
                    <div class="flex gap-4 overflow-hidden">
                        <template x-for="i in 5" :key="i">
                            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full bg-gradient-to-br from-gray-200 to-gray-300 animate-pulse flex-shrink-0"></div>
                        </template>
                    </div>
                </template>

                {{-- Empty State --}}
                <template x-if="!loading && !error && stories.length === 0">
                    <div class="bg-gradient-to-br from-gray-50 to-gray-100 border-2 border-dashed border-gray-200 rounded-2xl p-8 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-200 flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                        </div>
                        <p class="text-gray-600 font-medium">{{ __('No stories yet') }}</p>
                        <p class="text-sm text-gray-400 mt-1">{{ __('Check back soon for updates!') }}</p>
                    </div>
                </template>

                {{-- Stories Strip (Instagram-style) --}}
                <div x-show="!loading && stories.length > 0" class="flex gap-4 overflow-x-auto pb-4 scrollbar-hide" style="scrollbar-width: none; -ms-overflow-style: none;">
                    <template x-for="(story, index) in stories" :key="story.id">
                        <button type="button" 
                                class="group relative flex-shrink-0 focus:outline-none"
                                @click="openStory(story, index)">
                            {{-- Gradient Ring --}}
                            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full p-[3px] bg-gradient-to-tr from-[#6A8F3B] via-[#C8A356] to-[#6A8F3B] group-hover:scale-110 group-hover:shadow-xl transition-all duration-300">
                                <div class="w-full h-full rounded-full p-[2px] bg-white">
                                    <div class="w-full h-full rounded-full overflow-hidden">
                                        <template x-if="story.media_type === 'image'">
                                            <img :src="story.url" alt="story" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="story.media_type === 'video'">
                                            <div class="w-full h-full bg-gradient-to-br from-gray-800 to-gray-900 flex items-center justify-center">
                                                <svg class="w-8 h-8 text-white/90" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            {{-- Caption Badge --}}
                            <div x-show="story.caption" class="absolute -bottom-1 left-1/2 -translate-x-1/2 max-w-[90%]">
                                <span class="block text-[10px] text-gray-600 bg-white/95 backdrop-blur rounded-full px-2 py-0.5 shadow-sm truncate border border-gray-100" x-text="story.caption"></span>
                            </div>
                        </button>
                    </template>
                </div>

                {{-- Fullscreen Story Viewer --}}
                <div x-show="current" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 z-[100] flex items-center justify-center bg-black/90 backdrop-blur-md" 
                     style="display: none;">
                    
                    {{-- Progress Bar (for images) --}}
                    <div class="absolute top-4 left-4 right-4 flex gap-1">
                        <template x-for="(s, i) in stories" :key="s.id">
                            <div class="flex-1 h-1 bg-white/30 rounded-full overflow-hidden">
                                <div class="h-full bg-white rounded-full transition-all duration-100"
                                     :style="{
                                         width: i < currentIndex ? '100%' : (i === currentIndex ? progress + '%' : '0%')
                                     }"></div>
                            </div>
                        </template>
                    </div>

                    {{-- Navigation Areas --}}
                    <button @click="prevStory()" class="absolute left-0 top-0 bottom-0 w-1/3 z-10 cursor-pointer focus:outline-none" x-show="currentIndex > 0"></button>
                    <button @click="nextStory()" class="absolute right-0 top-0 bottom-0 w-1/3 z-10 cursor-pointer focus:outline-none"></button>

                    {{-- Content --}}
                    <div class="relative max-w-lg w-full mx-4">
                        {{-- Close Button --}}
                        <button type="button" 
                                class="absolute -top-12 right-0 text-white/80 hover:text-white p-2 rounded-full hover:bg-white/10 transition z-20"
                                @click="closeStory()" 
                                aria-label="Close">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>

                        {{-- Image --}}
                        <template x-if="current && current.media_type === 'image'">
                            <img :src="current.url" alt="story" class="w-full max-h-[75vh] object-contain rounded-3xl shadow-2xl">
                        </template>
                        
                        {{-- Video --}}
                        <template x-if="current && current.media_type === 'video'">
                            <video :src="current.url" controls autoplay playsinline class="w-full max-h-[75vh] rounded-3xl shadow-2xl bg-black"></video>
                        </template>

                        {{-- Caption --}}
                        <div x-show="current?.caption" class="absolute bottom-0 left-0 right-0 p-6 bg-gradient-to-t from-black/80 to-transparent rounded-b-3xl">
                            <p class="text-white text-lg font-medium" x-text="current?.caption"></p>
                        </div>
                    </div>

                    {{-- Navigation Arrows --}}
                    <button x-show="currentIndex > 0" @click="prevStory()" class="absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center text-white transition z-20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                    </button>
                    <button x-show="currentIndex < stories.length - 1" @click="nextStory()" class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center text-white transition z-20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </button>
                </div>
            </div>

            {{-- Trees Highlight (for farmers) --}}
            @if($user->role === 'farmer' && $user->tree_number)
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-2">
                        <div class="relative h-56 md:h-full">
                            @if($coverPhotos->count() > 1)
                                <img src="{{ Storage::url($coverPhotos[1]) }}" alt="Farm photo" class="w-full h-full object-cover">
                            @elseif($coverPhotos->count() > 0)
                                <img src="{{ Storage::url($coverPhotos[0]) }}" alt="Farm photo" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-green-700 to-amber-500 flex items-center justify-center">
                                    <svg class="w-24 h-24 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M12 22v-6m0 0a6 6 0 016-6h.5m-6 6a6 6 0 00-6-6H5m6 0V7m0 3a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v1a4 4 0 01-4 4z" />
                                    </svg>
                                </div>
                            @endif

                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-black/20 to-transparent"></div>
                            <div class="absolute bottom-4 {{ $isRTL ? 'left-4' : 'right-4' }} bg-white/90 backdrop-blur px-4 py-2 rounded-full text-sm font-semibold text-green-800 shadow-lg flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 22v-6m0 0a6 6 0 016-6h.5m-6 6a6 6 0 00-6-6H5m6 0V7m0 3a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v1a4 4 0 01-4 4z" />
                                </svg>
                                {{ __('Olive farm') }}
                            </div>
                        </div>

                        <div class="p-6 md:p-8 flex flex-col justify-center gap-4">
                            <div class="text-sm uppercase tracking-wide text-gray-500 font-semibold">
                                {{ __('Number of olive trees') }}
                            </div>
                            <div class="text-5xl md:text-6xl font-extrabold text-green-700 leading-tight">
                                {{ number_format($user->tree_number) }}
                            </div>
                            <p class="text-gray-700 text-base md:text-lg">
                                {{ __('Olive trees cared for by this grower and shared with the community.') }}
                            </p>

                            <div class="flex flex-wrap gap-2 mt-2">
                                @if($user->olive_type)
                                    <span class="px-3 py-1 rounded-full bg-green-50 text-green-700 text-sm font-semibold">
                                        {{ $user->olive_type }}
                                    </span>
                                @endif
                                @if($user->farm_location)
                                    <span class="px-3 py-1 rounded-full bg-amber-50 text-amber-700 text-sm font-semibold">
                                        {{ $user->farm_location }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Modern Gallery Section --}}
            @if($coverPhotos->count() > 0)
                @php
                    $galleryPhotos = $coverPhotos->take(9);
                @endphp
                <div x-data="{ selectedPhoto: null, selectedIndex: 0 }" 
                     class="relative mb-8" 
                     @keydown.escape.window="selectedPhoto = null"
                     @keydown.arrow-right.window="selectedPhoto && selectedIndex < {{ $galleryPhotos->count() - 1 }} && (selectedIndex++, selectedPhoto = '{{ Storage::url($galleryPhotos[0]) }}'.replace('{{ $galleryPhotos[0] }}', {{ json_encode($galleryPhotos->values()->toArray()) }}[selectedIndex]))"
                     @keydown.arrow-left.window="selectedPhoto && selectedIndex > 0 && (selectedIndex--, selectedPhoto = {{ json_encode($galleryPhotos->map(fn($p) => Storage::url($p))->values()->toArray()) }}[selectedIndex])">
                    
                    {{-- Gallery Header --}}
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">{{ __('Gallery') }}</h2>
                                <p class="text-sm text-gray-500">{{ $galleryPhotos->count() }} {{ __('photos') }}</p>
                            </div>
                        </div>
                        @if($isOwner)
                            <a href="{{ route('profile.edit') }}#photos" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border-2 border-gray-200 text-gray-700 rounded-xl text-sm font-semibold hover:border-amber-300 hover:bg-amber-50 transition-all duration-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                {{ __('Add Photos') }}
                            </a>
                        @endif
                    </div>

                    {{-- Modern Masonry-style Grid --}}
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                        @foreach($galleryPhotos as $index => $photo)
                            <button type="button" 
                                    class="group relative aspect-square overflow-hidden rounded-2xl bg-gray-100 focus:outline-none focus:ring-4 focus:ring-amber-300/50 {{ $index === 0 ? 'sm:col-span-2 sm:row-span-2' : '' }}"
                                    @click="selectedPhoto = '{{ Storage::url($photo) }}'; selectedIndex = {{ $index }}">
                                <img src="{{ Storage::url($photo) }}" 
                                     alt="Photo {{ $index + 1 }}" 
                                     class="w-full h-full object-cover transition-all duration-500 group-hover:scale-110">
                                {{-- Hover Overlay --}}
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/0 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-300">
                                    <div class="absolute bottom-3 left-3 right-3 flex items-center justify-between text-white">
                                        <span class="text-sm font-medium">{{ __('View') }}</span>
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                                        </svg>
                                    </div>
                                </div>
                                {{-- Corner Badge for first image --}}
                                @if($index === 0)
                                    <div class="absolute top-3 {{ $isRTL ? 'left-3' : 'right-3' }}">
                                        <span class="px-2 py-1 bg-white/90 backdrop-blur text-xs font-semibold text-gray-700 rounded-lg shadow">{{ __('Featured') }}</span>
                                    </div>
                                @endif
                            </button>
                        @endforeach
                    </div>

                    {{-- Fullscreen Photo Viewer --}}
                    <div x-show="selectedPhoto" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 z-[100] flex items-center justify-center bg-black/90 backdrop-blur-md" 
                         style="display: none;">
                        
                        {{-- Counter --}}
                        <div class="absolute top-4 left-1/2 -translate-x-1/2 px-4 py-2 bg-white/20 backdrop-blur rounded-full text-white text-sm font-medium">
                            <span x-text="selectedIndex + 1"></span> / {{ $galleryPhotos->count() }}
                        </div>

                        {{-- Close Button --}}
                        <button type="button" 
                                class="absolute top-4 right-4 text-white/80 hover:text-white p-2 rounded-full hover:bg-white/10 transition z-20"
                                @click="selectedPhoto = null" 
                                aria-label="Close">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>

                        {{-- Image --}}
                        <div class="relative max-w-5xl w-full mx-4" @click.away="selectedPhoto = null">
                            <img :src="selectedPhoto" alt="Photo large" class="w-full max-h-[80vh] object-contain rounded-3xl shadow-2xl">
                        </div>

                        {{-- Navigation Arrows --}}
                        <button x-show="selectedIndex > 0" 
                                @click="selectedIndex--; selectedPhoto = {{ json_encode($galleryPhotos->map(fn($p) => Storage::url($p))->values()->toArray()) }}[selectedIndex]" 
                                class="absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center text-white transition z-20">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                        </button>
                        <button x-show="selectedIndex < {{ $galleryPhotos->count() - 1 }}" 
                                @click="selectedIndex++; selectedPhoto = {{ json_encode($galleryPhotos->map(fn($p) => Storage::url($p))->values()->toArray()) }}[selectedIndex]" 
                                class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center text-white transition z-20">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                        </button>
                    </div>
                </div>
            @endif
            
            {{-- Contact & Details Section --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                {{-- Contact Information --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">
                            {{ __('Contact Information') }}
                        </h2>
                        
                        @if($contactInfo['phone'])
                            <div class="flex items-center gap-3 mb-3 text-gray-700">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                <a href="tel:{{ $contactInfo['phone'] }}" class="hover:text-green-600 transition-colors">
                                    {{ $contactInfo['phone'] }}
                                </a>
                            </div>
                        @endif
                        
                        @if($contactInfo['email'])
                            <div class="flex items-center gap-3 mb-3 text-gray-700">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <a href="mailto:{{ $contactInfo['email'] }}" class="hover:text-green-600 transition-colors break-all">
                                    {{ $contactInfo['email'] }}
                                </a>
                            </div>
                        @endif
                        
                        @if($addresses->count() > 0)
                            <div class="flex items-start gap-3 text-gray-700">
                                <svg class="w-5 h-5 text-green-600 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <div>
                                    @foreach($addresses as $address)
                                        <p class="mb-1">{{ $address->governorate }}, {{ $address->delegation }}</p>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    {{-- Role-Specific Information --}}
                    @if(count($roleInfo) > 0)
                        <div class="bg-white rounded-xl shadow-lg p-6 mt-6">
                            <h2 class="text-xl font-bold text-gray-900 mb-4">
                                {{ __('Additional Details') }}
                            </h2>
                            
                            <div class="space-y-3 text-sm">
                                @foreach($roleInfo as $key => $value)
                                    @if($value)
                                        <div>
                                            <span class="font-semibold text-gray-700">
                                                {{ __(\Illuminate\Support\Str::title(str_replace('_', ' ', $key))) }}
                                            </span>: 
                                            <span class="text-gray-600">{{ $value }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
                
                {{-- Active Listings --}}
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">
                            {{ __('Active Listings') }}
                        </h2>
                        
                        @if($listings->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($listings as $listing)
                                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                        <h3 class="font-semibold text-lg text-gray-900 mb-2">
                                            {{ $listing->title }}
                                        </h3>
                                        
                                        @if($listing->product)
                                            <p class="text-sm text-gray-600 mb-2">
                                                {{ $listing->product->variety }} - {{ $listing->product->quality }}
                                            </p>
                                        @endif
                                        
                                        <div class="flex items-center justify-between">
                                            <span class="text-lg font-bold text-green-600">
                                                {{ number_format($listing->price, 2) }} {{ $listing->currency }}
                                            </span>
                                            
                                            <a href="{{ route('listings.show', $listing) }}" 
                                               class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                                {{ __('View Details') }} →
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            {{-- Pagination --}}
                            <div class="mt-6">
                                {{ $listings->links() }}
                            </div>
                        @else
                            <div class="text-center py-12 text-gray-500">
                                {{ __('No active listings at the moment') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
        </div>
    </div>

    @if(!$isOwner)
    <script>
        function userInteraction() {
            return {
                liked: false,
                followed: false,
                likeCount: 0,
                followerCount: 0,
                loading: false,
                userId: {{ $user->id }},
                isLoggedIn: {{ auth()->check() ? 'true' : 'false' }},
                
                init() {
                    // Fetch initial status
                    fetch(`/user/${this.userId}/interaction-status`)
                        .then(res => res.json())
                        .then(data => {
                            this.liked = data.has_liked;
                            this.followed = data.is_following;
                            this.likeCount = data.likes_count;
                            this.followerCount = data.followers_count;
                        })
                        .catch(err => console.error('Failed to load interaction status:', err));
                },
                
                confirmLike() {
                    if (!this.isLoggedIn) {
                        window.location.href = '{{ route('login') }}';
                        return;
                    }
                    
                    const action = this.liked ? '{{ __('Unlike this profile?') }}' : '{{ __('Like this profile?') }}';
                    if (confirm(action)) {
                        this.toggleLike();
                    }
                },
                
                confirmFollow() {
                    if (!this.isLoggedIn) {
                        window.location.href = '{{ route('login') }}';
                        return;
                    }
                    
                    const action = this.followed ? '{{ __('Unfollow this user?') }}' : '{{ __('Follow this user?') }}';
                    if (confirm(action)) {
                        this.toggleFollow();
                    }
                },
                
                toggleLike() {
                    this.loading = true;
                    fetch(`/user/${this.userId}/toggle-like`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            this.liked = data.liked;
                            this.likeCount = data.likes_count;
                        } else {
                            alert(data.message || '{{ __('An error occurred') }}');
                        }
                    })
                    .catch(err => {
                        console.error('Failed to toggle like:', err);
                        alert('{{ __('An error occurred') }}');
                    })
                    .finally(() => {
                        this.loading = false;
                    });
                },
                
                toggleFollow() {
                    this.loading = true;
                    fetch(`/user/${this.userId}/toggle-follow`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            this.followed = data.followed;
                            this.followerCount = data.followers_count;
                        } else {
                            alert(data.message || '{{ __('An error occurred') }}');
                        }
                    })
                    .catch(err => {
                        console.error('Failed to toggle follow:', err);
                        alert('{{ __('An error occurred') }}');
                    })
                    .finally(() => {
                        this.loading = false;
                    });
                }
            };
        }
    </script>
    @endif
</x-app-layout>
