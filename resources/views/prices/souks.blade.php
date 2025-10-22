@extends('layouts.app')

@section('title','أسعار السوق')
@section('content')
<div class="max-w-7xl mx-auto p-6 space-y-6">
  <h1 class="text-2xl font-bold">أسعار السوق (Souks)</h1>

  @php $items = isset($souks) ? $souks : collect(); @endphp
  @if($items->count())
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      @foreach($items as $row)
        @php
          $dateRaw = $row->date ?? $row->priced_at ?? $row->created_at ?? null;
          try { $date = \Carbon\Carbon::parse($dateRaw)->format('Y-m-d'); } catch (\Throwable $e) { $date = ''; }
          $isOil = ($row->product_type ?? '') === 'oil';
          $trend = $row->trend ?? null;
          $trendColor = $trend === 'up' ? 'text-green-600' : ($trend === 'down' ? 'text-red-600' : 'text-gray-600');
          $trendIcon  = $trend === 'up' ? '📈' : ($trend === 'down' ? '📉' : '➡️');
          $changePct  = isset($row->change_percentage) ? rtrim(rtrim(number_format((float)$row->change_percentage,2),'0'),'.').'%' : '—';
          $priceMin = isset($row->price_min) ? number_format((float)$row->price_min, 2) : null;
          $priceAvg = isset($row->price_avg) ? number_format((float)$row->price_avg, 2) : null;
          $priceMax = isset($row->price_max) ? number_format((float)$row->price_max, 2) : null;
          $unit = $row->unit ?? ($isOil ? 'kg' : 'kg');
          $currency = $row->currency ?? 'TND';
          $quality = $row->quality ?? null;
          $variety = $row->variety ?? null;
          $gov = $row->governorate ?? '';
          $souk = $row->souk_name ?? '';
        @endphp

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
          <div class="bg-[#6A8F3B] text-white px-4 py-3 flex items-center justify-between">
            <div>
              <div class="font-bold">{{ $souk ?: '—' }}</div>
              <div class="text-[11px] opacity-95">
                {{ $gov ?: '—' }} @if($variety) — {{ $variety }} @endif
              </div>
            </div>
            <div class="text-xs opacity-95">{{ $date }}</div>
          </div>

          @if($isOil)
            <div class="p-6">
              <div class="flex items-center justify-between mb-4">
                <span class="text-sm font-bold text-gray-600 uppercase">🫗 زيت الزيتون</span>
                @if($quality)
                  <span class="px-3 py-1 bg-[#F8F4EC] rounded-full text-xs font-bold text-[#6A8F3B]">{{ $quality }}</span>
                @endif
              </div>
              <div class="mb-4">
                <div class="text-sm text-gray-500 mb-1">السعر المتوسط</div>
                <div class="text-3xl font-bold text-[#1B2A1B]">
                  {{ $priceAvg ?? '—' }} <span class="text-lg text-gray-600">{{ $currency }}/{{ $unit }}</span>
                </div>
                @if($priceMin && $priceMax)
                  <div class="text-xs text-gray-500 mt-1">النطاق: {{ $priceMin }} - {{ $priceMax }} {{ $currency }}</div>
                @endif
              </div>
              <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                <span class="text-sm text-gray-600">الاتجاه</span>
                <div class="flex items-center {{ $trendColor }}"><span class="text-2xl mr-2">{{ $trendIcon }}</span><span class="font-bold">{{ $changePct }}</span></div>
              </div>
              <div class="mt-3 text-xs text-gray-400">📅 {{ $date ? \Carbon\Carbon::parse($date)->format('d/m/Y') : '' }}</div>
            </div>
          @else
            <div class="p-5 space-y-3">
              <div class="text-sm text-gray-500">زيتون @if($variety) — {{ $variety }} @endif</div>
              <div class="grid grid-cols-3 gap-3 mt-2">
                <div class="bg-gray-50 rounded-xl p-3 text-center">
                  <div class="text-xs text-gray-500 mb-1">الأدنى</div>
                  <div class="font-extrabold">{{ $priceMin ? $priceMin.' '.$currency : '—' }}</div>
                </div>
                <div class="bg-gray-50 rounded-xl p-3 text-center">
                  <div class="text-xs text-gray-500 mb-1">المتوسط</div>
                  <div class="font-extrabold">{{ $priceAvg ? $priceAvg.' '.$currency : '—' }}</div>
                </div>
                <div class="bg-gray-50 rounded-xl p-3 text-center">
                  <div class="text-xs text-gray-500 mb-1">الأعلى</div>
                  <div class="font-extrabold">{{ $priceMax ? $priceMax.' '.$currency : '—' }}</div>
                </div>
              </div>
              <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                <span class="text-sm text-gray-600">الاتجاه</span>
                <div class="flex items-center {{ $trendColor }}"><span class="text-2xl mr-2">{{ $trendIcon }}</span><span class="font-bold">{{ $changePct }}</span></div>
              </div>
              <div class="mt-3 text-xs text-gray-400">📅 {{ $date ? \Carbon\Carbon::parse($date)->format('Y-m-d') : '' }}</div>
              <div class="text-xs text-gray-400"> {{ $unit }} </div>
            </div>
          @endif
        </div>
      @endforeach
    </div>

    @if(method_exists($items, 'links'))
      <div class="mt-6">{{ $items->links() }}</div>
    @endif
  @else
    <p class="text-gray-600">لا توجد بيانات حاليا.</p>
  @endif
</div>
@endsection
