@extends('layouts.app')

@php
    $isArabic = app()->getLocale() === 'ar';
    $isFrench = app()->getLocale() === 'fr';

    $varietyText = __($product->variety);
    $qualityText = __($product->quality);
    $priceText = '$' . number_format((float)$product->price, 2);

    $title = $isArabic 
        ? "عرض تصدير: زيت زيتون {$varietyText} — {$qualityText}"
        : ($isFrench 
            ? "Offre Export: Huile d'olive {$varietyText} — {$qualityText}"
            : "B2B Export Offer: {$varietyText} — {$qualityText} Olive Oil");

    $description = $isArabic 
        ? "شراء زيت زيتون تونسي بالجملة للتصدير. صنف {$varietyText}، جودة {$qualityText} بسعر {$priceText}. تواصل مع المصدر مباشرة." 
        : ($isFrench 
            ? "Achat d'huile d'olive tunisienne en gros pour export. Variété {$varietyText}, qualité {$qualityText} à {$priceText}. Contactez l'exportateur." 
            : "Buy wholesale Tunisian olive oil for export. Variety {$varietyText}, quality {$qualityText} at {$priceText}. Contact the seller directly.");

    $keywords = $isArabic 
        ? "زيت زيتون {$product->variety}, تصدير زيت زيتون, زيت زيتون جملة, صفقة زيت زيتون"
        : ($isFrench 
            ? "huile d'olive {$product->variety}, export huile d'olive tunisie, vente en gros huile d'olive"
            : "tunisian olive oil {$product->variety}, bulk olive oil export, wholesale extra virgin olive oil");
@endphp

@section('title', $title)
@section('description', $description)
@section('keywords', $keywords)

@section('content')
<main class="min-h-screen bg-gray-50 pt-24 pb-16" dir="{{ $isArabic ? 'rtl' : 'ltr' }}">
  <div class="max-w-4xl mx-auto px-4">
    <!-- Back Link -->
    <a href="{{ route('gulf.catalog') }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-[#6A8F3B] mb-6 transition font-bold text-sm group">
      <svg class="w-5 h-5 {{ $isArabic ? 'rotate-180' : '' }} group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
      </svg>
      {{ __('Back to Export Catalog') }}
    </a>

    <!-- Product Card -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-xl overflow-hidden p-8 md:p-12">
      <div class="flex items-center justify-between mb-6">
        <span class="text-xs font-bold text-[#6A8F3B] bg-emerald-50 px-3 py-1.5 rounded-full uppercase tracking-wider">{{ $product->is_organic ? __('Organic') : __('Standard') }}</span>
        @if($product->is_premium)
          <span class="text-xs font-bold text-[#C8A356] bg-amber-50 px-3 py-1.5 rounded-full uppercase tracking-wider">{{ __('Premium Rank') }}</span>
        @endif
      </div>

      <h1 class="text-3xl md:text-4xl font-black text-gray-900 leading-tight mb-2">
        {{ $varietyText }} — {{ $qualityText }}
      </h1>
      <p class="text-sm text-gray-400 mb-6">{{ __('Offered by:') }} <span class="font-bold text-gray-600">{{ $product->seller->name ?? __('Verified Exporter') }}</span></p>

      <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100 mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <span class="text-xs text-gray-400 block font-medium uppercase tracking-wider mb-1">{{ __('Wholesale Export Price') }}</span>
          <span class="text-3xl font-black text-[#6A8F3B]">{{ $priceText }} <span class="text-sm font-semibold text-gray-500">/ {{ __('kg') }}</span></span>
        </div>
        <div class="flex items-center gap-2">
          <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
          <span class="text-sm font-bold text-emerald-700">{{ __('Export Ready (EXW / FOB)') }}</span>
        </div>
      </div>

      <!-- Details Sections -->
      <div class="grid md:grid-cols-2 gap-8 mb-8">
        <div class="border border-gray-100 p-6 rounded-2xl bg-white shadow-sm">
          <h2 class="font-bold text-lg text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-[#6A8F3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            {{ __('Packaging & Quantity') }}
          </h2>
          <ul class="space-y-3 text-sm text-gray-600">
            <li class="flex justify-between border-b border-gray-50 pb-2">
              <span class="font-medium text-gray-400">{{ __('Volume (Liters):') }}</span>
              <span class="font-bold text-gray-800">{{ $product->volume_liters ?? '—' }} L</span>
            </li>
            <li class="flex justify-between border-b border-gray-50 pb-2">
              <span class="font-medium text-gray-400">{{ __('Weight (KG):') }}</span>
              <span class="font-bold text-gray-800">{{ $product->weight_kg ?? '—' }} kg</span>
            </li>
            <li class="flex justify-between">
              <span class="font-medium text-gray-400">{{ __('Minimum Packaging:') }}</span>
              <span class="font-bold text-gray-800">{{ $product->certs['min_packaging'] ?? '—' }}</span>
            </li>
          </ul>
        </div>

        <div class="border border-gray-100 p-6 rounded-2xl bg-white shadow-sm">
          <h2 class="font-bold text-lg text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-[#6A8F3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            {{ __('Certifications & Analysis') }}
          </h2>
          @if(!empty($product->certs) && count($product->certs))
            <ul class="space-y-3 text-sm text-gray-600">
              @foreach($product->certs as $key => $val)
                @if($key !== 'min_packaging')
                  <li class="flex justify-between border-b border-gray-50 pb-2">
                    <span class="font-medium text-gray-400 uppercase text-xs">{{ str_replace('_', ' ', $key) }}:</span>
                    <span class="font-bold text-gray-800">{{ is_array($val) ? implode(', ', $val) : $val }}</span>
                  </li>
                @endif
              @endforeach
            </ul>
          @else
            <p class="text-sm text-gray-400 italic">{{ __('No certificates uploaded.') }}</p>
          @endif
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="border-t border-gray-100 pt-8 flex flex-col sm:flex-row justify-end gap-4">
        <a href="{{ route('orders.requestAoula', ['product' => $product->id]) }}" class="px-6 py-3 rounded-2xl bg-[#6A8F3B] hover:bg-[#587830] text-white font-bold text-center shadow-md transition duration-200">
          {{ __('Request Sample / Quote') }}
        </a>
        <a href="{{ url('/contact') }}" class="px-6 py-3 rounded-2xl bg-gray-950 hover:bg-gray-800 text-white font-bold text-center transition duration-200">
          {{ __('Contact Exporter') }}
        </a>
      </div>
    </div>
  </div>
</main>
@endsection
