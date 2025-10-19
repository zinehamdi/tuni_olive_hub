<x-guest-layout>
    <div dir="rtl" class="mx-auto max-w-xl bg-gradient-to-br from-[#F8F4EC] to-[#EEF5E9] rounded-2xl shadow-xl p-6 lg:p-8">
    <div class="mb-4 text-sm text-gray-700">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-gradient-to-br from-white to-[#F8F4EC] focus:ring-2 focus:ring-[#C8A356]"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end mt-4">
            <x-primary-button class="min-h-[44px] px-5 py-3 bg-gradient-to-r from-[#1B2A1B] to-[#6A8F3B] text-white font-bold shadow-lg hover:shadow-2xl hover:scale-105 transition focus:ring-2 focus:ring-[#C8A356]">
                {{ __('Confirm') }}
            </x-primary-button>
        </div>
    </form>
    </div>
</x-guest-layout>
