@props(['type' => 'confirm'])
<div class="border rounded p-3 bg-white {{ app()->getLocale()==='ar' ? 'text-right' : '' }}">
  <div class="text-pepper font-medium">
    {{ $slot }}
  </div>
</div>
