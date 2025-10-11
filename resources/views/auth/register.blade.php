<x-guest-layout>
    <div x-data="{ role: '' }" dir="rtl" class="mx-auto max-w-xl bg-gradient-to-br from-[#F8F4EC] to-[#EEF5E9] rounded-2xl shadow-xl p-6 lg:p-8">
        <form method="GET" action="{{ route('register.role') }}" class="space-y-6">
            <h1 class="text-2xl font-bold text-center mb-6 text-[#C8A356]">اختيار الدور</h1>
            <div>
                <label for="role" class="block font-medium text-gray-700 mb-1">الدور <span class="text-red-600">*</span></label>
                <select id="role" name="role" required x-model="role" class="mt-1 block w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-gradient-to-br from-white to-[#F8F4EC] focus:ring-2 focus:ring-[#C8A356]">
                    <option value="">اختر الدور</option>
                    <option value="farmer">فلاح</option>
                    <option value="carrier">ناقِل</option>
                    <option value="mill">معصرة</option>
                    <option value="packer">مُعبئ</option>
                    <option value="normal">مستخدم عادي</option>
                </select>
            </div>
            <button type="submit" class="w-full bg-[#C8A356] text-white font-bold py-3 rounded-xl shadow hover:bg-[#b08a3c] transition" :disabled="role === ''">التالي</button>
        </form>
    </div>
</x-guest-layout>
