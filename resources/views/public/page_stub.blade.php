@extends('layouts.app')

@section('title', $title ?? '')
@section('description', $description ?? '')
@section('og_title', $title ?? '')
@section('og_description', $description ?? '')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-10 space-y-6">
    <h1 class="text-3xl font-bold text-gray-900">{{ $heading ?? '' }}</h1>
    <div class="text-gray-700 leading-relaxed [&>p]:mb-4 [&>h2]:text-xl [&>h2]:font-bold [&>h2]:text-gray-900 [&>h2]:mt-8 [&>h2]:mb-3 [&>h2]:border-b-2 [&>h2]:border-[#d1fae5] [&>h2]:pb-2">
        {!! $body ?? '' !!}
    </div>
    @if(!empty($disclaimer))
    <p class="text-sm text-gray-600 border-t border-gray-200 pt-6 mt-8 italic">
        {!! $disclaimer !!}
    </p>
    @endif
</div>
@endsection
