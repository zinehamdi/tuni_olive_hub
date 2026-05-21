import re

with open('resources/views/profile/public.blade.php', 'r') as f:
    content = f.read()

# 1. Fix the Alpine flashing by providing default text, and fix the mobile Header alignment
header_old = """                <!-- Avatar & Name -->
                <div class="px-4 sm:px-8 pb-6 relative flex flex-col sm:flex-row gap-6 items-end sm:items-center -mt-16 sm:-mt-20 z-10">
                    <div class="relative group">
                        @if($profilePhotoUrl)
                            <img src="{{ $profilePhotoUrl }}" class="w-32 h-32 sm:w-40 sm:h-40 rounded-full object-cover ring-4 ring-white shadow-xl bg-white" loading="lazy" onerror="this.src='{{ asset('images/zintooplogo3d.jpg') }}'">
                        @else
                            <div class="w-32 h-32 sm:w-40 sm:h-40 rounded-full bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center text-white text-5xl font-bold ring-4 ring-white shadow-xl">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                        <div class="absolute bottom-2 {{ $isRTL ? 'left-2' : 'right-2' }} w-6 h-6 bg-green-500 rounded-full ring-4 ring-white shadow-md"></div>
                    </div>
                    
                    <div class="flex-1 mt-16 sm:mt-0 pt-4 sm:pt-20">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-2">
                                    {{ $user->name }}
                                    @if($user->trust_score > 80)
                                        <svg class="w-6 h-6 text-blue-500" fill="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                    @endif
                                </h1>
                                <p class="text-gray-500 font-medium mt-1">
                                    @if($user->role === 'farmer' && ($user->farm_name || $user->farm_name_ar))
                                        🌾 {{ $user->farm_name ?? $user->farm_name_ar }}
                                    @elseif($user->role === 'mill' && $user->mill_name)
                                        🏭 {{ $user->mill_name }}
                                    @elseif($user->role === 'carrier' && $user->company_name)
                                        🚛 {{ $user->company_name }}
                                    @elseif($user->role === 'packer' && $user->packer_name)
                                        📦 {{ $user->packer_name }}
                                    @else
                                        {{ $user->role === 'farmer' ? __('Olive grower') : ($user->role === 'mill' ? __('Oil mill') : ($user->role === 'carrier' ? __('Transporter') : ($user->role === 'packer' ? __('Packaging') : __('Member')))) }}
                                    @endif
                                </p>
                            </div>
                            
                            <!-- Actions -->
                            @if(!$isOwner)
                            <div x-data="userInteraction()" x-init="init()" class="flex gap-2">
                                <button @click="confirmFollow()" :class="followed ? 'bg-gray-100 text-gray-800' : 'bg-gradient-to-r from-emerald-500 to-teal-600 text-white'" class="px-5 py-2.5 rounded-xl font-bold text-sm shadow-md hover:shadow-lg transition flex items-center gap-2">
                                    <span x-text="followed ? '{{ __('Following') }}' : '{{ __('Follow') }}'"></span>
                                    <span class="px-2 py-0.5 rounded-full text-xs" :class="followed ? 'bg-white' : 'bg-white/20'" x-text="followerCount"></span>
                                </button>
                                <button @click="confirmLike()" :class="liked ? 'bg-rose-50 text-rose-600 border border-rose-200' : 'bg-white text-gray-700 border border-gray-200'" class="px-4 py-2.5 rounded-xl font-bold text-sm shadow-sm hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 transition flex items-center gap-2">
                                    <svg class="w-5 h-5" :class="liked ? 'fill-rose-500 text-rose-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                                    <span x-text="likeCount"></span>
                                </button>
                                <a href="{{ auth()->check() ? route('messages.show', $user) : route('login') }}" class="px-4 py-2.5 bg-gray-900 text-white rounded-xl font-bold text-sm shadow-md hover:bg-gray-800 transition flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>"""

header_new = """                <!-- Avatar & Name -->
                <div class="px-4 sm:px-8 pb-6 relative flex flex-col sm:flex-row gap-4 sm:gap-6 items-center sm:items-end -mt-16 sm:-mt-20 z-10">
                    <div class="relative group shrink-0">
                        @if($profilePhotoUrl)
                            <img src="{{ $profilePhotoUrl }}" class="w-32 h-32 sm:w-40 sm:h-40 rounded-full object-cover ring-4 ring-white shadow-xl bg-white" loading="lazy" onerror="this.src='{{ asset('images/zintooplogo3d.jpg') }}'">
                        @else
                            <div class="w-32 h-32 sm:w-40 sm:h-40 rounded-full bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center text-white text-5xl font-bold ring-4 ring-white shadow-xl">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                        <div class="absolute bottom-2 {{ $isRTL ? 'left-2' : 'right-2' }} w-6 h-6 bg-green-500 rounded-full ring-4 ring-white shadow-md"></div>
                    </div>
                    
                    <div class="flex-1 w-full flex flex-col sm:flex-row sm:items-end justify-between gap-4 mt-2 sm:mt-0 sm:pt-20">
                        <div class="text-center sm:text-start">
                            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 flex items-center justify-center sm:justify-start gap-2">
                                {{ $user->name }}
                                @if($user->trust_score > 80)
                                    <svg class="w-6 h-6 text-blue-500" fill="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                @endif
                            </h1>
                            <div class="mt-2 inline-flex items-center gap-2 bg-emerald-50 text-emerald-700 px-3 py-1 rounded-lg border border-emerald-100 font-bold text-sm">
                                @if($user->role === 'farmer' && ($user->farm_name || $user->farm_name_ar))
                                    🌾 {{ $user->farm_name ?? $user->farm_name_ar }}
                                @elseif($user->role === 'mill' && $user->mill_name)
                                    🏭 {{ $user->mill_name }}
                                @elseif($user->role === 'carrier' && $user->company_name)
                                    🚛 {{ $user->company_name }}
                                @elseif($user->role === 'packer' && $user->packer_name)
                                    📦 {{ $user->packer_name }}
                                @else
                                    {{ $user->role === 'farmer' ? __('Olive grower') : ($user->role === 'mill' ? __('Oil mill') : ($user->role === 'carrier' ? __('Transporter') : ($user->role === 'packer' ? __('Packaging') : __('Member')))) }}
                                @endif
                            </div>
                        </div>
                        
                        <!-- Actions -->
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
                        @endif
                    </div>
                </div>"""

content = content.replace(header_old, header_new)


# 2. Change flex layout ordering for Mobile view
content = content.replace(
    '<aside class="w-full xl:w-72 flex-shrink-0 flex flex-col gap-4">',
    '<aside class="w-full xl:w-72 flex-shrink-0 flex flex-col gap-4 order-3 xl:order-1">'
)

content = content.replace(
    '<main class="flex-1 min-w-0">',
    '<main class="flex-1 min-w-0 w-full order-2 xl:order-2">'
)

content = content.replace(
    '<!-- RIGHT SIDEBAR: STORIES & GALLERY -->\n                <aside class="w-full xl:w-72 flex-shrink-0 flex flex-col gap-4">',
    '<!-- RIGHT SIDEBAR: STORIES & GALLERY -->\n                <aside class="w-full xl:w-72 flex-shrink-0 flex flex-col gap-4 order-1 xl:order-3">'
)


with open('resources/views/profile/public.blade.php', 'w') as f:
    f.write(content)
print("Updated successfully")
