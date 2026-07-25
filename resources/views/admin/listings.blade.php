@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-100 py-8">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">{{ __('Manage Listings') }}</h1>
                <p class="text-gray-600">{{ __('Approve, reject, or delete listings') }}</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition font-bold">
                ← {{ __('Back to Dashboard') }}
            </a>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-6">
            <form method="GET" action="{{ route('admin.listings') }}" class="flex flex-wrap gap-4">
                <!-- Status Filter -->
                <select name="status" class="px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-[#6A8F3B] focus:ring-4 focus:ring-[#6A8F3B]/20">
                    <option value="all">{{ __('All Statuses') }}</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                    <option value="sold" {{ request('status') === 'sold' ? 'selected' : '' }}>{{ __('Sold') }}</option>
                </select>

                <!-- Search -->
                <input type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="{{ __('Search by product or seller name') }}" 
                    class="flex-1 px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-[#6A8F3B] focus:ring-4 focus:ring-[#6A8F3B]/20">

                <button type="submit" class="px-6 py-2 bg-[#6A8F3B] text-white rounded-xl hover:bg-[#5a7a2f] transition font-bold">
                    {{ __('Filter') }}
                </button>

                @if(request('status') || request('search'))
                <a href="{{ route('admin.listings') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition font-bold">
                    {{ __('Clear') }}
                </a>
                @endif
            </form>
        </div>

        <!-- Listings Grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($listings as $listing)
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition">
                <!-- Product Image/Icon -->
                <div class="h-48 bg-gradient-to-br from-[#6A8F3B] to-[#C8A356] flex items-center justify-center relative">
                    @php
    $media = $listing->media[0] ?? null;
    $fallbackImage = 'https://toop.kairouanhub.com/storage/listings/23/28bc3509-9426-4f36-9e71-fd694f3cbc45.webp';
    if ($media) {
        $thumb = preg_replace('/^(listings\/\d+)\/([^\/]+)\.[a-z0-9]+$/i', '$1/thumbs/$2_sm.webp', $media);
    }
@endphp
@if($media)
    <img src="{{ asset('storage/' . $thumb) }}"
         onerror="this.onerror=null;this.src='{{ asset('storage/' . $media) }}';"
         alt="{{ $listing->product->variety }}"
         class="w-full h-full object-cover"
         loading="lazy">
@else
    @if($listing->seller && $listing->seller->profile_picture && \Illuminate\Support\Facades\Storage::disk('public')->exists($listing->seller->profile_picture))
        <div class="w-full h-full flex flex-col items-center justify-center p-3 relative overflow-hidden bg-gradient-to-br {{ $listing->product?->type === 'olive' ? 'from-[#1B3A1E] via-[#2D5A2F] to-[#528A42]' : 'from-[#7A5A1B] via-[#C8A356] to-[#5A7A2F]' }}">
            <img src="{{ Storage::url($listing->seller->profile_picture) }}" class="w-20 h-20 rounded-full object-cover border-4 border-white/90 shadow-xl relative z-10">
        </div>
    @else
        @php
            $type = $listing->product?->type ?? 'oil';
            $variety = $listing->product?->variety ?? $listing->seller?->name ?? 'Zintoop';
            $words = preg_split('/\s+/', trim($variety));
            $initials = count($words) >= 2 ? mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1) : mb_substr($variety, 0, 2);
            $initials = mb_strtoupper($initials);
        @endphp
        <div class="w-full h-full flex flex-col items-center justify-center p-3 relative overflow-hidden bg-gradient-to-br {{ $type === 'olive' ? 'from-[#143618] via-[#2A5C2E] to-[#47824B]' : 'from-[#8B6B23] via-[#C8A356] to-[#4F6C28]' }}">
            <div class="w-16 h-16 rounded-full bg-white/20 backdrop-blur-md border border-white/40 flex items-center justify-center shadow-lg mb-1">
                <span class="text-white text-lg font-black uppercase">{{ $initials }}</span>
            </div>
            <span class="text-white/90 text-[10px] font-bold px-2 py-0.5 rounded-full bg-black/20 backdrop-blur-sm border border-white/10">
                {{ $type === 'olive' ? '🫒 ' . (app()->getLocale() === 'ar' ? 'زيتون' : __('Olives')) : '🫗 ' . (app()->getLocale() === 'ar' ? 'زيت زيتون' : __('Olive Oil')) }}
            </span>
        </div>
    @endif
