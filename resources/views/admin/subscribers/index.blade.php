@extends('layouts.app')

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

        <form action="{{ route('admin.subscribers.bulk-message') }}" method="POST" id="bulkForm">
            @csrf
            
            <div class="flex justify-between items-center mb-4">
                <div class="flex gap-2">
                    <button type="button" onclick="openBulkModal()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-bold shadow disabled:opacity-50" id="btnBulkMessage" disabled>
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
                                <td class="px-6 py-4">
                                    @if($contact->source === 'user')
                                        <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs font-bold">{{ __('Registered User') }}</span>
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
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
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
                <div class="fixed inset-0 bg-black opacity-50 transition-opacity" onclick="closeBulkModal()"></div>
                
                <div class="bg-white rounded-2xl shadow-2xl z-10 w-full max-w-3xl mx-4 overflow-hidden transform transition-all">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900">{{ __('Compose Bulk Message') }}</h3>
                        <button type="button" onclick="closeBulkModal()" class="text-gray-400 hover:text-gray-600 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    
                    <div class="p-6">
                        <div class="mb-4 text-sm text-gray-600 p-4 bg-blue-50 rounded-lg border border-blue-100">
                            <strong class="font-bold text-blue-800">{{ __('Note:') }}</strong> {{ __('Emails will be sent automatically in the background. For WhatsApp, you will be given a queue to manually send each message via the WhatsApp app to comply with limits.') }}
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('Select Template (Optional)') }}</label>
                            <select id="templateSelector" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200" onchange="loadTemplate()">
                                <option value="">-- Custom Message --</option>
                                <option value="welcome">{{ __('Welcome to Zintoop') }}</option>
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
                            <textarea name="message_body" id="msgBody" rows="8" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200" placeholder="Type your message here..."></textarea>
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
        'welcome': {
            subject: 'مرحباً بك في منصة الزين Zintoop!',
            body: "مرحباً،\n\nنحن سعداء بانضمامك لمنصة الزين Zintoop، أكبر سوق لزيت الزيتون.\nيمكنك الآن استكشاف أحدث العروض والطلبات.\n\nتفضل بزيارة المنصة: https://zintoop.com\n\nفريق الزين."
        },
        'update': {
            subject: 'نشرة أخبار الزين Zintoop',
            body: "مرحباً،\n\nإليك أحدث الأخبار وتحديثات الأسعار لهذا الأسبوع في منصة الزين.\n\nتفضل بزيارة المنصة لمعرفة المزيد: https://zintoop.com/prices\n\nفريق الزين."
        },
        'deal': {
            subject: 'عروض جديدة متاحة الآن!',
            body: "مرحباً،\n\nهناك عروض جديدة لزيت الزيتون بانتظارك على منصة الزين. لا تفوت الفرصة!\n\nتصفح العروض الآن: https://zintoop.com/market\n\nفريق الزين."
        },
        'guide': {
            subject: 'دليلك الشامل لاستخدام منصة الزين Zintoop',
            body: "مرحباً بك في منصة الزين Zintoop!\n\nإليك كيف يمكنك الاستفادة القصوى من منصتنا:\n\n### 1. كيفية التسجيل\nقم بزيارة صفحة التسجيل، اختر دورك (فلاح، معصرة، ناقل...) واملأ بياناتك.\n![شرح التسجيل](https://zintoop.com/images/guide/register.png)\n\n### 2. كيفية إضافة وطلب العروض (Deals)\nيمكنك تقديم العروض وتصفح الأسعار مباشرة من لوحة التحكم الخاصة بك.\n![شرح إضافة العروض](https://zintoop.com/images/guide/submit-deals.png)\n\n### 3. كيفية المحادثة (الدردشة)\nتوفر منصة الزين نظام دردشة متكامل للتواصل المباشر مع الأعضاء.\n![شرح الدردشة](https://zintoop.com/images/guide/chat.png)\n\n### 4. تتبع الناقلين (Transporters)\nتابع مسار شحنتك بكل سهولة وتأكد من وصول زيتك بأمان.\n![شرح تتبع الشحنات](https://zintoop.com/images/guide/track-transporters.png)\n\nإذا كان لديك أي استفسار، لا تتردد في التواصل معنا.\n\nفريق الزين."
        }
    };

    const selectAllCheckbox = document.getElementById('selectAll');
    const contactCheckboxes = document.querySelectorAll('.contact-checkbox');
    const btnBulkMessage = document.getElementById('btnBulkMessage');

    function updateBulkButton() {
        const anyChecked = Array.from(contactCheckboxes).some(cb => cb.checked);
        btnBulkMessage.disabled = !anyChecked;
    }

    selectAllCheckbox.addEventListener('change', function() {
        contactCheckboxes.forEach(cb => cb.checked = this.checked);
        updateBulkButton();
    });

    contactCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkButton);
    });

    function openBulkModal() {
        document.getElementById('bulkModal').classList.remove('hidden');
    }

    function closeBulkModal() {
        document.getElementById('bulkModal').classList.add('hidden');
    }

    function loadTemplate() {
        const val = document.getElementById('templateSelector').value;
        if(val && templates[val]) {
            document.getElementById('msgSubject').value = templates[val].subject;
            document.getElementById('msgBody').value = templates[val].body;
        } else {
            document.getElementById('msgSubject').value = '';
            document.getElementById('msgBody').value = '';
        }
    }

    // AJAX form submission to handle WhatsApp queue logic
    let waQueue = [];
    let currentWaIndex = 0;
    
    document.getElementById('bulkForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const btn = document.getElementById('btnSubmitBulk');
        btn.disabled = true;
        btn.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Sending...';

        try {
            const formData = new FormData(this);
            const response = await fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json'
                }
            });
            
            const result = await response.json();
            
            if (result.success) {
                closeBulkModal();
                
                // Show success message for emails
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
