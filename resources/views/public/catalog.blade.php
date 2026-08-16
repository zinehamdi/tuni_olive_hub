@extends('layouts.app')

@section('content')
@php
    $q = $query ?? [];
@endphp
<div dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" class="min-h-screen bg-gray-50 text-gray-900">
  
  <!-- Hero Section -->
  <div class="relative w-full h-[40vh] min-h-[350px] flex flex-col items-center justify-center overflow-hidden">
      @php
          $bgImage = \Illuminate\Support\Facades\Storage::disk('public')->exists('settings/catalog-hero.webp')
              ? \Illuminate\Support\Facades\Storage::url('settings/catalog-hero.webp')
              : asset('images/HighTidebg.jpeg');
      @endphp
      <img src="{{ $bgImage }}" alt="{{ __('Premium Export Catalog') }}" class="absolute inset-0 w-full h-full object-cover">
      <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-black/20"></div>
      <div class="relative z-10 text-center px-4 max-w-4xl mx-auto">
          <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-white drop-shadow-lg mb-6">{{ __('Premium Export Catalog') }}</h1>
          <p class="text-white/90 text-lg md:text-xl font-medium drop-shadow-md max-w-2xl mx-auto">{{ __('Discover our finest selection of Tunisian olive oil and olives') }}</p>
      </div>
  </div>

  <main class="max-w-7xl mx-auto px-4 py-8 -mt-12 relative z-20">
    <form method="GET" class="grid md:grid-cols-5 gap-3 mb-8 bg-white p-6 rounded-2xl shadow-xl border border-gray-100">
      <input name="variety" value="{{ $q['variety'] ?? '' }}" placeholder="{{ __('Variety (e.g. Chemlali)') }}" class="border border-gray-200 rounded-xl px-4 py-2 focus:ring-2 focus:ring-[#6A8F3B] outline-none" />
      <input name="quality" value="{{ $q['quality'] ?? '' }}" placeholder="{{ __('Quality (e.g. Extra Virgin)') }}" class="border border-gray-200 rounded-xl px-4 py-2 focus:ring-2 focus:ring-[#6A8F3B] outline-none" />
      <label class="flex items-center gap-2 px-2 bg-gray-50 border border-gray-200 rounded-xl cursor-pointer hover:bg-emerald-50 transition">
          <input type="checkbox" name="organic" value="1" class="w-5 h-5 text-emerald-600 rounded" @checked(isset($q['organic'])) /> 
          <span class="font-bold text-gray-700">{{ __('Organic Product') }}</span>
      </label>
      <input name="min_pack" value="{{ $q['min_pack'] ?? '' }}" placeholder="{{ __('Min Pack (kg)') }}" class="border border-gray-200 rounded-xl px-4 py-2 focus:ring-2 focus:ring-[#6A8F3B] outline-none" />
      
      <div class="flex gap-2">
        <select name="sort" class="border border-gray-200 rounded-xl px-3 py-2 flex-1 focus:ring-2 focus:ring-[#6A8F3B] outline-none">
          <option value="premium_rank" @selected(($q['sort'] ?? '')==='premium_rank')>{{ __('Top Rated') }}</option>
          <option value="newest" @selected(($q['sort'] ?? '')==='newest')>{{ __('Newest') }}</option>
          <option value="price_asc" @selected(($q['sort'] ?? '')==='price_asc')>{{ __('Cheapest') }}</option>
        </select>
        <button class="px-6 py-2 rounded-xl bg-[#6A8F3B] text-white font-bold hover:bg-emerald-700 transition shadow-md">{{ __('Search') }}</button>
      </div>
    </form>

    @if($listings->count())
      <div class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach($listings as $listing)
          <div class="border rounded-lg overflow-hidden bg-white shadow-md hover:shadow-xl transition">
              @if($listing->product)
                  <!-- Product Image -->
                  <div class="relative w-full aspect-video bg-gradient-to-br from-[#6A8F3B] to-[#C8A356] overflow-hidden">
                      @php
                          // Try to get image from product media first, then listing media
                          $productImage = null;
                          if($listing->product->media && is_array($listing->product->media) && count($listing->product->media) > 0) {
                              $productImage = $listing->product->media[0];
                          } elseif($listing->media && is_array($listing->media) && count($listing->media) > 0) {
                              $productImage = $listing->media[0];
                          }
                      @endphp
                      
                      @if($productImage)
                          <!-- Display actual product image -->
                          <img src="{{ Storage::url($productImage) }}" 
                               alt="{{ $listing->product->variety }}" 
                               class="w-full h-full object-cover"
                               loading="lazy">
                          
                          <!-- Product Type Badge -->
                          @if($listing->product->type === 'oil')
                              <span class="absolute top-3 right-3 bg-white/95 text-[#6A8F3B] px-3 py-1 rounded-full text-xs font-bold shadow-md">🫗 {{ app()->getLocale() === 'ar' ? 'زيت زيتون' : (app()->getLocale() === 'fr' ? 'Huile d\'olive' : 'Olive Oil') }}</span>
                          @else
                              <span class="absolute top-3 right-3 bg-white/95 text-[#6A8F3B] px-3 py-1 rounded-full text-xs font-bold shadow-md">🫒 {{ app()->getLocale() === 'ar' ? 'زيتون' : (app()->getLocale() === 'fr' ? 'Olives' : 'Olives') }}</span>
                          @endif
                      @else
                          <!-- Fallback to icon if no image -->
                          <div class="flex items-center justify-center w-full h-full">
                              <svg class="w-24 h-24 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  @if($listing->product->type === 'oil')
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                  @else
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                                  @endif
                              </svg>
                          </div>
                      @endif
                  </div>
                  
                  <div class="p-4">
                      @php
                          $cardProductType = $listing->product->type === 'olive' ? (app()->getLocale() === 'ar' ? 'زيتون' : (app()->getLocale() === 'fr' ? 'Olives' : 'Olives')) : (app()->getLocale() === 'ar' ? 'زيت زيتون' : (app()->getLocale() === 'fr' ? 'Huile d\'olive' : 'Olive Oil'));
                          $cardQuality = $listing->product->quality ? ' ' . $listing->product->quality : '';
                          $cardCity = $listing->seller->addresses->first() ? (app()->getLocale() === 'ar' ? ' من ' : ' from ') . ($listing->seller->addresses->first()->governorate ?? '') : '';
                      @endphp
                      <div class="font-bold text-lg text-gray-900 mb-2 leading-tight">
                          {{ $cardProductType }} {{ $listing->product->variety }}{{ $cardQuality }}{{ $cardCity }}
                      </div>
                      
                      <div class="flex items-center gap-2 mb-3 flex-wrap">
                          <span class="px-2 py-1 rounded-full bg-[#6A8F3B] text-white text-xs font-semibold flex items-center gap-1">
                              <span>{{ $listing->seller?->role === 'farmer' ? '🌿' : ($listing->seller?->role === 'mill' ? '🏭' : ($listing->seller?->role === 'packer' ? '📦' : '👤')) }}</span>
                              <span>{{ $listing->seller?->farm_name ?? $listing->seller?->mill_name ?? $listing->seller?->company_name ?? $listing->seller?->name ?? __('Seller') }}</span>
                          </span>
                          @if($listing->product->quality)
                              <span class="px-2 py-1 rounded-full bg-[#C8A356] text-white text-xs font-semibold">
                                  @if($listing->product->quality === 'bio')
                                      {{ __('بيولوجي (Bio)') }}
                                  @elseif($listing->product->quality === 'evoo')
                                      {{ __('بكر ممتاز (EVOO)') }}
                                  @elseif($listing->product->quality === 'virgin')
                                      {{ __('بكر (Virgin)') }}
                                  @elseif($listing->product->quality === 'raffinee')
                                      {{ __('مكرر (Raffinée)') }}
                                  @elseif($listing->product->quality === 'pomace')
                                      {{ __('زيت فيتورة (Pomace)') }}
                                  @else
                                      {{ $listing->product->quality }}
                                  @endif
                              </span>
                          @endif
                      </div>
                      
                      @if($listing->product->price)
                          <div class="text-lg font-bold text-[#6A8F3B] mb-3">
                              {{ number_format($listing->product->price, 2) }} {{ app()->getLocale() === 'ar' ? 'دينار' : 'TND' }}
                          </div>
                      @endif
                      
                      <div class="text-sm text-gray-600 mb-3">
                          <div class="flex items-center gap-2">
                              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                              </svg>
                              <span>{{ $listing->seller->name ?? __('Seller') }}</span>
                          </div>
                          <div class="flex items-center gap-2 mt-1">
                              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                              </svg>
                              <span>{{ $listing->created_at->diffForHumans() }}</span>
                          </div>
                      </div>
                      
                      @if($listing->delivery_options)
                          @php
                              $__dloc = app()->getLocale();
                              $__dlabels = $__dloc === 'fr' ? [
                                  'pickup' => '📍 Sur place',
                                  'local_delivery' => '🚚 Livraison',
                                  'export' => '🚢 Export',
                                  'carrier' => '📦 Transporteur',
                              ] : ($__dloc === 'en' ? [
                                  'pickup' => '📍 Pickup',
                                  'local_delivery' => '🚚 Local Delivery',
                                  'export' => '🚢 Export',
                                  'carrier' => '📦 Carrier',
                              ] : [
                                  'pickup' => '📍 استلام',
                                  'local_delivery' => '🚚 توصيل',
                                  'export' => '🚢 تصدير',
                                  'carrier' => '📦 ناقل',
                              ]);
                              $__draww = $listing->delivery_options;
                              $__dkeys = is_array($__draww) ? $__draww
                                  : (is_string($__draww) ? (json_decode($__draww, true) ?: preg_split('/\s*,\s*/', $__draww, -1, PREG_SPLIT_NO_EMPTY)) : []);
                          @endphp
                          <div class="flex flex-wrap gap-1 mb-3">
                              @foreach($__dkeys as $__dk)
                                  @if(isset($__dlabels[trim($__dk)]))
                                      <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                          {{ $__dlabels[trim($__dk)] }}
                                      </span>
                                  @endif
                              @endforeach
                          </div>
                      @endif

                      <div class="mt-2">
                          <a href="{{ route('listings.show', $listing) }}" class="block w-full text-center px-4 py-2 bg-[#6A8F3B] text-white rounded-lg hover:bg-[#5a7a2f] transition font-semibold">
                              {{ app()->getLocale() === 'fr' ? 'Voir les détails' : (app()->getLocale() === 'en' ? 'View Details' : 'عرض التفاصيل') }}
                          </a>
                      </div>
                  </div>
              @endif
          </div>
        @endforeach
      </div>
      <div class="mt-8 bg-white/90 p-4 rounded-xl shadow-sm">{{ $listings->withQueryString()->links() }}</div>
    @else
      <div class="bg-white/90 p-12 rounded-2xl text-center shadow-sm border border-gray-100">
          <div class="text-6xl mb-4">🔍</div>
          <p class="text-xl font-bold text-gray-600">{{ __('No matching results for your search criteria.') }}</p>
          <a href="{{ route('catalog') }}" class="inline-block mt-4 text-[#6A8F3B] font-bold hover:underline">{{ __('Reset Search') }}</a>
      </div>
    @endif
  </main>
</div>
@endsection
