@php
    $isLoggedIn = auth()->check();
    $userName = $isLoggedIn ? auth()->user()->name : '';
    $userRole = $isLoggedIn ? auth()->user()->role : '';
    $currentLocale = app()->getLocale();
    $isRTL = in_array($currentLocale, ['ar']);

    if ($isLoggedIn) {
        $welcomeContent = match($currentLocale) {
            'en' => "Welcome back, **{$userName}**! 🫒 I am **Ezzitouni**, your AI Agricultural and Trade Advisor on ZinToop. How can I assist you today with your offers, market prices, buyer deals, or export regulations?",
            'fr' => "Bienvenue, **{$userName}** ! 🫒 Je suis **Ez-Zitouni**, votre conseiller agricole et commercial sur ZinToop. Comment puis-je vous aider aujourd'hui concernant vos offres, les prix du marché, les opportunités d'achat/vente ou l'exportation ?",
            default => "مرحباً بك يا **{$userName}**! 🫒 أنا «**الزيتوني**»، مستشارك وخبيرك التجاري والزراعي في منصة ZinToop. تفضل بسؤالي عن أي موضوع: عروضك، أسعار أسواق اليوم، صفقات الشراء والبيع، أو إجراءات التصدير.",
        };
    } else {
        $welcomeContent = match($currentLocale) {
            'en' => "Hello and welcome to **ZinToop**! 🫒 I am **Ezzitouni**, your AI Agricultural and Trade Consultant. Feel free to ask me anything about Tunisian olive oil varieties, daily souk prices, buying directly with 0% commission, or export specifications.",
            'fr' => "Bonjour et bienvenue sur **ZinToop** ! 🫒 Je suis **Ez-Zitouni**, votre conseiller agricole et commercial IA. Posez-moi vos questions sur les variétés d'huile tunisienne, les prix des marchés, l'achat direct sans commission ou le cahier des charges export.",
            default => "أهلاً ومرحباً بك في **ZinToop**! 🫒 أنا «**الزيتوني**»، مستشارك وخبيرك الزراعي والتجاري الذكي. تنجم تسألني على أي حاجة: أصناف الزيتون التونسي، أسعار الأسواق اليومية، الشراء والبيع مباشرة بدون وسيط وبدون عمولة، أو إجراءات التصدير.",
        };
    }

    $inputPlaceholder = match($currentLocale) {
        'fr' => "Posez une question sur les prix, l'achat, la vente, l'export...",
        'en' => "Ask Ezzitouni about prices, varieties, buying, export...",
        default => "اسأل الزيتوني عن الأسعار، الشراء، البيع، التصدير...",
    };

    $analyzingText = match($currentLocale) {
        'fr' => "Ez-Zitouni analyse votre demande...",
        'en' => "Ezzitouni is analyzing...",
        default => "الزيتوني يحلل استفسارك...",
    };

    $footerText = match($currentLocale) {
        'fr' => "Conseiller IA ZinToop • Mise en relation directe 0% commission",
        'en' => "ZinToop AI Consultant • Instant direct matching 0% commission",
        default => "مستشار ZinToop الذكي • تواصل مباشر بدون وسيط وبـ 0% عمولة",
    };
@endphp

<div x-data="ezzitouniChat()" class="fixed bottom-20 md:bottom-6 left-3 sm:left-6 z-[9999]" dir="{{ $isRTL ? 'rtl' : 'ltr' }}">
    
    <!-- Chat Toggle Button (Avatar / Close Icon) -->
    <button @click="toggleChat()" 
            type="button"
            class="relative w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-white shadow-[0_8px_30px_rgba(106,143,59,0.4)] border-3 sm:border-4 border-white hover:scale-105 active:scale-95 transition-all flex items-center justify-center overflow-hidden z-20 group focus:outline-none touch-manipulation"
            aria-label="{{ __('Zitouni (Ezzitouni)') }}">
        <!-- Ezzitouni Avatar -->
        <img x-show="!isOpen" src="{{ asset('images/ezzitouni_bot.png') }}" alt="Zitouni" class="w-full h-full object-cover">
        
        <!-- Close (X) Icon when chat is open -->
        <div x-show="isOpen" class="text-[#6A8F3B]" style="display: none;">
            <svg class="w-7 h-7 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </div>

        <!-- Online Indicator -->
        <span x-show="!isOpen" class="absolute bottom-0 right-1 w-3 h-3 sm:w-3.5 sm:h-3.5 bg-emerald-500 border-2 border-white rounded-full"></span>
    </button>

    <!-- Welcome Tooltip (Desktop only) -->
    <div x-show="!isOpen && showTooltip" 
         x-transition.opacity.duration.300ms
         class="absolute bottom-20 left-0 w-64 bg-white rounded-2xl p-3.5 shadow-xl border border-[#6A8F3B]/25 pointer-events-auto origin-bottom-left hidden md:block" 
         style="display: none;">
        
        <div class="flex items-center justify-between gap-2 mb-1.5">
            <div class="flex items-center gap-1.5">
                <h4 class="font-black text-[#6A8F3B] text-[10px] uppercase tracking-wider">{{ __('Zitouni (Ezzitouni)') }}</h4>
                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
            </div>
            <button @click.stop="closeTooltip()" class="text-gray-400 hover:text-gray-600 p-0.5 rounded-full hover:bg-gray-100 transition" aria-label="Close tooltip">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="relative h-12">
            <template x-for="(msg, index) in zitouniMessages" :key="index">
                <p x-show="zitouniIndex === index" 
                   x-transition:enter="transition ease-out duration-300"
                   x-transition:enter-start="opacity-0 translate-y-2"
                   x-transition:enter-end="opacity-100 translate-y-0"
                   x-transition:leave="transition ease-in duration-200 absolute inset-0"
                   x-transition:leave-start="opacity-100 translate-y-0"
                   x-transition:leave-end="opacity-0 -translate-y-2"
                   class="text-[11px] text-gray-700 font-bold leading-snug"
                   x-text="msg">
                </p>
            </template>
        </div>

        <div class="mt-1 flex gap-1">
            <template x-for="(msg, index) in zitouniMessages" :key="index">
                <div class="w-1 h-1 rounded-full transition-all duration-300"
                     :class="zitouniIndex === index ? 'bg-[#6A8F3B] w-2.5' : 'bg-gray-200'"></div>
            </template>
        </div>

        <!-- Decorative arrow -->
        <div class="absolute -bottom-1.5 left-6 w-3 h-3 bg-white border-b border-r border-[#6A8F3B]/25 transform rotate-45"></div>
    </div>

    <!-- Chat Window Container (Optimized for Mobile & Desktop) -->
    <div x-show="isOpen" 
         x-transition:enter="transition ease-out duration-250 transform"
         x-transition:enter-start="opacity-0 translate-y-6 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150 transform"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-6 scale-95"
         class="fixed sm:absolute bottom-20 left-3 right-3 sm:left-0 sm:right-auto w-auto sm:w-[380px] max-w-[calc(100vw-1.5rem)] bg-white rounded-3xl shadow-[0_15px_50px_rgba(0,0,0,0.18)] overflow-hidden border border-gray-100 flex flex-col z-30 touch-manipulation"
         style="height: 520px; max-height: calc(100dvh - 7rem); display: none;">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-[#6A8F3B] via-[#5a7a2f] to-[#3B5998] px-4 py-3.5 flex items-center justify-between text-white shadow-sm shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-full bg-white overflow-hidden p-0.5 shadow-sm shrink-0">
                    <img src="{{ asset('images/ezzitouni_bot.png') }}" alt="Zitouni" class="w-full h-full object-cover rounded-full">
                </div>
                <div>
                    <div class="flex items-center gap-1.5">
                        <h3 class="font-black text-sm leading-none">{{ __('Zitouni (Ezzitouni)') }}</h3>
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    </div>
                    <p class="text-[10px] sm:text-[11px] text-white/90 font-medium mt-0.5">{{ __('AI Agricultural & Trade Consultant') }}</p>
                </div>
            </div>
            <button @click="toggleChat()" type="button" class="w-7 h-7 flex items-center justify-center rounded-full hover:bg-white/20 transition text-white" aria-label="Close chat">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <!-- Messages Area (Smooth Native Scroll, Zero Clutter) -->
        <div id="ezzitouni-chat-box" class="flex-1 overflow-y-auto p-3.5 space-y-3 bg-[#F9FAF7] flex flex-col overscroll-contain" style="-webkit-overflow-scrolling: touch;">
            <template x-for="(msg, index) in messages" :key="index">
                <div class="flex flex-col w-full" :class="msg.role === 'user' ? 'items-end' : 'items-start'">
                    
                    <!-- Bubble Content -->
                    <div class="max-w-[88%] rounded-2xl p-3 text-xs sm:text-[13px] leading-relaxed shadow-sm transition-all"
                         :class="msg.role === 'user' 
                            ? 'bg-[#6A8F3B] text-white {{ $isRTL ? 'rounded-bl-none' : 'rounded-br-none' }} font-medium' 
                            : 'bg-white text-gray-800 border border-gray-100 {{ $isRTL ? 'rounded-br-none' : 'rounded-bl-none' }} shadow-[0_2px_8px_rgba(0,0,0,0.03)]'">
                        
                        <div x-html="formatMessage(msg.content)" class="space-y-1.5 break-words"></div>
                    </div>

                    <!-- Dynamic Action Buttons (Only attached contextually under specific bot replies) -->
                    <template x-if="msg.buttons && msg.buttons.length > 0">
                        <div class="w-full max-w-[88%] flex flex-wrap gap-1.5 mt-2">
                            <template x-for="(btn, bIndex) in msg.buttons" :key="bIndex">
                                <a :href="btn.url" 
                                   @click="handleButtonClick(btn, $event)"
                                   :target="btn.url.endsWith('.pdf') ? '_blank' : '_self'"
                                   class="px-3 py-2 rounded-xl text-xs font-bold transition-all shadow-sm flex items-center justify-center gap-1.5 text-center flex-1 min-w-[125px] active:scale-95 transform cursor-pointer touch-manipulation"
                                   :class="{
                                        'bg-[#6A8F3B] text-white hover:bg-[#5a7a2f] shadow-green-100': btn.type === 'primary',
                                        'bg-amber-600 text-white hover:bg-amber-700 shadow-amber-100': btn.type === 'secondary',
                                        'bg-emerald-600 text-white hover:bg-emerald-700 shadow-emerald-100': btn.type === 'auth',
                                        'bg-white border border-[#6A8F3B]/40 text-[#2C4119] hover:bg-[#6A8F3B] hover:text-white': btn.type === 'outline' || !btn.type
                                   }">
                                    <span x-text="btn.label"></span>
                                    <svg class="w-3.5 h-3.5 opacity-80 {{ $isRTL ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg>
                                </a>
                            </template>
                        </div>
                    </template>

                </div>
            </template>
            
            <!-- Typing Indicator -->
            <div x-show="isTyping" class="flex justify-start w-full">
                <div class="bg-white border border-gray-100 rounded-2xl rounded-bl-none px-3.5 py-2.5 shadow-sm flex items-center gap-1.5">
                    <span class="text-[10px] text-gray-400 font-bold ml-1">{{ $analyzingText }}</span>
                    <div class="w-1.5 h-1.5 bg-[#6A8F3B] rounded-full animate-bounce" style="animation-delay: 0s"></div>
                    <div class="w-1.5 h-1.5 bg-[#6A8F3B] rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                    <div class="w-1.5 h-1.5 bg-[#6A8F3B] rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
                </div>
            </div>
        </div>

        <!-- Input Area (Ultra Responsive & Fluid) -->
        <div class="p-2.5 sm:p-3 bg-white border-t border-gray-100 shrink-0">
            <form @submit.prevent="sendMessage" class="flex gap-2 items-center">
                <input x-model="newMessage" 
                       type="text" 
                       placeholder="{{ $inputPlaceholder }}" 
                       class="flex-1 bg-gray-50 border border-gray-200 rounded-2xl px-3.5 py-2.5 text-xs sm:text-sm focus:outline-none focus:border-[#6A8F3B] focus:ring-1 focus:ring-[#6A8F3B] transition"
                       :disabled="isTyping"
                       autocomplete="off"
                       autocorrect="off"
                       autocapitalize="sentences"
                       spellcheck="false"
                       dir="auto">
                <button type="submit" 
                        class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-gradient-to-br from-[#6A8F3B] to-[#5a7a2f] text-white flex items-center justify-center hover:opacity-95 transition disabled:opacity-40 shrink-0 shadow-md transform active:scale-95 touch-manipulation"
                        :disabled="!newMessage.trim() || isTyping"
                        aria-label="Send message">
                    <svg class="w-4 h-4 {{ $isRTL ? '-rotate-90' : 'rotate-90' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
                </button>
            </form>
            <div class="text-[9px] sm:text-[10px] text-center text-gray-400 mt-1 font-medium">
                {{ $footerText }}
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('ezzitouniChat', () => ({
        isOpen: false,
        showTooltip: true,
        isTyping: false,
        newMessage: '',
        zitouniIndex: 0,
        zitouniMessages: [
            @json(__('zitouni.message1')),
            @json(__('zitouni.message2')),
            @json(__('zitouni.message3')),
            @json(__('zitouni.message4'))
        ],
        messages: [
            {
                role: 'model',
                content: @json($welcomeContent),
                buttons: []
            }
        ],

        init() {
            const closedUntil = localStorage.getItem('zitouni_tooltip_closed_until');
            if (closedUntil && new Date().getTime() < parseInt(closedUntil)) {
                this.showTooltip = false;
            }

            setInterval(() => {
                if (!this.isOpen) {
                    this.zitouniIndex = (this.zitouniIndex + 1) % this.zitouniMessages.length;
                }
            }, 5000);
        },

        closeTooltip() {
            this.showTooltip = false;
            const until = new Date().getTime() + (24 * 60 * 60 * 1000);
            localStorage.setItem('zitouni_tooltip_closed_until', until.toString());
        },

        toggleChat() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.scrollToBottom();
            }
        },

        handleButtonClick(btn, event) {
            if (!btn || !btn.url) return;

            // 1. PDF downloads or external absolute links -> open in new tab
            if (btn.url.endsWith('.pdf') || btn.url.startsWith('http://') || btn.url.startsWith('https://')) {
                window.open(btn.url, '_blank');
                return;
            }

            // 2. Hash navigation (e.g. /ar/#products, /ar/#deals, or #products)
            const currentPath = window.location.pathname.replace(/\/+$/, '');
            const [urlPath, hash] = btn.url.split('#');
            const cleanUrlPath = (urlPath || '').replace(/\/+$/, '');

            if (hash && (cleanUrlPath === '' || cleanUrlPath === currentPath)) {
                if (event) event.preventDefault();
                const targetEl = document.getElementById(hash);
                if (targetEl) {
                    if (window.innerWidth < 768) {
                        this.isOpen = false;
                    }
                    targetEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    try {
                        window.history.pushState(null, '', '#' + hash);
                    } catch (e) {}
                    return;
                }
            }

            // 3. Direct navigation for internal routes (e.g. /ar/prices, /ar/listings/create, /ar/register)
            if (event) event.preventDefault();
            window.location.href = btn.url;
        },

        async sendMessage() {
            if (!this.newMessage.trim() || this.isTyping) return;

            const userMsg = this.newMessage.trim();
            this.newMessage = '';
            
            // Add user message to UI
            this.messages.push({ role: 'user', content: userMsg, buttons: [] });
            this.scrollToBottom();
            
            this.isTyping = true;

            try {
                // Prepare conversation history
                const historyPayload = this.messages.slice(0, -1).map(m => ({
                    role: m.role,
                    content: m.content
                }));

                const response = await fetch('{{ route('chatbot.chat') }}', {
                     method: 'POST',
                     headers: {
                         'Content-Type': 'application/json',
                         'Accept': 'application/json',
                         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                     },
                     body: JSON.stringify({
                         message: userMsg,
                         locale: '{{ app()->getLocale() }}',
                         history: historyPayload
                     })
                });

                const data = await response.json().catch(() => ({}));

                if (!response.ok) {
                    const errorMsg = data.reply || data.message || `حدث خطأ في الخادم (${response.status})`;
                    this.messages.push({ role: 'model', content: errorMsg, buttons: data.buttons || [] });
                } else {
                    this.messages.push({
                        role: 'model',
                        content: data.reply || @json(__('Sorry, could not generate a response.')),
                        buttons: data.buttons || []
                    });
                }
                
            } catch (error) {
                this.messages.push({
                    role: 'model',
                    content: @json(__('Sorry, a connection error occurred. Check your internet connection.')),
                    buttons: []
                });
            } finally {
                this.isTyping = false;
                this.scrollToBottom();
            }
        },

        scrollToBottom() {
            setTimeout(() => {
                const box = document.getElementById('ezzitouni-chat-box');
                if (box) box.scrollTop = box.scrollHeight;
            }, 60);
        },

        formatMessage(text) {
            if (!text) return '';
            
            // Bold **text**
            let formatted = text.replace(/\*\*(.*?)\*\*/g, '<strong class="font-bold text-gray-900">$1</strong>');
            
            // Unordered list items: lines starting with "- " or "* "
            formatted = formatted.replace(/(?:^|\n)[-*]\s+(.+)/g, '<div class="flex items-start gap-1.5 my-1"><span class="text-[#6A8F3B] font-bold">•</span><span>$1</span></div>');

            // Linebreaks outside existing HTML tags
            let result = '';
            let inTag = false;
            for (let i = 0; i < formatted.length; i++) {
                let char = formatted[i];
                if (char === '<') {
                    inTag = true;
                } else if (char === '>') {
                    inTag = false;
                }
                
                if (char === '\n') {
                    result += inTag ? ' ' : '<br>';
                } else {
                    result += char;
                }
            }
            return result;
        }
    }));

    window.ezzitouniSendChoice = function(val) {
        if (!val) return;
        const chatEl = document.querySelector('div[x-data*="ezzitouniChat"]');
        if (chatEl && window.Alpine) {
            const data = Alpine.$data(chatEl);
            if (data && typeof data.sendMessage === 'function') {
                data.newMessage = val;
                data.sendMessage();
            }
        }
    };
});
</script>
