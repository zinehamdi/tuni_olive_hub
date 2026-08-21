<nav class="fixed top-0 left-0 right-0 z-50 shadow-xl" dir="ltr" x-data="{ mobileMenuOpen: false, scrolled: false }" @scroll.window="scrolled = window.scrollY > 20">
    <div class="bg-gradient-to-r from-[#5a7a2f] via-[#6A8F3B] to-[#5a7a2f] text-white transition-shadow duration-300" :class="scrolled ? 'shadow-2xl' : 'shadow-xl'">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-14 sm:h-[60px]">
                <!-- Logo -->
                <a href="{{ url('/') }}" class="flex-shrink-0 group flex items-center gap-3 no-underline">
                    <div class="relative w-10 h-10 flex items-center justify-center transition-transform duration-500 group-hover:scale-110">
                        <div class="absolute inset-0 bg-[#6A8F3B]/20 rounded-xl blur-lg group-hover:bg-[#C8A356]/30 transition-all duration-500"></div>
                        <img src="{{ asset('images/zintoop-logo.png') }}" class="relative w-8 h-8 rounded-full object-cover drop-shadow-md logo-animate" alt="ZinToop Logo">
                    </div>
                    <div class="flex flex-col">
                        <span class="text-2xl font-black text-white tracking-tighter leading-none group-hover:text-[#C8A356] transition-colors duration-300">ZinToop</span>
                        <span class="text-[9px] uppercase font-bold tracking-[0.2em] text-white/60 group-hover:text-white transition-colors duration-300">{{ __('Marketplace') }}</span>
                    </div>
                </a>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center gap-1 flex-1 justify-center relative {{ __('ml-6') }}">
                    <a href="{{ route('home') }}" class="group px-4 py-2 rounded-xl hover:bg-white/15 transition-all duration-200 font-medium flex items-center gap-2 text-sm">
                        <div class="w-8 h-8 rounded-lg bg-white/10 group-hover:bg-white/20 flex items-center justify-center transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                        </div>
                        <span>{{ __('Home') }}</span>
                    </a>
                    <a href="{{ route('prices.index') }}" class="group px-4 py-2 rounded-xl hover:bg-white/15 transition-all duration-200 font-medium flex items-center gap-2 text-sm">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-amber-400/30 to-amber-600/30 group-hover:from-amber-400/40 group-hover:to-amber-600/40 flex items-center justify-center transition-all">
                            <span class="text-base">📊</span>
                        </div>
                        <span>{{ __('Prices') }}</span>
                    </a>
                    <a href="{{ route('listings.create') }}" class="group px-4 py-2 rounded-xl hover:bg-white/15 transition-all duration-200 font-medium flex items-center gap-2 text-sm">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-[#6A8F3B]/30 to-[#5a7a2f]/30 group-hover:from-[#6A8F3B]/50 group-hover:to-[#5a7a2f]/50 flex items-center justify-center transition-all">
                            <span class="text-base">🫒</span>
                        </div>
                        <span>{{ __('Sell Your Oil / Olives') }}</span>
                    </a>
                    <a href="{{ route('services.index') }}" class="group px-4 py-2 rounded-xl hover:bg-white/15 transition-all duration-200 font-medium flex items-center gap-2 text-sm">
                        <div class="w-8 h-8 rounded-lg bg-white/10 group-hover:bg-white/20 flex items-center justify-center transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        </div>
                        <span>{{ __('Service Hub') }}</span>
                    </a>
                    <a href="{{ route('services.pricing') }}" class="group px-4 py-2 rounded-xl hover:bg-white/15 transition-all duration-200 font-medium flex items-center gap-2 text-sm">
                        <div class="w-8 h-8 rounded-lg bg-white/10 group-hover:bg-white/20 flex items-center justify-center transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                        </div>
                        <span>{{ __('Our Digital Services') }}</span>
                    </a>
                </div>

                <!-- Right Side Actions -->
                <div class="flex items-center gap-2 sm:gap-3">
                    <!-- Language Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-1.5 px-3 py-1.5 bg-white/10 backdrop-blur-sm text-white rounded-full hover:bg-white/20 transition-all duration-200 font-bold text-xs uppercase border border-white/20 shadow-sm">
                            @php
                                $currentFlag = match(app()->getLocale()) {
                                    'ar' => '🇹🇳',
                                    'fr' => '🇫🇷',
                                    'en' => '🇬🇧',
                                    'es' => '🇪🇸',
                                    'zh' => '🇨🇳',
                                    'ja' => '🇯🇵',
                                    default => '🇹🇳'
                                };
                            @endphp
                            <span class="text-sm leading-none">{{ $currentFlag }}</span>
                            <span>{{ strtoupper(app()->getLocale()) }}</span>
                            <svg class="w-3 h-3 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </button>
                        
                        <div x-show="open" x-cloak @click.away="open = false" 
                             x-transition:enter="transition ease-out duration-200" 
                             x-transition:enter-start="opacity-0 scale-95 -translate-y-2" 
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0" 
                             class="absolute {{ __('right-0') }} mt-2 w-36 bg-white rounded-xl shadow-xl border border-gray-100 py-1 z-[110] overflow-hidden">
                            @php $switchPath = preg_replace('#^/(ar|fr|en|es|zh|ja)#', '', request()->getPathInfo()) ?: '/'; @endphp
                            <a href="{{ url('ar' . $switchPath) }}" class="flex items-center gap-2 px-4 py-2 text-xs font-bold text-gray-700 hover:bg-[#6A8F3B]/10 hover:text-[#6A8F3B] transition {{ __('') }}">
                                <span class="text-sm">🇹🇳</span> العربية (AR)
                            </a>
                            <a href="{{ url('fr' . $switchPath) }}" class="flex items-center gap-2 px-4 py-2 text-xs font-bold text-gray-700 hover:bg-[#6A8F3B]/10 hover:text-[#6A8F3B] transition {{ app()->getLocale()==='fr' ? 'bg-gray-50' : '' }}">
                                <span class="text-sm">🇫🇷</span> Français (FR)
                            </a>
                            <a href="{{ url('en' . $switchPath) }}" class="flex items-center gap-2 px-4 py-2 text-xs font-bold text-gray-700 hover:bg-[#6A8F3B]/10 hover:text-[#6A8F3B] transition {{ app()->getLocale()==='en' ? 'bg-gray-50' : '' }}">
                                <span class="text-sm">🇬🇧</span> English (EN)
                            </a>
                            <a href="{{ url('es' . $switchPath) }}" class="flex items-center gap-2 px-4 py-2 text-xs font-bold text-gray-700 hover:bg-[#6A8F3B]/10 hover:text-[#6A8F3B] transition {{ app()->getLocale()==='es' ? 'bg-gray-50' : '' }}">
                                <span class="text-sm">🇪🇸</span> Español (ES)
                            </a>
                            <a href="{{ url('zh' . $switchPath) }}" class="flex items-center gap-2 px-4 py-2 text-xs font-bold text-gray-700 hover:bg-[#6A8F3B]/10 hover:text-[#6A8F3B] transition {{ app()->getLocale()==='zh' ? 'bg-gray-50' : '' }}">
                                <span class="text-sm">🇨🇳</span> 中文 (ZH)
                            </a>
                            <a href="{{ url('ja' . $switchPath) }}" class="flex items-center gap-2 px-4 py-2 text-xs font-bold text-gray-700 hover:bg-[#6A8F3B]/10 hover:text-[#6A8F3B] transition {{ app()->getLocale()==='ja' ? 'bg-gray-50' : '' }}">
                                <span class="text-sm">🇯🇵</span> 日本語 (JA)
                            </a>
                        </div>
                    </div>

                    <!-- Mobile Menu Button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 rounded-xl hover:bg-white/15 transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Menu Backdrop Overlay -->
    <div x-show="mobileMenuOpen" x-cloak @click="mobileMenuOpen = false" 
         x-transition:enter="transition-opacity ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="md:hidden fixed inset-0 bg-black/60 z-[80]"></div>

    <!-- Mobile Menu Sliding Drawer -->
    <div x-show="mobileMenuOpen" x-cloak 
         x-transition:enter="transition ease-out duration-300 transform" 
         x-transition:enter-start="translate-x-full" 
         x-transition:enter-end="translate-x-0" 
         x-transition:leave="transition ease-in duration-200 transform" 
         x-transition:leave-start="translate-x-0" 
         x-transition:leave-end="translate-x-full" 
         class="md:hidden fixed inset-y-0 {{ __('left-0') }} w-4/5 max-w-sm bg-white shadow-2xl z-[90] flex flex-col overflow-hidden text-{{ __('left') }}">
        
        <div class="px-6 py-6 bg-gradient-to-br from-[#1a3310] to-[#122413] relative overflow-hidden flex-shrink-0">
            <div class="absolute inset-0 bg-[#C8A356]/10"></div>
            <div class="relative z-10">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                    <img src="{{ asset('images/zintoop-logo.png') }}" class="w-10 h-10 rounded-full object-cover drop-shadow-md">
                    <div class="flex flex-col">
                        <span class="text-xl font-black text-white">ZinToop</span>
                        <span class="text-[9px] uppercase font-bold tracking-[0.2em] text-[#C8A356]">{{ __('Marketplace') }}</span>
                    </div>
                </a>
            </div>
            <button @click="mobileMenuOpen = false" class="absolute top-6 {{ __('right-6') }} p-2 rounded-full bg-white/10 text-white hover:bg-white/20 transition-colors z-20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto py-6 px-4 space-y-1 bg-gray-50/50">
            <a href="{{ route('home') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-2xl hover:bg-white hover:shadow-sm transition-all text-gray-700 font-bold">
                <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center"><svg class="w-5 h-5 text-[#6A8F3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg></div>
                {{ __('Home') }}
            </a>
            <a href="{{ route('prices.index') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-2xl hover:bg-white hover:shadow-sm transition-all text-gray-700 font-bold">
                <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-xl">📊</div>
                {{ __('Prices') }}
            </a>
            <a href="{{ route('listings.create') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-2xl hover:bg-white hover:shadow-sm transition-all text-gray-700 font-bold">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-xl">🫒</div>
                {{ __('Sell Your Oil / Olives') }}
            </a>
            <a href="{{ route('services.index') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-2xl hover:bg-white hover:shadow-sm transition-all text-gray-700 font-bold">
                <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center"><svg class="w-5 h-5 text-[#6A8F3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg></div>
                {{ __('Service Hub') }}
            </a>
        </div>
    </div>
</nav>
