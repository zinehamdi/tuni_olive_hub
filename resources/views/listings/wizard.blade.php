@extends('layouts.app')

@section('content')
<script>
// Wizard form doesn't need product data injection anymore
console.log('[wizard] Variety selection mode - no product database needed');
</script>
<div class="min-h-screen bg-gradient-to-br from-[#F8F4EC] to-[#EEF5E9] py-12 px-4" x-data="wizardForm" x-init="console.log('[wizard] Alpine x-data initialized');">
    <div class="max-w-3xl mx-auto">
        <!-- Loading Overlay -->
        <div x-show="isSubmitting" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
            <div class="bg-white rounded-2xl p-8 flex flex-col items-center shadow-2xl">
                <svg class="animate-spin h-16 w-16 text-[#6A8F3B] mb-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-xl font-bold text-[#1B2A1B]">جاري نشر العرض...</p>
                <p class="text-gray-600 mt-2">الرجاء الانتظار</p>
            </div>
        </div>

        <!-- Error Alert -->
        <div x-show="errorMessage" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-md" style="display: none;">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-red-500 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="flex-1">
                    <p class="font-bold text-red-800">حدث خطأ</p>
                    <p class="text-red-700" x-text="errorMessage"></p>
                </div>
                <button @click="errorMessage = ''" class="text-red-500 hover:text-red-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    <div class="max-w-3xl mx-auto">
        <!-- Wizard Container -->
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">
            <!-- Progress Bar -->
            <div class="bg-gradient-to-r from-[#1B2A1B] to-[#6A8F3B] p-6">
                <div class="flex items-center justify-between mb-4">
                    <template x-for="step in totalSteps" :key="step">
                        <div class="flex-1 flex items-center">
                            <div 
                                :class="step <= currentStep ? 'bg-white text-[#6A8F3B]' : 'bg-[#6A8F3B] bg-opacity-30 text-white'"
                                class="w-10 h-10 rounded-full flex items-center justify-center font-bold transition-all duration-300 mx-auto"
                                x-text="step">
                            </div>
                            <div x-show="step < totalSteps" class="flex-1 h-1 bg-white bg-opacity-30 mx-2"></div>
                        </div>
                    </template>
                </div>
                <p class="text-white text-center text-lg font-semibold" x-text="stepTitle"></p>
            </div>

            <!-- Form Content -->
            <form method="POST" action="{{ route('listings.store') }}" @submit.prevent="handleSubmit" enctype="multipart/form-data" class="p-8">
                @csrf
                <input type="hidden" name="seller_id" value="{{ auth()->id() }}">
                <input type="hidden" name="status" value="active">
                <input type="hidden" name="estimated_oil_yield" x-model="formData.estimated_oil_yield">
                <input type="hidden" name="category" x-model="formData.category">
                <input type="hidden" name="variety" x-model="formData.variety">
                <input type="hidden" name="quality" x-model="formData.quality">
                <input type="hidden" name="packaging" x-model="formData.packaging">
                <input type="hidden" name="quantity" x-model="formData.quantity">
                <input type="hidden" name="price" x-model="formData.price_on_request ? 0 : formData.price">
                <input type="hidden" name="currency" x-model="formData.currency">
                <input type="hidden" name="unit" x-model="formData.unit">
                <input type="hidden" name="min_order" x-model="formData.min_order">
                <input type="hidden" name="payment_methods" x-model="JSON.stringify(formData.payment_methods)">
                <input type="hidden" name="delivery_options" x-model="JSON.stringify(formData.delivery_options)">
                
                <!-- Location Hidden Fields -->
                <input type="hidden" name="location_text" x-model="formData.location_text">
                <input type="hidden" name="latitude" x-model="formData.latitude">
                <input type="hidden" name="longitude" x-model="formData.longitude">
                <input type="hidden" name="governorate" x-model="formData.governorate">
                <input type="hidden" name="delegation" x-model="formData.delegation">

                <!-- Step 1: Product Category -->
                <div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-10" x-transition:enter-end="opacity-100 transform translate-x-0">
                    <h2 class="text-3xl font-bold text-[#1B2A1B] mb-2">ما الذي تريد بيعه؟</h2>
                    <p class="text-gray-600 mb-8">اختر نوع المنتج الذي تود عرضه للبيع</p>
                    
                    <div class="space-y-4">
                        <button type="button" @click="selectCategory('olive')"
                            :class="formData.category === 'olive' ? 'border-[#6A8F3B] bg-[#6A8F3B] bg-opacity-5 shadow-lg' : 'border-gray-200 hover:border-[#6A8F3B]'"
                            class="w-full border-2 rounded-2xl p-6 transition-all duration-300 text-right flex items-center group">
                            <div class="flex-shrink-0 w-16 h-16 bg-gradient-to-br from-[#6A8F3B] to-[#5a7a2f] rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="8"/>
                                </svg>
                            </div>
                            <div class="mr-6 flex-1">
                                <h3 class="text-2xl font-bold text-[#1B2A1B] mb-2">🫒 زيتون طازج</h3>
                                <p class="text-gray-600">زيتون خام من المزرعة مباشرة</p>
                            </div>
                            <div x-show="formData.category === 'olive'" class="flex-shrink-0">
                                <svg class="w-8 h-8 text-[#6A8F3B]" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </button>

                        <button type="button" @click="selectCategory('oil')"
                            :class="formData.category === 'oil' ? 'border-[#C8A356] bg-[#C8A356] bg-opacity-5 shadow-lg' : 'border-gray-200 hover:border-[#C8A356]'"
                            class="w-full border-2 rounded-2xl p-6 transition-all duration-300 text-right flex items-center group">
                            <div class="flex-shrink-0 w-16 h-16 bg-gradient-to-br from-[#C8A356] to-[#b08a3c] rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C10.9 2 10 2.9 10 4V5H8C6.9 5 6 5.9 6 7V9C6 10.1 6.9 11 8 11H8.5L9 13C9 14.7 10.3 16 12 16C13.7 16 15 14.7 15 13L15.5 11H16C17.1 11 18 10.1 18 9V7C18 5.9 17.1 5 16 5H14V4C14 2.9 13.1 2 12 2M8 7H16V9H15L14 15C14 15.6 13.6 16 13 16H11C10.4 16 10 15.6 10 15L9 9H8V7M11 17H13C13.6 17 14 17.4 14 18V20C14 21.1 13.1 22 12 22C10.9 22 10 21.1 10 20V18C10 17.4 10.4 17 11 17Z"/>
                                </svg>
                            </div>
                            <div class="mr-6 flex-1">
                                <h3 class="text-2xl font-bold text-[#1B2A1B] mb-2">🫗 زيت زيتون</h3>
                                <p class="text-gray-600">زيت زيتون معصور ومعالج</p>
                            </div>
                            <div x-show="formData.category === 'oil'" class="flex-shrink-0">
                                <svg class="w-8 h-8 text-[#C8A356]" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Step 2: Variety Selection -->
                <div x-show="currentStep === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-10" x-transition:enter-end="opacity-100 transform translate-x-0">
                    <h2 class="text-3xl font-bold text-[#1B2A1B] mb-2">{{ __('Select specific product') }}</h2>
                    <p class="text-gray-600 mb-8">{{ __('Select the type of') }} <span x-text="formData.category === 'olive' ? '{{ __('olives') }}' : '{{ __('olive oil') }}'"></span> {{ __('you are selling') }}</p>
                    
                    <div class="bg-gradient-to-br from-[#F8F4EC] to-[#EEF5E9] rounded-2xl p-6">
                        <label class="block text-lg font-bold text-[#1B2A1B] mb-4">الصنف / Variety</label>
                        <select x-model="formData.variety" required
                                class="w-full text-xl rounded-xl border-2 border-gray-300 px-6 py-4 bg-white focus:ring-4 focus:ring-[#6A8F3B] focus:border-[#6A8F3B] transition">
                            <option value="">— اختر الصنف / Choose Variety —</option>
                            
                            <!-- أصناف محلية شائعة -->
                            <optgroup label="📍 أصناف تونسية محلية – Local Tunisian Varieties">
                                <option value="chemlali">{{ __('chemlali') }} – Chemlali (وسط/جنوب – زيت خفيف، ذهبي)</option>
                                <option value="chetoui">{{ __('chetoui') }} – Chetoui (شمال – زيت قوي، مرّ، عطري)</option>
                                <option value="oueslati">{{ __('oueslati') }} – Oueslati (وسط – متوازن، ممتاز للجودة العالية)</option>
                                <option value="zalmati">{{ __('zalmati') }} – Zalmati (جنوب شرقي – ثقيل، مقاوم للجفاف)</option>
                                <option value="zarrazi">{{ __('zarrazi') }} – Zarrazi (قابس – غامق، ثابت في الطعم)</option>
                                <option value="barouni">{{ __('barouni') }} – Barouni (شمال – ناعم، زيت ومائدة)</option>
                                <option value="meski">{{ __('meski') }} – Meski (مائدة – حلو، ثمرة كبيرة)</option>
                                <option value="chemchali">{{ __('chemchali') }} – Chemchali (جنوب – متوسط، مرارة خفيفة)</option>
                                <option value="gerboui">{{ __('gerboui') }} – Gerboui (شمال غربي – متوازن، إنتاج محدود)</option>
                                <option value="sayali">{{ __('sayali') }} – Sayali (شمال – طري، ذهبي)</option>
                                <option value="sahli">{{ __('sahli') }} – Sahli (ساحلي – إنتاج وفير، متأقلم)</option>
                                <option value="fakhari">{{ __('fakhari') }} – Fakhari (فخاري – زيت كثيف ونكهة مميزة)</option>
                                <option value="tounsi">{{ __('tounsi') }} – Tounsi (تونسي – صنف تقليدي)</option>
                                <option value="neb_jmel">{{ __('neb_jmel') }} – Neb Jmel (ناب الجمل – ثمرة طويلة للمائدة)</option>
                                <option value="rkhami">{{ __('rkhami') }} – Rkhami (رخامي – إنتاج مزدوج)</option>
                            </optgroup>
                            
                            <!-- أصناف دخيلة -->
                            <optgroup label="🌍 أصناف دخيلة (مستوردة) – Imported Varieties">
                                <option value="arbequina">{{ __('arbequina') }} – Arbequina (إسباني – خفيف، فاكهي)</option>
                                <option value="arbosana">{{ __('arbosana') }} – Arbosana (إسباني – عطري، مرّ خفيف)</option>
                                <option value="koroneiki">{{ __('koroneiki') }} – Koroneiki (يوناني – مركز، مرّ، قوي)</option>
                                <option value="picholine">{{ __('picholine') }} – Picholine (فرنسي – عشبي، متوسط الجودة)</option>
                            </optgroup>
                            
                            <!-- أصناف نادرة -->
                            <optgroup label="⭐ أصناف نادرة ومحلية – Rare & Special Varieties">
                                <option value="adefou">{{ __('adefou') }} – Adefou (أصلي، محدود الإنتاج)</option>
                                <option value="boudaoud">{{ __('boudaoud') }} – Boudaoud (تقليدي، عطري)</option>
                                <option value="fougi-gtar">{{ __('fougi-gtar') }} – Fougi Gtar (جبلي، زيت غامق)</option>
                            </optgroup>
                            
                            <!-- مزيج -->
                            <option value="blend">{{ __('blend') }} – Blend (مزيج أصناف متعددة)</option>
                        </select>

                        <!-- AI Variety Recognition Button -->
                        <div class="mt-6 p-4 bg-white/50 border-2 border-[#C8A356]/30 rounded-xl">
                            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                                <div class="flex items-center gap-3 w-full sm:w-auto">
                                    <img src="{{ asset('images/ezzitouni_bot.png') }}" class="w-10 h-10 rounded-full border border-[#C8A356] shadow-sm">
                                    <div class="text-right">
                                        <h4 class="font-bold text-sm text-gray-900">التعرف الذكي على الصنف</h4>
                                        <p class="text-xs text-gray-600">التقط صورة لورقة الزيتون أو حبة الزيتون</p>
                                    </div>
                                </div>
                                <div class="w-full sm:w-auto text-center">
                                    <input type="file" id="variety_image" accept="image/*" @change="analyzeVarietyImage($event)" class="hidden">
                                    <label for="variety_image" class="cursor-pointer w-full flex items-center justify-center px-4 py-2 bg-[#6A8F3B] text-white rounded-lg hover:bg-[#5a7a2f] transition text-sm font-bold shadow-md">
                                        <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                                        تحليل الصورة
                                    </label>
                                </div>
                            </div>
                            
                            <div x-show="isAnalyzingVariety" class="mt-3 flex items-center justify-center p-2 text-sm text-[#6A8F3B]" style="display: none;">
                                <svg class="animate-spin h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                جاري تحليل الصنف...
                            </div>
                        </div>
                        
                        <p class="mt-4 text-sm text-gray-600">
                            💡 <strong>ملاحظة:</strong> اختر الصنف الأساسي لمنتجك. إذا كان مزيج من عدة أصناف، اختر "مزيج".
                        </p>
                    </div>

                    <!-- Quality (Only for Oil) -->
                    <div x-show="formData.category === 'oil'" class="mt-6 bg-gradient-to-br from-[#F8F4EC] to-[#EEF5E9] rounded-2xl p-6" x-cloak>
                        <label class="block text-lg font-bold text-[#1B2A1B] mb-4">الجودة / Quality (اختياري)</label>
                        <select x-model="formData.quality"
                                class="w-full text-xl rounded-xl border-2 border-gray-300 px-6 py-4 bg-white focus:ring-4 focus:ring-[#6A8F3B] focus:border-[#6A8F3B] transition">
                            <option value="">— غير محدد / Unspecified —</option>
                            <option value="بكر ممتاز (EVOO)">بكر ممتاز (Extra Virgin - EVOO)</option>
                            <option value="بكر (Virgin)">بكر (Virgin)</option>
                            <option value="بيولوجي (Organic)">زيت زيتون بيولوجي (Organic)</option>
                            <option value="فيتورة (Pomace)">زيت فيتورة (Pomace)</option>
                        </select>
                    </div>

                    <!-- Packaging / Condition -->
                    <div class="mt-6 bg-gradient-to-br from-[#F8F4EC] to-[#EEF5E9] rounded-2xl p-6">
                        <label class="block text-lg font-bold text-[#1B2A1B] mb-4">حالة التعبئة / Packaging</label>
                        <select x-model="formData.packaging" required
                                class="w-full text-xl rounded-xl border-2 border-gray-300 px-6 py-4 bg-white focus:ring-4 focus:ring-[#6A8F3B] focus:border-[#6A8F3B] transition">
                            <option value="">— اختر حالة التعبئة —</option>
                            <option value="جملة / صب (Vrac)">جملة / صب (Bulk / Vrac)</option>
                            <option value="معلّب (Packaged)">معلّب (Packaged / Conditionné)</option>
                        </select>
                    </div>

                    <!-- AI Smart Yield Estimator -->
                    <div x-show="formData.category === 'olive'" class="mt-8 bg-gradient-to-br from-[#1B2A1B] to-[#6A8F3B] rounded-2xl p-6 shadow-xl text-white relative overflow-hidden" x-cloak>
                        <!-- Decorative Background -->
                        <div class="absolute inset-0 opacity-5 mix-blend-overlay" style="background-image: url('{{ asset('images/ezzitouni_bot.png') }}'); background-repeat: no-repeat; background-position: left top; background-size: 200px;"></div>
                        
                        <div class="relative z-10 flex flex-col items-center text-center">
                            <!-- Ezzitouni Bot Avatar -->
                            <div class="w-24 h-24 mb-4 rounded-full border-4 border-[#C8A356] shadow-xl overflow-hidden bg-white transform hover:scale-110 transition duration-300">
                                <img src="{{ asset('images/ezzitouni_bot.png') }}" alt="Ezzitouni Bot" class="w-full h-full object-cover">
                            </div>
                            
                            <h3 class="text-2xl font-bold mb-3">
                                التقدير الذكي لنسبة الزيت (حصري) مع Ezzitouni Bot
                            </h3>
                            <p class="text-white/90 mb-6 text-lg max-w-2xl">هل تريد إقناع المشترين بجودة زيتونك؟ التقط صورة لزيتونة مهروسة بين أصابعك ودع صديقك الذكي "Ezzitouni Bot" يقدر نسبة الزيت!</p>
                            
                            <div class="flex flex-col sm:flex-row justify-center items-center gap-4 w-full">
                                <div class="w-full sm:w-auto">
                                    <input type="file" id="smashed_olive_image" accept="image/*" @change="analyzeOliveImage($event)" class="hidden">
                                    <label for="smashed_olive_image" class="cursor-pointer w-full flex items-center justify-center px-6 py-4 bg-white text-[#1B2A1B] rounded-xl hover:bg-gray-100 transition shadow-lg font-bold">
                                        <svg class="w-6 h-6 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        التقط صورة للاختبار
                                    </label>
                                </div>
                                
                                <div x-show="isAnalyzingOlive" class="flex-1 w-full flex items-center justify-center p-4 bg-white/10 rounded-xl backdrop-blur-sm border border-white/20" x-cloak>
                                    <svg class="animate-spin h-6 w-6 text-white ml-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    <span class="font-bold">جاري تحليل الصورة بالذكاء الاصطناعي...</span>
                                </div>
                                
                                <div x-show="formData.estimated_oil_yield" class="flex-1 w-full flex items-center justify-center p-4 bg-white text-[#1B2A1B] border-4 border-[#C8A356] rounded-xl shadow-2xl transform scale-105 transition-all" x-cloak>
                                    <span class="font-black text-xl">النسبة المتوقعة: <span x-text="formData.estimated_oil_yield" class="text-[#6A8F3B] text-2xl"></span>%</span>
                                    <button type="button" @click="formData.estimated_oil_yield = null; document.getElementById('smashed_olive_image').value='';" class="mr-4 text-red-500 hover:text-red-700 bg-red-50 p-2 rounded-full transition hover:bg-red-100">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Quantity & Unit -->
                <div x-show="currentStep === 3" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-10" x-transition:enter-end="opacity-100 transform translate-x-0">
                    <h2 class="text-3xl font-bold text-[#1B2A1B] mb-2">كم الكمية المتوفرة؟</h2>
                    <p class="text-gray-600 mb-8">حدد الكمية المتاحة للبيع والوحدة</p>
                    
                    <div class="space-y-6">
                        <div>
                            <label class="block text-lg font-semibold text-[#1B2A1B] mb-3">الكمية المتاحة</label>
                            <input type="number" x-model="formData.quantity" step="0.01" min="0" required
                                class="w-full text-2xl font-bold rounded-xl border-2 border-gray-300 px-6 py-4 focus:ring-4 focus:ring-[#6A8F3B] focus:border-[#6A8F3B] transition"
                                placeholder="مثال: 500">
                        </div>

                        <div>
                            <label class="block text-lg font-semibold text-[#1B2A1B] mb-3">الوحدة</label>
                            <div class="grid grid-cols-2 gap-3">
                                <button type="button" @click="formData.unit = 'kg'"
                                    :class="formData.unit === 'kg' ? 'bg-[#6A8F3B] text-white' : 'bg-gray-100 text-gray-700'"
                                    class="p-4 rounded-xl font-bold transition-all hover:shadow-lg">
                                    كيلوغرام (kg)
                                </button>
                                <button type="button" @click="formData.unit = 'ton'"
                                    :class="formData.unit === 'ton' ? 'bg-[#6A8F3B] text-white' : 'bg-gray-100 text-gray-700'"
                                    class="p-4 rounded-xl font-bold transition-all hover:shadow-lg">
                                    طن (ton)
                                </button>
                                <button type="button" @click="formData.unit = 'liter'" x-show="formData.category === 'oil'"
                                    :class="formData.unit === 'liter' ? 'bg-[#6A8F3B] text-white' : 'bg-gray-100 text-gray-700'"
                                    class="p-4 rounded-xl font-bold transition-all hover:shadow-lg">
                                    لتر (liter)
                                </button>
                                <button type="button" @click="formData.unit = 'bottle'" x-show="formData.category === 'oil'"
                                    :class="formData.unit === 'bottle' ? 'bg-[#6A8F3B] text-white' : 'bg-gray-100 text-gray-700'"
                                    class="p-4 rounded-xl font-bold transition-all hover:shadow-lg">
                                    زجاجة (bottle)
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Pricing -->
                <div x-show="currentStep === 4" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-10" x-transition:enter-end="opacity-100 transform translate-x-0">
                    <h2 class="text-3xl font-bold text-[#1B2A1B] mb-2">ما هو السعر؟</h2>
                    <p class="text-gray-600 mb-8">حدد سعر البيع للوحدة الواحدة</p>
                    
                    <div class="space-y-6">
                        <!-- Price On Request Toggle -->
                        <label class="flex items-center bg-gray-50 border-2 border-gray-200 rounded-xl p-4 hover:border-[#6A8F3B] transition cursor-pointer">
                            <div class="relative">
                                <input type="checkbox" x-model="formData.price_on_request" class="sr-only">
                                <div class="block bg-gray-300 w-14 h-8 rounded-full transition" :class="{'bg-[#6A8F3B]': formData.price_on_request}"></div>
                                <div class="dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition" :class="{'transform translate-x-6': formData.price_on_request}"></div>
                            </div>
                            <div class="ml-4 mr-4">
                                <h4 class="font-bold text-[#1B2A1B] text-lg">السعر قابل للتفاوض / حسب الكمية والوجهة</h4>
                                <p class="text-gray-500 text-sm">سيتم عرض "السعر عند الطلب" للمشترين ولن يتم إلزامك بتحديد سعر ثابت الآن.</p>
                            </div>
                        </label>

                        <div x-show="!formData.price_on_request" x-transition>
                            <label class="block text-lg font-semibold text-[#1B2A1B] mb-3">السعر لكل <span x-text="formData.unit || 'وحدة'"></span></label>
                            <div class="relative">
                                <input type="number" x-model="formData.price" step="0.01" min="0" :required="!formData.price_on_request"
                                    class="w-full text-3xl font-bold rounded-xl border-2 border-gray-300 px-6 py-4 pr-24 focus:ring-4 focus:ring-[#6A8F3B] focus:border-[#6A8F3B] transition text-right"
                                    placeholder="مثال: 2.50">
                                <span class="absolute left-6 top-1/2 transform -translate-y-1/2 text-2xl font-bold text-gray-400" x-text="formData.currency"></span>
                            </div>
                        </div>

                        <div x-show="!formData.price_on_request" x-transition>
                            <label class="block text-lg font-semibold text-[#1B2A1B] mb-3">العملة</label>
                            <div class="grid grid-cols-3 gap-3">
                                <button type="button" @click="formData.currency = 'TND'"
                                    :class="formData.currency === 'TND' ? 'bg-[#C8A356] text-white' : 'bg-gray-100 text-gray-700'"
                                    class="p-4 rounded-xl font-bold transition-all hover:shadow-lg">
                                    دينار 🇹🇳
                                </button>
                                <button type="button" @click="formData.currency = 'USD'"
                                    :class="formData.currency === 'USD' ? 'bg-[#C8A356] text-white' : 'bg-gray-100 text-gray-700'"
                                    class="p-4 rounded-xl font-bold transition-all hover:shadow-lg">
                                    دولار 🇺🇸
                                </button>
                                <button type="button" @click="formData.currency = 'EUR'"
                                    :class="formData.currency === 'EUR' ? 'bg-[#C8A356] text-white' : 'bg-gray-100 text-gray-700'"
                                    class="p-4 rounded-xl font-bold transition-all hover:shadow-lg">
                                    يورو 🇪🇺
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-lg font-semibold text-[#1B2A1B] mb-3">الحد الأدنى للطلب (اختياري) <span x-show="formData.unit" class="text-sm font-normal text-gray-500">(بـ <span x-text="formData.unit"></span>)</span></label>
                            <input type="number" x-model="formData.min_order" step="any" min="0" :max="formData.quantity"
                                class="w-full text-xl rounded-xl border-2 border-gray-300 px-6 py-3 focus:ring-4 focus:ring-[#6A8F3B] focus:border-[#6A8F3B] transition"
                                placeholder="اتركه فارغاً إذا لم يكن هناك حد أدنى">
                            <p x-show="formData.quantity" class="text-xs text-gray-500 mt-1">الكمية الإجمالية للمنتج: <span x-text="parseFloat(formData.quantity)"></span> <span x-text="formData.unit"></span></p>
                        </div>
                    </div>
                </div>

                <!-- Step 5: Payment Methods -->
                <div x-show="currentStep === 5" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-10" x-transition:enter-end="opacity-100 transform translate-x-0">
                    <h2 class="text-3xl font-bold text-[#1B2A1B] mb-2">كيف تريد الدفع؟</h2>
                    <p class="text-gray-600 mb-8">اختر طرق الدفع المقبولة (يمكنك اختيار أكثر من طريقة)</p>
                    
                    <div class="space-y-3">
                        <button type="button" @click="togglePaymentMethod('cash')"
                            :class="formData.payment_methods.includes('cash') ? 'bg-[#6A8F3B] text-white border-[#6A8F3B]' : 'bg-white text-gray-700 border-gray-200'"
                            class="w-full border-2 rounded-xl p-5 transition-all text-right flex items-center justify-between hover:shadow-lg">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center ml-4">
                                    <span class="text-2xl">💵</span>
                                </div>
                                <div>
                                    <h4 class="font-bold text-lg">نقداً</h4>
                                    <p class="text-sm opacity-80">الدفع نقداً عند الاستلام</p>
                                </div>
                            </div>
                            <input type="checkbox" :checked="formData.payment_methods.includes('cash')" class="w-6 h-6 rounded">
                        </button>

                        <button type="button" @click="togglePaymentMethod('bank_transfer')"
                            :class="formData.payment_methods.includes('bank_transfer') ? 'bg-[#6A8F3B] text-white border-[#6A8F3B]' : 'bg-white text-gray-700 border-gray-200'"
                            class="w-full border-2 rounded-xl p-5 transition-all text-right flex items-center justify-between hover:shadow-lg">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center ml-4">
                                    <span class="text-2xl">🏦</span>
                                </div>
                                <div>
                                    <h4 class="font-bold text-lg">تحويل بنكي</h4>
                                    <p class="text-sm opacity-80">تحويل مباشر إلى الحساب البنكي</p>
                                </div>
                            </div>
                            <input type="checkbox" :checked="formData.payment_methods.includes('bank_transfer')" class="w-6 h-6 rounded">
                        </button>

                        <button type="button" @click="togglePaymentMethod('check')"
                            :class="formData.payment_methods.includes('check') ? 'bg-[#6A8F3B] text-white border-[#6A8F3B]' : 'bg-white text-gray-700 border-gray-200'"
                            class="w-full border-2 rounded-xl p-5 transition-all text-right flex items-center justify-between hover:shadow-lg">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center ml-4">
                                    <span class="text-2xl">📝</span>
                                </div>
                                <div>
                                    <h4 class="font-bold text-lg">شيك</h4>
                                    <p class="text-sm opacity-80">الدفع بواسطة شيك بنكي</p>
                                </div>
                            </div>
                            <input type="checkbox" :checked="formData.payment_methods.includes('check')" class="w-6 h-6 rounded">
                        </button>
                    </div>
                </div>

                <!-- Step 6: Delivery Options -->
                <div x-show="currentStep === 6" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-10" x-transition:enter-end="opacity-100 transform translate-x-0">
                    <h2 class="text-3xl font-bold text-[#1B2A1B] mb-2">كيف سيتم التسليم؟</h2>
                    <p class="text-gray-600 mb-8">اختر خيارات التسليم المتاحة (يمكنك اختيار أكثر من خيار)</p>
                    
                    <div class="space-y-3">
                        <button type="button" @click="toggleDeliveryOption('pickup')"
                            :class="formData.delivery_options.includes('pickup') ? 'bg-[#C8A356] text-white border-[#C8A356]' : 'bg-white text-gray-700 border-gray-200'"
                            class="w-full border-2 rounded-xl p-5 transition-all text-right flex items-center justify-between hover:shadow-lg">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center ml-4">
                                    <span class="text-2xl">📍</span>
                                </div>
                                <div>
                                    <h4 class="font-bold text-lg">استلام من الموقع</h4>
                                    <p class="text-sm opacity-80">المشتري يأتي للاستلام مباشرة</p>
                                </div>
                            </div>
                            <input type="checkbox" :checked="formData.delivery_options.includes('pickup')" class="w-6 h-6 rounded">
                        </button>

                        <button type="button" @click="toggleDeliveryOption('local_delivery')"
                            :class="formData.delivery_options.includes('local_delivery') ? 'bg-[#C8A356] text-white border-[#C8A356]' : 'bg-white text-gray-700 border-gray-200'"
                            class="w-full border-2 rounded-xl p-5 transition-all text-right flex items-center justify-between hover:shadow-lg">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center ml-4">
                                    <span class="text-2xl">🚚</span>
                                </div>
                                <div>
                                    <h4 class="font-bold text-lg">توصيل محلي</h4>
                                    <p class="text-sm opacity-80">التوصيل داخل المدينة أو المنطقة</p>
                                </div>
                            </div>
                            <input type="checkbox" :checked="formData.delivery_options.includes('local_delivery')" class="w-6 h-6 rounded">
                        </button>

                        <button type="button" @click="toggleDeliveryOption('export')"
                            :class="formData.delivery_options.includes('export') ? 'bg-[#C8A356] text-white border-[#C8A356]' : 'bg-white text-gray-700 border-gray-200'"
                            class="w-full border-2 rounded-xl p-5 transition-all text-right flex items-center justify-between hover:shadow-lg">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center ml-4">
                                    <span class="text-2xl">✈️</span>
                                </div>
                                <div>
                                    <h4 class="font-bold text-lg">تصدير دولي</h4>
                                    <p class="text-sm opacity-80">الشحن إلى خارج البلاد</p>
                                </div>
                            </div>
                            <input type="checkbox" :checked="formData.delivery_options.includes('export')" class="w-6 h-6 rounded">
                        </button>
                    </div>
                </div>

                <!-- Step 7: Location -->
                                <!-- Step 7: Location -->
                <div x-show="currentStep === 7" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-10" x-transition:enter-end="opacity-100 transform translate-x-0">
                    <h2 class="text-3xl font-bold text-[#1B2A1B] mb-2">الموقع الجغرافي</h2>
                    <p class="text-gray-600 mb-8">حدد موقع المنتج لمساعدة المشترين على إيجادك</p>

                    <div class="bg-gradient-to-br from-[#F8F4EC] to-[#EEF5E9] rounded-2xl p-6 space-y-6">
                        
                        <!-- GPS Location Button -->
                        <div>
                            <label class="block font-bold text-gray-900 mb-3">تحديد الموقع تلقائياً</label>
                            
                            <!-- Info Alert -->
                            <div class="mb-3 p-3 bg-blue-50 border-r-4 border-blue-500 rounded-lg">
                                <p class="text-sm text-blue-800">
                                    <span class="font-bold">ℹ️ ملاحظة:</span> سيطلب منك المتصفح الإذن للوصول إلى موقعك. الرجاء النقر على "السماح" أو "Allow" عندما يظهر التنبيه.
                                </p>
                            </div>
                            
                            <button type="button" @click="getCurrentLocation"
                                    class="w-full px-6 py-4 bg-gradient-to-r from-[#6A8F3B] to-[#5a7a2f] text-white rounded-xl hover:shadow-lg transition font-bold text-lg flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                حدد موقعي الحالي
                            </button>
                            
                            <!-- Success Message -->
                            <div x-show="locationSuccess" x-transition
                                 class="mt-3 p-4 bg-green-50 border-2 border-green-500 rounded-lg text-green-700 font-bold">
                                ✓ تم تحديد الموقع الجغرافي بنجاح
                            </div>
                            
                            <!-- Error Message with Instructions -->
                            <div x-show="locationError" x-transition
                                 class="mt-3 p-4 bg-red-50 border-2 border-red-500 rounded-lg text-red-700">
                                <p class="font-bold mb-2">⚠️ خطأ في تحديد الموقع</p>
                                <p x-text="locationError" class="text-sm mb-3"></p>
                                
                                <!-- Instructions for allowing location access -->
                                <div class="mt-3 p-3 bg-white rounded-lg text-gray-700 text-sm space-y-2">
                                    <p class="font-bold text-gray-900">� كيفية السماح بالوصول إلى الموقع:</p>
                                    <ul class="list-disc list-inside space-y-1 mr-4">
                                        <li><strong>Chrome/Edge:</strong> انقر على أيقونة القفل 🔒 بجانب رابط الموقع في شريط العناوين، ثم اختر "السماح" للموقع</li>
                                        <li><strong>Firefox:</strong> انقر على أيقونة الموقع في شريط العناوين، ثم قم بتفعيل "الوصول إلى الموقع"</li>
                                        <li><strong>Safari:</strong> من قائمة Safari، اختر التفضيلات > المواقع > خدمات الموقع، ثم اسمح لهذا الموقع</li>
                                        <li><strong>الهاتف المحمول:</strong> من إعدادات المتصفح أو إعدادات الجهاز، قم بتفعيل إذن الموقع للمتصفح</li>
                                    </ul>
                                    <p class="mt-2 text-blue-600">💡 بعد السماح، انقر على زر "حدد موقعي الحالي" مرة أخرى</p>
                                    <p class="mt-2 text-gray-600">أو يمكنك إدخال الإحداثيات يدوياً أدناه ⬇️</p>
                                </div>
                                
                                <!-- Retry and Close Buttons -->
                                <div class="mt-3 flex gap-2">
                                    <button type="button" @click="getCurrentLocation"
                                            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-bold">
                                        🔄 حاول مرة أخرى
                                    </button>
                                    <button type="button" @click="locationError = ''"
                                            class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition text-sm">
                                        إغلاق
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="h-px bg-gray-300 my-6"></div>

                        <!-- Location Description -->
                        <div>
                            <label class="block font-bold text-gray-900 mb-2">وصف الموقع (اختياري)</label>
                            <input type="text" x-model="formData.location_text"
                                   placeholder="مثال: مزرعة الزيتون - طريق صفاقس"
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-[#6A8F3B] focus:outline-none text-lg">
                        </div>

                        <!-- Manual Coordinates -->
                        <div :class="locationSuccess ? 'border-2 border-green-500 rounded-xl p-4' : ''">
                            <label class="block font-bold text-gray-900 mb-3">الإحداثيات الجغرافية (اختياري)</label>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm text-gray-700 mb-1">خط العرض (Latitude)</label>
                                    <input type="number" step="0.000001"
                                           x-model="formData.latitude"
                                           placeholder="33.8869"
                                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-[#6A8F3B] focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-700 mb-1">خط الطول (Longitude)</label>
                                    <input type="number" step="0.000001"
                                           x-model="formData.longitude"
                                           placeholder="10.1815"
                                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-[#6A8F3B] focus:outline-none">
                                </div>
                            </div>
                            <p class="mt-3 text-sm text-gray-600">
                                💡 يمكنك الحصول على الإحداثيات من خرائط جوجل بالنقر بزر الماوس الأيمن على الموقع
                            </p>
                        </div>

                        <!-- Governorate & Delegation -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block font-bold text-gray-900 mb-2">الولاية</label>
                                <select x-model="formData.governorate"
                                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-[#6A8F3B] focus:outline-none text-lg">
                                    <option value="">اختر الولاية</option>
                                    <option value="تونس">تونس</option>
                                    <option value="أريانة">أريانة</option>
                                    <option value="بن عروس">بن عروس</option>
                                    <option value="منوبة">منوبة</option>
                                    <option value="نابل">نابل</option>
                                    <option value="زغوان">زغوان</option>
                                    <option value="بنزرت">بنزرت</option>
                                    <option value="باجة">باجة</option>
                                    <option value="جندوبة">جندوبة</option>
                                    <option value="الكاف">الكاف</option>
                                    <option value="سليانة">سليانة</option>
                                    <option value="القيروان">القيروان</option>
                                    <option value="القصرين">القصرين</option>
                                    <option value="سيدي بوزيد">سيدي بوزيد</option>
                                    <option value="صفاقس">صفاقس</option>
                                    <option value="قفصة">قفصة</option>
                                    <option value="توزر">توزر</option>
                                    <option value="قبلي">قبلي</option>
                                    <option value="مدنين">مدنين</option>
                                    <option value="تطاوين">تطاوين</option>
                                    <option value="قابس">قابس</option>
                                    <option value="المنستير">المنستير</option>
                                    <option value="المهدية">المهدية</option>
                                    <option value="سوسة">سوسة</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-bold text-gray-900 mb-2">المعتمدية (اختياري)</label>
                                <input type="text" x-model="formData.delegation"
                                       placeholder="مثال: حمام الأنف"
                                       class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-[#6A8F3B] focus:outline-none text-lg">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 8: Images Upload -->
                <div x-show="currentStep === 8" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-10" x-transition:enter-end="opacity-100 transform translate-x-0">
                    <h2 class="text-3xl font-bold text-[#1B2A1B] mb-2">📸 صور المنتج</h2>
                    <p class="text-gray-600 mb-8">أضف صور واضحة لمنتجك (اختياري)</p>
                    
                    <div class="bg-gray-50 rounded-2xl p-6 border-2 border-dashed border-gray-300 hover:border-[#6A8F3B] transition-colors">
                        <div class="text-center mb-4">
                            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <label for="images" class="cursor-pointer">
                                <span class="text-lg font-bold text-[#6A8F3B] hover:text-[#5a7a2f]">اختر الصور</span>
                                <span class="text-gray-600"> أو اسحبها هنا</span>
                            </label>
                            <input type="file" id="images" name="images[]" multiple accept="image/*"
                                   @change="
                                       const files = Array.from($event.target.files);
                                       if (files.length > 5) { showToast('يمكنك رفع 5 صور كحد أقصى', 'error'); return; }
                                       formData.images = files;
                                       formData.imagePreview = [];
                                       files.forEach((file, idx) => {
                                           const reader = new FileReader();
                                           reader.onload = (e) => { formData.imagePreview.push(e.target.result); };
                                           reader.readAsDataURL(file);
                                       });
                                   "
                                   class="hidden">
                        </div>
                        
                        <!-- Preview Selected Images -->
                        <div x-show="formData.imagePreview && formData.imagePreview.length > 0" class="grid grid-cols-3 gap-4 mt-4">
                            <template x-for="(image, index) in formData.imagePreview" :key="index">
                                <div class="relative group">
                                    <img :src="image" class="w-full h-32 object-cover rounded-lg border-2 border-gray-200">
                                    <button type="button" 
                                            @click="
                                                formData.imagePreview.splice(index, 1);
                                                const dt = new DataTransfer();
                                                const fileArray = Array.from(formData.images);
                                                fileArray.splice(index, 1);
                                                fileArray.forEach(f => dt.items.add(f));
                                                $el.closest('form').querySelector('#images').files = dt.files;
                                                formData.images = Array.from(dt.files);
                                            "
                                            class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                        
                        <p class="text-sm text-gray-500 mt-4 text-center">
                            يمكنك رفع عدة صور (الحد الأقصى 5 صور، كل صورة بحجم أقصى 2MB)
                        </p>
                    </div>
                </div>

                <!-- Step 9: Review & Confirm -->
                <div x-show="currentStep === 9" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-10" x-transition:enter-end="opacity-100 transform translate-x-0">
                    <h2 class="text-3xl font-bold text-[#1B2A1B] mb-2">مراجعة نهائية</h2>
                    <p class="text-gray-600 mb-8">تأكد من صحة جميع المعلومات قبل النشر</p>
                    
                    <div class="bg-gradient-to-br from-[#F8F4EC] to-[#EEF5E9] rounded-2xl p-6 space-y-4">
                        <div class="flex justify-between items-center py-3 border-b border-gray-300">
                            <span class="text-gray-600">نوع المنتج</span>
                            <span class="font-bold" x-text="formData.category === 'olive' ? '🫒 {{ __('Olives') }}' : '🫗 {{ __('Olive Oil') }}'"></span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-gray-300">
                            <span class="text-gray-600">الصنف / Variety</span>
                            <span class="font-bold" x-text="formData.variety || 'غير محدد'"></span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-gray-300">
                            <span class="text-gray-600">الكمية</span>
                            <span class="font-bold"><span x-text="formData.quantity"></span> <span x-text="formData.unit"></span></span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-gray-300">
                            <span class="text-gray-600">السعر</span>
                            <span class="font-bold text-2xl text-[#6A8F3B]"><span x-text="formData.price"></span> <span x-text="formData.currency"></span></span>
                        </div>
                        <div x-show="formData.min_order" class="flex justify-between items-center py-3 border-b border-gray-300">
                            <span class="text-gray-600">الحد الأدنى للطلب</span>
                            <span class="font-bold"><span x-text="formData.min_order"></span> <span x-text="formData.unit"></span></span>
                        </div>
                        <div class="flex justify-between items-start py-3 border-b border-gray-300">
                            <span class="text-gray-600">طرق الدفع</span>
                            <span class="font-bold text-left" x-text="formData.payment_methods.length > 0 ? formData.payment_methods.join('، ') : 'غير محدد'"></span>
                        </div>
                        <div class="flex justify-between items-start py-3 border-b border-gray-300">
                            <span class="text-gray-600">التسليم</span>
                            <span class="font-bold text-left" x-text="formData.delivery_options.length > 0 ? formData.delivery_options.join('، ') : 'غير محدد'"></span>
                        </div>
                        <div x-show="formData.location_text || formData.governorate" class="flex justify-between items-start py-3 border-b border-gray-300">
                            <span class="text-gray-600 flex items-center gap-2">
                                <svg class="w-5 h-5 text-[#6A8F3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                </svg>
                                الموقع
                            </span>
                            <span class="font-bold text-left">
                                <span x-show="formData.governorate" x-text="formData.governorate + (formData.delegation ? ', ' + formData.delegation : '')"></span>
                                <span x-show="formData.location_text" x-text="formData.location_text" class="block text-sm text-gray-600"></span>
                                <span x-show="formData.latitude && formData.longitude" class="block text-xs text-green-600 mt-1">
                                    ✓ موقع GPS محدد
                                </span>
                            </span>
                        </div>
                    </div>

                    <div class="mt-6 flex items-start">
                        <input type="checkbox" id="agree" required class="mt-1 w-5 h-5 rounded text-[#6A8F3B]">
                        <label for="agree" class="mr-3 text-gray-700">
                            أوافق على <a href="#" class="text-[#6A8F3B] underline">شروط وأحكام</a> المنصة وأقر بأن جميع المعلومات المقدمة صحيحة
                        </label>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
                    <!-- Previous Button -->
                    <button type="button" @click="if (currentStep > 1) { currentStep--; window.scrollTo({ top: 0, behavior: 'smooth' }); }" x-show="currentStep > 1"
                        class="px-8 py-4 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition font-bold text-lg flex items-center">
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        السابق
                    </button>
                    
                    <!-- Spacer for step 1 -->
                    <div x-show="currentStep === 1"></div>

                    <!-- Next Button (Steps 1-8) -->
                    <button type="button" 
                        @click="
                            let valid = true;
                            if (currentStep === 1 && !formData.category) { showToast('الرجاء اختيار نوع المنتج', 'error'); valid = false; }
                            else if (currentStep === 2 && !formData.variety) { showToast('الرجاء اختيار الصنف', 'error'); valid = false; }
                            else if (currentStep === 3 && (!formData.quantity || formData.quantity <= 0)) { showToast('الرجاء إدخال الكمية', 'error'); valid = false; }
                            else if (currentStep === 3 && !formData.unit) { showToast('الرجاء اختيار الوحدة', 'error'); valid = false; }
                            else if (currentStep === 4 && (!formData.price || formData.price <= 0)) { showToast('الرجاء إدخال السعر', 'error'); valid = false; }
                            else if (currentStep === 4 && formData.min_order && parseFloat(formData.min_order) > parseFloat(formData.quantity)) { showToast('أدنى كمية للطلب لا يمكن أن تكون أكبر من الكمية الإجمالية للمنتج (' + parseFloat(formData.quantity) + ' ' + (formData.unit || '') + ')', 'error'); valid = false; }
                            else if (currentStep === 5 && formData.payment_methods.length === 0) { showToast('الرجاء اختيار طريقة دفع واحدة على الأقل', 'error'); valid = false; }
                            else if (currentStep === 6 && formData.delivery_options.length === 0) { showToast('الرجاء اختيار خيار تسليم واحد على الأقل', 'error'); valid = false; }
                            else if (currentStep === 7 && !formData.governorate && !formData.location_text) { showToast('الرجاء إدخال الموقع أو اختيار الولاية', 'error'); valid = false; }
                            if (valid && currentStep < 9) { currentStep++; window.scrollTo({ top: 0, behavior: 'smooth' }); }
                        " 
                        x-show="currentStep < 9"
                        class="px-8 py-4 bg-gradient-to-r from-[#6A8F3B] to-[#5a7a2f] text-white rounded-xl hover:shadow-xl transition font-bold text-lg flex items-center">
                        التالي
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>

                    <!-- Submit Button (Step 9 only) -->
                    <button type="submit" x-show="currentStep === 9" :disabled="isSubmitting"
                        :class="isSubmitting ? 'opacity-50 cursor-not-allowed' : 'hover:shadow-2xl hover:scale-105'"
                        class="px-10 py-4 bg-gradient-to-r from-[#1B2A1B] to-[#6A8F3B] text-white rounded-xl transition font-bold text-xl flex items-center transform">
                        <svg x-show="!isSubmitting" class="w-6 h-6 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <svg x-show="isSubmitting" class="animate-spin h-6 w-6 ml-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="isSubmitting ? 'جاري النشر...' : 'نشر العرض 🚀'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('wizardForm', () => ({
        currentStep: 1,
        totalSteps: 8,
        isSubmitting: false,
        isAnalyzingOlive: false,
        isAnalyzingVariety: false,
        errorMessage: '',
        formData: {
            category: '',
            variety: '',
            quality: '',
            packaging: '',
            quantity: '',
            unit: 'kg',
            price: '',
            price_on_request: false,
            currency: 'TND',
            min_order: '',
            payment_methods: [],
            delivery_options: [],
            location_text: '',
            latitude: '',
            longitude: '',
            governorate: '',
            delegation: '',
            estimated_oil_yield: null
        },
        locationError: '',
        locationSuccess: false,
        
        get stepTitle() {
            const titles = {
                1: 'الخطوة 1: نوع المنتج',
                2: 'الخطوة 2: اختيار المنتج',
                3: 'الخطوة 3: الكمية',
                4: 'الخطوة 4: التسعير',
                5: 'الخطوة 5: طرق الدفع',
                6: 'الخطوة 6: التسليم',
                7: 'الخطوة 7: الموقع',
                8: 'الخطوة 8: المراجعة النهائية'
            };
            return titles[this.currentStep] || '';
        },
        
        selectCategory(category) {
            this.formData.category = category;
            this.formData.variety = '';
            if (category !== 'olive') {
                this.formData.estimated_oil_yield = null;
            }
        },

        async compressImage(file, maxWidth = 800) {
            return new Promise((resolve) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const img = new Image();
                    img.onload = () => {
                        const canvas = document.createElement('canvas');
                        let width = img.width;
                        let height = img.height;
                        
                        if (width > maxWidth) {
                            height = Math.round((height * maxWidth) / width);
                            width = maxWidth;
                        }
                        
                        canvas.width = width;
                        canvas.height = height;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);
                        
                        canvas.toBlob((blob) => {
                            resolve(new File([blob], file.name, {
                                type: 'image/jpeg',
                                lastModified: Date.now()
                            }));
                        }, 'image/jpeg', 0.8);
                    };
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);
            });
        },
        
        
        async analyzeVarietyImage(event) {
            const file = event.target.files[0];
            if (!file) return;
            
            this.isAnalyzingVariety = true;
            
            try {
                const compressedFile = await this.compressImage(file);
                const data = new FormData();
                data.append('image', compressedFile);
                
                const response = await fetch('/api/v1/ai/variety-detect', {
                    method: 'POST',
                    headers: { 'Accept': 'application/json' },
                    body: data
                });
                
                if (!response.ok) throw new Error('فشل التحليل.');
                
                const result = await response.json();
                if (result.success && result.detected_variety) {
                    // Extract variety value from localized string, e.g. "شتوي (Chétoui)" -> "chetoui"
                    const varietyString = result.detected_variety.toLowerCase();
                    if (varietyString.includes('chemlali')) this.formData.variety = 'chemlali';
                    else if (varietyString.includes('chetoui')) this.formData.variety = 'chetoui';
                    else if (varietyString.includes('oueslati')) this.formData.variety = 'oueslati';
                    else if (varietyString.includes('zalmati')) this.formData.variety = 'zalmati';
                    else if (varietyString.includes('zarrazi')) this.formData.variety = 'zarrazi';
                    else if (varietyString.includes('barouni')) this.formData.variety = 'barouni';
                    else if (varietyString.includes('meski')) this.formData.variety = 'meski';
                    else this.formData.variety = 'other';
                    
                    // Show a toast or small alert
                    showToast(`Ezzitouni Bot 🤖: تم التعرف على الصنف: ${result.detected_variety}`, 'success');
                } else {
                    throw new Error(result.message || 'حدث خطأ غير متوقع');
                }
            } catch (error) {
                showToast('عذراً، لم نتمكن من تحليل الصورة: ' + error.message, 'error');
            } finally {
                this.isAnalyzingVariety = false;
                event.target.value = ''; // Reset input
            }
        },

        async analyzeOliveImage(event) {
            const file = event.target.files[0];
            if (!file) return;
            
            this.isAnalyzingOlive = true;
            this.formData.estimated_oil_yield = null;
            
            try {
                // Compress image client-side to easily pass the 2MB PHP limit
                const compressedFile = await this.compressImage(file);
                
                const data = new FormData();
                data.append('image', compressedFile);
                
                const response = await fetch('{{ url('/api/v1/ai/yield-estimate') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json'
                    },
                    body: data
                });
                
                if (!response.ok) {
                    if (response.status === 413) {
                        throw new Error('حجم الصورة كبير جداً (تجاوز الحد المسموح به للخادم).');
                    }
                    
                    if (response.status === 422) {
                        throw new Error('الصورة غير صالحة أو تجاوزت الحجم المسموح به (2 ميغابايت).');
                    }
                    
                    let errorMessage = 'فشل في الاتصال بخادم الذكاء الاصطناعي (رمز الخطأ: ' + response.status + ')';
                    try {
                        const errorData = await response.json();
                        if (errorData.message) errorMessage = errorData.message;
                    } catch (e) {
                        // JSON parsing failed, stick with default message
                    }
                    throw new Error(errorMessage);
                }
                
                const result = await response.json();
                if (result.success) {
                    this.formData.estimated_oil_yield = result.estimated_yield;
                } else {
                    showToast('عذراً، لم نتمكن من تحليل الصورة. ' + (result.message || ''), 'error');
                }
            } catch (error) {
                console.error(error);
                showToast(error.message || 'حدث خطأ أثناء الاتصال بخادم الذكاء الاصطناعي.', 'error');
            } finally {
                this.isAnalyzingOlive = false;
            }
        },
        
        getSelectedVarietyName() {
            return this.formData.variety || 'غير محدد';
        },
        
        togglePaymentMethod(method) {
            const index = this.formData.payment_methods.indexOf(method);
            if (index > -1) {
                this.formData.payment_methods.splice(index, 1);
            } else {
                this.formData.payment_methods.push(method);
            }
        },
        
        toggleDeliveryOption(option) {
            const index = this.formData.delivery_options.indexOf(option);
            if (index > -1) {
                this.formData.delivery_options.splice(index, 1);
            } else {
                this.formData.delivery_options.push(option);
            }
        },
        
        getSelectedVarietyName() {
            return this.formData.variety || 'غير محدد';
        },
        
        nextStep() {
            if (this.validateStep()) {
                if (this.currentStep < this.totalSteps) {
                    this.currentStep++;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            }
        },
        
        prevStep() {
            if (this.currentStep > 1) {
                this.currentStep--;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        },
        
        validateStep() {
            switch(this.currentStep) {
                case 1:
                    if (!this.formData.category) {
                        showToast('الرجاء اختيار نوع المنتج', 'error');
                        return false;
                    }
                    break;
                case 2:
                    if (!this.formData.variety) {
                        showToast('الرجاء اختيار الصنف', 'error');
                        return false;
                    }
                    break;
                case 3:
                    if (!this.formData.quantity || this.formData.quantity <= 0) {
                        showToast('الرجاء إدخال الكمية المتاحة', 'error');
                        return false;
                    }
                    if (!this.formData.unit) {
                        showToast('الرجاء اختيار الوحدة', 'error');
                        return false;
                    }
                    break;
                case 4:
                    if (!this.formData.price_on_request && (!this.formData.price || this.formData.price <= 0)) {
                        showToast('الرجاء إدخال السعر', 'error');
                        return false;
                    }
                    break;
                case 5:
                    if (this.formData.payment_methods.length === 0) {
                        showToast('الرجاء اختيار طريقة دفع واحدة على الأقل', 'error');
                        return false;
                    }
                    break;
                case 6:
                    if (this.formData.delivery_options.length === 0) {
                        showToast('الرجاء اختيار خيار تسليم واحد على الأقل', 'error');
                        return false;
                    }
                    break;
                case 7:
                    // Location validation - at least governorate is required
                    if (!this.formData.governorate && !this.formData.location_text) {
                        showToast('الرجاء إدخال الموقع أو اختيار الولاية على الأقل', 'error');
                        return false;
                    }
                    break;
                case 8:
                    // Final review - no specific validation needed
                    // Just make sure all previous steps are valid
                    return true;
            }
            return true;
        },
        
        getCurrentLocation(event) {
            this.locationError = '';
            this.locationSuccess = false;
            
            if (!navigator.geolocation) {
                this.locationError = 'المتصفح لا يدعم تحديد الموقع الجغرافي';
                return;
            }
            
            // Safe way to reference the button element even if clicking the SVG inside
            const button = event ? event.currentTarget : null;
            const originalHTML = button ? button.innerHTML : '';
            
            if (button) {
                button.disabled = true;
                button.innerHTML = '<svg class="animate-spin h-5 w-5 mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
            }

            const successCallback = (position) => {
                this.formData.latitude = position.coords.latitude.toFixed(6);
                this.formData.longitude = position.coords.longitude.toFixed(6);
                this.locationSuccess = true;
                this.locationError = '';
                if (button) {
                    button.disabled = false;
                    button.innerHTML = '✓ تم تحديد الموقع بنجاح';
                    setTimeout(() => {
                        button.innerHTML = originalHTML;
                    }, 2000);
                }
            };
            
            // First attempt: Try with High Accuracy (GPS)
            navigator.geolocation.getCurrentPosition(
                successCallback,
                (error) => {
                    console.warn('High accuracy location failed. Retrying with low accuracy...', error.message);
                    
                    // Fallback attempt 1: Try with Low Accuracy (Wi-Fi / Cellular)
                    navigator.geolocation.getCurrentPosition(
                        successCallback,
                        (fallbackError) => {
                            console.warn('Low accuracy location failed. Retrying with IP Geolocation...', fallbackError.message);
                            
                            // Fallback attempt 2 (Last resort): IP-based Geolocation via API
                            fetch('https://ipwho.is/')
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success && data.latitude && data.longitude) {
                                        this.formData.latitude = Number(data.latitude).toFixed(6);
                                        this.formData.longitude = Number(data.longitude).toFixed(6);
                                        this.locationSuccess = true;
                                        this.locationError = '';
                                        if (button) {
                                            button.disabled = false;
                                            button.innerHTML = '✓ تم تحديد الموقع بنجاح';
                                            setTimeout(() => {
                                                button.innerHTML = originalHTML;
                                            }, 2000);
                                        }
                                    } else {
                                        throw new Error('Invalid IP location data');
                                    }
                                })
                                .catch(ipError => {
                                    console.error('IP Geolocation failed:', ipError);
                                    if (button) {
                                        button.disabled = false;
                                        button.innerHTML = originalHTML;
                                    }
                                    
                                    switch(fallbackError.code) {
                                        case fallbackError.PERMISSION_DENIED:
                                            this.locationError = 'تم رفض الإذن بالوصول إلى الموقع. الرجاء السماح للمتصفح بالوصول إلى موقعك.';
                                            break;
                                        case fallbackError.POSITION_UNAVAILABLE:
                                            this.locationError = 'معلومات الموقع غير متوفرة.';
                                            break;
                                        case fallbackError.TIMEOUT:
                                            this.locationError = 'انتهت مهلة طلب الموقع.';
                                            break;
                                        default:
                                            this.locationError = 'حدث خطأ غير معروف في تحديد الموقع.';
                                    }
                                });
                        },
                        {
                            enableHighAccuracy: false,
                            timeout: 5000, // Timeout low accuracy after 5 seconds to switch to IP quickly
                            maximumAge: 60000
                        }
                    );
                },
                {
                    enableHighAccuracy: true,
                    timeout: 4000, // Timeout high accuracy after 4 seconds
                    maximumAge: 0
                }
            );
        },
        
        handleSubmit(event) {
            event.preventDefault();
            
            console.log('🚀 Form submission started');
            console.log('📝 Current step:', this.currentStep);
            console.log('📦 Form data:', this.formData);
            
            // Clear any previous errors
            this.errorMessage = '';
            
            // Validate the current step
            console.log('✅ Validating step', this.currentStep);
            if (!this.validateStep()) {
                console.error('❌ Validation failed for step', this.currentStep);
                this.errorMessage = 'الرجاء التأكد من ملء جميع الحقول المطلوبة';
                return;
            }
            
            console.log('✅ Validation passed!');
            
            // Validate required fields one more time
            if (!this.formData.variety) {
                console.error('❌ Variety is missing');
                this.errorMessage = 'الرجاء اختيار الصنف';
                return;
            }
            
            if (!this.formData.price) {
                console.error('❌ Price is missing');
                this.errorMessage = 'الرجاء إدخال السعر';
                return;
            }
            
            console.log('✅ All required fields are present');
            console.log('📤 Submitting form to server...');
            
            // Show loading state
            this.isSubmitting = true;
            
            // Submit the form
            try {
                event.target.submit();
                console.log('✅ Form submitted successfully!');
            } catch (error) {
                console.error('❌ Form submission error:', error);
                this.errorMessage = 'حدث خطأ أثناء إرسال النموذج. الرجاء المحاولة مرة أخرى.';
                this.isSubmitting = false;
            }
        }
    }));
});
</script>
@endsection
