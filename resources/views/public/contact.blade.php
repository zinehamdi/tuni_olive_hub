@extends('layouts.app')

@php
$title = __('public.contact.title') . ' | ' . config('app.name');
$description = __('public.contact.description');
$heading = __('public.contact.heading');
$body = __('public.contact.body');
$disclaimer = __('public.common.disclaimer');
$contactEmail = config('app.contact_email') ?? 'contact@toop.kairouanhub.com';
$contactPhone = config('app.contact_phone') ?? '+21625777926';
@endphp

@section('title', $title)
@section('description', $description)
@section('og_title', $heading)
@section('og_description', $description)

@section('content')
<div class="max-w-5xl mx-auto px-4 py-10 space-y-8">
	<div class="space-y-3">
		<h1 class="text-3xl font-bold text-gray-900">{{ $heading }}</h1>
		<p class="text-gray-700 leading-relaxed">{{ $body }}</p>
		<p class="text-sm text-gray-600">{{ $disclaimer }}</p>
		@if(session('status'))
			<div class="mt-4 rounded-lg border border-green-200 bg-green-50 text-green-800 px-4 py-3 font-semibold">
				{{ session('status') }}
			</div>
		@endif
	</div>

	<div class="grid gap-6 md:grid-cols-3">
		<div class="md:col-span-1 space-y-4">
			<div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
				<div class="text-sm text-gray-500 mb-2">{{ __('Contact Email') }}</div>
				<a class="text-lg font-semibold text-[#6A8F3B] break-all" href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>
			</div>
			<div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
				<div class="text-sm text-gray-500 mb-2">{{ __('Phone') }}</div>
				<a class="text-lg font-semibold text-[#6A8F3B]" href="tel:{{ $contactPhone }}">{{ $contactPhone }}</a>
			</div>
		</div>

		<div class="md:col-span-2">
			<div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
				<h2 class="text-xl font-bold text-gray-900 mb-4">{{ __('Send us a message') }}</h2>
				<form action="{{ route('public.contact.submit') }}" method="POST" class="space-y-4">
					@csrf
					<div class="grid gap-4 md:grid-cols-2">
						<div>
							<label class="block text-sm font-semibold text-gray-700 mb-1" for="name">{{ __('Name') }}</label>
							<input id="name" name="name" value="{{ old('name') }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-[#6A8F3B] focus:ring-2 focus:ring-[#6A8F3B]" />
							@error('name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
						</div>
						<div>
							<label class="block text-sm font-semibold text-gray-700 mb-1" for="email">{{ __('Email') }}</label>
							<input id="email" type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-[#6A8F3B] focus:ring-2 focus:ring-[#6A8F3B]" />
							@error('email')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
						</div>
					</div>
					<div>
						<label class="block text-sm font-semibold text-gray-700 mb-1" for="phone">{{ __('Phone') }} ({{ __('optional') }})</label>
						<input id="phone" name="phone" value="{{ old('phone') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-[#6A8F3B] focus:ring-2 focus:ring-[#6A8F3B]" />
						@error('phone')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
					</div>
					<div>
						<label class="block text-sm font-semibold text-gray-700 mb-1" for="message">{{ __('Message') }}</label>
						<textarea id="message" name="message" rows="5" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-[#6A8F3B] focus:ring-2 focus:ring-[#6A8F3B]">{{ old('message') }}</textarea>
						@error('message')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
					</div>
					<button type="submit" class="w-full md:w-auto px-6 py-3 bg-[#6A8F3B] text-white font-semibold rounded-lg hover:bg-[#5a7a2f] transition">
						{{ __('Send Message') }}
					</button>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection
