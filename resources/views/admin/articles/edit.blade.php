@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-100 py-8">
    <div class="max-w-4xl mx-auto px-4">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-900">{{ __('Edit Article') }}</h1>
            <a href="{{ route('admin.articles.index') }}" class="text-gray-500 hover:text-gray-700">
                ← {{ __('Back') }}
            </a>
        </div>

        <form action="{{ route('admin.articles.update', $article) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-xl p-8 space-y-6">
            @csrf
            @method('PUT')

            @if($errors->any())
            <div class="p-4 bg-red-50 border border-red-200 rounded-xl mb-4">
                <ul class="list-disc list-inside text-red-700 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Translations Grid -->
            @foreach(['ar' => 'Arabic', 'en' => 'English', 'fr' => 'French'] as $lang => $label)
            <div class="border-b pb-6 mb-6">
                <h3 class="text-xl font-bold mb-4 text-[#6A8F3B]">{{ $label }}</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Title') }} ({{ $lang }})</label>
                        <input type="text" name="title[{{ $lang }}]" value="{{ old('title.'.$lang, $article->title[$lang] ?? '') }}" class="w-full rounded-xl border-gray-300 focus:border-[#6A8F3B] focus:ring focus:ring-[#6A8F3B]/20" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Category') }} ({{ $lang }})</label>
                        <input type="text" name="category[{{ $lang }}]" value="{{ old('category.'.$lang, $article->category[$lang] ?? '') }}" class="w-full rounded-xl border-gray-300 focus:border-[#6A8F3B] focus:ring focus:ring-[#6A8F3B]/20" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Content') }} ({{ $lang }})</label>
                        <textarea name="content[{{ $lang }}]" rows="5" class="w-full rounded-xl border-gray-300 focus:border-[#6A8F3B] focus:ring focus:ring-[#6A8F3B]/20" required>{{ old('content.'.$lang, $article->content[$lang] ?? '') }}</textarea>
                    </div>
                </div>
            </div>
            @endforeach

            <!-- Common Fields -->
            <div class="space-y-4">
                @if($article->image)
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Current Image') }}</label>
                    <img src="{{ Str::startsWith($article->image, ['http://', 'https://']) ? $article->image : asset($article->image) }}" class="h-32 rounded-lg object-cover" alt="Article image">
                </div>
                @endif
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Replace Image (Optional)') }}</label>
                    <input type="file" name="image" class="w-full rounded-xl border-gray-300 focus:border-[#6A8F3B] focus:ring focus:ring-[#6A8F3B]/20" accept="image/*">
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1" class="rounded text-[#6A8F3B] focus:ring-[#6A8F3B]" {{ old('is_active', $article->is_active) ? 'checked' : '' }}>
                    <label for="is_active" class="text-sm font-medium text-gray-700">{{ __('Active') }}</label>
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full py-3 bg-[#6A8F3B] text-white rounded-xl hover:bg-[#5a7a2f] transition font-bold text-lg">
                    {{ __('Update Article') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
