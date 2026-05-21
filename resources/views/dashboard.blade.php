@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-100 flex flex-col md:flex-row" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
    x-data="{
        sidebarOpen: false,
        editModal: null,
        saving: false,
        successMessage: '',
        errorMessage: '',
        formData: {
            name: @js(Auth::user()->name),
            email: @js(Auth::user()->email),
            phone: @js(Auth::user()->phone ?? ''),
            farm_name: @js(Auth::user()->farm_name ?? ''),
            farm_location: @js(Auth::user()->farm_location ?? ''),
            farm_name_ar: @js(Auth::user()->farm_name_ar ?? ''),
            company_name: @js(Auth::user()->company_name ?? ''),
            mill_name: @js(Auth::user()->mill_name ?? ''),
            packer_name: @js(Auth::user()->packer_name ?? ''),
            tree_number: @js(Auth::user()->tree_number ?? ''),
            olive_type: @js(Auth::user()->olive_type ?? ''),
            capacity: @js(Auth::user()->capacity ?? ''),
            fleet_size: @js(Auth::user()->fleet_size ?? ''),
            camion_capacity: @js(Auth::user()->camion_capacity ?? ''),
            packaging_types: @js(Auth::user()->packaging_types ?? '')
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
        },
        async acceptLoad(id) {
            if (!confirm('{{ __('Accept this transport task?') }}')) return;
            this.saving = true;
            try {
                const response = await fetch(`/api/v1/mobile/loads/${id}/accept`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                if (response.ok) {
                    location.reload();
                } else {
                    alert('{{ __('Failed to accept load.') }}');
                }
            } catch (error) {
                alert('{{ __('Network error.') }}');
            }
            this.saving = false;
        },
        async rejectLoad(id) {
            if (!confirm('{{ __('Reject this transport task?') }}')) return;
            this.saving = true;
            try {
                const response = await fetch(`/api/v1/mobile/loads/${id}/reject`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                if (response.ok) {
                    location.reload();
                } else {
                    alert('{{ __('Failed to reject load.') }}');
                }
            } catch (error) {
                alert('{{ __('Network error.') }}');
            }
            this.saving = false;
        },
        async finalizeTrip(tripId, pin) {
            if (!pin) return;
            this.saving = true;
            try {
                // 1. Verify PIN via POD endpoint
                const podRes = await fetch(`/api/v1/trips/${tripId}/pod`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ signed_pin: pin })
                });
                
                if (!podRes.ok) {
                    const result = await podRes.json();
                    throw new Error(result.message || '{{ __('Invalid PIN') }}');
                }

                // 2. Complete Trip
                const response = await fetch(`/api/v1/trips/${tripId}/complete`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                
                if (response.ok) {
                    this.successMessage = '{{ __('Trip completed successfully!') }}';
                    setTimeout(() => location.reload(), 2000);
                } else {
                    const result = await response.json();
                    throw new Error(result.message || '{{ __('Failed to complete trip.') }}');
                }
            } catch (error) {
                alert(error.message);
            }
            this.saving = false;
        },

    }"
    @keydown.escape.window="closeEdit()">
    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-40 md:hidden" x-transition></div>

    <!-- STORE LAYOUT: Left Sidebar | Center Feed | Right Sidebar -->
    <div class="flex w-full min-h-screen">

    <!-- LEFT SIDEBAR -->
    <aside :class="sidebarOpen ? 'translate-x-0' : (document.documentElement.dir === 'rtl' ? 'translate-x-full' : '-translate-x-full')"
           class="fixed md:sticky top-0 md:top-[72px] h-screen md:h-[calc(100vh-72px)] w-72 bg-white border-r border-gray-100 shadow-xl md:shadow-none z-50 md:z-10 flex flex-col overflow-y-auto transition-transform duration-300 md:translate-x-0 flex-shrink-0">

        <!-- Close btn (mobile) -->
        <div class="md:hidden flex items-center justify-between px-4 py-3 border-b border-gray-100">
            <span class="font-bold text-gray-900">{{ __('My Store') }}</span>
            <button @click="sidebarOpen = false" class="p-2 rounded-xl hover:bg-gray-100 text-gray-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Profile block -->
        <div class="p-5 border-b border-gray-100">
            @php $sidePhotos = array_values(array_filter(Auth::user()->cover_photos ?? [], fn($p) => is_string($p))); @endphp
            @if(count($sidePhotos) > 0)
            <div class="relative h-20 rounded-2xl overflow-hidden mb-4 group">
                <img src="{{ Storage::url($sidePhotos[0]) }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-b from-transparent to-black/40"></div>
                <label class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition cursor-pointer bg-black/30">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <input type="file" @change="uploadPhoto('cover', $event)" accept="image/*" class="hidden">
                </label>
            </div>
            @else
            <label class="block relative h-20 rounded-2xl overflow-hidden mb-4 cursor-pointer group">
                <div class="w-full h-full bg-gradient-to-br from-[#6A8F3B] to-[#C8A356] flex flex-col items-center justify-center text-white/80 group-hover:text-white transition">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span class="text-xs font-bold">{{ __('Add Cover') }}</span>
                </div>
                <input type="file" @change="uploadPhoto('cover', $event)" accept="image/*" class="hidden">
            </label>
            @endif

            <div class="flex items-center gap-3">
                <div class="relative group cursor-pointer flex-shrink-0" @click="$refs.sideProfileInput.click()">
                    <div class="w-14 h-14 rounded-2xl p-0.5 bg-gradient-to-br from-[#6A8F3B] via-[#C8A356] to-[#6A8F3B] shadow-lg">
                        <div class="w-full h-full rounded-xl overflow-hidden bg-white">
                            @if(Auth::user()->profile_picture)
                                <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-[#6A8F3B] to-[#C8A356] flex items-center justify-center">
                                    <span class="text-white font-bold text-xl">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 border-2 border-white rounded-full"></div>
                    <input type="file" x-ref="sideProfileInput" @change="uploadPhoto('profile', $event)" accept="image/*" class="hidden">
                </div>
                <div class="min-w-0">
                    <button @click="openEdit('name')" class="group flex items-center gap-1">
                        <span id="display-name" class="font-bold text-gray-900 text-sm truncate max-w-[140px]">{{ Auth::user()->name }}</span>
                        <svg class="w-3 h-3 text-gray-400 opacity-0 group-hover:opacity-100 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    </button>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold mt-0.5
                        @if(Auth::user()->role === 'farmer') bg-green-100 text-green-700
                        @elseif(Auth::user()->role === 'carrier') bg-blue-100 text-blue-700
                        @elseif(Auth::user()->role === 'mill') bg-amber-100 text-amber-700
                        @elseif(Auth::user()->role === 'packer') bg-purple-100 text-purple-700
                        @else bg-gray-100 text-gray-700 @endif">
                        @if(Auth::user()->role === 'farmer') 🌱 {{ __('Farmer') }}
                        @elseif(Auth::user()->role === 'carrier') 🚚 {{ __('Carrier') }}
                        @elseif(Auth::user()->role === 'mill') ⚙️ {{ __('Mill') }}
                        @elseif(Auth::user()->role === 'packer') 📦 {{ __('Packer') }}
                        @else 👤 {{ __('User') }} @endif
                    </span>
                </div>
            </div>

            <div class="mt-4 space-y-1.5">
                @if(Auth::user()->role === 'farmer')
                <button @click="openEdit('farm_name')" class="w-full flex items-center gap-2 px-3 py-2 bg-green-50 hover:bg-green-100 rounded-xl text-sm transition group">
                    <span>🌿</span><span id="display-farm_name" class="flex-1 truncate text-gray-700 font-medium text-right">{{ Auth::user()->farm_name ?: __('Add Farm Name') }}</span>
                    <svg class="w-3 h-3 text-gray-400 opacity-0 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                </button>
                <button @click="openEdit('farm_location')" class="w-full flex items-center gap-2 px-3 py-2 bg-green-50 hover:bg-green-100 rounded-xl text-sm transition group">
                    <span>📍</span><span id="display-farm_location" class="flex-1 truncate text-gray-700 font-medium text-right">{{ Auth::user()->farm_location ?: __('Add Farm Location') }}</span>
                    <svg class="w-3 h-3 text-gray-400 opacity-0 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                </button>
                <button @click="openEdit('olive_type')" class="w-full flex items-center gap-2 px-3 py-2 bg-green-50 hover:bg-green-100 rounded-xl text-sm transition group">
                    <span>🫒</span><span id="display-olive_type" class="flex-1 truncate text-gray-700 font-medium text-right">{{ Auth::user()->olive_type ?: __('Add Olive Type') }}</span>
                    <svg class="w-3 h-3 text-gray-400 opacity-0 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                </button>
                <button @click="openEdit('tree_number')" class="w-full flex items-center gap-2 px-3 py-2 bg-green-50 hover:bg-green-100 rounded-xl text-sm transition group">
                    <span>🌳</span><span id="display-tree_number" class="flex-1 truncate text-gray-700 font-medium text-right">{{ Auth::user()->tree_number ?: __('Add Tree Count') }}</span>
                    <svg class="w-3 h-3 text-gray-400 opacity-0 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                </button>
                @elseif(Auth::user()->role === 'carrier')
                <button @click="openEdit('company_name')" class="w-full flex items-center gap-2 px-3 py-2 bg-blue-50 hover:bg-blue-100 rounded-xl text-sm transition group">
                    <span>🏢</span><span id="display-company_name" class="flex-1 truncate text-gray-700 font-medium text-right">{{ Auth::user()->company_name ?: __('Add Company') }}</span>
                    <svg class="w-3 h-3 text-gray-400 opacity-0 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                </button>
                <button @click="openEdit('camion_capacity')" class="w-full flex items-center gap-2 px-3 py-2 bg-blue-50 hover:bg-blue-100 rounded-xl text-sm transition group">
                    <span>⚖️</span><span id="display-camion_capacity" class="flex-1 truncate text-gray-700 font-medium text-right">{{ Auth::user()->camion_capacity ? Auth::user()->camion_capacity . ' Kg' : __('Add Capacity') }}</span>
                    <svg class="w-3 h-3 text-gray-400 opacity-0 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                </button>
                @elseif(Auth::user()->role === 'mill')
                <button @click="openEdit('mill_name')" class="w-full flex items-center gap-2 px-3 py-2 bg-amber-50 hover:bg-amber-100 rounded-xl text-sm transition group">
                    <span>🏭</span><span id="display-mill_name" class="flex-1 truncate text-gray-700 font-medium text-right">{{ Auth::user()->mill_name ?: __('Add Mill Name') }}</span>
                    <svg class="w-3 h-3 text-gray-400 opacity-0 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                </button>
                @elseif(Auth::user()->role === 'packer')
                <button @click="openEdit('packer_name')" class="w-full flex items-center gap-2 px-3 py-2 bg-purple-50 hover:bg-purple-100 rounded-xl text-sm transition group">
                    <span>📦</span><span id="display-packer_name" class="flex-1 truncate text-gray-700 font-medium text-right">{{ Auth::user()->packer_name ?: __('Add Packer Name') }}</span>
                    <svg class="w-3 h-3 text-gray-400 opacity-0 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                </button>
                @endif
                <button @click="openEdit('phone')" class="w-full flex items-center gap-2 px-3 py-2 bg-gray-50 hover:bg-gray-100 rounded-xl text-sm transition group">
                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    <span id="display-phone" class="flex-1 truncate text-gray-600">{{ Auth::user()->phone ?: __('Add Phone') }}</span>
                    <svg class="w-3 h-3 text-gray-400 opacity-0 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                </button>
                <button @click="openEdit('email')" class="w-full flex items-center gap-2 px-3 py-2 bg-gray-50 hover:bg-gray-100 rounded-xl text-sm transition group">
                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span id="display-email" class="flex-1 truncate text-gray-600">{{ Auth::user()->email }}</span>
                    <svg class="w-3 h-3 text-gray-400 opacity-0 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                </button>
            </div>
        </div>

        <!-- Profile completion bar -->
        <div class="px-5 py-4 border-b border-gray-100">
            <div class="flex justify-between mb-1.5">
                <span class="text-xs font-bold text-gray-400 uppercase">{{ __('Profile') }}</span>
                <span class="text-xs font-bold text-[#6A8F3B]">{{ $profileCompletion }}%</span>
            </div>
            <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-[#6A8F3B] to-[#C8A356] rounded-full transition-all duration-700" style="width:{{ $profileCompletion }}%"></div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="p-3 space-y-0.5 flex-1">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 bg-[#6A8F3B]/10 text-[#6A8F3B] rounded-xl font-bold text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                {{ __('Dashboard') }}
            </a>
            @if(Auth::user()->role !== 'carrier')
            <a href="{{ route('listings.create') }}" class="flex items-center gap-3 px-4 py-2.5 text-gray-600 hover:bg-gray-50 hover:text-[#6A8F3B] rounded-xl font-bold text-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ __('Add Product') }}
            </a>
            @endif
            @if(in_array(Auth::user()->role, ['farmer','mill']))
            <a href="#inventory-section" @click="sidebarOpen=false" class="flex items-center gap-3 px-4 py-2.5 text-gray-600 hover:bg-gray-50 hover:text-[#6A8F3B] rounded-xl font-bold text-sm transition">
                <span>{{ Auth::user()->role === 'mill' ? '🛢️' : '🌿' }}</span>
                {{ Auth::user()->role === 'mill' ? __('Tanks & Stock') : __('My Batches') }}
            </a>
            @endif
            @if(Auth::user()->role === 'carrier')
            <a href="#assigned-tasks" @click="sidebarOpen=false" class="flex items-center gap-3 px-4 py-2.5 text-gray-600 hover:bg-gray-50 hover:text-[#6A8F3B] rounded-xl font-bold text-sm transition">
                <span>🚚</span>{{ __('Transport Tasks') }}
            </a>
            @endif
            <a href="{{ route('messages.inbox') }}" class="flex items-center gap-3 px-4 py-2.5 text-gray-600 hover:bg-gray-50 hover:text-[#6A8F3B] rounded-xl font-bold text-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                {{ __('Messages') }}
            </a>
            <a href="{{ route('home') }}" class="flex items-center gap-3 px-4 py-2.5 text-gray-600 hover:bg-gray-50 hover:text-[#6A8F3B] rounded-xl font-bold text-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                {{ __('Marketplace') }}
            </a>
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-gray-600 hover:bg-gray-50 hover:text-[#6A8F3B] rounded-xl font-bold text-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                {{ __('Settings') }}
            </a>
        </nav>
        <div class="p-4 border-t border-gray-100">
            <button @click="openEdit('more')" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-[#6A8F3B] to-[#5a7a2f] text-white font-bold rounded-xl hover:shadow-lg transition text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                {{ __('More Settings') }}
            </button>
        </div>
    </aside>

    <!-- CENTER FEED -->
    <main class="flex-1 min-w-0 overflow-y-auto">
        <!-- Mobile topbar -->
        <div class="md:hidden sticky top-0 z-30 flex items-center justify-between px-4 py-3 bg-white border-b border-gray-100 shadow-sm">
            <button @click="sidebarOpen = true" class="p-2 bg-gray-50 rounded-xl text-gray-700 border border-gray-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <span class="font-bold text-gray-900 text-sm">{{ Auth::user()->name }}</span>
            @if(Auth::user()->role !== 'carrier')
            <a href="{{ route('listings.create') }}" class="p-2 bg-[#6A8F3B] text-white rounded-xl">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            </a>
            @else
            <div class="w-9"></div>
            @endif
        </div>

        <!-- Success Toast -->
        <div x-show="successMessage" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200" class="fixed top-4 left-1/2 -translate-x-1/2 z-[200]">
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 text-white px-6 py-3 rounded-xl shadow-2xl flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span class="font-medium" x-text="successMessage"></span>
            </div>
        </div>
        @if(session('success'))
        <div class="fixed top-4 left-1/2 -translate-x-1/2 z-50">
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 text-white px-8 py-4 rounded-2xl shadow-2xl flex items-center gap-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="font-bold">{{ session('success') }}</span>
            </div>
        </div>
        @endif
        @if(session('error'))
        <div class="fixed top-4 left-1/2 -translate-x-1/2 z-50">
            <div class="bg-red-500 text-white px-8 py-4 rounded-2xl shadow-2xl flex items-center gap-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="font-bold">{{ session('error') }}</span>
            </div>
        </div>
        @endif

        <div class="p-4 md:p-6 space-y-5 max-w-3xl mx-auto pb-12">

            <!-- Profile Hero Card -->
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
                @php
                    $validPhotos = array_values(array_filter(Auth::user()->cover_photos ?? [], fn($p) => is_string($p)));
                    $photoCount = count($validPhotos);
                @endphp
                @if($photoCount > 0)
                <div class="h-44 relative overflow-hidden" x-data="{ currentSlide: 0, slides: {{ $photoCount }} }" x-init="setInterval(() => { currentSlide = (currentSlide + 1) % slides }, 4000)">
                    @foreach($validPhotos as $index => $photo)
                    <div x-show="currentSlide === {{ $index }}" x-transition:enter="transition ease-out duration-1000" x-transition:enter-start="opacity-0 scale-105" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-1000" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="absolute inset-0">
                        <img src="{{ Storage::url($photo) }}" class="w-full h-full object-cover" loading="lazy">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent"></div>
                    </div>
                    @endforeach
                    @if($photoCount > 1)
                    <div class="absolute bottom-2 left-1/2 -translate-x-1/2 flex gap-1.5 z-10">
                        @foreach($validPhotos as $index => $photo)
                        <button @click="currentSlide = {{ $index }}" :class="currentSlide === {{ $index }} ? 'bg-white w-5' : 'bg-white/50 w-2'" class="h-1.5 rounded-full transition-all duration-300"></button>
                        @endforeach
                    </div>
                    @endif
                    <label class="absolute top-3 right-3 z-10 bg-white/20 hover:bg-white/40 backdrop-blur-sm text-white text-xs font-bold px-3 py-1.5 rounded-full flex items-center gap-1.5 cursor-pointer transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>{{ __('Add') }}
                        <input type="file" @change="uploadPhoto('cover', $event)" accept="image/*" class="hidden">
                    </label>
                </div>
                @else
                <div class="h-44 bg-gradient-to-br from-[#6A8F3B] via-[#7a9f4b] to-[#C8A356] relative flex items-center justify-center">
                    <label class="flex flex-col items-center gap-2 text-white/80 hover:text-white cursor-pointer">
                        <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg></div>
                        <span class="text-sm font-medium">{{ __('Add Cover Photo') }}</span>
                        <input type="file" @change="uploadPhoto('cover', $event)" accept="image/*" class="hidden">
                    </label>
                </div>
                @endif

                <div class="px-5 pb-5 -mt-10 relative z-10">
                    <div class="flex items-end gap-4">
                        <div class="relative group cursor-pointer flex-shrink-0" @click="$refs.heroProfileInput.click()">
                            <div class="w-20 h-20 rounded-2xl p-0.5 bg-gradient-to-br from-[#6A8F3B] via-[#C8A356] to-[#6A8F3B] shadow-2xl">
                                <div class="w-full h-full rounded-xl overflow-hidden bg-white">
                                    @if(Auth::user()->profile_picture)
                                    <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" class="w-full h-full object-cover">
                                    @else
                                    <div class="w-full h-full bg-gradient-to-br from-[#6A8F3B] to-[#C8A356] flex items-center justify-center">
                                        <span class="text-white font-bold text-3xl">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="absolute inset-0 rounded-2xl bg-black/50 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/></svg>
                            </div>
                            <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-500 border-2 border-white rounded-full flex items-center justify-center"><div class="w-2 h-2 bg-white rounded-full animate-pulse"></div></div>
                            <input type="file" x-ref="heroProfileInput" @change="uploadPhoto('profile', $event)" accept="image/*" class="hidden">
                        </div>
                        <div class="flex-1 pb-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <button @click="openEdit('name')" class="group flex items-center gap-1.5">
                                    <h1 class="text-xl font-bold text-white drop-shadow-lg">{{ Auth::user()->name }}</h1>
                                    <svg class="w-4 h-4 text-white/70 opacity-0 group-hover:opacity-100 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </button>
                                @if(Auth::user()->is_verified)<span class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-500 text-white rounded-full text-xs font-bold"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>{{ __('Verified') }}</span>@endif
                            </div>
                            <p class="text-white/80 text-sm mt-0.5 drop-shadow">{{ Auth::user()->phone ?: Auth::user()->email }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stories -->
            <div id="stories-section" x-data="{
                stories:[],current:null,currentIndex:0,loading:true,error:false,userId:{{ Auth::id() }},progress:0,timer:null,
                fetchStories(){fetch(`/user/${this.userId}/stories`).then(r=>r.json()).then(d=>{this.stories=d;}).catch(()=>{this.error=true;}).finally(()=>{this.loading=false;});},
                openStory(s,i){this.current=s;this.currentIndex=i;this.startProgress();},
                closeStory(){this.current=null;this.stopProgress();},
                nextStory(){if(this.currentIndex<this.stories.length-1){this.currentIndex++;this.current=this.stories[this.currentIndex];this.startProgress();}else{this.closeStory();}},
                prevStory(){if(this.currentIndex>0){this.currentIndex--;this.current=this.stories[this.currentIndex];this.startProgress();}},
                startProgress(){this.progress=0;this.stopProgress();if(this.current?.media_type==='image'){this.timer=setInterval(()=>{this.progress+=2;if(this.progress>=100)this.nextStory();},100);}},
                stopProgress(){if(this.timer)clearInterval(this.timer);}
            }" x-init="fetchStories()" @keydown.escape.window="closeStory()" class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-50">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-[#6A8F3B] to-[#C8A356] flex items-center justify-center shadow">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <h2 class="font-bold text-gray-900 text-sm">{{ __('My Stories') }}</h2>
                            <p class="text-[11px] text-gray-400">{{ __('Stay until deleted') }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('stories.store') }}" enctype="multipart/form-data">
                        @csrf
                        <label class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-[#6A8F3B] to-[#5a7a2f] text-white rounded-xl text-xs font-bold cursor-pointer hover:shadow-md transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            {{ __('Add') }}
                            <input type="file" name="media" accept="image/*,video/*" class="hidden" required onchange="this.form.submit()">
                        </label>
                    </form>
                </div>
                <div class="px-5 py-4">
                    <template x-if="loading && !error"><div class="flex gap-3"><template x-for="i in 5" :key="i"><div class="w-14 h-14 rounded-2xl bg-gray-100 animate-pulse flex-shrink-0"></div></template></div></template>
                    <template x-if="!loading && !error && stories.length === 0"><div class="text-center py-4 text-gray-400 text-sm">{{ __('No stories yet') }} — <span class="text-[#6A8F3B] font-bold">{{ __('add your first!') }}</span></div></template>
                    <div x-show="!loading && !error && stories.length > 0" x-cloak class="flex gap-3 overflow-x-auto pb-1" style="scrollbar-width:none;">
                        <template x-for="(story, index) in stories" :key="story.id">
                            <button type="button" class="group relative flex-shrink-0 focus:outline-none" @click="openStory(story, index)">
                                <div class="w-14 h-14 rounded-2xl p-[2px] bg-gradient-to-br from-[#6A8F3B] via-[#C8A356] to-[#6A8F3B] group-hover:scale-110 group-hover:shadow-xl transition-all duration-300">
                                    <div class="w-full h-full rounded-xl overflow-hidden bg-white">
                                        <template x-if="story.media_type === 'image'"><img :src="story.url" class="w-full h-full object-cover"></template>
                                        <template x-if="story.media_type === 'video'"><div class="w-full h-full bg-gray-900 flex items-center justify-center"><svg class="w-5 h-5 text-white/90" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg></div></template>
                                    </div>
                                </div>
                            </button>
                        </template>
                    </div>
                </div>
                <!-- Fullscreen viewer -->
                <div x-show="current" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/90 backdrop-blur-md" style="display:none;">
                    <div class="absolute top-4 left-4 right-4 flex gap-1">
                        <template x-for="(s,i) in stories" :key="s.id"><div class="flex-1 h-1 bg-white/30 rounded-full overflow-hidden"><div class="h-full bg-white rounded-full transition-all duration-100" :style="{width: i<currentIndex?'100%':(i===currentIndex?progress+'%':'0%')}"></div></div></template>
                    </div>
                    <button @click="prevStory()" class="absolute left-0 top-0 bottom-0 w-1/3 z-10" x-show="currentIndex > 0"></button>
                    <button @click="nextStory()" class="absolute right-0 top-0 bottom-0 w-1/3 z-10"></button>
                    <div class="relative max-w-lg w-full mx-4">
                        <button class="absolute -top-12 right-0 text-white/80 hover:text-white p-2 rounded-full hover:bg-white/10 transition z-20" @click="closeStory()"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                        <template x-if="current && current.media_type === 'image'"><img :src="current.url" class="w-full max-h-[75vh] object-contain rounded-3xl shadow-2xl"></template>
                        <template x-if="current && current.media_type === 'video'"><video :src="current.url" controls autoplay playsinline class="w-full max-h-[75vh] rounded-3xl shadow-2xl bg-black"></video></template>
                        <div x-show="current?.caption" class="absolute bottom-0 left-0 right-0 p-6 bg-gradient-to-t from-black/80 to-transparent rounded-b-3xl"><p class="text-white text-lg font-medium" x-text="current?.caption"></p></div>
                    </div>
                    <button x-show="currentIndex > 0" @click="prevStory()" class="absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center text-white z-20"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></button>
                    <button x-show="currentIndex < stories.length-1" @click="nextStory()" class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center text-white z-20"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></button>
                </div>
            </div>

            <!-- Photo Gallery -->
            @php $galleryPhotos = array_values(array_filter(Auth::user()->cover_photos ?? [], fn($p) => is_string($p) && !empty($p))); @endphp
            @if(count($galleryPhotos) > 0)
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden" x-data="{
                lightbox:false,currentPhoto:0,
                photos:{{ json_encode(array_map(fn($p) => Storage::url($p), $galleryPhotos)) }},
                openLightbox(i){this.currentPhoto=i;this.lightbox=true;},closeLightbox(){this.lightbox=false;},
                next(){this.currentPhoto=(this.currentPhoto+1)%this.photos.length;},prev(){this.currentPhoto=(this.currentPhoto-1+this.photos.length)%this.photos.length;}
            }" @keydown.escape.window="closeLightbox()" @keydown.right.window="lightbox&&next()" @keydown.left.window="lightbox&&prev()">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-50">
                    <div class="flex items-center gap-2"><span class="text-lg">🖼️</span><h2 class="font-bold text-gray-900 text-sm">{{ __('Gallery') }} <span class="text-gray-400 font-normal text-xs">({{ count($galleryPhotos) }}/5)</span></h2></div>
                    <a href="{{ route('profile.edit') }}#photos" class="text-xs text-[#6A8F3B] font-bold hover:underline">{{ __('Manage') }}</a>
                </div>
                <div class="p-3 grid grid-cols-3 sm:grid-cols-5 gap-2">
                    @foreach($galleryPhotos as $index => $photo)
                    <button @click="openLightbox({{ $index }})" class="group relative aspect-square rounded-xl overflow-hidden hover:scale-105 hover:shadow-lg transition-all focus:outline-none focus:ring-2 focus:ring-[#6A8F3B]">
                        <img src="{{ Storage::url($photo) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" loading="lazy">
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition flex items-center justify-center">
                            <svg class="w-5 h-5 text-white opacity-0 group-hover:opacity-100 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                    </button>
                    @endforeach
                </div>
                <div x-show="lightbox" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/95 backdrop-blur-md">
                    <button @click="closeLightbox()" class="absolute top-4 right-4 text-white/80 hover:text-white p-2 rounded-full hover:bg-white/10 transition z-20"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    <div class="absolute top-4 left-4 text-white/80 text-sm bg-black/30 px-3 py-1.5 rounded-full"><span x-text="currentPhoto+1"></span>/<span x-text="photos.length"></span></div>
                    <img :src="photos[currentPhoto]" class="max-w-[90vw] max-h-[85vh] object-contain rounded-2xl shadow-2xl">
                    <button x-show="photos.length>1" @click="prev()" class="absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center text-white z-20"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></button>
                    <button x-show="photos.length>1" @click="next()" class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center text-white z-20"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></button>
                </div>
            </div>
            @endif

            <!-- Inventory (farmer/mill) -->
            @if(in_array(Auth::user()->role, ['farmer','mill']))
            <div id="inventory-section" class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-50">
                    <div class="flex items-center gap-2"><span class="text-xl">{{ Auth::user()->role === 'mill' ? '🛢️' : '🌿' }}</span><div><h2 class="font-bold text-gray-900 text-sm">{{ Auth::user()->role === 'mill' ? __('Tanks & Stock') : __('My Batches') }}</h2><p class="text-[11px] text-gray-400">{{ __('Available quantities') }}</p></div></div>
                    <button @click="alert('{{ __('Coming soon!') }}')" class="text-xs font-bold px-3 py-1.5 bg-[#C8A356]/10 text-[#C8A356] rounded-xl hover:bg-[#C8A356]/20 transition flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>{{ Auth::user()->role === 'mill' ? __('Add Tank') : __('Add Batch') }}</button>
                </div>
                <div class="p-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @forelse($tanks as $tank)
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 hover:border-[#C8A356]/40 transition">
                        <div class="flex justify-between items-center mb-3"><h3 class="font-bold text-gray-900 text-sm">{{ $tank->name }}</h3><span class="text-xs font-bold px-2 py-0.5 bg-[#6A8F3B]/10 text-[#6A8F3B] rounded-lg">{{ $tank->type === 'olive' ? __('Olive') : __('Oil') }}</span></div>
                        @php $pct = $tank->capacity > 0 ? ($tank->current_volume/$tank->capacity)*100 : 0; @endphp
                        <div class="mb-2"><div class="flex justify-between text-xs text-gray-500 mb-1"><span>{{ __('Available') }}</span><span>{{ number_format($tank->current_volume) }}/{{ number_format($tank->capacity) }} {{ $tank->type==='olive'?'Kg':'L' }}</span></div><div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden"><div class="h-full {{ $pct>50?'bg-green-500':($pct>20?'bg-amber-500':'bg-red-500') }} rounded-full" style="width:{{ $pct }}%"></div></div></div>
                        @if($tank->variety)<p class="text-[11px] text-gray-400">{{ __('Variety') }}: {{ $tank->variety }}</p>@endif
                    </div>
                    @empty
                    <div class="col-span-full text-center py-8 text-gray-400"><span class="text-3xl block mb-2">{{ Auth::user()->role==='mill'?'🛢️':'🌿' }}</span><p class="text-sm">{{ __('No data yet') }}</p></div>
                    @endforelse
                </div>
            </div>
            @endif

            <!-- Carrier Tasks -->
            @if(Auth::user()->role === 'carrier')
            <div id="assigned-tasks" class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 bg-gradient-to-r from-blue-600 to-indigo-600">
                    <h2 class="font-bold text-white flex items-center gap-2"><span>🚚</span>{{ __('Transport Tasks') }}</h2>
                    <span class="bg-white/20 text-white px-3 py-0.5 rounded-full text-xs font-bold">{{ $assignedLoads->total() }} {{ __('total') }}</span>
                </div>
                <div class="p-4 space-y-3">
                    @forelse($assignedLoads as $load)
                    <div class="border border-gray-100 rounded-xl p-4 hover:border-blue-200 hover:shadow-sm transition">
                        <div class="flex flex-col sm:flex-row justify-between items-start gap-3">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1.5"><span class="text-xs font-bold px-2 py-0.5 bg-blue-100 text-blue-700 rounded-lg">#{{ $load->id }}</span><span class="text-sm font-bold text-gray-900">{{ $load->qty }} {{ __($load->unit) }} — {{ __($load->kind) }}</span></div>
                                <div class="grid grid-cols-2 gap-1 text-xs text-gray-500"><div><span class="text-green-500">📍</span> {{ $load->pickup?->governorate }}, {{ $load->pickup?->delegation }}</div><div><span class="text-red-500">🚩</span> {{ $load->dropoffAddress?->governorate }}, {{ $load->dropoffAddress?->delegation }}</div></div>
                            </div>
                            <div class="flex flex-row sm:flex-col gap-2">
                                @if($load->status === \App\Models\Load::ST_MATCHED)
                                <button @click="acceptLoad({{ $load->id }})" class="px-4 py-1.5 bg-blue-600 text-white rounded-xl text-xs font-bold hover:bg-blue-700 transition">✅ {{ __('Accept') }}</button>
                                <button @click="rejectLoad({{ $load->id }})" class="px-4 py-1.5 bg-red-50 text-red-600 border border-red-200 rounded-xl text-xs font-bold hover:bg-red-100 transition">❌ {{ __('Reject') }}</button>
                                @else
                                @php $sc=['new'=>'bg-gray-100 text-gray-700','matched'=>'bg-blue-100 text-blue-700','in_transit'=>'bg-amber-100 text-amber-700','delivered'=>'bg-green-100 text-green-700','settled'=>'bg-emerald-100 text-emerald-700']; @endphp
                                <span class="px-3 py-1.5 rounded-xl text-xs font-bold text-center {{ $sc[$load->status]??'bg-gray-100' }}">{{ __(ucfirst($load->status)) }}</span>
                                <a href="{{ route('messages.show', $load->owner_id) }}" class="px-3 py-1.5 bg-gray-900 text-white rounded-xl text-xs font-bold hover:bg-gray-800 transition text-center">💬</a>
                                @if($load->status==='in_transit')@php $at=$load->activeTrip();@endphp@if($at)
                                <div x-data="{quickPin:''}" class="mt-1 p-2.5 bg-amber-50 rounded-xl border border-amber-200 flex gap-2">
                                    <input type="text" x-model="quickPin" placeholder="PIN" class="flex-1 px-2 py-1 border border-amber-200 rounded-lg text-center font-mono text-xs focus:outline-none focus:border-amber-400 min-w-0">
                                    <button @click="finalizeTrip({{ $at->id }},quickPin)" :disabled="!quickPin||saving" class="px-2.5 py-1 bg-green-600 text-white rounded-lg text-xs font-bold disabled:opacity-50">🏁</button>
                                </div>
                                @endif@endif@endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-400"><span class="text-4xl block mb-2">🚚</span><p class="text-sm font-medium text-gray-600">{{ __('No assigned tasks yet') }}</p></div>
                    @endforelse
                    @if($assignedLoads->count() > 0)<div class="pt-2">{{ $assignedLoads->links() }}</div>@endif
                </div>
            </div>
            @endif

            <!-- My Products Grid -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 bg-gradient-to-r from-[#6A8F3B] to-[#5a7a2f]">
                    <h2 class="font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        {{ __('My Products') }}
                    </h2>
                    @if(Auth::user()->role !== 'carrier')
                    <a href="{{ route('listings.create') }}" class="bg-white text-[#6A8F3B] px-4 py-1.5 rounded-xl text-xs font-bold hover:shadow-md transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>{{ __('Add New') }}
                    </a>
                    @endif
                </div>
                <div class="p-4">
                    @if($listings->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($listings as $listing)
                        @php
                            $productImage = null;
                            if($listing->product && $listing->product->media && is_array($listing->product->media) && count($listing->product->media) > 0) { $productImage = $listing->product->media[0]; }
                            elseif($listing->media && is_array($listing->media) && count($listing->media) > 0) { $productImage = $listing->media[0]; }
                        @endphp
                        <div class="group border border-gray-100 rounded-2xl overflow-hidden hover:border-[#6A8F3B]/50 hover:shadow-xl transition-all duration-300 flex flex-col">
                            <div class="relative h-40 bg-gradient-to-br from-[#6A8F3B] to-[#C8A356] overflow-hidden flex-shrink-0">
                                @if($productImage)
                                <img src="{{ Storage::url($productImage) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                                @else
                                <div class="w-full h-full flex items-center justify-center"><span class="text-5xl">{{ $listing->product?->type === 'oil' ? '🫗' : '🫒' }}</span></div>
                                @endif
                                <div class="absolute top-2 right-2">
                                    @if($listing->status==='active')<span class="bg-green-500 text-white px-2 py-0.5 rounded-full text-xs font-bold shadow">✓ {{ __('Active') }}</span>
                                    @elseif($listing->status==='pending')<span class="bg-amber-500 text-white px-2 py-0.5 rounded-full text-xs font-bold shadow">⏳ {{ __('Pending') }}</span>
                                    @elseif($listing->status==='sold')<span class="bg-gray-600 text-white px-2 py-0.5 rounded-full text-xs font-bold shadow">✓ {{ __('Sold') }}</span>
                                    @else<span class="bg-red-500 text-white px-2 py-0.5 rounded-full text-xs font-bold shadow">✕ {{ __('Inactive') }}</span>@endif
                                </div>
                                @if($listing->product)<div class="absolute bottom-2 left-2"><span class="bg-white/90 backdrop-blur text-[#6A8F3B] px-2 py-0.5 rounded-lg text-xs font-bold">{{ $listing->product->type==='oil' ? '🫗 '.__('Oil') : '🫒 '.__('Olives') }}</span></div>@endif
                            </div>
                            <div class="p-4 flex flex-col flex-1">
                                <h3 class="font-bold text-gray-900 group-hover:text-[#6A8F3B] transition text-sm mb-1 truncate">{{ $listing->product?->variety ?? __('Product') }}@if($listing->product?->quality)<span class="text-xs text-gray-400 font-normal"> — {{ $listing->product->quality }}</span>@endif</h3>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[#6A8F3B] font-bold">{{ $listing->price ?? '-' }} <span class="text-xs text-gray-400">{{ $listing->currency ?? 'TND' }}</span></span>
                                    <span class="text-gray-400 text-xs">{{ $listing->product?->quantity ?? '-' }} {{ __('units') }}</span>
                                </div>
                                <p class="text-xs text-gray-400 mb-3">{{ $listing->created_at->diffForHumans() }}</p>
                                @if(Auth::user()->addresses->first())
                                @php $address = Auth::user()->addresses->first(); @endphp
                                <p class="text-xs text-gray-400 mb-3">📍 {{ $address->governorate ?? '' }}@if($address->delegation), {{ $address->delegation }}@endif</p>
                                @endif
                                <div class="flex gap-2 mt-auto">
                                    <a href="{{ url('/listings/'.$listing->id) }}" class="flex-1 text-center bg-[#6A8F3B] text-white px-3 py-2 rounded-xl hover:bg-[#5a7a2f] transition font-bold text-xs">👁 {{ __('View') }}</a>
                                    <a href="{{ url('/listings/'.$listing->id.'/edit') }}" class="flex-1 text-center bg-blue-50 text-blue-600 border border-blue-100 px-3 py-2 rounded-xl hover:bg-blue-100 transition font-bold text-xs">✏️ {{ __('Edit') }}</a>
                                    <form action="{{ url('/listings/'.$listing->id) }}" method="POST" onsubmit="return confirm('{{ __('Delete this listing?') }}')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="px-3 py-2 bg-red-50 text-red-600 border border-red-100 rounded-xl hover:bg-red-100 transition font-bold text-xs">🗑</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-4">{{ $listings->links() }}</div>
                    @else
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                        <h3 class="text-lg font-bold text-gray-500 mb-2">{{ __('No listings yet') }}</h3>
                        <p class="text-gray-400 text-sm mb-5">{{ __('Start by adding your first product!') }}</p>
                        @if(Auth::user()->role !== 'carrier')
                        <a href="{{ route('listings.create') }}" class="inline-flex items-center gap-2 bg-[#6A8F3B] text-white px-6 py-3 rounded-xl font-bold hover:shadow-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>{{ __('Add New Product') }}
                        </a>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

        </div>{{-- /max-w container --}}
    </main>

    <!-- RIGHT SIDEBAR -->
    <aside class="hidden xl:flex w-72 flex-shrink-0 flex-col gap-4 p-4 sticky top-[72px] h-[calc(100vh-72px)] overflow-y-auto">

        <!-- Stats -->
        <div class="bg-white rounded-2xl shadow-lg p-4">
            <h3 class="text-xs font-bold text-gray-400 uppercase mb-3">{{ __('Stats') }}</h3>
            <div class="space-y-2">
                @if(Auth::user()->role === 'carrier')
                <div class="flex items-center justify-between bg-blue-50 rounded-xl px-3 py-2.5"><span class="text-xs font-bold text-blue-700">🚚 {{ __('Tasks') }}</span><span class="text-xl font-black text-blue-700">{{ $assignedLoads->total() }}</span></div>
                <div class="flex items-center justify-between bg-amber-50 rounded-xl px-3 py-2.5"><span class="text-xs font-bold text-amber-700">⚡ {{ __('In Transit') }}</span><span class="text-xl font-black text-amber-700">{{ Auth::user()->assignedLoads()->where('status','in_transit')->count() }}</span></div>
                <div class="flex items-center justify-between bg-green-50 rounded-xl px-3 py-2.5"><span class="text-xs font-bold text-green-700">✓ {{ __('Delivered') }}</span><span class="text-xl font-black text-green-700">{{ Auth::user()->assignedLoads()->whereIn('status',['delivered','settled'])->count() }}</span></div>
                @else
                <div class="flex items-center justify-between bg-blue-50 rounded-xl px-3 py-2.5"><span class="text-xs font-bold text-blue-700">📋 {{ __('Total') }}</span><span class="text-xl font-black text-blue-700">{{ $listings->total() }}</span></div>
                <div class="flex items-center justify-between bg-green-50 rounded-xl px-3 py-2.5"><span class="text-xs font-bold text-green-700">✓ {{ __('Active') }}</span><span class="text-xl font-black text-green-700">{{ $activeListings }}</span></div>
                <div class="flex items-center justify-between bg-amber-50 rounded-xl px-3 py-2.5"><span class="text-xs font-bold text-amber-700">⏳ {{ __('Pending') }}</span><span class="text-xl font-black text-amber-700">{{ $pendingListings }}</span></div>
                @endif
                <div class="flex items-center justify-between bg-purple-50 rounded-xl px-3 py-2.5"><span class="text-xs font-bold text-purple-700">👤 {{ __('Profile') }}</span><span class="text-xl font-black text-purple-700">{{ $profileCompletion }}%</span></div>
            </div>
        </div>

        <!-- Messages -->
        <div class="bg-white rounded-2xl shadow-lg p-4" x-data="{unreadCount:0,init(){this.fetchUnread();setInterval(()=>this.fetchUnread(),30000);},async fetchUnread(){try{const r=await fetch('/messages/unread-count');const d=await r.json();this.unreadCount=d.count;}catch(e){}}}">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-xs font-bold text-gray-400 uppercase">{{ __('Messages') }}</h3>
                <template x-if="unreadCount > 0"><span class="px-2 py-0.5 bg-red-500 text-white text-xs font-bold rounded-full animate-pulse" x-text="unreadCount > 99 ? '99+' : unreadCount"></span></template>
            </div>
            <a href="{{ route('messages.inbox') }}" class="relative w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-xl text-sm font-bold hover:shadow-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                {{ __('Open Inbox') }}
                <template x-if="unreadCount > 0"><span class="absolute -top-1.5 -left-1.5 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center" x-text="unreadCount > 9 ? '9+' : unreadCount"></span></template>
            </a>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-2xl shadow-lg p-4">
            <h3 class="text-xs font-bold text-gray-400 uppercase mb-3">{{ __('Quick Actions') }}</h3>
            <div class="space-y-1.5">
                @if(Auth::user()->role !== 'carrier')
                <a href="{{ route('listings.create') }}" class="flex items-center gap-3 px-3 py-2.5 bg-[#6A8F3B]/10 text-[#6A8F3B] rounded-xl hover:bg-[#6A8F3B]/20 transition font-bold text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>{{ __('Add Product') }}
                </a>
                @endif
                <a href="{{ route('home') }}" class="flex items-center gap-3 px-3 py-2.5 bg-gray-50 text-gray-700 rounded-xl hover:bg-gray-100 transition font-bold text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>{{ __('Marketplace') }}
                </a>
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2.5 bg-gray-50 text-gray-700 rounded-xl hover:bg-gray-100 transition font-bold text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>{{ __('Settings') }}
                </a>
            </div>
        </div>

        <!-- Notifications -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="flex items-center gap-2 px-4 py-3 bg-gray-900">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <h3 class="text-sm font-bold text-white">{{ __('Activity') }}</h3>
            </div>
            <div class="p-4">
                @if(Auth::user()->notifications->count() > 0)
                <div class="space-y-3">
                    @foreach(Auth::user()->notifications()->latest()->limit(4)->get() as $notification)
                    <div class="flex gap-3 pb-3 border-b border-gray-50 last:border-0 last:pb-0">
                        <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center flex-shrink-0 text-sm">🔔</div>
                        <div class="min-w-0"><p class="text-xs text-gray-900 leading-relaxed">{{ $notification->data['body'] ?? '' }}</p><p class="text-[10px] text-gray-400 mt-0.5">{{ $notification->created_at->diffForHumans() }}</p></div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-center text-gray-400 text-xs py-4">{{ __('No recent activity') }}</p>
                @endif
            </div>
        </div>

        <!-- Tip -->
        <div class="bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-200 rounded-2xl p-4">
            <div class="flex items-start gap-3">
                <div class="bg-amber-500 rounded-full p-1.5 flex-shrink-0"><svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg></div>
                <div><h4 class="font-bold text-gray-900 text-xs mb-1">💡 {{ __('Tip') }}</h4><p class="text-xs text-gray-600 leading-relaxed">{{ __('Add clear photos and detailed descriptions to increase sales!') }}</p></div>
            </div>
        </div>

    </aside>

    </div>{{-- /flex layout --}}


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

    <!-- Farm Location Edit Modal -->
    <div x-show="editModal === 'farm_location'" x-cloak
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        class="fixed inset-0 z-[150] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div @click.away="closeEdit()" class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">📍 {{ __('Edit Farm Location') }}</h3>
                    <button @click="closeEdit()" class="text-white/80 hover:text-white"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                </div>
            </div>
            <div class="p-6">
                <div x-show="errorMessage" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm" x-text="errorMessage"></div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Farm Location') }}</label>
                <input type="text" x-model="formData.farm_location" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-500/20 outline-none transition" placeholder="{{ __('e.g., Sfax, Tunisia') }}">
                <div class="flex gap-3 mt-6">
                    <button @click="closeEdit()" class="flex-1 px-4 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition">{{ __('Cancel') }}</button>
                    <button @click="saveField('farm_location')" :disabled="saving" class="flex-1 px-4 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold rounded-xl hover:shadow-lg transition flex items-center justify-center gap-2">
                        <svg x-show="saving" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span x-text="saving ? '{{ __('Saving...') }}' : '{{ __('Save') }}'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Olive Type Edit Modal -->
    <div x-show="editModal === 'olive_type'" x-cloak
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        class="fixed inset-0 z-[150] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div @click.away="closeEdit()" class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">🫒 {{ __('Edit Olive Type') }}</h3>
                    <button @click="closeEdit()" class="text-white/80 hover:text-white"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                </div>
            </div>
            <div class="p-6">
                <div x-show="errorMessage" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm" x-text="errorMessage"></div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Olive Type') }}</label>
                <input type="text" x-model="formData.olive_type" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-500/20 outline-none transition" placeholder="{{ __('e.g., Chemlali') }}">
                <div class="flex gap-3 mt-6">
                    <button @click="closeEdit()" class="flex-1 px-4 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition">{{ __('Cancel') }}</button>
                    <button @click="saveField('olive_type')" :disabled="saving" class="flex-1 px-4 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold rounded-xl hover:shadow-lg transition flex items-center justify-center gap-2">
                        <svg x-show="saving" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span x-text="saving ? '{{ __('Saving...') }}' : '{{ __('Save') }}'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tree Number Edit Modal -->
    <div x-show="editModal === 'tree_number'" x-cloak
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        class="fixed inset-0 z-[150] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div @click.away="closeEdit()" class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">🌳 {{ __('Edit Tree Count') }}</h3>
                    <button @click="closeEdit()" class="text-white/80 hover:text-white"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                </div>
            </div>
            <div class="p-6">
                <div x-show="errorMessage" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm" x-text="errorMessage"></div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Number of Trees') }}</label>
                <input type="number" x-model="formData.tree_number" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-500/20 outline-none transition" placeholder="{{ __('e.g., 500') }}">
                <div class="flex gap-3 mt-6">
                    <button @click="closeEdit()" class="flex-1 px-4 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition">{{ __('Cancel') }}</button>
                    <button @click="saveField('tree_number')" :disabled="saving" class="flex-1 px-4 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold rounded-xl hover:shadow-lg transition flex items-center justify-center gap-2">
                        <svg x-show="saving" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span x-text="saving ? '{{ __('Saving...') }}' : '{{ __('Save') }}'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Truck Capacity Edit Modal -->
    <div x-show="editModal === 'camion_capacity'" x-cloak
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        class="fixed inset-0 z-[150] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div @click.away="closeEdit()" class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">⚖️ {{ __('Edit Truck Capacity') }}</h3>
                    <button @click="closeEdit()" class="text-white/80 hover:text-white"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                </div>
            </div>
            <div class="p-6">
                <div x-show="errorMessage" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm" x-text="errorMessage"></div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Capacity (Kg)') }}</label>
                <input type="number" x-model="formData.camion_capacity" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition" placeholder="{{ __('e.g., 3000') }}">
                <div class="flex gap-3 mt-6">
                    <button @click="closeEdit()" class="flex-1 px-4 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition">{{ __('Cancel') }}</button>
                    <button @click="saveField('camion_capacity')" :disabled="saving" class="flex-1 px-4 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold rounded-xl hover:shadow-lg transition flex items-center justify-center gap-2">
                        <svg x-show="saving" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span x-text="saving ? '{{ __('Saving...') }}' : '{{ __('Save') }}'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    </main>
</div>
@endsection
