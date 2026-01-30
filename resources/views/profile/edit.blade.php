<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-6 bg-white shadow sm:rounded-lg flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold text-gray-900">{{ __('Share updates and keep your profile fresh') }}</p>
                    <p class="text-sm text-gray-600">{{ __('Post a story (image or video) and manage your photo gallery in one place.') }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('user.profile', auth()->id()) }}#stories" class="px-4 py-2 bg-[#6A8F3B] text-white rounded-lg text-sm font-semibold hover:bg-[#5a7a2f] shadow-sm">{{ __('Add Story') }}</a>
                    <a href="#photos" class="px-4 py-2 bg-white text-[#6A8F3B] border border-[#6A8F3B]/40 rounded-lg text-sm font-semibold hover:bg-[#6A8F3B]/5 shadow-sm">{{ __('Manage Gallery') }}</a>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
