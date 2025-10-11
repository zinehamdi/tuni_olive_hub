@extends('layouts.app')
@section('content')
<div class="max-w-xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">Carrier Onboarding</h1>
    <form method="POST" action="{{ route('onboarding.submit', ['role' => 'carrier']) }}">
        @csrf
        <div class="mb-4">
            <label for="company_name" class="block font-medium">Company Name</label>
            <input type="text" name="company_name" id="company_name" class="mt-1 block w-full border rounded p-2" required>
        </div>
        <div class="mb-4">
            <label for="fleet_size" class="block font-medium">Fleet Size</label>
            <input type="number" name="fleet_size" id="fleet_size" class="mt-1 block w-full border rounded p-2" required>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Complete Onboarding</button>
    </form>
</div>
@endsection