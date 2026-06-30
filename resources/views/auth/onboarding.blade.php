@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto mt-10 p-6 bg-white rounded-lg shadow-md">
    <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">{{ __('Complete Your Profile') }}</h2>
    
    <p class="text-gray-600 mb-8 text-center">
        {{ __('To fully access the marketplace and connect with others, please provide your phone number and role.') }}
    </p>

    <form method="POST" action="{{ route('onboarding.store') }}" class="space-y-6" x-data="{ role: '{{ old('role') }}' }">
        @csrf

        <!-- Phone Number -->
        <div>
            <label for="phone" class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-6 h-6 text-[#6A8F3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
                <span>{{ __('Phone Number') }} <span class="text-red-500">*</span></span>
            </label>
            <div class="mt-1 relative rounded-md shadow-sm">
                <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" required
                    class="block w-full rounded-xl border-gray-300 pl-4 py-3 focus:border-[#6A8F3B] focus:ring-[#6A8F3B] @error('phone') border-red-500 @enderror"
                    placeholder="+216 20 123 456"
                    onfocus="if(this.value === '') this.value = '+216 '">
            </div>
            @error('phone')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Role Selection with Icons -->
        <div class="space-y-3">
            <label class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-6 h-6 text-[#6A8F3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span>الدور <span class="text-red-600">*</span></span>
            </label>
            
            <!-- Farmer -->
            <label class="flex items-center gap-4 p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-[#6A8F3B] hover:bg-[#6A8F3B]/5 transition-all" :class="role === 'farmer' ? 'border-[#6A8F3B] bg-[#6A8F3B]/10' : ''">
                <input type="radio" name="role" value="farmer" x-model="role" class="text-[#6A8F3B] focus:ring-[#6A8F3B] w-5 h-5">
                <div class="flex items-center gap-3 flex-1">
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-[#6A8F3B] to-[#5a7a2f] flex items-center justify-center text-white">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                        </svg>
                    </div>
                    <div>
                        <div class="font-bold text-gray-900">فلاح</div>
                        <div class="text-sm text-gray-500">منتج زيت الزيتون والزيتون</div>
                    </div>
                </div>
            </label>

            <!-- Carrier -->
            <label class="flex items-center gap-4 p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-[#6A8F3B] hover:bg-[#6A8F3B]/5 transition-all" :class="role === 'carrier' ? 'border-[#6A8F3B] bg-[#6A8F3B]/10' : ''">
                <input type="radio" name="role" value="carrier" x-model="role" class="text-[#6A8F3B] focus:ring-[#6A8F3B] w-5 h-5">
                <div class="flex items-center gap-3 flex-1">
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-[#C8A356] to-[#b08a3c] flex items-center justify-center text-white">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                        </svg>
                    </div>
                    <div>
                        <div class="font-bold text-gray-900">ناقِل</div>
                        <div class="text-sm text-gray-500">خدمات النقل والتوصيل</div>
                    </div>
                </div>
            </label>

            <!-- Mill -->
            <label class="flex items-center gap-4 p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-[#6A8F3B] hover:bg-[#6A8F3B]/5 transition-all" :class="role === 'mill' ? 'border-[#6A8F3B] bg-[#6A8F3B]/10' : ''">
                <input type="radio" name="role" value="mill" x-model="role" class="text-[#6A8F3B] focus:ring-[#6A8F3B] w-5 h-5">
                <div class="flex items-center gap-3 flex-1">
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-[#8B4513] to-[#6B3410] flex items-center justify-center text-white">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div>
                        <div class="font-bold text-gray-900">معصرة</div>
                        <div class="text-sm text-gray-500">عصر وتجهيز الزيتون</div>
                    </div>
                </div>
            </label>

            <!-- Packer -->
            <label class="flex items-center gap-4 p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-[#6A8F3B] hover:bg-[#6A8F3B]/5 transition-all" :class="role === 'packer' ? 'border-[#6A8F3B] bg-[#6A8F3B]/10' : ''">
                <input type="radio" name="role" value="packer" x-model="role" class="text-[#6A8F3B] focus:ring-[#6A8F3B] w-5 h-5">
                <div class="flex items-center gap-3 flex-1">
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-[#9333EA] to-[#7E22CE] flex items-center justify-center text-white">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <div>
                        <div class="font-bold text-gray-900">مُعبئ</div>
                        <div class="text-sm text-gray-500">تعبئة وتغليف المنتجات</div>
                    </div>
                </div>
            </label>

            <!-- Normal User -->
            <label class="flex items-center gap-4 p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-[#6A8F3B] hover:bg-[#6A8F3B]/5 transition-all" :class="role === 'normal' ? 'border-[#6A8F3B] bg-[#6A8F3B]/10' : ''">
                <input type="radio" name="role" value="normal" x-model="role" class="text-[#6A8F3B] focus:ring-[#6A8F3B] w-5 h-5">
                <div class="flex items-center gap-3 flex-1">
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-[#3B82F6] to-[#2563EB] flex items-center justify-center text-white">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <div class="font-bold text-gray-900">مشترٍ / مستورد</div>
                        <div class="text-sm text-gray-500">تصفح وشراء المنتجات</div>
                    </div>
                </div>
            </label>
            @error('role')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="pt-4">
            <button type="submit" 
                class="w-full py-4 px-6 bg-gradient-to-r from-[#6A8F3B] to-[#5a7a2f] text-white font-bold rounded-xl shadow-lg hover:shadow-xl hover:scale-[1.02] transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2" 
                :disabled="role === ''">
                <span>التالي</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                </svg>
            </button>
        </div>
    </form>
</div>
@endsection
