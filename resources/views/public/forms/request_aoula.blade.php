@extends('layouts.app')

@section('content')
@php
  $apiBase = url('/api/v1');
@endphp
<div dir="rtl" class="min-h-screen bg-[#F8F4EC] text-gray-900">
  <header class="sticky top-0 z-50 bg-white/90 border-b">
    <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
      <a href="{{ url('/') }}" class="px-3 py-2 rounded-xl bg-gray-100 hover:bg-gray-200">← رجوع</a>
      <h1 class="text-lg font-bold">طلب عولة</h1>
    </div>
  </header>

  <main class="max-w-3xl mx-auto px-4 py-8">
    <div class="mx-auto max-w-xl">
      <div class="bg-gradient-to-br from-[#F8F4EC] to-[#EEF5E9] rounded-2xl shadow-xl p-6 lg:p-8 space-y-6">
        <h2 class="text-2xl font-extrabold text-[#1B2A1B]">طلب عولة</h2>
        <form id="aoulaForm" class="space-y-6" onsubmit="return submitAoula(event)">
      <input type="hidden" name="buyer_id" value="{{ $userId }}" />

      <div class="bg-gradient-to-br from-white to-[#F8F4EC] rounded-2xl p-4 shadow-sm space-y-4">
        <div>
          <label for="listing_id" class="block text-[#C8A356] font-semibold mb-1">اختر عرضاً</label>
          <select id="listing_id" name="listing_id" class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-gradient-to-br from-white to-[#F8F4EC] focus:ring-2 focus:ring-[#C8A356] focus:border-transparent transition" required>
          <option value="">— اختر —</option>
          @foreach($listings as $l)
            <option value="{{ $l->id }}">{{ $l->product->variety ?? '—' }} — {{ $l->product->quality ?? '—' }} — البائع: {{ $l->seller->name ?? '—' }}</option>
          @endforeach
          </select>
        </div>

      <div class="grid md:grid-cols-2 gap-6">
        <div>
          <label for="qty" class="block text-[#C8A356] font-semibold mb-1">الكمية</label>
          <input id="qty" name="qty" type="number" step="0.001" class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-gradient-to-br from-white to-[#F8F4EC] focus:ring-2 focus:ring-[#C8A356] focus:border-transparent transition" required />
        </div>
        <div>
          <label for="unit" class="block text-[#C8A356] font-semibold mb-1">الوحدة</label>
          <select id="unit" name="unit" class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-gradient-to-br from-white to-[#F8F4EC] focus:ring-2 focus:ring-[#C8A356] focus:border-transparent transition" required>
            <option value="l">لتر</option>
            <option value="kg">كغ</option>
            <option value="ton">طن</option>
          </select>
        </div>
      </div>

      <div>
        <label for="price_unit" class="block text-[#C8A356] font-semibold mb-1">السعر للوحدة</label>
        <input id="price_unit" name="price_unit" type="number" step="0.001" class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-gradient-to-br from-white to-[#F8F4EC] focus:ring-2 focus:ring-[#C8A356] focus:border-transparent transition" placeholder="مثال: 18.500" required />
      </div>

      <div class="grid md:grid-cols-2 gap-6">
        <div>
          <label for="addr" class="block text-[#C8A356] font-semibold mb-1">العنوان</label>
          <input id="addr" name="meta[address]" type="text" class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-gradient-to-br from-white to-[#F8F4EC] focus:ring-2 focus:ring-[#C8A356] focus:border-transparent transition" placeholder="الولاية/المدينة، العنوان" />
        </div>
        <div>
          <label for="phone" class="block text-[#C8A356] font-semibold mb-1">الهاتف</label>
          <input id="phone" name="meta[phone]" type="tel" class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-gradient-to-br from-white to-[#F8F4EC] focus:ring-2 focus:ring-[#C8A356] focus:border-transparent transition" placeholder="رقم الهاتف" />
        </div>
      </div>

      <div class="grid md:grid-cols-2 gap-6">
        <div>
          <label for="variety" class="block text-[#C8A356] font-semibold mb-1">الصنف المطلوب</label>
          <input id="variety" name="meta[variety]" type="text" class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-gradient-to-br from-white to-[#F8F4EC] focus:ring-2 focus:ring-[#C8A356] focus:border-transparent transition" placeholder="مثال: شملالي" />
        </div>
        <div>
          <label for="budget" class="block text-[#C8A356] font-semibold mb-1">الميزانية التقريبية</label>
          <input id="budget" name="meta[budget]" type="number" step="0.001" class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-gradient-to-br from-white to-[#F8F4EC] focus:ring-2 focus:ring-[#C8A356] focus:border-transparent transition" placeholder="مثال: 1000" />
        </div>
      </div>

      <input type="hidden" name="payment_method" value="cod" />

      <div id="aoulaErrors" class="hidden text-sm text-red-600 bg-red-50 border border-red-200 rounded-md p-2" role="alert" aria-live="assertive"></div>
      <div id="aoulaSuccess" class="hidden text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-md p-2" role="status" aria-live="polite"></div>

      <div class="sticky bottom-4 flex justify-end gap-3">
        <button type="button" onclick="document.getElementById('aoulaForm').reset()" class="min-h-[44px] px-5 py-3 rounded-xl bg-gradient-to-r from-[#9B6A4A] to-[#F8F4EC] text-gray-900 shadow-lg hover:shadow-2xl hover:scale-105 transition">إلغاء</button>
        <button class="min-h-[44px] px-5 py-3 rounded-xl bg-gradient-to-r from-[#1B2A1B] to-[#6A8F3B] text-white font-bold shadow-lg hover:shadow-2xl hover:scale-105 transition focus:ring-2 focus:ring-[#C8A356]">إرسال</button>
      </div>
        </form>
      </div>
    </div>
  </main>
</div>

<script>
async function submitAoula(e){
  e.preventDefault();
  const form = e.target;
  const fd = new FormData(form);

  // We must include seller_id based on selected listing
  const listingId = form.querySelector('select[name="listing_id"]').value;
  const selected = {{ $listings->keyBy('id')->toJson() }};
  const listing = selected[listingId];
  if (listing && listing.seller_id) {
    fd.append('seller_id', listing.seller_id);
  }

  const url = '{{ $apiBase }}/orders';
  const res = await fetch(url, {
    method: 'POST',
    headers: {
      'Accept': 'application/json',
      'X-Requested-With': 'XMLHttpRequest'
    },
    body: fd
  });
  const out = document.getElementById('aoulaErrors');
  const okMsg = document.getElementById('aoulaSuccess');
  out.textContent = '';
  okMsg.textContent = '';
  okMsg.classList.add('hidden');
  try {
    const json = await res.json();
    if (res.ok && json.success){
      okMsg.textContent = 'تم إرسال الطلب بنجاح. سيتم التواصل معك قريباً.';
      okMsg.classList.remove('hidden');
      (e.target.querySelector('button[type="submit"]')||{}).disabled = true;
      return false;
    }
    if (res.status === 401) out.textContent = 'الرجاء تسجيل الدخول.';
    else if (res.status === 403) out.textContent = 'غير مسموح.';
    else if (res.status === 422) out.textContent = 'تحقق من الحقول: ' + JSON.stringify(json.errors || {}, null, 2);
    else out.textContent = (json.error || 'حصل خطأ غير متوقع.');
    out.classList.remove('hidden');
  } catch (err){
    out.textContent = 'حصل خطأ غير متوقع.';
    out.classList.remove('hidden');
  }
  return false;
}
</script>
@endsection
