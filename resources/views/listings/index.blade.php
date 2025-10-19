@extends('layouts.app')
@section('content')
<div class="mx-auto max-w-3xl">
  <h2 class="text-2xl font-extrabold text-[#1B2A1B] mb-6">العروض المنشورة</h2>
  <table class="w-full border rounded-xl bg-white shadow-lg">
    <thead>
      <tr class="bg-[#F8F4EC] text-[#1B2A1B]">
        <th class="p-3">#</th>
        <th class="p-3">المنتج</th>
        <th class="p-3">الصنف</th>
        <th class="p-3">الجودة</th>
        <th class="p-3">الكمية</th>
        <th class="p-3">السعر</th>
        <th class="p-3">البائع</th>
        <th class="p-3">تاريخ النشر</th>
      </tr>
    </thead>
    <tbody>
      @foreach($listings as $listing)
        <tr class="border-t">
          <td class="p-3">{{ $listing->id }}</td>
          <td class="p-3">{{ $listing->product->type ?? '-' }}</td>
          <td class="p-3">{{ $listing->product->variety ?? '-' }}</td>
          <td class="p-3">{{ $listing->product->quality ?? '-' }}</td>
          <td class="p-3">{{ $listing->available_qty ?? '-' }} {{ $listing->qty_unit ?? '' }}</td>
          <td class="p-3">{{ $listing->price_per_unit ?? '-' }} {{ $listing->currency ?? '' }}</td>
          <td class="p-3">{{ $listing->seller->name ?? '-' }}</td>
          <td class="p-3">{{ $listing->created_at->format('Y-m-d') }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
  <div class="mt-6">{{ $listings->links() }}</div>
</div>
@endsection
