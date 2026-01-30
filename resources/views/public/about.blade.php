@php
$title = __('public.about.title') . ' | ' . config('app.name');
$description = __('public.about.description');
$heading = __('public.about.heading');
$body = __('public.about.body');
$disclaimer = __('public.common.disclaimer');
@endphp
@include('public.page_stub', compact('title','description','heading','body','disclaimer'))
