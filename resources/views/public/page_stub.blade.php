@extends('layouts.app')

@section('title', $title ?? '')
@section('description', $description ?? '')
@section('og_title', $title ?? '')
@section('og_description', $description ?? '')

@section('content')
<div class="max-w-4xl mx-auto px-6 py-12 space-y-8 bg-white rounded-3xl shadow-sm border border-gray-100 mt-6">
    <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-950 border-b pb-6 border-gray-100">{{ $heading ?? '' }}</h1>
    <div class="text-gray-800 leading-relaxed space-y-6 text-base sm:text-lg">
        {!! $body ?? '' !!}
    </div>
    @if(isset($disclaimer) && $disclaimer)
    <div class="border-t pt-6 border-gray-100">
        <div class="text-xs sm:text-sm text-gray-500 italic bg-gray-50 p-4 rounded-2xl border border-gray-100/80">
            {!! $disclaimer !!}
        </div>
    </div>
    @endif
</div>
@endsection
