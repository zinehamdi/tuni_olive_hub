@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-100" dir="rtl"
    x-data="{
        editModal: null,
        saving: false,
        successMessage: '',
        errorMessage: '',
        formData: {
            name: '{{ Auth::user()->name }}',
            email: '{{ Auth::user()->email }}',
            phone: '{{ Auth::user()->phone ?? '' }}',
            farm_name: '{{ Auth::user()->farm_name ?? '' }}',
            farm_name_ar: '{{ Auth::user()->farm_name_ar ?? '' }}',
            company_name: '{{ Auth::user()->company_name ?? '' }}',
            mill_name: '{{ Auth::user()->mill_name ?? '' }}',
            packer_name: '{{ Auth::user()->packer_name ?? '' }}',
            tree_number: '{{ Auth::user()->tree_number ?? '' }}',
            olive_type: '{{ Auth::user()->olive_type ?? '' }}',
            capacity: '{{ Auth::user()->capacity ?? '' }}',
            fleet_size: '{{ Auth::user()->fleet_size ?? '' }}',
            camion_capacity: '{{ Auth::user()->camion_capacity ?? '' }}',
            packaging_types: '{{ Auth::user()->packaging_types ?? '' }}'
        },
        openEdit(field) {
            this.editModal = field;
            this.errorMessage = '';
        },
        closeEdit() {
            this.editModal = null;
        },
        async saveField(field) {
            this.saving = true;
            this.errorMessage = '';
            
            const formDataObj = new FormData();
            formDataObj.append('_token', '{{ csrf_token() }}');
            formDataObj.append('_method', 'PATCH');
            formDataObj.append('field', field);
            formDataObj.append('value', this.formData[field]);
            
            try {
                const response = await fetch('{{ route('profile.update.field') }}', {
                    method: 'POST',
                    body: formDataObj,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    this.successMessage = result.message || '{{ __('Updated successfully!') }}';
                    this.closeEdit();
                    setTimeout(() => this.successMessage = '', 3000);
                    // Update the displayed value
                    if (document.getElementById('display-' + field)) {
                        document.getElementById('display-' + field).textContent = this.formData[field];
                    }
                } else {
                    this.errorMessage = result.message || '{{ __('Update failed. Please try again.') }}';
                }
            } catch (error) {
                this.errorMessage = '{{ __('An error occurred. Please try again.') }}';
            }
            
            this.saving = false;
        },
        async uploadPhoto(type, event) {
            const file = event.target.files[0];
            if (!file) return;
            
            this.saving = true;
            const formDataObj = new FormData();
            formDataObj.append('_token', '{{ csrf_token() }}');
            formDataObj.append('type', type);
            formDataObj.append('photo', file);
            
            try {
                const response = await fetch('{{ route('profile.upload.photo') }}', {
                    method: 'POST',
                    body: formDataObj,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    this.successMessage = result.message || '{{ __('Photo uploaded!') }}';
                    setTimeout(() => location.reload(), 1000);
                } else {
                    this.errorMessage = result.message || '{{ __('Upload failed.') }}';
                }
            } catch (error) {
                this.errorMessage = '{{ __('An error occurred.') }}';
            }
            
            this.saving = false;
        }
    }"
    @keydown.escape.window="closeEdit()">

    <!-- Success Toast -->
    <div x-show="successMessage" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-200" class="fixed top-4 left-1/2 transform -translate-x-1/2 z-[200]">
        <div class="bg-gradient-to-r from-green-500 to-emerald-600 text-white px-6 py-3 rounded-xl shadow-2xl flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            <span class="font-medium" x-text="successMessage"></span>
        </div>
    </div>

    <!-- Session Messages -->
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
                        <div class="h-72 sm:h-80 relative overflow-hidden" x-data="{ currentSlide: 0, slides: {{ $photoCount }} }" x-init="setInterval(() => { currentSlide = (currentSlide + 1) % slides }, 4000)">
                            @foreach($validPhotos as $index => $photo)
                                <div x-show="currentSlide === {{ $index }}" 
                                     x-transition:enter="transition ease-out duration-1000"
                                     x-transition:enter-start="opacity-0 transform scale-105"
                                     x-transition:enter-end="opacity-100 transform scale-100"
                                     x-transition:leave="transition ease-in duration-1000"
                                     x-transition:leave-start="opacity-100"
                                     x-transition:leave-end="opacity-0"
                                     class="absolute inset-0">
                                    <img src="{{ Storage::url($photo) }}" alt="Cover {{ $index + 1 }}" class="w-full h-full object-cover" loading="lazy">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
                                </div>
                            @endforeach
                            
                            <!-- Photo Counter Badge + Add Button -->
                            <div class="absolute top-4 right-4 z-10 flex items-center gap-2">
                                @if($photoCount < 5)
                                <label class="bg-white/20 hover:bg-white/40 backdrop-blur-sm text-white text-xs font-bold px-3 py-1.5 rounded-full flex items-center gap-1.5 cursor-pointer transition hover:scale-105">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    {{ __('Add') }}
                                    <input type="file" @change="uploadPhoto('cover', $event)" accept="image/*" class="hidden">
                                </label>
                                @endif
                                <span class="bg-black/50 backdrop-blur-sm text-white text-xs font-bold px-3 py-1.5 rounded-full flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span x-text="(currentSlide + 1) + '/{{ $photoCount }}'"></span>
                                </span>
                            </div>
                            
                            <!-- Slideshow Indicators -->
                            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex gap-2 z-10">
                                @foreach($validPhotos as $index => $photo)
                                    <button @click="currentSlide = {{ $index }}" 
                                            :class="currentSlide === {{ $index }} ? 'bg-white w-6' : 'bg-white/50 w-2'" 
                                            class="h-2 rounded-full transition-all duration-300 hover:bg-white"></button>
                                @endforeach
                            </div>
                            
                            <!-- Navigation Arrows -->
                            @if($photoCount > 1)
                                <button @click="currentSlide = (currentSlide - 1 + slides) % slides" class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-white/20 hover:bg-white/40 backdrop-blur-sm text-white rounded-full p-3 transition z-10 hover:scale-110">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                    </svg>
                                </button>
                                <button @click="currentSlide = (currentSlide + 1) % slides" class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-white/20 hover:bg-white/40 backdrop-blur-sm text-white rounded-full p-3 transition z-10 hover:scale-110">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            @endif
                        </div>
                    @else
                        <!-- Default gradient cover if no valid photos -->
                        <div class="h-72 sm:h-80 bg-gradient-to-br from-[#6A8F3B] via-[#7a9f4b] to-[#C8A356] relative">
                            <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.1\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-30"></div>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <label class="flex flex-col items-center gap-3 text-white/80 hover:text-white transition group cursor-pointer">
                                    <div class="w-16 h-16 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center group-hover:scale-110 transition">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </div>
                                    <span class="font-medium text-sm">{{ __('Add Cover Photos') }}</span>
                                    <input type="file" @change="uploadPhoto('cover', $event)" accept="image/*" class="hidden">
                                </label>
                            </div>
                        </div>
                    @endif
                @else
                    <!-- Default gradient cover if no photos -->
                    <div class="h-72 sm:h-80 bg-gradient-to-br from-[#6A8F3B] via-[#7a9f4b] to-[#C8A356] relative">
                        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.1\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-30"></div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <label class="flex flex-col items-center gap-3 text-white/80 hover:text-white transition group cursor-pointer">
                                <div class="w-16 h-16 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center group-hover:scale-110 transition">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                </div>
                                <span class="font-medium text-sm">{{ __('Add Cover Photos') }}</span>
                                <input type="file" @change="uploadPhoto('cover', $event)" accept="image/*" class="hidden">
                            </label>
                        </div>
                    </div>
                @endif
                
                <!-- Profile Content -->
                <div class="px-6 pb-6">
                    <div class="flex flex-col lg:flex-row gap-6 -mt-20 relative z-10">
                        <!-- Left Side: Profile Picture with Status Ring -->
                        <div class="flex-shrink-0">
                            <div class="relative group cursor-pointer" @click="$refs.profilePhotoInput.click()">
                                <div class="w-36 h-36 rounded-2xl p-1 bg-gradient-to-br from-[#6A8F3B] via-[#C8A356] to-[#6A8F3B] shadow-2xl">
                                    <div class="w-full h-full rounded-xl overflow-hidden bg-white relative">
                                        @if(Auth::user()->profile_picture)
                                            <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Profile" class="w-full h-full object-cover" loading="lazy">
                                        @else
                                            <div class="w-full h-full bg-gradient-to-br from-[#6A8F3B] to-[#C8A356] flex items-center justify-center">
                                                <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                            </div>
                                        @endif
                                        <!-- Hover Overlay -->
                                        <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                            <div class="text-white text-center">
                                                <svg class="w-8 h-8 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                <span class="text-xs font-medium">{{ __('Change') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Online Status -->
                                <div class="absolute -bottom-1 -right-1 w-8 h-8 bg-green-500 border-4 border-white rounded-full flex items-center justify-center shadow-lg">
                                    <div class="w-3 h-3 bg-white rounded-full animate-pulse"></div>
                                </div>
                                <input type="file" x-ref="profilePhotoInput" @change="uploadPhoto('profile', $event)" accept="image/*" class="hidden">
                            </div>
                        </div>

                        <!-- Center: User Info - Glass Effect -->
                        <div class="flex-1 min-w-0">
                            <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-5 shadow-xl border border-white/50">
                                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                                    <div class="flex-1 min-w-0">
                                        <!-- Name & Badges Row - Editable -->
                                        <div class="flex flex-wrap items-center gap-3 mb-3">
                                            <button @click="openEdit('name')" class="group flex items-center gap-2 hover:bg-gray-100 rounded-lg px-2 py-1 -mx-2 -my-1 transition">
                                                <h1 id="display-name" class="text-2xl lg:text-3xl font-bold text-gray-900 truncate">
                                                    {{ Auth::user()->name }}
                                                </h1>
                                                <svg class="w-4 h-4 text-gray-400 opacity-0 group-hover:opacity-100 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </button>
                                            @if(Auth::user()->is_verified)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-500 text-white rounded-full text-xs font-bold shadow-lg">
                                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                                {{ __('Verified') }}
                                            </span>
                                            @endif
                                        </div>

                                        <!-- Role & Trust Score -->
                                        <div class="flex flex-wrap items-center gap-2 mb-4">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-sm font-bold
                                                @if(Auth::user()->role === 'farmer') bg-gradient-to-r from-green-500 to-emerald-600 text-white
                                                @elseif(Auth::user()->role === 'carrier') bg-gradient-to-r from-blue-500 to-indigo-600 text-white
                                                @elseif(Auth::user()->role === 'mill') bg-gradient-to-r from-amber-500 to-orange-600 text-white
                                                @elseif(Auth::user()->role === 'packer') bg-gradient-to-r from-purple-500 to-violet-600 text-white
                                                @else bg-gradient-to-r from-gray-500 to-gray-600 text-white
                                                @endif shadow-lg">
                                                @if(Auth::user()->role === 'farmer') 🌱 {{ __('Farmer') }}
                                                @elseif(Auth::user()->role === 'carrier') 🚚 {{ __('Carrier') }}
                                                @elseif(Auth::user()->role === 'mill') ⚙️ {{ __('Mill') }}
                                                @elseif(Auth::user()->role === 'packer') 📦 {{ __('Packer') }}
                                                @else 👤 {{ __('User') }}
                                                @endif
                                            </span>
                                            
                                            @if(Auth::user()->trust_score)
                                            <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-amber-400 to-yellow-500 text-amber-900 rounded-xl text-sm font-bold shadow-lg">
                                                ⭐ {{ number_format(Auth::user()->trust_score, 1) }}
                                            </span>
                                            @endif
                                        </div>

                                        <!-- Contact Info Grid - Editable -->
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
                                            @if(Auth::user()->role === 'farmer')
                                            <button @click="openEdit('farm_name')" class="group flex items-center gap-2 text-gray-700 bg-green-50 hover:bg-green-100 rounded-lg px-3 py-2 text-right transition w-full">
                                                <span class="text-green-600">🌿</span>
                                                <span id="display-farm_name" class="font-medium truncate flex-1">{{ Auth::user()->farm_name ?: __('Add Farm Name') }}</span>
                                                <svg class="w-3.5 h-3.5 text-gray-400 opacity-0 group-hover:opacity-100 transition flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </button>
                                            @elseif(Auth::user()->role === 'carrier')
                                            <button @click="openEdit('company_name')" class="group flex items-center gap-2 text-gray-700 bg-blue-50 hover:bg-blue-100 rounded-lg px-3 py-2 text-right transition w-full">
                                                <span class="text-blue-600">🏢</span>
                                                <span id="display-company_name" class="font-medium truncate flex-1">{{ Auth::user()->company_name ?: __('Add Company Name') }}</span>
                                                <svg class="w-3.5 h-3.5 text-gray-400 opacity-0 group-hover:opacity-100 transition flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </button>
                                            @elseif(Auth::user()->role === 'mill')
                                            <button @click="openEdit('mill_name')" class="group flex items-center gap-2 text-gray-700 bg-amber-50 hover:bg-amber-100 rounded-lg px-3 py-2 text-right transition w-full">
                                                <span class="text-amber-600">🏭</span>
                                                <span id="display-mill_name" class="font-medium truncate flex-1">{{ Auth::user()->mill_name ?: __('Add Mill Name') }}</span>
                                                <svg class="w-3.5 h-3.5 text-gray-400 opacity-0 group-hover:opacity-100 transition flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </button>
                                            @elseif(Auth::user()->role === 'packer')
                                            <button @click="openEdit('packer_name')" class="group flex items-center gap-2 text-gray-700 bg-purple-50 hover:bg-purple-100 rounded-lg px-3 py-2 text-right transition w-full">
                                                <span class="text-purple-600">📦</span>
                                                <span id="display-packer_name" class="font-medium truncate flex-1">{{ Auth::user()->packer_name ?: __('Add Packer Name') }}</span>
                                                <svg class="w-3.5 h-3.5 text-gray-400 opacity-0 group-hover:opacity-100 transition flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </button>
                                            @endif
                                            
                                            <button @click="openEdit('email')" class="group flex items-center gap-2 text-gray-600 bg-gray-50 hover:bg-gray-100 rounded-lg px-3 py-2 text-right transition w-full">
                                                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                                                <span id="display-email" class="truncate flex-1">{{ Auth::user()->email }}</span>
                                                <svg class="w-3.5 h-3.5 text-gray-400 opacity-0 group-hover:opacity-100 transition flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </button>
                                            
                                            <button @click="openEdit('phone')" class="group flex items-center gap-2 text-gray-600 bg-gray-50 hover:bg-gray-100 rounded-lg px-3 py-2 text-right transition w-full">
                                                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                </svg>
                                                <span id="display-phone" class="flex-1">{{ Auth::user()->phone ?: __('Add Phone') }}</span>
                                                <svg class="w-3.5 h-3.5 text-gray-400 opacity-0 group-hover:opacity-100 transition flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Right Side: Completion & Actions -->
                                    <div class="flex flex-row lg:flex-col items-center lg:items-end gap-4">
                                        <!-- Profile Completion Ring -->
                                        <div class="flex flex-col items-center">
                                            <div class="relative w-16 h-16">
                                                <svg class="w-16 h-16 transform -rotate-90">
                                                    <circle cx="32" cy="32" r="26" stroke="#e5e7eb" stroke-width="5" fill="none" />
                                                    <circle cx="32" cy="32" r="26" 
                                                        stroke="url(#progressGradient)" 
                                                        stroke-width="5" 
                                                        fill="none"
                                                        stroke-dasharray="163"
                                                        stroke-dashoffset="{{ 163 - (163 * $profileCompletion / 100) }}"
                                                        stroke-linecap="round" />
                                                    <defs>
                                                        <linearGradient id="progressGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                                            <stop offset="0%" stop-color="#6A8F3B" />
                                                            <stop offset="100%" stop-color="#C8A356" />
                                                        </linearGradient>
                                                    </defs>
                                                </svg>
                                                <div class="absolute inset-0 flex items-center justify-center">
                                                    <span class="text-sm font-bold text-gray-700">{{ $profileCompletion }}%</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- More Options Button -->
                                        <button @click="openEdit('more')" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-[#6A8F3B] to-[#5a7a2f] text-white font-bold rounded-xl hover:shadow-lg hover:scale-105 transition-all duration-300 text-sm whitespace-nowrap">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                            </svg>
                                            {{ __('More Settings') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Header Section with Quick Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">
            <!-- Welcome Message -->
            <div class="lg:col-span-2 px-1">
                <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 mb-1 sm:mb-2">
                    {{ __('Welcome') }}، {{ Auth::user()->name }} 👋
                </h1>
                <p class="text-gray-600 text-base sm:text-lg">{{ __('Manage your listings and products') }}</p>
            </div>

            <!-- Messages Box with Notification Badge -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg sm:shadow-xl overflow-hidden h-full"
                     x-data="{ 
                         loading: true, 
                         unreadCount: 0,
                         init() {
                             this.fetchUnreadCount();
                             setInterval(() => this.fetchUnreadCount(), 30000);
                         },
                         async fetchUnreadCount() {
                             try {
                                 const res = await fetch('/messages/unread-count');
                                 const data = await res.json();
                                 this.unreadCount = data.count;
                             } catch (e) {}
                             this.loading = false;
                         }
                     }">
                    <div class="p-4 bg-gradient-to-r from-emerald-50 to-teal-50 border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center shadow-lg shadow-emerald-500/30">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                    {{ __('Messages') }}
                                    <template x-if="unreadCount > 0">
                                        <span class="px-2 py-0.5 bg-red-500 text-white text-xs font-bold rounded-full animate-pulse" x-text="unreadCount > 99 ? '99+' : unreadCount"></span>
                                    </template>
                                </h3>
                                <p class="text-sm text-gray-500">{{ __('Connect with other users') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-4">
                        <p class="text-sm text-gray-600 mb-4 flex items-center gap-2">
                            <span>💬</span>
                            <span>{{ __('Start a conversation with farmers, mills, or carriers') }}</span>
                        </p>
                        <a href="{{ route('messages.inbox') }}" 
                           class="relative w-full flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-xl text-sm font-semibold hover:shadow-lg hover:shadow-emerald-500/30 transition-all transform hover:scale-105">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            {{ __('Open Inbox') }}
                            <template x-if="unreadCount > 0">
                                <span class="absolute -top-2 -left-2 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center shadow-lg" x-text="unreadCount > 9 ? '9+' : unreadCount"></span>
                            </template>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions Horizontal Bar -->
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg sm:shadow-xl p-4 sm:p-6 mb-6 sm:mb-8">
            <h3 class="text-base sm:text-lg font-bold text-gray-900 mb-3 sm:mb-4">{{ __('Quick Actions') }}</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 sm:gap-3">
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

        <!-- Stories Section -->
        <div x-data="{
                stories: [],
                current: null,
                currentIndex: 0,
                loading: true,
                error: false,
                userId: {{ Auth::id() }},
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
            id="stories"
            class="bg-white rounded-2xl shadow-xl p-6 mb-8">

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
                            {{ __('My Stories') }}
                            <span class="px-2.5 py-1 text-[10px] uppercase tracking-wide rounded-full bg-gradient-to-r from-green-500/10 to-amber-500/10 text-green-700 font-bold border border-green-200">{{ __('Live') }}</span>
                        </h2>
                        <p class="text-sm text-gray-500">{{ __('Your updates • Auto-expire in 48h') }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('stories.store') }}" enctype="multipart/form-data" class="flex flex-wrap items-center gap-2">
                    @csrf
                    <label class="group inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-[#6A8F3B] to-[#5a7a2f] text-white rounded-xl text-sm font-semibold cursor-pointer hover:shadow-lg hover:scale-105 transition-all duration-300">
                        <svg class="w-5 h-5 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>{{ __('Add Story') }}</span>
                        <input type="file" name="media" accept="image/*,video/*" class="hidden" required onchange="this.form.submit()">
                    </label>
                    <input type="text" name="caption" maxlength="200" placeholder="{{ __('Caption (optional)') }}" class="border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm w-full sm:w-48 focus:ring-2 focus:ring-[#6A8F3B]/30 focus:border-[#6A8F3B] transition-all">
                </form>
            </div>

            {{-- Loading State --}}
            <template x-if="loading && !error">
                <div class="flex gap-4 overflow-hidden">
                    <template x-for="i in 5" :key="i">
                        <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full bg-gradient-to-br from-gray-200 to-gray-300 animate-pulse flex-shrink-0"></div>
                    </template>
                </div>
            </template>

            {{-- Error State --}}
            <template x-if="error">
                <div class="bg-red-50 border border-red-200 rounded-2xl p-4 flex items-center gap-3 text-red-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span class="text-sm font-medium">{{ __('Could not load stories. Please try again later.') }}</span>
                </div>
            </template>

            {{-- Empty State --}}
            <template x-if="!loading && !error && stories.length === 0">
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 border-2 border-dashed border-gray-200 rounded-2xl p-8 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-200 flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                    </div>
                    <p class="text-gray-600 font-medium">{{ __('No stories yet') }}</p>
                    <p class="text-sm text-gray-400 mt-1">{{ __('Add your first story!') }}</p>
                </div>
            </template>

            {{-- Stories Strip --}}
            <div x-show="!loading && !error && stories.length > 0" x-cloak class="flex gap-4 overflow-x-auto pb-4" style="scrollbar-width: none;">
                <template x-for="(story, index) in stories" :key="story.id">
                    <button type="button" class="group relative flex-shrink-0 focus:outline-none" @click="openStory(story, index)">
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
                 class="fixed inset-0 z-[100] flex items-center justify-center bg-black/90 backdrop-blur-md" 
                 style="display: none;">
                <div class="absolute top-4 left-4 right-4 flex gap-1">
                    <template x-for="(s, i) in stories" :key="s.id">
                        <div class="flex-1 h-1 bg-white/30 rounded-full overflow-hidden">
                            <div class="h-full bg-white rounded-full transition-all duration-100" :style="{ width: i < currentIndex ? '100%' : (i === currentIndex ? progress + '%' : '0%') }"></div>
                        </div>
                    </template>
                </div>
                <button @click="prevStory()" class="absolute left-0 top-0 bottom-0 w-1/3 z-10 cursor-pointer focus:outline-none" x-show="currentIndex > 0"></button>
                <button @click="nextStory()" class="absolute right-0 top-0 bottom-0 w-1/3 z-10 cursor-pointer focus:outline-none"></button>
                <div class="relative max-w-lg w-full mx-4">
                    <button type="button" class="absolute -top-12 right-0 text-white/80 hover:text-white p-2 rounded-full hover:bg-white/10 transition z-20" @click="closeStory()" aria-label="Close">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                    <template x-if="current && current.media_type === 'image'">
                        <img :src="current.url" alt="story" class="w-full max-h-[75vh] object-contain rounded-3xl shadow-2xl">
                    </template>
                    <template x-if="current && current.media_type === 'video'">
                        <video :src="current.url" controls autoplay playsinline class="w-full max-h-[75vh] rounded-3xl shadow-2xl bg-black"></video>
                    </template>
                    <div x-show="current?.caption" class="absolute bottom-0 left-0 right-0 p-6 bg-gradient-to-t from-black/80 to-transparent rounded-b-3xl">
                        <p class="text-white text-lg font-medium" x-text="current?.caption"></p>
                    </div>
                </div>
                <button x-show="currentIndex > 0" @click="prevStory()" class="absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center text-white transition z-20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                </button>
                <button x-show="currentIndex < stories.length - 1" @click="nextStory()" class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center text-white transition z-20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </button>
            </div>
        </div>

        <!-- Photo Gallery Section -->
        @php
            $galleryPhotos = Auth::user()->cover_photos ?? [];
            $galleryPhotos = array_values(array_filter($galleryPhotos, fn($p) => is_string($p) && !empty($p)));
        @endphp
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8" x-data="{ 
            lightbox: false, 
            currentPhoto: 0,
            photos: {{ json_encode(array_map(fn($p) => Storage::url($p), $galleryPhotos)) }},
            openLightbox(index) { this.currentPhoto = index; this.lightbox = true; },
            closeLightbox() { this.lightbox = false; },
            next() { this.currentPhoto = (this.currentPhoto + 1) % this.photos.length; },
            prev() { this.currentPhoto = (this.currentPhoto - 1 + this.photos.length) % this.photos.length; }
        }" @keydown.escape.window="closeLightbox()" @keydown.right.window="lightbox && next()" @keydown.left.window="lightbox && prev()">
            
            {{-- Gallery Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#C8A356] to-[#A88932] flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                            {{ __('Photo Gallery') }}
                            <span class="px-2.5 py-1 text-[10px] uppercase tracking-wide rounded-full bg-gradient-to-r from-amber-500/10 to-orange-500/10 text-amber-700 font-bold border border-amber-200">{{ count($galleryPhotos) }}/5</span>
                        </h2>
                        <p class="text-sm text-gray-500">{{ __('Your profile showcase photos') }}</p>
                    </div>
                </div>
                <a href="{{ route('profile.edit') }}#photos" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-[#C8A356] to-[#A88932] text-white rounded-xl text-sm font-semibold hover:shadow-lg hover:scale-105 transition-all duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>{{ __('Manage Photos') }}</span>
                </a>
            </div>

            @if(count($galleryPhotos) > 0)
                {{-- Masonry-style Gallery Grid --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-4">
                    @foreach($galleryPhotos as $index => $photo)
                        <button type="button" @click="openLightbox({{ $index }})" 
                            class="group relative aspect-square rounded-xl overflow-hidden cursor-pointer focus:outline-none focus:ring-4 focus:ring-[#C8A356]/50 transition-all duration-300 hover:scale-105 hover:shadow-xl hover:z-10">
                            <img src="{{ Storage::url($photo) }}" alt="{{ __('Gallery photo') }} {{ $index + 1 }}" 
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" loading="lazy">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <div class="absolute bottom-2 left-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <span class="bg-white/90 backdrop-blur-sm text-gray-800 text-xs font-bold px-2 py-1 rounded-lg shadow">#{{ $index + 1 }}</span>
                            </div>
                            <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <span class="bg-white/90 backdrop-blur-sm text-gray-800 p-1.5 rounded-lg shadow flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7" />
                                    </svg>
                                </span>
                            </div>
                        </button>
                    @endforeach
                </div>

                {{-- Lightbox --}}
                <div x-show="lightbox" x-cloak
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200"
                    class="fixed inset-0 z-[100] flex items-center justify-center bg-black/95 backdrop-blur-md">
                    
                    {{-- Close button --}}
                    <button type="button" @click="closeLightbox()" class="absolute top-4 right-4 text-white/80 hover:text-white p-2 rounded-full hover:bg-white/10 transition z-20">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                    
                    {{-- Image counter --}}
                    <div class="absolute top-4 left-4 text-white/80 text-sm font-medium bg-black/30 backdrop-blur-sm px-3 py-1.5 rounded-full">
                        <span x-text="currentPhoto + 1"></span> / <span x-text="photos.length"></span>
                    </div>
                    
                    {{-- Main image --}}
                    <img :src="photos[currentPhoto]" alt="Gallery" class="max-w-[90vw] max-h-[85vh] object-contain rounded-2xl shadow-2xl">
                    
                    {{-- Navigation arrows --}}
                    <button x-show="photos.length > 1" @click="prev()" class="absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center text-white transition z-20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                    </button>
                    <button x-show="photos.length > 1" @click="next()" class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center text-white transition z-20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </button>
                    
                    {{-- Thumbnail strip --}}
                    <div x-show="photos.length > 1" class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2 p-2 bg-black/50 backdrop-blur-sm rounded-xl max-w-[90vw] overflow-x-auto">
                        <template x-for="(photo, index) in photos" :key="index">
                            <button type="button" @click="currentPhoto = index" 
                                :class="currentPhoto === index ? 'ring-2 ring-white scale-110' : 'opacity-60 hover:opacity-100'"
                                class="w-12 h-12 rounded-lg overflow-hidden flex-shrink-0 transition-all duration-300">
                                <img :src="photo" alt="Thumbnail" class="w-full h-full object-cover">
                            </button>
                        </template>
                    </div>
                </div>
            @else
                {{-- Empty State --}}
                <div class="bg-gradient-to-br from-amber-50 to-orange-50 border-2 border-dashed border-amber-200 rounded-2xl p-8 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-amber-100 flex items-center justify-center">
                        <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <p class="text-amber-800 font-medium mb-1">{{ __('No photos in your gallery') }}</p>
                    <p class="text-sm text-amber-600">{{ __('Upload photos from your profile settings') }}</p>
                    <a href="{{ route('profile.edit') }}#photos" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-gradient-to-r from-[#C8A356] to-[#A88932] text-white rounded-lg text-sm font-semibold hover:shadow-lg transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        {{ __('Add Photos') }}
                    </a>
                </div>
            @endif
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

    <!-- ==================== FLOATING EDIT MODALS ==================== -->
    
    <!-- Name Edit Modal -->
    <div x-show="editModal === 'name'" x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        class="fixed inset-0 z-[150] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div @click.away="closeEdit()" class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100">
            <div class="bg-gradient-to-r from-[#6A8F3B] to-[#5a7a2f] px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        {{ __('Edit Name') }}
                    </h3>
                    <button @click="closeEdit()" class="text-white/80 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <div x-show="errorMessage" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm" x-text="errorMessage"></div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Your Name') }}</label>
                <input type="text" x-model="formData.name" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-[#6A8F3B] focus:ring-2 focus:ring-[#6A8F3B]/20 outline-none transition" placeholder="{{ __('Enter your name') }}">
                <div class="flex gap-3 mt-6">
                    <button @click="closeEdit()" class="flex-1 px-4 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition">{{ __('Cancel') }}</button>
                    <button @click="saveField('name')" :disabled="saving" class="flex-1 px-4 py-3 bg-gradient-to-r from-[#6A8F3B] to-[#5a7a2f] text-white font-semibold rounded-xl hover:shadow-lg transition flex items-center justify-center gap-2">
                        <svg x-show="saving" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span x-text="saving ? '{{ __('Saving...') }}' : '{{ __('Save') }}'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Email Edit Modal -->
    <div x-show="editModal === 'email'" x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        class="fixed inset-0 z-[150] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div @click.away="closeEdit()" class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        {{ __('Edit Email') }}
                    </h3>
                    <button @click="closeEdit()" class="text-white/80 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <div x-show="errorMessage" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm" x-text="errorMessage"></div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Email Address') }}</label>
                <input type="email" x-model="formData.email" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition" placeholder="{{ __('Enter your email') }}">
                <p class="text-xs text-gray-500 mt-2">{{ __('You may need to verify your new email address.') }}</p>
                <div class="flex gap-3 mt-6">
                    <button @click="closeEdit()" class="flex-1 px-4 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition">{{ __('Cancel') }}</button>
                    <button @click="saveField('email')" :disabled="saving" class="flex-1 px-4 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold rounded-xl hover:shadow-lg transition flex items-center justify-center gap-2">
                        <svg x-show="saving" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span x-text="saving ? '{{ __('Saving...') }}' : '{{ __('Save') }}'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Phone Edit Modal -->
    <div x-show="editModal === 'phone'" x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        class="fixed inset-0 z-[150] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div @click.away="closeEdit()" class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="bg-gradient-to-r from-emerald-500 to-teal-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        {{ __('Edit Phone') }}
                    </h3>
                    <button @click="closeEdit()" class="text-white/80 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <div x-show="errorMessage" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm" x-text="errorMessage"></div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Phone Number') }}</label>
                <input type="tel" x-model="formData.phone" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition" placeholder="{{ __('Enter your phone number') }}" dir="ltr">
                <div class="flex gap-3 mt-6">
                    <button @click="closeEdit()" class="flex-1 px-4 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition">{{ __('Cancel') }}</button>
                    <button @click="saveField('phone')" :disabled="saving" class="flex-1 px-4 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-semibold rounded-xl hover:shadow-lg transition flex items-center justify-center gap-2">
                        <svg x-show="saving" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span x-text="saving ? '{{ __('Saving...') }}' : '{{ __('Save') }}'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Farm Name Edit Modal -->
    <div x-show="editModal === 'farm_name'" x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        class="fixed inset-0 z-[150] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div @click.away="closeEdit()" class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">🌿 {{ __('Edit Farm Name') }}</h3>
                    <button @click="closeEdit()" class="text-white/80 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <div x-show="errorMessage" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm" x-text="errorMessage"></div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Farm Name') }}</label>
                <input type="text" x-model="formData.farm_name" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-500/20 outline-none transition" placeholder="{{ __('Enter your farm name') }}">
                <div class="flex gap-3 mt-6">
                    <button @click="closeEdit()" class="flex-1 px-4 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition">{{ __('Cancel') }}</button>
                    <button @click="saveField('farm_name')" :disabled="saving" class="flex-1 px-4 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold rounded-xl hover:shadow-lg transition flex items-center justify-center gap-2">
                        <svg x-show="saving" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span x-text="saving ? '{{ __('Saving...') }}' : '{{ __('Save') }}'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Company Name Edit Modal (Carrier) -->
    <div x-show="editModal === 'company_name'" x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        class="fixed inset-0 z-[150] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div @click.away="closeEdit()" class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">🏢 {{ __('Edit Company Name') }}</h3>
                    <button @click="closeEdit()" class="text-white/80 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <div x-show="errorMessage" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm" x-text="errorMessage"></div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Company Name') }}</label>
                <input type="text" x-model="formData.company_name" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition" placeholder="{{ __('Enter your company name') }}">
                <div class="flex gap-3 mt-6">
                    <button @click="closeEdit()" class="flex-1 px-4 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition">{{ __('Cancel') }}</button>
                    <button @click="saveField('company_name')" :disabled="saving" class="flex-1 px-4 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold rounded-xl hover:shadow-lg transition flex items-center justify-center gap-2">
                        <svg x-show="saving" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span x-text="saving ? '{{ __('Saving...') }}' : '{{ __('Save') }}'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Mill Name Edit Modal -->
    <div x-show="editModal === 'mill_name'" x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        class="fixed inset-0 z-[150] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div @click.away="closeEdit()" class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="bg-gradient-to-r from-amber-500 to-orange-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">🏭 {{ __('Edit Mill Name') }}</h3>
                    <button @click="closeEdit()" class="text-white/80 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <div x-show="errorMessage" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm" x-text="errorMessage"></div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Mill Name') }}</label>
                <input type="text" x-model="formData.mill_name" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none transition" placeholder="{{ __('Enter your mill name') }}">
                <div class="flex gap-3 mt-6">
                    <button @click="closeEdit()" class="flex-1 px-4 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition">{{ __('Cancel') }}</button>
                    <button @click="saveField('mill_name')" :disabled="saving" class="flex-1 px-4 py-3 bg-gradient-to-r from-amber-500 to-orange-600 text-white font-semibold rounded-xl hover:shadow-lg transition flex items-center justify-center gap-2">
                        <svg x-show="saving" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span x-text="saving ? '{{ __('Saving...') }}' : '{{ __('Save') }}'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Packer Name Edit Modal -->
    <div x-show="editModal === 'packer_name'" x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        class="fixed inset-0 z-[150] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div @click.away="closeEdit()" class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="bg-gradient-to-r from-purple-500 to-violet-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">📦 {{ __('Edit Packer Name') }}</h3>
                    <button @click="closeEdit()" class="text-white/80 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <div x-show="errorMessage" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm" x-text="errorMessage"></div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Packer Name') }}</label>
                <input type="text" x-model="formData.packer_name" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 outline-none transition" placeholder="{{ __('Enter your packer name') }}">
                <div class="flex gap-3 mt-6">
                    <button @click="closeEdit()" class="flex-1 px-4 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition">{{ __('Cancel') }}</button>
                    <button @click="saveField('packer_name')" :disabled="saving" class="flex-1 px-4 py-3 bg-gradient-to-r from-purple-500 to-violet-600 text-white font-semibold rounded-xl hover:shadow-lg transition flex items-center justify-center gap-2">
                        <svg x-show="saving" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span x-text="saving ? '{{ __('Saving...') }}' : '{{ __('Save') }}'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- More Settings Modal (Full Profile Edit) -->
    <div x-show="editModal === 'more'" x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        class="fixed inset-0 z-[150] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm overflow-y-auto">
        <div @click.away="closeEdit()" class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden my-8">
            <div class="bg-gradient-to-r from-[#6A8F3B] to-[#C8A356] px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        {{ __('Profile Settings') }}
                    </h3>
                    <button @click="closeEdit()" class="text-white/80 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>
            <div class="p-6 max-h-[70vh] overflow-y-auto">
                <div x-show="errorMessage" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm" x-text="errorMessage"></div>
                
                <!-- Quick Links Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <button @click="openEdit('name')" class="flex flex-col items-center gap-3 p-4 bg-gray-50 hover:bg-gray-100 rounded-xl transition group">
                        <div class="w-12 h-12 rounded-full bg-[#6A8F3B]/10 flex items-center justify-center group-hover:scale-110 transition">
                            <svg class="w-6 h-6 text-[#6A8F3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700">{{ __('Name') }}</span>
                    </button>
                    
                    <button @click="openEdit('email')" class="flex flex-col items-center gap-3 p-4 bg-gray-50 hover:bg-gray-100 rounded-xl transition group">
                        <div class="w-12 h-12 rounded-full bg-blue-500/10 flex items-center justify-center group-hover:scale-110 transition">
                            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700">{{ __('Email') }}</span>
                    </button>
                    
                    <button @click="openEdit('phone')" class="flex flex-col items-center gap-3 p-4 bg-gray-50 hover:bg-gray-100 rounded-xl transition group">
                        <div class="w-12 h-12 rounded-full bg-emerald-500/10 flex items-center justify-center group-hover:scale-110 transition">
                            <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700">{{ __('Phone') }}</span>
                    </button>
                    
                    <a href="{{ route('profile.edit') }}#photos" class="flex flex-col items-center gap-3 p-4 bg-gray-50 hover:bg-gray-100 rounded-xl transition group">
                        <div class="w-12 h-12 rounded-full bg-amber-500/10 flex items-center justify-center group-hover:scale-110 transition">
                            <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700">{{ __('Photos') }}</span>
                    </a>
                    
                    <a href="{{ route('profile.edit') }}" class="flex flex-col items-center gap-3 p-4 bg-gray-50 hover:bg-gray-100 rounded-xl transition group">
                        <div class="w-12 h-12 rounded-full bg-purple-500/10 flex items-center justify-center group-hover:scale-110 transition">
                            <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700">{{ __('Location') }}</span>
                    </a>
                    
                    <a href="{{ route('password.request') }}" class="flex flex-col items-center gap-3 p-4 bg-gray-50 hover:bg-gray-100 rounded-xl transition group">
                        <div class="w-12 h-12 rounded-full bg-red-500/10 flex items-center justify-center group-hover:scale-110 transition">
                            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700">{{ __('Password') }}</span>
                    </a>
                </div>
                
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <a href="{{ route('profile.edit') }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        {{ __('Open Full Profile Settings') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
