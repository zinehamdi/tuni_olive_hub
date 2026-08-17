<x-guest-layout>
    <div class="mx-auto max-w-md">
        <!-- Login Card -->
        <div class="bg-white rounded-2xl shadow-2xl p-5 border border-gray-100">
            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <!-- Email or Phone Number -->
                <div>
                    <x-input-label for="email" :value="__('Email or Phone Number')" class="text-gray-900 font-bold mb-2 flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#6A8F3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                        </svg>
                    </x-input-label>
                    <x-text-input id="email" 
                        class="block mt-1 w-full rounded-xl border-2 border-gray-200 px-4 py-2 bg-gray-50 text-gray-900 focus:border-[#6A8F3B] focus:ring-4 focus:ring-[#6A8F3B]/20 transition-all" 
                        type="text" 
                        name="email" 
                        :value="old('email')" 
                        required 
                        autofocus 
                        autocomplete="username" 
                        placeholder="{{ __('Email or phone number') }}" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div>
                    <x-input-label for="password" :value="__('Password')" class="text-gray-900 font-bold mb-2 flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#6A8F3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </x-input-label>
                    <x-text-input id="password" 
                        class="block mt-1 w-full rounded-xl border-2 border-gray-200 px-4 py-2 bg-gray-50 text-gray-900 focus:border-[#6A8F3B] focus:ring-4 focus:ring-[#6A8F3B]/20 transition-all"
                        type="password"
                        name="password"
                        required 
                        autocomplete="current-password" 
                        placeholder="••••••••" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                        <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-[#6A8F3B] shadow-sm focus:ring-[#6A8F3B]" name="remember">
                        <span class="mr-2 text-sm text-gray-600 group-hover:text-gray-900 transition">{{ __('Remember me') }}</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="text-sm text-[#6A8F3B] hover:text-[#5a7a2f] font-semibold transition" href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif
                </div>

                <!-- Social Login -->
                <div>
                    <a href="{{ route('auth.facebook') }}" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-[#1877F2] hover:bg-[#166fe5] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#1877F2] transition-colors items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd"></path>
                        </svg>
                        {{ __('Continue with Facebook') }}
                    </a>
                    
                    <a href="{{ route('auth.google') }}" class="w-full flex justify-center py-2 px-4 mt-2 border border-gray-200 rounded-xl shadow-sm text-sm font-bold text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 transition-colors items-center gap-2">
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
                <div class="space-y-4">
                    <button type="submit" class="w-full py-2.5 px-6 bg-gradient-to-r from-[#6A8F3B] to-[#5a7a2f] text-white font-bold rounded-xl shadow-lg hover:shadow-xl hover:scale-[1.02] transition-all duration-200 flex items-center justify-center gap-2">
                        <span>{{ __('Log in') }}</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                    </button>

                    <!-- Register Link -->
                    <div class="text-center">
                        <span class="text-gray-600">{{ __("Don't have an account?") }}</span>
                        <a href="{{ route('register') }}" class="text-[#C8A356] hover:text-[#b08a3c] font-bold transition">
                            {{ __('Register now') }}
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
