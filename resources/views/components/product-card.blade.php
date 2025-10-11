@props(['title' => 'منتج', 'price' => '', 'variety' => '', 'quality' => ''])
<div class="border rounded overflow-hidden bg-white">
  <div class="bg-gray-100 aspect-video"></div>
  <div class="p-3 {{ app()->getLocale()==='ar' ? 'text-right' : '' }}">
    <div class="font-semibold">{{ $title }}</div>
    <div class="text-sm text-gray-600 flex items-center gap-2 {{ app()->getLocale()==='ar' ? 'flex-row-reverse' : '' }}">
      <span class="px-2 py-0.5 rounded bg-olive text-white text-xs">{{ $variety }}</span>
      <span class="px-2 py-0.5 rounded bg-gold text-white text-xs">{{ $quality }}</span>
    </div>
    <div class="mt-2 font-medium text-olive">{{ $price }}</div>
    <div class="mt-2">
      <a href="#" class="inline-block px-3 py-1 bg-sky text-white rounded text-sm">تفاصيل</a>
    </div>
  </div>
</div>
