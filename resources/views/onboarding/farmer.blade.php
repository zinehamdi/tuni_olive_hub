@extends('layouts.app')
@section('content')
<div class="max-w-xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">تعريف الفلاح (Farmer Onboarding)</h1>
    <form method="POST" action="{{ route('onboarding.submit', ['role' => 'farmer']) }}" class="space-y-6">
        @csrf
        <div class="mb-4">
            <label for="farm_name_ar" class="block font-medium">اسم الضيعة بالعربية</label>
            <input type="text" name="farm_name_ar" id="farm_name_ar" class="mt-1 block w-full border rounded p-2" required placeholder="مثال: ضيعة الزيتونة">
        </div>
        <div class="mb-4">
            <label for="farm_name_lat" class="block font-medium">اسم الضيعة باللاتينية</label>
            <input type="text" name="farm_name_lat" id="farm_name_lat" class="mt-1 block w-full border rounded p-2" required placeholder="ex: Zaytouna Farm">
        </div>
        <div class="mb-4">
            <label for="location" class="block font-medium">المكان (Location)</label>
            <input type="text" name="location" id="location" class="mt-1 block w-full border rounded p-2" required placeholder="مثال: القيروان، تونس">
        </div>
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">كمل التسجيل (Complete Onboarding)</button>
    </form>
</div>
@endsection