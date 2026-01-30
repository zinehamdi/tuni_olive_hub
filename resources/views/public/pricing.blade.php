@php
$title = __('public.pricing.title') . ' | ' . config('app.name');
$description = __('public.pricing.description');
$heading = __('public.pricing.heading');
$body = __('public.pricing.body');
$disclaimer = __('public.common.disclaimer');
@endphp
@include('public.page_stub', compact('title','description','heading','body','disclaimer'))
