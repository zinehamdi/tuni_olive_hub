@php
$title = __('public.terms.title') . ' | ' . config('app.name');
$description = __('public.terms.description');
$heading = __('public.terms.heading');
$body = __('public.terms.body');
$disclaimer = __('public.common.disclaimer');
@endphp
@include('public.page_stub', compact('title','description','heading','body','disclaimer'))
