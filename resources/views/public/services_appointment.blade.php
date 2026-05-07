@extends('layouts.app')

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .flatpickr-calendar {
        font-family: inherit;
        border: 1px solid #e5e7eb;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        border-radius: 0.75rem;
    }
    .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange, .flatpickr-day.selected.inRange, .flatpickr-day.startRange.inRange, .flatpickr-day.endRange.inRange, .flatpickr-day.selected:focus, .flatpickr-day.startRange:focus, .flatpickr-day.endRange:focus, .flatpickr-day.selected:hover, .flatpickr-day.startRange:hover, .flatpickr-day.endRange:hover, .flatpickr-day.selected.prevMonthDay, .flatpickr-day.startRange.prevMonthDay, .flatpickr-day.endRange.prevMonthDay, .flatpickr-day.selected.nextMonthDay, .flatpickr-day.startRange.nextMonthDay, .flatpickr-day.endRange.nextMonthDay {
        background: #6A8F3B;
        border-color: #6A8F3B;
    }
</style>
@endpush

@section('title', app()->getLocale() === 'ar' ? 'حجز موعد' : 'Book Appointment')

@section('content')
<div class="min-h-screen pt-24 pb-20 px-4 bg-gray-50">
    <div class="max-w-4xl mx-auto">
        
        <h1 class="text-3xl font-black text-[#1a3310] mb-8 flex items-center gap-3">
            <span>📅</span>
            {{ app()->getLocale() === 'ar' ? 'حجز موعد حملة تسويقية' : 'Book Marketing Campaign Appointment' }}
        </h1>

        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
        @endif
        
        @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
        @endif

        @if(empty($cart))
        <div class="bg-white rounded-2xl shadow-sm p-12 text-center border border-gray-100">
            <span class="text-5xl block mb-4">📭</span>
            <h2 class="text-xl font-bold text-gray-700 mb-2">{{ app()->getLocale() === 'ar' ? 'السلة فارغة' : 'Your cart is empty' }}</h2>
            <p class="text-gray-500 mb-6">{{ app()->getLocale() === 'ar' ? 'لم تقم بإضافة أي خدمات إلى سلتك بعد.' : 'You have not added any services to your cart yet.' }}</p>
            <a href="{{ route('services.pricing') }}" class="inline-block bg-[#6A8F3B] text-white px-6 py-2.5 rounded-xl font-semibold hover:bg-[#5a7a2f] transition">
                {{ app()->getLocale() === 'ar' ? 'تصفح الخدمات' : 'Browse Services' }}
            </a>
        </div>
        @else
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 md:p-8">
                <div class="space-y-6">
                    @php $total = 0; @endphp
                    @foreach($cart as $id => $item)
                    @php $total += $item['price'] * $item['quantity']; @endphp
                    <div class="flex flex-col sm:flex-row items-center gap-4 sm:gap-6 pb-6 border-b border-gray-100 last:border-0 last:pb-0">
                        <div class="w-16 h-16 rounded-xl bg-gray-50 flex items-center justify-center text-2xl border border-gray-100 shrink-0">
                            {{ $item['icon'] ?? '📦' }}
                        </div>
                        <div class="flex-grow text-center sm:text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}">
                            <h3 class="text-lg font-bold text-gray-900">{{ $item['name'] }}</h3>
                            <p class="text-sm text-gray-500 mt-1">{{ app()->getLocale() === 'ar' ? 'الكمية' : 'Quantity' }}: {{ $item['quantity'] }}</p>
                        </div>
                        <div class="text-xl font-black text-[#6A8F3B] whitespace-nowrap">
                            {{ number_format($item['price'] * $item['quantity'], 2) }} <span class="text-sm font-normal text-gray-500">{{ $item['currency'] }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <div class="bg-gray-50 p-6 md:p-8 border-t border-gray-100">
                <form action="{{ route('services.appointment.submit', $service->id) }}" method="POST" onsubmit="trackCheckoutInitiate({{ $total }})">
                    @csrf
                    <div class="mb-6 bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r text-blue-800 text-sm leading-relaxed">
                        {{ app()->getLocale() === 'ar' ? 'نحتاج إلى بعض المعلومات عن نشاطك التجاري لبناء حملتك. سيتم تحديد ميزانية الحملة من الباقات التي اخترتها. يرجى ملء النموذج أدناه لحجز موعد لمناقشة التفاصيل.' : 'We need some information about your business to build your campaign. The campaign budget is determined by your selected packages. Please fill out the form below to book an appointment to discuss the details.' }}
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ app()->getLocale() === 'ar' ? 'الاسم' : 'Name' }} <span class="text-red-500">*</span></label>
                            <input type="text" name="name" required class="w-full rounded-xl border-gray-300 focus:border-[#6A8F3B] focus:ring focus:ring-[#6A8F3B]/20">
                            @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ app()->getLocale() === 'ar' ? 'رقم الهاتف' : 'Phone Number' }} <span class="text-red-500">*</span></label>
                            <input type="text" name="phone" required class="w-full rounded-xl border-gray-300 focus:border-[#6A8F3B] focus:ring focus:ring-[#6A8F3B]/20">
                            @error('phone') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ app()->getLocale() === 'ar' ? 'معلومات عن نشاطك التجاري' : 'Business Information' }} <span class="text-red-500">*</span></label>
                        <textarea name="business_info" required rows="3" placeholder="{{ app()->getLocale() === 'ar' ? 'صفحة الفيسبوك، الموقع الإلكتروني، وصف للمنتجات...' : 'Facebook page, website, product descriptions...' }}" class="w-full rounded-xl border-gray-300 focus:border-[#6A8F3B] focus:ring focus:ring-[#6A8F3B]/20"></textarea>
                        @error('business_info') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-8">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ app()->getLocale() === 'ar' ? 'تاريخ ووقت الموعد المفضل' : 'Preferred Appointment Date & Time' }} <span class="text-red-500">*</span></label>
                        <input type="text" id="appointment_date" name="appointment_date" required placeholder="{{ app()->getLocale() === 'ar' ? 'اختر التاريخ والوقت...' : 'Select Date & Time...' }}" class="w-full rounded-xl border-gray-300 focus:border-[#6A8F3B] focus:ring focus:ring-[#6A8F3B]/20 bg-white cursor-pointer">
                        @error('appointment_date') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex flex-col sm:flex-row justify-between items-center gap-4 border-t border-gray-200 pt-6">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">{{ app()->getLocale() === 'ar' ? 'الميزانية الإجمالية للحملة' : 'Total Campaign Budget' }}</p>
                            <div class="text-3xl font-black text-[#1a3310]">
                                {{ number_format($total, 2) }} <span class="text-lg font-normal text-gray-500">TND</span>
                            </div>
                        </div>
                        
                        <button type="submit" class="w-full sm:w-auto bg-[#6A8F3B] hover:bg-[#5a7a2f] text-white font-bold py-3.5 px-10 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 flex items-center justify-center gap-2">
                            <span>📅</span>
                            {{ app()->getLocale() === 'ar' ? 'احجز موعد' : 'Book Appointment' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif
        
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ar.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js"></script>
<script>
(function() {
    let locale = '{{ app()->getLocale() }}';
    let minDate = new Date();
    minDate.setDate(minDate.getDate() + 1); // tomorrow

    if (typeof flatpickr !== 'undefined') {
        flatpickr("#appointment_date", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            minDate: minDate,
            time_24hr: true,
            locale: locale === 'ar' ? 'ar' : (locale === 'fr' ? 'fr' : 'en'),
            disableMobile: "true",
            minTime: "09:00",
            maxTime: "18:00"
        });
    }
})();

function trackCheckoutInitiate(totalValue) {
    if(typeof fbq !== 'undefined') {
        fbq('track', 'InitiateCheckout', {
            value: totalValue,
            currency: 'TND'
        });
    }
}
</script>
@endpush
