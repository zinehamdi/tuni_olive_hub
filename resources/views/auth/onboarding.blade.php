@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto mt-10 p-6 bg-white rounded-lg shadow-md">
    <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">{{ __('Complete Your Profile') }}</h2>
    
    <p class="text-gray-600 mb-8 text-center">
        {{ __('To fully access the marketplace and connect with others, please provide your phone number and role.') }}
    </p>

    <form method="POST" action="{{ route('onboarding.store') }}" class="space-y-6">
        @csrf

        <!-- Phone Number -->
        <div>
            <label for="phone" class="block text-sm font-medium text-gray-700">{{ __('Phone Number') }} <span class="text-red-500">*</span></label>
            <div class="mt-1 relative rounded-md shadow-sm">
                <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" required
                    class="block w-full rounded-md border-gray-300 pl-4 py-3 focus:border-green-500 focus:ring-green-500 @error('phone') border-red-500 @enderror"
                    placeholder="+216 20 123 456">
            </div>
            @error('phone')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Role Selection -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">{{ __('I am a:') }} <span class="text-red-500">*</span></label>
            
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                <label class="relative flex flex-col p-4 border border-gray-300 rounded-lg cursor-pointer hover:border-green-500 focus-within:ring-2 focus-within:ring-green-500 focus-within:ring-offset-2">
                    <input type="radio" name="role" value="farmer" class="sr-only" required>
                    <span class="font-bold text-gray-900 text-center">{{ __('Farmer') }}</span>
                    <span class="text-xs text-gray-500 text-center mt-1">{{ __('Sell olive and oil') }}</span>
                </label>

                <label class="relative flex flex-col p-4 border border-gray-300 rounded-lg cursor-pointer hover:border-green-500 focus-within:ring-2 focus-within:ring-green-500 focus-within:ring-offset-2">
                    <input type="radio" name="role" value="mill" class="sr-only">
                    <span class="font-bold text-gray-900 text-center">{{ __('Mill Owner') }}</span>
                    <span class="text-xs text-gray-500 text-center mt-1">{{ __('Press and sell oil') }}</span>
                </label>

                <label class="relative flex flex-col p-4 border border-gray-300 rounded-lg cursor-pointer hover:border-green-500 focus-within:ring-2 focus-within:ring-green-500 focus-within:ring-offset-2">
                    <input type="radio" name="role" value="carrier" class="sr-only">
                    <span class="font-bold text-gray-900 text-center">{{ __('Carrier') }}</span>
                    <span class="text-xs text-gray-500 text-center mt-1">{{ __('Transport goods') }}</span>
                </label>
                
                <label class="relative flex flex-col p-4 border border-gray-300 rounded-lg cursor-pointer hover:border-green-500 focus-within:ring-2 focus-within:ring-green-500 focus-within:ring-offset-2">
                    <input type="radio" name="role" value="packer" class="sr-only">
                    <span class="font-bold text-gray-900 text-center">{{ __('Packer / Exporter') }}</span>
                    <span class="text-xs text-gray-500 text-center mt-1">{{ __('Buy in bulk') }}</span>
                </label>
                
                <label class="relative flex flex-col p-4 border border-gray-300 rounded-lg cursor-pointer hover:border-green-500 focus-within:ring-2 focus-within:ring-green-500 focus-within:ring-offset-2">
                    <input type="radio" name="role" value="normal" class="sr-only">
                    <span class="font-bold text-gray-900 text-center">{{ __('Consumer') }}</span>
                    <span class="text-xs text-gray-500 text-center mt-1">{{ __('Buy for personal use') }}</span>
                </label>
            </div>
            @error('role')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="pt-4">
            <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-bold text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                {{ __('Complete Registration') }}
            </button>
        </div>
    </form>
    
    <script>
        // Simple script to highlight the selected radio card
        document.querySelectorAll('input[type="radio"][name="role"]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                document.querySelectorAll('input[type="radio"][name="role"]').forEach(r => {
                    r.parentElement.classList.remove('border-green-600', 'bg-green-50', 'ring-2', 'ring-green-600');
                });
                if(e.target.checked) {
                    e.target.parentElement.classList.add('border-green-600', 'bg-green-50', 'ring-2', 'ring-green-600');
                }
            });
        });
    </script>
</div>
@endsection
