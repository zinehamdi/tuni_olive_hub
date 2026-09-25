@props(['listing'])

@php
    $locale = app()->getLocale();
    $variety = $listing->product->variety ?? 'زيت زيتون تونسي';
    $qtyUnit = $listing->quantity_unit_label;
    $priceUnit = $listing->price_unit_label;
    $formattedVal = fmod((float)$listing->price, 1.0) == 0.0 ? number_format((float)$listing->price, 0, '.', '') : ((($listing->currency ?? 'TND') === 'TND') ? number_format((float)$listing->price, 3, '.', '') : number_format((float)$listing->price, 2, '.', ''));
    $currLabel = ($locale === 'ar' && ($listing->currency ?? 'TND') === 'TND') ? 'دينار' : ($listing->currency ?? 'TND');
    $priceText = $listing->price == 0 ? __('Price on request') : ($formattedVal . ' ' . $currLabel . ' / ' . $priceUnit);
    $city = optional($listing->seller->addresses->first())->city ?? optional($listing->seller->addresses->first())->governorate ?? 'Tunisie';

    // Image fallback
    $productImage = null;
    if ($listing->product && $listing->product->media && is_array($listing->product->media) && count($listing->product->media) > 0) {
        $productImage = $listing->product->media[0];
    } elseif ($listing->media && is_array($listing->media) && count($listing->media) > 0) {
        $productImage = $listing->media[0];
    }
    $imageUrl = $productImage ? (str_starts_with($productImage, 'http') ? $productImage : asset('storage/' . ltrim($productImage, '/'))) : asset('images/hero_slide_1.png');
@endphp

<div class="bg-white border border-gray-200 hover:border-[#6A8F3B]/50 rounded-2xl overflow-hidden shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between group">
    <div>
        <div class="relative w-full aspect-video overflow-hidden bg-gray-100">
            <img src="{{ $imageUrl }}" 
                 alt="{{ $variety }}" 
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                 loading="lazy">
            <span class="absolute top-2.5 right-2.5 bg-black/60 backdrop-blur-md text-white text-[10px] font-black px-2.5 py-1 rounded-full uppercase tracking-wider">
                @if($listing->is_saniya)
                    🌿 {{ $locale === 'ar' ? 'سانية للتخضير' : ($locale === 'fr' ? 'Sur pied' : 'Standing Crop') }}
                @elseif($listing->product?->type === 'olive')
                    🫒 {{ __('Olives') }}
                @else
                    🫗 {{ __('Extra Virgin') }}
                @endif
            </span>
            @if($listing->tree_count)
                <span class="absolute bottom-2.5 right-2.5 bg-emerald-900/80 backdrop-blur-md text-white text-[10px] font-bold px-2 py-0.5 rounded-full">
                    🌳 {{ $listing->tree_count }} {{ $locale === 'ar' ? 'شجرة' : __('trees') }}
                </span>
            @endif
            @if($listing->product?->is_organic)
                <span class="absolute top-2.5 left-2.5 bg-emerald-600/90 backdrop-blur-md text-white text-[10px] font-black px-2 py-0.5 rounded-full">
                    🌱 {{ __('Bio / Organic') }}
                </span>
            @endif
        </div>

        <div class="p-4 space-y-2">
            <div class="flex items-center justify-between text-xs text-gray-500">
                <span>📍 {{ $city }}</span>
                <span class="font-bold text-emerald-700">{{ $listing->quantity ? number_format((float)$listing->quantity) . ' ' . $qtyUnit : __('Available') }}</span>
            </div>

            <h3 class="font-bold text-gray-900 text-sm group-hover:text-[#6A8F3B] transition line-clamp-1">
                {{ $variety }}
            </h3>

            <p class="text-xs text-gray-500 line-clamp-2">
                {{ $listing->description ?? (($listing->seller->display_name ?? $listing->seller->name) . ' - ' . __('Verified producer lot with direct mill sourcing.')) }}
            </p>
        </div>
    </div>

    <div class="p-4 pt-0 border-t border-gray-100 flex items-center justify-between mt-2">
        <div>
            <span class="text-[10px] text-gray-400 block font-bold">{{ __('Price') }}</span>
            <span class="text-sm font-black text-gray-900">{{ $priceText }}</span>
        </div>
        <a href="{{ route('listings.show', $listing) }}" class="px-3.5 py-2 bg-[#183b1c] hover:bg-[#6A8F3B] text-white text-xs font-bold rounded-xl shadow-xs transition">
            {{ __('View Lot') }} →
        </a>
    </div>
</div>
