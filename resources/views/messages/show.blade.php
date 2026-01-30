@php
    $locale = app()->getLocale();
    $isRTL = $locale === 'ar';
@endphp

<x-app-layout>
    <div class="min-h-screen bg-gray-100" dir="{{ $isRTL ? 'rtl' : 'ltr' }}">
        <div class="max-w-4xl mx-auto">
            
            {{-- Header --}}
            <div class="bg-white shadow-sm sticky top-0 z-10">
                <div class="px-2 sm:px-4 py-3 sm:py-4 flex items-center gap-2 sm:gap-4">
                    {{-- Back button --}}
                    <a href="{{ route('messages.inbox') }}" 
                       class="p-2 hover:bg-gray-100 rounded-full transition-colors flex-shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-gray-600 {{ $isRTL ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    
                    {{-- User info --}}
                    <a href="{{ route('user.profile', $user) }}" class="flex items-center gap-2 sm:gap-3 flex-1 min-w-0 hover:bg-gray-50 rounded-lg p-1.5 sm:p-2 -m-1.5 sm:-m-2 transition-colors">
                        @if($user->profile_picture)
                            <img src="{{ Storage::url($user->profile_picture) }}" 
                                 alt="{{ $user->name }}"
                                 class="w-9 h-9 sm:w-10 sm:h-10 rounded-full object-cover ring-2 ring-white shadow flex-shrink-0">
                        @else
                            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center text-white text-base sm:text-lg font-bold ring-2 ring-white shadow flex-shrink-0">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                        
                        <div class="min-w-0">
                            <h2 class="font-semibold text-gray-900 text-sm sm:text-base truncate">{{ $user->name }}</h2>
                            <span class="text-xs px-2 py-0.5 rounded-full
                                {{ $user->role === 'farmer' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $user->role === 'carrier' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $user->role === 'mill' ? 'bg-amber-100 text-amber-700' : '' }}
                                {{ $user->role === 'packer' ? 'bg-purple-100 text-purple-700' : '' }}
                                {{ !in_array($user->role, ['farmer', 'carrier', 'mill', 'packer']) ? 'bg-gray-100 text-gray-700' : '' }}
                            ">
                                {{ __($user->role) }}
                            </span>
                        </div>
                    </a>
                </div>
            </div>
            
            {{-- Messages Area --}}
            <div id="messages-container" class="px-3 sm:px-4 py-4 sm:py-6 space-y-3 sm:space-y-4 min-h-[calc(100vh-180px)] sm:min-h-[calc(100vh-200px)] max-h-[calc(100vh-180px)] sm:max-h-[calc(100vh-200px)] overflow-y-auto" x-data="messageChat()" x-init="init()">
                @foreach($messages as $message)
                    <div class="flex {{ $message->sender_id === auth()->id() ? ($isRTL ? 'justify-start' : 'justify-end') : ($isRTL ? 'justify-end' : 'justify-start') }}">
                        <div class="max-w-[85%] sm:max-w-[75%] {{ $message->sender_id === auth()->id() 
                            ? 'bg-gradient-to-br from-blue-500 to-indigo-600 text-white rounded-2xl ' . ($isRTL ? 'rounded-bl-sm' : 'rounded-br-sm')
                            : 'bg-white text-gray-900 rounded-2xl shadow ' . ($isRTL ? 'rounded-br-sm' : 'rounded-bl-sm') }}">
                            <p class="px-4 py-3 text-sm leading-relaxed">{{ $message->body }}</p>
                            <span class="block px-4 pb-2 text-xs {{ $message->sender_id === auth()->id() ? 'text-blue-200' : 'text-gray-400' }}">
                                {{ $message->created_at->format('M d, H:i') }}
                            </span>
                        </div>
                    </div>
                @endforeach
                
                {{-- New messages will be appended here --}}
                <template x-for="msg in newMessages" :key="msg.id">
                    <div :class="msg.is_mine ? '{{ $isRTL ? 'justify-start' : 'justify-end' }}' : '{{ $isRTL ? 'justify-end' : 'justify-start' }}'" class="flex">
                        <div :class="msg.is_mine 
                            ? 'bg-gradient-to-br from-blue-500 to-indigo-600 text-white rounded-2xl {{ $isRTL ? 'rounded-bl-sm' : 'rounded-br-sm' }}'
                            : 'bg-white text-gray-900 rounded-2xl shadow {{ $isRTL ? 'rounded-br-sm' : 'rounded-bl-sm' }}'"
                            class="max-w-[75%]">
                            <p class="px-4 py-3 text-sm leading-relaxed" x-text="msg.body"></p>
                            <span :class="msg.is_mine ? 'text-blue-200' : 'text-gray-400'" class="block px-4 pb-2 text-xs" x-text="msg.created_at"></span>
                        </div>
                    </div>
                </template>
            </div>
            
            {{-- Message Input --}}
            <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-2 sm:p-4 safe-area-inset-bottom">
                <div class="max-w-4xl mx-auto">
                    <form x-data="messageSender()" @submit.prevent="send()" class="flex items-end gap-2 sm:gap-3">
                        <div class="flex-1 relative">
                            <textarea 
                                x-model="message"
                                @keydown.enter.prevent="!$event.shiftKey && send()"
                                rows="1"
                                class="w-full px-3 sm:px-4 py-2.5 sm:py-3 border border-gray-200 rounded-xl sm:rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none text-sm"
                                placeholder="{{ __('Type a message...') }}"
                                :disabled="sending"
                            ></textarea>
                        </div>
                        <button 
                            type="submit"
                            :disabled="!message.trim() || sending"
                            class="p-2.5 sm:p-3 bg-gradient-to-br from-blue-500 to-indigo-600 text-white rounded-full shadow-lg hover:shadow-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed flex-shrink-0">
                            <svg x-show="!sending" class="w-5 h-5 {{ $isRTL ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            <svg x-show="sending" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
            
        </div>
    </div>
    
    <script>
        function messageChat() {
            return {
                newMessages: [],
                lastMessageId: {{ $messages->last()?->id ?? 0 }},
                
                init() {
                    // Scroll to bottom
                    this.scrollToBottom();
                    
                    // Poll for new messages every 5 seconds
                    // Poll for new messages every 15 seconds (better for performance)
                    setInterval(() => this.checkNewMessages(), 15000);
                },
                
                scrollToBottom() {
                    setTimeout(() => {
                        const container = document.getElementById('messages-container');
                        container.scrollTop = container.scrollHeight;
                    }, 100);
                },
                
                async checkNewMessages() {
                    try {
                        const res = await fetch(`/messages/{{ $user->id }}/get`);
                        const data = await res.json();
                        
                        if (data.success && data.messages.length > 0) {
                            const latestId = Math.max(...data.messages.map(m => m.id));
                            if (latestId > this.lastMessageId) {
                                // Add only new messages
                                const newOnes = data.messages.filter(m => m.id > this.lastMessageId);
                                this.newMessages.push(...newOnes);
                                this.lastMessageId = latestId;
                                this.scrollToBottom();
                            }
                        }
                    } catch (err) {
                        console.error('Failed to check messages:', err);
                    }
                }
            };
        }
        
        function messageSender() {
            return {
                message: '',
                sending: false,
                
                async send() {
                    if (!this.message.trim() || this.sending) return;
                    
                    this.sending = true;
                    
                    try {
                        const res = await fetch(`/messages/{{ $user->id }}/send`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ message: this.message })
                        });
                        
                        const data = await res.json();
                        
                        if (data.success) {
                            // Add message to chat
                            Alpine.store('newMessage', {
                                id: data.data.id,
                                body: data.data.body,
                                sender_id: data.data.sender_id,
                                is_mine: true,
                                created_at: data.data.created_at
                            });
                            
                            // Trigger update in chat component
                            window.dispatchEvent(new CustomEvent('message-sent', { detail: {
                                id: data.data.id,
                                body: data.data.body,
                                sender_id: data.data.sender_id,
                                is_mine: true,
                                created_at: data.data.created_at
                            }}));
                            
                            this.message = '';
                        } else {
                            alert(data.message || '{{ __('Failed to send message') }}');
                        }
                    } catch (err) {
                        console.error('Failed to send:', err);
                        alert('{{ __('Failed to send message') }}');
                    } finally {
                        this.sending = false;
                    }
                }
            };
        }
        
        // Listen for sent messages
        window.addEventListener('message-sent', (e) => {
            const chatComponent = document.querySelector('[x-data="messageChat()"]');
            if (chatComponent && chatComponent.__x) {
                chatComponent.__x.$data.newMessages.push(e.detail);
                chatComponent.__x.$data.lastMessageId = e.detail.id;
                chatComponent.__x.$data.scrollToBottom();
            }
        });
    </script>
</x-app-layout>
