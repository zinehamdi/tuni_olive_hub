import re

files = [
    'resources/views/profile/public.blade.php',
    'resources/views/dashboard.blade.php'
]

def update_file(filename):
    with open(filename, 'r') as f:
        content = f.read()

    # public.blade.php
    if 'profile/public.blade.php' in filename:
        old_title_block = """                                            <h3 class="font-bold text-gray-900 line-clamp-1 group-hover:text-emerald-600 transition">{{ $listing->product?->variety ?? __('Unknown Variety') }}</h3>
                                            <div class="flex items-center gap-1 text-xs text-gray-500 mt-1">"""
        
        new_title_block = """                                            <h3 class="font-bold text-gray-900 line-clamp-1 group-hover:text-emerald-600 transition">{{ $listing->product?->variety ?? __('Unknown Variety') }}</h3>
                                            
                                            <!-- Seller info and Quality -->
                                            <div class="flex items-center flex-wrap gap-2 mt-2">
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

                                            <div class="flex items-center gap-1 text-xs text-gray-500 mt-2">"""
        content = content.replace(old_title_block, new_title_block)
        
    # dashboard.blade.php
    if 'dashboard.blade.php' in filename:
        old_title_block = """                                        <div>
                                            <h3 class="font-bold text-gray-900 line-clamp-1 group-hover:text-[#6A8F3B] transition">{{ $listing->product?->variety ?? __('Unknown Variety') }}</h3>
                                            <div class="flex items-center gap-1 text-xs text-gray-500 mt-1">"""
                                            
        new_title_block = """                                        <div>
                                            <h3 class="font-bold text-gray-900 line-clamp-1 group-hover:text-[#6A8F3B] transition">{{ $listing->product?->variety ?? __('Unknown Variety') }}</h3>
                                            
                                            <!-- Seller info and Quality -->
                                            <div class="flex items-center flex-wrap gap-2 mt-2">
                                                <div class="text-[10px] font-bold bg-gray-100 text-gray-600 px-2 py-0.5 rounded-md flex items-center gap-1">
                                                    <span>{{ Auth::user()->role === 'farmer' ? '🌿' : (Auth::user()->role === 'mill' ? '🏭' : (Auth::user()->role === 'packer' ? '📦' : '👤')) }}</span>
                                                    <span>{{ Auth::user()->farm_name ?? Auth::user()->mill_name ?? Auth::user()->company_name ?? Auth::user()->name }}</span>
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

                                            <div class="flex items-center gap-1 text-xs text-gray-500 mt-2">"""
        content = content.replace(old_title_block, new_title_block)

    with open(filename, 'w') as f:
        f.write(content)

for f in files:
    update_file(f)
print("Updated product cards")
