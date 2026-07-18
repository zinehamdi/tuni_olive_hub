<x-guest-layout>
    <div class="mx-auto max-w-2xl px-4 py-8">
        <!-- Register Card -->
        <div class="bg-white rounded-3xl shadow-2xl p-8 border border-gray-100">
            <div class="text-center mb-8">
                <span class="text-4xl">🛠️</span>
                <h2 class="text-2xl font-black mt-2 text-[#6A8F3B]">تسجيل مزود خدمة جديد</h2>
                <p class="text-gray-500 text-sm mt-1">انضم إلى أكبر دليل للخدمات الفلاحية والتجارية في تونس واجذب حرفاء جدد</p>
            </div>

            <!-- Validation Errors -->
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border-r-4 border-red-500 rounded-xl text-sm text-red-600 space-y-1">
                    @foreach ($errors->all() as $error)
                        <p>⚠️ {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('services.register.store') }}" enctype="multipart/form-data" class="space-y-6" x-data="{ role: 'carrier', priceType: 'quote' }">
                @csrf

                <!-- Basic Info Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">اسم مزود الخدمة / الشركة <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="مثال: شركة نقل الزيتون السريع" class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#6A8F3B] focus:border-transparent">
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">البريد الإلكتروني <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="example@domain.com" class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#6A8F3B] focus:border-transparent">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Phone -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">رقم الهاتف / واتساب <span class="text-red-500">*</span></label>
                        <input type="text" name="phone" value="{{ old('phone') }}" required placeholder="216XXXXXXXX+" class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#6A8F3B] focus:border-transparent">
                    </div>

                    <!-- Role Selection -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">نوع الخدمة المقدمة <span class="text-red-500">*</span></label>
                        <select name="role" x-model="role" required class="w-full rounded-xl border border-gray-200 px-4 py-3 bg-white focus:outline-none focus:ring-2 focus:ring-[#6A8F3B] focus:border-transparent appearance-none bg-[url('data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2020%2020%22%20fill%3D%22%234a5568%22%3E%3Cpath%20fill-rule%3D%22evenodd%22%20d%3D%22M5.293%207.293a1%201%200%20011.414%200L10%2010.586l3.293-3.293a1%201%200%20111.414%201.414l-4%204a1%201%200%2001-1.414%200l-4-4a1%201%200%20010-1.414z%22%20clip-rule%3D%22evenodd%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem_1.25rem] bg-no-repeat rtl:bg-[left_1rem_center] ltr:bg-[right_1rem_center] rtl:pl-10 ltr:pr-10">
                            <option value="carrier">🚛 ناقل بري وبحري (Transporteur)</option>
                            <option value="mill">🏢 معصرة زيتون (Pressoir / Moulin)</option>
                            <option value="packer">📦 وحدة تعبئة وتغليف (Unité d'embouteillage)</option>
                            <option value="transiteur">🛃 مخلص جمركي (Transiteur)</option>
                            <option value="comptable">📊 محاسب (Comptable)</option>
                            <option value="service_bureau">📝 مكتب خدمات إدارية (Bureau de services)</option>
                            <option value="agri_equipment">🚜 شركات معدات وآليات فلاحية (Matériel Agricole)</option>
                            <option value="agri_materials">🌱 شركات أسمدة ومواد فلاحية (Matières Agricoles)</option>
                            <option value="agri_study_office">📐 مكتب دراسات فلاحية (Bureau d'études agricoles)</option>
                        </select>
                    </div>

                    <!-- Provider Type Selection -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">هيكل النشاط / نوع الكيان <span class="text-red-500">*</span></label>
                        <select name="provider_type" required class="w-full rounded-xl border border-gray-200 px-4 py-3 bg-white focus:outline-none focus:ring-2 focus:ring-[#6A8F3B] focus:border-transparent appearance-none bg-[url('data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2020%2020%22%20fill%3D%22%234a5568%22%3E%3Cpath%20fill-rule%3D%22evenodd%22%20d%3D%22M5.293%207.293a1%201%200%20011.414%200L10%2010.586l3.293-3.293a1%201%200%20111.414%201.414l-4%204a1%201%200%2001-1.414%200l-4-4a1%201%200%20010-1.414z%22%20clip-rule%3D%22evenodd%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem_1.25rem] bg-no-repeat rtl:bg-[left_1rem_center] ltr:bg-[right_1rem_center] rtl:pl-10 ltr:pr-10">
                            <option value="freelancer" {{ old('provider_type') === 'freelancer' ? 'selected' : '' }}>👤 مستقل (Freelancer)</option>
                            <option value="bureau" {{ old('provider_type') === 'bureau' ? 'selected' : '' }}>🏢 مكتب (Bureau / Cabinet)</option>
                            <option value="societe" {{ old('provider_type') === 'societe' ? 'selected' : '' }}>🏢 شركة (Société)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Governorate -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">الولاية (المركز الرئيسي) <span class="text-red-500">*</span></label>
                        <select name="governorate" required class="w-full rounded-xl border border-gray-200 px-4 py-3 bg-white focus:outline-none focus:ring-2 focus:ring-[#6A8F3B] focus:border-transparent appearance-none bg-[url('data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2020%2020%22%20fill%3D%22%234a5568%22%3E%3Cpath%20fill-rule%3D%22evenodd%22%20d%3D%22M5.293%207.293a1%201%200%20011.414%200L10%2010.586l3.293-3.293a1%201%200%20111.414%201.414l-4%204a1%201%200%2001-1.414%200l-4-4a1%201%200%20010-1.414z%22%20clip-rule%3D%22evenodd%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem_1.25rem] bg-no-repeat rtl:bg-[left_1rem_center] ltr:bg-[right_1rem_center] rtl:pl-10 ltr:pr-10">
                            <option value="">-- اختر الولاية --</option>
                            @foreach(config('governorates', []) as $gov)
                                <option value="{{ $gov }}" {{ old('governorate') === $gov ? 'selected' : '' }}>{{ $gov }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Pricing Info -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">طريقة التسعير <span class="text-red-500">*</span></label>
                        <select name="price_type" x-model="priceType" required class="w-full rounded-xl border border-gray-200 px-4 py-3 bg-white focus:outline-none focus:ring-2 focus:ring-[#6A8F3B] focus:border-transparent appearance-none bg-[url('data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2020%2020%22%20fill%3D%22%234a5568%22%3E%3Cpath%20fill-rule%3D%22evenodd%22%20d%3D%22M5.293%207.293a1%201%200%20011.414%200L10%2010.586l3.293-3.293a1%201%200%20111.414%201.414l-4%204a1%201%200%2001-1.414%200l-4-4a1%201%200%20010-1.414z%22%20clip-rule%3D%22evenodd%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem_1.25rem] bg-no-repeat rtl:bg-[left_1rem_center] ltr:bg-[right_1rem_center] rtl:pl-10 ltr:pr-10">
                            <option value="quote">💬 اتصل للحصول على تسعيرة / السعر حسب الطلب</option>
                            <option value="fixed">💰 سعر محدد (يبدأ من...)</option>
                        </select>
                    </div>
                </div>

                <!-- Fixed Price Field (conditional) -->
                <div x-show="priceType === 'fixed'" x-transition class="mt-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">السعر التقريبي بالدينار التونسي (TND)</label>
                    <input type="number" name="service_price" value="{{ old('service_price') }}" min="0" placeholder="مثال: 150" class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#6A8F3B] focus:border-transparent">
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">نبذة عن الخدمة والمعدات المتوفرة <span class="text-red-500">*</span></label>
                    <textarea name="service_description" rows="4" required placeholder="مثال: نوفر خدمات نقل زيتون متكاملة بشاحنات حديثة مغطاة من كافة مناطق الجمهورية..." class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#6A8F3B] focus:border-transparent">{{ old('service_description') }}</textarea>
                </div>

                <!-- Upload Images -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Logo / Profile Picture -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">شعار الشركة أو الصورة الشخصية</label>
                        <input type="file" name="profile_picture" class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-[#6A8F3B]/10 file:text-[#6A8F3B] hover:file:bg-[#6A8F3B]/20 cursor-pointer">
                    </div>

                    <!-- Service/Equipment Image -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">صورة تعبيرية للخدمة (جرار، شاحنة، منتج)</label>
                        <input type="file" name="cover_photos[]" class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-[#6A8F3B]/10 file:text-[#6A8F3B] hover:file:bg-[#6A8F3B]/20 cursor-pointer">
                    </div>
                </div>

                <!-- Security Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">كلمة المرور <span class="text-red-500">*</span></label>
                        <input type="password" name="password" required placeholder="••••••••" class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#6A8F3B] focus:border-transparent">
                    </div>

                    <!-- Password Confirmation -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">تأكيد كلمة المرور <span class="text-red-500">*</span></label>
                        <input type="password" name="password_confirmation" required placeholder="••••••••" class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#6A8F3B] focus:border-transparent">
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                    class="w-full py-4 px-6 bg-gradient-to-r from-[#6A8F3B] to-[#5a7a2f] text-white font-bold rounded-xl shadow-lg hover:shadow-xl hover:scale-[1.02] transition-all duration-200 flex items-center justify-center gap-2 mt-4">
                    <span>سجل حسابك كمزود خدمة</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </button>

                <!-- Login Link -->
                <div class="text-center pt-4 border-t">
                    <span class="text-gray-600">لديك حساب بالفعل؟</span>
                    <a href="{{ route('login') }}" class="text-[#C8A356] hover:text-[#b08a3c] font-bold transition">
                        سجل دخولك
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
