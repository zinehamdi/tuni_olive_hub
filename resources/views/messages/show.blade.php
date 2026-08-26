<x-app-layout>
    @php
        $isRTL = app()->getLocale() === 'ar';
    @endphp

    <div class="h-[calc(100dvh-65px)] bg-gray-50 flex flex-col justify-between overflow-hidden" 
         x-data="messageChat" 
         x-init="init()"
         dir="{{ $isRTL ? 'rtl' : 'ltr' }}">

        <div class="w-full max-w-4xl mx-auto flex-1 flex flex-col bg-white shadow-sm overflow-hidden relative">

            {{-- Chat Header --}}
            <div class="bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between shadow-sm z-10">
                <div class="flex items-center gap-3">
                    <a href="{{ route('messages.inbox') }}" class="p-2 hover:bg-gray-100 rounded-xl transition text-gray-500 hover:text-gray-900" title="{{ __('Back to Inbox') }}">
                        <svg class="w-5 h-5 {{ $isRTL ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    
                    <a href="{{ $user->role !== 'admin' ? route('user.profile', $user) : '#' }}" class="flex items-center gap-3 group">
                        <div class="relative">
                            @if($user->profile_picture)
                                <img src="{{ Storage::url($user->profile_picture) }}" class="w-10 h-10 rounded-full object-cover shadow-sm group-hover:ring-2 group-hover:ring-[#6A8F3B] transition">
                            @else
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#6A8F3B] to-[#5a7a2f] flex items-center justify-center text-white font-bold text-lg shadow-sm group-hover:scale-105 transition">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                            <span class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 border-2 border-white rounded-full"></span>
                        </div>
                        <div>
                            <h2 class="font-bold text-gray-900 text-sm sm:text-base group-hover:text-[#6A8F3B] transition flex items-center gap-1.5">
                                {{ $user->name }}
                                @if($user->role === 'admin')
                                    <span class="text-[10px] px-1.5 py-0.5 bg-amber-100 text-amber-800 rounded font-medium">{{ __('Admin') }}</span>
                                @endif
                            </h2>
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium inline-block mt-0.5
                                {{ $user->role === 'farmer' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                {{ $user->role === 'carrier' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $user->role === 'mill' ? 'bg-amber-100 text-amber-700' : '' }}
                                {{ $user->role === 'packer' ? 'bg-purple-100 text-purple-700' : '' }}
                                {{ !in_array($user->role, ['farmer', 'carrier', 'mill', 'packer']) ? 'bg-gray-100 text-gray-700' : '' }}
                            ">
                                {{ __($user->role) }}
                            </span>
                        </div>
                    </a>
                    
                    {{-- Propose Deal Button --}}
                    @if(isset($availableListings) && $availableListings->count() > 0)
                        <button @click="$dispatch('open-propose-deal')" class="flex items-center gap-1 sm:gap-2 px-3 sm:px-4 py-2 bg-gradient-to-r from-[#C8A356] to-[#b08a3c] text-white rounded-lg hover:shadow-lg transition font-bold shadow whitespace-nowrap text-xs sm:text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span class="hidden sm:inline">{{ __('Propose Deal') }}</span>
                            <span class="sm:hidden">{{ __('Deal') }}</span>
                        </button>
                    @endif
                </div>
            </div>
            
            {{-- Active Deals Section --}}
            @if(isset($pendingOrders) && $pendingOrders->count() > 0)
                <div class="bg-amber-50 border-b border-amber-100 p-3 sm:p-4 shadow-sm relative z-0">
                    <h3 class="font-bold text-amber-800 mb-3 flex items-center gap-2 text-sm sm:text-base">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        {{ __('Active Deals') }}
                    </h3>
                    <div class="space-y-3 max-h-48 overflow-y-auto pr-2">
                        @foreach($pendingOrders as $order)
                            <div class="bg-white rounded-xl shadow-sm border border-amber-200 p-3 sm:p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3" x-data="{ confirming: false, rejecting: false }">
                                <div>
                                    <div class="font-bold text-gray-900 text-sm sm:text-base">
                                        {{ $order->listing?->product?->type === 'olive' ? __('Olives') : __('Olive Oil') }} 
                                        - {{ $order->qty }} {{ __($order->unit) }}
                                    </div>
                                    <div class="text-xs sm:text-sm text-gray-600 mt-1 flex items-center flex-wrap gap-2">
                                        <span>{{ __('Price:') }} <strong>{{ $order->total }} {{ __('TND') }}</strong> ({{ $order->price_unit }} {{ __('TND') }}/{{ __($order->unit) }})</span>
                                        <span>•</span>
                                        <span class="font-semibold {{ $order->status === 'confirmed' ? 'text-green-600' : 'text-amber-600' }}">
                                            {{ $order->status === 'confirmed' ? __('Confirmed') : __('Pending Approval') }}
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="flex items-center flex-wrap gap-2">
                                    {{-- Pending Deal Actions --}}
                                    @if($order->status === 'pending')
                                        @if($order->seller_id === auth()->id())
                                            <button @click="confirming = true; acceptDeal({{ $order->id }})" 
                                                    class="px-3 py-1.5 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-lg text-xs sm:text-sm font-bold shadow hover:shadow-lg transition flex items-center justify-center gap-1.5 whitespace-nowrap"
                                                    :disabled="confirming || rejecting">
                                                <svg x-show="!confirming" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                <svg x-show="confirming" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                <span>{{ __('Accept Deal') }}</span>
                                            </button>
                                        @endif

                                        {{-- Counter-Offer Button (Both parties can renegotiate) --}}
                                        <button @click="$dispatch('open-counter-offer', { orderId: {{ $order->id }}, currentPrice: {{ $order->price_unit }}, qty: {{ $order->qty }}, unit: '{{ $order->unit }}' })" 
                                                class="px-3 py-1.5 bg-gradient-to-r from-[#C8A356] to-[#b08a3c] text-white rounded-lg text-xs sm:text-sm font-bold shadow hover:shadow-lg transition flex items-center justify-center gap-1.5 whitespace-nowrap"
                                                :disabled="confirming || rejecting">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            <span>{{ __('Counter-Offer') }}</span>
                                        </button>

                                        {{-- Reject Button --}}
                                        <button @click="if(confirm('{{ __('Are you sure you want to cancel/reject this deal?') }}')) { rejecting = true; rejectDeal({{ $order->id }}); }" 
                                                class="px-2.5 py-1.5 bg-red-50 text-red-600 border border-red-200 hover:bg-red-100 rounded-lg text-xs sm:text-sm font-bold transition flex items-center justify-center gap-1 whitespace-nowrap"
                                                :disabled="confirming || rejecting">
                                            <svg x-show="!rejecting" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            <svg x-show="rejecting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                            <span>{{ __('Reject') }}</span>
                                        </button>
                                    @endif
                                    
                                    {{-- Confirmed Deal Actions --}}
                                    @if($order->status === 'confirmed' && !$order->transportLoad)
                                        @if(in_array(auth()->id(), [$order->buyer_id, $order->seller_id]) || auth()->user()->role === 'admin')
                                            <button @click.prevent="openTransporters({{ $order->id }})" class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg text-xs sm:text-sm font-bold shadow hover:shadow-lg transition-all flex items-center gap-2 whitespace-nowrap cursor-pointer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                                {{ __('Summon Transporter') }}
                                            </button>
                                        @endif
                                    @elseif($order->transportLoad)
                                        <div class="flex items-center flex-wrap gap-2">
                                            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-lg text-xs font-bold flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                {{ __('Transporter Summoned') }}
                                            </span>
                                            
                                            {{-- Secure Delivery PIN Badge for Buyer --}}
                                            @php
                                                $loadPin = $order->transportLoad->meta['pin_code'] ?? null;
                                            @endphp
                                            @if($loadPin && auth()->id() === $order->buyer_id)
                                                <div class="px-2.5 py-1 bg-amber-500 text-white rounded-lg text-xs font-black shadow flex items-center gap-1 animate-pulse" title="{{ __('Give this PIN to the carrier only after delivery') }}">
                                                    <span>🔐 PIN:</span>
                                                    <span class="tracking-widest font-mono">{{ $loadPin }}</span>
                                                </div>
                                            @endif

                                            @if($order->transportLoad->status === 'in_transit' && $order->transportLoad->activeTrip())
                                                <a href="{{ route('mobile.trip', ['id' => $order->transportLoad->id]) }}" target="_blank" class="px-3 py-1 bg-green-100 text-green-700 rounded-lg text-xs font-bold flex items-center gap-1 hover:bg-green-200 transition-colors">
                                                    <svg class="w-3 h-3 animate-pulse" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                                                    {{ __('Live Track') }}
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            
            {{-- Messages Area --}}
            <div id="messages-container"
                 class="px-3 sm:px-6 py-4 space-y-2 overflow-y-auto"
                 style="height: calc(100dvh - 190px); min-height: 200px; scroll-behavior: smooth;"
                 @message-sent.window="if (!newMessages.find(m => m.id === $event.detail.id)) { newMessages.push({...$event.detail, is_mine: $event.detail.sender_id == {{ auth()->id() }} }); lastMessageId = $event.detail.id; scrollToBottom(); showScrollFab = false; }"
                 @show-toast.window="showToast($event.detail.message, $event.detail.type)"
                 @scroll="showScrollFab = $el.scrollHeight - $el.scrollTop - $el.clientHeight > 120">
                <input type="hidden" id="selected-order-id" value="" />

                @if($messages->count() > 0)
                <div class="flex items-center gap-3 my-3">
                    <div class="flex-1 h-px bg-gray-200"></div>
                    <span class="text-[11px] text-gray-400 font-medium px-2">{{ $messages->first()->created_at->translatedFormat('d M Y') }}</span>
                    <div class="flex-1 h-px bg-gray-200"></div>
                </div>
                @endif

                @foreach($messages as $message)
                    @php
                        $isMine = $message->sender_id === auth()->id();
                        $isBot = ($message->attachments['is_bot'] ?? false) || str_starts_with($message->body, '🤖');
                    @endphp

                    @if($isBot)
                        {{-- Dedicated Ez-Zitouni AI Message Bubble --}}
                        <div class="w-full my-3 flex justify-center">
                            <div class="max-w-[92%] sm:max-w-[80%] bg-gradient-to-r from-amber-50/90 via-emerald-50/90 to-amber-50/90 border-2 border-amber-300 rounded-2xl p-4 shadow-md text-gray-900 relative">
                                <div class="flex items-center gap-2 mb-2 pb-2 border-b border-amber-200">
                                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-[#6A8F3B] to-[#C8A356] flex items-center justify-center text-white text-sm shadow">
                                        🤖
                                    </div>
                                    <span class="font-black text-xs text-[#6A8F3B] uppercase tracking-wider">{{ __('Ez-Zitouni AI Assistant') }}</span>
                                </div>
                                <div class="text-sm leading-relaxed whitespace-pre-wrap font-medium text-gray-800">
                                    {!! nl2br(e(preg_replace('/^🤖 \*\*[^*]+\*\*:\n?/', '', $message->body))) !!}
                                </div>
                                <div class="text-left mt-1 text-[10px] text-gray-400">
                                    {{ $message->created_at->translatedFormat('H:i') }}
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Standard User Message --}}
                        <div class="flex {{ $isMine ? ($isRTL ? 'justify-start' : 'justify-end') : ($isRTL ? 'justify-end' : 'justify-start') }} items-end gap-2 group">
                            @if(!$isMine)
                                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0 mb-5">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                            <div class="max-w-[78%] sm:max-w-[65%]">
                                <div class="{{ $isMine
                                    ? 'bg-gradient-to-br from-[#3B5998] to-[#2d4275] text-white ' . ($isRTL ? 'rounded-t-2xl rounded-br-2xl rounded-bl-md' : 'rounded-t-2xl rounded-bl-2xl rounded-br-md')
                                    : 'bg-white border border-gray-200 text-gray-900 shadow-sm ' . ($isRTL ? 'rounded-t-2xl rounded-bl-2xl rounded-br-md' : 'rounded-t-2xl rounded-br-2xl rounded-bl-md') }} px-4 py-2.5">
                                    <p class="text-sm leading-relaxed whitespace-pre-wrap break-words">{{ $message->body }}</p>
                                </div>
                                <div class="flex items-center gap-1 mt-0.5 px-1 {{ $isMine ? ($isRTL ? 'justify-start' : 'justify-end') : ($isRTL ? 'justify-end' : 'justify-start') }}">
                                    <span class="text-[10px] text-gray-400">{{ $message->created_at->translatedFormat('H:i') }}</span>
                                    @if($isMine)
                                        @if($message->read_at)
                                            <svg class="w-3.5 h-3.5 text-blue-400" viewBox="0 0 16 16" fill="currentColor"><path d="M.5 6.5l1-1 4 4L13.5 2l1 1-9 9z"/><path d="M3.5 6.5l1-1 4 4" opacity=".4"/></svg>
                                        @else
                                            <svg class="w-3.5 h-3.5 text-gray-300" viewBox="0 0 16 16" fill="currentColor"><path d="M.5 6.5l1-1 4 4L13.5 2l1 1-9 9z"/></svg>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach

                {{-- New messages via Alpine (real-time) --}}
                <template x-for="msg in newMessages" :key="msg.id">
                    <div :class="msg.is_mine ? '{{ $isRTL ? 'justify-start' : 'justify-end' }}' : '{{ $isRTL ? 'justify-end' : 'justify-start' }}'" class="flex items-end gap-2">
                        <div class="max-w-[78%] sm:max-w-[65%]">
                            <div :class="msg.is_mine
                                ? 'bg-gradient-to-br from-[#3B5998] to-[#2d4275] text-white {{ $isRTL ? 'rounded-t-2xl rounded-br-2xl rounded-bl-md' : 'rounded-t-2xl rounded-bl-2xl rounded-br-md' }}'
                                : 'bg-white border border-gray-200 text-gray-900 shadow-sm {{ $isRTL ? 'rounded-t-2xl rounded-bl-2xl rounded-br-md' : 'rounded-t-2xl rounded-br-2xl rounded-bl-md' }}'" class="px-4 py-2.5">
                                <p class="text-sm leading-relaxed whitespace-pre-wrap break-words" x-text="msg.body"></p>
                            </div>
                            <span :class="msg.is_mine ? '{{ $isRTL ? 'text-left' : 'text-right' }}' : '{{ $isRTL ? 'text-right' : 'text-left' }}'" class="block mt-0.5 text-[10px] text-gray-400 px-1" x-text="msg.created_at"></span>
                        </div>
                    </div>
                </template>

                <div class="h-2"></div>
            </div>
            
            {{-- Scroll-to-bottom FAB --}}
            <div class="relative h-0">
                <button x-show="showScrollFab"
                        x-cloak
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        @click="scrollToBottom(); showScrollFab = false"
                        class="absolute -top-12 left-1/2 -translate-x-1/2 bg-white border border-gray-200 shadow-lg rounded-full px-4 py-1.5 flex items-center gap-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition z-30"
                        style="display:none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    {{ __('New messages') }}
                </button>
            </div>

            {{-- Message Input Bar --}}
            <div class="sticky bottom-0 left-0 right-0 bg-white border-t-2 border-gray-100 shadow-[0_-4px_20px_rgba(0,0,0,0.06)] z-20">
                <div class="max-w-4xl mx-auto px-3 sm:px-4 py-3">
                    <form x-data="messageSender" @submit.prevent="send()" class="flex items-end gap-2 sm:gap-3">
                        <div class="flex-1 relative">
                            <textarea
                                x-model="message"
                                @keydown.enter.exact.prevent="send()"
                                rows="1"
                                placeholder="{{ __('Type your message here...') }}"
                                class="w-full resize-none rounded-2xl border-2 border-gray-200 focus:border-[#3B5998] focus:ring-0 px-4 py-2.5 text-sm sm:text-base leading-relaxed max-h-32 transition-all placeholder-gray-400 bg-gray-50/50 focus:bg-white"
                                style="min-height: 44px;"
                                @input="$el.style.height = 'auto'; $el.style.height = Math.min($el.scrollHeight, 128) + 'px'"></textarea>
                        </div>
                        <button type="submit"
                                :disabled="!message.trim() || sending"
                                class="h-11 px-4 sm:px-5 bg-gradient-to-r from-[#6A8F3B] to-[#5a7a2f] hover:opacity-90 disabled:opacity-40 text-white font-bold rounded-2xl transition-all shadow-md flex items-center gap-2 flex-shrink-0 disabled:cursor-not-allowed">
                            <svg x-show="!sending" class="w-5 h-5 {{ $isRTL ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            <svg x-show="sending" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="hidden sm:inline" x-text="sending ? '{{ __('Sending...') }}' : '{{ __('Send') }}'"></span>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Propose Deal Modal --}}
            @if(isset($availableListings) && $availableListings->count() > 0)
                <div x-show="showProposeDealModal"
                     style="display: none;"
                     class="fixed inset-0 z-50 overflow-y-auto"
                     aria-labelledby="modal-title"
                     role="dialog"
                     aria-modal="true"
                     @open-propose-deal.window="showProposeDealModal = true">
                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <div x-show="showProposeDealModal"
                             @click="showProposeDealModal = false"
                             x-transition:enter="ease-out duration-300"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="ease-in duration-200"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                             aria-hidden="true"></div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                        <div x-show="showProposeDealModal"
                             x-transition:enter="ease-out duration-300"
                             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                             x-transition:leave="ease-in duration-200"
                             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                             class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full p-6">
                            
                            <h3 class="text-xl font-bold text-gray-900 mb-4" id="modal-title">
                                {{ __('Propose Deal') }}
                            </h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('Select Product') }}</label>
                                    <select x-model="selectedListingId" @change="updateListing()" class="w-full border-gray-300 focus:border-[#6A8F3B] focus:ring-[#6A8F3B] rounded-xl shadow-sm text-sm sm:text-base py-2.5 px-3">
                                        <option value="">{{ __('Select a product') }}</option>
                                        <template x-for="listing in listings.filter(l => l.seller_id != {{ auth()->id() }})" :key="listing.id">
                                            <option :value="listing.id" class="text-gray-900 bg-white" x-text="t(listing.product.variety) + ' (' + Number(listing.quantity).toFixed(0) + ' ' + t(listing.unit) + ' {{ __('Available') }} - ' + listing.price + ' {{ __('TND') }})'"></option>
                                        </template>
                                    </select>
                                </div>
                                
                                <template x-if="currentListing">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                                {{ __('Quantity') }} <span x-text="'(' + t(currentListing.unit) + ')'"></span>
                                            </label>
                                            <input type="number" x-model.number="qty" :min="currentListing.min_order || 1" :max="currentListing.quantity" step="0.1" class="w-full border-gray-300 focus:border-[#6A8F3B] focus:ring-[#6A8F3B] rounded-xl shadow-sm text-sm sm:text-base py-2.5 px-3">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('Proposed Price') }}</label>
                                            <input type="number" x-model.number="priceUnit" min="0" step="0.1" class="w-full border-gray-300 focus:border-[#6A8F3B] focus:ring-[#6A8F3B] rounded-xl shadow-sm text-sm sm:text-base py-2.5 px-3">
                                        </div>
                                    </div>
                                </template>
                                
                                <template x-if="currentListing">
                                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 mt-2">
                                        <div class="flex justify-between items-center text-lg font-bold">
                                            <span class="text-gray-900">{{ __('Total') }}</span>
                                            <span class="text-[#6A8F3B]"><span x-text="(qty * priceUnit).toFixed(2)"></span> {{ __('TND') }}</span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            
                            <div class="mt-6 flex gap-3">
                                <button @click="showProposeDealModal = false" class="flex-1 px-4 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition font-bold text-sm sm:text-base">
                                    {{ __('Cancel') }}
                                </button>
                                <button @click="submitProposeDeal" :disabled="makingDeal || !currentListing || !qty || !priceUnit" class="flex-1 px-4 py-3 bg-[#6A8F3B] text-white rounded-xl hover:bg-[#5a7a2f] disabled:opacity-50 transition font-bold flex items-center justify-center gap-2 text-sm sm:text-base">
                                    <span x-show="!makingDeal">{{ __('Send Offer') }}</span>
                                    <svg x-show="makingDeal" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Counter-Offer Modal (Renegotiation) --}}
            <div x-show="showCounterOfferModal"
                 style="display: none;"
                 class="fixed inset-0 z-50 overflow-y-auto"
                 aria-labelledby="modal-counter-title"
                 role="dialog"
                 aria-modal="true"
                 @open-counter-offer.window="counterOrderId = $event.detail.orderId; counterPrice = $event.detail.currentPrice; counterQty = $event.detail.qty; counterUnit = $event.detail.unit; showCounterOfferModal = true;">
                <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div @click="showCounterOfferModal = false" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                    <div class="inline-block align-bottom bg-white rounded-2xl p-6 overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full text-start">
                        <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2" id="modal-counter-title">
                            <span>💬</span> {{ __('Propose New Price (Counter-Offer)') }}
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">{{ __('New Unit Price (TND)') }}</label>
                                <input type="number" step="0.1" min="0.1" x-model.number="counterPrice" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:border-[#6A8F3B] focus:ring-[#6A8F3B] text-lg font-bold">
                            </div>
                            <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-sm">
                                <div class="flex justify-between font-bold text-gray-800">
                                    <span>{{ __('Quantity:') }}</span>
                                    <span><span x-text="counterQty"></span> <span x-text="counterUnit"></span></span>
                                </div>
                                <div class="flex justify-between font-bold text-[#6A8F3B] text-base mt-2 pt-2 border-t border-amber-200">
                                    <span>{{ __('New Total:') }}</span>
                                    <span><span x-text="(counterQty * counterPrice).toFixed(2)"></span> {{ __('TND') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 flex gap-3">
                            <button @click="showCounterOfferModal = false" class="flex-1 px-4 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition font-bold text-sm">
                                {{ __('Cancel') }}
                            </button>
                            <button @click="submitCounterOffer()" :disabled="submittingCounter || !counterPrice" class="flex-1 px-4 py-3 bg-[#6A8F3B] text-white rounded-xl hover:bg-[#5a7a2f] disabled:opacity-50 transition font-bold text-sm flex items-center justify-center gap-2">
                                <span x-show="!submittingCounter">{{ __('Send Counter-Offer') }}</span>
                                <svg x-show="submittingCounter" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Browse Transporters Modal (Proximity Sorted + Direct Call) --}}
            <div x-show="showTransporters" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-transporters-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="showTransporters" @click="showTransporters = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div x-show="showTransporters" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-2xl text-start overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-start w-full">
                                    <h3 class="text-xl leading-6 font-bold text-gray-900 mb-1" id="modal-transporters-title">
                                        🚚 {{ __('Browse Transporters (Sorted by Proximity)') }}
                                    </h3>
                                    <p class="text-xs text-gray-500 mb-4">{{ __('Transporters are sorted from closest to furthest relative to pickup location.') }}</p>
                                    
                                    @if(isset($carriers) && $carriers->count() > 0)
                                        <div class="space-y-3 max-h-[60vh] overflow-y-auto pr-2">
                                            @foreach($carriers as $carrier)
                                                <div class="border rounded-2xl p-4 flex flex-col sm:flex-row items-center justify-between gap-4 hover:border-blue-300 transition-colors bg-gray-50 hover:bg-white shadow-sm">
                                                    <div class="flex items-center gap-3 w-full sm:w-auto">
                                                        @if($carrier->profile_picture)
                                                            <img src="{{ Storage::url($carrier->profile_picture) }}" class="w-12 h-12 rounded-full object-cover shadow-sm">
                                                        @else
                                                            <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xl shadow-sm">
                                                                {{ strtoupper(substr($carrier->name, 0, 1)) }}
                                                            </div>
                                                        @endif
                                                        <div class="{{ $isRTL ? 'text-right' : 'text-left' }}">
                                                            <div class="flex items-center gap-2">
                                                                <h4 class="font-bold text-gray-900">{{ $carrier->name }}</h4>
                                                                @if(isset($carrier->distance_km))
                                                                    <span class="px-2 py-0.5 bg-blue-50 text-blue-700 border border-blue-200 rounded-full text-[11px] font-bold">
                                                                        📍 {{ $carrier->distance_km < 15 ? __('Same Governorate') : round($carrier->distance_km) . ' ' . __('km away') }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            <div class="text-xs text-gray-500 mt-1 flex flex-wrap items-center gap-2">
                                                                <span>{{ $carrier->governorate_label ?? __('Tunisia') }}</span>
                                                                @if($carrier->phone)
                                                                    <span>•</span>
                                                                    <span class="font-mono text-gray-700">{{ $carrier->phone }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                                                        {{-- Direct Phone Call Button --}}
                                                        @if($carrier->phone)
                                                            <a href="tel:{{ $carrier->phone }}" class="px-3 py-2.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-xl text-xs font-bold flex items-center gap-1.5 transition border border-emerald-200" title="{{ __('Call Transporter') }}">
                                                                <span>📞</span>
                                                                <span>{{ __('Call') }}</span>
                                                            </a>
                                                        @endif

                                                        {{-- Assign & Summon Button --}}
                                                        <button @click="assignTransporter({{ $carrier->id }})"
                                                                :disabled="assigningCarrier === {{ $carrier->id }}"
                                                                class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow flex items-center justify-center gap-1.5 whitespace-nowrap">
                                                            <span x-show="assigningCarrier !== {{ $carrier->id }}">{{ __('Assign & Summon') }}</span>
                                                            <svg x-show="assigningCarrier === {{ $carrier->id }}" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-8 text-gray-500">
                                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4M8 16l-4-4 4-4"/></svg>
                                            <p>{{ __('No transporters available currently.') }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-200">
                            <button @click="showTransporters = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                {{ __('Cancel') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Elegant Toast Notification -->
        <div x-show="$store.toast.show" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-4"
             class="fixed top-24 left-1/2 -translate-x-1/2 z-[100] w-[90%] max-w-sm"
             style="display: none;">
            <div :class="$store.toast.type === 'success' ? 'bg-[#6A8F3B]' : 'bg-rose-600'" 
                 class="rounded-2xl shadow-2xl p-4 flex items-center gap-3 text-white border border-white/20 backdrop-blur-md">
                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center shrink-0">
                    <svg x-show="$store.toast.type === 'success'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <svg x-show="$store.toast.type === 'error'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </div>
                <div class="flex-1">
                    <p class="font-bold text-sm" x-text="$store.toast.message"></p>
                </div>
                <button @click="$store.toast.show = false" class="p-1 hover:bg-white/10 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('messageChat', () => ({
                newMessages: [],
                lastMessageId: {{ $messages->last()?->id ?? 0 }},
                assigningCarrier: null,
                showCounterOfferModal: false,
                showTransporters: false,
                selectedOrderId: {{ $order?->id ?? 'null' }},
                counterOrderId: null,
                counterPrice: 0,
                counterQty: 1,
                counterUnit: 'L',
                submittingCounter: false,

                openTransporters(orderId) {
                    this.selectedOrderId = orderId;
                    this.showTransporters = true;
                },

                init() {
                    this.scrollToBottom();
                    window.chatComponent = this;
                    if (window.Echo) {
                        window.Echo.private('threads.{{ $thread->id }}')
                            .listen('.message.sent', (e) => {
                                window.dispatchEvent(new CustomEvent('message-sent', { detail: e }));
                                if (e.sender_id !== {{ auth()->id() }}) this.markAsRead();
                            });
                    }
                },

                async markAsRead() {
                    try { await fetch(`{{ route('messages.get', ['locale' => app()->getLocale(), 'user' => $user->id]) }}`); } catch (e) {}
                },

                scrollToBottom() {
                    setTimeout(() => {
                        const container = document.getElementById('messages-container');
                        if (container) container.scrollTop = container.scrollHeight;
                    }, 100);
                },

                async submitCounterOffer() {
                    if (!this.counterOrderId || !this.counterPrice || this.submittingCounter) return;
                    this.submittingCounter = true;
                    try {
                        const response = await fetch(`/api/v1/orders/${this.counterOrderId}/counter-offer`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ price_unit: this.counterPrice })
                        });
                        const data = await response.json();
                        if (data.success || data.data) {
                            this.showCounterOfferModal = false;
                            window.Alpine.store('toast').showToast('{{ __("Counter-offer sent successfully!") }}', 'success');
                            setTimeout(() => { window.location.reload(); }, 1200);
                        } else {
                            alert(data.message || 'Error sending counter-offer');
                        }
                    } catch (e) {
                        alert('Network error while sending counter-offer');
                    } finally {
                        this.submittingCounter = false;
                    }
                },

                async assignTransporter(carrierId) {
                    const orderId = this.selectedOrderId || {{ $order?->id ?? 'null' }};
                    if (!orderId) {
                        alert('No order selected.');
                        return;
                    }
                    
                    if (this.assigningCarrier) return;
                    this.assigningCarrier = carrierId;
                    try {
                        const response = await fetch(`{{ route('loads.summon', ['locale' => app()->getLocale()]) }}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ order_id: orderId, carrier_id: carrierId })
                        });
                        const resData = await response.json();
                        if (resData.success) {
                            window.Alpine.store('toast').showToast(resData.message || '{{ __("Transporter assigned successfully!") }}', 'success');
                            setTimeout(() => { window.location.reload(); }, 1500);
                        } else {
                            window.Alpine.store('toast').showToast(resData.message || '{{ __("Error assigning transporter.") }}', 'error');
                        }
                    } catch (err) {
                        window.Alpine.store('toast').showToast('{{ __("Network error while assigning transporter.") }}', 'error');
                    } finally {
                        this.assigningCarrier = null;
                    }
                }
            }));

            Alpine.data('messageSender', () => ({
                message: '',
                sending: false,
                async send() {
                    if (!this.message.trim() || this.sending) return;
                    this.sending = true;
                    try {
                        const res = await fetch(`{{ route('messages.send', ['locale' => app()->getLocale(), 'user' => $user->id]) }}`, {
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
                            window.dispatchEvent(new CustomEvent('message-sent', { detail: data.data }));
                            this.message = '';
                        } else {
                            alert(data.message || 'Failed to send message');
                        }
                    } catch (err) {
                        console.error('Failed to send:', err);
                        alert('Failed to send message');
                    } finally {
                        this.sending = false;
                    }
                }
            }));

            window.assignTransporter = function(carrierId) {
                if (window.chatComponent && typeof window.chatComponent.assignTransporter === 'function') {
                    window.chatComponent.assignTransporter(carrierId);
                } else {
                    console.error('Chat component not initialized.');
                }
            };

            window.acceptDeal = function(orderId) {
                fetch(`/api/v1/orders/${orderId}/transition`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ next: 'confirm' })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success || data.data) {
                        window.location.reload();
                    } else {
                        alert(data.message || 'Error accepting deal.');
                    }
                })
                .catch(err => {
                    alert('Network error while accepting deal.');
                });
            };

            window.rejectDeal = function(orderId) {
                fetch(`/api/v1/orders/${orderId}/transition`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ next: 'cancel' })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success || data.data) {
                        window.location.reload();
                    } else {
                        alert(data.message || 'Error cancelling deal.');
                    }
                })
                .catch(err => {
                    alert('Network error while cancelling deal.');
                });
            };
        });
    </script>
    @endpush
</x-app-layout>
