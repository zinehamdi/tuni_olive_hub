@php
/** @var \App\Models\User $user */
@endphp
<section class="bg-white rounded-2xl sm:rounded-3xl shadow-2xl overflow-hidden">
    <!-- Header Section with Gradient -->
    <div class="bg-gradient-to-r from-[#1B2A1B] to-[#6A8F3B] p-4 sm:p-6 md:p-8">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-white/20 backdrop-blur-sm rounded-xl sm:rounded-2xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 sm:w-10 sm:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <h2 class="text-xl sm:text-2xl md:text-3xl font-bold text-white truncate">
                    {{ __('Profile Information') }}
                </h2>
                <p class="mt-1 text-sm sm:text-base text-white/90 line-clamp-2">
                    {{ __("Update your account's profile information and email address.") }}
                </p>
            </div>
        </div>
    </div>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="p-4 sm:p-6 md:p-8 space-y-6 sm:space-y-8" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <!-- Profile Picture Section -->
        <div class="bg-gradient-to-br from-gray-50 to-white p-4 sm:p-6 md:p-8 rounded-xl sm:rounded-2xl border-2 border-gray-100 hover:border-[#6A8F3B] transition-all duration-300">
            <div class="flex items-start sm:items-center gap-3 sm:gap-4 mb-4 sm:mb-6">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-[#6A8F3B] to-[#5a7a2f] rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-900">{{ __('Profile Picture') }}</h3>
                    <p class="text-sm sm:text-base text-gray-600 line-clamp-2">{{ __('Your profile photo will be shown on your public profile') }}</p>
                </div>
            </div>
            
            <div class="flex flex-col md:flex-row items-center gap-4 sm:gap-6">
                @if($user->profile_picture)
                    <img src="{{ Storage::url($user->profile_picture) }}" alt="Profile" class="w-24 h-24 sm:w-32 sm:h-32 rounded-xl sm:rounded-2xl object-cover border-4 border-white shadow-2xl ring-4 ring-[#6A8F3B]/20">
                @else
                    <div class="w-24 h-24 sm:w-32 sm:h-32 rounded-xl sm:rounded-2xl bg-gradient-to-br from-[#6A8F3B] to-[#5a7a2f] flex items-center justify-center text-white text-3xl sm:text-4xl font-bold shadow-2xl ring-4 ring-[#6A8F3B]/20">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
                <div class="flex-1 w-full">
                    <label for="profile_picture" class="block w-full">
                        <div class="relative group cursor-pointer">
                            <input type="file" id="profile_picture" name="profile_picture" accept="image/*" 
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            <div class="border-2 border-dashed border-gray-300 rounded-xl sm:rounded-2xl p-4 sm:p-6 text-center bg-white group-hover:border-[#6A8F3B] group-hover:bg-[#6A8F3B]/5 transition-all duration-300">
                                <svg class="w-10 h-10 sm:w-12 sm:h-12 mx-auto text-gray-400 group-hover:text-[#6A8F3B] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <p class="mt-2 sm:mt-3 text-xs sm:text-sm font-semibold text-gray-700">{{ __('Click to upload profile picture') }}</p>
                                <p class="mt-1 text-xs text-gray-500">📸 PNG, JPG, GIF, WebP • Max 5MB</p>
                            </div>
                        </div>
                    </label>
                </div>
            </div>
            <x-input-error class="mt-3" :messages="$errors->get('profile_picture')" />
        </div>

        <!-- Cover Photos Section (Slideshow) -->
        <div class="bg-gradient-to-br from-green-50 to-white p-4 sm:p-6 md:p-8 rounded-xl sm:rounded-2xl border-2 border-green-100 hover:border-green-300 transition-all duration-300">
            <div class="flex items-start sm:items-center gap-3 sm:gap-4 mb-4 sm:mb-6">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-900">
                        @if($user->role === 'farmer') <span class="hidden sm:inline">🌱</span> {{ __('Farm Cover Photos (Slideshow)') }}
                        @elseif($user->role === 'carrier') <span class="hidden sm:inline">🚚</span> {{ __('Fleet Cover Photos (Slideshow)') }}
                        @elseif($user->role === 'mill') <span class="hidden sm:inline">⚙️</span> {{ __('Mill Cover Photos (Slideshow)') }}
                        @elseif($user->role === 'packer') <span class="hidden sm:inline">📦</span> {{ __('Facility Cover Photos (Slideshow)') }}
                        @else <span class="hidden sm:inline">🖼️</span> {{ __('Cover Photos (Slideshow)') }}
                        @endif
                    </h3>
                    <p class="text-sm sm:text-base text-gray-600 mt-1 line-clamp-2">
                        @if($user->role === 'farmer') {{ __('Upload multiple photos of your farm, olive grove, or farming activity (displayed as slideshow)') }}
                        @elseif($user->role === 'carrier') {{ __('Upload multiple photos of your trucks, delivery fleet, or logistics operation (displayed as slideshow)') }}
                        @elseif($user->role === 'mill') {{ __('Upload multiple photos of your mill facility, machinery, or production line (displayed as slideshow)') }}
                        @elseif($user->role === 'packer') {{ __('Upload multiple photos of your packaging facility, bottling line, or warehouse (displayed as slideshow)') }}
                        @else {{ __('Upload multiple photos representing your business or activity (displayed as slideshow)') }}
                        @endif
                    </p>
                </div>
            </div>
            
            <div id="photos"></div>
            <!-- Current Cover Photos Preview -->
            @if($user->cover_photos && is_array($user->cover_photos) && count($user->cover_photos) > 0)
                <div class="mb-4 sm:mb-6">
                    <p class="text-xs sm:text-sm font-semibold text-gray-700 mb-2 sm:mb-3">{{ __('Current Photos') }} ({{ count($user->cover_photos) }}/5)</p>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 sm:gap-4">
                        @foreach($user->cover_photos as $index => $photo)
                            @if(is_string($photo))
                                <div class="relative group">
                                    <img src="{{ Storage::url($photo) }}" alt="Cover {{ $index + 1 }}" class="w-full h-24 sm:h-28 md:h-32 object-cover rounded-lg sm:rounded-xl border-2 border-gray-200 group-hover:border-green-500 transition-all duration-300">
                                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 rounded-lg sm:rounded-xl transition-all duration-300 flex items-center justify-center">
                                        <button type="button" onclick="removeCoverPhoto({{ $index }})" class="opacity-0 group-hover:opacity-100 bg-red-500 hover:bg-red-600 text-white rounded-full p-2 sm:p-3 transform scale-0 group-hover:scale-100 transition-all duration-300 shadow-lg">
                                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="absolute top-1 left-1 sm:top-2 sm:left-2 bg-green-500 text-white text-xs font-bold px-1.5 py-0.5 sm:px-2 sm:py-1 rounded-md sm:rounded-lg shadow-lg">
                                        #{{ $index + 1 }}
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                <input type="hidden" name="remove_cover_photos" id="remove_cover_photos" value="">
            @endif
            
            <!-- Upload New Photos -->
            <label for="cover_photos" class="block">
                <div class="relative group cursor-pointer">
                    <input type="file" id="cover_photos" name="cover_photos[]" accept="image/*" multiple
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                    <div class="border-2 border-dashed border-green-300 rounded-xl sm:rounded-2xl p-6 sm:p-8 text-center bg-white group-hover:border-green-500 group-hover:bg-green-50 transition-all duration-300">
                        <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-green-100 to-green-200 rounded-xl sm:rounded-2xl flex items-center justify-center mx-auto mb-3 sm:mb-4 group-hover:scale-110 transition-transform">
                            <svg class="w-8 h-8 sm:w-10 sm:h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <p class="text-base sm:text-lg font-bold text-gray-900">{{ __('Click to upload cover photos') }}</p>
                        <p class="mt-2 text-xs sm:text-sm text-gray-600">{{ __('Select multiple images') }}</p>
                        <div class="mt-3 sm:mt-4 flex flex-wrap items-center justify-center gap-3 sm:gap-6 text-xs text-gray-500">
                            <span class="flex items-center gap-1">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Max 5
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                5MB each
                            </span>
                            <span class="flex items-center gap-1">
                                🎬 Slideshow
                            </span>
                        </div>
                    </div>
                </div>
            </label>
            
            <!-- New Photos Preview -->
            <div id="new_photos_preview" class="hidden mt-4">
                <p class="text-xs sm:text-sm font-semibold text-green-700 mb-2 sm:mb-3">
                    <span class="inline-flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ __('Selected for upload') }}: <span id="selected_count">0</span> {{ __('photos') }}
                    </span>
                </p>
                <div id="preview_grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 sm:gap-4">
                    <!-- Preview images will be inserted here -->
                </div>
            </div>
            
            <x-input-error class="mt-3" :messages="$errors->get('cover_photos')" />
            <x-input-error class="mt-3" :messages="$errors->get('cover_photos.*')" />
        </div>

        <script>
            let photosToRemove = [];
            
            function removeCoverPhoto(index) {
                photosToRemove.push(index);
                document.getElementById('remove_cover_photos').value = JSON.stringify(photosToRemove);
                event.target.closest('.relative').style.display = 'none';
            }
            
            // Preview selected files
            document.getElementById('cover_photos').addEventListener('change', function(e) {
                const files = e.target.files;
                const previewContainer = document.getElementById('new_photos_preview');
                const previewGrid = document.getElementById('preview_grid');
                const selectedCount = document.getElementById('selected_count');
                
                // Clear previous previews
                previewGrid.innerHTML = '';
                
                if (files.length > 0) {
                    previewContainer.classList.remove('hidden');
                    selectedCount.textContent = files.length;
                    
                    Array.from(files).forEach((file, index) => {
                        if (file.type.startsWith('image/')) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                const div = document.createElement('div');
                                div.className = 'relative group';
                                div.innerHTML = `
                                    <img src="${e.target.result}" alt="Preview ${index + 1}" 
                                        class="w-full h-24 sm:h-28 md:h-32 object-cover rounded-lg sm:rounded-xl border-2 border-green-400">
                                    <div class="absolute top-1 left-1 sm:top-2 sm:left-2 bg-blue-500 text-white text-xs font-bold px-1.5 py-0.5 sm:px-2 sm:py-1 rounded-md sm:rounded-lg shadow-lg">
                                        {{ __('New') }}
                                    </div>
                                    <div class="absolute bottom-1 right-1 sm:bottom-2 sm:right-2 bg-black/70 text-white text-[10px] px-1.5 py-0.5 rounded">
                                        ${(file.size / 1024 / 1024).toFixed(2)} MB
                                    </div>
                                `;
                                previewGrid.appendChild(div);
                            };
                            reader.readAsDataURL(file);
                        }
                    });
                } else {
                    previewContainer.classList.add('hidden');
                }
            });
        </script>

        <!-- Basic Information Section -->
        <div class="bg-gradient-to-br from-blue-50 to-white p-4 sm:p-6 md:p-8 rounded-xl sm:rounded-2xl border-2 border-blue-100">
            <div class="flex items-start sm:items-center gap-3 sm:gap-4 mb-4 sm:mb-6">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-900">{{ __('Basic Information') }}</h3>
                    <p class="text-sm sm:text-base text-gray-600">{{ __('Your personal details and contact information') }}</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-xs sm:text-sm font-bold text-gray-900 mb-2">
                        {{ __('Name') }} <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <input type="text" id="name" name="name" 
                            value="{{ old('name', $user->name) }}"
                            required autofocus autocomplete="name"
                            class="w-full pl-10 sm:pl-12 pr-3 sm:pr-4 py-2.5 sm:py-3 text-sm sm:text-base border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all bg-white">
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-xs sm:text-sm font-bold text-gray-900 mb-2">
                        {{ __('Phone') }} <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <input type="text" id="phone" name="phone" 
                            value="{{ old('phone', $user->phone) }}"
                            required autocomplete="tel"
                            class="w-full pl-10 sm:pl-12 pr-3 sm:pr-4 py-2.5 sm:py-3 text-sm sm:text-base border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all bg-white">
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                </div>
            </div>

            <!-- Email (Full Width) -->
            <div class="mt-4 sm:mt-6">
                <label for="email" class="block text-xs sm:text-sm font-bold text-gray-900 mb-2">
                    {{ __('Email') }} <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <input type="email" id="email" name="email" 
                        value="{{ old('email', $user->email) }}"
                        required autocomplete="username"
                        class="w-full pl-10 sm:pl-12 pr-3 sm:pr-4 py-2.5 sm:py-3 text-sm sm:text-base border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all bg-white">
                </div>
                <x-input-error class="mt-2" :messages="$errors->get('email')" />

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-4 bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg">
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-yellow-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <div class="ml-3">
                                <p class="text-sm font-semibold text-yellow-800">{{ __('Your email address is unverified.') }}</p>
                                <button form="send-verification" class="mt-2 text-sm underline text-yellow-700 hover:text-yellow-900 font-semibold">
                                    {{ __('Click here to re-send the verification email.') }}
                                </button>
                                @if (session('status') === 'verification-link-sent')
                                    <p class="mt-2 text-sm font-medium text-green-600 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ __('A new verification link has been sent to your email address.') }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Privacy controls (global) --}}
        <div class="mt-6 sm:mt-8 p-4 sm:p-6 rounded-xl border border-gray-200 bg-gray-50">
            <h4 class="text-base sm:text-lg font-bold text-gray-900 mb-3">{{ __('Privacy & visibility') }}</h4>
            <p class="text-sm text-gray-600 mb-4">{{ __('Choose what visitors can see on your public profile.') }}</p>

            <div class="space-y-3">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="hidden" name="show_contact_info" value="0">
                    <input type="checkbox" name="show_contact_info" value="1" class="mt-1 text-[#6A8F3B] focus:ring-[#6A8F3B] rounded"
                        {{ old('show_contact_info', $user->show_contact_info) ? 'checked' : '' }}>
                    <div>
                        <div class="text-sm font-semibold text-gray-900">{{ __('Show phone and email') }}</div>
                        <div class="text-xs text-gray-600">{{ __('Display your contact details on the public profile page.') }}</div>
                    </div>
                </label>

                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="hidden" name="show_address" value="0">
                    <input type="checkbox" name="show_address" value="1" class="mt-1 text-[#6A8F3B] focus:ring-[#6A8F3B] rounded"
                        {{ old('show_address', $user->show_address) ? 'checked' : '' }}>
                    <div>
                        <div class="text-sm font-semibold text-gray-900">{{ __('Show address') }}</div>
                        <div class="text-xs text-gray-600">{{ __('Reveal your saved addresses on the public profile page.') }}</div>
                    </div>
                </label>
            </div>
        </div>

        <!-- Role-Specific Information -->
        @if($user->role === 'farmer')
            <div class="bg-gradient-to-br from-green-50 via-white to-green-50 p-8 rounded-2xl border-2 border-green-200 shadow-lg">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center">
                        <span class="text-2xl">🌱</span>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">{{ __('Farm Information') }}</h3>
                        <p class="text-gray-600">{{ __('Details about your olive farm and production') }}</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="farm_name" class="block text-sm font-bold text-gray-900 mb-2">{{ __('Farm Name') }}</label>
                        <input type="text" id="farm_name" name="farm_name" 

                            value="{{ old('farm_name', $user->farm_name) }}"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-green-500/20 focus:border-green-500 transition-all bg-white">
                        <x-input-error class="mt-2" :messages="$errors->get('farm_name')" />
                    </div>
                    <div>
                        <label for="farm_name_ar" class="block text-sm font-bold text-gray-900 mb-2">{{ __('Farm Name (Arabic)') }}</label>
                        <input type="text" id="farm_name_ar" name="farm_name_ar" 
                            value="{{ old('farm_name_ar', $user->farm_name_ar) }}"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-green-500/20 focus:border-green-500 transition-all bg-white">
                        <x-input-error class="mt-2" :messages="$errors->get('farm_name_ar')" />
                    </div>
                    <div>
                        <label for="tree_number" class="block text-sm font-bold text-gray-900 mb-2">{{ __('Number of Trees') }}</label>
                        <input type="number" id="tree_number" name="tree_number" 
                            value="{{ old('tree_number', $user->tree_number) }}"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-green-500/20 focus:border-green-500 transition-all bg-white">
                        <x-input-error class="mt-2" :messages="$errors->get('tree_number')" />
                    </div>
                    <div>
                        <label for="olive_type" class="block text-sm font-bold text-gray-900 mb-2">{{ __('Olive Variety') }}</label>
                        <input type="text" id="olive_type" name="olive_type" 
                            value="{{ old('olive_type', $user->olive_type) }}"
                            placeholder="Chemlali, Chetoui, etc."
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-green-500/20 focus:border-green-500 transition-all bg-white">
                        <x-input-error class="mt-2" :messages="$errors->get('olive_type')" />
                    </div>
                </div>
            </div>
        @endif

        @if($user->role === 'carrier')
            <div class="bg-gradient-to-br from-blue-50 via-white to-blue-50 p-8 rounded-2xl border-2 border-blue-200 shadow-lg">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                        <span class="text-2xl">🚚</span>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">{{ __('Carrier Information') }}</h3>
                        <p class="text-gray-600">{{ __('Details about your transportation services') }}</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="company_name" class="block text-sm font-bold text-gray-900 mb-2">{{ __('Company Name') }}</label>
                        <input type="text" id="company_name" name="company_name" 
                            value="{{ old('company_name', $user->company_name) }}"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all bg-white">
                        <x-input-error class="mt-2" :messages="$errors->get('company_name')" />
                    </div>
                    <div>
                        <label for="fleet_size" class="block text-sm font-bold text-gray-900 mb-2">{{ __('Fleet Size') }}</label>
                        <input type="number" id="fleet_size" name="fleet_size" 
                            value="{{ old('fleet_size', $user->fleet_size) }}"
                            placeholder="Number of vehicles"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all bg-white">
                        <x-input-error class="mt-2" :messages="$errors->get('fleet_size')" />
                    </div>
                    <div class="md:col-span-2">
                        <label for="camion_capacity" class="block text-sm font-bold text-gray-900 mb-2">{{ __('Truck Capacity (tons)') }}</label>
                        <input type="number" step="0.1" id="camion_capacity" name="camion_capacity" 
                            value="{{ old('camion_capacity', $user->camion_capacity) }}"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all bg-white">
                        <x-input-error class="mt-2" :messages="$errors->get('camion_capacity')" />
                    </div>
                </div>
            </div>
        @endif

        @if($user->role === 'mill')
            <div class="bg-gradient-to-br from-amber-50 via-white to-amber-50 p-8 rounded-2xl border-2 border-amber-200 shadow-lg">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center">
                        <span class="text-2xl">⚙️</span>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">{{ __('Mill Information') }}</h3>
                        <p class="text-gray-600">{{ __('Details about your olive oil mill') }}</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="mill_name" class="block text-sm font-bold text-gray-900 mb-2">{{ __('Mill Name') }}</label>
                        <input type="text" id="mill_name" name="mill_name" 
                            value="{{ old('mill_name', $user->mill_name) }}"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-amber-500/20 focus:border-amber-500 transition-all bg-white">
                        <x-input-error class="mt-2" :messages="$errors->get('mill_name')" />
                    </div>
                    <div>
                        <label for="capacity" class="block text-sm font-bold text-gray-900 mb-2">{{ __('Daily Capacity (kg)') }}</label>
                        <input type="number" id="capacity" name="capacity" 
                            value="{{ old('capacity', $user->capacity) }}"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-amber-500/20 focus:border-amber-500 transition-all bg-white">
                        <x-input-error class="mt-2" :messages="$errors->get('capacity')" />
                    </div>
                </div>
            </div>
        @endif

        @if($user->role === 'packer')
            <div class="bg-gradient-to-br from-purple-50 via-white to-purple-50 p-8 rounded-2xl border-2 border-purple-200 shadow-lg">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                        <span class="text-2xl">📦</span>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">{{ __('Packer Information') }}</h3>
                        <p class="text-gray-600">{{ __('Details about your packaging facility') }}</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="packer_name" class="block text-sm font-bold text-gray-900 mb-2">{{ __('Company/Facility Name') }}</label>
                        <input type="text" id="packer_name" name="packer_name" 
                            value="{{ old('packer_name', $user->packer_name) }}"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-purple-500/20 focus:border-purple-500 transition-all bg-white">
                        <x-input-error class="mt-2" :messages="$errors->get('packer_name')" />
                    </div>
                    <div>
                        <label for="packaging_types" class="block text-sm font-bold text-gray-900 mb-2">{{ __('Packaging Types') }}</label>
                        <input type="text" id="packaging_types" name="packaging_types" 
                            value="{{ old('packaging_types', $user->packaging_types) }}"
                            placeholder="Bottles, Cans, Bulk"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-purple-500/20 focus:border-purple-500 transition-all bg-white">
                        <x-input-error class="mt-2" :messages="$errors->get('packaging_types')" />
                    </div>
                </div>
            </div>
        @endif

        @if(in_array($user->role, ['carrier', 'mill', 'packer', 'transiteur', 'comptable', 'service_bureau', 'agri_equipment', 'agri_materials', 'agri_study_office']))
            <!-- Service Provider Settings -->
            <div class="bg-gradient-to-br from-indigo-50 to-white p-4 sm:p-6 md:p-8 rounded-xl sm:rounded-2xl border-2 border-indigo-100 hover:border-indigo-300 transition-all duration-300">
                <div class="flex items-start sm:items-center gap-3 sm:gap-4 mb-4 sm:mb-6">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-900">{{ app()->getLocale() === 'ar' ? 'إعدادات مزود الخدمة' : __('Service Provider Settings') }}</h3>
                        <p class="text-sm sm:text-base text-gray-600 mt-1">{{ app()->getLocale() === 'ar' ? 'إدارة معلومات الخدمة B2B والتسعير والوصف لبيانات دليل الخدمات.' : __('Manage your B2B service info, pricing, and description for the Service Hub directory.') }}</p>
                    </div>
                </div>

                <div class="space-y-6">
                     <!-- Provider Type Dropdown -->
                    <div>
                        <x-input-label for="provider_type" :value="app()->getLocale() === 'ar' ? 'نوع الكيان / الهيكل' : __('Structure Type / Entity Type')" class="font-bold text-gray-700 text-sm mb-2" />
                        <select id="provider_type" name="provider_type" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all bg-white text-sm appearance-none bg-[url('data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2020%2020%22%20fill%3D%22%234a5568%22%3E%3Cpath%20fill-rule%3D%22evenodd%22%20d%3D%22M5.293%207.293a1%201%200%20011.414%200L10%2010.586l3.293-3.293a1%201%200%20111.414%201.414l-4%204a1%201%200%2001-1.414%200l-4-4a1%201%200%20010-1.414z%22%20clip-rule%3D%22evenodd%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem_1.25rem] bg-no-repeat rtl:bg-[left_1rem_center] ltr:bg-[right_1rem_center] rtl:pl-10 ltr:pr-10">
                            <option value="freelancer" {{ old('provider_type', $user->meta_data['provider_type'] ?? '') === 'freelancer' ? 'selected' : '' }}>👤 مستقل (Freelancer)</option>
                            <option value="bureau" {{ old('provider_type', $user->meta_data['provider_type'] ?? '') === 'bureau' ? 'selected' : '' }}>🏢 مكتب (Bureau / Cabinet)</option>
                            <option value="societe" {{ old('provider_type', $user->meta_data['provider_type'] ?? '') === 'societe' ? 'selected' : '' }}>🏢 شركة (Société)</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('provider_type')" />
                    </div>

                    <!-- Pricing Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6" x-data="{ priceType: '{{ old('price_type', $user->meta_data['price_type'] ?? 'quote') }}' }">
                        <div>
                            <x-input-label for="price_type" :value="app()->getLocale() === 'ar' ? 'نموذج التسعير' : __('Pricing Model')" class="font-bold text-gray-700 text-sm mb-2" />
                            <select id="price_type" name="price_type" x-model="priceType" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all bg-white text-sm appearance-none bg-[url('data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2020%2020%22%20fill%3D%22%234a5568%22%3E%3Cpath%20fill-rule%3D%22evenodd%22%20d%3D%22M5.293%207.293a1%201%200%20011.414%200L10%2010.586l3.293-3.293a1%201%200%20111.414%201.414l-4%204a1%201%200%2001-1.414%200l-4-4a1%201%200%20010-1.414z%22%20clip-rule%3D%22evenodd%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem_1.25rem] bg-no-repeat rtl:bg-[left_1rem_center] ltr:bg-[right_1rem_center] rtl:pl-10 ltr:pr-10">
                                <option value="quote">💬 اتصل للحصول على تسعيرة / السعر حسب الطلب</option>
                                <option value="fixed">💰 سعر محدد (يبدأ من...)</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('price_type')" />
                        </div>

                        <div x-show="priceType === 'fixed'" x-transition>
                            <x-input-label for="service_price" :value="app()->getLocale() === 'ar' ? 'السعر التقريبي (د.ت)' : __('Approximate Price (TND)')" class="font-bold text-gray-700 text-sm mb-2" />
                            <input type="number" id="service_price" name="service_price" min="0" value="{{ old('service_price', $user->meta_data['service_price'] ?? '') }}" placeholder="150" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all bg-white text-sm">
                            <x-input-error class="mt-2" :messages="$errors->get('service_price')" />
                        </div>
                    </div>

                    <!-- Service Description -->
                    <div>
                        <x-input-label for="service_description" :value="app()->getLocale() === 'ar' ? 'نبذة عن خدماتك وقدراتك' : __('About your service, equipment, and capabilities')" class="font-bold text-gray-700 text-sm mb-2" />
                        <textarea id="service_description" name="service_description" rows="4" placeholder="{{ app()->getLocale() === 'ar' ? 'صف بالتفصيل ما تقدمه من خدمات...' : __('Describe your service details...') }}" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all bg-white text-sm">{{ old('service_description', $user->meta_data['service_description'] ?? '') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('service_description')" />
                    </div>

                    <!-- Interactive Services List Manager -->
                    <div class="mt-8 pt-8 border-t border-indigo-100" x-data="{
                        services: @js($user->meta_data['services'] ?? []),
                        newTitle: '',
                        newPrice: '',
                        newPriceType: 'fixed',
                        newDesc: '',
                        addService() {
                            if (!this.newTitle.trim()) {
                                showToast('{{ app()->getLocale() === "ar" ? "يرجى إدخال اسم الخدمة!" : "Please enter a service name!" }}', 'error');
                                return;
                            }
                            this.services.push({
                                title: this.newTitle.trim(),
                                price: this.newPrice ? parseFloat(this.newPrice) : null,
                                price_type: this.newPriceType,
                                description: this.newDesc.trim()
                            });
                            this.newTitle = '';
                            this.newPrice = '';
                            this.newPriceType = 'fixed';
                            this.newDesc = '';
                        },
                        removeService(index) {
                            if (confirm('{{ app()->getLocale() === "ar" ? "هل أنت متأكد من حذف هذه الخدمة؟" : "Are you sure you want to delete this service?" }}')) {
                                this.services.splice(index, 1);
                            }
                        }
                    }">
                        <input type="hidden" name="services" :value="JSON.stringify(services)">

                        <h4 class="text-base sm:text-lg font-bold text-gray-900 mb-2">🛠️ {{ app()->getLocale() === 'ar' ? 'بطاقات الخدمات التفصيلية' : 'Detailed Service Cards' }}</h4>
                        <p class="text-xs sm:text-sm text-gray-500 mb-4">{{ app()->getLocale() === 'ar' ? 'أضف خدمات محددة مثل (عقد تصدير 150 د.ت، فحص جودة 50 د.ت) لتظهر كبطاقات مستقلة للزبائن.' : 'Add specific services (e.g., Export Contract 150 TND, Quality Inspection 50 TND) to display them as individual cards.' }}</p>

                        <!-- Services Cards List -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                            <template x-for="(srv, index) in services" :key="index">
                                <div class="bg-white border-2 border-indigo-50 hover:border-indigo-200 rounded-2xl p-4 shadow-sm relative flex flex-col justify-between group">
                                    <button type="button" @click="removeService(index)" class="absolute top-2 left-2 text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 rounded-full w-8 h-8 flex items-center justify-center transition">
                                        🗑️
                                    </button>
                                    <div class="space-y-2 text-right">
                                        <template x-if="srv.image">
                                            <div class="w-full h-24 rounded-xl overflow-hidden relative border border-gray-100 bg-gray-50 mb-2">
                                                <img :src="'/' + srv.image" class="w-full h-full object-cover">
                                            </div>
                                        </template>
                                        <h5 class="font-bold text-gray-800 text-sm" x-text="srv.title"></h5>
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-lg">
                                                <span x-show="srv.price_type === 'fixed'"><span x-text="srv.price"></span> TND</span>
                                                <span x-show="srv.price_type === 'quote'">{{ app()->getLocale() === 'ar' ? 'طلب تسعيرة' : 'Get Quote' }}</span>
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500 line-clamp-2" x-text="srv.description"></p>
                                    </div>
                                </div>
                            </template>
                            <template x-if="services.length === 0">
                                <div class="col-span-full border-2 border-dashed border-gray-200 rounded-2xl p-6 text-center text-gray-400 text-xs">
                                    {{ app()->getLocale() === 'ar' ? 'لا توجد خدمات تفصيلية مضافة بعد. يمكنك إضافتها أدناه.' : 'No detailed services added yet. You can add them below.' }}
                                </div>
                            </template>
                        </div>

                        <!-- Form to Add New Service Card -->
                        <div class="bg-white border-2 border-dashed border-indigo-100 rounded-2xl p-4 sm:p-5 space-y-4">
                            <h5 class="text-xs sm:text-sm font-bold text-gray-700 uppercase tracking-wider">{{ app()->getLocale() === 'ar' ? 'إضافة بطاقة خدمة جديدة' : 'Add New Service Card' }}</h5>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1">{{ app()->getLocale() === 'ar' ? 'اسم الخدمة' : 'Service Title' }} <span class="text-red-500">*</span></label>
                                    <input type="text" x-model="newTitle" placeholder="{{ app()->getLocale() === 'ar' ? 'مثال: عقد تصدير' : 'e.g. Export Contract' }}" class="w-full px-3 py-2.5 border border-gray-300 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1">{{ app()->getLocale() === 'ar' ? 'طريقة التسعير' : 'Pricing Model' }}</label>
                                    <select x-model="newPriceType" class="w-full px-3 py-2.5 border border-gray-300 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none appearance-none bg-[url('data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2020%2020%22%20fill%3D%22%234a5568%22%3E%3Cpath%20fill-rule%3D%22evenodd%22%20d%3D%22M5.293%207.293a1%201%200%20011.414%200L10%2010.586l3.293-3.293a1%201%200%20111.414%201.414l-4%204a1%201%200%2001-1.414%200l-4-4a1%201%200%20010-1.414z%22%20clip-rule%3D%22evenodd%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1rem_1rem] bg-no-repeat rtl:bg-[left_0.75rem_center] ltr:bg-[right_0.75rem_center] rtl:pl-8 ltr:pr-8">
                                        <option value="fixed">{{ app()->getLocale() === 'ar' ? '💰 سعر محدد' : 'Fixed Price' }}</option>
                                        <option value="quote">{{ app()->getLocale() === 'ar' ? '💬 سعر عند الطلب' : 'Quote Needed' }}</option>
                                    </select>
                                </div>
                                <div x-show="newPriceType === 'fixed'">
                                    <label class="block text-xs font-bold text-gray-600 mb-1">{{ app()->getLocale() === 'ar' ? 'السعر (د.ت)' : 'Price (TND)' }}</label>
                                    <input type="number" x-model="newPrice" placeholder="150" class="w-full px-3 py-2.5 border border-gray-300 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">{{ app()->getLocale() === 'ar' ? 'وصف تفصيلي للخدمة' : 'Service Description' }}</label>
                                <textarea x-model="newDesc" rows="2" placeholder="{{ app()->getLocale() === 'ar' ? 'اكتب وصفاً موجزاً لما تتضمنه الخدمة...' : 'Briefly describe what this service includes...' }}" class="w-full px-3 py-2.5 border border-gray-300 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"></textarea>
                            </div>
                            
                            <button type="button" @click="addService()" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow transition">
                                ➕ {{ app()->getLocale() === 'ar' ? 'إضافة الخدمة إلى القائمة' : 'Add Service to List' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Save Button Section -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 sm:gap-4 pt-4 sm:pt-6 border-t-2 border-gray-100">
            <button type="submit" class="w-full sm:w-auto group relative px-8 sm:px-12 py-3 sm:py-4 bg-gradient-to-r from-[#6A8F3B] to-[#5a7a2f] text-white font-bold rounded-xl sm:rounded-2xl hover:shadow-2xl hover:shadow-[#6A8F3B]/30 transform hover:scale-105 transition-all duration-300 flex items-center justify-center gap-2 sm:gap-3 overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-[#5a7a2f] to-[#6A8F3B] opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <svg class="w-5 h-5 sm:w-6 sm:h-6 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="relative z-10 text-base sm:text-lg">{{ __('Save Changes') }}</span>
            </button>

            @if (session('status') === 'profile-updated')
                <div
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-init="setTimeout(() => show = false, 5000)"
                    class="w-full sm:w-auto flex items-center gap-2 sm:gap-3 bg-green-50 border-2 border-green-500 text-green-700 px-4 sm:px-6 py-2 sm:py-3 rounded-lg sm:rounded-xl shadow-lg"
                >
                    <div class="w-6 h-6 sm:w-8 sm:h-8 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-sm sm:text-base truncate">{{ __('Saved successfully!') }}</p>
                        <p class="text-xs sm:text-sm truncate">{{ __('Your profile has been updated') }}</p>
                    </div>
                </div>
            @endif
        </div>
    </form>
</section>
