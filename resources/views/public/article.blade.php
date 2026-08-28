@extends('layouts.app')

@section('title', localized($article->title) ?? 'Article')

@section('og_title', localized($article->title) ?? '')
@section('twitter_title', localized($article->title) ?? '')
@section('og_description', Str::limit(strip_tags($article->content[app()->getLocale()] ?? ''), 150))
@section('description', Str::limit(strip_tags($article->content[app()->getLocale()] ?? ''), 150))
@section('og_image', Str::startsWith($article->image, ['http://', 'https://']) ? $article->image : (Str::startsWith($article->image, ['storage/', 'images/']) ? asset($article->image) : asset('images/' . $article->image)))
@section('twitter_image', Str::startsWith($article->image, ['http://', 'https://']) ? $article->image : (Str::startsWith($article->image, ['storage/', 'images/']) ? asset($article->image) : asset('images/' . $article->image)))

@section('content')
<div class="bg-gray-100 min-h-screen py-10">
    <div class="max-w-4xl mx-auto px-4">
        <!-- Back Button -->
        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-[#6A8F3B] mb-6 transition-all font-bold text-sm group">
            <svg class="w-5 h-5 rtl:rotate-180 group-hover:-translate-x-1 rtl:group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            {{ __('Back to Marketplace') }}
        </a>

        <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-200">
            <!-- Image inside the card -->
            <div class="w-full h-[260px] md:h-[380px] overflow-hidden relative bg-gradient-to-br from-[#183b1c] to-[#6A8F3B]">
                <img src="{{ Str::startsWith($article->image, ['http://', 'https://']) ? $article->image : (Str::startsWith($article->image, ['storage/', 'images/']) ? asset($article->image) : asset('images/' . $article->image)) }}" 
                     onerror="this.onerror=null;this.src='{{ asset('images/hero_slide_1.png') }}'" 
                     alt="{{ localized($article->title) ?? '' }}" 
                     class="w-full h-full object-cover">
                <div class="absolute bottom-0 {{ app()->getLocale() === 'ar' ? 'right-0' : 'left-0' }} bg-[#183b1c] text-white px-6 py-2.5 font-black text-xs uppercase tracking-widest shadow-lg">
                    {{ $article->category[app()->getLocale()] ?? $article->category['en'] ?? $article->category['ar'] ?? __('Article') }}
                </div>
            </div>

            <div class="p-8 md:p-12">
                <h1 class="text-3xl md:text-5xl font-black text-gray-900 leading-tight mb-6">
                    {{ localized($article->title) ?? '' }}
                </h1>

                <div class="flex items-center gap-4 text-[10px] font-black text-gray-400 uppercase tracking-widest mb-10 border-b border-gray-100 pb-6">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        {{ $article->created_at->format('d M Y') }}
                    </span>
                    <span>•</span>
                    <span class="text-[#6A8F3B]">{{ __('Editorial Team') }}</span>
                    
                    <div class="ml-auto flex items-center gap-3">
                        <span class="hidden sm:inline">{{ __('Share:') }}</span>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="https://wa.me/?text={{ urlencode(($article->title[app()->getLocale()] ?? $article->title['en'] ?? $article->title['ar'] ?? '') . ' ' . url()->current()) }}" target="_blank" class="w-8 h-8 rounded-lg bg-green-50 text-green-600 flex items-center justify-center hover:bg-green-600 hover:text-white transition-all">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.246 2.248 3.484 5.232 3.484 8.412-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.309 1.656zm6.224-3.52s.126.074.39.231c1.56.93 3.351 1.421 5.22 1.422 5.513 0 10-4.487 10-10 0-2.673-1.04-5.186-2.93-7.076-1.89-1.889-4.403-2.928-7.07-2.929-5.515 0-10.002 4.487-10.002 10 0 1.763.461 3.486 1.332 5.012l.145.255-1.111 4.056 4.126-1.082z"/></svg>
                        </a>
                    </div>
                </div>

                <div class="prose prose-xl prose-green max-w-none text-gray-700 leading-relaxed font-serif">
                    {!! preg_replace(
                        '~(https?://[^\s<]+)~i',
                        '<a href="$1" target="_blank" class="text-[#6A8F3B] underline hover:text-[#5b7c32] break-all">$1</a>',
                        nl2br(e($article->content[app()->getLocale()] ?? ''))
                    ) !!}
                </div>

                <!-- Related Articles Section -->
                @if(isset($relatedArticles) && $relatedArticles->count() > 0)
                <div class="mt-16 pt-10 border-t border-gray-100">
                    <h3 class="text-2xl font-black text-gray-900 mb-6">{{ __('Read Also') }}</h3>
                    
                    <div class="relative group" 
                         x-data="{ 
                            scroll() { 
                                const el = this.$refs.articleScroll;
                                if (el.scrollLeft >= (el.scrollWidth - el.clientWidth - 10)) {
                                    el.scrollLeft = 0;
                                } else {
                                    el.scrollLeft += 1;
                                }
                            },
                            interval: null,
                            startScroll() {
                                this.interval = setInterval(() => this.scroll(), 30);
                            },
                            stopScroll() {
                                clearInterval(this.interval);
                            }
                         }" 
                         x-init="startScroll()" 
                         @mouseenter="stopScroll()" 
                         @mouseleave="startScroll()"
                         @touchstart="stopScroll()"
                         @touchend="startScroll()">
                        
                        <div x-ref="articleScroll" class="flex gap-6 overflow-x-auto pb-6 pt-2 scrollbar-hide snap-x snap-mandatory">
                            @foreach($relatedArticles as $related)
                            <div class="w-[280px] md:w-[320px] flex-shrink-0 snap-center py-2">
                                <a href="{{ route('articles.show', $related->id) }}" class="group block bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 h-full flex flex-col border-2 border-gray-200 hover:border-[#6A8F3B]/30">
                                    <div class="aspect-[16/9] bg-gray-200 overflow-hidden relative flex-shrink-0">
                                        <img src="{{ Str::startsWith($related->image, ['http://', 'https://']) ? $related->image : (Str::startsWith($related->image, ['storage/', 'images/']) ? asset($related->image) : asset('images/' . $related->image)) }}" 
                                             onerror="this.onerror=null;this.src='{{ asset('images/hero_slide_1.png') }}'" 
                                             alt="{{ $related->title[app()->getLocale()] ?? '' }}" 
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                        @if(isset($related->category[app()->getLocale()]) || isset($related->category['en']) || isset($related->category['ar']))
                                        <div class="absolute top-2 {{ __('left-2') }} bg-white/95 backdrop-blur px-2 py-1 rounded-lg text-[10px] font-bold text-[#6A8F3B] shadow-sm">{{ $related->category[app()->getLocale()] ?? $related->category['en'] ?? $related->category['ar'] }}</div>
                                        @endif
                                    </div>
                                    <div class="p-5 flex flex-col flex-1">
                                        <h4 class="font-bold text-gray-900 group-hover:text-[#6A8F3B] transition-colors line-clamp-2 text-sm leading-snug mb-3 flex-1">{{ $related->title[app()->getLocale()] ?? '' }}</h4>
                                        <div class="text-[10px] text-gray-400 font-bold mt-auto flex items-center gap-1 border-t border-gray-100 pt-3">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            {{ $related->created_at->format('d M Y') }}
                                        </div>
                                    </div>
                                </a>
                            </div>
                            @endforeach
                        </div>
                        
                        <!-- Shadow Gradients for indication of more items -->
                        <div class="absolute inset-y-0 left-0 w-12 bg-gradient-to-r from-white/90 to-transparent pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="absolute inset-y-0 right-0 w-12 bg-gradient-to-l from-white/90 to-transparent pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    </div>
                </div>
                @endif

                <!-- Signature -->
                <div class="mt-16 pt-10 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-6">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#6A8F3B] to-[#5a7a2f] flex items-center justify-center text-white font-black text-2xl shadow-xl shadow-[#6A8F3B]/20">Z</div>
                        <div>
                            <div class="font-black text-gray-900 text-lg">{{ __('ZinToop Marketplace') }}</div>
                            <div class="text-xs text-gray-400 font-bold uppercase tracking-widest">{{ __('Premium Tunisian Olive Oil') }}</div>
                        </div>
                    </div>
                    
                    <a href="{{ route('home') }}" class="px-8 py-4 bg-gray-900 text-white rounded-2xl font-black text-sm hover:bg-black transition-all shadow-xl">
                        {{ __('Explore the Market') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('head')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "Article",
  "headline": "{{ localized($article->title) ?? '' }}",
  "image": [
    "{{ Str::startsWith($article->image, ['http://', 'https://']) ? $article->image : (Str::startsWith($article->image, 'storage/') ? asset($article->image) : (Storage::disk('public')->exists($article->image) ? Storage::url($article->image) : asset('images/' . $article->image))) }}"
  ],
  "datePublished": "{{ $article->created_at->toIso8601String() }}",
  "dateModified": "{{ $article->updated_at->toIso8601String() }}",
  "author": [{
      "@@type": "Organization",
      "name": "ZinToop Marketplace",
      "url": "{{ url('/') }}"
  }]
}
</script>
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "BreadcrumbList",
  "itemListElement": [
    {
      "@@type": "ListItem",
      "position": 1,
      "name": "ZinToop",
      "item": "{{ url('/') }}"
    },
    {
      "@@type": "ListItem",
      "position": 2,
      "name": "{{ __('Articles') }}",
      "item": "{{ url(app()->getLocale() . '/articles/' . $article->id) }}"
    },
    {
      "@@type": "ListItem",
      "position": 3,
      "name": "{{ localized($article->title) ?? '' }}",
      "item": "{{ url()->current() }}"
    }
  ]
}
</script>
@endpush

@endsection

