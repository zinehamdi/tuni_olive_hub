@php
$title = __('public.privacy.title') . ' | ' . config('app.name');
$description = __('public.privacy.description');
$heading = __('public.privacy.heading');
$body = __('public.privacy.body');
$disclaimer = __('public.common.disclaimer');
@endphp
@include('public.page_stub', compact('title','description','heading','body','disclaimer'))
