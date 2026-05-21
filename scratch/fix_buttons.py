import re

with open('resources/views/profile/public.blade.php', 'r') as f:
    content = f.read()

old_actions = """                        <!-- Actions -->
                        @if(!$isOwner)
                        <div x-data="userInteraction()" x-init="init()" class="flex justify-center sm:justify-end gap-2 w-full sm:w-auto mt-2 sm:mt-0" x-cloak>
                            <button @click="confirmFollow()" :class="followed ? 'bg-gray-100 text-gray-800' : 'bg-gradient-to-r from-emerald-500 to-teal-600 text-white'" class="flex-1 sm:flex-none justify-center px-5 py-2.5 rounded-xl font-bold text-sm shadow-md hover:shadow-lg transition flex items-center gap-2">
                                <span x-text="followed ? '{{ __('Following') }}' : '{{ __('Follow') }}'">{{ __('Follow') }}</span>
                                <span class="px-2 py-0.5 rounded-full text-xs" :class="followed ? 'bg-white' : 'bg-white/20'" x-text="followerCount || '-'"></span>
                            </button>
                            <button @click="confirmLike()" :class="liked ? 'bg-rose-50 text-rose-600 border border-rose-200' : 'bg-white text-gray-700 border border-gray-200'" class="px-4 py-2.5 rounded-xl font-bold text-sm shadow-sm hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 transition flex items-center gap-2">
                                <svg class="w-5 h-5" :class="liked ? 'fill-rose-500 text-rose-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                                <span x-text="likeCount || '-'"></span>
                            </button>
                            <a href="{{ auth()->check() ? route('messages.show', $user) : route('login') }}" class="px-4 py-2.5 bg-gray-900 text-white rounded-xl font-bold text-sm shadow-md hover:bg-gray-800 transition flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                            </a>
                        </div>
                        @endif"""

new_actions = """                        <!-- Actions -->
                        @if(!$isOwner)
                        <div x-data="userInteraction()" x-init="init()" class="flex justify-center sm:justify-end gap-2 w-full sm:w-auto mt-4 sm:mt-0">
                            <button @click="confirmFollow()" :class="followed ? 'bg-gray-100 text-gray-800' : 'bg-gradient-to-r from-emerald-500 to-teal-600 text-white'" class="bg-gradient-to-r from-emerald-500 to-teal-600 text-white flex-1 sm:flex-none justify-center px-5 py-2.5 rounded-xl font-bold text-sm shadow-md hover:shadow-lg transition flex items-center gap-2">
                                <span x-text="followed ? '{{ __('Following') }}' : '{{ __('Follow') }}'">{{ __('Follow') }}</span>
                                <span class="px-2 py-0.5 rounded-full text-xs bg-white/20" :class="followed ? 'bg-white' : 'bg-white/20'" x-text="followerCount !== null ? followerCount : '-'">-</span>
                            </button>
                            <button @click="confirmLike()" :class="liked ? 'bg-rose-50 text-rose-600 border-rose-200' : 'bg-white text-gray-700 border-gray-200'" class="bg-white text-gray-700 border border-gray-200 px-4 py-2.5 rounded-xl font-bold text-sm shadow-sm hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 transition flex items-center gap-2">
                                <svg class="w-5 h-5 text-gray-400" :class="liked ? 'fill-rose-500 text-rose-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                                <span x-text="likeCount !== null ? likeCount : '-'">-</span>
                            </button>
                            <a href="{{ auth()->check() ? route('messages.show', $user) : route('login') }}" class="px-4 py-2.5 bg-gray-900 text-white rounded-xl font-bold text-sm shadow-md hover:bg-gray-800 transition flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                            </a>
                        </div>
                        @endif"""

content = content.replace(old_actions, new_actions)

with open('resources/views/profile/public.blade.php', 'w') as f:
    f.write(content)
print("done")
