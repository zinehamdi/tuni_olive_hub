@extends('layouts.app')
@section('content')
<div class="max-w-xl mx-auto py-8">
    <div class="bg-gradient-to-br from-[#F8F4EC] to-[#EEF5E9] rounded-2xl shadow-xl p-6">
        <h1 class="text-2xl font-bold text-center mb-6 text-[#C8A356]">الملف الشخصي</h1>
        <div class="flex flex-col items-center mb-6">
            @if($user->profile_picture)
                <img src="{{ Storage::url($user->profile_picture) }}" alt="Profile" class="w-32 h-32 rounded-full object-cover border-4 border-[#C8A356] mb-2">
            @else
                <div class="w-32 h-32 rounded-full bg-gray-200 flex items-center justify-center text-4xl text-gray-400 mb-2">
                    <i class="fas fa-user"></i>
                </div>
            @endif
            <div class="text-xl font-bold">{{ $user->name }}</div>
            <div class="text-gray-600">{{ $user->role }}</div>
        </div>
        <div class="space-y-4">
            <div><span class="font-bold">رقم الهاتف:</span> {{ $user->phone ?? '-' }}</div>
            <div><span class="font-bold">البريد الإلكتروني:</span> {{ $user->email ?? '-' }}</div>
            <div><span class="font-bold">مكان الضيعة:</span> {{ $user->farm_location ?? '-' }}</div>
            <div><span class="font-bold">عدد أشجار الزيتون:</span> {{ $user->tree_number ?? '-' }}</div>
            <div><span class="font-bold">نوع الزيتون:</span> {{ trans('olive_types.' . $user->olive_type) ?? '-' }}</div>
        </div>
    </div>
</div>
@endsection
