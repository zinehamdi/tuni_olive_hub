@extends('layouts.app')
@section('title', 'أسعار اليوم')

@section('content')
<div class="max-w-7xl mx-auto p-6 space-y-8">

  {{-- KPIs --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div class="bg-white rounded-2xl shadow border p-5">
      <div class="text-xs text-gray-500 mb-2">متوسط تونس (آخر 7 أيام)</div>
      <div class="flex flex-wrap gap-2">
        <span class="px-3 py-1 rounded-full bg-green-50 text-green-700 text-xs font-semibold">زيت: {{ isset($tunisianAvg) ? number_format((float)$tunisianAvg, 2) : '—' }} TND</span>
        <span class="px-3 py-1 rounded-full bg-amber-50 text-amber-700 text-xs font-semibold">زيتون: {{ isset($tunisianOliveAvg) ? number_format((float)$tunisianOliveAvg, 2) : '—' }} TND</span>
      </div>
    </div>
    <div class="bg-white rounded-2xl shadow border p-5">
      <div class="text-xs text-gray-500 mb-2">متوسط عالمي (آخر 7 أيام)</div>
      <div class="text-2xl font-extrabold">{{ isset($worldAvg) ? number_format((float)$worldAvg, 2) : '—' }} <span class="text-base font-semibold">EUR/kg</span></div>
    </div>
  </div>

  {{-- Tunisian Souks --}}
  <div class="flex items-center justify-between">
    <h2 class="text-xl font-bold">أسعار الأسواق التونسية (آخر إدخالات)</h2>
    <a href="{{ route('prices.souks') }}" class="text-sm text-[#6A8F3B] hover:underline">عرض الكل</a>
  </div>

  @php $souks = $soukPrices ?? collect(); @endphp
  @if($souks->count())
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      @foreach($souks as $row)
        @php
          $date = optional(\Carbon\Carbon::parse($row->date ?? $row->created_at))->format('Y-m-d');
          $isOil = ($row->product_type ?? '') === 'oil';
          $trend = $row->trend ?? null;
          $trendColor = $trend === 'up' ? 'text-green-600' : ($trend === 'down' ? 'text-red-600' : 'text-gray-600');
          $trendIcon  = $trend === 'up' ? '📈' : ($trend === 'down' ? '📉' : '➡️');
          $changePct  = isset($row->change_percentage) ? rtrim(rtrim(number_format((float)$row->change_percentage,2),'0'),'.').'%' : '—';
          $priceMin = $row->price_min ?? null;
          $priceAvg = $row->price_avg ?? null;
          $priceMax = $row->price_max ?? null;
          $unit = $row->unit ?? 'kg';
          $currency = $row->currency ?? 'TND';
          $quality = $row->quality ?? '';
          $variety = $row->variety ?? '';
          $gov = $row->governorate ?? '';
          $souk = $row->souk_name ?? '';
        @endphp

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
          <div class="bg-[#6A8F3B] text-white px-4 py-3 flex items-center justify-between">
            <div>
              <div class="font-bold">{{ $souk ?: '—' }}</div>
              <div class="text-[11px] opacity-95">{{ $gov ?: '—' }} @if($variety) — {{ $variety }} @endif</div>
            </div>
            <div class="text-xs opacity-95">{{ $date }}</div>
          </div>

          @if($isOil)
            <div class="p-6">
              <div class="text-sm text-gray-500 mb-1">زيت زيتون</div>
              <div class="text-3xl font-bold text-[#1B2A1B]">{{ $priceAvg ?? '—' }} <span class="text-lg text-gray-600">{{ $currency }}/{{ $unit }}</span></div>
              <div class="text-xs text-gray-500 mt-1">النطاق: {{ $priceMin }} - {{ $priceMax }} {{ $currency }}</div>
              <div class="flex items-center justify-between pt-4 border-t border-gray-200 mt-3">
                <span class="text-sm text-gray-600">الاتجاه</span>
                <div class="flex items-center {{ $trendColor }}"><span class="text-2xl mr-2">{{ $trendIcon }}</span><span class="font-bold">{{ $changePct }}</span></div>
              </div>
              <div class="mt-3 text-xs text-gray-400">📅 {{ $date }}</div>
            </div>
          @else
            <div class="p-6">
              <div class="text-sm text-gray-500 mb-1">زيتون @if($variety) — {{ $variety }} @endif</div>
              <div class="grid grid-cols-3 gap-2 mt-3">
                <div class="bg-gray-50 rounded-xl p-2 text-center">
                  <div class="text-xs text-gray-500 mb-1">الأدنى</div>
                  <div class="font-extrabold">{{ $priceMin ? $priceMin.' '.$currency : '—' }}</div>
                </div>
                <div class="bg-gray-50 rounded-xl p-2 text-center">
                  <div class="text-xs text-gray-500 mb-1">المتوسط</div>
                  <div class="font-extrabold">{{ $priceAvg ? $priceAvg.' '.$currency : '—' }}</div>
                </div>
                <div class="bg-gray-50 rounded-xl p-2 text-center">
                  <div class="text-xs text-gray-500 mb-1">الأعلى</div>
                  <div class="font-extrabold">{{ $priceMax ? $priceMax.' '.$currency : '—' }}</div>
                </div>
              </div>
              <div class="flex items-center justify-between pt-4 border-t border-gray-200 mt-3">
                <span class="text-sm text-gray-600">الاتجاه</span>
                <div class="flex items-center {{ $trendColor }}"><span class="text-2xl mr-2">{{ $trendIcon }}</span><span class="font-bold">{{ $changePct }}</span></div>
              </div>
              <div class="mt-3 text-xs text-gray-400">📅 {{ $date }}</div>
            </div>
          @endif
        </div>
      @endforeach
    </div>
  @endif

  {{-- 🌍 World Prices as Cards --}}
  <div class="flex items-center justify-between mt-8">
    <h2 class="text-xl font-bold">أسعار عالمية (آخر إدخالات)</h2>
  </div>

  @php $world = $worldPrices ?? collect(); @endphp
  @if($world->count())
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      @foreach($world as $row)
        @php
          $date = optional(\Carbon\Carbon::parse($row->date ?? $row->created_at))->format('Y-m-d');
          $price = isset($row->price) ? number_format((float)$row->price, 2) : '—';
          $variety = $row->variety ?? '';
          $quality = $row->quality ?? '';
          $country = $row->country ?? '—';
        @endphp

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
          <div class="bg-[#6A8F3B] text-white px-4 py-3 flex items-center justify-between">
            <div>
              <div class="font-bold">{{ $country }}</div>
              <div class="text-[11px] opacity-95">{{ $variety ?: '—' }} @if($quality) — {{ $quality }} @endif</div>
            </div>
            <div class="text-xs opacity-95">{{ $date }}</div>
          </div>

          <div class="p-6">
            <div class="text-sm text-gray-500 mb-1">🌍 سعر عالمي</div>
            <div class="text-3xl font-bold text-[#1B2A1B]">{{ $price }} <span class="text-lg text-gray-600">EUR/kg</span></div>
            <div class="mt-3 text-xs text-gray-400">📅 {{ $date }}</div>
          </div>
        </div>
      @endforeach
    </div>
  @else
    <p class="text-gray-600">لا توجد بيانات عالمية حاليا.</p>
  @endif

</div>
@endsection
