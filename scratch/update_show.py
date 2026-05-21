import re

with open('resources/views/listings/show.blade.php', 'r') as f:
    content = f.read()

# Update main title quality mapping
old_quality_code = """                            $qualityLabel = $listing->product->quality ? ' ' . $listing->product->quality : '';"""
new_quality_code = """                            $q = $listing->product->quality;
                            $qualityLabel = '';
                            if($q === 'bio') $qualityLabel = ' - ' . __('بيولوجي (Bio)');
                            elseif($q === 'evoo') $qualityLabel = ' - ' . __('بكر ممتاز (EVOO)');
                            elseif($q === 'virgin') $qualityLabel = ' - ' . __('بكر (Virgin)');
                            elseif($q === 'raffinee') $qualityLabel = ' - ' . __('مكرر (Raffinée)');
                            elseif($q === 'pomace') $qualityLabel = ' - ' . __('زيت فيتورة (Pomace)');
                            elseif($q) $qualityLabel = ' - ' . $q;"""
content = content.replace(old_quality_code, new_quality_code)

# Update the related product card
old_related_card = """                        <div class="p-4">
                            <h3 class="font-bold text-gray-900 mb-1 line-clamp-1">{{ $relatedVariety }}</h3>
                            <div class="flex items-center justify-between text-sm text-gray-600 mb-3">"""
new_related_card = """                        <div class="p-4">
                            <h3 class="font-bold text-gray-900 mb-1 line-clamp-1">{{ $relatedVariety }}</h3>
                            
                            <!-- Seller info and Quality -->
                            <div class="flex items-center flex-wrap gap-2 mt-2 mb-2">
                                <div class="text-[10px] font-bold bg-gray-100 text-gray-600 px-2 py-0.5 rounded-md flex items-center gap-1">
                                    <span>{{ $related->seller?->role === 'farmer' ? '🌿' : ($related->seller?->role === 'mill' ? '🏭' : ($related->seller?->role === 'packer' ? '📦' : '👤')) }}</span>
                                    <span>{{ $related->seller?->farm_name ?? $related->seller?->mill_name ?? $related->seller?->company_name ?? $related->seller?->name ?? __('Seller') }}</span>
                                </div>
                                @if($related->product?->quality)
                                    <div class="text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-100 px-2 py-0.5 rounded-md">
                                        @if($related->product->quality === 'bio')
                                            {{ __('بيولوجي (Bio)') }}
                                        @elseif($related->product->quality === 'evoo')
                                            {{ __('بكر ممتاز (EVOO)') }}
                                        @elseif($related->product->quality === 'virgin')
                                            {{ __('بكر (Virgin)') }}
                                        @elseif($related->product->quality === 'raffinee')
                                            {{ __('مكرر (Raffinée)') }}
                                        @elseif($related->product->quality === 'pomace')
                                            {{ __('زيت فيتورة (Pomace)') }}
                                        @else
                                            {{ $related->product->quality }}
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <div class="flex items-center justify-between text-sm text-gray-600 mb-3">"""
content = content.replace(old_related_card, new_related_card)

with open('resources/views/listings/show.blade.php', 'w') as f:
    f.write(content)

