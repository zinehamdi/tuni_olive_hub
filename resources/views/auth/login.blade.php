<x-guest-layout>
    <div class="mx-auto max-w-xl">
        <!-- Login Card -->
        <div class="bg-white rounded-2xl shadow-2xl p-8 border border-gray-100">
            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('Email')" class="text-gray-900 font-bold mb-2 flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#6A8F3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                        </svg>
                    </x-input-label>
                    <x-text-input id="email" 
                        class="block mt-2 w-full rounded-xl border-2 border-gray-200 px-4 py-3 bg-gray-50 text-gray-900 focus:border-[#6A8F3B] focus:ring-4 focus:ring-[#6A8F3B]/20 transition-all" 
                        type="email" 
                        name="email" 
                        :value="old('email')" 
                        required 
                        autofocus 
                        autocomplete="username" 
                        placeholder="example@email.com" />
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
                        class="block mt-2 w-full rounded-xl border-2 border-gray-200 px-4 py-3 bg-gray-50 text-gray-900 focus:border-[#6A8F3B] focus:ring-4 focus:ring-[#6A8F3B]/20 transition-all"
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

                <!-- Submit Button -->
                <div class="space-y-4">
                    <button type="submit" class="w-full py-4 px-6 bg-gradient-to-r from-[#6A8F3B] to-[#5a7a2f] text-white font-bold rounded-xl shadow-lg hover:shadow-xl hover:scale-[1.02] transition-all duration-200 flex items-center justify-center gap-2">
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
