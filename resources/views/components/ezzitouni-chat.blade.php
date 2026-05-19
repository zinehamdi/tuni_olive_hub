<div x-data="ezzitouniChat()" class="fixed bottom-6 left-6 z-[9999]">
    
    <!-- Chat Toggle Button (Ezzitouni Avatar) -->
    <button @click="toggleChat()" 
            class="relative w-16 h-16 rounded-full bg-white shadow-[0_10px_40px_-10px_rgba(106,143,59,0.5)] border-4 border-white hover:scale-110 transition-transform flex items-center justify-center overflow-hidden z-20 group">
        <img src="{{ asset('images/ezzitouni_bot.png') }}" alt="Zitouni" class="w-full h-full object-cover">
        
        <!-- Online Indicator -->
        <span class="absolute bottom-0 right-1 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></span>
    </button>

    <!-- Welcome Tooltip (Shows when chat is closed) -->
    <div x-show="!isOpen" 
         x-transition.opacity.duration.500ms
         class="absolute bottom-20 left-0 w-64 bg-white rounded-2xl p-4 shadow-xl border-2 border-[#6A8F3B]/20 pointer-events-none origin-bottom-left" style="display: none;">
        
        <div class="flex items-center gap-2 mb-2">
            <h4 class="font-black text-[#6A8F3B] text-[10px] uppercase tracking-wider">{{ __('Zitouni') }}</h4>
            <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
        </div>

        <div class="relative h-14">
            <template x-for="(msg, index) in zitouniMessages" :key="index">
                <p x-show="zitouniIndex === index" 
                   x-transition:enter="transition ease-out duration-500 delay-200"
                   x-transition:enter-start="opacity-0 translate-y-4"
                   x-transition:enter-end="opacity-100 translate-y-0"
                   x-transition:leave="transition ease-in duration-300 absolute inset-0"
                   x-transition:leave-start="opacity-100 translate-y-0"
                   x-transition:leave-end="opacity-0 -translate-y-4"
                   class="text-[10px] text-gray-700 font-bold leading-relaxed"
                   x-text="msg">
                </p>
            </template>
        </div>

        <div class="mt-1 flex gap-1">
            <template x-for="(msg, index) in zitouniMessages" :key="index">
                <div class="w-1 h-1 rounded-full transition-all duration-300"
                     :class="zitouniIndex === index ? 'bg-[#6A8F3B] w-3' : 'bg-gray-200'"></div>
            </template>
        </div>

        <!-- Decorative arrow -->
        <div class="absolute -bottom-2 left-6 w-4 h-4 bg-white border-b-2 border-r-2 border-[#6A8F3B]/20 transform rotate-45"></div>
    </div>

    <!-- Chat Window -->
    <div x-show="isOpen" 
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 translate-y-10 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-10 scale-95"
         class="absolute bottom-20 left-0 w-[350px] max-w-[calc(100vw-3rem)] bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100 flex flex-col z-10"
         style="height: 500px; max-height: calc(100vh - 8rem); display: none;">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-[#6A8F3B] to-[#5a7a2f] p-4 flex items-center justify-between text-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-white overflow-hidden p-0.5">
                    <img src="{{ asset('images/ezzitouni_bot.png') }}" alt="Zitouni" class="w-full h-full object-cover rounded-full">
                </div>
                <div>
                    <h3 class="font-bold leading-tight">الزيتوني (Ezzitouni)</h3>
                    <p class="text-xs text-white/80">خبير الزيت والتجارة الدولية</p>
                </div>
            </div>
            <button @click="toggleChat()" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-white/20 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <!-- Messages Area -->
        <div id="ezzitouni-chat-box" class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50 flex flex-col">
            <template x-for="(msg, index) in messages" :key="index">
                <div class="flex w-full" :class="msg.role === 'user' ? 'justify-end' : 'justify-start'">
                    <div class="max-w-[85%] rounded-2xl p-3 text-sm shadow-sm"
                         :class="msg.role === 'user' 
                            ? 'bg-[#6A8F3B] text-white rounded-br-none' 
                            : 'bg-white text-gray-800 border border-gray-100 rounded-bl-none'">
                        
                        <div x-html="formatMessage(msg.content)"></div>
                    </div>
                </div>
            </template>
            
            <!-- Typing Indicator -->
            <div x-show="isTyping" class="flex justify-start w-full">
                <div class="bg-white border border-gray-100 rounded-2xl rounded-bl-none p-4 shadow-sm flex items-center gap-1">
                    <div class="w-2 h-2 bg-[#6A8F3B] rounded-full animate-bounce" style="animation-delay: 0s"></div>
                    <div class="w-2 h-2 bg-[#6A8F3B] rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                    <div class="w-2 h-2 bg-[#6A8F3B] rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
                </div>
            </div>
        </div>

        <!-- Input Area -->
        <div class="p-4 bg-white border-t border-gray-100">
            <form @submit.prevent="sendMessage" class="flex gap-2">
                <input x-model="newMessage" 
                       type="text" 
                       placeholder="اكتب رسالتك هنا..." 
                       class="flex-1 bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#6A8F3B] focus:ring-1 focus:ring-[#6A8F3B]"
                       :disabled="isTyping"
                       dir="auto">
                <button type="submit" 
                        class="w-10 h-10 rounded-xl bg-[#6A8F3B] text-white flex items-center justify-center hover:bg-[#5a7a2f] transition disabled:opacity-50"
                        :disabled="!newMessage.trim() || isTyping">
                    <svg class="w-5 h-5 transform rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
                </button>
            </form>
            <div class="text-[10px] text-center text-gray-400 mt-2">
                مدعوم بالذكاء الاصطناعي. قد تكون الإجابات غير دقيقة أحياناً.
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('ezzitouniChat', () => ({
        isOpen: false,
        isOpen: false,
        isTyping: false,
        newMessage: '',
        zitouniIndex: 0,
        zitouniMessages: [
            "منصة ZinToop آمنة تماماً ولا تحتوي على روابط مزعجة تعطل تجربتك.",
            "نحن نربط بين منتجي زيت الزيتون التونسي والمشترين مباشرة وبدون وسطاء.",
            "استخدم شريط البحث والفرز لتجد أفضل جودة زيت قريبة من منطقتك بسهولة.",
            "رؤيتنا هي رقمنة قطاع الزيتون في تونس لضمان تجارة عادلة لكل فلاح تونسي."
        ],
        messages: [
            { role: 'model', content: 'أهلاً بك! أنا "الزيتوني"، الخبير الخاص بمنصة ZinToop. كيف يمكنني مساعدتك اليوم؟\n\nإذا كنت تبحث عن شراء أو تصدير الزيت، يمكنني توجيهك.' }
        ],

        init() {
            // Rotate messages infinitely
            setInterval(() => {
                if (!this.isOpen) {
                    this.zitouniIndex = (this.zitouniIndex + 1) % this.zitouniMessages.length;
                }
            }, 5000);
        },

        toggleChat() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.scrollToBottom();
            }
        },

        async sendMessage() {
            if (!this.newMessage.trim() || this.isTyping) return;

            const userMsg = this.newMessage.trim();
            this.newMessage = '';
            
            // Add user message to UI
            this.messages.push({ role: 'user', content: userMsg });
            this.scrollToBottom();
            
            this.isTyping = true;

            try {
                // Send to backend
                const response = await fetch('/api/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        message: userMsg,
                        history: this.messages.slice(0, -1) // Send previous messages for context
                    })
                });

                if (!response.ok) {
                    // Try to parse error JSON (Validation, CSRF, or our custom 500 error)
                    const data = await response.json().catch(() => ({}));
                    const errorMsg = data.reply || data.message || `حدث خطأ في الخادم (${response.status})`;
                    this.messages.push({ role: 'model', content: errorMsg });
                } else {
                    const data = await response.json();
                    this.messages.push({ role: 'model', content: data.reply || "عذراً، لم أتمكن من صياغة رد." });
                }
                
            } catch (error) {
                this.messages.push({ role: 'model', content: 'عذراً، حدث خطأ في الاتصال بالخادم. تأكد من اتصالك بالإنترنت.' });
            } finally {
                this.isTyping = false;
                this.scrollToBottom();
            }
        },

        scrollToBottom() {
            setTimeout(() => {
                const box = document.getElementById('ezzitouni-chat-box');
                if (box) box.scrollTop = box.scrollHeight;
            }, 50);
        },

        formatMessage(text) {
            if (!text) return '';
            // Convert simple markdown to HTML (bold, newlines)
            let formatted = text
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                .replace(/\n/g, '<br>');
            return formatted;
        }
    }));
});
</script>
