@props([
    'title' => 'منتج', 
    'price' => '', 
    'variety' => '', 
    'quality' => '', 
    'image' => null, 
    'media' => null,
    'seller' => null,
    'productType' => 'oil'
])

<div class="border rounded-lg overflow-hidden bg-white shadow-sm hover:shadow-lg transition-shadow duration-300">
    <!-- Product Image -->
    <div class="relative w-full aspect-video bg-gradient-to-br from-[#6A8F3B] to-[#C8A356] overflow-hidden">
        @php
            // Get image from media array or direct image prop
            $imageUrl = null;
            if ($image) {
                $imageUrl = $image;
            } elseif ($media && is_array($media) && count($media) > 0) {
                $imageUrl = Storage::url($media[0]);
            }
        @endphp
        
        @if($imageUrl)
            <!-- Display actual product image -->
            <img src="{{ $imageUrl }}" 
                 alt="{{ $title }}" 
                 class="w-full h-full object-cover"
                 loading="lazy">
            
            <!-- Product Type Badge -->
            @if($productType === 'oil')
                <span class="absolute bottom-2 right-2 bg-white/90 text-[#6A8F3B] px-2 py-1 rounded-lg text-xs font-bold shadow-md">🫗 {{ __('Oil') }}</span>
            @else
                <span class="absolute bottom-2 right-2 bg-white/90 text-[#6A8F3B] px-2 py-1 rounded-lg text-xs font-bold shadow-md">🫒 {{ __('Olives') }}</span>
            @endif
        @else
            @php
                $type = $productType ?: ($listing->category ?? $listing->product?->type ?? 'oil');
                $varietyText = $variety ?: ($seller->name ?? $title);
                $words = preg_split('/\s+/', trim($varietyText));
                $initials = count($words) >= 2 ? mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1) : mb_substr($varietyText, 0, 2);
                $initials = mb_strtoupper($initials);
                $bgStyle = ($type === 'olive') 
                    ? 'background: linear-gradient(135deg, #143618 0%, #2A5C2E 50%, #47824B 100%);' 
                    : 'background: linear-gradient(135deg, #7A5A1B 0%, #C8A356 50%, #4F6C28 100%);';
            @endphp
            <div class="w-full h-full flex flex-col items-center justify-center p-3 relative overflow-hidden" style="{{ $bgStyle }}">
                <div class="w-14 h-14 rounded-full bg-white/20 backdrop-blur-md border border-white/40 flex items-center justify-center shadow-lg mb-1">
                    <span class="text-white text-base font-black uppercase">{{ $initials }}</span>
                </div>
                <span class="text-white/90 text-[10px] font-bold px-2 py-0.5 rounded-full bg-black/20 backdrop-blur-sm border border-white/10">
                    {{ $type === 'olive' ? '🫒 ' . (app()->getLocale() === 'ar' ? 'زيتون' : __('Olives')) : '🫗 ' . (app()->getLocale() === 'ar' ? 'زيت زيتون' : __('Olive Oil')) }}
                </span>
            </div>
        @endif
    </div>
    
    <!-- Product Info -->
    <div class="p-3 {{ app()->getLocale() === 'ar' ? 'text-right' : '' }}">
        <div class="font-semibold text-gray-900 mb-2">{{ $title }}</div>
        
        <!-- Variety & Quality Badges -->
        @if($variety || $quality)
            <div class="flex items-center gap-2 mb-2 {{ app()->getLocale() === 'ar' ? 'flex-row-reverse' : '' }}">
                @if($variety)
                    <span class="px-2 py-0.5 rounded bg-[#6A8F3B] text-white text-xs font-medium">{{ $variety }}</span>
                @endif
                @if($quality)
                    <span class="px-2 py-0.5 rounded bg-[#C8A356] text-white text-xs font-medium">{{ $quality }}</span>
                @endif
            </div>
        @endif
        
        <!-- Price -->
        @if($price)
            <div class="mt-2 font-bold text-[#6A8F3B] text-lg">{{ $price }}</div>
        @endif
        
        <!-- Slot for additional content (buttons, etc.) -->
        {{ $slot }}
    </div>
</div>
