@extends('layouts.app')
@section('content')
<div class="max-w-xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">Packer Onboarding</h1>
    <form method="POST" action="{{ route('onboarding.submit', ['role' => 'packer']) }}">
        @csrf
        <div class="mb-4">
            <label for="packer_name" class="block font-medium">Packer Name</label>
            <input type="text" name="packer_name" id="packer_name" class="mt-1 block w-full border rounded p-2" required>
        </div>
        <div class="mb-4">
            <label for="packaging_types" class="block font-medium">Packaging Types</label>
            <input type="text" name="packaging_types" id="packaging_types" class="mt-1 block w-full border rounded p-2" required>
        </div>
        <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded">Complete Onboarding</button>
    </form>
</div>
@endsection