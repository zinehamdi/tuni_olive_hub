@php($q = $query ?? [])
<div dir="rtl" class="min-h-screen bg-white text-gray-900" style="background-image:url('/images/HighTidebg.jpeg');background-size:cover;background-position:center;background-attachment:fixed;">
  <header class="sticky top-0 z-50 bg-white/90 border-b">
    <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
      <a href="{{ url('/') }}" class="text-lg font-bold">منصّة الزيت التونسي</a>
      <a href="{{ route('home') }}" class="px-3 py-2 rounded-xl bg-emerald-600 text-white hover:opacity-90">الصفحة الرئيسية</a>
    </div>
  </header>

  <main class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-extrabold mb-6">عروض التصدير</h1>

    <form method="GET" class="grid md:grid-cols-5 gap-3 mb-6">
      <input name="variety" value="{{ $q['variety'] ?? '' }}" placeholder="الصنف" class="border rounded-xl px-3 py-2" />
      <input name="quality" value="{{ $q['quality'] ?? '' }}" placeholder="الجودة" class="border rounded-xl px-3 py-2" />
      <label class="flex items-center gap-2"><input type="checkbox" name="organic" value="1" @checked(isset($q['organic'])) /> عضوي</label>
      <input name="min_pack" value="{{ $q['min_pack'] ?? '' }}" placeholder="أدنى تعبئة" class="border rounded-xl px-3 py-2" />
      <select name="sort" class="border rounded-xl px-3 py-2">
        <option value="premium_rank" @selected(($q['sort'] ?? '')==='premium_rank')>الأفضل</option>
        <option value="newest" @selected(($q['sort'] ?? '')==='newest')>الأحدث</option>
        <option value="price_asc" @selected(($q['sort'] ?? '')==='price_asc')>الأرخص</option>
      </select>
      <div class="md:col-span-5 flex justify-end">
        <button class="px-4 py-2 rounded-xl bg-blue-600 text-white hover:opacity-90">ابحث</button>
      </div>
    </form>

    @if($products->count())
      <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-5">
        @foreach($products as $prod)
          <a href="{{ route('gulf.product', $prod) }}" class="block border rounded-2xl p-4 hover:shadow">
            <div class="text-sm text-gray-600 mb-1">{{ $prod->seller->name ?? 'مجهول' }}</div>
            <div class="text-lg font-bold">{{ $prod->variety }} — {{ $prod->quality }}</div>
            <div class="text-emerald-700 mt-1">${{ number_format((float)$prod->price, 2) }}</div>
          </a>
        @endforeach
      </div>
      <div class="mt-6">{{ $products->withQueryString()->links() }}</div>
    @else
      <p class="text-gray-600">لا توجد نتائج مطابقة حالياً.</p>
    @endif
  </main>
</div>
