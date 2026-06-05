@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-100 py-8">
    <div class="max-w-5xl mx-auto px-4">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ __('Edit Listing') }}</h1>
                <p class="text-gray-600">{{ __('Fix price, details, or media for this listing') }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.listings') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">{{ __('Back to listings') }}</a>
                <a href="{{ route('listings.show', $listing) }}" class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition" target="_blank">{{ __('View live') }}</a>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-6 space-y-6">
            <div class="p-4 bg-gray-50 rounded-xl flex items-start gap-3">
                <div class="w-10 h-10 bg-green-100 text-green-700 rounded-full flex items-center justify-center font-bold">{{ substr($seller->name,0,1) }}</div>
                <div>
                    <div class="font-semibold text-gray-900">{{ $seller->name }} (ID {{ $seller->id }})</div>
                    <div class="text-sm text-gray-600">{{ ucfirst($seller->role) }} · {{ __('Listing ID') }} {{ $listing->id }}</div>
                    <div class="text-sm text-gray-600">{{ __('Status') }}: <span class="font-semibold">{{ ucfirst($listing->status) }}</span></div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.listings.update', $listing) }}" class="space-y-6" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-1">{{ __('Variety') }}</label>
                        <input name="variety" value="{{ old('variety', $product->variety) }}" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-[#6A8F3B] focus:ring-4 focus:ring-[#6A8F3B]/20" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-1">{{ __('Quality') }}</label>
                        <select name="quality" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-[#6A8F3B] focus:ring-4 focus:ring-[#6A8F3B]/20">
                            <option value="">{{ __('None') }}</option>
                            <option value="بكر ممتاز (EVOO)" {{ old('quality', $product->quality) === 'بكر ممتاز (EVOO)' ? 'selected' : '' }}>بكر ممتاز (EVOO)</option>
                            <option value="بكر (Virgin)" {{ old('quality', $product->quality) === 'بكر (Virgin)' ? 'selected' : '' }}>بكر (Virgin)</option>
                            <option value="بكر عادي (Ordinary Virgin)" {{ old('quality', $product->quality) === 'بكر عادي (Ordinary Virgin)' ? 'selected' : '' }}>بكر عادي (Ordinary Virgin)</option>
                            <option value="وقاد (Lampante)" {{ old('quality', $product->quality) === 'وقاد (Lampante)' ? 'selected' : '' }}>وقاد (Lampante)</option>
                            <option value="بيولوجي (Organic)" {{ old('quality', $product->quality) === 'بيولوجي (Organic)' ? 'selected' : '' }}>بيولوجي (Organic)</option>
                        </select>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_organic" value="1" {{ old('is_organic', $product->is_organic) ? 'checked' : '' }} class="h-4 w-4 text-[#6A8F3B] border-gray-300 rounded">
                        <span class="text-sm font-semibold text-gray-800">{{ __('Organic') }}</span>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-1">{{ __('Packaging / Condition') }}</label>
                        <select name="packaging" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-[#6A8F3B] focus:ring-4 focus:ring-[#6A8F3B]/20">
                            <option value="">{{ __('None') }}</option>
                            <option value="صبّة (Vrac)" {{ old('packaging', $listing->packaging) === 'صبّة (Vrac)' ? 'selected' : '' }}>صبّة (Vrac)</option>
                            <option value="معلّب (Packaged)" {{ old('packaging', $listing->packaging) === 'معلّب (Packaged)' ? 'selected' : '' }}>معلّب (Packaged)</option>
                            <option value="جملة (Gros)" {{ old('packaging', $listing->packaging) === 'جملة (Gros)' ? 'selected' : '' }}>جملة (Gros)</option>
                            <option value="تفصيل (Détail)" {{ old('packaging', $listing->packaging) === 'تفصيل (Détail)' ? 'selected' : '' }}>تفصيل (Détail)</option>
                        </select>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-1">{{ __('Price') }} ({{ __('TND') }})</label>
                        <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $product->price) }}" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-[#6A8F3B] focus:ring-4 focus:ring-[#6A8F3B]/20" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-1">{{ __('Currency') }}</label>
                        <input name="currency" value="{{ old('currency', $listing->currency ?? 'TND') }}" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-[#6A8F3B] focus:ring-4 focus:ring-[#6A8F3B]/20">
                    </div>
                </div>

                <div class="grid md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-1">{{ __('Quantity') }}</label>
                        <input type="number" step="0.01" min="0" name="quantity" value="{{ old('quantity', $listing->quantity) }}" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-[#6A8F3B] focus:ring-4 focus:ring-[#6A8F3B]/20" placeholder="{{ $product->type === 'oil' ? __('Liters') : __('Kilograms') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-1">{{ __('Unit') }}</label>
                        <input value="{{ $product->type === 'oil' ? __('Liter (fixed)') : __('Kilogram (fixed)') }}" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-100 text-gray-700" readonly>
                        <input type="hidden" name="unit" value="{{ $product->type === 'oil' ? 'liter' : 'kg' }}">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-1">{{ __('Min order') }}</label>
                        <input type="number" step="0.01" min="0" name="min_order" value="{{ old('min_order', $listing->min_order) }}" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-[#6A8F3B] focus:ring-4 focus:ring-[#6A8F3B]/20">
                    </div>
                </div>

                <div class="grid md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-1">{{ __('Weight (kg)') }}</label>
                        <input type="number" step="0.01" min="0" name="weight_kg" value="{{ old('weight_kg', $product->weight_kg) }}" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-[#6A8F3B] focus:ring-4 focus:ring-[#6A8F3B]/20">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-1">{{ __('Volume (liters)') }}</label>
                        <input type="number" step="0.01" min="0" name="volume_liters" value="{{ old('volume_liters', $product->volume_liters) }}" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-[#6A8F3B] focus:ring-4 focus:ring-[#6A8F3B]/20">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-1">{{ __('Stock') }}</label>
                        <input type="number" step="0.01" min="0" name="stock" value="{{ old('stock', $product->stock) }}" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-[#6A8F3B] focus:ring-4 focus:ring-[#6A8F3B]/20">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-800 mb-1">{{ __('Status') }}</label>
                    <select name="status" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-[#6A8F3B] focus:ring-4 focus:ring-[#6A8F3B]/20" required>
                        @foreach(['draft','active','paused','sold','out'] as $state)
                            <option value="{{ $state }}" {{ old('status', $listing->status) === $state ? 'selected' : '' }}>{{ ucfirst($state) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid md:grid-cols-2 gap-6 p-4 bg-gray-50 border border-gray-200 rounded-xl">
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-2">{{ __('Upload New Media') }}</label>
                        <input type="file" name="new_media[]" multiple accept="image/*" class="w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-xl focus:border-[#6A8F3B] focus:ring-4 focus:ring-[#6A8F3B]/20 bg-white hover:bg-gray-50 transition cursor-pointer file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#6A8F3B]/10 file:text-[#6A8F3B] hover:file:bg-[#6A8F3B]/20">
                        <p class="text-xs text-gray-500 mt-2">{{ __('Select images to append to the listing. Max 5MB per file.') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-2">{{ __('Existing Media Paths') }}</label>
                        <input name="media" value="{{ old('media', $listing->media ? implode(',', $listing->media) : '') }}" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-[#6A8F3B] focus:ring-4 focus:ring-[#6A8F3B]/20 font-mono text-sm bg-white" placeholder="listings/1/img1.webp,listings/1/img2.webp">
                        <p class="text-xs text-gray-500 mt-2">{{ __('Edit or remove existing paths (comma-separated).') }}</p>
                    </div>
                </div>

                @if($errors->any())
                <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-700">
                    <ul class="list-disc list-inside space-y-1 text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-3 bg-[#6A8F3B] text-white rounded-xl hover:bg-[#5a7a2f] transition font-bold">
                        {{ __('Save changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
