<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            ✏️ تعديل سعر سوق
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl rounded-lg">
                <div class="p-8">
                    <form action="{{ route('admin.prices.souk.update', $price) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Souk Name -->
                        <div>
                            <label for="souk_name" class="block text-sm font-medium text-gray-700 mb-2">
                                اسم السوق <span class="text-red-500">*</span>
                            </label>
                            <select name="souk_name" id="souk_name" required
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-olive focus:ring-olive">
                                <option value="">اختر السوق</option>
                                @foreach(\App\Models\SoukPrice::getFamousSouks() as $soukKey => $soukName)
                                <option value="{{ $soukKey }}" {{ old('souk_name', $price->souk_name) === $soukKey ? 'selected' : '' }}>
                                    {{ $soukName }}
                                </option>
                                @endforeach
                            </select>
                            @error('souk_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Governorate -->
                        <div>
                            <label for="governorate" class="block text-sm font-medium text-gray-700 mb-2">
                                المحافظة
                            </label>
                            <input type="text" name="governorate" id="governorate" value="{{ old('governorate', $price->governorate) }}"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-olive focus:ring-olive"
                                placeholder="مثال: صفاقس، تونس، سوسة...">
                            @error('governorate')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Product Type -->
                        <div>
                            <label for="product_type" class="block text-sm font-medium text-gray-700 mb-2">
                                نوع المنتج <span class="text-red-500">*</span>
                            </label>
                            <select name="product_type" id="product_type" required
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-olive focus:ring-olive"
                                onchange="toggleQuality()">
                                <option value="">اختر النوع</option>
                                <option value="olive" {{ old('product_type', $price->product_type) === 'olive' ? 'selected' : '' }}>زيتون</option>
                                <option value="oil" {{ old('product_type', $price->product_type) === 'oil' ? 'selected' : '' }}>زيت</option>
                            </select>
                            @error('product_type')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Variety -->
                        <div>
                            <label for="variety" class="block text-sm font-medium text-gray-700 mb-2">
                                الصنف/النوع <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="variety" id="variety" value="{{ old('variety', $price->variety) }}" required
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-olive focus:ring-olive"
                                placeholder="مثال: الشملالي، الشتوي، زيت عضوي...">
                            @error('variety')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Quality (for oil only) -->
                        <div id="quality-field" class="hidden">
                            <label for="quality" class="block text-sm font-medium text-gray-700 mb-2">
                                الجودة
                            </label>
                            <select name="quality" id="quality"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-olive focus:ring-olive">
                                <option value="">اختياري</option>
                                <option value="EVOO" {{ old('quality', $price->quality) === 'EVOO' ? 'selected' : '' }}>EVOO (ممتاز)</option>
                                <option value="virgin" {{ old('quality', $price->quality) === 'virgin' ? 'selected' : '' }}>Virgin (بكر)</option>
                                <option value="refined" {{ old('quality', $price->quality) === 'refined' ? 'selected' : '' }}>Refined (مكرر)</option>
                            </select>
                            @error('quality')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Price Range -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="price_min" class="block text-sm font-medium text-gray-700 mb-2">
                                    أقل سعر <span class="text-red-500">*</span>
                                </label>
                                <input type="number" step="0.01" name="price_min" id="price_min" value="{{ old('price_min', $price->price_min) }}" required
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-olive focus:ring-olive"
                                    placeholder="0.00">
                                @error('price_min')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="price_max" class="block text-sm font-medium text-gray-700 mb-2">
                                    أعلى سعر <span class="text-red-500">*</span>
                                </label>
                                <input type="number" step="0.01" name="price_max" id="price_max" value="{{ old('price_max', $price->price_max) }}" required
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-olive focus:ring-olive"
                                    placeholder="0.00">
                                @error('price_max')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Currency & Unit -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">
                                    العملة <span class="text-red-500">*</span>
                                </label>
                                <select name="currency" id="currency" required
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-olive focus:ring-olive">
                                    <option value="TND" {{ old('currency', $price->currency) === 'TND' ? 'selected' : '' }}>TND (دينار تونسي)</option>
                                    <option value="EUR" {{ old('currency', $price->currency) === 'EUR' ? 'selected' : '' }}>EUR (يورو)</option>
                                    <option value="USD" {{ old('currency', $price->currency) === 'USD' ? 'selected' : '' }}>USD (دولار)</option>
                                </select>
                                @error('currency')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="unit" class="block text-sm font-medium text-gray-700 mb-2">
                                    الوحدة <span class="text-red-500">*</span>
                                </label>
                                <select name="unit" id="unit" required
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-olive focus:ring-olive">
                                    <option value="kg" {{ old('unit', $price->unit) === 'kg' ? 'selected' : '' }}>kg (كيلوغرام)</option>
                                    <option value="L" {{ old('unit', $price->unit) === 'L' ? 'selected' : '' }}>L (لتر)</option>
                                    <option value="ton" {{ old('unit', $price->unit) === 'ton' ? 'selected' : '' }}>ton (طن)</option>
                                </select>
                                @error('unit')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Date -->
                        <div>
                            <label for="date" class="block text-sm font-medium text-gray-700 mb-2">
                                التاريخ <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="date" id="date" value="{{ old('date', $price->date->format('Y-m-d')) }}" required
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-olive focus:ring-olive">
                            @error('date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Trend -->
                        <div>
                            <label for="trend" class="block text-sm font-medium text-gray-700 mb-2">
                                الاتجاه <span class="text-red-500">*</span>
                            </label>
                            <select name="trend" id="trend" required
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-olive focus:ring-olive">
                                <option value="up" {{ old('trend', $price->trend) === 'up' ? 'selected' : '' }}>📈 ارتفاع</option>
                                <option value="stable" {{ old('trend', $price->trend) === 'stable' ? 'selected' : '' }}>➡️ مستقر</option>
                                <option value="down" {{ old('trend', $price->trend) === 'down' ? 'selected' : '' }}>📉 انخفاض</option>
                            </select>
                            @error('trend')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Change Percentage -->
                        <div>
                            <label for="change_percentage" class="block text-sm font-medium text-gray-700 mb-2">
                                نسبة التغيير (%)
                            </label>
                            <input type="number" step="0.01" name="change_percentage" id="change_percentage" value="{{ old('change_percentage', $price->change_percentage) }}"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-olive focus:ring-olive"
                                placeholder="مثال: 5.5 أو -3.2">
                            @error('change_percentage')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                ملاحظات
                            </label>
                            <textarea name="notes" id="notes" rows="3"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-olive focus:ring-olive"
                                placeholder="أي معلومات إضافية...">{{ old('notes', $price->notes) }}</textarea>
                            @error('notes')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Active Status -->
                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $price->is_active) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-olive focus:ring-olive">
                            <label for="is_active" class="mr-2 text-sm font-medium text-gray-700">
                                عرض السعر (نشط)
                            </label>
                        </div>

                        <!-- Buttons -->
                        <div class="flex items-center justify-between pt-6 border-t">
                            <a href="{{ route('admin.prices.souk.index') }}" class="text-gray-600 hover:text-gray-800">
                                إلغاء
                            </a>
                            <button type="submit" class="bg-olive text-white px-8 py-3 rounded-lg hover:bg-olive-dark transition font-semibold">
                                تحديث السعر
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleQuality() {
            const productType = document.getElementById('product_type').value;
            const qualityField = document.getElementById('quality-field');
            
            if (productType === 'oil') {
                qualityField.classList.remove('hidden');
            } else {
                qualityField.classList.add('hidden');
                document.getElementById('quality').value = '';
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', () => {
            toggleQuality();
        });
    </script>
</x-app-layout>
