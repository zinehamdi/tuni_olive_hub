@extends('layouts.app')

@section('title', $article->title[app()->getLocale()] ?? '')

@section('og_title', $article->title[app()->getLocale()] ?? '')
@section('og_description', Str::limit(strip_tags($article->content[app()->getLocale()] ?? ''), 150))
@section('description', Str::limit(strip_tags($article->content[app()->getLocale()] ?? ''), 150))
@section('og_image', Str::startsWith($article->image, ['http://', 'https://']) ? $article->image : asset($article->image))
@section('twitter_image', Str::startsWith($article->image, ['http://', 'https://']) ? $article->image : asset($article->image))
@section('twitter_title', $article->title[app()->getLocale()] ?? '')
@section('twitter_description', Str::limit(strip_tags($article->content[app()->getLocale()] ?? ''), 150))

@section('content')
<div class="max-w-4xl mx-auto py-12 px-4 sm:px-6">
    <a href="{{ url()->previous() == url()->current() ? url('/') : url()->previous() }}" class="inline-flex items-center gap-2 text-[#6A8F3B] hover:text-[#5a7a2f] font-semibold mb-6 transition">
        <svg class="w-5 h-5 {{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        {{ __('Back') }}
    </a>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="w-full aspect-[21/9] bg-gray-100 relative">
            <img src="{{ Str::startsWith($article->image, ['http://', 'https://']) ? $article->image : asset($article->image) }}" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&q=80'" alt="{{ $article->title[app()->getLocale()] ?? '' }}" class="w-full h-full object-cover">
            @if(isset($article->category[app()->getLocale()]))
            <div class="absolute top-6 left-6 bg-white/90 backdrop-blur px-4 py-1.5 rounded-full text-sm font-bold text-[#6A8F3B] shadow-sm">
                {{ $article->category[app()->getLocale()] }}
            </div>
            @endif
        </div>
        
        <div class="p-8 md:p-12">
            <div class="flex items-center gap-4 text-sm text-gray-500 mb-6">
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    {{ $article->created_at->format('M d, Y') }}
                </span>
                <span>•</span>
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    3 {{ __('min read') }}
                </span>
            </div>

            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-8 leading-tight">{{ $article->title[app()->getLocale()] ?? '' }}</h1>

            <div class="prose prose-lg prose-green max-w-none text-gray-700 leading-relaxed">
                <p>{{ $article->content[app()->getLocale()] ?? '' }}</p>
                
                <p class="mt-6 p-6 bg-green-50 rounded-2xl border border-green-100 text-green-900">
                    {{ __('Stay tuned to ZinToop for more updates and insights on the global and local olive oil market.') }}
                </p>
            </div>

            <div class="mt-12 pt-8 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-[#6A8F3B] text-white flex items-center justify-center font-bold text-lg">
                        Z
                    </div>
                    <div>
                        <div class="font-bold text-gray-900">{{ __('ZinToop Editorial Team') }}</div>
                        <div class="text-sm text-gray-500">{{ __('Expert Analysis') }}</div>
                    </div>
                </div>
                
                <div class="flex items-center gap-3">
                    <span class="text-sm font-semibold text-gray-500">{{ __('Share:') }}</span>
                    <button class="w-8 h-8 rounded-full bg-gray-100 hover:bg-[#6A8F3B] hover:text-white transition flex items-center justify-center text-gray-600">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                    </button>
                    <button class="w-8 h-8 rounded-full bg-gray-100 hover:bg-[#6A8F3B] hover:text-white transition flex items-center justify-center text-gray-600">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
