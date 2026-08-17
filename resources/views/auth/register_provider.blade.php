<x-guest-layout>
    <div class="mx-auto max-w-2xl px-4 py-8">
        <!-- Register Card -->
        <div class="bg-white rounded-3xl shadow-2xl p-6 border border-gray-100">
            <div class="text-center mb-8">
                <span class="text-4xl">🛠️</span>
                <h2 class="text-2xl font-black mt-2 text-[#6A8F3B]">{{ __('Register New Service Provider') }}</h2>
                <p class="text-gray-500 text-sm mt-1">{{ __('Join the largest agricultural and commercial services directory in Tunisia and attract new customers') }}</p>
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
                        <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('Service Provider / Company Name') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="{{ __('Example: Fast Olive Transport Company') }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#6A8F3B] focus:border-transparent">
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('Email Address') }} <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="example@domain.com" class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#6A8F3B] focus:border-transparent">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Phone -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('Phone Number') }} / {{ __('WhatsApp') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="phone" value="{{ old('phone') }}" required placeholder="216XXXXXXXX+" class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#6A8F3B] focus:border-transparent">
                    </div>

                    <!-- Role Selection -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('Provided Service Type') }} <span class="text-red-500">*</span></label>
                        <select name="role" x-model="role" required class="w-full rounded-xl border border-gray-200 px-4 py-3 bg-white focus:outline-none focus:ring-2 focus:ring-[#6A8F3B] focus:border-transparent appearance-none bg-[url('data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2020%2020%22%20fill%3D%22%234a5568%22%3E%3Cpath%20fill-rule%3D%22evenodd%22%20d%3D%22M5.293%207.293a1%201%200%20011.414%200L10%2010.586l3.293-3.293a1%201%200%20111.414%201.414l-4%204a1%201%200%2001-1.414%200l-4-4a1%201%200%20010-1.414z%22%20clip-rule%3D%22evenodd%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem_1.25rem] bg-no-repeat rtl:bg-[left_1rem_center] ltr:bg-[right_1rem_center] rtl:pl-10 ltr:pr-10">
                            <option value="carrier">{{ __('🚛 Land and Sea Carrier') }}</option>
                            <option value="mill">{{ __('🏢 Olive Mill') }}</option>
                            <option value="packer">{{ __('📦 Packaging Unit') }}</option>
                            <option value="transiteur">{{ __('🛃 Customs Broker') }}</option>
                            <option value="comptable">{{ __('📊 Accountant') }}</option>
                            <option value="service_bureau">{{ __('📝 Administrative Services Office') }}</option>
                            <option value="agri_equipment">{{ __('🚜 Agricultural Equipment Companies') }}</option>
                            <option value="agri_materials">{{ __('🌱 Fertilizers and Agricultural Materials Companies') }}</option>
                            <option value="agri_study_office">{{ __('📐 Agricultural Studies Office') }}</option>
                        </select>
                    </div>

                    <!-- Provider Type Selection -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('Business Structure / Entity Type') }} <span class="text-red-500">*</span></label>
                        <select name="provider_type" required class="w-full rounded-xl border border-gray-200 px-4 py-3 bg-white focus:outline-none focus:ring-2 focus:ring-[#6A8F3B] focus:border-transparent appearance-none bg-[url('data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2020%2020%22%20fill%3D%22%234a5568%22%3E%3Cpath%20fill-rule%3D%22evenodd%22%20d%3D%22M5.293%207.293a1%201%200%20011.414%200L10%2010.586l3.293-3.293a1%201%200%20111.414%201.414l-4%204a1%201%200%2001-1.414%200l-4-4a1%201%200%20010-1.414z%22%20clip-rule%3D%22evenodd%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem_1.25rem] bg-no-repeat rtl:bg-[left_1rem_center] ltr:bg-[right_1rem_center] rtl:pl-10 ltr:pr-10">
                            <option value="freelancer" {{ old('provider_type') === 'freelancer' ? 'selected' : '' }}>{{ __('👤 Freelancer') }}</option>
                            <option value="bureau" {{ old('provider_type') === 'bureau' ? 'selected' : '' }}>{{ __('🏢 Office / Cabinet') }}</option>
                            <option value="societe" {{ old('provider_type') === 'societe' ? 'selected' : '' }}>{{ __('🏢 Company') }}</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Governorate -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('State (Main Center)') }} <span class="text-red-500">*</span></label>
                        <select name="governorate" required class="w-full rounded-xl border border-gray-200 px-4 py-3 bg-white focus:outline-none focus:ring-2 focus:ring-[#6A8F3B] focus:border-transparent appearance-none bg-[url('data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2020%2020%22%20fill%3D%22%234a5568%22%3E%3Cpath%20fill-rule%3D%22evenodd%22%20d%3D%22M5.293%207.293a1%201%200%20011.414%200L10%2010.586l3.293-3.293a1%201%200%20111.414%201.414l-4%204a1%201%200%2001-1.414%200l-4-4a1%201%200%20010-1.414z%22%20clip-rule%3D%22evenodd%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem_1.25rem] bg-no-repeat rtl:bg-[left_1rem_center] ltr:bg-[right_1rem_center] rtl:pl-10 ltr:pr-10">
                            <option value="">{{ __('-- Choose State --') }}</option>
                            @foreach(config('governorates', []) as $gov)
                                <option value="{{ $gov }}" {{ old('governorate') === $gov ? 'selected' : '' }}>{{ $gov }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Pricing Info -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('Pricing Method') }} <span class="text-red-500">*</span></label>
                        <select name="price_type" x-model="priceType" required class="w-full rounded-xl border border-gray-200 px-4 py-3 bg-white focus:outline-none focus:ring-2 focus:ring-[#6A8F3B] focus:border-transparent appearance-none bg-[url('data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2020%2020%22%20fill%3D%22%234a5568%22%3E%3Cpath%20fill-rule%3D%22evenodd%22%20d%3D%22M5.293%207.293a1%201%200%20011.414%200L10%2010.586l3.293-3.293a1%201%200%20111.414%201.414l-4%204a1%201%200%2001-1.414%200l-4-4a1%201%200%20010-1.414z%22%20clip-rule%3D%22evenodd%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.25rem_1.25rem] bg-no-repeat rtl:bg-[left_1rem_center] ltr:bg-[right_1rem_center] rtl:pl-10 ltr:pr-10">
                            <option value="quote">{{ __('💬 Call for quote / Price on request') }}</option>
                            <option value="fixed">{{ __('💰 Fixed price (Starts from...)') }}</option>
                        </select>
                    </div>
                </div>

                <!-- Fixed Price Field (conditional) -->
                <div x-show="priceType === 'fixed'" x-transition class="mt-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('Approximate Price in TND') }}</label>
                    <input type="number" name="service_price" value="{{ old('service_price') }}" min="0" placeholder="{{ __('Example: 150') }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#6A8F3B] focus:border-transparent">
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('About the service and available equipment') }} <span class="text-red-500">*</span></label>
                    <textarea name="service_description" rows="4" required placeholder="{{ __('Example: We provide integrated olive transport services...') }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#6A8F3B] focus:border-transparent">{{ old('service_description') }}</textarea>
                </div>

                <!-- Upload Images -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Logo / Profile Picture -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('Company Logo or Profile Picture') }}</label>
                        <input type="file" name="profile_picture" class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-[#6A8F3B]/10 file:text-[#6A8F3B] hover:file:bg-[#6A8F3B]/20 cursor-pointer">
                    </div>

                    <!-- Service/Equipment Image -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('Expressive Image for Service (Tractor, Truck, Product)') }}</label>
                        <input type="file" name="cover_photos[]" class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-[#6A8F3B]/10 file:text-[#6A8F3B] hover:file:bg-[#6A8F3B]/20 cursor-pointer">
                    </div>
                </div>

                <!-- Security Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('Password') }} <span class="text-red-500">*</span></label>
                        <input type="password" name="password" required placeholder="••••••••" class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#6A8F3B] focus:border-transparent">
                    </div>

                    <!-- Password Confirmation -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('Confirm Password') }} <span class="text-red-500">*</span></label>
                        <input type="password" name="password_confirmation" required placeholder="••••••••" class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#6A8F3B] focus:border-transparent">
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                    class="w-full py-4 px-6 bg-gradient-to-r from-[#6A8F3B] to-[#5a7a2f] text-white font-bold rounded-xl shadow-lg hover:shadow-xl hover:scale-[1.02] transition-all duration-200 flex items-center justify-center gap-2 mt-4">
                    <span>{{ __('Register your account as a service provider') }}</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </button>

                <!-- Login Link -->
                <div class="text-center pt-4 border-t">
                    <span class="text-gray-600">{{ __('Already have an account?') }}</span>
                    <a href="{{ route('login') }}" class="text-[#C8A356] hover:text-[#b08a3c] font-bold transition">
                        {{ __('Log in') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
