@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-100 py-8">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">{{ __('Manage Users') }}</h1>
                <p class="text-gray-600">{{ __('View and moderate all platform users') }}</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition font-bold">
                ← {{ __('Back to Dashboard') }}
            </a>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-6">
            <form method="GET" action="{{ route('admin.users') }}" class="flex flex-wrap gap-4">
                <!-- Role Filter -->
                <select name="role" class="px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-[#6A8F3B] focus:ring-4 focus:ring-[#6A8F3B]/20">
                    <option value="all">{{ __('All Roles') }}</option>
                    <option value="farmer" {{ request('role') === 'farmer' ? 'selected' : '' }}>{{ __('Farmers') }}</option>
                    <option value="carrier" {{ request('role') === 'carrier' ? 'selected' : '' }}>{{ __('Carriers') }}</option>
                    <option value="mill" {{ request('role') === 'mill' ? 'selected' : '' }}>{{ __('Mills') }}</option>
                    <option value="packer" {{ request('role') === 'packer' ? 'selected' : '' }}>{{ __('Packers') }}</option>
                    <option value="normal" {{ request('role') === 'normal' ? 'selected' : '' }}>{{ __('Normal Users') }}</option>
                </select>

                <!-- Search -->
                <input type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="{{ __('Search by name, email, or phone') }}" 
                    class="flex-1 px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-[#6A8F3B] focus:ring-4 focus:ring-[#6A8F3B]/20">

                <button type="submit" class="px-6 py-2 bg-[#6A8F3B] text-white rounded-xl hover:bg-[#5a7a2f] transition font-bold">
                    {{ __('Filter') }}
                </button>

                @if(request('role') || request('search'))
                <a href="{{ route('admin.users') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition font-bold">
                    {{ __('Clear') }}
                </a>
                @endif
            </form>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b-2 border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">{{ __('User') }}</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">{{ __('Role') }}</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">{{ __('Contact') }}</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">{{ __('Location') }}</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">{{ __('Registered') }}</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($users as $user)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-600">ID: {{ $user->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $user->email }}</div>
                                <div class="text-sm text-gray-600">{{ $user->phone ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    @if($user->addresses->first())
                                        {{ $user->addresses->first()->governorate ?? 'N/A' }}
                                    @else
                                        N/A
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $user->created_at->format('Y-m-d') }}</div>
                                <div class="text-xs text-gray-600">{{ $user->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('user.profile', $user) }}" 
                                        class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" 
                                        title="{{ __('View Profile') }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    
                                    <form action="{{ route('admin.users.ban', $user) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure you want to ban this user?') }}')">
                                        @csrf
                                        <button type="submit" class="p-2 text-orange-600 hover:bg-orange-50 rounded-lg transition" title="{{ __('Ban User') }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                            </svg>
                                        </button>
                                    </form>
                                    
                                    <form action="{{ route('admin.users.delete', $user) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this user? This action cannot be undone.') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="{{ __('Delete User') }}">
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
                                {{ __('No users found') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
