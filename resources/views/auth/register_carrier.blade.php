<x-guest-layout>
    <div class="mx-auto max-w-3xl">
        <!-- Register Card -->
        <div class="bg-white rounded-2xl shadow-2xl p-8 border border-gray-100">
            <div class="text-center mb-6">
                <div class="flex justify-center mb-4">
                    <div class="w-20 h-20 rounded-full bg-gradient-to-br from-[#C8A356] to-[#b08a3c] flex items-center justify-center shadow-lg">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                        </svg>
                    </div>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">تسجيل كناقل</h2>
                <p class="text-gray-600">أكمل البيانات للانضمام كناقل في المنصة</p>
            </div>
        
            @if ($errors->any())
                <div class="bg-red-50 border-r-4 border-red-500 text-red-700 px-6 py-4 rounded-lg mb-6">
                    <div class="flex items-center gap-3 mb-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-bold">يرجى تصحيح الأخطاء التالية:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <input type="hidden" name="role" value="carrier">
                
                <!-- Personal Information Section -->
                <div class="border-b pb-6">
                    <h3 class="text-lg font-bold text-[#C8A356] mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        المعلومات الشخصية
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-gray-900 font-bold mb-2">الاسم الكامل <span class="text-red-600">*</span></label>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus 
                                class="w-full rounded-xl border-2 border-gray-200 px-4 py-3 bg-gray-50 text-gray-900 focus:border-[#C8A356] focus:ring-4 focus:ring-[#C8A356]/20 transition-all" 
                                placeholder="أدخل اسمك الكامل">
                            @error('name')
                                <p class="text-red-600 text-sm mt-2 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-gray-900 font-bold mb-2">البريد الإلكتروني <span class="text-red-600">*</span></label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required 
                                class="w-full rounded-xl border-2 border-gray-200 px-4 py-3 bg-gray-50 text-gray-900 focus:border-[#C8A356] focus:ring-4 focus:ring-[#C8A356]/20 transition-all" 
                                placeholder="example@email.com">
                            @error('email')
                                <p class="text-red-600 text-sm mt-2 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-gray-900 font-bold mb-2">رقم الهاتف <span class="text-red-600">*</span></label>
                            <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" required 
                                class="w-full rounded-xl border-2 border-gray-200 px-4 py-3 bg-gray-50 text-gray-900 focus:border-[#C8A356] focus:ring-4 focus:ring-[#C8A356]/20 transition-all" 
                                placeholder="+216 XX XXX XXX">
                            @error('phone')
                                <p class="text-red-600 text-sm mt-2 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-gray-900 font-bold mb-2">كلمة المرور <span class="text-red-600">*</span></label>
                            <input id="password" type="password" name="password" required 
                                class="w-full rounded-xl border-2 border-gray-200 px-4 py-3 bg-gray-50 text-gray-900 focus:border-[#C8A356] focus:ring-4 focus:ring-[#C8A356]/20 transition-all" 
                                placeholder="••••••••">
                            @error('password')
                                <p class="text-red-600 text-sm mt-2 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-gray-900 font-bold mb-2">تأكيد كلمة المرور <span class="text-red-600">*</span></label>
                            <input id="password_confirmation" type="password" name="password_confirmation" required 
                                class="w-full rounded-xl border-2 border-gray-200 px-4 py-3 bg-gray-50 text-gray-900 focus:border-[#C8A356] focus:ring-4 focus:ring-[#C8A356]/20 transition-all" 
                                placeholder="••••••••">
                        </div>
                    </div>
                </div>

                <!-- Carrier Information Section -->
                <div>
                    <h3 class="text-lg font-bold text-[#C8A356] mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                        </svg>
                        معلومات النقل
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="camion_capacity" class="block text-gray-900 font-bold mb-2">سعة الشاحنة (بالطن) <span class="text-red-600">*</span></label>
                            <input id="camion_capacity" type="number" step="0.1" name="camion_capacity" value="{{ old('camion_capacity') }}" required 
                                class="w-full rounded-xl border-2 border-gray-200 px-4 py-3 bg-gray-50 text-gray-900 focus:border-[#C8A356] focus:ring-4 focus:ring-[#C8A356]/20 transition-all" 
                                placeholder="مثال: 5، 10، 20">
                            @error('camion_capacity')
                                <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Profile Images Section -->
                <div x-data="{ 
                    profilePreview: null, 
                    coverPreviews: [],
                    handleProfileChange(event) {
                        const file = event.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                this.profilePreview = e.target.result;
                            };
                            reader.readAsDataURL(file);
                        }
                    },
                    handleCoverChange(event) {
                        const files = Array.from(event.target.files);
                        this.coverPreviews = [];
                        files.slice(0, 5).forEach(file => {
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                this.coverPreviews.push(e.target.result);
                            };
                            reader.readAsDataURL(file);
                        });
                    },
                    removeCover(index) {
                        this.coverPreviews.splice(index, 1);
                        const input = document.getElementById('cover_photos');
                        const dt = new DataTransfer();
                        const files = Array.from(input.files);
                        files.forEach((file, i) => {
                            if (i !== index) dt.items.add(file);
                        });
                        input.files = dt.files;
                    }
                }">
                    <h3 class="text-lg font-bold text-[#C8A356] mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        الصور (اختياري)
                    </h3>

                    <div class="space-y-6">
                        <!-- Profile Picture -->
                        <div>
                            <label class="block text-gray-900 font-bold mb-2">صورة الملف الشخصي</label>
                            <div class="flex flex-col sm:flex-row items-start gap-4">
                                <div class="flex-shrink-0">
                                    <template x-if="profilePreview">
                                        <img :src="profilePreview" class="w-32 h-32 rounded-full object-cover border-4 border-[#C8A356]/20 shadow-lg">
                                    </template>
                                    <template x-if="!profilePreview">
                                        <div class="w-32 h-32 rounded-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center border-4 border-gray-200">
                                            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                    </template>
                                </div>
                                <div class="flex-1">
                                    <input 
                                        type="file" 
                                        id="profile_picture" 
                                        name="profile_picture" 
                                        accept="image/*"
                                        @change="handleProfileChange($event)"
                                        class="hidden">
                                    <label for="profile_picture" 
                                        class="inline-flex items-center gap-2 px-6 py-3 bg-white border-2 border-[#C8A356] text-[#C8A356] rounded-xl cursor-pointer hover:bg-[#C8A356] hover:text-white transition-all shadow-md hover:shadow-lg">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span class="font-bold">اختر صورة</span>
                                    </label>
                                    <p class="text-sm text-gray-600 mt-2">
                                        أي صورة، أي حجم - سيتم تحسينها تلقائياً
                                    </p>
                                    @error('profile_picture')
                                        <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Cover Photos -->
                        <div>
                            <label class="block text-gray-900 font-bold mb-2">صور الغلاف (حتى 5 صور)</label>
                            <div class="space-y-4">
                                <div>
                                    <input 
                                        type="file" 
                                        id="cover_photos" 
                                        name="cover_photos[]" 
                                        accept="image/*"
                                        multiple
                                        @change="handleCoverChange($event)"
                                        class="hidden">
                                    <label for="cover_photos" 
                                        class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-[#C8A356] to-[#b08a3c] text-white rounded-xl cursor-pointer hover:shadow-lg transition-all shadow-md">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        <span class="font-bold">أضف صور الغلاف</span>
                                    </label>
                                    <p class="text-sm text-gray-600 mt-2">
                                        يمكنك اختيار حتى 5 صور. أي صورة، أي حجم - سيتم تحسينها تلقائياً لكل صورة
                                    </p>
                                    @error('cover_photos')
                                        <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                                    @enderror
                                    @error('cover_photos.*')
                                        <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Cover Photos Preview -->
                                <template x-if="coverPreviews.length > 0">
                                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
                                        <template x-for="(preview, index) in coverPreviews" :key="index">
                                            <div class="relative group">
                                                <img :src="preview" class="w-full h-32 object-cover rounded-lg border-2 border-gray-200 shadow-md">
                                                <button 
                                                    type="button"
                                                    @click="removeCover(index)"
                                                    class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-opacity shadow-lg hover:bg-red-600">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-6">
                    <button type="submit" class="w-full py-4 px-6 bg-gradient-to-r from-[#C8A356] to-[#b08a3c] text-white font-bold rounded-xl shadow-lg hover:shadow-xl hover:scale-[1.02] transition-all duration-200 flex items-center justify-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>إنشاء الحساب</span>
                    </button>

                    <!-- Login Link -->
                    <div class="text-center mt-6">
                        <span class="text-gray-600">لديك حساب بالفعل؟</span>
                        <a href="{{ route('login') }}" class="text-[#C8A356] hover:text-[#b08a3c] font-bold transition">
                            سجل دخولك
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
