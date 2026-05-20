@extends('layouts.app')

@section('title', $title ?? '')
@section('description', $description ?? '')
@section('og_title', $title ?? '')
@section('og_description', $description ?? '')

@section('content')
<style>
    .policy-card {
        max-width: 56rem;
        margin: 1.5rem auto;
        padding: 2.5rem 2rem;
        background: #fff;
        border-radius: 1.5rem;
        box-shadow: 0 1px 12px 0 rgba(0,0,0,0.07);
        border: 1px solid #f0f0f0;
    }
    .policy-card h1 {
        font-size: 2rem;
        font-weight: 900;
        color: #111827;
        border-bottom: 2px solid #f0fdf4;
        padding-bottom: 1rem;
        margin-bottom: 1.5rem;
        line-height: 1.3;
    }
    .policy-body { color: #374151; line-height: 1.8; font-size: 1rem; }
    .policy-body p { margin-bottom: 1rem; color: #374151; line-height: 1.75; }
    .policy-body h2 {
        font-size: 1.15rem;
        font-weight: 800;
        color: #111827;
        margin-top: 2rem;
        margin-bottom: 0.65rem;
        border-bottom: 2px solid #d1fae5;
        padding-bottom: 0.3rem;
    }
    .policy-body strong { color: #14532d; }
    .policy-disclaimer { margin-top: 2rem; border-top: 1px solid #f0f0f0; padding-top: 1.25rem; }
    .policy-disclaimer-inner {
        font-size: 0.85rem;
        color: #6b7280;
        font-style: italic;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 1rem 1.25rem;
    }
    @media (max-width: 640px) {
        .policy-card { padding: 1.25rem 1rem; border-radius: 1rem; }
        .policy-card h1 { font-size: 1.4rem; }
    }
</style>
<div class="policy-card">
    <h1>{{ $heading ?? '' }}</h1>
    <div class="policy-body">
        {!! $body ?? '' !!}
    </div>
    @if(!empty($disclaimer))
    <div class="policy-disclaimer">
        <div class="policy-disclaimer-inner">{!! $disclaimer !!}</div>
    </div>
    @endif
</div>
@endsection
