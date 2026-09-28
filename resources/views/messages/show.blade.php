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
            
            {{-- Transporter / GPS Pin Sharing Banner --}}
            @php
                $isCarrierInteraction = ($user->role === 'carrier' || auth()->user()->role === 'carrier');
            @endphp
            @if($isCarrierInteraction)
                <div class="bg-gradient-to-r from-emerald-50 via-teal-50 to-emerald-50 border-b border-emerald-200/80 px-3 py-2 sm:px-4 sm:py-2.5 flex items-center justify-between gap-2 shadow-2xs">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="text-xl sm:text-2xl flex-shrink-0 animate-bounce">📍</span>
                        <div class="min-w-0">
                            <p class="text-xs sm:text-sm font-black text-emerald-950 leading-tight truncate">
                                {{ __('Send exact GPS location to ensure the transporter arrives directly.') }}
                            </p>
                            <p class="text-[10px] sm:text-[11px] text-emerald-700 hidden sm:block">
                                {{ __('Opens directly in Google Maps for turn-by-turn driving navigation.') }}
                            </p>
                        </div>
                    </div>
                    <button type="button" 
                            @click="window.dispatchEvent(new CustomEvent('send-gps-request'))"
                            class="px-3 py-1.5 sm:px-3.5 sm:py-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 active:scale-95 text-white rounded-xl text-xs sm:text-xs font-black shadow-xs flex items-center gap-1.5 transition whitespace-nowrap cursor-pointer flex-shrink-0">
                        <span>📍</span>
                        <span>{{ __('Send GPS Pin') }}</span>
                    </button>
                </div>
            @endif

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
                                            <button @click.prevent="openTransporters({{ $order->id }}, {{ (float)$order->qty }}, '{{ $order->unit }}', '{{ addslashes($order->seller?->addresses?->first()?->governorate ?? '') }}')" class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg text-xs sm:text-sm font-bold shadow hover:shadow-lg transition-all flex items-center gap-2 whitespace-nowrap cursor-pointer">
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
                        $isTransportAssignment = ($message->attachments['type'] ?? null) === 'transport_assignment';
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
                    @elseif($isTransportAssignment)
                        @php
                            $att = $message->attachments ?? [];
                            $tStatus = $att['status'] ?? 'pending';
                            $loadId = $att['load_id'] ?? null;
                            $orderId = $att['order_id'] ?? null;
                            $isAssignedCarrier = (int)auth()->id() === (int)($att['carrier_id'] ?? 0);
                        @endphp
                        {{-- Dedicated Interactive Transport Mission Assignment Card --}}
                        <div class="w-full my-4 flex justify-center px-2">
                            <div class="w-full max-w-lg bg-white rounded-3xl p-5 shadow-xl border-2 {{ $tStatus === 'accepted' ? 'border-emerald-300' : ($tStatus === 'denied' ? 'border-gray-200 opacity-80' : 'border-blue-300') }} text-gray-900 relative space-y-4" x-data="{ accepting: false, denying: false }">
                                {{-- Card Header --}}
                                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-10 h-10 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center text-xl font-bold flex-shrink-0 shadow-sm">
                                            🚚
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-900 text-sm sm:text-base">{{ __('Transport Mission Assignment') }}</h4>
                                            <span class="text-[11px] text-gray-400 font-mono">#LOAD-{{ $loadId }}</span>
                                        </div>
                                    </div>
                                    <div>
                                        @if($tStatus === 'accepted')
                                            <span class="px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full text-xs font-bold flex items-center gap-1 shadow-sm">
                                                ✓ {{ __('Accepted') }}
                                            </span>
                                        @elseif($tStatus === 'denied')
                                            <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-bold">
                                                ❌ {{ __('Declined') }}
                                            </span>
                                        @else
                                            <span class="px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-xs font-bold animate-pulse shadow-sm">
                                                ⏳ {{ __('Pending Response') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Route & Cargo Summary --}}
                                <div class="grid grid-cols-2 gap-3 text-xs">
                                    <div class="p-3 bg-blue-50/70 rounded-2xl border border-blue-100">
                                        <span class="text-[10px] font-bold text-blue-500 uppercase block mb-1">📍 {{ __('Route') }}</span>
                                        <p class="font-bold text-gray-800 truncate" title="{{ $att['pickup'] ?? '' }}">🟢 {{ $att['pickup'] ?? '---' }}</p>
                                        <p class="font-bold text-gray-800 truncate mt-1" title="{{ $att['dropoff'] ?? '' }}">🚩 {{ $att['dropoff'] ?? '---' }}</p>
                                        <span class="text-[10px] text-blue-600 font-bold block mt-1.5">~{{ $att['distance_km'] ?? '---' }} km</span>
                                    </div>
                                    <div class="p-3 bg-emerald-50/70 rounded-2xl border border-emerald-100">
                                        <span class="text-[10px] font-bold text-emerald-600 uppercase block mb-1">💰 {{ __('Estimated Earning') }}</span>
                                        <p class="font-black text-emerald-800 text-base">~{{ $att['estimated_cost'] ?? '---' }} <span class="text-xs font-normal">TND</span></p>
                                        <span class="text-[10px] text-emerald-700 font-bold block mt-1.5">📦 {{ $att['qty'] ?? '' }} {{ $att['unit'] ?? '' }}</span>
                                    </div>
                                </div>

                                {{-- Action Buttons --}}
                                @if($tStatus === 'pending')
                                    @if($isAssignedCarrier)
                                        <div class="flex items-center gap-3 pt-2">
                                            <button @click="accepting = true; acceptTransportAssignment({{ $loadId }})"
                                                    :disabled="accepting || denying"
                                                    class="flex-1 py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl font-bold text-xs sm:text-sm transition shadow-md flex items-center justify-center gap-2">
                                                <span x-show="!accepting">✓ {{ __('Accept Mission') }}</span>
                                                <svg x-show="accepting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                            </button>
                                            <button @click="denying = true; denyTransportAssignment({{ $loadId }})"
                                                    :disabled="accepting || denying"
                                                    class="py-3 px-4 bg-gray-100 hover:bg-red-50 text-gray-700 hover:text-red-700 rounded-2xl font-bold text-xs sm:text-sm transition border border-gray-200">
                                                <span x-show="!denying">{{ __('Decline') }}</span>
                                                <svg x-show="denying" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                            </button>
                                        </div>
                                    @else
                                        <div class="text-center py-2.5 px-3 text-xs text-amber-800 font-medium bg-amber-50 rounded-2xl border border-amber-200">
                                            ⏳ {{ __('Waiting for the transporter to confirm acceptance of this mission...') }}
                                        </div>
                                    @endif
                                @elseif($tStatus === 'accepted')
                                    @if($isAssignedCarrier)
                                        <div class="pt-1">
                                            <a href="{{ route('mobile.trip', ['locale' => app()->getLocale(), 'id' => $loadId]) }}" class="w-full py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-2xl font-bold text-xs sm:text-sm flex items-center justify-center gap-2 shadow-md transition">
                                                <span>📱</span>
                                                <span>{{ __('Open Driver Trip Dashboard') }}</span>
                                            </a>
                                        </div>
                                    @else
                                        <div class="text-center py-2.5 px-3 text-xs text-emerald-800 font-bold bg-emerald-50 rounded-2xl border border-emerald-200">
                                            ✅ {{ __('Carrier has accepted the mission! Trip is underway.') }}
                                        </div>
                                    @endif
                                @endif

                                <div class="text-left text-[10px] text-gray-400 pt-1">
                                    {{ $message->created_at->translatedFormat('H:i') }}
                                </div>
                            </div>
                        </div>
                    @elseif(($message->attachments['type'] ?? null) === 'transporter_request_accepted')
                        @php
                            $att = $message->attachments ?? [];
                            $isCarrier = (int)auth()->id() === (int)($att['carrier_id'] ?? 0);
                            $customerPhone = $att['customer_phone'] ?? '';
                            $cleanPhone = preg_replace('/[^0-9]/', '', (string)$customerPhone);
                        @endphp
                        {{-- Dedicated Confirmed Transport Mission Dispatch Card --}}
                        <div class="w-full my-4 flex justify-center px-2">
                            <div class="w-full max-w-lg bg-white rounded-3xl p-4 sm:p-5 shadow-xl border-2 border-emerald-400 text-gray-900 relative space-y-3 sm:space-y-4">
                                {{-- Card Header --}}
                                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white flex items-center justify-center text-xl font-bold flex-shrink-0 shadow-sm">
                                            🚚
                                        </div>
                                        <div>
                                            <h4 class="font-black text-gray-900 text-sm sm:text-base">{{ __('Confirmed Transport Mission') }}</h4>
                                            <span class="text-[11px] text-emerald-700 font-bold">✓ {{ __('Mission Accepted by Carrier') }}</span>
                                        </div>
                                    </div>
                                    <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-full text-xs font-black shadow-2xs">
                                        {{ __('Active') }}
                                    </span>
                                </div>

                                {{-- Details --}}
                                <div class="grid grid-cols-2 gap-2 sm:gap-3 text-xs">
                                    <div class="p-2.5 sm:p-3 bg-slate-50 rounded-2xl border border-gray-200">
                                        <span class="text-[10px] font-bold text-gray-500 uppercase block mb-1">📍 {{ __('Route') }}</span>
                                        <p class="font-black text-gray-900 text-xs truncate">🟢 {{ $att['pickup'] ?? '---' }}</p>
                                        <p class="font-black text-gray-900 text-xs truncate mt-1">🚩 {{ $att['dropoff'] ?? '---' }}</p>
                                        <span class="text-[10px] text-blue-600 font-bold block mt-1">~{{ $att['distance_km'] ?? '---' }} km</span>
                                    </div>
                                    <div class="p-2.5 sm:p-3 bg-emerald-50/70 rounded-2xl border border-emerald-100">
                                        <span class="text-[10px] font-bold text-emerald-600 uppercase block mb-1">💰 {{ __('Fair Price') }}</span>
                                        <p class="font-black text-emerald-800 text-sm sm:text-base">~{{ $att['estimated_cost'] ?? '---' }} <span class="text-xs font-normal">TND</span></p>
                                        <span class="text-[10px] text-emerald-700 font-bold block mt-1">📦 {{ $att['weight'] ?? '' }} {{ $att['unit'] ?? '' }}</span>
                                    </div>
                                </div>

                                {{-- Action Row --}}
                                <div class="pt-1 flex flex-col sm:flex-row items-center gap-2">
                                    @if($isCarrier)
                                        {{-- Carrier can directly call the customer --}}
                                        @if(!empty($cleanPhone))
                                            <a href="tel:{{ $cleanPhone }}" class="w-full sm:flex-1 py-2.5 px-4 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 text-white rounded-xl font-black text-xs sm:text-sm flex items-center justify-center gap-2 shadow-md transition active:scale-95">
                                                <span>📞</span>
                                                <span>{{ __('Call Customer') }} ({{ $customerPhone }})</span>
                                            </a>
                                        @endif
                                    @else
                                        {{-- Customer can send GPS Drop Pin --}}
                                        <button type="button" 
                                                @click="window.dispatchEvent(new CustomEvent('send-gps-request'))"
                                                class="w-full py-2.5 px-4 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 text-white rounded-xl font-black text-xs sm:text-sm flex items-center justify-center gap-2 shadow-md transition active:scale-95 cursor-pointer">
                                            <span>📍</span>
                                            <span>{{ __('Send Exact GPS Pin to Transporter') }}</span>
                                        </button>
                                    @endif
                                </div>

                                <div class="text-left text-[10px] text-gray-400 pt-1">
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

                                    {{-- Google Maps GPS Navigation Card --}}
                                    @if(str_contains($message->body, 'maps.google.com') || str_contains($message->body, 'google.com/maps'))
                                        @php
                                            preg_match('/https:\/\/(www\.)?(google\.com\/maps|maps\.google\.com)[^\s\n\r]+/', $message->body, $gMapsMatches);
                                            $gMapsUrl = $gMapsMatches[0] ?? null;
                                        @endphp
                                        @if($gMapsUrl)
                                            <div class="mt-2.5 pt-2 border-t {{ $isMine ? 'border-white/20' : 'border-gray-200' }}">
                                                <a href="{{ $gMapsUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-black text-xs shadow-sm transition active:scale-95">
                                                    <span>🧭</span>
                                                    <span>{{ __('Open in Google Maps') }}</span>
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                </a>
                                            </div>
                                        @endif
                                    @endif
                                </div>

                                {{-- Inline Translation Display --}}
                                <template x-if="translations[{{ $message->id }}]">
                                    <div class="mt-1.5 p-2.5 bg-amber-50/90 border border-amber-200 rounded-xl text-xs text-gray-800 leading-relaxed shadow-sm">
                                        <div class="flex items-center gap-1.5 font-bold text-[10px] text-[#6A8F3B] mb-1">
                                            <span>🌐 {{ __('Translated by Ezzitouni AI') }}</span>
                                        </div>
                                        <p x-text="translations[{{ $message->id }}]"></p>
                                    </div>
                                </template>

                                <div class="flex items-center gap-2 mt-0.5 px-1 {{ $isMine ? ($isRTL ? 'justify-start' : 'justify-end') : ($isRTL ? 'justify-end' : 'justify-start') }}">
                                    <span class="text-[10px] text-gray-400">{{ $message->created_at->translatedFormat('H:i') }}</span>
                                    
                                    @if(!$isMine)
                                        <button type="button" 
                                                @click="translateMessage({{ $message->id }}, @js($message->body))" 
                                                class="text-[10px] text-[#6A8F3B] hover:text-[#5a7a2f] font-bold flex items-center gap-1 opacity-75 hover:opacity-100 transition"
                                                :disabled="translatingId === {{ $message->id }}">
                                            <span x-show="translatingId !== {{ $message->id }}">🌐 {{ __('Translate') }}</span>
                                            <span x-show="translatingId === {{ $message->id }}" class="animate-pulse">⏳ {{ __('Translating...') }}</span>
                                        </button>
                                    @endif

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

                            <template x-if="translations[msg.id]">
                                <div class="mt-1.5 p-2.5 bg-amber-50/90 border border-amber-200 rounded-xl text-xs text-gray-800 leading-relaxed shadow-sm">
                                    <div class="flex items-center gap-1.5 font-bold text-[10px] text-[#6A8F3B] mb-1">
                                        <span>🌐 {{ __('Translated by Ezzitouni AI') }}</span>
                                    </div>
                                    <p x-text="translations[msg.id]"></p>
                                </div>
                            </template>

                            <div class="flex items-center gap-2 mt-0.5 px-1" :class="msg.is_mine ? '{{ $isRTL ? 'justify-start' : 'justify-end' }}' : '{{ $isRTL ? 'justify-end' : 'justify-start' }}'">
                                <span class="text-[10px] text-gray-400" x-text="msg.created_at"></span>
                                <template x-if="!msg.is_mine">
                                    <button type="button" 
                                            @click="translateMessage(msg.id, msg.body)" 
                                            class="text-[10px] text-[#6A8F3B] hover:text-[#5a7a2f] font-bold flex items-center gap-1 opacity-75 hover:opacity-100 transition"
                                            :disabled="translatingId === msg.id">
                                        <span x-show="translatingId !== msg.id">🌐 {{ __('Translate') }}</span>
                                        <span x-show="translatingId === msg.id" class="animate-pulse">⏳ {{ __('Translating...') }}</span>
                                    </button>
                                </template>
                            </div>
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
                    <form x-data="messageSender" 
                          @submit.prevent="send()" 
                          @send-gps-request.window="sendGpsLocation()" 
                          class="flex items-end gap-2 sm:gap-3">
                        {{-- 1-Click Live GPS Drop Pin Button --}}
                        <button type="button" 
                                @click="sendGpsLocation()" 
                                :disabled="locatingGps || sending"
                                title="{{ __('Send My Exact GPS Location') }}"
                                class="h-11 w-11 flex items-center justify-center bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-300 rounded-2xl transition shadow-2xs flex-shrink-0 cursor-pointer active:scale-95">
                            <span x-show="!locatingGps" class="text-base sm:text-lg">📍</span>
                            <svg x-show="locatingGps" class="w-4 h-4 animate-spin text-emerald-700" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </button>

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
                                    <p class="text-xs text-gray-500 mb-3">{{ __('Transporters are sorted from closest to furthest relative to pickup location.') }}</p>

                                    {{-- Required Deal Load & Capacity Filter Banner --}}
                                    <div x-show="selectedOrderQty > 0" class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-2xl p-3 mb-3 flex flex-wrap items-center justify-between gap-2 shadow-xs">
                                        <div class="flex items-center gap-2">
                                            <span class="text-base">⚖️</span>
                                            <span class="text-xs font-bold text-amber-900">{{ __('Required Deal Load:') }}</span>
                                            <span class="text-xs font-black text-blue-700 bg-white px-2.5 py-0.5 rounded-lg border border-amber-200" x-text="selectedOrderQty + ' ' + selectedOrderUnit"></span>
                                        </div>
                                        <label class="flex items-center gap-1.5 text-xs font-bold text-gray-700 cursor-pointer bg-white px-2.5 py-1 rounded-xl border border-amber-200 hover:bg-amber-100/50 transition">
                                            <input type="checkbox" x-model="filterSuitableOnly" @change="fetchDealCarriers()" class="rounded text-blue-600 focus:ring-0">
                                            <span>{{ __('Show only vehicles with suitable capacity') }}</span>
                                        </label>
                                    </div>

                                    {{-- Route & Address Exact Location Specification --}}
                                    <div class="bg-blue-50/70 border border-blue-200 rounded-2xl p-4 mb-4 text-start space-y-3">
                                        <div class="flex items-center gap-2">
                                            <span class="text-base">📍</span>
                                            <h4 class="font-bold text-gray-900 text-xs sm:text-sm">{{ __('Confirm Exact Pickup & Dropoff Locations') }}</h4>
                                        </div>
                                        <p class="text-[11px] text-gray-600">{{ __('Ez-Zitouni calculates transport distance and fare based on these locations. Use GPS or specify delegation for 100% accuracy.') }}</p>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                                            {{-- Pickup Box --}}
                                            <div class="bg-white rounded-xl p-3 border border-blue-100 shadow-sm space-y-2">
                                                <div class="flex items-center justify-between">
                                                    <span class="text-[11px] font-bold text-emerald-700 flex items-center gap-1">
                                                        <span>🟢</span>
                                                        <span>{{ __('Pickup (مكان التحميل)') }}</span>
                                                    </span>
                                                    <button type="button" @click="useCurrentLocation('pickup')" :disabled="gettingGps" class="text-[10px] text-blue-600 hover:text-blue-800 font-bold flex items-center gap-1">
                                                        <span>📍</span>
                                                        <span>{{ __('GPS') }}</span>
                                                    </button>
                                                </div>
                                                <select x-model="pickupGov" @change="fetchDealCarriers()" class="w-full text-xs py-1.5 px-2 bg-gray-50 border border-gray-200 rounded-lg font-medium focus:ring-0 focus:border-blue-400">
                                                    <option value="">{{ __('Select Governorate') }}</option>
                                                    @foreach(['صفاقس', 'تونس', 'سوسة', 'القيروان', 'المنستير', 'المهدية', 'نابل', 'بنزرت', 'باجة', 'جندوبة', 'الكاف', 'سليانة', 'زغوان', 'سيدي بوزيد', 'القصرين', 'قفصة', 'قابس', 'مدنين', 'تطاوين', 'توزر', 'قبلي', 'أريانة', 'بن عروس', 'منوبة'] as $g)
                                                        <option value="{{ $g }}">{{ $g }}</option>
                                                    @endforeach
                                                </select>
                                                <input type="text" x-model="pickupDel" placeholder="{{ __('Delegation / Locality (e.g. طينة)') }}" class="w-full text-xs py-1.5 px-2 bg-gray-50 border border-gray-200 rounded-lg placeholder-gray-400 focus:ring-0 focus:border-blue-400">
                                            </div>

                                            {{-- Dropoff Box --}}
                                            <div class="bg-white rounded-xl p-3 border border-blue-100 shadow-sm space-y-2">
                                                <div class="flex items-center justify-between">
                                                    <span class="text-[11px] font-bold text-red-700 flex items-center gap-1">
                                                        <span>🚩</span>
                                                        <span>{{ __('Dropoff (مكان التسليم)') }}</span>
                                                    </span>
                                                    <button type="button" @click="useCurrentLocation('dropoff')" :disabled="gettingGps" class="text-[10px] text-blue-600 hover:text-blue-800 font-bold flex items-center gap-1">
                                                        <span>📍</span>
                                                        <span>{{ __('GPS') }}</span>
                                                    </button>
                                                </div>
                                                <select x-model="dropoffGov" class="w-full text-xs py-1.5 px-2 bg-gray-50 border border-gray-200 rounded-lg font-medium focus:ring-0 focus:border-blue-400">
                                                    <option value="">{{ __('Select Governorate') }}</option>
                                                    @foreach(['تونس', 'صفاقس', 'سوسة', 'القيروان', 'المنستير', 'المهدية', 'نابل', 'بنزرت', 'باجة', 'جندوبة', 'الكاف', 'سليانة', 'زغوان', 'سيدي بوزيد', 'القصرين', 'قفصة', 'قابس', 'مدنين', 'تطاوين', 'توزر', 'قبلي', 'أريانة', 'بن عروس', 'منوبة'] as $g)
                                                        <option value="{{ $g }}">{{ $g }}</option>
                                                    @endforeach
                                                </select>
                                                <input type="text" x-model="dropoffDel" placeholder="{{ __('Delegation / Locality (e.g. المروج)') }}" class="w-full text-xs py-1.5 px-2 bg-gray-50 border border-gray-200 rounded-lg placeholder-gray-400 focus:ring-0 focus:border-blue-400">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    {{-- Loading State --}}
                                    <div x-show="loadingCarriers" class="py-8 text-center text-blue-600 text-xs font-bold flex items-center justify-center gap-2">
                                        <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        <span>{{ __('Loading and sorting nearest transporters...') }}</span>
                                    </div>

                                    {{-- Dynamic Carriers (Live sorted by nearest & capacity) --}}
                                    <div x-show="!loadingCarriers && dynamicCarriers.length > 0" class="space-y-1.5 sm:space-y-2.5 max-h-[60vh] overflow-y-auto pr-2">
                                        <template x-for="carrier in dynamicCarriers" :key="carrier.id">
                                            <div class="group relative bg-white hover:bg-slate-50 border border-slate-200 hover:border-blue-400 rounded-xl p-2 sm:p-3 transition-all duration-150 shadow-2xs hover:shadow-xs border-s-4 border-s-blue-600 flex items-center justify-between gap-2 sm:gap-3">
                                                <!-- Identity & Metrics -->
                                                <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1">
                                                    <template x-if="carrier.avatar">
                                                        <img :src="carrier.avatar" class="w-9 h-9 sm:w-11 sm:h-11 rounded-full object-cover ring-1 ring-blue-100 flex-shrink-0 shadow-2xs">
                                                    </template>
                                                    <template x-if="!carrier.avatar">
                                                        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-gradient-to-br from-blue-600 to-indigo-700 text-white flex items-center justify-center font-black text-xs sm:text-base flex-shrink-0 shadow-2xs ring-1 ring-blue-100" x-text="carrier.name.charAt(0).toUpperCase()"></div>
                                                    </template>
                                                    <div class="min-w-0 flex-1 text-start">
                                                        <!-- Transporter Name (Wadha7) -->
                                                        <div class="flex items-center gap-1 min-w-0">
                                                            <h4 class="font-black text-slate-900 text-xs sm:text-base tracking-tight leading-snug group-hover:text-blue-600 transition-colors truncate" x-text="carrier.name"></h4>
                                                            <span x-show="carrier.is_verified" class="inline-flex items-center text-blue-600 flex-shrink-0 text-[10px]" title="{{ __('Verified') }}">
                                                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" viewBox="0 0 20 20" fill="currentColor">
                                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                                </svg>
                                                            </span>
                                                        </div>
                                                        <!-- Badges: Distance & Capacity -->
                                                        <div class="flex items-center gap-1 mt-0.5 flex-wrap">
                                                            <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded bg-blue-50 text-blue-900 text-[10px] sm:text-[11px] font-black border border-blue-200/60">
                                                                <span>📍</span>
                                                                <span x-text="carrier.distance_km != null ? carrier.distance_km + ' {{ __('km') }}' : '{{ __('Same Governorate') }}'"></span>
                                                            </span>
                                                            <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-950 text-[10px] sm:text-[11px] font-black border border-emerald-200/60">
                                                                <span>⚖️</span>
                                                                <span x-text="carrier.capacity_label"></span>
                                                            </span>
                                                            <template x-if="selectedOrderQty > 0">
                                                                <span :class="carrier.fits_capacity ? 'bg-emerald-50 text-emerald-800 border-emerald-300' : 'bg-amber-50 text-amber-800 border-amber-300'" class="px-1.5 py-0.5 border rounded text-[9px] font-black" x-text="carrier.fits_capacity ? '✅ {{ __('Fits') }}' : '⚠️ {{ __('Smaller') }}'"></span>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Action Button -->
                                                <div class="flex-shrink-0">
                                                    <button @click="assignTransporter(carrier.id)"
                                                            :disabled="assigningCarrier === carrier.id"
                                                            class="inline-flex items-center justify-center gap-1 px-2.5 py-1.5 sm:px-4 sm:py-2 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white rounded-lg sm:rounded-xl text-[11px] sm:text-xs font-black shadow-2xs hover:shadow-xs transition-all whitespace-nowrap cursor-pointer">
                                                        <span x-show="assigningCarrier !== carrier.id" class="inline-flex items-center gap-1">
                                                            <span class="text-xs">✉️</span>
                                                            <span>{{ __('Send Request') }}</span>
                                                        </span>
                                                        <svg x-show="assigningCarrier === carrier.id" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </template>
                                    </div>

                                    {{-- Initial Server Rendered List (Before any dynamic search or if JS loading) --}}
                                    <div x-show="!loadingCarriers && dynamicCarriers.length === 0" class="space-y-1.5 sm:space-y-2.5 max-h-[60vh] overflow-y-auto pr-2">
                                        @if(isset($carriers) && $carriers->count() > 0)
                                            @foreach($carriers as $carrier)
                                                <div class="group relative bg-white hover:bg-slate-50 border border-slate-200 hover:border-blue-400 rounded-xl p-2 sm:p-3 transition-all duration-150 shadow-2xs hover:shadow-xs border-s-4 border-s-blue-600 flex items-center justify-between gap-2 sm:gap-3">
                                                    <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1">
                                                        @if($carrier->profile_picture)
                                                            <img src="{{ Storage::url($carrier->profile_picture) }}" class="w-9 h-9 sm:w-11 sm:h-11 rounded-full object-cover ring-1 ring-blue-100 flex-shrink-0 shadow-2xs">
                                                        @else
                                                            <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-gradient-to-br from-blue-600 to-indigo-700 text-white flex items-center justify-center font-black text-xs sm:text-base flex-shrink-0 shadow-2xs ring-1 ring-blue-100">
                                                                {{ strtoupper(substr($carrier->name, 0, 1)) }}
                                                            </div>
                                                        @endif
                                                        <div class="min-w-0 flex-1 {{ $isRTL ? 'text-right' : 'text-left' }}">
                                                            <div class="flex items-center gap-1 min-w-0">
                                                                <h4 class="font-black text-slate-900 text-xs sm:text-base tracking-tight leading-snug group-hover:text-blue-600 transition-colors truncate">{{ $carrier->name }}</h4>
                                                                @if($carrier->is_verified)
                                                                    <span class="inline-flex items-center text-blue-600 flex-shrink-0 text-[10px]" title="{{ __('Verified') }}">
                                                                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" viewBox="0 0 20 20" fill="currentColor">
                                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                                        </svg>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            <div class="flex items-center gap-1 mt-0.5 flex-wrap">
                                                                @if(isset($carrier->distance_km))
                                                                    <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded bg-blue-50 text-blue-900 text-[10px] sm:text-[11px] font-black border border-blue-200/60">
                                                                        <span>📍</span>
                                                                        <span>{{ $carrier->distance_km < 15 ? __('Same Governorate') : round($carrier->distance_km) . ' ' . __('km') }}</span>
                                                                    </span>
                                                                @endif
                                                                @if($carrier->camion_capacity)
                                                                    <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-950 text-[10px] sm:text-[11px] font-black border border-emerald-200/60">
                                                                        <span>⚖️</span>
                                                                        <span>{{ $carrier->camion_capacity < 100 ? $carrier->camion_capacity . ' ' . __('Tons') : $carrier->camion_capacity . ' ' . __('Kg') }}</span>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="flex-shrink-0">
                                                        <button @click="assignTransporter({{ $carrier->id }})"
                                                                :disabled="assigningCarrier === {{ $carrier->id }}"
                                                                class="inline-flex items-center justify-center gap-1 px-2.5 py-1.5 sm:px-4 sm:py-2 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white rounded-lg sm:rounded-xl text-[11px] sm:text-xs font-black shadow-2xs hover:shadow-xs transition-all whitespace-nowrap cursor-pointer">
                                                            <span x-show="assigningCarrier !== {{ $carrier->id }}" class="inline-flex items-center gap-1">
                                                                <span class="text-xs">✉️</span>
                                                                <span>{{ __('Send Request') }}</span>
                                                            </span>
                                                            <svg x-show="assigningCarrier === {{ $carrier->id }}" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="text-center py-8 text-gray-500">
                                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4M8 16l-4-4 4-4"/></svg>
                                                <p>{{ __('No transporters found matching your criteria.') }}</p>
                                            </div>
                                        @endif
                                    </div>
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
    @php
        $firstDealOrder = (isset($pendingOrders) && $pendingOrders->isNotEmpty()) ? $pendingOrders->first() : (isset($order) ? $order : null);
    @endphp
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('messageChat', () => ({
                newMessages: [],
                lastMessageId: {{ $messages->last()?->id ?? 0 }},
                assigningCarrier: null,
                showCounterOfferModal: false,
                showTransporters: false,
                selectedOrderId: {{ $firstDealOrder?->id ?? 'null' }},
                selectedOrderQty: 0,
                selectedOrderUnit: 'L',
                dynamicCarriers: [],
                loadingCarriers: false,
                filterSuitableOnly: false,
                counterOrderId: null,
                counterPrice: 0,
                counterQty: 1,
                counterUnit: 'L',
                submittingCounter: false,
                translations: {},
                translatingId: null,
                pickupGov: '{{ $firstDealOrder?->seller?->addresses?->first()?->governorate ?? "صفاقس" }}',
                pickupDel: '{{ $firstDealOrder?->seller?->addresses?->first()?->delegation ?? "" }}',
                pickupLat: null,
                pickupLng: null,
                dropoffGov: '{{ $firstDealOrder?->buyer?->addresses?->first()?->governorate ?? "تونس" }}',
                dropoffDel: '{{ $firstDealOrder?->buyer?->addresses?->first()?->delegation ?? "" }}',
                dropoffLat: null,
                dropoffLng: null,
                gettingGps: false,

                useCurrentLocation(type) {
                    if (!navigator.geolocation) {
                        alert('{{ __("GPS is not supported on this browser.") }}');
                        return;
                    }
                    this.gettingGps = true;
                    navigator.geolocation.getCurrentPosition((pos) => {
                        const lat = pos.coords.latitude;
                        const lng = pos.coords.longitude;
                        if (type === 'pickup') {
                            this.pickupLat = lat;
                            this.pickupLng = lng;
                            window.Alpine.store('toast').showToast('{{ __("Pickup GPS coordinates acquired!") }}', 'success');
                            this.fetchDealCarriers();
                        } else {
                            this.dropoffLat = lat;
                            this.dropoffLng = lng;
                            window.Alpine.store('toast').showToast('{{ __("Dropoff GPS coordinates acquired!") }}', 'success');
                        }
                        this.gettingGps = false;
                    }, (err) => {
                        this.gettingGps = false;
                        alert('{{ __("Could not get GPS location. Please check browser permissions.") }}');
                    }, { enableHighAccuracy: true });
                },

                async acceptTransportAssignment(loadId) {
                    try {
                        const res = await fetch(`/transport/loads/${loadId}/accept`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                        const data = await res.json();
                        if (data.success) {
                            window.Alpine.store('toast').showToast(data.message || '{{ __("Mission accepted successfully!") }}', 'success');
                            if (data.redirect_url) {
                                window.location.href = data.redirect_url;
                            } else {
                                setTimeout(() => window.location.reload(), 1500);
                            }
                        } else {
                            window.Alpine.store('toast').showToast(data.message || '{{ __("Action failed.") }}', 'error');
                        }
                    } catch (e) {
                        window.Alpine.store('toast').showToast('{{ __("Network error.") }}', 'error');
                    }
                },

                async denyTransportAssignment(loadId) {
                    if (!confirm('{{ __("Are you sure you want to decline this mission?") }}')) return;
                    try {
                        const res = await fetch(`/transport/loads/${loadId}/deny`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                        const data = await res.json();
                        if (data.success) {
                            window.Alpine.store('toast').showToast(data.message || '{{ __("Mission declined.") }}', 'info');
                            setTimeout(() => window.location.reload(), 1500);
                        } else {
                            window.Alpine.store('toast').showToast(data.message || '{{ __("Action failed.") }}', 'error');
                        }
                    } catch (e) {
                        window.Alpine.store('toast').showToast('{{ __("Network error.") }}', 'error');
                    }
                },

                async translateMessage(msgId, text) {
                    if (this.translations[msgId]) {
                        return;
                    }
                    this.translatingId = msgId;
                    try {
                        const res = await fetch(`{{ route('messages.translate') }}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                message_id: msgId,
                                text: text,
                                target_locale: '{{ app()->getLocale() }}'
                            })
                        });
                        const data = await res.json();
                        if (data.success && data.translated_text) {
                            this.translations[msgId] = data.translated_text;
                        } else {
                            window.Alpine.store('toast').showToast(data.message || '{{ __("Translation failed.") }}', 'error');
                        }
                    } catch (err) {
                        window.Alpine.store('toast').showToast('{{ __("Translation error.") }}', 'error');
                    } finally {
                        this.translatingId = null;
                    }
                },

                openTransporters(orderId, qty = 0, unit = 'L', sellerGov = '') {
                    this.selectedOrderId = orderId;
                    this.selectedOrderQty = qty;
                    this.selectedOrderUnit = unit;
                    if (sellerGov && (!this.pickupGov || this.pickupGov === 'صفاقس')) {
                        this.pickupGov = sellerGov;
                    }
                    this.showTransporters = true;
                    this.fetchDealCarriers();
                },

                async fetchDealCarriers() {
                    this.loadingCarriers = true;
                    try {
                        const params = new URLSearchParams();
                        if (this.pickupLat && this.pickupLng) {
                            params.append('lat', this.pickupLat);
                            params.append('lng', this.pickupLng);
                        } else if (this.pickupGov) {
                            params.append('governorate', this.pickupGov);
                        }
                        if (this.selectedOrderQty > 0) {
                            params.append('required_qty', this.selectedOrderQty);
                            params.append('unit', this.selectedOrderUnit || 'l');
                        }
                        if (this.filterSuitableOnly) {
                            params.append('only_suitable', '1');
                        }
                        const res = await fetch(`{{ route('carriers.search', ['locale' => app()->getLocale()]) }}?${params.toString()}`);
                        const data = await res.json();
                        if (data.success && data.carriers) {
                            this.dynamicCarriers = data.carriers;
                        }
                    } catch (e) {
                        console.error('Failed to fetch sorted carriers:', e);
                    } finally {
                        this.loadingCarriers = false;
                    }
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
                    const orderId = this.selectedOrderId || {{ $firstDealOrder?->id ?? 'null' }};
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
                            body: JSON.stringify({
                                order_id: orderId,
                                carrier_id: carrierId,
                                pickup_governorate: this.pickupGov || null,
                                pickup_delegation: this.pickupDel || null,
                                pickup_lat: this.pickupLat || null,
                                pickup_lng: this.pickupLng || null,
                                dropoff_governorate: this.dropoffGov || null,
                                dropoff_delegation: this.dropoffDel || null,
                                dropoff_lat: this.dropoffLat || null,
                                dropoff_lng: this.dropoffLng || null,
                            })
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
                locatingGps: false,
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
                },
                sendGpsLocation() {
                    if (!navigator.geolocation) {
                        alert('{{ __("GPS is not supported on this device.") }}');
                        return;
                    }
                    this.locatingGps = true;
                    navigator.geolocation.getCurrentPosition(
                        (pos) => {
                            const lat = pos.coords.latitude.toFixed(6);
                            const lng = pos.coords.longitude.toFixed(6);
                            const mapsUrl = `https://www.google.com/maps?q=${lat},${lng}`;
                            this.message = `📍 موقعي الدقيق عبر الـ GPS:\n${mapsUrl}\n(يمكنك النقر مباشرة لفتح نظام الملاحة خرائط جوجل 🧭)`;
                            this.locatingGps = false;
                            this.send();
                        },
                        (err) => {
                            this.locatingGps = false;
                            alert('{{ __("Unable to access GPS location.") }}');
                        },
                        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                    );
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
