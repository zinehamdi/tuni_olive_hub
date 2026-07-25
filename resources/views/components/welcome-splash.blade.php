{{-- ─── ZinToop Premium Welcoming Splash Screen Cover (Zero-Flash & 100% Mobile Responsive) ─── --}}
<div id="zintoop-welcome-splash"
     x-data="{ 
        dismiss() {
            const el = document.getElementById('zintoop-welcome-splash');
            if (el) {
                el.classList.add('opacity-0', 'scale-105', 'pointer-events-none', 'transition-all', 'duration-700');
                setTimeout(() => el.remove(), 750);
            }
            document.body.style.overflow = '';
            sessionStorage.setItem('zintoop_splash_seen', 'true');
        }
     }"
     x-init="
        document.body.style.overflow = 'hidden';
        setTimeout(() => dismiss(), 2600);
     "
     class="fixed inset-0 z-[999999] flex items-center justify-center bg-[#0C1A0F] overflow-hidden select-none w-full h-full min-h-screen">

    <script>
        if (sessionStorage.getItem('zintoop_splash_seen')) {
            document.getElementById('zintoop-welcome-splash').style.display = 'none';
        }
    </script>

    <!-- Background Ambient Glows & Radial Gold Light -->
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(200,163,86,0.25)_0%,transparent_70%)] pointer-events-none"></div>
    <div class="absolute -top-24 -left-24 sm:-top-32 sm:-left-32 w-72 h-72 sm:w-96 sm:h-96 rounded-full bg-[#6A8F3B]/25 blur-3xl pointer-events-none animate-pulse"></div>
    <div class="absolute -bottom-24 -right-24 sm:-bottom-32 sm:-right-32 w-72 h-72 sm:w-96 sm:h-96 rounded-full bg-[#C8A356]/25 blur-3xl pointer-events-none animate-pulse" style="animation-delay: 1s;"></div>
    
    <!-- Skip Button (Top Corner) -->
    <button @click="dismiss()"
            class="absolute top-4 left-4 sm:top-6 sm:left-6 px-3.5 py-1.5 sm:px-4 sm:py-2 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 text-white/90 hover:text-white rounded-full text-[11px] sm:text-xs font-bold transition shadow-xl z-50 flex items-center gap-1.5">
        <span>تخطي</span>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>
    </button>

    <!-- Center Card & Animated Content (Mobile Optimized) -->
    <div class="relative z-10 max-w-lg w-full px-5 sm:px-6 text-center flex flex-col items-center animate-splash-appear">
        
        <!-- Animated Logo Container (Perfectly Circular) -->
        <div class="relative mb-5 sm:mb-6 group">
            <!-- Pulsing Glow Ring -->
            <div class="absolute -inset-3 sm:-inset-4 bg-gradient-to-r from-[#6A8F3B] via-[#C8A356] to-[#6A8F3B] rounded-full blur-xl opacity-70 animate-tilt"></div>
            
            <div class="relative w-28 h-28 sm:w-36 sm:h-36 rounded-full bg-gradient-to-br from-[#142E18] to-[#0B170C] p-2 border-2 border-[#C8A356]/80 shadow-2xl flex items-center justify-center overflow-hidden">
                <img src="{{ asset('images/zintoop-logo.png') }}" 
                     alt="ZinToop Tunisian Olive Oil Platform" 
                     class="w-full h-full object-contain p-1.5 rounded-full drop-shadow-[0_10px_20px_rgba(200,163,86,0.5)] animate-logo-float">
            </div>
        </div>

        <!-- English Sub-Title -->
        <p class="text-[10px] sm:text-xs font-black uppercase tracking-[0.2em] sm:tracking-[0.25em] text-[#C8A356] mb-1.5 sm:mb-2 animate-fade-in-down">
            Zin Tunisian Olive Oil Platform
        </p>

        <!-- Main Brand Title -->
        <h1 class="text-4xl sm:text-6xl font-black tracking-tight text-white mb-2 sm:mb-3 font-sans leading-none">
            <span class="bg-gradient-to-r from-white via-[#F5E5C0] to-[#C8A356] text-transparent bg-clip-text drop-shadow-md">ZinToop</span>
        </h1>

        <!-- Arabic Main Title -->
        <h2 class="text-xl sm:text-3xl font-extrabold text-white leading-snug sm:leading-relaxed mb-3 sm:mb-4 font-arabic">
            منصة الزين لزيت الزيتون التونسي 🇹🇳
        </h2>

        <!-- Decorative Line -->
        <div class="w-20 sm:w-28 h-1 bg-gradient-to-r from-transparent via-[#C8A356] to-transparent rounded-full mb-3 sm:mb-4"></div>

        <!-- Tagline -->
        <p class="text-xs sm:text-base text-gray-300 font-medium opacity-90 max-w-sm sm:max-w-md leading-relaxed">
            السوق الأول لمنتجي ومشتري زيت الزيتون والزيتون في تونس والعالم
        </p>

        <!-- Loading Progress Line -->
        <div class="w-40 sm:w-48 h-1.5 bg-white/10 rounded-full mt-6 sm:mt-8 overflow-hidden relative">
            <div class="h-full bg-gradient-to-r from-[#6A8F3B] via-[#C8A356] to-[#6A8F3B] rounded-full animate-splash-progress"></div>
        </div>
    </div>
</div>

<style>
    @keyframes splash-appear {
        0% { opacity: 0; transform: scale(0.9) translateY(12px); }
        100% { opacity: 1; transform: scale(1) translateY(0); }
    }
    @keyframes logo-float {
        0%, 100% { transform: translateY(0px) scale(1); }
        50% { transform: translateY(-5px) scale(1.04); }
    }
    @keyframes splash-progress {
        0% { width: 0%; }
        100% { width: 100%; }
    }
    .animate-splash-appear {
        animation: splash-appear 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    .animate-logo-float {
        animation: logo-float 2.2s ease-in-out infinite;
    }
    .animate-splash-progress {
        animation: splash-progress 2.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }
</style>
