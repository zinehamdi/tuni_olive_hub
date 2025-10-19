<div dir="rtl" class="min-h-screen grid place-items-center bg-white" style="background-image:url('/images/HighTidebg.jpeg');background-size:cover;background-position:center;background-attachment:fixed;">
  <div class="text-center p-10">
    <h1 class="text-2xl font-bold mb-4">{{ $title ?? 'الصفحة جاهزة قريباً' }}</h1>
    <p class="text-gray-700 mb-6">هذه صفحة مؤقتة لربط الأزرار بالروابط الصحيحة. يمكنك استبدالها لاحقاً بالمحتوى الحقيقي.</p>
    <a href="{{ url('/') }}" class="px-4 py-3 rounded-xl bg-emerald-600 text-white hover:opacity-90 focus:ring focus:outline-none">عودة للصفحة الرئيسية</a>
  </div>
</div>
