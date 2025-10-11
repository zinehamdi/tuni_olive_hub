@extends('layouts.guest')
@section('content')
    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-6 shadow-xl">
        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="role" value="carrier">
            <div class="mb-4">
                <label for="name" class="block text-gray-700 dark:text-gray-200">Name</label>
                <input id="name" type="text" name="name" required autofocus class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
            </div>
            <div class="mb-4">
                <label for="email" class="block text-gray-700 dark:text-gray-200">Email</label>
                <input id="email" type="email" name="email" required class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-700 dark:text-gray-200">Password</label>
                <input id="password" type="password" name="password" required class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
            </div>
            <div class="mb-4">
                <label for="camion_capacity" class="block text-gray-700 dark:text-gray-200">Camion Capacity (tons)</label>
                <input id="camion_capacity" type="number" name="camion_capacity" required class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
            </div>
            <div class="mb-4">
                <label for="profile_picture" class="block text-gray-700 dark:text-gray-200">Profile Picture</label>
                <input id="profile_picture" type="file" name="profile_picture" class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
            </div>
            <button type="submit" class="w-full py-2 px-4 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700">Register as Carrier</button>
        </form>
    </div>
@endsection
