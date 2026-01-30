@php
    $locale = app()->getLocale();
    $isRTL = $locale === 'ar';
@endphp

<x-app-layout>
    <div class="min-h-screen bg-gray-100 py-4 sm:py-8" dir="{{ $isRTL ? 'rtl' : 'ltr' }}">
        <div class="max-w-4xl mx-auto px-3 sm:px-6 lg:px-8">
            
            {{-- Header --}}
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-md sm:shadow-lg p-4 sm:p-6 mb-4 sm:mb-6">
                <div class="flex items-center justify-between">
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900 flex items-center gap-2 sm:gap-3">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        {{ __('Inbox') }}
                    </h1>
                    <span class="text-sm text-gray-500">
                        {{ $threads->count() }} {{ __('conversations') }}
                    </span>
                </div>
            </div>

            {{-- Conversations List --}}
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-md sm:shadow-lg overflow-hidden">
                @if($threads->count() > 0)
                    <div class="divide-y divide-gray-100">
                        @foreach($threads as $item)
                            <a href="{{ route('messages.show', $item['other_user']) }}" 
                               class="block p-3 sm:p-4 hover:bg-gray-50 transition-colors {{ $item['unread_count'] > 0 ? 'bg-blue-50/50' : '' }}">
                                <div class="flex items-center gap-3 sm:gap-4">
                                    {{-- Avatar --}}
                                    <div class="relative flex-shrink-0">
                                        @if($item['other_user']->profile_picture)
                                            <img src="{{ Storage::url($item['other_user']->profile_picture) }}" 
                                                 alt="{{ $item['other_user']->name }}"
                                                 class="w-12 h-12 sm:w-14 sm:h-14 rounded-full object-cover ring-2 ring-white shadow">
                                        @else
                                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center text-white text-lg sm:text-xl font-bold ring-2 ring-white shadow">
                                                {{ strtoupper(substr($item['other_user']->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        
                                        {{-- Unread indicator --}}
                                        @if($item['unread_count'] > 0)
                                            <span class="absolute -top-1 {{ $isRTL ? '-left-1' : '-right-1' }} w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-bold">
                                                {{ $item['unread_count'] > 9 ? '9+' : $item['unread_count'] }}
                                            </span>
                                        @endif
                                    </div>
                                    
                                    {{-- Content --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between mb-1">
                                            <h3 class="font-semibold text-gray-900 truncate {{ $item['unread_count'] > 0 ? 'text-blue-900' : '' }}">
                                                {{ $item['other_user']->name }}
                                            </h3>
                                            @if($item['last_message'])
                                                <span class="text-xs text-gray-500 flex-shrink-0 {{ $isRTL ? 'mr-2' : 'ml-2' }}">
                                                    {{ $item['last_message']->created_at->diffForHumans() }}
                                                </span>
                                            @endif
                                        </div>
                                        
                                        {{-- Role badge --}}
                                        <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium mb-1
                                            {{ $item['other_user']->role === 'farmer' ? 'bg-green-100 text-green-700' : '' }}
                                            {{ $item['other_user']->role === 'carrier' ? 'bg-blue-100 text-blue-700' : '' }}
                                            {{ $item['other_user']->role === 'mill' ? 'bg-amber-100 text-amber-700' : '' }}
                                            {{ $item['other_user']->role === 'packer' ? 'bg-purple-100 text-purple-700' : '' }}
                                            {{ !in_array($item['other_user']->role, ['farmer', 'carrier', 'mill', 'packer']) ? 'bg-gray-100 text-gray-700' : '' }}
                                        ">
                                            {{ __($item['other_user']->role) }}
                                        </span>
                                        
                                        {{-- Last message preview --}}
                                        @if($item['last_message'])
                                            <p class="text-sm text-gray-600 truncate {{ $item['unread_count'] > 0 ? 'font-medium' : '' }}">
                                                @if($item['last_message']->sender_id === auth()->id())
                                                    <span class="text-gray-400">{{ __('You') }}:</span>
                                                @endif
                                                {{ Str::limit($item['last_message']->body, 50) }}
                                            </p>
                                        @else
                                            <p class="text-sm text-gray-400 italic">{{ __('No messages yet') }}</p>
                                        @endif
                                    </div>
                                    
                                    {{-- Arrow --}}
                                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0 {{ $isRTL ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    {{-- Empty State --}}
                    <div class="text-center py-16">
                        <div class="w-20 h-20 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('No messages yet') }}</h3>
                        <p class="text-gray-500 max-w-sm mx-auto">
                            {{ __('Start a conversation by visiting a user profile and clicking the Message button.') }}
                        </p>
                    </div>
                @endif
            </div>
            
        </div>
    </div>
</x-app-layout>
