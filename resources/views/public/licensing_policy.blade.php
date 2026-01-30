@php
$title = __('public.licensing_policy.title') . ' | ' . config('app.name');
$description = __('public.licensing_policy.description');
$heading = __('public.licensing_policy.heading');
$body = __('public.licensing_policy.body');
$disclaimer = __('public.common.disclaimer');
@endphp
@include('public.page_stub', compact('title','description','heading','body','disclaimer'))
