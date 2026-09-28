@extends('layouts.app')

@php
    $isRTL = app()->getLocale() === 'ar';
@endphp

@section('title', __('Carrier Verifications') . ' - ZinToop Admin')

@section('content')
<div dir="{{ $isRTL ? 'rtl' : 'ltr' }}" class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-100 py-8 px-4 sm:px-6">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black text-gray-900 flex items-center gap-2">
                    <span>🛡️</span>
                    <span>{{ __('Carrier Verification & Document Review') }}</span>
                </h1>
                <p class="text-sm text-gray-600 mt-1">{{ __('Examine uploaded Carte Grise, Driving Licenses, and National IDs before granting the Verified Carrier badge.') }}</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl transition font-bold text-sm">
                ← {{ __('Back to Dashboard') }}
            </a>
        </div>

        {{-- Success / Error Alerts --}}
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl font-bold text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- Verification List --}}
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
            @if($carriers->count() > 0)
                <div class="divide-y divide-gray-100">
                    @foreach($carriers as $carrier)
                        @php
                            $vData = $carrier->meta_data['carrier_verification'] ?? [];
                            $vStatus = $vData['status'] ?? ($carrier->is_verified ? 'approved' : 'pending');
                        @endphp
                        <div class="p-6 hover:bg-gray-50/80 transition space-y-4">
                            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
                                {{-- Carrier Profile --}}
                                <div class="flex items-center gap-4">
                                    @if($carrier->profile_picture)
                                        <img src="{{ Storage::url($carrier->profile_picture) }}" class="w-16 h-16 rounded-2xl object-cover shadow-sm">
                                    @else
                                        <div class="w-16 h-16 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-2xl shadow-sm">
                                            {{ strtoupper(substr($carrier->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h3 class="text-lg font-bold text-gray-900">{{ $carrier->name }}</h3>
                                            @if($carrier->is_verified)
                                                <span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-800 border border-emerald-200 rounded-full text-xs font-bold">
                                                    ✓ {{ __('Verified Transporter') }} 🛡️
                                                </span>
                                            @endif

                                            @if($vStatus === 'pending')
                                                <span class="px-2.5 py-0.5 bg-amber-100 text-amber-800 border border-amber-200 rounded-full text-xs font-bold animate-pulse">
                                                    ⏳ {{ __('Pending Review') }}
                                                </span>
                                            @elseif($vStatus === 'rejected')
                                                <span class="px-2.5 py-0.5 bg-red-100 text-red-800 border border-red-200 rounded-full text-xs font-bold">
                                                    ❌ {{ __('Rejected') }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1 flex flex-wrap items-center gap-3">
                                            <span>📞 {{ $carrier->phone ?: __('No phone') }}</span>
                                            <span>•</span>
                                            <span>📧 {{ $carrier->email }}</span>
                                            <span>•</span>
                                            <span>⚖️ {{ $carrier->camion_capacity ? $carrier->camion_capacity . ' Kg' : __('No capacity') }}</span>
                                            @if(!empty($vData['submitted_at']))
                                                <span>•</span>
                                                <span>🕒 {{ \Carbon\Carbon::parse($vData['submitted_at'])->diffForHumans() }}</span>
                                            @endif
                                        </div>
                                        @if(!empty($vData['vehicle_plate']))
                                            <div class="text-xs font-mono font-bold text-blue-700 mt-1">
                                                🚘 {{ __('Plate:') }} {{ $vData['vehicle_plate'] }}
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Moderation Actions --}}
                                <div class="flex items-center gap-2 w-full lg:w-auto justify-end flex-wrap">
                                    @if(!$carrier->is_verified || $vStatus !== 'approved')
                                        <form method="POST" action="{{ route('admin.carrier_verifications.approve', ['locale' => app()->getLocale(), 'user' => $carrier->id]) }}">
                                            @csrf
                                            <button type="submit" onclick="return confirm('{{ __('Are you sure you want to approve and verify this carrier?') }}')" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-xs sm:text-sm flex items-center gap-1.5 transition shadow">
                                                <span>✓</span>
                                                <span>{{ __('Approve & Verify') }} 🛡️</span>
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Reject Modal Trigger --}}
                                    <div x-data="{ openReject: false }">
                                        <button @click="openReject = true" class="px-4 py-2.5 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 rounded-xl font-bold text-xs sm:text-sm transition">
                                            <span>❌</span>
                                            <span>{{ __('Reject') }}</span>
                                        </button>

                                        <div x-show="openReject" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
                                            <div class="flex items-center justify-center min-h-screen px-4">
                                                <div @click="openReject = false" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                                                <div class="bg-white rounded-2xl p-6 max-w-md w-full relative z-10 shadow-2xl">
                                                    <h4 class="text-base font-bold text-gray-900 mb-2">{{ __('Reject Carrier Documents') }}</h4>
                                                    <form method="POST" action="{{ route('admin.carrier_verifications.reject', ['locale' => app()->getLocale(), 'user' => $carrier->id]) }}">
                                                        @csrf
                                                        <label class="block text-xs font-bold text-gray-600 mb-1">{{ __('Reason / Instructions for re-upload:') }}</label>
                                                        <textarea name="reason" rows="3" required placeholder="{{ __('e.g. The photo of the carte grise is blurry, please take a clearer shot.') }}" class="w-full text-xs p-3 border border-gray-300 rounded-xl mb-4 focus:ring-0 focus:border-red-400"></textarea>
                                                        <div class="flex gap-2 justify-end">
                                                            <button type="button" @click="openReject = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl font-bold text-xs">{{ __('Cancel') }}</button>
                                                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-xl font-bold text-xs">{{ __('Confirm Rejection') }}</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <a href="{{ route('messages.show', ['locale' => app()->getLocale(), 'user' => $carrier->id]) }}" class="px-3.5 py-2.5 bg-gray-900 text-white rounded-xl font-bold text-xs transition hover:bg-gray-800" title="{{ __('Direct Message') }}">
                                        💬
                                    </a>
                                </div>
                            </div>

                            {{-- Documents Gallery --}}
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                                {{-- Carte Grise --}}
                                <div class="bg-gray-50 border border-gray-200 rounded-2xl p-3 text-center">
                                    <span class="text-xs font-bold text-gray-700 block mb-2">📄 {{ __('Carte Grise (بطاقة رمادية)') }}</span>
                                    @if(!empty($vData['carte_grise']))
                                        <a href="{{ Storage::url($vData['carte_grise']) }}" target="_blank" class="inline-block group relative">
                                            @if(str_ends_with(strtolower($vData['carte_grise']), '.pdf'))
                                                <div class="w-full h-32 bg-red-50 text-red-600 rounded-xl flex items-center justify-center font-bold text-sm border border-red-200 group-hover:scale-105 transition">
                                                    PDF Document ↗
                                                </div>
                                            @else
                                                <img src="{{ Storage::url($vData['carte_grise']) }}" class="w-full h-32 object-cover rounded-xl border border-gray-200 shadow-sm group-hover:scale-105 transition">
                                            @endif
                                            <span class="text-[10px] text-blue-600 font-bold mt-1 inline-block">{{ __('Click to inspect') }} ↗</span>
                                        </a>
                                    @else
                                        <span class="text-xs text-gray-400">{{ __('Not provided') }}</span>
                                    @endif
                                </div>

                                {{-- Permis de Conduire --}}
                                <div class="bg-gray-50 border border-gray-200 rounded-2xl p-3 text-center">
                                    <span class="text-xs font-bold text-gray-700 block mb-2">🪪 {{ __('Driving License (رخصة السياقة)') }}</span>
                                    @if(!empty($vData['permis']))
                                        <a href="{{ Storage::url($vData['permis']) }}" target="_blank" class="inline-block group relative">
                                            @if(str_ends_with(strtolower($vData['permis']), '.pdf'))
                                                <div class="w-full h-32 bg-red-50 text-red-600 rounded-xl flex items-center justify-center font-bold text-sm border border-red-200 group-hover:scale-105 transition">
                                                    PDF Document ↗
                                                </div>
                                            @else
                                                <img src="{{ Storage::url($vData['permis']) }}" class="w-full h-32 object-cover rounded-xl border border-gray-200 shadow-sm group-hover:scale-105 transition">
                                            @endif
                                            <span class="text-[10px] text-blue-600 font-bold mt-1 inline-block">{{ __('Click to inspect') }} ↗</span>
                                        </a>
                                    @else
                                        <span class="text-xs text-gray-400">{{ __('Not provided') }}</span>
                                    @endif
                                </div>

                                {{-- Carte d'Identite CIN --}}
                                <div class="bg-gray-50 border border-gray-200 rounded-2xl p-3 text-center">
                                    <span class="text-xs font-bold text-gray-700 block mb-2">🆔 {{ __('National ID CIN (بطاقة التعريف)') }}</span>
                                    @if(!empty($vData['cin']))
                                        <a href="{{ Storage::url($vData['cin']) }}" target="_blank" class="inline-block group relative">
                                            @if(str_ends_with(strtolower($vData['cin']), '.pdf'))
                                                <div class="w-full h-32 bg-red-50 text-red-600 rounded-xl flex items-center justify-center font-bold text-sm border border-red-200 group-hover:scale-105 transition">
                                                    PDF Document ↗
                                                </div>
                                            @else
                                                <img src="{{ Storage::url($vData['cin']) }}" class="w-full h-32 object-cover rounded-xl border border-gray-200 shadow-sm group-hover:scale-105 transition">
                                            @endif
                                            <span class="text-[10px] text-blue-600 font-bold mt-1 inline-block">{{ __('Click to inspect') }} ↗</span>
                                        </a>
                                    @else
                                        <span class="text-xs text-gray-400">{{ __('Not provided') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="p-4 border-t border-gray-100">
                    {{ $carriers->links() }}
                </div>
            @else
                <div class="p-12 text-center text-gray-400">
                    <span class="text-5xl block mb-3">🛡️</span>
                    <h3 class="text-lg font-bold text-gray-800">{{ __('No verification submissions yet') }}</h3>
                    <p class="text-xs text-gray-500 mt-1">{{ __('When carriers upload their Carte Grise, Permis, and CIN, they will show up here for moderation.') }}</p>
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
