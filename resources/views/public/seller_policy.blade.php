@php
$title = __('public.seller_policy.title') . ' | ' . config('app.name');
$description = __('public.seller_policy.description');
$heading = __('public.seller_policy.heading');
$body = __('public.seller_policy.body');
$disclaimer = __('public.common.disclaimer');
@endphp
@include('public.page_stub', compact('title','description','heading','body','disclaimer'))
