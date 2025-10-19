@extends('layouts.app')
@section('content')
<div class="max-w-xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">Mill Onboarding</h1>
    <form method="POST" action="{{ route('onboarding.submit', ['role' => 'mill']) }}">
        @csrf
        <div class="mb-4">
            <label for="mill_name" class="block font-medium">Mill Name</label>
            <input type="text" name="mill_name" id="mill_name" class="mt-1 block w-full border rounded p-2" required>
        </div>
        <div class="mb-4">
            <label for="capacity" class="block font-medium">Capacity (tons/year)</label>
            <input type="number" name="capacity" id="capacity" class="mt-1 block w-full border rounded p-2" required>
        </div>
        <button type="submit" class="bg-yellow-600 text-white px-4 py-2 rounded">Complete Onboarding</button>
    </form>
</div>
@endsection