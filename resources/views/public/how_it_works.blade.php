@extends('layouts.app')

@php
$title = __('how_it_works_title') . ' | ' . config('app.name');
$description = __('how_it_works_description');
$heading = __('how_it_works_heading');
$body = __('how_it_works_body');
$disclaimer = __('common_disclaimer');

$roles = [
	['title' => __('Producer / Farmer'), 'desc' => __('role_producer_desc')],
	['title' => __('Mill'), 'desc' => __('role_mill_desc')],
	['title' => __('Packer / Private Label'), 'desc' => __('role_packer_desc')],
	['title' => __('Buyer / Importer'), 'desc' => __('role_buyer_desc')],
	['title' => __('Carrier / Logistics'), 'desc' => __('role_carrier_desc')],
];

$steps = [
	['title' => __('Publish'), 'desc' => __('step_publish_desc')],
	['title' => __('Discover & filter'), 'desc' => __('step_discover_desc')],
	['title' => __('Contact & negotiate'), 'desc' => __('step_contact_desc')],
	['title' => __('Add services'), 'desc' => __('step_services_desc')],
	['title' => __('Execute & deliver'), 'desc' => __('step_execute_desc')],
];
@endphp

@section('title', $title)
@section('description', $description)
@section('og_title', $heading)
@section('og_description', $description)

@section('content')
<div class="min-h-screen bg-gradient-to-b from-white to-[#f8faf5]">
	<!-- Hero -->
	<div class="relative overflow-hidden bg-gradient-to-br from-[#6A8F3B] via-[#7ba34f] to-[#C8A356] text-white py-14 px-4">
		<div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,#ffffff22,transparent_45%)]"></div>
		<div class="absolute inset-0 bg-[radial-gradient(circle_at_bottom_right,#00000011,transparent_50%)]"></div>
		<div class="max-w-5xl mx-auto relative z-10 text-center space-y-4">
			<p class="text-sm uppercase tracking-[0.25em] text-white/80">{{ __('how_it_works_title') }}</p>
			<h1 class="text-3xl md:text-4xl font-extrabold leading-tight">{{ $heading }}</h1>
			<p class="text-lg md:text-xl text-white/85">{{ $description }}</p>
			<p class="text-sm text-white/80">{{ $disclaimer }}</p>
		</div>
	</div>

	<!-- Roles grid -->
	<div class="max-w-6xl mx-auto px-4 py-10">
		<div class="flex items-center gap-3 mb-6">
			<span class="h-1.5 w-10 rounded-full bg-[#6A8F3B]"></span>
			<h2 class="text-2xl font-bold text-gray-900">{{ __('Roles on the platform') }}</h2>
		</div>
		<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
			@foreach($roles as $role)
			<div class="rounded-2xl border border-gray-200 bg-white shadow-sm hover:shadow-lg transition p-5 flex flex-col gap-2">
				<div class="flex items-center gap-3">
					<span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-[#6A8F3B] to-[#C8A356] text-white shadow">★</span>
					<h3 class="text-lg font-semibold text-gray-900">{{ $role['title'] }}</h3>
				</div>
				<p class="text-sm text-gray-700 leading-relaxed">{{ $role['desc'] }}</p>
			</div>
			@endforeach
		</div>
	</div>

	<!-- Flow steps -->
	<div class="max-w-6xl mx-auto px-4 pb-12">
		<div class="flex items-center gap-3 mb-6">
			<span class="h-1.5 w-10 rounded-full bg-[#C8A356]"></span>
			<h2 class="text-2xl font-bold text-gray-900">{{ __('Journey steps') }}</h2>
		</div>
		<div class="grid md:grid-cols-5 gap-4">
			@foreach($steps as $index => $step)
			<div class="relative rounded-2xl border border-gray-200 bg-white shadow-sm p-4 flex flex-col gap-2">
				<div class="flex items-center gap-2 font-bold" style="color:#5a7a2f">
					<span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-[#6A8F3B]/15 to-[#C8A356]/15 border border-[#6A8F3B]/30 text-[#5a7a2f]">{{ $index + 1 }}</span>
					<span>{{ $step['title'] }}</span>
				</div>
				<p class="text-sm text-gray-700 leading-relaxed">{{ $step['desc'] }}</p>
			</div>
			@endforeach
		</div>
	</div>

	<!-- Body paragraph -->
	<div class="max-w-5xl mx-auto px-4 pb-12">
		<div class="rounded-3xl border border-dashed border-[#6A8F3B]/30 bg-white p-6 shadow-sm">
			<h3 class="text-xl font-semibold text-gray-900 mb-3">{{ $heading }}</h3>
			<p class="text-gray-800 leading-relaxed">{{ $body }}</p>
		</div>
	</div>

	<!-- CTA -->
	<div class="max-w-5xl mx-auto px-4 pb-14">
		<div class="rounded-3xl bg-gradient-to-r from-[#6A8F3B] to-[#C8A356] text-white p-8 shadow-xl text-center space-y-3">
			<h3 class="text-2xl font-bold">{{ __('Get started now') }}</h3>
<p class="text-white/90">{{ __('Publish a listing or reach suppliers and service partners today.') }}</p>
			<div class="flex flex-wrap justify-center gap-3">
				<a href="{{ route('listings.create') }}" class="px-6 py-3 bg-white text-[#6A8F3B] font-semibold rounded-xl hover:bg-white/90 transition">{{ __('Add your listing') }}</a>
				<a href="{{ route('public.contact') }}" class="px-6 py-3 border border-white/40 text-white font-semibold rounded-xl hover:bg-white/10 transition">{{ __('Contact Us') }}</a>
			</div>
		</div>
	</div>
</div>
@endsection
