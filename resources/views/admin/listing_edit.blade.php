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

                <div class="grid md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-1">نوع المنتج (Category)</label>
                        <select id="categorySelect" name="category" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-[#6A8F3B] focus:ring-4 focus:ring-[#6A8F3B]/20" required>
                            <option value="oil" {{ old('category', $product->type) === 'oil' ? 'selected' : '' }}>زيت زيتون (Olive Oil)</option>
                            <option value="olive" {{ old('category', $product->type) === 'olive' ? 'selected' : '' }}>زيتون (Olives)</option>
                        </select>
                    </div>
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

                <div x-data="{ priceOnRequest: {{ old('price', $product->price) == 0 ? 'true' : 'false' }} }">
                    <div class="mb-4 p-4 bg-gray-50 border border-gray-200 rounded-xl">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <div class="relative">
                                <input type="checkbox" name="price_on_request" x-model="priceOnRequest" class="sr-only">
                                <div class="block bg-gray-300 w-10 h-6 rounded-full transition" :class="{'bg-[#6A8F3B]': priceOnRequest}"></div>
                                <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition" :class="{'transform translate-x-4': priceOnRequest}"></div>
                            </div>
                            <span class="text-sm font-bold text-gray-800">السعر عند الطلب (إخفاء السعر)</span>
                        </label>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div x-show="!priceOnRequest">
                            <label class="block text-sm font-semibold text-gray-800 mb-1">{{ __('Price') }} ({{ __('TND') }})</label>
                            <!-- If priceOnRequest is true, we send a hidden input with 0. If false, we show the number input. -->
                            <input type="number" step="0.01" min="0" name="price" :value="priceOnRequest ? 0 : '{{ old('price', $product->price) }}'" :required="!priceOnRequest" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-[#6A8F3B] focus:ring-4 focus:ring-[#6A8F3B]/20">
                        </div>
                        <div x-show="!priceOnRequest">
                            <label class="block text-sm font-semibold text-gray-800 mb-1">{{ __('Currency') }}</label>
                            <input name="currency" value="{{ old('currency', $listing->currency ?? 'TND') }}" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-[#6A8F3B] focus:ring-4 focus:ring-[#6A8F3B]/20">
                        </div>
                    </div>
                </div>

                <div class="grid md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-1">{{ __('Quantity') }}</label>
                        <input type="number" step="0.01" min="0" name="quantity" value="{{ old('quantity', $listing->quantity) }}" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-[#6A8F3B] focus:ring-4 focus:ring-[#6A8F3B]/20">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-1">{{ __('Unit') }}</label>
                        <input id="unitDisplay" value="{{ $product->type === 'oil' ? 'لتر (Liter)' : 'كغ (Kilogram)' }}" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-100 text-gray-700" readonly>
                        <input id="unitHidden" type="hidden" name="unit" value="{{ $product->type === 'oil' ? 'liter' : 'kg' }}">
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

                <!-- Interactive Media Gallery Manager -->
                <div class="p-5 bg-gray-50 border border-gray-200 rounded-2xl space-y-3" x-data="adminMediaManager({{ json_encode($listing->media ?? []) }})">
                    <div class="flex items-center justify-between border-b pb-2">
                        <label class="block text-sm font-bold text-gray-800">🖼️ {{ __('Media Gallery & Photos') }}</label>
                        <span class="text-xs text-gray-500 font-semibold">{{ __('Click red trash icon to delete an image') }}</span>
                    </div>

                    <input type="hidden" name="media" :value="existingImages.join(',')">

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-1">
                        <!-- Kept Existing Images -->
                        <template x-for="(img, index) in existingImages" :key="img">
                            <div class="relative group rounded-xl overflow-hidden border-2 border-gray-200 bg-black/5 aspect-square shadow-sm">
                                <img :src="'/storage/' + img" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                <button type="button" @click="removeImage(index)" 
                                        title="Delete image"
                                        class="absolute top-2 right-2 w-8 h-8 rounded-full bg-red-600 hover:bg-red-700 text-white flex items-center justify-center shadow-lg transition transform hover:scale-110">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                                <div class="absolute bottom-1 right-1 bg-black/60 text-white text-[10px] px-2 py-0.5 rounded backdrop-blur">Saved</div>
                            </div>
                        </template>

                        <!-- New Image Previews -->
                        <template x-for="(preview, index) in newPreviews" :key="index">
                            <div class="relative group rounded-xl overflow-hidden border-2 border-emerald-500 bg-emerald-50/50 aspect-square shadow-sm">
                                <img :src="preview.url" class="w-full h-full object-cover">
                                <button type="button" @click="removeNewPreview(index)" 
                                        title="Cancel new image"
                                        class="absolute top-2 right-2 w-8 h-8 rounded-full bg-red-600 hover:bg-red-700 text-white flex items-center justify-center shadow-lg transition transform hover:scale-110">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                                <div class="absolute bottom-1 right-1 bg-emerald-600 text-white text-[10px] px-2 py-0.5 rounded font-bold">New</div>
                            </div>
                        </template>

                        <!-- Dropzone / Add Button -->
                        <label class="border-2 border-dashed border-[#6A8F3B] hover:border-[#5a7a2f] rounded-xl flex flex-col items-center justify-center p-3 bg-white hover:bg-emerald-50/40 cursor-pointer transition aspect-square text-center relative group">
                            <input type="file" name="new_media[]" multiple accept="image/*" @change="handleFileSelect($event)" class="hidden">
                            <div class="w-10 h-10 rounded-full bg-[#6A8F3B]/10 text-[#6A8F3B] group-hover:bg-[#6A8F3B] group-hover:text-white flex items-center justify-center transition mb-1">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                            <span class="text-xs font-bold text-[#6A8F3B] group-hover:text-[#5a7a2f]">{{ __('Add Photos') }}</span>
                        </label>
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

<script>
function adminMediaManager(initialImages) {
    return {
        existingImages: Array.isArray(initialImages) ? initialImages : [],
        newPreviews: [],
        removeImage(index) {
            this.existingImages.splice(index, 1);
        },
        removeNewPreview(index) {
            this.newPreviews.splice(index, 1);
        },
        handleFileSelect(event) {
            const files = event.target.files;
            if (!files) return;
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const url = URL.createObjectURL(file);
                this.newPreviews.push({ file, url });
            }
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('categorySelect');
    if (categorySelect) {
        categorySelect.addEventListener('change', function() {
            const val = this.value;
            const display = document.getElementById('unitDisplay');
            const hidden = document.getElementById('unitHidden');
            if (display && hidden) {
                if (val === 'oil') {
                    display.value = 'لتر (Liter)';
                    hidden.value = 'liter';
                } else {
                    display.value = 'كغ (Kilogram)';
                    hidden.value = 'kg';
                }
            }
        });
    }
});
</script>
@endsection
