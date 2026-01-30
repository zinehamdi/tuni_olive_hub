@php
$title = __('public.commission_policy.title') . ' | ' . config('app.name');
$description = __('public.commission_policy.description');
$heading = __('public.commission_policy.heading');
$body = __('public.commission_policy.body');
$disclaimer = __('public.common.disclaimer');
@endphp
@include('public.page_stub', compact('title','description','heading','body','disclaimer'))
