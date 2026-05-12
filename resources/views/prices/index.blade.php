@extends('layouts.app')
@section('title', __('Today\'s Prices'))

@section('content')
<div class="max-w-7xl mx-auto p-6 space-y-8">

  {{-- KPIs --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div class="bg-white rounded-2xl shadow border p-5">
      <div class="text-xs text-gray-500 mb-2">{{ __('Tunisian Average (Last 7 Days)') }}</div>
      <div class="flex flex-wrap gap-2">
        <span class="px-3 py-1 rounded-full bg-green-50 text-green-700 text-xs font-semibold">{{ __('Oil') }}: {{ isset($tunisianAvg) ? number_format((float)$tunisianAvg, 2) : '—' }} TND</span>
        <span class="px-3 py-1 rounded-full bg-amber-50 text-amber-700 text-xs font-semibold">{{ __('Olives') }}: {{ isset($tunisianOliveAvg) ? number_format((float)$tunisianOliveAvg, 2) : '—' }} TND</span>
      </div>
    </div>
    <div class="bg-white rounded-2xl shadow border p-5">
      <div class="text-xs text-gray-500 mb-2">{{ __('World Average (Last 7 Days)') }}</div>
      <div class="text-2xl font-extrabold">{{ isset($worldAvg) ? number_format((float)$worldAvg, 2) : '—' }} <span class="text-base font-semibold">EUR/kg</span></div>
    </div>
  </div>

  {{-- Tunisian Souks --}}
  <div class="flex items-center justify-between">
    <h2 class="text-xl font-bold">{{ __('Tunisian Souk Prices (Latest Entries)') }}</h2>
    <a href="{{ route('prices.souks') }}" class="text-sm text-[#6A8F3B] hover:underline">{{ __('View All') }}</a>
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

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden relative group">
          <!-- Background Decorative Icon -->
          <div class="absolute -right-2 -bottom-2 w-32 h-32 opacity-[0.12] pointer-events-none group-hover:scale-110 transition-transform duration-700">
            @if($isOil)
              <img src="{{ asset('icons/target-svg.svg') }}" class="w-full h-full object-contain">
            @else
              <img src="{{ asset('icons/availability-svg.svg') }}" class="w-full h-full object-contain">
            @endif
          </div>

          <div class="bg-[#6A8F3B] text-white px-4 py-3 flex items-center justify-between relative z-10">
            <div>
              <div class="font-bold">{{ $souk ?: '—' }}</div>
              <div class="text-[11px] opacity-95">{{ $gov ?: '—' }} @if($variety) — {{ $variety }} @endif</div>
            </div>
            <div class="text-xs opacity-95">{{ $date }}</div>
          </div>

          @if($isOil)
            <div class="p-6 relative z-10">
              <div class="text-sm text-gray-500 mb-1">{{ __('Olive Oil') }}</div>
              <div class="text-3xl font-bold text-[#1B2A1B]">{{ $priceAvg ?? '—' }} <span class="text-lg text-gray-600">{{ $currency }}/{{ $unit }}</span></div>
              <div class="text-xs text-gray-500 mt-1">{{ __('Range') }}: {{ $priceMin }} - {{ $priceMax }} {{ $currency }}</div>
              <div class="flex items-center justify-between pt-4 border-t border-gray-100 mt-3 relative">
                <!-- Trend Background Icon -->
                <div class="absolute right-0 top-1/2 -translate-y-1/2 w-12 h-12 opacity-[0.15] pointer-events-none">
                   <img src="{{ asset('icons/interface-control-svggraphscreen.svg') }}" class="w-full h-full object-contain">
                </div>
                <span class="text-sm text-gray-600">{{ __('Trend') }}</span>
                <div class="flex items-center {{ $trendColor }} relative z-10"><span class="text-2xl mr-2">{{ $trendIcon }}</span><span class="font-bold">{{ $changePct }}</span></div>
              </div>
              <div class="mt-3 text-xs text-gray-400 flex items-center gap-1">
                <img src="{{ asset('icons/date-svg.svg') }}" class="w-3 h-3 opacity-40">
                <span>{{ $date }}</span>
              </div>
            </div>
          @else
            <div class="p-6 relative z-10">
              <div class="text-sm text-gray-500 mb-1">{{ __('Olives') }} @if($variety) — {{ __($variety) }} @endif</div>
              <div class="grid grid-cols-3 gap-2 mt-3">
                <div class="bg-gray-50 rounded-xl p-2 text-center">
                  <div class="text-xs text-gray-500 mb-1">{{ __('Min') }}</div>
                  <div class="font-extrabold">{{ $priceMin ? $priceMin.' '.$currency : '—' }}</div>
                </div>
                <div class="bg-gray-50 rounded-xl p-2 text-center">
                  <div class="text-xs text-gray-500 mb-1">{{ __('Average') }}</div>
                  <div class="font-extrabold">{{ $priceAvg ? $priceAvg.' '.$currency : '—' }}</div>
                </div>
                <div class="bg-gray-50 rounded-xl p-2 text-center">
                  <div class="text-xs text-gray-500 mb-1">{{ __('Max') }}</div>
                  <div class="font-extrabold">{{ $priceMax ? $priceMax.' '.$currency : '—' }}</div>
                </div>
              </div>
              <div class="flex items-center justify-between pt-4 border-t border-gray-200 mt-3 relative">
                <!-- Trend Background Icon -->
                <div class="absolute right-0 top-1/2 -translate-y-1/2 w-12 h-12 opacity-[0.15] pointer-events-none">
                   <img src="{{ asset('icons/interface-control-svggraphscreen.svg') }}" class="w-full h-full object-contain">
                </div>
                <span class="text-sm text-gray-600">{{ __('Trend') }}</span>
                <div class="flex items-center {{ $trendColor }} relative z-10"><span class="text-2xl mr-2">{{ $trendIcon }}</span><span class="font-bold">{{ $changePct }}</span></div>
              </div>
              <div class="mt-3 text-xs text-gray-400 flex items-center gap-1">
                <img src="{{ asset('icons/date-svg.svg') }}" class="w-3 h-3 opacity-40">
                <span>{{ $date }}</span>
              </div>
            </div>
          @endif
        </div>
      @endforeach
    </div>
  @endif

  {{-- 🌍 World Prices as Cards --}}
  <div class="flex items-center justify-between mt-8">
    <h2 class="text-xl font-bold">{{ __('World Prices (Latest Entries)') }}</h2>
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

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden relative group">
          <!-- Background Decorative Icon -->
          <div class="absolute -right-2 -bottom-2 w-32 h-32 opacity-[0.12] pointer-events-none group-hover:scale-110 transition-transform duration-700">
            <img src="{{ asset('icons/machine-vision-svg.svg') }}" class="w-full h-full object-contain">
          </div>

          <div class="bg-[#6A8F3B] text-white px-4 py-3 flex items-center justify-between relative z-10">
            <div>
              <div class="font-bold">{{ $country }}</div>
              <div class="text-[11px] opacity-95">{{ $variety ? __($variety) : '—' }} @if($quality) — {{ __($quality) }} @endif</div>
            </div>
            <div class="text-xs opacity-95">{{ $date }}</div>
          </div>

          <div class="p-6 relative z-10">
            <div class="text-sm text-gray-500 mb-1 flex items-center gap-2">
              <span class="text-lg">🌍</span>
              <span>{{ __('World Price') }}</span>
            </div>
            <div class="text-3xl font-bold text-[#1B2A1B]">{{ $price }} <span class="text-lg text-gray-600">EUR/kg</span></div>
            <div class="mt-3 text-xs text-gray-400 flex items-center gap-1">
              <img src="{{ asset('icons/date-svg.svg') }}" class="w-3 h-3 opacity-40">
              <span>{{ $date }}</span>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @else
    <p class="text-gray-600">{{ __('No world data available currently.') }}</p>
  @endif

</div>

@if(isset($soukPrices) && $soukPrices->count())
@push('head')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "ItemList",
  "itemListElement": [
    @foreach($soukPrices as $index => $row)
    {
      "@@type": "ListItem",
      "position": {{ $index + 1 }},
      "item": {
        "@@type": "Product",
        "name": "{{ ($row->product_type ?? '') === 'oil' ? __('Olive Oil') : __('Olives') }} {{ $row->variety ? '- ' . $row->variety : '' }} - {{ $row->souk_name ?: $row->governorate }}",
        "offers": {
          "@@type": "Offer",
          "price": "{{ $row->price_avg ?? $row->price_min }}",
          "priceCurrency": "{{ $row->currency ?? 'TND' }}",
          "availability": "https://schema.org/InStock",
          "priceValidUntil": "{{ now()->addDays(7)->format('Y-m-d') }}"
        }
      }
    }{{ !$loop->last ? ',' : '' }}
    @endforeach
  ]
}
</script>
@endpush
@endif
@endsection
