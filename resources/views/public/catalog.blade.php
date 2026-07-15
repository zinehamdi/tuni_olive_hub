@extends('layouts.app')

@php
    $isArabic = app()->getLocale() === 'ar';
    $isFrench = app()->getLocale() === 'fr';

    $title = $isArabic 
        ? 'عروض التصدير والبيع بالجملة لزيت الزيتون التونسي' 
        : ($isFrench 
            ? 'Catalogue d\'Exportation et Vrac d\'Huile d\'Olive Tunisienne' 
            : 'Tunisian Olive Oil B2B Export Catalog - Wholesale & Bulk');

    $description = $isArabic 
        ? 'تصفح عروض التصدير وزيت الزيتون التونسي بالجملة من المنتجين المعتمدين مباشرة. صفقات مضمونة للتصدير الدولي.' 
        : ($isFrench 
            ? 'Consultez les offres d\'exportation et de vrac d\'huile d\'olive tunisienne en direct des producteurs certifiés.' 
            : 'Browse Tunisian olive oil B2B export offers and bulk deals directly from certified producers. Guaranteed international trade.');

    $keywords = $isArabic 
        ? 'تصدير زيت زيتون, زيت زيتون بالجملة, منتجي زيت الزيتون في تونس, مستوردي زيت زيتون'
        : ($isFrench 
            ? 'exportation huile d\'olive tunisie, huile d\'olive en vrac, grossiste huile d\'olive, vente en gros'
            : 'olive oil export tunisia, bulk olive oil wholesale, B2B olive oil catalog, buy olive oil bulk');
@endphp

@section('title', $title)
@section('description', $description)
@section('keywords', $keywords)

@section('content')
<main class="min-h-screen bg-gray-50 pt-24 pb-16" dir="{{ $isArabic ? 'rtl' : 'ltr' }}">
  <div class="max-w-7xl mx-auto px-4">
    <!-- Header -->
    <header class="mb-8">
      <h1 class="text-3xl font-black text-gray-900 leading-tight mb-2">{{ __('B2B Export Catalog') }}</h1>
      <p class="text-gray-600 text-sm">{{ __('Verified Tunisian exporters & wholesale suppliers') }}</p>
      <div class="w-16 h-1 bg-[#6A8F3B] mt-4 rounded-full"></div>
    </header>

    <!-- Filter Form -->
    <form method="GET" class="grid sm:grid-cols-2 md:grid-cols-5 gap-4 mb-8 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
      <div class="flex flex-col gap-1">
        <label class="text-xs font-bold text-gray-500 uppercase">{{ __('Variety') }}</label>
        <input name="variety" value="{{ $query['variety'] ?? '' }}" placeholder="{{ __('e.g. Chemlali, Chetoui') }}" class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#6A8F3B]" />
      </div>
      
      <div class="flex flex-col gap-1">
        <label class="text-xs font-bold text-gray-500 uppercase">{{ __('Quality') }}</label>
        <input name="quality" value="{{ $query['quality'] ?? '' }}" placeholder="{{ __('e.g. Extra Virgin') }}" class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#6A8F3B]" />
      </div>
      
      <div class="flex flex-col gap-1">
        <label class="text-xs font-bold text-gray-500 uppercase">{{ __('Min Pack') }}</label>
        <input name="min_pack" value="{{ $query['min_pack'] ?? '' }}" placeholder="{{ __('Min weight in kg') }}" class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#6A8F3B]" />
      </div>

      <div class="flex flex-col gap-1">
        <label class="text-xs font-bold text-gray-500 uppercase">{{ __('Sort By') }}</label>
        <select name="sort" class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#6A8F3B]">
          <option value="premium_rank" @selected(($query['sort'] ?? '')==='premium_rank')>{{ __('Recommended') }}</option>
          <option value="newest" @selected(($query['sort'] ?? '')==='newest')>{{ __('Newest') }}</option>
          <option value="price_asc" @selected(($query['sort'] ?? '')==='price_asc')>{{ __('Lowest Price') }}</option>
        </select>
      </div>

      <div class="flex flex-row items-center gap-2 pt-5">
        <input type="checkbox" id="organic" name="organic" value="1" @checked(isset($query['organic'])) class="rounded border-gray-300 text-[#6A8F3B] focus:ring-[#6A8F3B]" />
        <label for="organic" class="text-sm font-semibold text-gray-700 cursor-pointer">{{ __('Organic Only') }}</label>
      </div>
      
      <div class="md:col-span-5 flex justify-end gap-3 pt-2">
        @if(count($query))
          <a href="{{ route('gulf.catalog') }}" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-sm font-bold text-gray-700 transition">{{ __('Reset') }}</a>
        @endif
        <button class="px-6 py-2 rounded-xl bg-[#6A8F3B] text-white hover:bg-[#587830] text-sm font-bold shadow-md transition">{{ __('Search') }}</button>
      </div>
    </form>

    <!-- Results -->
    @if($products->count())
      <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-6">
        @foreach($products as $prod)
          <div class="bg-white border border-gray-100 rounded-3xl p-6 shadow-sm hover:shadow-md transition-all flex flex-col justify-between group">
            <div>
              <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-[#6A8F3B] bg-emerald-50 px-2.5 py-1 rounded-full uppercase tracking-wider">{{ $prod->is_organic ? __('Organic') : __('Standard') }}</span>
                <span class="text-xs text-gray-400 font-medium">{{ $prod->seller->addresses->first() ? $prod->seller->addresses->first()->governorate : 'Tunisia' }}</span>
              </div>
              <h3 class="text-xl font-bold text-gray-800 group-hover:text-[#6A8F3B] transition-colors mb-1">{{ __($prod->variety) }} — {{ __($prod->quality) }}</h3>
              <p class="text-xs text-gray-400 mb-4">{{ __('Exporter:') }} {{ $prod->seller->name ?? __('Verified Exporter') }}</p>
            </div>
            
            <div class="border-t border-gray-50 pt-4 flex items-center justify-between mt-2">
              <div>
                <span class="text-xs text-gray-400 block">{{ __('Wholesale Price') }}</span>
                <span class="text-lg font-black text-gray-900">${{ number_format((float)$prod->price, 2) }}</span>
              </div>
              <a href="{{ route('gulf.product', $prod) }}" class="px-4 py-2 bg-gray-950 text-white rounded-xl text-xs font-bold hover:bg-[#6A8F3B] transition-all">
                {{ __('View Details') }}
              </a>
            </div>
          </div>
        @endforeach
      </div>
      <div class="mt-8 shadow-sm rounded-2xl bg-white p-4">{{ $products->withQueryString()->links() }}</div>
    @else
      <div class="bg-white border border-gray-100 rounded-3xl p-12 text-center shadow-sm">
        <p class="text-gray-500 mb-4 font-medium">{{ __('No export offers match your search criteria.') }}</p>
        <a href="{{ route('gulf.catalog') }}" class="px-5 py-2.5 bg-[#6A8F3B] text-white rounded-xl text-sm font-bold hover:bg-[#587830] transition">{{ __('Clear Filters') }}</a>
      </div>
    @endif
  </div>
</main>
@endsection
