@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col md:flex-row" dir="{{ __('ltr') }}" x-data="{ sidebarOpen: false }">
    
    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-gray-900/50 z-40 md:hidden" x-transition></div>

    <!-- Sidebar -->
    <aside x-cloak
           x-effect="$el.style.transform = window.innerWidth >= 768 ? '' : (sidebarOpen ? 'translateX(0)' : '{{ __('translateX(-100%)') }}')"
           class="fixed md:sticky top-0 md:top-[72px] bottom-0 md:h-[calc(100vh-72px)] w-72 bg-white shadow-2xl md:shadow-lg z-50 md:z-10 flex flex-col transition-transform duration-300 ltr:left-0 rtl:right-0">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <span class="text-[#6A8F3B]">🛡️</span> {{ __('Admin Panel') }}
            </h2>
            <button @click="sidebarOpen = false" class="md:hidden text-gray-500 hover:text-gray-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <nav class="flex-1 p-4 space-y-2 overflow-y-auto scrollbar-hide">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-[#6A8F3B] rounded-xl font-bold transition group">
                <span class="text-xl group-hover:scale-110 transition">📊</span> {{ __('Dashboard') }}
            </a>
            <a href="{{ route('admin.analytics.visitors') }}" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-[#6A8F3B] rounded-xl font-bold transition group">
                <span class="text-xl group-hover:scale-110 transition">🌍</span> {{ __('Visitor Analytics') }}
            </a>
            <a href="{{ route('admin.analytics.marketing') }}" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-[#6A8F3B] rounded-xl font-bold transition group">
                <span class="text-xl group-hover:scale-110 transition">📈</span> {{ app()->getLocale() === 'ar' ? 'تحليلات التسويق' : __('Marketing Analytics') }}
            </a>
            <a href="{{ route('admin.users') }}" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-[#6A8F3B] rounded-xl font-bold transition group">
                <span class="text-xl group-hover:scale-110 transition">👥</span> {{ __('Manage Users') }}
            </a>
            <a href="{{ route('admin.listings') }}" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-[#6A8F3B] rounded-xl font-bold transition group">
                <span class="text-xl group-hover:scale-110 transition">🏷️</span> {{ __('Manage Listings') }}
            </a>
            <a href="{{ route('admin.prices.souk.index') }}" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-[#6A8F3B] rounded-xl font-bold transition group">
                <span class="text-xl group-hover:scale-110 transition">🫒</span> {{ __('Souk Prices') }}
            </a>
            <a href="{{ route('admin.prices.world.index') }}" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-[#6A8F3B] rounded-xl font-bold transition group">
                <span class="text-xl group-hover:scale-110 transition">🌍</span> {{ __('World Prices') }}
            </a>
            <a href="{{ route('admin.articles.index') }}" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-[#6A8F3B] rounded-xl font-bold transition group">
                <span class="text-xl group-hover:scale-110 transition">📰</span> {{ __('Articles') }}
            </a>
            <a href="{{ route('admin.deals.index') }}" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-[#6A8F3B] rounded-xl font-bold transition group">
                <span class="text-xl group-hover:scale-110 transition">🤝</span> {{ __('Deals') }}
            </a>
            <a href="{{ route('admin.deals.requests.index') }}" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-[#6A8F3B] rounded-xl font-bold transition group">
                <span class="text-xl group-hover:scale-110 transition">📩</span> {{ __('Deal Requests') }}
            </a>
            <a href="{{ route('admin.subscribers.index') }}" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-[#6A8F3B] rounded-xl font-bold transition group">
                <span class="text-xl group-hover:scale-110 transition">📧</span> {{ __('Subscribers') }}
            </a>
            <a href="{{ route('admin.hero-slides.index') }}" class="flex items-center gap-3 px-4 py-3 bg-[#6A8F3B]/10 text-[#6A8F3B] rounded-xl font-bold transition">
                <span class="text-xl">🖼️</span> {{ __('Hero Slides') }}
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 w-full p-4 sm:p-8 min-w-0">
        <!-- Header -->
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl sm:text-4xl font-black text-gray-900 mb-2">{{ __('Manage Hero Slides') }}</h1>
                <p class="text-gray-600 font-medium">{{ __('Add or edit slideshow background images for the home page') }}</p>
            </div>
            <button @click="sidebarOpen = true" class="md:hidden p-3 bg-white rounded-xl shadow-md text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-2xl flex items-center gap-3">
                <span>✅</span>
                <span class="font-bold">{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Upload Forms -->
            <div class="space-y-8">
                <!-- Catalog Hero Form -->
                <div class="bg-white rounded-3xl border border-gray-150 p-6 shadow-sm">
                    <h2 class="text-xl font-extrabold text-gray-900 mb-6 flex items-center gap-2">
                        <span>📖</span> {{ __('Catalog Background') }}
                    </h2>
                    
                    <form action="{{ route('admin.hero-slides.catalog-bg') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        <div x-data="{ isDragOver: false }" class="relative">
                            <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('Background Image') }} <span class="text-red-500">*</span></label>
                            
                            @if(\Illuminate\Support\Facades\Storage::disk('public')->exists('settings/catalog-hero.webp'))
                                <div class="mb-4 aspect-video rounded-xl overflow-hidden shadow-sm relative">
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url('settings/catalog-hero.webp') }}" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-black/20 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                                        <span class="bg-white/90 text-gray-800 text-xs font-bold px-3 py-1.5 rounded-lg">{{ __('Current Background') }}</span>
                                    </div>
                                </div>
                            @endif

                            <div :class="isDragOver ? 'border-[#6A8F3B] bg-[#6A8F3B]/5' : 'border-gray-250 bg-gray-50'"
                                 @dragover.prevent="isDragOver = true"
                                 @dragleave.prevent="isDragOver = false"
                                 @drop.prevent="isDragOver = false; $refs.catalogFileInput.files = $event.dataTransfer.files; $refs.catalogFileInput.dispatchEvent(new Event('change'))"
                                 class="border-2 border-dashed rounded-2xl p-6 text-center cursor-pointer hover:border-[#6A8F3B] transition duration-200 relative">
                                
                                <input type="file" name="catalog_bg" required x-ref="catalogFileInput" @change="if ($refs.catalogFileInput.files[0]) { $refs.catalogFileName.innerText = $refs.catalogFileInput.files[0].name; }" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                
                                <div class="space-y-2">
                                    <span class="text-3xl block">🖼️</span>
                                    <p class="text-xs font-bold text-gray-800">{{ __('Drag & drop image here or click to upload') }}</p>
                                    <p x-ref="catalogFileName" class="text-xs text-[#6A8F3B] font-bold mt-2 truncate"></p>
                                </div>
                            </div>
                            @error('catalog_bg')
                                <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="w-full py-3 bg-gray-900 hover:bg-black text-white rounded-xl font-bold text-sm transition duration-200 shadow-md flex items-center justify-center gap-2">
                            <span>💾</span> {{ __('Update Background') }}
                        </button>
                    </form>
                </div>

                <!-- Add New Slide Form -->
                <div class="bg-white rounded-3xl border border-gray-150 p-6 shadow-sm">
                    <h2 class="text-xl font-extrabold text-gray-900 mb-6 flex items-center gap-2">
                        <span>📤</span> {{ __('Add New Slide') }}
                    </h2>

                <form action="{{ route('admin.hero-slides.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    
                    <!-- Drag and Drop Image Box -->
                    <div x-data="{ isDragOver: false }" class="relative">
                        <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('Slide Image') }} <span class="text-red-500">*</span></label>
                        <div :class="isDragOver ? 'border-[#6A8F3B] bg-[#6A8F3B]/5' : 'border-gray-250 bg-gray-50'"
                             @dragover.prevent="isDragOver = true"
                             @dragleave.prevent="isDragOver = false"
                             @drop.prevent="isDragOver = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'))"
                             class="border-2 border-dashed rounded-2xl p-6 text-center cursor-pointer hover:border-[#6A8F3B] transition duration-200 relative">
                            
                            <input type="file" name="image" required x-ref="fileInput" @change="if ($refs.fileInput.files[0]) { $refs.fileName.innerText = $refs.fileInput.files[0].name; }" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            
                            <div class="space-y-2">
                                <span class="text-4xl block">🖼️</span>
                                <p class="text-sm font-bold text-gray-800">{{ __('Drag & drop image here or click to upload') }}</p>
                                <p class="text-xs text-gray-500">WEBP, PNG, JPG, GIF (Max. 10MB, Recommended 1920x1080)</p>
                                <p x-ref="fileName" class="text-xs text-[#6A8F3B] font-bold mt-2 truncate"></p>
                            </div>
                        </div>
                        @error('image')
                            <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-bold text-gray-700 mb-2">{{ __('Title') }} ({{ __('Optional') }})</label>
                        <input type="text" id="title" name="title" placeholder="{{ __('Enter slide title') }}" class="w-full bg-gray-50 border border-gray-250 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#6A8F3B]">
                        @error('title')
                            <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Subtitle -->
                    <div>
                        <label for="subtitle" class="block text-sm font-bold text-gray-700 mb-2">{{ __('Subtitle') }} ({{ __('Optional') }})</label>
                        <input type="text" id="subtitle" name="subtitle" placeholder="{{ __('Enter slide subtitle') }}" class="w-full bg-gray-50 border border-gray-250 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#6A8F3B]">
                        @error('subtitle')
                            <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Order -->
                    <div>
                        <label for="order" class="block text-sm font-bold text-gray-700 mb-2">{{ __('Display Order') }}</label>
                        <input type="number" id="order" name="order" value="0" min="0" class="w-full bg-gray-50 border border-gray-250 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#6A8F3B]">
                        @error('order')
                            <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Active Toggle -->
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm font-bold text-gray-700">{{ __('Publish immediately') }}</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" checked class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#6A8F3B]"></div>
                        </label>
                    </div>

                    <button type="submit" class="w-full py-3.5 bg-[#6A8F3B] hover:bg-[#5a7a2f] text-white rounded-xl font-bold text-sm transition duration-200 shadow-md hover:shadow-lg active:scale-[0.98] flex items-center justify-center gap-2 cursor-pointer">
                        <span>🚀</span> {{ __('Publish Slide') }}
                    </button>
                </form>
            </div>
            </div> <!-- End space-y-8 -->

            <!-- Slides List -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-3xl border border-gray-150 p-6 shadow-sm">
                    <h2 class="text-xl font-extrabold text-gray-900 mb-6 flex items-center gap-2">
                        <span>🖼️</span> {{ __('Current Slides') }}
                        <span class="text-sm font-bold text-gray-500 bg-gray-100 px-3 py-1 rounded-full">{{ $slides->count() }}</span>
                    </h2>

                    @if($slides->isEmpty())
                        <div class="text-center py-12 text-gray-500">
                            <span class="text-5xl block mb-4">🏜️</span>
                            <p class="font-semibold">{{ __('No slides uploaded yet.') }}</p>
                            <p class="text-sm text-gray-400 mt-1">{{ __('Add slides using the upload box on the left.') }}</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            @foreach($slides as $slide)
                                <div class="bg-gray-50 rounded-2xl border border-gray-200 overflow-hidden shadow-sm flex flex-col group relative" x-data="{ editing: false }">
                                    
                                    <!-- Slide Preview Image -->
                                    <div class="aspect-video relative overflow-hidden bg-gray-800">
                                        <img src="{{ $slide->image_url }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="Slide preview">
                                        
                                        <!-- Active Badge -->
                                        <div class="absolute top-3 left-3">
                                            @if($slide->is_active)
                                                <span class="px-2.5 py-1 rounded-full bg-green-500 text-white font-bold text-[10px] uppercase shadow-sm">
                                                    {{ __('Active') }}
                                                </span>
                                            @else
                                                <span class="px-2.5 py-1 rounded-full bg-gray-500 text-white font-bold text-[10px] uppercase shadow-sm">
                                                    {{ __('Inactive') }}
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Actions overlay -->
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center gap-3">
                                            <button @click="editing = true" class="p-2.5 bg-white text-gray-800 hover:bg-[#6A8F3B] hover:text-white rounded-xl transition duration-200 cursor-pointer shadow">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>
                                            
                                            <form action="{{ route('admin.hero-slides.destroy', $slide) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this slide?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2.5 bg-red-600 text-white hover:bg-red-700 rounded-xl transition duration-200 cursor-pointer shadow">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Content Info -->
                                    <div class="p-4 flex-1 flex flex-col justify-between">
                                        <div>
                                            <h3 class="font-bold text-gray-900 truncate">{{ $slide->title ?: __('No Title') }}</h3>
                                            <p class="text-xs text-gray-500 truncate mt-1">{{ $slide->subtitle ?: __('No Subtitle') }}</p>
                                        </div>
                                        <div class="mt-4 flex items-center justify-between border-t border-gray-100 pt-3">
                                            <span class="text-xs font-bold text-gray-400">{{ __('Order') }}: <span class="text-gray-700">{{ $slide->order }}</span></span>
                                            <span class="text-[10px] font-semibold text-gray-400">{{ $slide->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>

                                    <!-- Edit Inline Modal Overlay -->
                                    <div x-show="editing" class="absolute inset-0 bg-white z-20 p-4 flex flex-col justify-between" x-transition>
                                        <form action="{{ route('admin.hero-slides.update', $slide) }}" method="POST" enctype="multipart/form-data" class="space-y-3 flex-1 overflow-y-auto scrollbar-hide">
                                            @csrf
                                            @method('PATCH')

                                            <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                                                <h4 class="font-extrabold text-gray-900 text-sm">{{ __('Edit Slide') }}</h4>
                                                <button type="button" @click="editing = false" class="text-gray-400 hover:text-gray-600 text-lg">&times;</button>
                                            </div>

                                            <div>
                                                <label class="block text-[10px] font-bold text-gray-500 uppercase">{{ __('Replace Image') }}</label>
                                                <input type="file" name="image" class="w-full text-xs text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#6A8F3B]/10 file:text-[#6A8F3B] hover:file:bg-[#6A8F3B]/20">
                                            </div>

                                            <div>
                                                <label class="block text-[10px] font-bold text-gray-500 uppercase">{{ __('Title') }}</label>
                                                <input type="text" name="title" value="{{ $slide->title }}" class="w-full bg-gray-50 border border-gray-250 rounded-lg px-2.5 py-1.5 text-xs focus:outline-none">
                                            </div>

                                            <div>
                                                <label class="block text-[10px] font-bold text-gray-500 uppercase">{{ __('Subtitle') }}</label>
                                                <input type="text" name="subtitle" value="{{ $slide->subtitle }}" class="w-full bg-gray-50 border border-gray-250 rounded-lg px-2.5 py-1.5 text-xs focus:outline-none">
                                            </div>

                                            <div class="grid grid-cols-2 gap-2">
                                                <div>
                                                    <label class="block text-[10px] font-bold text-gray-500 uppercase">{{ __('Order') }}</label>
                                                    <input type="number" name="order" value="{{ $slide->order }}" class="w-full bg-gray-50 border border-gray-250 rounded-lg px-2.5 py-1.5 text-xs focus:outline-none">
                                                </div>
                                                <div class="flex flex-col justify-end">
                                                    <label class="flex items-center gap-2 text-xs font-bold text-gray-700 cursor-pointer mb-2">
                                                        <input type="checkbox" name="is_active" value="1" {{ $slide->is_active ? 'checked' : '' }} class="rounded border-gray-300 text-[#6A8F3B] focus:ring-[#6A8F3B]">
                                                        {{ __('Active') }}
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="flex gap-2 pt-2">
                                                <button type="submit" class="flex-1 py-2 bg-[#6A8F3B] hover:bg-[#5a7a2f] text-white text-xs font-bold rounded-lg transition duration-200 shadow">
                                                    {{ __('Save') }}
                                                </button>
                                                <button type="button" @click="editing = false" class="flex-1 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-xs font-bold rounded-lg transition duration-200">
                                                    {{ __('Cancel') }}
                                                </button>
                                            </div>
                                        </form>
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
