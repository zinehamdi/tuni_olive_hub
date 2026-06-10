@extends('layouts.app')
@section('content')
<div class="max-w-4xl mx-auto py-8 px-4" x-data="{
    currentStep: 1,
    productType: '',
    productId: '',
    variety: '',
    oliveCondition: '',
    harvestYear: '',
    quality: '',
    acidity: '',
    extractionMethod: 'cold_pressed',
    productionYear: '',
    quantity: '',
    unit: 'kg',
    price: '',
    currency: 'TND',
    minOrder: '',
    
    selectProductType(type) {
        this.productType = type;
        const select = document.querySelector('select[name=\'product_id\']');
        if (select) {
            const options = select.querySelectorAll('option');
            options.forEach(option => {
                if (option.value === '') return;
                const optionType = option.getAttribute('data-type');
                if (optionType === type) {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                }
            });
        }
    },
    
    getProductName() {
        const select = document.querySelector('select[name=\'product_id\']');
        if (!select) return '';
        const option = select.querySelector(\`option[value=\'\${this.productId}\']\`);
        return option ? option.textContent.trim() : '';
    },
    
    nextStep() {
        if (this.validateStep()) {
            if (this.currentStep < 4) {
                this.currentStep++;
                this.updateProgress();
            }
        }
    },
    
    prevStep() {
        if (this.currentStep > 1) {
            this.currentStep--;
            this.updateProgress();
        }
    },
    
    validateStep() {
        if (this.currentStep === 1) {
            if (!this.productType) {
                alert('الرجاء اختيار نوع المنتج');
                return false;
            }
            if (!this.productId) {
                alert('الرجاء اختيار المنتج');
                return false;
            }
        }
        if (this.currentStep === 3) {
            if (!this.quantity || this.quantity <= 0) {
                alert('الرجاء إدخال الكمية المتاحة');
                return false;
            }
            if (!this.price || this.price <= 0) {
                alert('الرجاء إدخال السعر');
                return false;
            }
        }
        return true;
    },
    
    updateProgress() {
        for (let i = 1; i <= 4; i++) {
            const indicator = document.getElementById(\`step\${i}-indicator\`);
            const label = document.getElementById(\`step\${i}-label\`);
            
            if (indicator && label) {
                if (i < this.currentStep) {
                    indicator.classList.remove('bg-gray-300');
                    indicator.classList.add('bg-[#6A8F3B]');
                    label.classList.remove('text-gray-500');
                    label.classList.add('text-[#6A8F3B]');
                } else if (i === this.currentStep) {
                    indicator.classList.remove('bg-gray-300');
                    indicator.classList.add('bg-[#6A8F3B]');
                    label.classList.remove('text-gray-500');
                    label.classList.add('text-[#6A8F3B]');
                    label.classList.add('font-bold');
                } else {
                    indicator.classList.add('bg-gray-300');
                    indicator.classList.remove('bg-[#6A8F3B]');
                    label.classList.add('text-gray-500');
                    label.classList.remove('text-[#6A8F3B]');
                    label.classList.remove('font-bold');
                }
            }
        }
        
        for (let i = 1; i <= 3; i++) {
            const progress = document.getElementById(\`progress\${i}\`);
            if (progress) {
                if (i < this.currentStep) {
                    progress.style.width = '100%';
                } else {
                    progress.style.width = '0%';
                }
            }
        }
        
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}">
    <div class="bg-white rounded-2xl shadow-xl p-6 lg:p-8">
        <!-- Progress Steps -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-2">
                <div class="flex-1">
                    <div class="flex items-center">
                        <div id="step1-indicator" class="w-10 h-10 bg-[#6A8F3B] text-white rounded-full flex items-center justify-center font-bold transition">1</div>
                        <div class="flex-1 h-1 bg-gray-300 mx-2"><div id="progress1" class="h-full bg-[#6A8F3B] transition-all duration-300" style="width: 0%"></div></div>
                    </div>
                    <p id="step1-label" class="text-xs mt-1 text-center font-medium text-[#6A8F3B]">نوع المنتج</p>
                </div>
                <div class="flex-1">
                    <div class="flex items-center">
                        <div id="step2-indicator" class="w-10 h-10 bg-gray-300 text-white rounded-full flex items-center justify-center font-bold transition">2</div>
                        <div class="flex-1 h-1 bg-gray-300 mx-2"><div id="progress2" class="h-full bg-[#6A8F3B] transition-all duration-300" style="width: 0%"></div></div>
                    </div>
                    <p id="step2-label" class="text-xs mt-1 text-center text-gray-500">التفاصيل</p>
                </div>
                <div class="flex-1">
                    <div class="flex items-center">
                        <div id="step3-indicator" class="w-10 h-10 bg-gray-300 text-white rounded-full flex items-center justify-center font-bold transition">3</div>
                        <div class="flex-1 h-1 bg-gray-300 mx-2"><div id="progress3" class="h-full bg-[#6A8F3B] transition-all duration-300" style="width: 0%"></div></div>
                    </div>
                    <p id="step3-label" class="text-xs mt-1 text-center text-gray-500">السعر والكمية</p>
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-center">
                        <div id="step4-indicator" class="w-10 h-10 bg-gray-300 text-white rounded-full flex items-center justify-center font-bold transition">4</div>
                    </div>
                    <p id="step4-label" class="text-xs mt-1 text-center text-gray-500">التأكيد</p>
                </div>
            </div>
        </div>

        <!-- Error Messages -->
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="wizardForm" method="POST" action="{{ route('listings.store') }}">
            @csrf
            <input type="hidden" name="seller_id" value="{{ auth()->id() }}">
            <input type="hidden" name="status" value="active">

            <!-- Debug indicator -->
            <div class="mb-4 p-2 bg-blue-100 text-sm">
                Step: <span x-text="currentStep"></span> | 
                Product Type: <span x-text="productType || 'Not selected'"></span>
            </div>

            <!-- Step 1: Product Type Selection -->
            <div x-show="currentStep === 1" x-transition.opacity class="space-y-6">
                <h2 class="text-2xl font-bold text-[#1B2A1B] mb-4">اختر نوع المنتج</h2>
                
                <div class="grid md:grid-cols-2 gap-4">
                    <button type="button" @click="selectProductType('olive')" 
                        :class="productType === 'olive' ? 'border-[#6A8F3B] bg-[#6A8F3B] bg-opacity-10' : 'border-gray-300'"
                        class="border-2 rounded-xl p-6 hover:border-[#6A8F3B] transition text-center">
                        <svg class="w-16 h-16 mx-auto mb-3 text-[#6A8F3B]" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2a8 8 0 100 16 8 8 0 000-16zM7 9a1 1 0 100 2 1 1 0 000-2zm6 0a1 1 0 100 2 1 1 0 000-2z"/>
                        </svg>
                        <h3 class="text-xl font-bold mb-2">زيتون</h3>
                        <p class="text-sm text-gray-600">زيتون خام للبيع</p>
                    </button>

                    <button type="button" @click="selectProductType('olive_oil')" 
                        :class="productType === 'olive_oil' ? 'border-[#6A8F3B] bg-[#6A8F3B] bg-opacity-10' : 'border-gray-300'"
                        class="border-2 rounded-xl p-6 hover:border-[#6A8F3B] transition text-center">
                        <svg class="w-16 h-16 mx-auto mb-3 text-[#C8A356]" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"/>
                        </svg>
                        <h3 class="text-xl font-bold mb-2">زيت زيتون</h3>
                        <p class="text-sm text-gray-600">زيت زيتون مُعالج</p>
                    </button>
                </div>

                <div x-show="productType">
                    <label class="block text-[#C8A356] font-semibold mb-2">اختر المنتج *</label>
                    <select name="product_id" x-model="productId" required
                        class="w-full rounded-xl border border-gray-300 px-4 py-3 bg-white focus:ring-2 focus:ring-[#C8A356]">
                        <option value="">— اختر المنتج —</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" data-type="{{ $product->type }}">
                                {{ __($product->variety) }}
                                @if($product->quality) - {{ $product->quality }} @endif
                                ({{ $product->type === 'olive_oil' ? 'زيت زيتون' : 'زيتون' }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Step 2: Product Details -->
            <div x-show="currentStep === 2" x-transition.opacity class="space-y-6">
                <h2 class="text-2xl font-bold text-[#1B2A1B] mb-4">تفاصيل المنتج</h2>

                <!-- Olive Specific Fields -->
                <div x-show="productType === 'olive'" class="space-y-4">
                    <div>
                        <label class="block text-[#C8A356] font-semibold mb-2">الصنف</label>
                        <select x-model="variety" class="w-full rounded-xl border border-gray-300 px-4 py-3 bg-white focus:ring-2 focus:ring-[#C8A356]">
                            <option value="">— اختر الصنف —</option>
                            
                            <!-- أصناف محلية شائعة -->
                            <optgroup label="📍 أصناف تونسية محلية">
                                <option value="chemlali">شملالي – Chemlali (وسط/جنوب – زيت خفيف، ذهبي)</option>
                                <option value="chetoui">شتوي – Chetoui (شمال – زيت قوي، مرّ، عطري)</option>
                                <option value="oueslati">وسلاتي – Oueslati (وسط – متوازن، ممتاز للجودة العالية)</option>
                                <option value="zalmati">زلماتي – Zalmati (جنوب شرقي – ثقيل، مقاوم للجفاف)</option>
                                <option value="zarrazi">زرازي – Zarrazi (قابس – غامق، ثابت في الطعم)</option>
                                <option value="barouni">برّوني – Barouni (شمال – ناعم، زيت ومائدة)</option>
                                <option value="meski">مسكي – Meski (مائدة – حلو، ثمرة كبيرة)</option>
                                <option value="chemchali">شمشالي – Chemchali (جنوب – متوسط، مرارة خفيفة)</option>
                                <option value="gerboui">جربوي – Gerboui (شمال غربي – متوازن، إنتاج محدود)</option>
                                <option value="sayali">سيالي – Sayali (شمال – طري، ذهبي)</option>
                                <option value="sahli">ساحلي – Sahli (ساحلي – إنتاج وفير، متأقلم)</option>
                                <option value="fakhari">فخاري – Fakhari (فخاري – زيت كثيف ونكهة مميزة)</option>
                                <option value="tounsi">تونسي – Tounsi (تونسي – صنف تقليدي)</option>
                                <option value="neb_jmel">ناب الجمل – Neb Jmel (ناب الجمل – ثمرة طويلة للمائدة)</option>
                                <option value="rkhami">رخامي – Rkhami (رخامي – إنتاج مزدوج)</option>
                            </optgroup>
                            
                            <!-- أصناف دخيلة -->
                            <optgroup label="🌍 أصناف دخيلة (مستوردة)">
                                <option value="arbequina">أربيكينا – Arbequina (إسباني – خفيف، فاكهي)</option>
                                <option value="arbosana">أربوسانا – Arbosana (إسباني – عطري، مرّ خفيف)</option>
                                <option value="koroneiki">كورونيكي – Koroneiki (يوناني – مركز، مرّ، قوي)</option>
                                <option value="picholine">بيشولين – Picholine (فرنسي – عشبي، متوسط الجودة)</option>
                            </optgroup>
                            
                            <!-- أصناف نادرة -->
                            <optgroup label="⭐ أصناف نادرة ومحلية">
                                <option value="adefou">عدّفو – Adefou (أصلي، محدود الإنتاج)</option>
                                <option value="boudaoud">بوداود – Boudaoud (تقليدي، عطري)</option>
                                <option value="fougi-gtar">فوڨي ڨطار – Fougi Gtar (جبلي، زيت غامق)</option>
                            </optgroup>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[#C8A356] font-semibold mb-2">حالة الزيتون</label>
                        <select x-model="oliveCondition" class="w-full rounded-xl border border-gray-300 px-4 py-3 bg-white focus:ring-2 focus:ring-[#C8A356]">
                            <option value="fresh">طازج</option>
                            <option value="semi_dry">نصف جاف</option>
                            <option value="for_pressing">للعصر</option>
                            <option value="table_olive">زيتون مائدة</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[#C8A356] font-semibold mb-2">سنة الحصاد</label>
                        <input type="number" x-model="harvestYear" min="2020" max="2030" 
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-[#C8A356]"
                            placeholder="2024">
                    </div>
                </div>

                <!-- Olive Oil Specific Fields -->
                <div x-show="productType === 'olive_oil'" class="space-y-4">
                    <div>
                        <label class="block text-[#C8A356] font-semibold mb-2">درجة الجودة</label>
                        <select x-model="quality" class="w-full rounded-xl border border-gray-300 px-4 py-3 bg-white focus:ring-2 focus:ring-[#C8A356]">
                            <option value="">— اختر الجودة —</option>
                            <option value="extra_virgin">بكر ممتاز (Extra Virgin)</option>
                            <option value="virgin">بكر (Virgin)</option>
                            <option value="refined">مكرر (Refined)</option>
                            <option value="organic">عضوي (Organic)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[#C8A356] font-semibold mb-2">نسبة الحموضة (%)</label>
                        <input type="number" x-model="acidity" step="0.01" min="0" max="5"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-[#C8A356]"
                            placeholder="0.5">
                        <p class="text-xs text-gray-500 mt-1">زيت زيتون بكر ممتاز: أقل من 0.8%</p>
                    </div>

                    <div>
                        <label class="block text-[#C8A356] font-semibold mb-2">طريقة الاستخلاص</label>
                        <select x-model="extractionMethod" class="w-full rounded-xl border border-gray-300 px-4 py-3 bg-white focus:ring-2 focus:ring-[#C8A356]">
                            <option value="cold_pressed">عصر على البارد (Cold Pressed)</option>
                            <option value="traditional">تقليدي (Traditional)</option>
                            <option value="modern">حديث (Modern)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[#C8A356] font-semibold mb-2">سنة الإنتاج</label>
                        <input type="number" x-model="productionYear" min="2020" max="2030"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-[#C8A356]"
                            placeholder="2024">
                    </div>
                </div>
            </div>

            <!-- Step 3: Price & Quantity -->
            <div x-show="currentStep === 3" x-transition.opacity class="space-y-6">
                <h2 class="text-2xl font-bold text-[#1B2A1B] mb-4">السعر والكمية</h2>

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[#C8A356] font-semibold mb-2">الكمية المتاحة *</label>
                        <input type="number" x-model="quantity" step="0.001" min="0" required
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-[#C8A356]"
                            placeholder="100">
                    </div>

                    <div>
                        <label class="block text-[#C8A356] font-semibold mb-2">الوحدة</label>
                        <select x-model="unit" class="w-full rounded-xl border border-gray-300 px-4 py-3 bg-white focus:ring-2 focus:ring-[#C8A356]">
                            <option value="kg">كيلوغرام (kg)</option>
                            <option value="ton">طن (ton)</option>
                            <option value="liter">لتر (liter)</option>
                            <option value="bottle">زجاجة (bottle)</option>
                        </select>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[#C8A356] font-semibold mb-2">السعر للوحدة *</label>
                        <input type="number" x-model="price" step="0.01" min="0" required
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-[#C8A356]"
                            placeholder="مثال: 2.50">
                    </div>

                    <div>
                        <label class="block text-[#C8A356] font-semibold mb-2">العملة</label>
                        <select x-model="currency" class="w-full rounded-xl border border-gray-300 px-4 py-3 bg-white focus:ring-2 focus:ring-[#C8A356]">
                            <option value="TND">دينار تونسي (TND)</option>
                            <option value="USD">دولار أمريكي (USD)</option>
                            <option value="EUR">يورو (EUR)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-[#C8A356] font-semibold mb-2">الحد الأدنى للطلب</label>
                    <input type="number" name="min_order" x-model="minOrder" step="0.001" min="0"
                        class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-[#C8A356]"
                        placeholder="10">
                    <p class="text-xs text-gray-500 mt-1">اتركه فارغاً إذا لم يكن هناك حد أدنى</p>
                </div>

                <div>
                    <label class="block text-[#C8A356] font-semibold mb-3">{{ __('Payment Options') }}</label>
                    <div class="space-y-3">
                        <!-- Cash -->
                        <label class="flex items-center gap-3 p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-[#6A8F3B] hover:bg-[#6A8F3B]/5 transition-all">
                            <input type="checkbox" name="payment_methods[]" value="cash" class="text-[#6A8F3B] focus:ring-[#6A8F3B] rounded w-5 h-5">
                            <div class="flex items-center gap-3 flex-1">
                                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-[#10B981] to-[#059669] flex items-center justify-center text-white flex-shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <div class="font-bold text-gray-900 text-sm">{{ __('Cash') }}</div>
                                    <div class="text-xs text-gray-500">{{ __('Pay in cash upon delivery or pickup') }}</div>
                                </div>
                            </div>
                        </label>

                        <!-- Bank Transfer -->
                        <label class="flex items-center gap-3 p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-[#6A8F3B] hover:bg-[#6A8F3B]/5 transition-all">
                            <input type="checkbox" name="payment_methods[]" value="bank_transfer" class="text-[#6A8F3B] focus:ring-[#6A8F3B] rounded w-5 h-5">
                            <div class="flex items-center gap-3 flex-1">
                                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-[#3B82F6] to-[#2563EB] flex items-center justify-center text-white flex-shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <div class="font-bold text-gray-900 text-sm">{{ __('Bank Transfer') }}</div>
                                    <div class="text-xs text-gray-500">{{ __('Direct bank transfer to account') }}</div>
                                </div>
                            </div>
                        </label>

                        <!-- Check -->
                        <label class="flex items-center gap-3 p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-[#6A8F3B] hover:bg-[#6A8F3B]/5 transition-all">
                            <input type="checkbox" name="payment_methods[]" value="check" class="text-[#6A8F3B] focus:ring-[#6A8F3B] rounded w-5 h-5">
                            <div class="flex items-center gap-3 flex-1">
                                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-[#9333EA] to-[#7E22CE] flex items-center justify-center text-white flex-shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <div class="font-bold text-gray-900 text-sm">{{ __('Check') }}</div>
                                    <div class="text-xs text-gray-500">{{ __('Payment by certified check') }}</div>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-[#C8A356] font-semibold mb-3">{{ __('Delivery Options') }}</label>
                    <div class="space-y-3">
                        <!-- Pickup -->
                        <label class="flex items-center gap-3 p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-[#6A8F3B] hover:bg-[#6A8F3B]/5 transition-all">
                            <input type="checkbox" name="delivery_options[]" value="pickup" class="text-[#6A8F3B] focus:ring-[#6A8F3B] rounded w-5 h-5">
                            <div class="flex items-center gap-3 flex-1">
                                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-[#6A8F3B] to-[#5a7a2f] flex items-center justify-center text-white flex-shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <div class="font-bold text-gray-900 text-sm">{{ __('Pickup') }}</div>
                                    <div class="text-xs text-gray-500">{{ __('Buyer picks up from location') }}</div>
                                </div>
                            </div>
                        </label>

                        <!-- Local Delivery -->
                        <label class="flex items-center gap-3 p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-[#6A8F3B] hover:bg-[#6A8F3B]/5 transition-all">
                            <input type="checkbox" name="delivery_options[]" value="local_delivery" class="text-[#6A8F3B] focus:ring-[#6A8F3B] rounded w-5 h-5">
                            <div class="flex items-center gap-3 flex-1">
                                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-[#C8A356] to-[#b08a3c] flex items-center justify-center text-white flex-shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <div class="font-bold text-gray-900 text-sm">{{ __('Local Delivery') }}</div>
                                    <div class="text-xs text-gray-500">{{ __('We deliver within the region') }}</div>
                                </div>
                            </div>
                        </label>

                        <!-- Export -->
                        <label class="flex items-center gap-3 p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-[#6A8F3B] hover:bg-[#6A8F3B]/5 transition-all">
                            <input type="checkbox" name="delivery_options[]" value="export" class="text-[#6A8F3B] focus:ring-[#6A8F3B] rounded w-5 h-5">
                            <div class="flex items-center gap-3 flex-1">
                                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-[#EF4444] to-[#DC2626] flex items-center justify-center text-white flex-shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <div class="font-bold text-gray-900 text-sm">{{ __('Export') }}</div>
                                    <div class="text-xs text-gray-500">{{ __('Available for international export') }}</div>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Step 4: Review & Confirm -->
            <div x-show="currentStep === 4" x-transition.opacity class="space-y-6">
                <h2 class="text-2xl font-bold text-[#1B2A1B] mb-4">مراجعة وتأكيد</h2>

                <div class="bg-gradient-to-br from-[#F8F4EC] to-[#EEF5E9] rounded-xl p-6 space-y-4">
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">نوع المنتج</p>
                            <p class="font-bold" x-text="productType === 'olive' ? 'زيتون' : 'زيت زيتون'"></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">المنتج المختار</p>
                            <p class="font-bold" x-text="getProductName()"></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">الكمية</p>
                            <p class="font-bold"><span x-text="quantity"></span> <span x-text="unit"></span></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">السعر</p>
                            <p class="font-bold"><span x-text="price"></span> <span x-text="currency"></span> / <span x-text="unit"></span></p>
                        </div>
                        <div x-show="minOrder > 0">
                            <p class="text-sm text-gray-600">الحد الأدنى للطلب</p>
                            <p class="font-bold"><span x-text="minOrder"></span> <span x-text="unit"></span></p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" required id="agree" class="rounded text-[#6A8F3B]">
                    <label for="agree" class="mr-2 text-sm">أوافق على شروط وأحكام المنصة</label>
                </div>
            </div>

            <!-- Navigation Buttons -->
            <div class="flex justify-between mt-8 pt-6 border-t border-gray-200">
                <button type="button" @click="prevStep" 
                    x-show="currentStep > 1"
                    x-transition.opacity
                    class="px-8 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition font-bold">
                    ← السابق
                </button>
                <div x-show="currentStep === 1"></div>

                <button type="button" @click="nextStep" 
                    x-show="currentStep < 4"
                    x-transition.opacity
                    class="px-8 py-3 bg-[#6A8F3B] text-white rounded-xl hover:bg-[#5a7a2f] transition font-bold shadow-md hover:shadow-lg">
                    التالي →
                </button>

                <button type="submit" 
                    x-show="currentStep === 4"
                    x-transition.opacity
                    class="px-8 py-3 bg-gradient-to-r from-[#1B2A1B] to-[#6A8F3B] text-white rounded-xl hover:shadow-xl transition font-bold">
                    ✓ نشر العرض
                </button>
            </div>
        </form>
    </div>
</div>

<style>
[x-cloak] { display: none !important; }
</style>

@endsection
