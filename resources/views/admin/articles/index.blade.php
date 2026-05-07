@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-100 py-8">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">{{ __('Manage Articles') }}</h1>
                <p class="text-gray-600">{{ __('Create and edit articles or ads') }}</p>
            </div>
            <div class="flex gap-4">
                <a href="{{ route('admin.dashboard') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition font-bold">
                    ← {{ __('Back to Dashboard') }}
                </a>
                <a href="{{ route('admin.articles.create') }}" class="px-6 py-3 bg-[#6A8F3B] text-white rounded-xl hover:bg-[#5a7a2f] transition font-bold">
                    + {{ __('New Article') }}
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl font-medium">
            {{ session('success') }}
        </div>
        @endif

        <!-- Articles Table -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b-2 border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">{{ __('Image') }}</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">{{ __('Title') }}</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">{{ __('Category') }}</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">{{ __('Status') }}</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($articles as $article)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                @if($article->image)
                                    <img src="{{ Str::startsWith($article->image, ['http://', 'https://']) ? $article->image : asset($article->image) }}" class="w-16 h-12 object-cover rounded-lg" alt="Article image">
                                @else
                                    <div class="w-16 h-12 bg-gray-200 rounded-lg flex items-center justify-center text-gray-400 text-xs">No Image</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-900">{{ $article->title[app()->getLocale()] ?? $article->title['ar'] ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500">{{ $article->created_at->format('Y-m-d') }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $article->category[app()->getLocale()] ?? $article->category['ar'] ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4">
                                @if($article->is_active)
                                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">{{ __('Active') }}</span>
                                @else
                                    <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold">{{ __('Inactive') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.articles.edit', $article) }}"
                                        class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition"
                                        title="{{ __('Edit Article') }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h2m-1 14v-6m0 0V9m0 4H9m2 0h2m4-8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </a>
                                    
                                    <form action="{{ route('admin.articles.destroy', $article) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this article?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="{{ __('Delete Article') }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                {{ __('No articles found') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $articles->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
