@extends('layouts.app')

@section('content')
@php
  $apiBase = url('/api/v1');
@endphp
<div dir="rtl" class="min-h-screen bg-[#F8F4EC] text-gray-900">
  <header class="sticky top-0 z-50 bg-white/90 border-b">
    <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
      <a href="{{ url('/') }}" class="px-3 py-2 rounded-xl bg-gray-100 hover:bg-gray-200">← رجوع</a>
      <h1 class="text-lg font-bold">تعديل العرض</h1>
    </div>
  </header>

  <main class="max-w-3xl mx-auto px-4 py-8">
    <div class="mx-auto max-w-xl">
      <div class="bg-gradient-to-br from-[#F8F4EC] to-[#EEF5E9] rounded-2xl shadow-xl p-6 lg:p-8 space-y-6">
        <h2 class="text-2xl font-extrabold text-[#1B2A1B]">تعديل العرض</h2>
        <form id="listingForm" class="space-y-6" onsubmit="return submitListing(event)">
      <input type="hidden" name="seller_id" value="{{ auth()->id() }}" />

      <div class="bg-gradient-to-br from-white to-[#F8F4EC] rounded-2xl p-4 shadow-sm space-y-4">
        <div>
          <label for="product_id" class="block text-[#C8A356] font-semibold mb-1">المنتج</label>
          <select id="product_id" name="product_id" class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-gradient-to-br from-white to-[#F8F4EC] focus:ring-2 focus:ring-[#C8A356] focus:border-transparent transition" required>
          <option value="">— اختر —</option>
          @foreach($products as $p)
            <option value="{{ $p->id }}" {{ $listing->product_id == $p->id ? 'selected' : '' }}>{{ $p->variety }} — {{ $p->quality }} — ${{ number_format((float)$p->price,2) }}</option>
          @endforeach
          </select>
        </div>

        <div>
          <label for="status" class="block text-[#C8A356] font-semibold mb-1">الحالة</label>
          <select id="status" name="status" class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-gradient-to-br from-white to-[#F8F4EC] focus:ring-2 focus:ring-[#C8A356] focus:border-transparent transition">
          <option value="draft" {{ $listing->status == 'draft' ? 'selected' : '' }}>مسودة</option>
          <option value="active" {{ $listing->status == 'active' ? 'selected' : '' }}>نشط</option>
          <option value="paused" {{ $listing->status == 'paused' ? 'selected' : '' }}>موقوف مؤقتاً</option>
          </select>
        </div>

        <div>
          <label for="min_order" class="block text-[#C8A356] font-semibold mb-1">أدنى كمية للطلب</label>
          <input id="min_order" name="min_order" type="number" step="0.001" value="{{ $listing->min_order }}" class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-gradient-to-br from-white to-[#F8F4EC] focus:ring-2 focus:ring-[#C8A356] focus:border-transparent transition" placeholder="مثال: 100"/>
        </div>
      </div>

      <div class="grid md:grid-cols-2 gap-6">
        <div class="bg-gradient-to-br from-white to-[#F8F4EC] rounded-2xl p-4 shadow-sm">
          <div class="text-[#C8A356] font-semibold mb-2">طرق الدفع</div>
          <div class="space-y-2 border border-[#C7D1C7] rounded-xl p-3">
            @php
              $pm = is_array($listing->payment_methods) ? $listing->payment_methods : json_decode($listing->payment_methods ?? '[]', true);
              $pm = is_array($pm) ? $pm : [];
            @endphp
            <label class="flex items-center gap-2"><input type="checkbox" name="payment_methods[]" value="cod" {{ in_array('cod', $pm) ? 'checked' : '' }}> دفع عند التسليم (COD)</label>
            <label class="flex items-center gap-2"><input type="checkbox" name="payment_methods[]" value="flouci" {{ in_array('flouci', $pm) ? 'checked' : '' }}> Flouci</label>
            <label class="flex items-center gap-2"><input type="checkbox" name="payment_methods[]" value="d17" {{ in_array('d17', $pm) ? 'checked' : '' }}> D17</label>
            <label class="flex items-center gap-2"><input type="checkbox" name="payment_methods[]" value="stripe" {{ in_array('stripe', $pm) ? 'checked' : '' }}> بطاقة بنكية</label>
            <label class="flex items-center gap-2"><input type="checkbox" name="payment_methods[]" value="bank_lc" {{ in_array('bank_lc', $pm) ? 'checked' : '' }}> اعتماد مستندي (LC)</label>
          </div>
        </div>

        <div class="bg-gradient-to-br from-white to-[#F8F4EC] rounded-2xl p-4 shadow-sm">
          <div class="text-[#C8A356] font-semibold mb-2">خيارات التوصيل</div>
          <div class="space-y-2 border border-[#C7D1C7] rounded-xl p-3">
            @php
              $do = is_array($listing->delivery_options) ? $listing->delivery_options : json_decode($listing->delivery_options ?? '[]', true);
              $do = is_array($do) ? $do : [];
            @endphp
            <label class="flex items-center gap-2"><input type="checkbox" name="delivery_options[]" value="pickup" {{ in_array('pickup', $do) ? 'checked' : '' }}> استلام من المصدر</label>
            <label class="flex items-center gap-2"><input type="checkbox" name="delivery_options[]" value="carrier" {{ in_array('carrier', $do) ? 'checked' : '' }}> عبر ناقل</label>
            <label class="flex items-center gap-2"><input type="checkbox" name="delivery_options[]" value="export" {{ in_array('export', $do) ? 'checked' : '' }}> للتصدير</label>
          </div>
        </div>
      </div>

      <div id="listingErrors" class="text-sm text-red-600 bg-red-50 border border-red-200 rounded-md p-2 hidden" role="alert" aria-live="assertive"></div>

      <div class="sticky bottom-4 flex justify-end gap-3">
        <button type="button" onclick="window.history.back()" class="min-h-[44px] px-5 py-3 rounded-xl bg-gradient-to-r from-[#9B6A4A] to-[#F8F4EC] text-gray-900 shadow-lg hover:shadow-2xl hover:scale-105 transition">إلغاء</button>
        <button class="min-h-[44px] px-5 py-3 rounded-xl bg-gradient-to-r from-[#1B2A1B] to-[#6A8F3B] text-white font-bold shadow-lg hover:shadow-2xl hover:scale-105 transition focus:ring-2 focus:ring-[#C8A356]">حفظ التعديلات</button>
      </div>
        </form>
      </div>
    </div>
  </main>
</div>

<script>
async function submitListing(e){
  e.preventDefault();
  const form = e.target;
  const fd = new FormData(form);
  fd.append('_method', 'PUT'); // Force method to PUT for update route
  const url = '{{ $apiBase }}/listings/{{ $listing->id }}';
  const res = await fetch(url, {
    method: 'POST', // Submit as POST with _method PUT inside form data
    headers: {
      'Accept': 'application/json',
      'X-Requested-With': 'XMLHttpRequest'
    },
    body: fd
  });
  const out = document.getElementById('listingErrors');
  out.textContent = '';
  try {
    const json = await res.json();
    if (res.ok && json.success){
      window.location.href = '{{ route('dashboard') }}'; // Redirect back to dashboard on success
      return false;
    }
    out.textContent = JSON.stringify(json.error || json.errors || json, null, 2);
    out.classList.remove('hidden');
  } catch (err){
    out.textContent = 'حصل خطأ غير متوقع.';
    out.classList.remove('hidden');
  }
  return false;
}
</script>
@endsection
