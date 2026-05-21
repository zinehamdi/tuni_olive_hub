import re

with open('resources/views/home.blade.php', 'r') as f:
    content = f.read()

# Update the related product card
old_home_card = """                                    <div>
                                        <h3 class="font-bold text-gray-900 mb-1 line-clamp-1 group-hover:text-[#6A8F3B] transition">{{ $varietyName }}</h3>
                                        <div class="flex items-center gap-1 text-sm text-gray-500 mb-2">"""
new_home_card = """                                    <div>
                                        <h3 class="font-bold text-gray-900 mb-1 line-clamp-1 group-hover:text-[#6A8F3B] transition">{{ $varietyName }}</h3>
                                        
                                        <!-- Seller info and Quality -->
                                        <div class="flex items-center flex-wrap gap-2 mt-2 mb-2">
                                            <div class="text-[10px] font-bold bg-gray-100 text-gray-600 px-2 py-0.5 rounded-md flex items-center gap-1">
                                                <span>{{ $listing->seller?->role === 'farmer' ? '🌿' : ($listing->seller?->role === 'mill' ? '🏭' : ($listing->seller?->role === 'packer' ? '📦' : '👤')) }}</span>
                                                <span>{{ $listing->seller?->farm_name ?? $listing->seller?->mill_name ?? $listing->seller?->company_name ?? $listing->seller?->name ?? __('Seller') }}</span>
                                            </div>
                                            @if($listing->product?->quality)
                                                <div class="text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-100 px-2 py-0.5 rounded-md">
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
                                                </div>
                                            @endif
                                        </div>

                                        <div class="flex items-center gap-1 text-sm text-gray-500 mb-2">"""

if old_home_card in content:
    content = content.replace(old_home_card, new_home_card)
    with open('resources/views/home.blade.php', 'w') as f:
        f.write(content)
    print("Updated home.blade.php")
else:
    print("Could not find the card block in home.blade.php")

