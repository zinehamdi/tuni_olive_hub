@extends('layouts.app')
@section('content')
<div class="max-w-7xl mx-auto py-8 px-4">
    @if(session('success'))
        <div class="bg-gradient-to-r from-green-400 to-green-600 border border-green-600 text-white px-6 py-4 rounded-xl mb-6 shadow-lg animate-bounce-once">
            <div class="flex items-center">
                <svg class="w-6 h-6 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-lg font-bold">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <style>
    @keyframes bounce-once {
        0%, 100% { transform: translateY(0); }
        25% { transform: translateY(-10px); }
        50% { transform: translateY(0); }
        75% { transform: translateY(-5px); }
    }
    .animate-bounce-once {
        animation: bounce-once 0.8s ease-in-out;
    }
    .new-listing-highlight {
        animation: highlight-fade 3s ease-out;
    }
    @keyframes highlight-fade {
        0% { background-color: #d1fae5; transform: scale(1.02); }
        100% { background-color: white; transform: scale(1); }
    }
    </style>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Card -->
        <div class="lg:col-span-1">
            <div class="bg-gradient-to-br from-[#F8F4EC] to-[#EEF5E9] rounded-2xl shadow-xl p-6">
                <h1 class="text-2xl font-bold text-center mb-6 text-[#C8A356]">الملف الشخصي</h1>
                <div class="flex flex-col items-center mb-6">
                    @if($user->profile_picture)
                        <img src="{{ Storage::url($user->profile_picture) }}" alt="Profile" class="w-32 h-32 rounded-full object-cover border-4 border-[#C8A356] mb-2">
                    @else
                        <div class="w-32 h-32 rounded-full bg-gray-200 flex items-center justify-center text-4xl text-gray-400 mb-2">
                            <svg class="w-20 h-20" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    @endif
                    <div class="text-xl font-bold">{{ $user->name }}</div>
                    <div class="text-gray-600">
                        @if($user->role === 'farmer')
                            {{ __('Farmer') }}
                        @elseif($user->role === 'carrier')
                            {{ __('Carrier') }}
                        @elseif($user->role === 'mill')
                            {{ __('Mill') }}
                        @elseif($user->role === 'packer')
                            {{ __('Packer') }}
                        @else
                            {{ __('User') }}
                        @endif
                    </div>
                </div>
                <div class="space-y-3">
                    <div><span class="font-bold">البريد الإلكتروني:</span> {{ $user->email }}</div>
                    @if($user->role === 'farmer')
                        <div><span class="font-bold">مكان الضيعة:</span> {{ $user->farm_location ?? '-' }}</div>
                        <div><span class="font-bold">عدد أشجار الزيتون:</span> {{ $user->tree_number ?? '-' }}</div>
                        <div><span class="font-bold">نوع الزيتون:</span> {{ $user->olive_type ?? '-' }}</div>
                    @elseif($user->role === 'carrier')
                        <div><span class="font-bold">سعة الشاحنة:</span> {{ $user->camion_capacity ?? '-' }} طن</div>
                    @elseif($user->role === 'mill')
                        <div><span class="font-bold">اسم المعصرة:</span> {{ $user->mill_name ?? '-' }}</div>
                    @endif
                </div>
                <div class="mt-6">
                    <a href="{{ route('profile.edit') }}" class="block w-full text-center bg-[#C8A356] text-white font-bold py-2 rounded-xl hover:bg-[#b08a3c] transition">
                        تعديل الملف الشخصي
                    </a>
                </div>
            </div>
        </div>

        <!-- Listings Section -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-xl p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-[#C8A356]">إعلاناتي</h2>
                    <a href="{{ route('listings.create') }}" class="bg-[#6A8F3B] text-white font-bold py-2 px-6 rounded-xl hover:bg-[#5a7a2f] transition">
                        إضافة إعلان جديد
                    </a>
                </div>

                @if($listings->count() > 0)
                    <div class="grid gap-4 md:grid-cols-2">
                        @foreach($listings as $index => $listing)
                            <div class="border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg transition {{ $index === 0 && session('success') ? 'new-listing-highlight border-green-400' : '' }}">
                                <div class="flex">
                                    <!-- Product Image/Icon -->
                                    <div class="w-32 h-32 bg-gradient-to-br from-[#6A8F3B] to-[#C8A356] flex items-center justify-center flex-shrink-0">
                                        <svg class="w-16 h-16 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            @if($listing->product && $listing->product->type === 'oil')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                                            @endif
                                        </svg>
                                    </div>
                                    
                                    <!-- Product Details -->
                                    <div class="flex-1 p-4">
                                        <div class="flex justify-between items-start mb-2">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <h3 class="text-lg font-bold text-gray-900">
                                                    @if($listing->product)
                                                        {{ $listing->product->variety }}
                                                        @if($listing->product->quality)
                                                            <span class="text-sm text-gray-600">- {{ $listing->product->quality }}</span>
                                                        @endif
                                                    @else
                                                        إعلان #{{ $listing->id }}
                                                    @endif
                                                </h3>
                                                @if($index === 0 && session('success'))
                                                    <span class="bg-green-500 text-white text-xs px-2 py-1 rounded-full font-bold">جديد</span>
                                                @endif
                                            </div>
                                            
                                            <div class="flex gap-2">
                                                <a href="{{ route('listings.edit', $listing) }}" class="text-blue-600 hover:text-blue-800" title="تعديل">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                                <form action="{{ route('listings.destroy', $listing) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الإعلان؟');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-800" title="حذف">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        
                                        <div class="flex flex-wrap gap-2 mb-2">
                                            @if($listing->product)
                                                <span class="bg-[#6A8F3B] text-white text-xs px-2 py-1 rounded-full font-semibold">
                                                    {{ $listing->product->type === 'olive' ? 'زيتون' : 'زيت زيتون' }}
                                                </span>
                                            @endif
                                            @if($listing->status === 'active')
                                                <span class="bg-green-500 text-white text-xs px-2 py-1 rounded-full">نشط</span>
                                            @else
                                                <span class="bg-gray-400 text-white text-xs px-2 py-1 rounded-full">{{ $listing->status }}</span>
                                            @endif
                                        </div>
                                        
                                        @if($listing->product && $listing->product->price)
                                            <div class="text-lg font-bold text-[#6A8F3B] mb-2">
                                                {{ number_format($listing->product->price, 2) }} دينار
                                            </div>
                                        @endif
                                        
                                        <div class="flex gap-4 text-sm text-gray-600">
                                            @if($listing->min_order)
                                                <span>📊 حد أدنى: {{ $listing->min_order }}</span>
                                            @endif
                                            <span>🕒 {{ $listing->created_at->diffForHumans() }}</span>
                                        </div>
                                        
                                        @if($listing->payment_methods)
                                            <div class="mt-2 text-xs text-gray-500">
                                                <span class="font-semibold">طرق الدفع:</span> 
                                                {{ is_array($listing->payment_methods) ? implode('، ', $listing->payment_methods) : $listing->payment_methods }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $listings->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        <p class="text-gray-600 text-lg mb-4">لا توجد إعلانات حتى الآن</p>
                        <a href="{{ route('listings.create') }}" class="inline-block bg-[#6A8F3B] text-white font-bold py-2 px-6 rounded-xl hover:bg-[#5a7a2f] transition">
                            أضف إعلانك الأول
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
