@extends('layouts.app')
@section('content')
<div class="max-w-xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">User Onboarding</h1>
    <form method="POST" action="{{ route('onboarding.submit', ['role' => 'normal']) }}">
        @csrf
        <div class="mb-4">
            <label for="full_name" class="block font-medium">Full Name</label>
            <input type="text" name="full_name" id="full_name" class="mt-1 block w-full border rounded p-2" required>
        </div>
        <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded">Complete Onboarding</button>
    </form>
</div>
@endsection