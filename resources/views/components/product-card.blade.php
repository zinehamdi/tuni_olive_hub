@props([
    'title' => 'منتج', 
    'price' => '', 
    'variety' => '', 
    'quality' => '', 
    'image' => null, 
    'media' => null,
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
            <!-- Fallback icon if no image available -->
            <div class="flex items-center justify-center w-full h-full">
                @if($productType === 'oil')
                    <svg class="w-16 h-16 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                @else
                    <svg class="w-16 h-16 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                    </svg>
                @endif
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
