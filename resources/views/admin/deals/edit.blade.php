@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-100 py-8">
    <div class="max-w-4xl mx-auto px-4">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">{{ __('Edit Deal') }}</h1>
                <p class="text-gray-600">{{ __('Modify the request or service offer') }}</p>
            </div>
            <a href="{{ route('admin.deals.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition font-bold">
                ← {{ __('Back') }}
            </a>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8">
            <form action="{{ route('admin.deals.update', $deal) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid md:grid-cols-2 gap-6 mb-6">
                    <!-- Type -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('Type') }}</label>
                        <select name="type" class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 focus:border-amber-500 focus:outline-none transition">
                            <option value="demand" {{ $deal->type === 'demand' ? 'selected' : '' }}>{{ __('Demand (Searching)') }}</option>
                            <option value="service" {{ $deal->type === 'service' ? 'selected' : '' }}>{{ __('Service (Providing)') }}</option>
                            <option value="supply" {{ $deal->type === 'supply' ? 'selected' : '' }}>{{ __('Supply (Supplier)') }}</option>
                        </select>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('Status') }}</label>
                        <select name="status" class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 focus:border-amber-500 focus:outline-none transition">
                            <option value="active" {{ $deal->status === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                            <option value="expired" {{ $deal->status === 'expired' ? 'selected' : '' }}>{{ __('Expired') }}</option>
                            <option value="closed" {{ $deal->status === 'closed' ? 'selected' : '' }}>{{ __('Closed') }}</option>
                        </select>
                    </div>
                </div>

                <!-- Tabs for Languages -->
                <div x-data="{ tab: '{{ app()->getLocale() }}' }" class="mb-8">
                    <div class="flex gap-2 mb-4 border-b border-gray-100 pb-2">
                        <button type="button" @click="tab = 'ar'" :class="tab === 'ar' ? 'bg-amber-100 text-amber-800' : 'text-gray-500'" class="px-4 py-2 rounded-lg font-bold transition">Arabic</button>
                        <button type="button" @click="tab = 'fr'" :class="tab === 'fr' ? 'bg-amber-100 text-amber-800' : 'text-gray-500'" class="px-4 py-2 rounded-lg font-bold transition">French</button>
                        <button type="button" @click="tab = 'en'" :class="tab === 'en' ? 'bg-amber-100 text-amber-800' : 'text-gray-500'" class="px-4 py-2 rounded-lg font-bold transition">English</button>
                    </div>

                    <div x-show="tab === 'ar'" dir="rtl">
                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 mb-2">العنوان (العربية)</label>
                            <input type="text" name="title[ar]" value="{{ $deal->title['ar'] ?? '' }}" class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 focus:border-amber-500 focus:outline-none transition" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 mb-2">الوصف (العربية)</label>
                            <textarea name="description[ar]" rows="4" class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 focus:border-amber-500 focus:outline-none transition" required>{{ $deal->description['ar'] ?? '' }}</textarea>
                        </div>
                    </div>

                    <div x-show="tab === 'fr'">
                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Titre (Français)</label>
                            <input type="text" name="title[fr]" value="{{ $deal->title['fr'] ?? '' }}" class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 focus:border-amber-500 focus:outline-none transition" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Description (Français)</label>
                            <textarea name="description[fr]" rows="4" class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 focus:border-amber-500 focus:outline-none transition" required>{{ $deal->description['fr'] ?? '' }}</textarea>
                        </div>
                    </div>

                    <div x-show="tab === 'en'">
                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Title (English)</label>
                            <input type="text" name="title[en]" value="{{ $deal->title['en'] ?? '' }}" class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 focus:border-amber-500 focus:outline-none transition" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Description (English)</label>
                            <textarea name="description[en]" rows="4" class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 focus:border-amber-500 focus:outline-none transition" required>{{ $deal->description['en'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6 mb-8">
                    <!-- Price Range -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('Price Range / Budget') }}</label>
                        <input type="text" name="price_range" value="{{ $deal->price_range }}" placeholder="e.g. 100 - 200 TND" class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 focus:border-amber-500 focus:outline-none transition">
                    </div>

                    <!-- Location -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('Location') }}</label>
                        <input type="text" name="location" value="{{ $deal->location }}" placeholder="e.g. Kairouan, Tunisia" class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 focus:border-amber-500 focus:outline-none transition">
                    </div>
                </div>

                <div class="flex items-center gap-6 mb-8">
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <div class="relative w-12 h-6">
                            <input type="checkbox" name="is_featured" value="1" {{ $deal->is_featured ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-full h-full bg-gray-200 rounded-full peer-checked:bg-amber-500 transition-colors"></div>
                            <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-6"></div>
                        </div>
                        <span class="text-sm font-bold text-gray-700 group-hover:text-amber-600 transition">{{ __('Featured Deal') }}</span>
                    </label>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('Expires At') }}</label>
                        <input type="date" name="expires_at" value="{{ $deal->expires_at ? $deal->expires_at->format('Y-m-d') : '' }}" class="px-4 py-2 rounded-xl border-2 border-gray-100 focus:border-amber-500 focus:outline-none transition">
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-12 py-4 bg-amber-600 text-white rounded-2xl hover:bg-amber-700 transition font-bold text-lg shadow-xl hover:-translate-y-1 active:scale-95">
                        {{ __('Update Deal') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
