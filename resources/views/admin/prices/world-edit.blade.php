<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            ✏️ تعديل سعر عالمي
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl rounded-lg">
                <div class="p-8">
                    <form action="{{ route('admin.prices.world.update', $price) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-2 gap-4">
                            <!-- Country -->
                            <div>
                                <label for="country" class="block text-sm font-medium text-gray-700 mb-2">
                                    الدولة <span class="text-red-500">*</span>
                                </label>
                                <select name="country" id="country" required
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-olive focus:ring-olive">
                                    <option value="">اختر الدولة</option>
                                    @foreach(\App\Models\WorldOlivePrice::getMajorProducers() as $countryKey => $countryName)
                                    <option value="{{ $countryKey }}" {{ old('country', $price->country) === $countryKey ? 'selected' : '' }}>
                                        {{ $countryName }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('country')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Region -->
                            <div>
                                <label for="region" class="block text-sm font-medium text-gray-700 mb-2">
                                    المنطقة
                                </label>
                                <input type="text" name="region" id="region" value="{{ old('region', $price->region) }}"
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-olive focus:ring-olive"
                                    placeholder="مثال: أندلسيا، توسكانا...">
                                @error('region')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <!-- Variety -->
                            <div>
                                <label for="variety" class="block text-sm font-medium text-gray-700 mb-2">
                                    الصنف
                                </label>
                                <input type="text" name="variety" id="variety" value="{{ old('variety', $price->variety) }}"
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-olive focus:ring-olive"
                                    placeholder="مثال: Arbequina, Koroneiki...">
                                @error('variety')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Quality -->
                            <div>
                                <label for="quality" class="block text-sm font-medium text-gray-700 mb-2">
                                    الجودة <span class="text-red-500">*</span>
                                </label>
                                <select name="quality" id="quality" required
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-olive focus:ring-olive">
                                    <option value="">اختر الجودة</option>
                                    <option value="EVOO" {{ old('quality', $price->quality) === 'EVOO' ? 'selected' : '' }}>EVOO (ممتاز)</option>
                                    <option value="virgin" {{ old('quality', $price->quality) === 'virgin' ? 'selected' : '' }}>Virgin (بكر)</option>
                                    <option value="refined" {{ old('quality', $price->quality) === 'refined' ? 'selected' : '' }}>Refined (مكرر)</option>
                                    <option value="lampante" {{ old('quality', $price->quality) === 'lampante' ? 'selected' : '' }}>Lampante</option>
                                </select>
                                @error('quality')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <!-- Price -->
                            <div>
                                <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                                    السعر <span class="text-red-500">*</span>
                                </label>
                                <input type="number" step="0.01" name="price" id="price" value="{{ old('price', $price->price) }}" required
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-olive focus:ring-olive"
                                    placeholder="0.00">
                                @error('price')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Currency -->
                            <div>
                                <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">
                                    العملة <span class="text-red-500">*</span>
                                </label>
                                <select name="currency" id="currency" required
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-olive focus:ring-olive">
                                    <option value="EUR" {{ old('currency', $price->currency) === 'EUR' ? 'selected' : '' }}>EUR</option>
                                    <option value="USD" {{ old('currency', $price->currency) === 'USD' ? 'selected' : '' }}>USD</option>
                                    <option value="TND" {{ old('currency', $price->currency) === 'TND' ? 'selected' : '' }}>TND</option>
                                </select>
                                @error('currency')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Unit -->
                            <div>
                                <label for="unit" class="block text-sm font-medium text-gray-700 mb-2">
                                    الوحدة <span class="text-red-500">*</span>
                                </label>
                                <select name="unit" id="unit" required
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-olive focus:ring-olive">
                                    <option value="L" {{ old('unit', $price->unit) === 'L' ? 'selected' : '' }}>L (لتر)</option>
                                    <option value="kg" {{ old('unit', $price->unit) === 'kg' ? 'selected' : '' }}>kg (كيلوغرام)</option>
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

                        <div class="grid grid-cols-2 gap-4">
                            <!-- Trend -->
                            <div>
                                <label for="trend" class="block text-sm font-medium text-gray-700 mb-2">
                                    الاتجاه <span class="text-red-500">*</span>
                                </label>
                                <select name="trend" id="trend" required
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-olive focus:ring-olive">
                                    <option value="up" {{ old('trend', $price->trend) === 'up' ? 'selected' : '' }}>📈 ارتفاع</option>
                                    <option value="stable" {{ old('trend', $price->trend) === 'stable' ? 'selected' : '' }}>➡️ مستقر</option>
                                    <option value="down" {{ old('trend', $price->trend) === 'down' ? 'selected' : '' }}>📉 انخفاع</option>
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
                        </div>

                        <!-- Source -->
                        <div>
                            <label for="source" class="block text-sm font-medium text-gray-700 mb-2">
                                المصدر
                            </label>
                            <input type="text" name="source" id="source" value="{{ old('source', $price->source) }}"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-olive focus:ring-olive"
                                placeholder="مثال: International Olive Council, Reuters...">
                            @error('source')
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

                        <!-- Buttons -->
                        <div class="flex items-center justify-between pt-6 border-t">
                            <a href="{{ route('admin.prices.world.index') }}" class="text-gray-600 hover:text-gray-800">
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
</x-app-layout>
