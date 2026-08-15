<x-guest-layout>
    <div class="mx-auto max-w-2xl">
        <!-- Register Card -->
        <div class="bg-white rounded-2xl shadow-2xl p-8 border border-gray-100">
            <form method="GET" action="{{ route('register.role') }}" class="space-y-6" x-data="{ role: '' }">
                <h2 class="text-2xl font-bold text-center mb-6 text-[#6A8F3B]">{{ __('Select your role') }}</h2>
                
                <!-- Role Selection with Icons -->
                <div class="space-y-3">
                    <label class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-[#6A8F3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span>{{ __('Role') }} <span class="text-red-600">*</span></span>
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
                                <div class="font-bold text-gray-900">{{ __('Farmer') }}</div>
                                <div class="text-sm text-gray-500">{{ __('Olive and olive oil producer') }}</div>
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
                                <div class="font-bold text-gray-900">{{ __('Carrier') }}</div>
                                <div class="text-sm text-gray-500">{{ __('Transport and delivery services') }}</div>
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
                                <div class="font-bold text-gray-900">{{ __('Mill') }}</div>
                                <div class="text-sm text-gray-500">{{ __('Pressing and processing olives') }}</div>
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
                                <div class="font-bold text-gray-900">{{ __('Packer') }}</div>
                                <div class="text-sm text-gray-500">{{ __('Packaging of products') }}</div>
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
                                <div class="font-bold text-gray-900">{{ __('Buyer / Importer') }}</div>
                                <div class="text-sm text-gray-500">{{ __('Browse and purchase products') }}</div>
                            </div>
                        </div>
                    </label>
                </div>

                <!-- Social Login -->
                <div>
                    <a href="{{ route('auth.facebook') }}" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-[#1877F2] hover:bg-[#166fe5] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#1877F2] transition-colors items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd"></path>
                        </svg>
                        {{ __('Continue with Facebook') }}
                    </a>
                    
                    <a href="{{ route('auth.google') }}" class="w-full flex justify-center py-3 px-4 mt-3 border border-gray-200 rounded-xl shadow-sm text-sm font-bold text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 transition-colors items-center gap-2">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                        </svg>
                        {{ __('Continue with Google') }}
                    </a>
                </div>

                <div class="relative flex py-2 items-center">
                    <div class="flex-grow border-t border-gray-300"></div>
                    <span class="flex-shrink-0 mx-4 text-gray-400 font-medium text-sm">{{ __('OR') }}</span>
                    <div class="flex-grow border-t border-gray-300"></div>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                    class="w-full py-4 px-6 bg-gradient-to-r from-[#6A8F3B] to-[#5a7a2f] text-white font-bold rounded-xl shadow-lg hover:shadow-xl hover:scale-[1.02] transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2" 
                    :disabled="role === ''">
                    <span>{{ __('Next') }}</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </button>

                <!-- Login Link -->
                <div class="text-center pt-4 border-t">
                    <span class="text-gray-600">{{ __('Already have an account?') }}</span>
                    <a href="{{ route('login') }}" class="text-[#C8A356] hover:text-[#b08a3c] font-bold transition">
                        {{ __('Log in') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
