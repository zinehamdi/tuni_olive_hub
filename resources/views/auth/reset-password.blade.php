<x-guest-layout>
    <div dir="rtl" class="mx-auto max-w-xl bg-gradient-to-br from-[#F8F4EC] to-[#EEF5E9] rounded-2xl shadow-xl p-6 lg:p-8">
    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-gradient-to-br from-white to-[#F8F4EC] focus:ring-2 focus:ring-[#C8A356]" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-gradient-to-br from-white to-[#F8F4EC] focus:ring-2 focus:ring-[#C8A356]" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-gradient-to-br from-white to-[#F8F4EC] focus:ring-2 focus:ring-[#C8A356]"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="min-h-[44px] px-5 py-3 bg-gradient-to-r from-[#1B2A1B] to-[#6A8F3B] text-white font-bold shadow-lg hover:shadow-2xl hover:scale-105 transition focus:ring-2 focus:ring-[#C8A356]">
                {{ __('Reset Password') }}
            </x-primary-button>
        </div>
    </form>
    </div>
</x-guest-layout>
