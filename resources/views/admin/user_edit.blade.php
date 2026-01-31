@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-100 py-8">
    <div class="max-w-4xl mx-auto px-4">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ __('Edit User') }}</h1>
                <p class="text-sm text-gray-600">{{ __('Update account info and privacy visibility for this user.') }}</p>
            </div>
            <a href="{{ route('admin.users') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition">← {{ __('Back to users') }}</a>
        </div>

        @if(session('status'))
            <div class="mb-4 px-4 py-3 rounded-lg bg-green-50 text-green-800 border border-green-200">{{ session('status') }}</div>
        @endif

        <div class="bg-white rounded-2xl shadow-xl p-6 space-y-6">
            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-1">{{ __('Name') }}</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full rounded-lg border-2 border-gray-200 px-3 py-2 focus:border-[#6A8F3B] focus:ring-2 focus:ring-[#6A8F3B]/20">
                        <x-input-error class="mt-1" :messages="$errors->get('name')" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-1">{{ __('Email') }}</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full rounded-lg border-2 border-gray-200 px-3 py-2 focus:border-[#6A8F3B] focus:ring-2 focus:ring-[#6A8F3B]/20">
                        <x-input-error class="mt-1" :messages="$errors->get('email')" />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-800 mb-1">{{ __('Phone') }}</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full rounded-lg border-2 border-gray-200 px-3 py-2 focus:border-[#6A8F3B] focus:ring-2 focus:ring-[#6A8F3B]/20">
                    <x-input-error class="mt-1" :messages="$errors->get('phone')" />
                </div>

                <div class="p-4 rounded-xl border border-gray-200 bg-gray-50 space-y-3">
                    <div class="text-sm font-semibold text-gray-900">{{ __('Privacy & visibility') }}</div>
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="hidden" name="show_contact_info" value="0">
                        <input type="checkbox" name="show_contact_info" value="1" class="mt-1 text-[#6A8F3B] focus:ring-[#6A8F3B] rounded" {{ old('show_contact_info', $user->show_contact_info) ? 'checked' : '' }}>
                        <div>
                            <div class="text-sm font-semibold text-gray-900">{{ __('Show phone and email') }}</div>
                            <div class="text-xs text-gray-600">{{ __('Display contact details on the public profile page.') }}</div>
                        </div>
                    </label>
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="hidden" name="show_address" value="0">
                        <input type="checkbox" name="show_address" value="1" class="mt-1 text-[#6A8F3B] focus:ring-[#6A8F3B] rounded" {{ old('show_address', $user->show_address) ? 'checked' : '' }}>
                        <div>
                            <div class="text-sm font-semibold text-gray-900">{{ __('Show address') }}</div>
                            <div class="text-xs text-gray-600">{{ __('Reveal saved addresses on the public profile page.') }}</div>
                        </div>
                    </label>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="px-5 py-2 bg-[#6A8F3B] text-white rounded-lg font-semibold hover:bg-[#5a7a2f] transition">{{ __('Save changes') }}</button>
                    <a href="{{ route('admin.users') }}" class="px-5 py-2 bg-gray-200 text-gray-800 rounded-lg font-semibold hover:bg-gray-300 transition">{{ __('Cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
