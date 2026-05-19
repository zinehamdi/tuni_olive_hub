@php
$title = __('about_title') . ' | ' . config('app.name');
$description = __('about_description');
$heading = __('about_heading');
$body = __('about_body');
$disclaimer = __('common_disclaimer');
@endphp
@include('public.page_stub', compact('title','description','heading','body','disclaimer'))
