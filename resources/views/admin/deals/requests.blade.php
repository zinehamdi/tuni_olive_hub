@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-100 py-8">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">{{ __('Deal Requests') }}</h1>
                <p class="text-gray-600">{{ __('Review incoming interests and requests from users') }}</p>
            </div>
            <div class="flex gap-4">
                <a href="{{ route('admin.deals.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition font-bold">
                    ← {{ __('Manage Deals') }}
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl font-medium">
            {{ session('success') }}
        </div>
        @endif

        <!-- Requests Table -->
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b-2 border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">{{ __('Date') }}</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">{{ __('Deal') }}</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">{{ __('Requester') }}</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">{{ __('Requirements') }}</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">{{ __('Status') }}</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($requests as $req)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $req->created_at->format('Y-m-d H:i') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900">{{ $req->deal->title[app()->getLocale()] ?? $req->deal->title['ar'] ?? 'N/A' }}</div>
                                <div class="text-xs text-amber-600 font-bold uppercase">{{ $req->deal->type }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900">{{ $req->name }}</div>
                                <div class="text-xs text-gray-500">{{ $req->phone }}</div>
                                <div class="text-xs text-gray-400">{{ $req->email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @if($req->requirements)
                                        @foreach($req->requirements as $trait)
                                            <span class="px-2 py-0.5 bg-blue-50 text-blue-700 rounded text-[10px] font-black uppercase tracking-wider">{{ str_replace('_', ' ', $trait) }}</span>
                                        @endforeach
                                    @endif
                                </div>
                                @if($req->message)
                                    <div class="mt-2 text-xs text-gray-600 italic line-clamp-1" title="{{ $req->message }}">
                                        "{{ $req->message }}"
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <form action="{{ route('admin.deals.requests.status', $req) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" onchange="this.form.submit()" class="text-xs font-bold rounded-lg border-gray-200 focus:ring-[#6A8F3B] 
                                        {{ $req->status === 'new' ? 'text-blue-600 bg-blue-50' : ($req->status === 'contacted' ? 'text-amber-600 bg-amber-50' : 'text-gray-600 bg-gray-50') }}">
                                        <option value="new" {{ $req->status === 'new' ? 'selected' : '' }}>{{ __('New') }}</option>
                                        <option value="contacted" {{ $req->status === 'contacted' ? 'selected' : '' }}>{{ __('Contacted') }}</option>
                                        <option value="closed" {{ $req->status === 'closed' ? 'selected' : '' }}>{{ __('Closed') }}</option>
                                    </select>
                                </form>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <form action="{{ route('admin.deals.requests.destroy', $req) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                {{ __('No requests found') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($requests->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $requests->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
