@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-8" x-data="testData">
    <div class="bg-white p-6 rounded-lg shadow">
        <h1 class="text-2xl font-bold mb-4">Alpine.js Test</h1>
        
        <p class="mb-4">Test Message: <span x-text="test"></span></p>
        
        <p class="mb-4">Count: <span x-text="count"></span></p>
        
        <button @click="count++" class="bg-blue-500 text-white px-4 py-2 rounded">
            Increment
        </button>
        
        <hr class="my-6">
        
        <h2 class="text-xl font-bold mb-4">Products Test</h2>
        <p>Products from controller: {{ count($products) }}</p>
        
        <div x-data="productsData">
            <p>Products in Alpine: <span x-text="products.length"></span></p>
            <template x-for="product in products" :key="product.id">
                <div class="p-2 border mb-2">
                    <span x-text="product.variety"></span> - 
                    <span x-text="product.type"></span>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('testData', () => ({
        test: 'Alpine is working!',
        count: 0
    }));
    
    Alpine.data('productsData', () => ({
        products: @json($products)
    }));
});
</script>
@endsection
