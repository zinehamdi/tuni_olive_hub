import re

with open('resources/views/home.blade.php', 'r') as f:
    content = f.read()

old_block = """                                <div class="flex items-center gap-2 mb-3 flex-wrap">
                                    <span class="px-2 py-1 rounded-full bg-[#6A8F3B] text-white text-xs font-semibold">
                                        {{ $listing->product->type === 'olive' ? 'زيتون' : 'زيت زيتون' }}
                                    </span>
                                    @if($listing->product->quality)
                                        <span class="px-2 py-1 rounded-full bg-[#C8A356] text-white text-xs font-semibold">
                                            {{ $listing->product->quality }}
                                        </span>
                                    @endif"""

new_block = """                                <div class="flex items-center gap-2 mb-3 flex-wrap">
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
                                    @endif"""

if old_block in content:
    content = content.replace(old_block, new_block)
    with open('resources/views/home.blade.php', 'w') as f:
        f.write(content)
    print("Updated home.blade.php")
else:
    print("Not found in home.blade.php")

