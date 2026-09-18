<x-guest-layout>
    @section('title', '🫒 تأكيد رقم الواتساب | ZinToop')

    <div dir="rtl" class="mx-auto max-w-xl bg-gradient-to-br from-[#FAF8F5] via-white to-[#F2F7EE] rounded-3xl shadow-2xl border border-emerald-100/80 p-6 sm:p-10 relative overflow-hidden text-right">
        <!-- Background Ambient Glow -->
        <div class="absolute -top-20 -right-20 w-60 h-60 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-20 -left-20 w-60 h-60 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Success celebration overlay (hidden until verified) -->
        <div id="verified-overlay" class="hidden absolute inset-0 bg-white/95 backdrop-blur-md z-30 flex flex-col items-center justify-center p-8 text-center animate-fade-in">
            <div class="w-20 h-20 bg-gradient-to-tr from-emerald-600 to-green-400 rounded-full flex items-center justify-center text-white text-4xl shadow-xl shadow-emerald-500/30 mb-6 animate-bounce">
                ✓
            </div>
            <h3 class="text-2xl font-black text-gray-900 mb-2">🎉 تم تفعيل حسابك بنجاح!</h3>
            <p class="text-emerald-800 font-semibold mb-4">أهلاً بك معنا سي {{ $user->name }} في شبكة النقل الفلاحي التونسي.</p>
            <div class="flex items-center gap-2 text-sm text-gray-500 font-medium">
                <span class="inline-block w-3 h-3 bg-emerald-500 rounded-full animate-ping"></span>
                <span>جاري تحويلك إلى لوحة التحكم فوراً...</span>
            </div>
        </div>

        <!-- Header Icon & Status -->
        <div class="flex flex-col items-center text-center mb-8 relative">
            <div class="relative mb-5">
                <!-- Outer Pulsing Ring -->
                <div class="w-24 h-24 bg-gradient-to-tr from-[#25D366]/20 to-[#128C7E]/10 rounded-full flex items-center justify-center animate-pulse">
                    <div class="w-20 h-20 bg-gradient-to-tr from-[#25D366] to-[#128C7E] rounded-full flex items-center justify-center text-white text-3xl shadow-xl shadow-[#25D366]/30">
                        <svg class="w-10 h-10 fill-current" viewBox="0 0 24 24">
                            <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.073.376-.044.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564c.173.087.289.13.332.203.043.072.043.419-.101.824z"/>
                        </svg>
                    </div>
                </div>
                <!-- Badge Check -->
                <span class="absolute -bottom-1 -left-1 bg-amber-500 text-white p-1.5 rounded-full shadow-md text-xs">
                    🫒
                </span>
            </div>

            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-300 mb-3 shadow-sm">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                تأكيد حساب الناقل المعتمد
            </span>

            <h2 class="text-2xl sm:text-3xl font-black text-gray-900 leading-tight mb-2">
                🫒 خطوة أخيرة لإكمال التسجيل
            </h2>
            <p class="text-gray-600 text-sm font-medium">
                شبكة النقل الفلاحي التونسي | <span class="text-emerald-700 font-bold">ZinToop</span>
            </p>
        </div>

        <!-- Main Alert Box -->
        <div class="bg-gradient-to-r from-emerald-50 via-green-50 to-emerald-100/60 border-2 border-emerald-300/80 rounded-2xl p-5 sm:p-6 mb-6 shadow-md relative overflow-hidden">
            <div class="flex items-start gap-4">
                <div class="text-3xl sm:text-4xl flex-shrink-0 mt-0.5 select-none">
                    📩
                </div>
                <div class="space-y-3">
                    <p class="text-gray-900 text-base sm:text-lg font-bold leading-relaxed">
                        تم إرسال رسالة تفعيل إلى حسابك في <span class="text-[#128C7E] font-black underline decoration-wavy">WhatsApp</span> على الرقم:
                    </p>
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-white rounded-xl border border-emerald-400/80 shadow-sm text-emerald-900 font-mono font-black text-lg tracking-wider" dir="ltr">
                        <svg class="w-5 h-5 text-[#25D366]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771z"/>
                        </svg>
                        <span>{{ $user->phone }}</span>
                    </div>
                    <p class="text-gray-700 text-sm sm:text-base font-semibold leading-relaxed pt-1">
                        يرجى فتح تطبيق <strong class="text-[#128C7E]">WhatsApp</strong> والضغط على زر <span class="inline-block bg-emerald-700 text-white text-xs sm:text-sm px-2.5 py-1 rounded-lg font-bold shadow-sm">[✅ تفعيل الكورسات]</span> لإكمال التسجيل ودخول المنصة فوراً.
                    </p>
                </div>
            </div>
        </div>

        <!-- Live Polling Status Indicator -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6 shadow-sm flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="relative flex h-3.5 w-3.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-emerald-600"></span>
                </span>
                <div>
                    <span class="text-xs sm:text-sm font-bold text-gray-800 block">في انتظار تأكيدك في الواتساب...</span>
                    <span class="text-[11px] text-gray-500 block">الصفحة تتحقق تلقائياً وستفتح لك الداشبورد فور الضغط.</span>
                </div>
            </div>
            <div class="flex-shrink-0">
                <div class="animate-spin text-emerald-600 text-xl font-bold">
                    ⟳
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="space-y-3 pt-2">
            <!-- Open WhatsApp Direct Button -->
            <a href="https://wa.me/21625777926?text={{ urlencode('تفعيل') }}" target="_blank" class="w-full min-h-[50px] flex items-center justify-center gap-3 px-6 py-3.5 bg-gradient-to-r from-[#25D366] to-[#128C7E] text-white text-base font-black rounded-2xl shadow-xl shadow-[#25D366]/25 hover:shadow-2xl hover:scale-[1.02] active:scale-95 transition-all text-center">
                <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                    <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.073.376-.044.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564c.173.087.289.13.332.203.043.072.043.419-.101.824z"/>
                </svg>
                <span>📲 فتح تطبيق WhatsApp الآن</span>
            </a>

            <!-- Resend Button -->
            <button id="resend-btn" onclick="resendWhatsAppMessage()" type="button" class="w-full min-h-[44px] flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-white border border-gray-300 text-gray-700 font-bold hover:bg-gray-50 hover:border-gray-400 active:scale-95 transition shadow-sm text-sm">
                <span id="resend-spinner" class="hidden animate-spin">⏳</span>
                <span id="resend-text">🔄 لم تصلك الرسالة؟ إعادة إرسال رسالة التفعيل</span>
            </button>
            <div id="resend-feedback" class="hidden text-center text-xs font-bold py-1"></div>
        </div>

        <!-- Footer / Logout / Help -->
        <div class="mt-8 pt-6 border-t border-gray-200 flex flex-wrap items-center justify-between gap-4 text-xs text-gray-500">
            <div class="flex items-center gap-2">
                <span>هل سجلت برقم غير صحيح؟</span>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-rose-600 font-bold underline hover:text-rose-800 transition">
                        تسجيل الخروج وإعادة التسجيل
                    </button>
                </form>
            </div>
            <div>
                <a href="https://wa.me/21625777926" target="_blank" class="text-emerald-700 font-bold hover:underline flex items-center gap-1">
                    <span>💬 مساعدة فنية</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Live Polling Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let isVerified = false;
            const checkUrl = "{{ route('carrier.verify.check') }}";
            const overlay = document.getElementById('verified-overlay');

            function pollVerificationStatus() {
                if (isVerified) return;

                fetch(checkUrl, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data && data.verified === true) {
                        isVerified = true;
                        if (overlay) {
                            overlay.classList.remove('hidden');
                        }
                        setTimeout(() => {
                            window.location.href = data.redirect || "{{ route('dashboard') }}";
                        }, 1200);
                    }
                })
                .catch(err => console.debug('Status poll error:', err));
            }

            // Poll every 3 seconds
            const pollInterval = setInterval(pollVerificationStatus, 3000);

            // Also check immediately when window gains focus
            window.addEventListener('focus', pollVerificationStatus);
        });

        function resendWhatsAppMessage() {
            const btn = document.getElementById('resend-btn');
            const spinner = document.getElementById('resend-spinner');
            const text = document.getElementById('resend-text');
            const feedback = document.getElementById('resend-feedback');
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            btn.disabled = true;
            spinner.classList.remove('hidden');
            text.textContent = 'جاري الإرسال...';
            feedback.classList.add('hidden');

            fetch("{{ route('carrier.verify.resend') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf || '',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                feedback.classList.remove('hidden');
                if (data.success) {
                    feedback.className = 'text-center text-xs font-bold py-1 text-emerald-700';
                    feedback.textContent = '✅ ' + (data.message || 'تمت إعادة إرسال رسالة التفعيل إلى الواتساب بنجاح.');
                } else {
                    feedback.className = 'text-center text-xs font-bold py-1 text-rose-600';
                    feedback.textContent = '⚠️ ' + (data.message || 'تعذر الإرسال، حاول مجدداً.');
                }
            })
            .catch(() => {
                feedback.classList.remove('hidden');
                feedback.className = 'text-center text-xs font-bold py-1 text-rose-600';
                feedback.textContent = '⚠️ حدث خطأ في الاتصال، يرجى المحاولة لاحقاً.';
            })
            .finally(() => {
                spinner.classList.add('hidden');
                text.textContent = '🔄 لم تصلك الرسالة؟ إعادة إرسال رسالة التفعيل';
                setTimeout(() => { btn.disabled = false; }, 4000);
            });
        }
    </script>
</x-guest-layout>