@endif
                    
                    <!-- Status Badge -->
                    <div class="absolute top-4 right-4">
                        <span class="px-3 py-1 rounded-full text-xs font-bold
                            {{ $listing->status === 'active' ? 'bg-green-500 text-white' : '' }}
                            {{ $listing->status === 'pending' ? 'bg-orange-500 text-white' : '' }}
                            {{ $listing->status === 'inactive' ? 'bg-gray-500 text-white' : '' }}
                            {{ $listing->status === 'sold' ? 'bg-blue-500 text-white' : '' }}">
                            {{ ucfirst($listing->status) }}
                        </span>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __($listing->product->variety) }}</h3>
                    
                    <div class="flex items-center gap-2 mb-3">
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-semibold">
                            {{ $listing->product->type === 'oil' ? __('Olive Oil') : __('Olives') }}
                        </span>
                        @if($listing->product->quality)
                        <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs font-semibold">
                            {{ $listing->product->quality }}
                        </span>
                        @endif
                    </div>

                    <div class="text-2xl font-bold text-[#6A8F3B] mb-4">
                        {{ number_format($listing->product->price, 2) }} {{ app()->getLocale() === 'ar' ? 'دينار' : __('TND') }}/
                        @php
                            $unitLabel = $listing->unit === 'liter'
                                ? (app()->getLocale() === 'ar' ? 'لتر' : 'L')
                                : (app()->getLocale() === 'ar' ? 'كلغ' : 'kg');
                        @endphp
                        {{ $unitLabel }}
                    </div>

                    <!-- Seller Info -->
                    <div class="flex items-center gap-2 mb-4 pb-4 border-b border-gray-200">
                        <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center text-gray-600 font-bold text-sm">
                            {{ substr($listing->seller->name, 0, 1) }}
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-gray-900">
    @if($listing->seller->role !== 'admin')
      <a href="{{ route('user.profile', $listing->seller) }}" target="_blank" class="hover:underline">{{ $listing->seller->name }}</a>
    @else
      {{ __('Seller') }}
    @endif
  </div>
  <div class="text-xs text-gray-600">
    {{ $listing->seller->role === 'admin' ? __('Seller') : ucfirst($listing->seller->role) }}
  </div>
                        </div>
                    </div>

                    <!-- Details -->
                    <div class="space-y-2 mb-4 text-sm">
                        @if($listing->product->quantity)
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">{{ __('Quantity:') }}</span>
                            <span class="font-semibold text-gray-900">{{ $listing->product->quantity }} {{ __('kg') }}</span>
                        </div>
                        @endif
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">{{ __('Posted:') }}</span>
                            <span class="font-semibold text-gray-900">{{ $listing->created_at->diffForHumans() }}</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col gap-2">
                        @if($listing->status === 'pending')
                        <div class="flex gap-2">
                            <form action="{{ route('admin.listings.approve', $listing) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition font-bold">
                                    {{ __('Approve') }}
                                </button>
                            </form>
                            <form action="{{ route('admin.listings.reject', $listing) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition font-bold">
                                    {{ __('Reject') }}
                                </button>
                            </form>
                        </div>
                        @endif

                        <!-- Featured Toggle -->
                        <form action="{{ route('admin.listings.toggle_featured', $listing) }}" method="POST" class="w-full">
                            @csrf
                            <button type="submit" class="w-full py-2 px-3 rounded-lg text-xs font-bold transition flex items-center justify-center gap-1.5 {{ $listing->is_featured ? 'bg-amber-500 hover:bg-amber-600 text-white shadow-md' : 'bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-300' }}">
                                <span>⭐</span>
                                <span>{{ $listing->is_featured ? __('إلغاء التمييز (عرض عالي)') : __('عرض مميز ⭐ (إظهار في Top 6)') }}</span>
                            </button>
                        </form>

                        <div class="flex gap-2">
                            <a href="{{ route('listings.show', $listing) }}" class="flex-1 text-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition font-bold">
                                {{ __('View') }}
                            </a>
                            <a href="{{ route('admin.listings.edit', $listing) }}" class="flex-1 text-center px-4 py-2 bg-gray-100 text-gray-900 border border-gray-200 rounded-lg hover:bg-gray-200 transition font-bold">
                                {{ __('Edit') }}
                            </a>
                            <form action="{{ route('admin.listings.delete', $listing) }}" method="POST" class="flex-1" onsubmit="return confirm('{{ __('Are you sure you want to delete this listing?') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition font-bold">
                                    {{ __('Delete') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full">
                <div class="bg-white rounded-2xl shadow-xl p-12 text-center">
                    <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <p class="text-gray-500 text-lg">{{ __('No listings found') }}</p>
                </div>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($listings->hasPages())
        <div class="mt-8">
            {{ $listings->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
