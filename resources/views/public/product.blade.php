<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>{{ $product->variety }} — {{ $product->quality }} | منصّة الزيت التونسي</title>
  <link rel="canonical" href="{{ route('gulf.product', $product) }}" />
  <meta name="description" content="{{ $product->variety }} {{ $product->quality }} بسعر ${{ number_format((float)$product->price, 2) }}" />
  <meta property="og:title" content="{{ $product->variety }} — {{ $product->quality }}" />
  <meta property="og:type" content="product" />
  <meta property="og:url" content="{{ route('gulf.product', $product) }}" />
  <meta property="og:description" content="عرض جاهز للتصدير من منصّة الزيت التونسي." />
  <meta property="og:site_name" content="Tunisian Olive Oil" />
  <meta name="twitter:card" content="summary_large_image" />
</head>
<body class="min-h-screen bg-white text-gray-900" style="background-image:url('/images/HighTidebg.jpeg');background-size:cover;background-position:center;background-attachment:fixed;">
  <header class="sticky top-0 z-50 bg-white/90 border-b">
    <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
      <a href="{{ route('gulf.catalog') }}" class="px-3 py-2 rounded-xl bg-gray-100 hover:bg-gray-200">← رجوع</a>
      <a href="{{ url('/') }}" class="px-3 py-2 rounded-xl bg-emerald-600 text-white hover:opacity-90">الصفحة الرئيسية</a>
    </div>
  </header>

  <main class="max-w-4xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-extrabold mb-2">{{ $product->variety }} — {{ $product->quality }}</h1>
    <div class="text-gray-700 mb-4">البائع: {{ $product->seller->name ?? 'مجهول' }}</div>
    <div class="text-emerald-700 text-xl font-bold mb-6">${{ number_format((float)$product->price, 2) }}</div>

    <div class="grid md:grid-cols-2 gap-6">
      <div>
        <div class="rounded-2xl border p-4">
          <h2 class="font-bold mb-2">التعبئة</h2>
          <div class="text-gray-700">وزن بالكيلو: {{ $product->weight_kg ?? '—' }}</div>
          <div class="text-gray-700">حجم باللتر: {{ $product->volume_liters ?? '—' }}</div>
        </div>
      </div>
      <div>
        <div class="rounded-2xl border p-4">
          <h2 class="font-bold mb-2">الشهادات</h2>
          <pre class="text-sm text-gray-700 whitespace-pre-wrap">{{ json_encode($product->certs, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
      </div>
    </div>

    <div class="mt-8 flex gap-3 justify-end">
      <a href="{{ route('orders.requestAoula', ['product' => $product->id]) }}" class="px-4 py-3 rounded-xl bg-emerald-600 text-white hover:opacity-90">اطلب عولة</a>
      <a href="{{ url('/contact') }}" class="px-4 py-3 rounded-xl bg-blue-600 text-white hover:opacity-90">تواصل</a>
    </div>
  </main>
  </body>
</html>
