@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-100 py-8">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">{{ __('Manage Deals') }}</h1>
                <p class="text-gray-600">{{ __('Create and edit requests, services, or supplier offers') }}</p>
            </div>
            <div class="flex gap-4">
                <a href="{{ route('admin.dashboard') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition font-bold">
                    ← {{ __('Back to Dashboard') }}
                </a>
                <a href="{{ route('admin.deals.create') }}" class="px-6 py-3 bg-amber-600 text-white rounded-xl hover:bg-amber-700 transition font-bold shadow-lg">
                    + {{ __('New Deal') }}
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl font-medium">
            {{ session('success') }}
        </div>
        @endif

        <!-- Deals Table -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b-2 border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">{{ __('Type') }}</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">{{ __('Title') }}</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">{{ __('User') }}</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">{{ __('Status') }}</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">{{ __('Featured') }}</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($deals as $deal)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                @php
                                    $colors = [
                                        'demand' => 'bg-amber-100 text-amber-800 border-amber-200',
                                        'service' => 'bg-blue-100 text-blue-800 border-blue-200',
                                        'supply' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                    ];
                                @endphp
                                <span class="px-3 py-1 border rounded-full text-xs font-bold uppercase tracking-wider {{ $colors[$deal->type] }}">
                                    {{ __($deal->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900">{{ $deal->title[app()->getLocale()] ?? $deal->title['ar'] ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500">{{ $deal->location }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $deal->user->name }}
                            </td>
                            <td class="px-6 py-4">
                                @if($deal->status === 'active')
                                    <span class="flex items-center gap-1.5 text-green-600 font-bold text-sm">
                                        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                                        {{ __('Active') }}
                                    </span>
                                @else
                                    <span class="text-gray-400 font-bold text-sm">{{ __($deal->status) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($deal->is_featured)
                                    <span class="text-amber-500">★</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.deals.edit', $deal) }}"
                                        class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition"
                                        title="{{ __('Edit Deal') }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h2m-1 14v-6m0 0V9m0 4H9m2 0h2m4-8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </a>
                                    
                                    <form action="{{ route('admin.deals.destroy', $deal) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this deal?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="{{ __('Delete Deal') }}">
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
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                {{ __('No deals found') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($deals->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $deals->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
