@guest
<div id="leadCaptureModal" class="fixed inset-0 z-[10001] flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm transition-all duration-300 opacity-0 pointer-events-none">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md border border-gray-100 overflow-hidden transform transition-all duration-300 scale-95 opacity-0 relative text-right" id="leadCaptureContent" dir="{{ __('ltr') }}">
        
        <!-- Header Banner -->
        <div class="bg-gradient-to-r from-[#111827] via-[#0F291E] to-[#6A8F3B] p-6 text-white text-center relative overflow-hidden">
            <button id="closeLeadModal" class="absolute top-4 left-4 text-white/80 hover:text-white transition bg-black/20 hover:bg-black/40 rounded-full w-8 h-8 flex items-center justify-center z-10 cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-md px-3.5 py-1 rounded-full border border-white/20 text-xs font-bold text-emerald-200 mb-3">
                🫒 {{ __('ZinToop Platform') }}
            </div>
            
            <h2 class="text-xl font-black leading-snug">
                {{ __("Join Tunisia's Premier Olive Oil Marketplace") }}
            </h2>
        </div>

        <div class="p-6 sm:p-7">
            <p class="text-xs text-gray-600 mb-5 leading-relaxed font-medium text-center">
                {{ __('Create your free account in seconds to connect directly with farmers, mills, and transporters without commissions.') }}
            </p>

            <!-- Value Highlights -->
            <div class="bg-emerald-50/70 border border-emerald-100 rounded-2xl p-4 mb-6 space-y-2 text-xs text-emerald-900 font-semibold">
                <div class="flex items-center gap-2">
                    <span class="text-base">✅</span>
                    <span>{{ __('Direct WhatsApp & phone calls with verified users') }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-base">✅</span>
                    <span>{{ __('Explore oil mills & agricultural services directory') }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-base">✅</span>
                    <span>{{ __('Real-time local & international olive oil market prices') }}</span>
                </div>
            </div>

            <!-- Primary 1-Click Google Sign Up -->
            <div class="space-y-3">
                <a href="{{ route('auth.google') }}" class="w-full py-3.5 bg-white border-2 border-gray-200 hover:border-gray-300 hover:bg-gray-50 text-gray-800 font-black rounded-2xl shadow-md transition duration-200 text-sm flex items-center justify-center gap-3 group">
                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                    </svg>
                    <span>{{ __('Sign up instantly with Google / Gmail') }}</span>
                </a>
                <p class="text-[11px] text-emerald-700 font-bold text-center -mt-1">
                    ⚡ {{ __('Fastest & Easiest - 1 click without passwords') }}
                </p>

                <!-- Secondary Email Registration -->
                <a href="{{ route('register') }}" class="w-full py-3 bg-[#6A8F3B] hover:bg-[#5a7a2f] text-white font-bold rounded-xl transition duration-200 text-xs flex items-center justify-center gap-2 shadow-sm">
                    ✉️ {{ __('Register with Email') }}
                </a>
            </div>

            <!-- Login Link -->
            <div class="mt-5 pt-4 border-t border-gray-100 text-center">
                <span class="text-xs text-gray-500">{{ __('Already have an account?') }}</span>
                <a href="{{ route('login') }}" class="text-xs font-bold text-[#6A8F3B] hover:underline mr-1">
                    🔐 {{ __('Log In') }}
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('leadCaptureModal');
    const content = document.getElementById('leadCaptureContent');
    const closeBtn = document.getElementById('closeLeadModal');
    
    if (!modal || !content) return;

    // Check if user dismissed prompt recently (remember for 7 days)
    const dismissedUntil = localStorage.getItem('zintoop_register_prompt_dismissed');
    const now = new Date().getTime();
    
    if (dismissedUntil && now < parseInt(dismissedUntil)) {
        return;
    }

    // Do not show on auth pages
    const path = window.location.pathname;
    if (path.includes('/register') || path.includes('/login') || path.includes('/onboarding')) {
        return;
    }

    let modalTriggered = false;

    const showModal = () => {
        if (modalTriggered) return;
        modalTriggered = true;
        
        modal.classList.remove('opacity-0', 'pointer-events-none');
        content.classList.remove('scale-95', 'opacity-0');
    };

    const hideModal = () => {
        content.classList.add('scale-95', 'opacity-0');
        modal.classList.add('opacity-0', 'pointer-events-none');
        
        // Remember dismiss for 7 days
        const sevenDays = new Date().getTime() + (7 * 24 * 60 * 60 * 1000);
        localStorage.setItem('zintoop_register_prompt_dismissed', sevenDays);
    };

    if (closeBtn) {
        closeBtn.addEventListener('click', hideModal);
    }

    // 1. Time trigger: 18 seconds stay
    const timeTrigger = setTimeout(showModal, 18000);

    // 2. Scroll trigger: 40% page scroll
    window.addEventListener('scroll', () => {
        if (modalTriggered) return;
        const scrollPercent = (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100;
        if (scrollPercent > 40) {
            showModal();
            clearTimeout(timeTrigger);
        }
    });
});
</script>
@endguest
