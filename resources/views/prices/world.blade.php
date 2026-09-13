@extends('layouts.app')

@php
    $locale = app()->getLocale();
    $pageTitle = match($locale) {
        'ar' => 'أسعار زيت الزيتون العالمية | بورصة إسبانيا، إيطاليا واليونان',
        'fr' => 'Prix Mondiaux de l\'Huile d\'Olive | Espagne, Italie, Grèce & Tunisie',
        default => 'Global Olive Oil Prices | Spain, Italy, Greece & Tunisia Benchmarks',
    };
    $pageDesc = match($locale) {
        'ar' => 'متابعة حية لأسعار زيت الزيتون العالمية في كبرى البورصات: إسبانيا، إيطاليا، اليونان وتونس باليورو والدينار.',
        'fr' => 'Suivi en direct des prix mondiaux de l\'huile d\'olive extra vierge en Espagne, Italie, Grèce et Tunisie en EUR/kg.',
        default => 'Live international extra virgin olive oil benchmark prices across Spain, Italy, Greece, and Tunisia in EUR/kg.',
    };
@endphp

@section('title', $pageTitle)
@section('description', $pageDesc)
@section('content')
<div class="max-w-6xl mx-auto p-6 space-y-4">
  <h1 class="text-2xl font-bold mb-4">🌍 {{ __('أسعار عالمية (World Market)') }}</h1>

  @php $items = isset($worldPrices) ? $worldPrices : collect(); @endphp
  @if($items->count())
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      @foreach($items as $row)
        @php
          $country = $row->country ?? '';
          $variety = $row->variety ?? '';
          $quality = $row->quality ?? '';
          $price   = isset($row->price) ? number_format((float)$row->price, 2) : '';
          $dateRaw = $row->date ?? $row->created_at ?? null;
          try { $date = \Carbon\Carbon::parse($dateRaw)->format('Y-m-d'); } catch (\Throwable $e) { $date = ''; }
        @endphp

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
          <!-- Header -->
          <div class="bg-[#6A8F3B] text-white px-4 py-3 flex items-center justify-between">
            <div class="font-bold">{{ __($country) }}</div>
            <div class="text-xs opacity-95">{{ $date }}</div>
          </div>

          <!-- Body -->
          <div class="p-6">
            <div class="flex items-center justify-between mb-4">
              <span class="text-sm font-bold text-gray-600 uppercase">🫗 {{ __('زيت الزيتون') }}</span>
              @if($quality)
                <span class="px-3 py-1 bg-[#F8F4EC] rounded-full text-xs font-bold text-[#6A8F3B]">{{ __($quality) }}</span>
              @endif
            </div>

            @if($variety)
              <div class="text-sm text-gray-500 mb-3">{{ __('الصنف') }} — {{ __($variety) }}</div>
            @endif

            <div class="mb-2">
              <div class="text-sm text-gray-500 mb-1">{{ __('السعر') }}</div>
              <div class="text-3xl font-bold text-[#1B2A1B]">
                {{ $price }}
                <span class="text-lg text-gray-600">EUR/kg</span>
              </div>
            </div>

            <div class="mt-3 text-xs text-gray-400">📅 {{ $date }}</div>
          </div>
        </div>
      @endforeach
    </div>

    @if(method_exists($items,'links'))
      <div class="mt-4">{{ $items->links() }}</div>
    @endif
  @else
    <p class="text-gray-600">{{ __('لا توجد بيانات حاليا.') }}</p>
  @endif
</div>
@endsection
