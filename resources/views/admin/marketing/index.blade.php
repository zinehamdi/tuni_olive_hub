@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-100 py-8">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">
                    {{ app()->getLocale() === 'ar' ? 'مواعيد التسويق' : __('Marketing Appointments') }}
                </h1>
                <p class="text-gray-600">{{ __('View and manage client marketing requests') }}</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 transition font-semibold">
                <svg class="w-5 h-5 {{ __('') }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ __('Back to Dashboard') }}
            </a>
        </div>

        @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-xl shadow-sm">
            {{ session('success') }}
        </div>
        @endif

        <!-- Appointments Table -->
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="w-full text-{{ __('left') }}">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-6 py-4 text-sm font-bold text-gray-700">{{ __('Client') }}</th>
                            <th class="px-6 py-4 text-sm font-bold text-gray-700">{{ __('Business Info') }}</th>
                            <th class="px-6 py-4 text-sm font-bold text-gray-700">{{ __('Date') }}</th>
                            <th class="px-6 py-4 text-sm font-bold text-gray-700">{{ __('Budget') }}</th>
                            <th class="px-6 py-4 text-sm font-bold text-gray-700">{{ __('Status') }}</th>
                            <th class="px-6 py-4 text-sm font-bold text-gray-700">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($appointments as $appointment)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900">{{ $appointment->name }}</div>
                                <div class="text-sm text-gray-500 font-mono">{{ $appointment->phone }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-700 max-w-xs truncate">{{ $appointment->business_info }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-[#6A8F3B]">{{ $appointment->appointment_date->format('Y-m-d H:i') }}</div>
                                <div class="text-xs text-gray-400">{{ $appointment->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-gray-900">{{ number_format($appointment->total_budget, 2) }}</span>
                                <span class="text-xs text-gray-500">TND</span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusClasses = [
                                        'pending' => 'bg-orange-100 text-orange-700 border-orange-200',
                                        'confirmed' => 'bg-blue-100 text-blue-700 border-blue-200',
                                        'completed' => 'bg-green-100 text-green-700 border-green-200',
                                        'cancelled' => 'bg-red-100 text-red-700 border-red-200',
                                    ];
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $statusClasses[$appointment->status] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.marketing.edit', $appointment) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="{{ __('Edit') }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 00-2 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.marketing.destroy', $appointment) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="{{ __('Delete') }}">
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
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500 italic">
                                {{ __('No marketing appointments found.') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($appointments->hasPages())
            <div class="px-6 py-4 border-t border-gray-50">
                {{ $appointments->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
