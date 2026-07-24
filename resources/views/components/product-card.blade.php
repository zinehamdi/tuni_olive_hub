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
            @if($seller && isset($seller->profile_picture) && \Illuminate\Support\Facades\Storage::disk('public')->exists($seller->profile_picture))
                <div class="w-full h-full flex flex-col items-center justify-center p-3 relative overflow-hidden bg-gradient-to-br {{ $productType === 'olive' ? 'from-[#1B3A1E] via-[#2D5A2F] to-[#528A42]' : 'from-[#7A5A1B] via-[#C8A356] to-[#5A7A2F]' }}">
                    <img src="{{ Storage::url($seller->profile_picture) }}" class="w-16 h-16 rounded-full object-cover border-2 border-white/90 shadow-lg relative z-10">
                </div>
            @else
                @php
                    $varietyText = $variety ?: ($seller->name ?? $title);
                    $words = preg_split('/\s+/', trim($varietyText));
                    $initials = count($words) >= 2 ? mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1) : mb_substr($varietyText, 0, 2);
                    $initials = mb_strtoupper($initials);
                @endphp
                <div class="w-full h-full flex flex-col items-center justify-center p-3 relative overflow-hidden bg-gradient-to-br {{ $productType === 'olive' ? 'from-[#143618] via-[#2A5C2E] to-[#47824B]' : 'from-[#8B6B23] via-[#C8A356] to-[#4F6C28]' }}">
                    <div class="w-14 h-14 rounded-full bg-white/20 backdrop-blur-md border border-white/40 flex items-center justify-center shadow-lg mb-1">
                        <span class="text-white text-base font-black uppercase">{{ $initials }}</span>
                    </div>
                </div>
            @endif
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
