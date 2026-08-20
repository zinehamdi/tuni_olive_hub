@extends('layouts.app')

@push('head')
<!-- Quill Rich Text Editor CDN -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<style>
    .ql-editor {
        min-height: 250px;
        direction: rtl;
        text-align: right;
    }
    .ql-toolbar {
        border-top-left-radius: 0.5rem;
        border-top-right-radius: 0.5rem;
        background-color: #f9fafb;
    }
    #editorContainer {
        border-bottom-left-radius: 0.5rem;
        border-bottom-right-radius: 0.5rem;
        min-height: 250px;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-100 py-8">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">{{ __('Manage Subscribers & Users') }}</h1>
                <p class="text-gray-600">{{ __('View captured leads and registered users for bulk messaging') }}</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition font-bold">
                ← {{ __('Back to Dashboard') }}
            </a>
        </div>

        <!-- Filters & Summary -->
        <div class="bg-white rounded-2xl shadow-md p-4 mb-6 flex flex-col lg:flex-row gap-4 items-center justify-between">
            <div class="flex flex-col md:flex-row gap-4 items-center w-full lg:w-auto">
                <div class="flex items-center gap-3 w-full md:w-auto">
                    <label for="roleFilter" class="font-bold text-gray-700 whitespace-nowrap">الدور:</label>
                    <select id="roleFilter" class="rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 w-full md:w-auto" onchange="filterByRole(this.value)">
                        <option value="all" {{ $role === 'all' ? 'selected' : '' }}>الكل (All Contacts)</option>
                        <option value="subscriber" {{ $role === 'subscriber' ? 'selected' : '' }}>زوار النشرة البريدية (Newsletter Leads)</option>
                        <option value="has_listings" {{ $role === 'has_listings' ? 'selected' : '' }}>مستخدمون لديهم عروض (Has Listings)</option>
                        <option value="farmer" {{ $role === 'farmer' ? 'selected' : '' }}>فلاح (Farmer)</option>
                        <option value="mill" {{ $role === 'mill' ? 'selected' : '' }}>معصرة (Mill)</option>
                        <option value="packer" {{ $role === 'packer' ? 'selected' : '' }}>مُعلِّب (Packer)</option>
                        <option value="carrier" {{ $role === 'carrier' ? 'selected' : '' }}>ناقل (Carrier)</option>
                        <option value="normal" {{ $role === 'normal' ? 'selected' : '' }}>عادي (Normal/Consumer)</option>
                        <option value="admin" {{ $role === 'admin' ? 'selected' : '' }}>مسؤول (Admin)</option>
                    </select>
                </div>

                <div class="flex items-center gap-3 w-full md:w-auto">
                    <label for="typeFilter" class="font-bold text-gray-700 whitespace-nowrap">النوع:</label>
                    <select id="typeFilter" class="rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 w-full md:w-auto" onchange="filterByType(this.value)">
                        <option value="all" {{ $type === 'all' ? 'selected' : '' }}>الكل (All)</option>
                        <option value="email" {{ $type === 'email' ? 'selected' : '' }}>البريد الإلكتروني فقط (Email Only)</option>
                        <option value="whatsapp" {{ $type === 'whatsapp' ? 'selected' : '' }}>الواتساب فقط (WhatsApp Only)</option>
                    </select>
                </div>
                
                <div class="flex items-center gap-2 w-full md:w-80">
                    <input type="text" id="searchInput" placeholder="بحث بالبريد، الاسم أو الهاتف..." value="{{ request('search') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200">
                    <button type="button" onclick="performSearch()" class="px-4 py-2 bg-[#6A8F3B] hover:bg-[#5b7c32] text-white rounded-xl font-bold transition shadow whitespace-nowrap">
                        بحث
                    </button>
                </div>
            </div>
            
            <div class="text-sm font-bold text-gray-600">
                إجمالي جهات الاتصال في هذا الفلتر: <span class="text-green-600 text-lg">{{ $totalContacts }}</span>
            </div>
        </div>

        <form action="{{ route('admin.subscribers.bulk-message') }}" method="POST" id="bulkForm">
            @csrf
            
            <div class="flex justify-between items-center mb-4">
                <div class="flex gap-2">
                    <button type="button" onclick="openBulkModal()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-bold shadow disabled:opacity-50" id="btnBulkMessage" {{ $totalContacts > 0 ? '' : 'disabled' }}>
                        <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                        {{ __('Send Bulk Message') }}
                    </button>
                </div>
            </div>

            <!-- Subscribers Table -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b-2 border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-left">
                                    <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                                </th>
                                <th class="px-4 py-4 text-left text-xs font-bold text-gray-500 uppercase">#</th>
                                <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">{{ __('Source') }}</th>
                                <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">{{ __('Contact Type') }}</th>
                                <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">{{ __('Contact Value') }}</th>
                                <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">{{ __('Date Added') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($contacts as $contact)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <input type="checkbox" name="contacts[]" value="{{ $contact->source }}:{{ $contact->type }}:{{ $contact->contact_value }}" class="contact-checkbox rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                                </td>
                                <td class="px-4 py-4 text-xs font-bold text-gray-400">
                                    #{{ $loop->iteration + ($contacts->currentPage() - 1) * $contacts->perPage() }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($contact->source === 'user')
                                        <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs font-bold">{{ __('Registered User') }} ({{ $contact->role }})</span>
                                    @else
                                        <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded text-xs font-bold">{{ __('Lead (Visitor)') }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($contact->type === 'email')
                                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold flex items-center inline-flex gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                            Email
                                        </span>
                                    @else
                                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold flex items-center inline-flex gap-1">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 0C5.385 0 0 5.385 0 12.031c0 2.128.552 4.135 1.523 5.918L.004 24l6.236-1.636a11.967 11.967 0 005.791 1.492h.004c6.645 0 12.03-5.385 12.03-12.031C24.065 5.385 18.68 0 12.031 0zm0 21.84c-1.802 0-3.564-.485-5.111-1.401l-.366-.217-3.799.996 1.015-3.704-.239-.38C2.569 15.485 2.016 13.785 2.016 12.03 2.016 6.505 6.51 2.01 12.035 2.01c2.677 0 5.193 1.043 7.085 2.936A9.972 9.972 0 0122.052 12.03c0 5.524-4.494 10.02-10.02 10.02v-.01zm5.496-7.502c-.301-.151-1.782-.879-2.058-.979-.276-.1-.478-.151-.678.151-.201.301-.778.979-.953 1.18-.175.201-.35.226-.652.075-.301-.151-1.272-.469-2.423-1.496-.895-.8-1.501-1.787-1.677-2.088-.175-.301-.019-.464.131-.614.136-.136.301-.351.452-.526.151-.176.201-.301.301-.502.101-.201.05-.376-.025-.526-.075-.151-.678-1.634-.928-2.237-.243-.591-.49-.51-.678-.519-.175-.01-.377-.01-.578-.01-.201 0-.527.075-.803.376-.276.301-1.054 1.03-1.054 2.508 0 1.479 1.079 2.909 1.23 3.109.15.201 2.118 3.232 5.13 4.531 2.457 1.06 2.96 1.058 3.565.98.665-.084 1.782-.729 2.032-1.433.251-.703.251-1.306.175-1.433-.075-.126-.276-.201-.578-.352z"></path></svg>
                                            WhatsApp
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-900">{{ $contact->contact_value }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @php $date = \Carbon\Carbon::parse($contact->created_at); @endphp
                                    <div class="text-sm text-gray-900">{{ $date->format('Y-m-d H:i') }}</div>
                                    <div class="text-xs text-gray-600">{{ $date->diffForHumans() }}</div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    {{ __('No contacts found yet.') }}
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $contacts->links() }}
                </div>
            </div>

            <!-- Bulk Modal -->
            <div id="bulkModal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
                <div class="fixed inset-0 bg-black/60 transition-opacity" onclick="closeBulkModal()"></div>
                
                <div class="bg-white rounded-2xl shadow-2xl z-10 w-full max-w-3xl mx-4 overflow-hidden transform transition-all">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900">{{ __('Compose Bulk Message') }}</h3>
                        <button type="button" onclick="closeBulkModal()" class="text-gray-400 hover:text-gray-600 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    
                    <div class="p-6 max-h-[80vh] overflow-y-auto">
                        <div class="mb-4 text-sm text-gray-600 p-4 bg-blue-50 rounded-lg border border-blue-100 text-right" dir="rtl">
                            <strong class="font-bold text-blue-800">{{ __('Note:') }}</strong> سيتم إرسال رسائل البريد الإلكتروني تلقائياً في الخلفية. بالنسبة لرسائل الواتساب، سيتم تجميعها وتسهيل إرسالها تتابعياً عبر تطبيق واتساب.
                        </div>

                        <!-- Recipient Scope Selector -->
                        <div class="mb-4 p-4 bg-gray-50 border border-gray-200 rounded-xl space-y-3 text-right" dir="rtl">
                            <label class="block text-sm font-bold text-gray-850 mb-1">المستلمون (Recipients)</label>
                            <div class="flex flex-col gap-2.5">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="recipient_scope" value="selected" checked onchange="toggleRecipientScope()" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="text-sm font-semibold text-gray-700">الجهات المحددة يدوياً فقط (<span id="selectedCountDisplay" class="font-bold text-green-600">0</span> جهة)</span>
                                </label>
                                
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="recipient_scope" value="range" onchange="toggleRecipientScope()" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="text-sm font-semibold text-gray-700">🎯 تحديد نطاق بالرقم (Range Batch) — مثال: من 1 إلى 49</span>
                                </label>

                                <!-- Range Inputs Box -->
                                <div id="rangeInputsContainer" class="hidden mt-1 p-4 bg-gradient-to-br from-green-50 to-emerald-50/60 border border-green-200 rounded-xl space-y-3 text-right">
                                    <div class="flex items-center justify-between flex-wrap gap-1">
                                        <div class="text-xs font-bold text-green-900">✍️ اكتب أرقام النطاق مباشرة (مثال: من 1 إلى 50، أو من 500 إلى 550):</div>
                                        <div class="text-xs text-gray-600 font-semibold">إجمالي القائمة: <strong class="text-green-700 font-bold">{{ $totalContacts }}</strong> جهة</div>
                                    </div>
                                    
                                    <div class="flex items-center gap-3 flex-wrap">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-bold text-gray-700">من رقم:</span>
                                            <input type="number" id="range_start" name="range_start" value="1" min="1" inputmode="numeric" placeholder="1" class="w-32 px-3 py-2 border-2 border-green-300 rounded-lg text-center font-bold text-base focus:ring-2 focus:ring-green-500 focus:border-green-600 bg-white">
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-bold text-gray-700">إلى رقم:</span>
                                            <input type="number" id="range_end" name="range_end" value="50" min="1" inputmode="numeric" placeholder="50" class="w-32 px-3 py-2 border-2 border-green-300 rounded-lg text-center font-bold text-base focus:ring-2 focus:ring-green-500 focus:border-green-600 bg-white">
                                        </div>
                                        
                                        <button type="button" onclick="nextRangeBatch(50)" class="px-3.5 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-xs font-bold transition shadow-sm flex items-center gap-1">
                                            <span>الدفعة التالية (+50)</span>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                        </button>
                                    </div>
                                    
                                    <!-- Quick Presets -->
                                    <div class="flex items-center gap-1.5 flex-wrap pt-2 border-t border-green-200/60">
                                        <span class="text-xs text-gray-600 font-semibold">اختيار سريع:</span>
                                        <button type="button" onclick="setRange(1, 50)" class="px-2.5 py-1 bg-white border border-green-300 text-green-800 rounded-md text-xs font-bold hover:bg-green-100 transition">1 - 50</button>
                                        <button type="button" onclick="setRange(51, 100)" class="px-2.5 py-1 bg-white border border-green-300 text-green-800 rounded-md text-xs font-bold hover:bg-green-100 transition">51 - 100</button>
                                        <button type="button" onclick="setRange(101, 150)" class="px-2.5 py-1 bg-white border border-green-300 text-green-800 rounded-md text-xs font-bold hover:bg-green-100 transition">101 - 150</button>
                                        <button type="button" onclick="setRange(151, 200)" class="px-2.5 py-1 bg-white border border-green-300 text-green-800 rounded-md text-xs font-bold hover:bg-green-100 transition">151 - 200</button>
                                        <button type="button" onclick="setRange(201, 250)" class="px-2.5 py-1 bg-white border border-green-300 text-green-800 rounded-md text-xs font-bold hover:bg-green-100 transition">201 - 250</button>
                                        <button type="button" onclick="setRange(251, 300)" class="px-2.5 py-1 bg-white border border-green-300 text-green-800 rounded-md text-xs font-bold hover:bg-green-100 transition">251 - 300</button>
                                    </div>
                                </div>

                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="recipient_scope" value="all_filtered" onchange="toggleRecipientScope()" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="text-sm font-semibold text-gray-700">إرسال لجميع الجهات في هذا الفلتر ({{ $totalContacts }} جهة)</span>
                                </label>
                            </div>
                            <input type="hidden" name="role_filter" value="{{ $role }}">
                            <input type="hidden" name="type_filter" value="{{ $type }}">
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('Select Template (Optional)') }}</label>
                            <select id="templateSelector" name="template" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200" onchange="loadTemplate()">
                                <option value="">-- Custom Message --</option>
                                <option value="fix_listings_ar">تنبيه: مراجعة وتعديل العروض (العربية) 🫒</option>
                                <option value="fix_listings_fr">Correction d'annonces et guide (Français) 🫒</option>
                                <option value="welcome">{{ __('Welcome to Zintoop') }}</option>
                                <option value="update_announcement">🚀 {{ __('ZinToop Major Platform Update (HTML Theme)') }}</option>
                                <option value="latest_listing">🆕 {{ __('Latest Listing — Subscriber Notification') }}</option>
                                <option value="update">{{ __('Weekly Update / News') }}</option>
                                <option value="deal">{{ __('New Deals Available') }}</option>
                                <option value="guide">{{ __('How to Use Zintoop Guide') }}</option>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('Subject (For Emails Only)') }}</label>
                            <input type="text" name="subject" id="msgSubject" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200" placeholder="e.g. Welcome to our platform!">
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('Message Body') }}</label>
                            <!-- Hidden input containing HTML output from Quill -->
                            <input type="hidden" name="message_body" id="msgBody">
                            <div id="editorContainer"></div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                        <button type="button" onclick="closeBulkModal()" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition font-bold">{{ __('Cancel') }}</button>
                        <button type="submit" id="btnSubmitBulk" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-bold flex items-center gap-2 shadow">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                            {{ __('Dispatch Messages') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <!-- WhatsApp Queue Modal -->
        <div id="waQueueModal" class="fixed inset-0 z-[60] flex items-center justify-center hidden">
            <div class="fixed inset-0 bg-black opacity-80 transition-opacity"></div>
            <div class="bg-white rounded-2xl shadow-2xl z-10 w-full max-w-md mx-4 overflow-hidden transform transition-all text-center">
                <div class="p-8">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4 text-green-600">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 0C5.385 0 0 5.385 0 12.031c0 2.128.552 4.135 1.523 5.918L.004 24l6.236-1.636a11.967 11.967 0 005.791 1.492h.004c6.645 0 12.03-5.385 12.03-12.031C24.065 5.385 18.68 0 12.031 0zm0 21.84c-1.802 0-3.564-.485-5.111-1.401l-.366-.217-3.799.996 1.015-3.704-.239-.38C2.569 15.485 2.016 13.785 2.016 12.03 2.016 6.505 6.51 2.01 12.035 2.01c2.677 0 5.193 1.043 7.085 2.936A9.972 9.972 0 0122.052 12.03c0 5.524-4.494 10.02-10.02 10.02v-.01zm5.496-7.502c-.301-.151-1.782-.879-2.058-.979-.276-.1-.478-.151-.678.151-.201.301-.778.979-.953 1.18-.175.201-.35.226-.652.075-.301-.151-1.272-.469-2.423-1.496-.895-.8-1.501-1.787-1.677-2.088-.175-.301-.019-.464.131-.614.136-.136.301-.351.452-.526.151-.176.201-.301.301-.502.101-.201.05-.376-.025-.526-.075-.151-.678-1.634-.928-2.237-.243-.591-.49-.51-.678-.519-.175-.01-.377-.01-.578-.01-.201 0-.527.075-.803.376-.276.301-1.054 1.03-1.054 2.508 0 1.479 1.079 2.909 1.23 3.109.15.201 2.118 3.232 5.13 4.531 2.457 1.06 2.96 1.058 3.565.98.665-.084 1.782-.729 2.032-1.433.251-.703.251-1.306.175-1.433-.075-.126-.276-.201-.578-.352z"></path></svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ __('WhatsApp Queue') }}</h3>
                    <p class="text-gray-600 mb-6" id="waQueueStatus">0 / 0 Messages Sent</p>
                    
                    <a href="#" target="_blank" id="btnSendNextWa" class="w-full block px-6 py-4 bg-green-600 text-white rounded-xl hover:bg-green-700 transition font-bold text-lg shadow-lg mb-4">
                        {{ __('Send to Next Contact') }}
                    </a>

                    <button type="button" onclick="closeWaQueue()" class="text-gray-500 hover:text-gray-700 font-semibold underline">{{ __('Close Queue') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const templates = {
        'fix_listings_ar': {
            subject: 'تنبيه هام: الرجاء مراجعة وتحديث عروض زيت الزيتون الخاصة بكم على منصة الزين 🫒',
            body: `
<p>أهلاً بك شريكنا العزيز،</p>
<p>لقد لاحظنا وجود بعض الأخطاء الشائعة في بعض العروض المنشورة مؤخراً على منصة الزين (Zintoop)، ونود مساعدتك على تصحيحها لضمان جذب أكبر عدد من المشترين:</p>
<p><strong>⚠️ الأخطاء الشائعة التي يجب إصلاحها:</strong></p>
<p>1. <strong>عدم إضافة صور:</strong> العروض التي لا تحتوي على صور حقيقية للمنتج لا تحظى بنسب مشاهدة عالية.</p>
<p>2. <strong>الخلط بين نوع المنتج:</strong> يرجى التأكد من اختيار <strong>زيت زيتون</strong> وليس <strong>زيتون</strong> إذا كنت تبيع زيتاً.</p>
<hr>
<p><strong>🛠️ كيف تقوم بتعديل عرضك وإضافة الصور؟</strong></p>
<p>لقد قمنا بتسهيل وتحديث عملية التعديل بالكامل:</p>
<p>1. قم بزيارة <strong>لوحة التحكم (Dashboard)</strong> الخاصة بك.</p>
<p>2. توجه إلى العرض المعني واضغط على زر <strong>تعديل العرض</strong>.</p>
<p>3. قم بتعديل نوع المنتج (زيت زيتون / زيتون) وتحديث الكمية والسعر.</p>
<p>4. اضغط على قسم <strong>صور المنتج</strong> لرفع صور جديدة وحفظ التعديلات.</p>
<p>تفضل بزيارة لوحة التحكم الآن لتحديث عروضك:<br>
<a href="https://zintoop.com/dashboard">رابط لوحة التحكم</a></p>
<p>مع تحيات فريق عمل منصة الزين (Zintoop).</p>
`
        },
        'fix_listings_fr': {
            subject: 'Important: Veuillez vérifier et mettre à jour vos annonces d\'huile d\'olive sur ZinToop 🫒',
            body: `
<p>Bonjour,</p>
<p>Nous avons constaté quelques erreurs courantes sur certaines annonces récemment publiées sur la plateforme ZinToop, et nous aimerions vous aider à les corriger pour maximiser vos ventes :</p>
<p><strong>⚠️ Erreurs courantes à corriger :</strong></p>
<p>1. <strong>Absence d'images :</strong> Les annonces sans photos réelles reçoivent très peu d'intérêt de la part des acheteurs.</p>
<p>2. <strong>Confusion de catégorie :</strong> Veuillez vérifier que vous avez sélectionné <strong>Huile d'olive</strong> et non <strong>Olives</strong> si vous vendez de l'huile.</p>
<hr>
<p><strong>🛠️ Comment modifier votre annonce et ajouter des photos ?</strong></p>
<p>Nous avons entièrement simplifié le processus de modification :</p>
<p>1. Connectez-vous à votre <strong>Tableau de bord (Dashboard)</strong>.</p>
<p>2. Repérez l'annonce concernée et cliquez sur <strong>Modifier l'annonce</strong>.</p>
<p>3. Modifiez la catégorie (Huile d'olive / Olives), la quantité et le prix.</p>
<p>4. Allez dans la section <strong>Photos du produit</strong> pour importer de nouvelles images et cliquez sur <strong>Enregistrer</strong>.</p>
<hr>
<p>Accédez à votre tableau de bord maintenant pour corriger vos annonces :<br>
<a href="https://zintoop.com/dashboard">Accéder au Tableau de bord</a></p>
<br>
<p>Cordialement,<br>
L'équipe ZinToop.</p>
`
        },
        'welcome': {
            subject: 'مرحباً بك في منصة الزين Zintoop!',
            body: "<p>مرحباً،</p><p>نحن سعداء بانضمامك لمنصة الزين Zintoop، أكبر سوق لزيت الزيتون.<br>يمكنك الآن استكشاف أحدث العروض والطلبات.</p><p>تفضل بزيارة المنصة: <a href='https://zintoop.com'>https://zintoop.com</a></p><p>فريق الزين.</p>"
        },
        'latest_listing': {
            subject: '🆕 {{ addslashes($latestListing?->product?->variety ?? "عرض جديد") }} — {{ addslashes($latestListing?->product?->type === "oil" ? "زيت زيتون" : "زيتون") }} | ZinToop',
            body: `<p style="direction:rtl;text-align:right;"><strong>🆕 عرض جديد في السوق</strong></p>
<p style="direction:rtl;text-align:right;">تم نشر عرض جديد على منصة زينتوب:</p>
<ul style="direction:rtl;text-align:right;">
    <li><strong>المنتج:</strong> {{ addslashes($latestListing?->product?->variety ?? '—') }} ({{ addslashes($latestListing?->product?->type === 'oil' ? 'زيت زيتون' : 'زيتون') }})</li>
    <li><strong>الكمية:</strong> {{ addslashes(number_format($latestListing?->quantity ?? 0, 0)) }} {{ addslashes($latestListing?->unit ?? '') }}</li>
    <li><strong>السعر:</strong> {{ addslashes($latestListing && $latestListing->price > 0 ? number_format($latestListing->price, 2).' '.($latestListing->currency ?? 'TND') : 'عند الطلب') }}</li>
    {{ $latestListing?->governorate ? '<li><strong>المنطقة:</strong> '.addslashes($latestListing->governorate).'</li>' : '' }}
</ul>
<p style="direction:rtl;text-align:right;"><a href="{{ $latestListing ? route('listings.show', $latestListing->id) : url('/#products') }}">👁 مشاهدة العرض الآن</a></p>`
        },
        'update_announcement': {
            subject: '🚀 إطلاق التحديث الجديد لمنصة زين توب | ZinToop New Update',
            body: `
<p style="direction: rtl; text-align: right;"><strong>🚀 إعلان إطلاق التحديث الشامل لمنصة زين توب (ZinToop)</strong></p>
<p style="direction: rtl; text-align: right;">سيتم إرسال القالب الترويجي الاحترافي المنسق بـ HTML وألوان المنصة الرسمية تلقائياً لجميع المشتركين والمستخدمين، متضمناً:</p>
<ul style="direction: rtl; text-align: right;">
    <li>💼 <strong>دليل الخدمات الفلاحية والتجارية (ServiceHub):</strong> المعاصر، الناقلين، التعبئة والتغليف والمخلصين الجمركيين.</li>
    <li>📈 <strong>أسعار بورصة زيت الزيتون:</strong> متابعة يومية لأسعار السوق التونسي والعالمي.</li>
    <li>📱 <strong>التواصل الفوري والواتساب:</strong> إمكانية التواصل المباشر وتصفح الملفات الموثقة.</li>
</ul>
<p style="direction: rtl; text-align: right;">يمكنك استعراض الشكل النهائي للقالب على الرابط المحاكي: <a href="/email-preview" target="_blank">معاينة تصميم الإيميل</a></p>
`
        },
        'update': {
            subject: 'نشرة أخبار الزين Zintoop',
            body: "<p>مرحباً،</p><p>إليك أحدث الأخبار وتحديثات الأسعار لهذا الأسبوع في منصة الزين.</p><p>تفضل بزيارة المنصة لمعرفة المزيد: <a href='https://zintoop.com/prices'>https://zintoop.com/prices</a></p><p>فريق الزين.</p>"
        },
        'deal': {
            subject: 'عروض جديدة متاحة الآن!',
            body: "<p>مرحباً،</p><p>هناك عروض جديدة لزيت الزيتون بانتظارك على منصة الزين. لا تفوت الفرصة!</p><p>تصفح العروض الآن: <a href='https://zintoop.com/market'>https://zintoop.com/market</a></p><p>فريق الزين.</p>"
        },
        'guide': {
            subject: 'دليلك الشامل لاستخدام منصة الزين Zintoop',
            body: `
<p>مرحباً بك في منصة الزين Zintoop!</p>
<p>إليك كيف يمكنك الاستفادة القصوى من منصتنا:</p>
<h3>1. كيفية التسجيل</h3>
<p>قم بزيارة صفحة التسجيل، اختر دورك (فلاح، معصرة، ناقل...) واملأ بياناتك.</p>
<h3>2. كيفية إضافة وطلب العروض (Deals)</h3>
<p>يمكنك تقديم العروض وتصفح الأسعار مباشرة من لوحة التحكم الخاصة بك.</p>
<h3>3. كيفية المحادثة (الدردشة)</h3>
<p>توفر منصة الزين نظام دردشة متكامل للتواصل المباشر مع الأعضاء.</p>
<h3>4. تتبع الناقلين (Transporters)</h3>
<p>تابع مسار شحنتك بكل سهولة وتأكد من وصول زيتك بأمان.</p>
<p>إذا كان لديك أي استفسار، لا تتردد في التواصل معنا.</p>
<p>فريق الزين.</p>
`
        }
    };

    const selectAllCheckbox = document.getElementById('selectAll');
    const contactCheckboxes = document.querySelectorAll('.contact-checkbox');
    const btnBulkMessage = document.getElementById('btnBulkMessage');
    let quill;

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Quill Rich Text Editor
        quill = new Quill('#editorContainer', {
            theme: 'snow',
            modules: {
                toolbar: {
                    container: [
                        [{ 'header': [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline', 'clean'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        ['link', 'image']
                    ],
                    handlers: {
                        image: imageHandler
                    }
                }
            }
        });

        // Sync editor output HTML to hidden input
        quill.on('text-change', function() {
            document.getElementById('msgBody').value = quill.root.innerHTML;
        });

        updateBulkButton();
    });

    // Custom image handler for uploading directly to server
    function imageHandler() {
        const input = document.createElement('input');
        input.setAttribute('type', 'file');
        input.setAttribute('accept', 'image/*');
        input.click();

        input.onchange = async () => {
            const file = input.files[0];
            if (!file) return;

            const range = quill.getSelection(true);
            quill.insertText(range.index, '[Uploading image...]');

            const formData = new FormData();
            formData.append('image', file);
            formData.append('_token', '{{ csrf_token() }}');

            try {
                const res = await fetch('{{ route('admin.subscribers.upload-image') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const json = await res.json();
                
                quill.deleteText(range.index, '[Uploading image...]'.length);

                if (res.ok && json.url) {
                    quill.insertEmbed(range.index, 'image', json.url);
                } else {
                    alert('Error: ' + (json.error || 'Failed to upload image'));
                }
            } catch (err) {
                quill.deleteText(range.index, '[Uploading image...]'.length);
                alert('Image upload failed.');
            }
        };
    }

    function updateBulkButton() {
        const checkedBoxes = Array.from(contactCheckboxes).filter(cb => cb.checked);
        document.getElementById('selectedCountDisplay').innerText = checkedBoxes.length;
        
        const totalMatching = {{ $totalContacts }};
        if (totalMatching > 0) {
            btnBulkMessage.disabled = false;
        } else {
            btnBulkMessage.disabled = checkedBoxes.length === 0;
        }
    }

    selectAllCheckbox.addEventListener('change', function() {
        contactCheckboxes.forEach(cb => cb.checked = this.checked);
        updateBulkButton();
    });

    contactCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkButton);
    });

    function performSearch() {
        const role = document.getElementById('roleFilter').value;
        const type = document.getElementById('typeFilter').value;
        const search = document.getElementById('searchInput').value;
        window.location.href = `{{ route('admin.subscribers.index') }}?role=${role}&type=${type}&search=${encodeURIComponent(search)}`;
    }

    // Trigger search on enter key press
    document.getElementById('searchInput').addEventListener('keyup', function(event) {
        if (event.key === 'Enter') {
            performSearch();
        }
    });

    function filterByRole(role) {
        const type = document.getElementById('typeFilter').value;
        const search = document.getElementById('searchInput').value;
        window.location.href = `{{ route('admin.subscribers.index') }}?role=${role}&type=${type}&search=${encodeURIComponent(search)}`;
    }

    function filterByType(type) {
        const role = document.getElementById('roleFilter').value;
        const search = document.getElementById('searchInput').value;
        window.location.href = `{{ route('admin.subscribers.index') }}?role=${role}&type=${type}&search=${encodeURIComponent(search)}`;
    }

    function setRange(start, end) {
        document.getElementById('range_start').value = start;
        document.getElementById('range_end').value = end;
    }

    function nextRangeBatch(step = 50) {
        const endInput = document.getElementById('range_end');
        const startInput = document.getElementById('range_start');
        let currentEnd = parseInt(endInput.value) || 0;
        let newStart = currentEnd + 1;
        let newEnd = newStart + step - 1;
        startInput.value = newStart;
        endInput.value = newEnd;
    }

    function toggleRecipientScope() {
        const scope = document.querySelector('input[name="recipient_scope"]:checked').value;
        const rangeContainer = document.getElementById('rangeInputsContainer');
        if (rangeContainer) {
            if (scope === 'range') {
                rangeContainer.classList.remove('hidden');
            } else {
                rangeContainer.classList.add('hidden');
            }
        }
    }

    function openBulkModal() {
        document.getElementById('bulkModal').classList.remove('hidden');
        updateBulkButton();
    }

    function closeBulkModal() {
        document.getElementById('bulkModal').classList.add('hidden');
    }

    function loadTemplate() {
        const val = document.getElementById('templateSelector').value;
        if(val && templates[val]) {
            document.getElementById('msgSubject').value = templates[val].subject;
            quill.root.innerHTML = templates[val].body;
            document.getElementById('msgBody').value = templates[val].body;
        } else {
            document.getElementById('msgSubject').value = '';
            quill.root.innerHTML = '';
            document.getElementById('msgBody').value = '';
        }
    }

    // AJAX form submission to handle WhatsApp queue logic
    let waQueue = [];
    let currentWaIndex = 0;
    
    document.getElementById('bulkForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const scope = formData.get('recipient_scope');
        const checkedCount = Array.from(contactCheckboxes).filter(cb => cb.checked).length;

        if (scope === 'selected' && checkedCount === 0) {
            alert('الرجاء تحديد مستلم واحد على الأقل أو اختيار إرسال حسب النطاق أو للجميع.');
            return;
        }

        if (scope === 'range') {
            const start = parseInt(document.getElementById('range_start').value) || 0;
            const end = parseInt(document.getElementById('range_end').value) || 0;
            if (start < 1 || end < start) {
                alert('الرجاء إدخال نطاق أرقام صحيح (مثال: من 1 إلى 49).');
                return;
            }
        }

        const btn = document.getElementById('btnSubmitBulk');
        btn.disabled = true;
        btn.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Sending...';

        try {
            const response = await fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const result = await response.json();
            
            if (result.success) {
                closeBulkModal();
                
                if (result.emails_queued > 0) {
                    alert(`${result.emails_queued} emails queued for delivery.`);
                }
                
                // Init WA Queue
                if (result.whatsapp_queue && result.whatsapp_queue.length > 0) {
                    waQueue = result.whatsapp_queue;
                    currentWaIndex = 0;
                    document.getElementById('waQueueModal').classList.remove('hidden');
                    updateWaQueueUI();
                } else {
                    window.location.reload();
                }
            } else {
                alert('Error: ' + result.message);
            }
        } catch (error) {
            alert('An error occurred. Please try again.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg> Dispatch Messages';
        }
    });

    function updateWaQueueUI() {
        const statusText = document.getElementById('waQueueStatus');
        const btnNext = document.getElementById('btnSendNextWa');
        
        statusText.innerText = `${currentWaIndex} / ${waQueue.length} Messages Sent`;
        
        if (currentWaIndex < waQueue.length) {
            const item = waQueue[currentWaIndex];
            const text = encodeURIComponent(item.message);
            let phone = item.phone.replace(/[^0-9]/g, '');
            if(!phone.startsWith('216')) { // Auto prefix Tunisia for raw numbers if needed
                if(phone.length === 8) phone = '216' + phone;
            }
            btnNext.href = `https://wa.me/${phone}?text=${text}`;
            btnNext.innerText = `Send to ${item.phone} (Open WhatsApp)`;
        } else {
            btnNext.href = '#';
            btnNext.innerText = 'All Done! ✓';
            btnNext.classList.replace('bg-green-600', 'bg-gray-400');
            btnNext.classList.remove('hover:bg-green-700');
            btnNext.onclick = (e) => { e.preventDefault(); closeWaQueue(); };
        }
    }

    document.getElementById('btnSendNextWa').addEventListener('click', function(e) {
        if (currentWaIndex < waQueue.length) {
            currentWaIndex++;
            setTimeout(updateWaQueueUI, 1000); // update UI after they click to open the new tab
        }
    });

    function closeWaQueue() {
        document.getElementById('waQueueModal').classList.add('hidden');
        window.location.reload();
    }
</script>
@endsection
