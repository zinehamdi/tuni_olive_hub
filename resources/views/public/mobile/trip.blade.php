<div dir="rtl" class="min-h-screen bg-white text-gray-900" style="background-image:url('/images/HighTidebg.jpeg');background-size:cover;background-position:center;background-attachment:fixed;">
  <header class="sticky top-0 z-50 bg-white/90 border-b">
    <div class="max-w-3xl mx-auto px-4 py-4 flex items-center justify-between">
      <a href="{{ url('/') }}" class="px-3 py-2 rounded-xl bg-gray-100 hover:bg-gray-200">← رجوع</a>
      <h1 class="text-lg font-bold">رحلتي النشطة</h1>
    </div>
  </header>

  <main class="max-w-3xl mx-auto px-4 py-8">
    <section id="tripBox" class="rounded-2xl border p-4 hidden">
      <div class="flex items-center justify-between">
        <div class="font-bold">رمز الرحلة: <span id="srCode">—</span></div>
        <div class="text-sm text-gray-600">PIN: <span id="pinMask">—</span></div>
      </div>
      <ul id="loads" class="mt-3 space-y-2"></ul>

      <form id="podForm" class="mt-4 flex items-center gap-3" onsubmit="return uploadPod(event)">
        <input type="file" name="photo" accept="image/jpeg,image/png" class="border rounded-xl px-3 py-2" required />
        <button class="px-4 py-2 rounded-xl bg-emerald-600 text-white hover:opacity-90">رفع صورة POD</button>
      </form>
      <div id="podMsg" class="text-sm text-gray-700 mt-2"></div>
    </section>

    <section id="emptyBox" class="rounded-2xl border p-6 text-center text-gray-700">
      لا توجد رحلة نشطة حالياً.
    </section>
  </main>
</div>

<script>
const api = '{{ url('/api/v1') }}';
let tripId = null;
async function fetchActive(){
  const res = await fetch(api + '/mobile/trips/active', { headers: { 'Accept': 'application/json' }});
  if (!res.ok) return;
  const json = await res.json();
  const d = json.data;
  const tripBox = document.getElementById('tripBox');
  const emptyBox = document.getElementById('emptyBox');
  if (!d) { tripBox.classList.add('hidden'); emptyBox.classList.remove('hidden'); return; }
  tripId = d.id;
  document.getElementById('srCode').textContent = d.sr_code || '—';
  document.getElementById('pinMask').textContent = d.pin_mask || '—';
  const ul = document.getElementById('loads');
  ul.innerHTML = '';
  (d.loads || []).forEach(ld => {
    const li = document.createElement('li');
    li.className = 'rounded-xl border p-3';
    li.textContent = `حمولة #${ld.id} — ${ld.qty} ${ld.unit} — من: ${ld.pickup?.address || '—'} → إلى: ${ld.dropoff?.address || '—'}`;
    ul.appendChild(li);
  });
  emptyBox.classList.add('hidden');
  tripBox.classList.remove('hidden');
}

async function uploadPod(e){
  e.preventDefault();
  if (!tripId) return false;
  const fd = new FormData(e.target);
  const res = await fetch(api + `/mobile/trips/${tripId}/pod/photo`, {
    method: 'POST',
    headers: { 'Accept': 'application/json' },
    body: fd
  });
  const out = document.getElementById('podMsg');
  try {
    const json = await res.json();
    if (res.ok && json.success) {
      out.textContent = `تم الرفع. العدد الإجمالي للصور: ${json.data.count}`;
      e.target.reset();
    } else {
      if (res.status === 429) out.textContent = 'محاولات كثيرة. الرجاء المحاولة لاحقاً.';
      else if (res.status === 401) out.textContent = 'الرجاء تسجيل الدخول.';
      else if (res.status === 403) out.textContent = 'غير مسموح.';
      else out.textContent = (json.error || 'فشل في الرفع');
    }
  } catch (err) {
    out.textContent = 'حصل خطأ.';
  }
  return false;
}

fetchActive();
</script>
