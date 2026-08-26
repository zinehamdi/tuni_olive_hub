@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-100 py-8">
    <div class="max-w-4xl mx-auto px-4">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    {{ app()->getLocale() === 'ar' ? 'تعديل موعد تسويق' : __('Edit Marketing Appointment') }}
                </h1>
                <p class="text-gray-600">{{ __('Update appointment details and status') }}</p>
            </div>
            <a href="{{ route('admin.marketing.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 transition font-semibold">
                <svg class="w-5 h-5 {{ __('') }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ __('Back') }}
            </a>
        </div>

        @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-xl shadow-sm">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100 p-8">
            <form action="{{ route('admin.marketing.update', $appointment) }}" method="POST" class="space-y-6">
                @csrf
                @method('PATCH')

                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('Client Name') }}</label>
                        <input type="text" name="name" value="{{ old('name', $appointment->name) }}" 
                            class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 focus:border-[#6A8F3B] focus:ring-4 focus:ring-[#6A8F3B]/10 transition-all outline-none" required>
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('Phone Number') }}</label>
                        <input type="text" name="phone" value="{{ old('phone', $appointment->phone) }}" 
                            class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 focus:border-[#6A8F3B] focus:ring-4 focus:ring-[#6A8F3B]/10 transition-all outline-none font-mono" required>
                    </div>

                    <!-- Appointment Date -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('Appointment Date') }}</label>
                        <input type="datetime-local" name="appointment_date" value="{{ old('appointment_date', $appointment->appointment_date->format('Y-m-d\TH:i')) }}" 
                            class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 focus:border-[#6A8F3B] focus:ring-4 focus:ring-[#6A8F3B]/10 transition-all outline-none" required>
                    </div>

                    <!-- Total Budget -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('Total Budget (TND)') }}</label>
                        <input type="number" step="0.01" name="total_budget" value="{{ old('total_budget', $appointment->total_budget) }}" 
                            class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 focus:border-[#6A8F3B] focus:ring-4 focus:ring-[#6A8F3B]/10 transition-all outline-none" required>
                    </div>
                </div>

                <!-- Business Info -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('Business Info / Requirements') }}</label>
                    <textarea name="business_info" rows="4" 
                        class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 focus:border-[#6A8F3B] focus:ring-4 focus:ring-[#6A8F3B]/10 transition-all outline-none resize-none" required>{{ old('business_info', $appointment->business_info) }}</textarea>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('Status') }}</label>
                    <select name="status" class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 focus:border-[#6A8F3B] focus:ring-4 focus:ring-[#6A8F3B]/10 transition-all outline-none appearance-none bg-no-repeat bg-[right_1rem_center] bg-[length:1em_1em]" style="background-image: url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 fill=%22none%22 viewBox=%220 0 24 24%22 stroke=%22%236b7280%22%3E%3Cpath stroke-linecap=%22round%22 stroke-linejoin=%22round%22 stroke-width=%222%22 d=%22M19 9l-7 7-7-7%22/%3E%3C/svg%3E');">
                        <option value="pending" {{ old('status', $appointment->status) === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                        <option value="confirmed" {{ old('status', $appointment->status) === 'confirmed' ? 'selected' : '' }}>{{ __('Confirmed') }}</option>
                        <option value="completed" {{ old('status', $appointment->status) === 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                        <option value="cancelled" {{ old('status', $appointment->status) === 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                    </select>
                </div>

                <!-- Cart Data (Display Only) -->
                @if($appointment->cart_data)
                <div class="mt-8 p-6 bg-gray-50 rounded-2xl border border-gray-100">
                    <h3 class="text-sm font-bold text-gray-900 mb-4">{{ __('Selected Services') }}</h3>
                    <div class="space-y-3">
                        @php $cart = is_string($appointment->cart_data) ? json_decode($appointment->cart_data, true) : $appointment->cart_data; @endphp
                        @foreach($cart as $item)
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center gap-3">
                                <span class="w-2 h-2 rounded-full bg-[#6A8F3B]"></span>
                                <span class="text-gray-700 font-medium">{{ $item['title'][app()->getLocale()] ?? $item['title']['en'] ?? 'Service' }}</span>
                            </div>
                            <span class="text-gray-900 font-bold">{{ number_format($item['price'], 0) }} TND</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="pt-6 border-t border-gray-50 flex gap-4">
                    <button type="submit" class="flex-1 px-6 py-4 bg-gradient-to-r from-[#6A8F3B] to-[#5a7a2f] text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
                        {{ __('Save Changes') }}
                    </button>
                    <a href="{{ route('admin.marketing.index') }}" class="px-6 py-4 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-all duration-300">
                        {{ __('Cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
