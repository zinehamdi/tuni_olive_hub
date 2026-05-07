@extends('layouts.app')

@section('title', __('My Active Trip'))

@section('content')
<div dir="rtl" class="min-h-screen bg-transparent" x-data="tripApp">
    <style>
        .glass-morphism {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .animate-pulse-slow {
            animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        #tracking-map {
            height: 300px;
            width: 100%;
            border-radius: 2rem;
            z-index: 10;
        }
    </style>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <div class="max-w-2xl mx-auto space-y-6 pb-20">
        <!-- Header Section -->
        <div class="text-center py-8">
            <h1 class="text-3xl font-black text-gray-900 mb-2">{{ __('Track My Trip') }}</h1>
            <p class="text-gray-600">{{ __('Real-time transport and delivery management') }}</p>
        </div>

        <!-- Loading State -->
        <div x-show="loading" class="flex flex-col items-center justify-center py-20">
            <div class="w-16 h-16 border-4 border-[#6A8F3B] border-t-transparent rounded-full animate-spin"></div>
            <p class="mt-4 text-gray-600 font-medium animate-pulse">{{ __('Syncing with fleet...') }}</p>
        </div>

        <!-- Active Trip Section -->
        <template x-if="!loading && activeTrip">
            <div class="space-y-6">
                <!-- Live Map Card -->
                <div class="glass-morphism rounded-[2.5rem] p-4 shadow-2xl relative overflow-hidden">
                    <div id="tracking-map" x-init="initMap()"></div>
                </div>

                <!-- GPS Simulator (Dev/Test Tool) -->
                <div class="glass-morphism rounded-[2.5rem] p-5 shadow-xl border-2 border-dashed border-orange-200" x-data="{ simOpen: false, simLat: '', simLng: '', simSending: false }">
                    <button @click="simOpen = !simOpen" class="w-full flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-orange-100 flex items-center justify-center text-orange-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2v-4M9 21H5a2 2 0 01-2-2v-4m0 0h18"/></svg>
                            </div>
                            <div class="text-left">
                                <p class="text-sm font-black text-orange-700">🧪 {{ __('GPS Simulator') }}</p>
                                <p class="text-[10px] text-orange-500 font-medium">{{ __('Test tracking without moving') }}</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-orange-400 transition-transform duration-200" :class="simOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div x-show="simOpen" x-collapse x-cloak class="mt-5 pt-5 border-t border-orange-100 space-y-4">
                        <p class="text-xs text-gray-500 font-medium">{{ __('Enter coordinates below and click send to simulate GPS movement on the map and server.') }}</p>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-wider mb-1">Latitude</label>
                                <input type="number" step="0.0001" x-model="simLat" placeholder="36.8189" class="w-full px-4 py-3 bg-white border-2 border-orange-100 rounded-2xl text-sm font-mono focus:border-orange-400 focus:ring-0 transition-all">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-wider mb-1">Longitude</label>
                                <input type="number" step="0.0001" x-model="simLng" placeholder="10.1658" class="w-full px-4 py-3 bg-white border-2 border-orange-100 rounded-2xl text-sm font-mono focus:border-orange-400 focus:ring-0 transition-all">
                            </div>
                        </div>
                        <!-- Quick presets for Tunisia -->
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">{{ __('Quick Presets') }}</p>
                            <div class="flex flex-wrap gap-2">
                                <button @click="simLat='36.8189'; simLng='10.1658'" class="px-3 py-1.5 bg-orange-50 text-orange-700 rounded-xl text-xs font-bold hover:bg-orange-100 transition-all">🏙️ Tunis</button>
                                <button @click="simLat='36.7300'; simLng='10.0700'" class="px-3 py-1.5 bg-orange-50 text-orange-700 rounded-xl text-xs font-bold hover:bg-orange-100 transition-all">🫒 Manouba</button>
                                <button @click="simLat='35.8245'; simLng='10.6346'" class="px-3 py-1.5 bg-orange-50 text-orange-700 rounded-xl text-xs font-bold hover:bg-orange-100 transition-all">🏭 Sousse</button>
                                <button @click="simLat='33.8870'; simLng='9.5563'"  class="px-3 py-1.5 bg-orange-50 text-orange-700 rounded-xl text-xs font-bold hover:bg-orange-100 transition-all">🌴 Sfax</button>
                            </div>
                        </div>
                        <button @click="async () => { simSending = true; await simulateLocation(simLat, simLng); simSending = false; }" :disabled="simSending || !simLat || !simLng" class="w-full py-4 bg-gradient-to-r from-orange-500 to-amber-500 text-white rounded-2xl font-black text-sm shadow-lg shadow-orange-200 hover:scale-[1.01] active:scale-[0.99] transition-all disabled:opacity-50">
                            <span x-show="!simSending" class="flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                {{ __('SEND LOCATION') }}
                            </span>
                            <span x-show="simSending" class="flex items-center justify-center gap-2">
                                <svg class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                {{ __('Sending...') }}
                            </span>
                        </button>
                    </div>
                </div>

                <!-- Trip Status Card -->
                <div class="glass-morphism rounded-[2.5rem] p-8 shadow-2xl relative overflow-hidden">
                    <div class="absolute -top-10 -right-10 w-40 h-40 bg-[#6A8F3B]/10 rounded-full blur-3xl"></div>
                    
                    <div class="flex justify-between items-center mb-8">
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-[#6A8F3B] block mb-1">{{ __('Trip Identifier') }}</span>
                            <h2 class="text-2xl font-black text-gray-900" x-text="activeTrip.sr_code"></h2>
                        </div>
                        <!-- Full PIN display for carrier -->
                        <div class="text-left">
                            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 block mb-1">{{ __('Security PIN') }}</span>
                            <button @click="copyPin()" class="group relative bg-amber-500 hover:bg-amber-600 text-white px-5 py-2.5 rounded-xl font-mono font-black text-xl shadow-xl shadow-amber-200 transition-all active:scale-95 flex items-center gap-2" title="{{ __('Tap to copy') }}">
                                <span x-text="activeTrip.pin_full || activeTrip.pin_mask"></span>
                                <svg class="w-4 h-4 opacity-70 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                <!-- Copy flash -->
                                <span x-show="pinCopied" x-transition:enter="transition ease-out duration-200" x-transition:leave="transition ease-in duration-150" class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-[10px] font-bold px-2 py-1 rounded-lg whitespace-nowrap">{{ __('Copied!') }}</span>
                            </button>
                            <p class="text-[9px] text-amber-600 font-bold mt-1 text-center">{{ __('Give this PIN to the receiver') }}</p>
                        </div>
                    </div>

                    <!-- Journey Timeline -->
                    <div class="space-y-8 relative before:absolute before:right-3 before:top-4 before:bottom-4 before:w-0.5 before:bg-gradient-to-b before:from-[#6A8F3B] before:to-transparent">
                        <template x-for="load in activeTrip.loads" :key="load.id">
                            <div class="relative pr-10">
                                <div class="absolute right-0 top-1 w-6 h-6 rounded-full bg-white border-4 border-[#6A8F3B] shadow-lg z-10 flex items-center justify-center">
                                    <div class="w-1.5 h-1.5 bg-[#6A8F3B] rounded-full animate-ping"></div>
                                </div>
                                <div class="bg-white/40 rounded-3xl p-6 border border-white/50 shadow-sm hover:shadow-md transition-all duration-300">
                                    <div class="flex justify-between items-center mb-4">
                                        <span class="px-3 py-1 bg-[#6A8F3B]/10 text-[#6A8F3B] rounded-full text-xs font-black" x-text="'L' + load.id"></span>
                                        <span class="text-lg font-black text-gray-800" x-text="load.qty + ' ' + (unitMap[load.unit] || load.unit)"></span>
                                    </div>
                                    <div class="space-y-4">
                                        <div class="flex gap-4">
                                            <div class="w-10 h-10 rounded-2xl bg-emerald-50 flex items-center justify-center flex-shrink-0 text-xl shadow-inner">🟢</div>
                                            <div>
                                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ __('Pickup Point') }}</p>
                                                <p class="text-sm text-gray-700 font-semibold leading-relaxed" x-text="load.pickup.address"></p>
                                            </div>
                                        </div>
                                        <div class="flex gap-4">
                                            <div class="w-10 h-10 rounded-2xl bg-red-50 flex items-center justify-center flex-shrink-0 text-xl shadow-inner">🚩</div>
                                            <div>
                                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ __('Destination') }}</p>
                                                <p class="text-sm text-gray-700 font-semibold leading-relaxed" x-text="load.dropoff.address"></p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Navigation Shortcuts -->
                                    <div class="mt-6 grid grid-cols-2 gap-3">
                                        <a :href="'https://www.google.com/maps/dir/?api=1&destination=' + load.pickup.lat + ',' + load.pickup.lng" target="_blank" class="flex items-center justify-center gap-2 px-4 py-3 bg-emerald-50 text-emerald-700 rounded-2xl text-xs font-black border border-emerald-100 hover:bg-emerald-100 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /></svg>
                                            {{ __('PICKUP') }}
                                        </a>
                                        <a :href="'https://www.google.com/maps/dir/?api=1&destination=' + load.dropoff.lat + ',' + load.dropoff.lng" target="_blank" class="flex items-center justify-center gap-2 px-4 py-3 bg-red-50 text-red-700 rounded-2xl text-xs font-black border border-red-100 hover:bg-red-100 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /></svg>
                                            {{ __('DROPOFF') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Proof of Delivery Section -->
                <div class="glass-morphism rounded-[2.5rem] p-8 shadow-2xl">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-100 flex items-center justify-center text-emerald-600 shadow-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        </div>
                        <h3 class="text-xl font-black text-gray-900">{{ __('Proof of Delivery') }}</h3>
                    </div>

                    <form @submit.prevent="uploadPod" class="space-y-6">
                        <div class="relative group">
                            <input type="file" name="photo" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" @change="onFileChange">
                            <div class="border-4 border-dashed border-gray-100 rounded-[2rem] p-10 text-center group-hover:border-[#6A8F3B] transition-all bg-white/30 hover:bg-white/50">
                                <div class="w-20 h-20 mx-auto mb-4 rounded-3xl bg-[#6A8F3B]/5 flex items-center justify-center text-[#6A8F3B] group-hover:scale-110 transition-transform shadow-inner">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                </div>
                                <p class="text-sm text-gray-600 font-black tracking-wide" x-text="fileName || '{{ __('CAPTURE OR SELECT IMAGE') }}'"></p>
                            </div>
                        </div>
                        <button type="submit" :disabled="uploading || !fileName" class="w-full py-5 bg-gradient-to-r from-[#6A8F3B] to-[#5a7a2f] text-white rounded-[2rem] font-black text-lg shadow-2xl shadow-[#6A8F3B]/30 hover:scale-[1.02] active:scale-[0.98] transition-all disabled:opacity-50 disabled:grayscale">
                            <span x-show="!uploading">{{ __('CONFIRM DELIVERY') }}</span>
                            <span x-show="uploading" class="flex items-center justify-center gap-3">
                                <svg class="w-6 h-6 animate-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                {{ __('UPLOADING...') }}
                            </span>
                        </button>
                    </form>
                    <div x-show="uploadMsg" x-transition class="mt-6 p-4 rounded-2xl text-center font-bold" :class="uploadSuccess ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600'">
                        <span x-text="uploadMsg"></span>
                    </div>

                    <!-- Finalize Section -->
                    <div class="mt-8 pt-8 border-t border-gray-100">
                        <div class="mb-6">
                            <label class="block text-sm font-black text-gray-700 mb-3 tracking-wider uppercase">{{ __('RECEIVER SECURITY PIN') }}</label>
                            <p class="text-xs text-gray-400 mb-3">{{ __('Ask the receiver to show you their dashboard. The PIN is auto-filled below from your trip.') }}</p>
                            <input type="text" x-model="securityPin" placeholder="e.g. 8472" class="w-full px-6 py-4 bg-gray-50 border-2 border-amber-200 rounded-2xl text-center text-2xl font-black tracking-[1em] focus:border-[#6A8F3B] focus:ring-0 transition-all">
                        </div>
                        <button @click="finalizeTrip" :disabled="completing || !securityPin" class="w-full py-5 bg-black text-white rounded-[2rem] font-black text-lg shadow-2xl hover:bg-gray-800 transition-all disabled:opacity-50">
                            <span x-show="!completing">{{ __('FINALIZE & COMPLETE TRIP') }}</span>
                            <span x-show="completing" class="flex items-center justify-center gap-3">
                                <svg class="w-6 h-6 animate-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                {{ __('FINALIZING...') }}
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <!-- No Active Trip / Pending Requests -->
        <template x-if="!loading && !activeTrip">
            <div class="space-y-8">
                <div class="flex items-center justify-between px-6">
                    <h2 class="text-2xl font-black text-gray-900">{{ __('Pending Requests') }}</h2>
                    <div class="w-10 h-10 rounded-2xl bg-blue-100 flex items-center justify-center text-blue-600 font-black" x-text="pendingLoads.length"></div>
                </div>

                <template x-if="pendingLoads.length === 0">
                    <div class="text-center py-24 glass-morphism rounded-[3rem] shadow-xl">
                        <div class="text-6xl mb-6 animate-bounce">🚚</div>
                        <h3 class="text-2xl font-black text-gray-900 mb-2">{{ __('Fleet is Idle') }}</h3>
                        <p class="text-gray-500 font-medium px-10">{{ __('All current transport assignments have been processed.') }}</p>
                        <button @click="fetchData" class="mt-8 px-8 py-3 bg-gray-900 text-white rounded-full font-bold hover:bg-black transition-all flex items-center gap-2 mx-auto">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                            {{ __('Refresh Feed') }}
                        </button>
                    </div>
                </template>

                <div class="grid grid-cols-1 gap-6">
                    <template x-for="load in pendingLoads" :key="load.id">
                        <div class="glass-morphism rounded-[2.5rem] p-8 shadow-xl border-2 border-transparent hover:border-[#6A8F3B]/30 transition-all duration-500 group">
                            <div class="flex justify-between items-start mb-6">
                                <div>
                                    <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-[10px] font-black tracking-widest mb-2 inline-block uppercase" x-text="'REQ-' + load.id"></span>
                                    <h4 class="text-2xl font-black text-gray-900" x-text="load.qty + ' ' + (unitMap[load.unit] || load.unit) + ' ' + (unitMap[load.kind] || load.kind)"></h4>
                                    <p class="text-gray-500 font-bold mt-1" x-text="'👤 ' + load.owner_name"></p>
                                </div>
                                <div class="w-16 h-16 rounded-[1.5rem] bg-gradient-to-br from-blue-50 to-indigo-50 flex items-center justify-center text-3xl shadow-inner group-hover:rotate-12 transition-transform">📦</div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8 bg-gray-50/30 rounded-[2rem] p-6">
                                <div class="flex items-center gap-4">
                                    <span class="w-10 h-10 rounded-2xl bg-white flex items-center justify-center shadow-sm">📍</span>
                                    <div>
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ __('Pickup') }}</p>
                                        <p class="text-sm text-gray-700 font-black" x-text="load.pickup"></p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <span class="w-10 h-10 rounded-2xl bg-white flex items-center justify-center shadow-sm">🚩</span>
                                    <div>
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ __('Dropoff') }}</p>
                                        <p class="text-sm text-gray-700 font-black" x-text="load.dropoff"></p>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <button @click="acceptLoad(load.id)" class="py-5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-3xl font-black text-sm shadow-xl shadow-blue-200 hover:scale-[1.02] active:scale-[0.98] transition-all">
                                    {{ __('ACCEPT') }}
                                </button>
                                <button @click="rejectLoad(load.id)" class="py-5 bg-white text-red-600 border-2 border-red-50 rounded-3xl font-black text-sm hover:bg-red-50 transition-all">
                                    {{ __('REJECT') }}
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </template>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('tripApp', () => ({
            api: '{{ url('/api/v1') }}',
            loading: true,
            activeTrip: null,
            pendingLoads: [],
            fileName: '',
            securityPin: '',
            uploading: false,
            completing: false,
            uploadMsg: '',
            uploadSuccess: false,
            pinCopied: false,
            map: null,
            transporterMarker: null,
            markers: [],
            unitMap: {
                'kg': '{{ __('kg') }}',
                'kilogram': '{{ __('Kilogram') }}',
                'liter': '{{ __('Liter') }}',
                'ton': '{{ __('Ton') }}',
                'olive': '{{ __('Olives') }}',
                'oil': '{{ __('Olive Oil') }}'
            },

            async init() {
                await this.fetchData();
                // Refresh trip data (ETA, status) every 30s
                setInterval(() => this.fetchData(), 30000);
                this.startTracking();
            },

            startTracking() {
                if ("geolocation" in navigator) {
                    this.lastUpdate = 0;
                    this.watchId = navigator.geolocation.watchPosition(async (pos) => {
                        if (!this.activeTrip) return;
                        
                        const { latitude, longitude, accuracy } = pos.coords;
                        
                        // Ignore low accuracy readings (>100m) to prevent jumps
                        if (accuracy > 100) return;

                        // Throttle server updates to once every 10 seconds to save battery
                        const now = Date.now();
                        if (now - this.lastUpdate > 10000) {
                            this.lastUpdate = now;
                            this.sendLocation(latitude, longitude);
                        }
                        
                        // Update local marker immediately for smoothness
                        this.updateTransporterMarker(latitude, longitude);
                        
                    }, (err) => console.error('Geo error', err), { 
                        enableHighAccuracy: true,
                        maximumAge: 3000,
                        timeout: 20000
                    });
                }
            },

            async sendLocation(lat, lng) {
                try {
                    await fetch(`${this.api}/mobile/trips/${this.activeTrip.id}/location`, {
                        method: 'POST',
                        headers: { 
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ lat, lng })
                    });
                } catch (e) { console.error('Tracking sync error', e); }
            },

            async simulateLocation(lat, lng) {
                if (!this.activeTrip || !lat || !lng) return;
                // Get simSending from the nested x-data — we update the map directly here
                const latNum = parseFloat(lat);
                const lngNum = parseFloat(lng);
                this.updateTransporterMarker(latNum, lngNum);
                this.map?.setView([latNum, lngNum], 13);
                await this.sendLocation(latNum, lngNum);
            },

            copyPin() {
                const pin = this.activeTrip?.pin_full || this.activeTrip?.pin_mask;
                if (!pin) return;
                navigator.clipboard?.writeText(pin).then(() => {
                    this.pinCopied = true;
                    setTimeout(() => { this.pinCopied = false; }, 2000);
                }).catch(() => {
                    // fallback for non-HTTPS
                    const el = document.createElement('input');
                    el.value = pin;
                    document.body.appendChild(el);
                    el.select();
                    document.execCommand('copy');
                    document.body.removeChild(el);
                    this.pinCopied = true;
                    setTimeout(() => { this.pinCopied = false; }, 2000);
                });
            },

            updateTransporterMarker(lat, lng) {
                if (!this.map) return;
                const pos = [lat, lng];
                if (!this.transporterMarker) {
                    this.transporterMarker = L.marker(pos, {
                        icon: L.divIcon({
                            html: '<div class="w-10 h-10 bg-blue-600 rounded-full border-4 border-white shadow-xl flex items-center justify-center text-xl animate-bounce">🚚</div>',
                            className: '',
                            iconSize: [40, 40],
                            iconAnchor: [20, 20]
                        })
                    }).addTo(this.map);
                } else {
                    this.transporterMarker.setLatLng(pos);
                }
            },

            async fetchData() {
                try {
                    const urlParams = new URLSearchParams(window.location.search);
                    const loadId = urlParams.get('id');
                    
                    const tripUrl = loadId 
                        ? `${this.api}/mobile/loads/${loadId}/trip` 
                        : `${this.api}/mobile/trips/active`;

                    const [tripRes, pendingRes] = await Promise.all([
                        fetch(tripUrl),
                        fetch(`${this.api}/mobile/loads/pending`)
                    ]);


                    if (tripRes.ok) {
                        const tripJson = await tripRes.json();
                        this.activeTrip = tripJson.data;
                        // Auto-fill PIN for testing convenience
                        if (this.activeTrip?.pin_full && !this.securityPin) {
                            this.securityPin = this.activeTrip.pin_full;
                        }
                        this.updateMap();
                    }

                    if (pendingRes.ok) {
                        const pendingJson = await pendingRes.json();
                        this.pendingLoads = pendingJson.data;
                    }
                } catch (err) {
                    console.error('Fetch error:', err);
                } finally {
                    this.loading = false;
                }
            },

            initMap() {
                this.$nextTick(() => {
                    if (this.map) return;
                    this.map = L.map('tracking-map').setView([33.8869, 9.5375], 6);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap'
                    }).addTo(this.map);
                });
            },

            updateMap() {
                if (!this.map || !this.activeTrip) return;
                
                // Transporter Marker
                if (this.activeTrip.current_location && this.activeTrip.current_location.lat) {
                    this.updateTransporterMarker(this.activeTrip.current_location.lat, this.activeTrip.current_location.lng);
                }

                // Pickup/Dropoff Markers
                this.markers.forEach(m => this.map.removeLayer(m));
                this.markers = [];
                
                this.activeTrip.loads.forEach(load => {
                    const p = L.marker([load.pickup.lat, load.pickup.lng], {
                        icon: L.divIcon({ html: '<div class="text-2xl">🟢</div>', className: '', iconSize: [30, 30], iconAnchor: [15, 15] })
                    }).addTo(this.map).bindPopup('{{ __('Pickup') }}');
                    const d = L.marker([load.dropoff.lat, load.dropoff.lng], {
                        icon: L.divIcon({ html: '<div class="text-2xl">🚩</div>', className: '', iconSize: [30, 30], iconAnchor: [15, 15] })
                    }).addTo(this.map).bindPopup('{{ __('Destination') }}');
                    this.markers.push(p, d);
                });

                if (this.markers.length > 0) {
                    const group = new L.featureGroup([...this.markers, ...(this.transporterMarker ? [this.transporterMarker] : [])]);
                    this.map.fitBounds(group.getBounds(), { padding: [50, 50] });
                }
            },

            onFileChange(e) {
                this.fileName = e.target.files[0]?.name || '';
            },

            async uploadPod(e) {
                if (!this.activeTrip) return;
                this.uploading = true;
                this.uploadMsg = '';
                
                const fd = new FormData(e.target);
                try {
                    const res = await fetch(`${this.api}/mobile/trips/${this.activeTrip.id}/pod/photo`, {
                        method: 'POST',
                        headers: { 'Accept': 'application/json' },
                        body: fd
                    });
                    const json = await res.json();
                    if (res.ok) {
                        this.uploadSuccess = true;
                        this.uploadMsg = '{{ __('Proof confirmed! Well done.') }}';
                        this.fileName = '';
                        e.target.reset();
                    } else {
                        this.uploadSuccess = false;
                        this.uploadMsg = json.error || '{{ __('Submission failed.') }}';
                    }
                } catch (err) {
                    this.uploadSuccess = false;
                    this.uploadMsg = '{{ __('Connection lost.') }}';
                } finally {
                    this.uploading = false;
                }
            },

            async finalizeTrip() {
                if (!this.securityPin) return;
                this.completing = true;
                this.uploadMsg = '';
                try {
                    // 1. Submit PIN for verification
                    const podRes = await fetch(`${this.api}/trips/${this.activeTrip.id}/pod`, {
                        method: 'POST',
                        headers: { 
                            'Accept': 'application/json', 
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ signed_pin: this.securityPin })
                    });

                    if (!podRes.ok) {
                        const err = await podRes.json();
                        throw new Error(err.message || 'Invalid PIN');
                    }

                    // 2. Mark Trip as Complete
                    const completeRes = await fetch(`${this.api}/trips/${this.activeTrip.id}/complete`, {
                        method: 'POST',
                        headers: { 
                            'Accept': 'application/json', 
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ distance_km: 0 })
                    });

                    if (completeRes.ok) {
                        this.uploadSuccess = true;
                        this.uploadMsg = '{{ __('Trip Completed Successfully!') }}';
                        setTimeout(() => window.location.reload(), 2000);
                    } else {
                        const err = await completeRes.json();
                        throw new Error(err.message || 'Completion failed');
                    }
                } catch (e) {
                    this.uploadSuccess = false;
                    this.uploadMsg = e.message;
                } finally {
                    this.completing = false;
                }
            },

            async acceptLoad(id) {
                if (!confirm('{{ __('Accept this transport task?') }}')) return;
                try {
                    const res = await fetch(`${this.api}/mobile/loads/${id}/accept`, {
                        method: 'POST',
                        headers: { 
                            'Accept': 'application/json', 
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    if (res.ok) {
                        await this.fetchData();
                    } else {
                        alert('{{ __('Action failed.') }}');
                    }
                } catch (err) {
                    alert('{{ __('Error.') }}');
                }
            },

            async rejectLoad(id) {
                if (!confirm('{{ __('Reject this transport task?') }}')) return;
                try {
                    const res = await fetch(`${this.api}/mobile/loads/${id}/reject`, {
                        method: 'POST',
                        headers: { 
                            'Accept': 'application/json', 
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    if (res.ok) {
                        await this.fetchData();
                    } else {
                        alert('{{ __('Action failed.') }}');
                    }
                } catch (err) {
                    alert('{{ __('Error.') }}');
                }
            }
        }));
    });
</script>
@endpush
