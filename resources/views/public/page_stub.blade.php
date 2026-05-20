@extends('layouts.app')

@section('title', $title ?? '')
@section('description', $description ?? '')
@section('og_title', $title ?? '')
@section('og_description', $description ?? '')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-10 space-y-6">
    <h1 class="text-3xl font-bold text-gray-900">{{ $heading ?? '' }}</h1>
    <p class="text-gray-700 leading-relaxed">{{ $body ?? '' }}</p>
    <p class="text-sm text-gray-600">{{ $disclaimer ?? __('public.common.disclaimer') }}</p>
</div>
@endsection
