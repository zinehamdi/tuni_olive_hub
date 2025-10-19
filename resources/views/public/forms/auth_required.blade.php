@extends('layouts.app')

@section('content')
<div dir="rtl" class="min-h-screen grid place-items-center bg-[#F8F4EC] text-gray-900">
  <div class="max-w-md w-full p-0">
    <div class="bg-gradient-to-br from-[#F8F4EC] to-[#EEF5E9] rounded-2xl shadow-xl p-6 lg:p-8 text-center space-y-4">
      <h1 class="text-2xl font-extrabold text-[#1B2A1B]">{{ $title ?? 'مطلوب تسجيل الدخول' }}</h1>
      <p class="text-gray-700">{{ $hint ?? 'هذه الصفحة تتطلب حساباً مسجلاً.' }}</p>
      <div class="flex items-center justify-center gap-3 pt-2">
        <a href="{{ route('login') }}" class="min-h-[44px] px-5 py-3 rounded-xl bg-gradient-to-r from-[#1B2A1B] to-[#6A8F3B] text-white font-bold shadow-lg hover:shadow-2xl hover:scale-105 transition focus:ring-2 focus:ring-[#C8A356]">تسجيل الدخول</a>
        <a href="{{ route('register') }}" class="min-h-[44px] px-5 py-3 rounded-xl bg-gradient-to-r from-[#9B6A4A] to-[#F8F4EC] text-gray-900 shadow-lg hover:shadow-2xl hover:scale-105 transition">إنشاء حساب</a>
        <a href="{{ url('/') }}" class="min-h-[44px] px-5 py-3 rounded-xl bg-gradient-to-r from-white to-[#F8F4EC] text-gray-900 border border-[#C7D1C7] shadow-sm hover:shadow-md transition">رجوع</a>
      </div>
    </div>
  </div>
</div>
@endsection
