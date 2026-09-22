<div class="relative flex flex-col items-center justify-center py-10 px-4 text-center space-y-6 bg-transparent w-full max-w-4xl mx-auto">
    <!-- Royal Green Glassmorphic One-Side Rounded Pill: Palestine Solidarity -->
    <div class="inline-flex items-center gap-2.5 px-5 py-2 rounded-s-full rounded-e-md bg-gradient-to-r from-[#032e16]/90 via-[#074e24]/85 to-[#032e16]/90 backdrop-blur-md border border-emerald-400/35 text-white shadow-[0_8px_25px_rgba(4,46,22,0.45)] hover:border-emerald-300/60 transition-all duration-300 transform hover:scale-[1.02]">
        <span class="inline-flex items-center justify-center text-base filter drop-shadow">🇵🇸</span>
        <span class="font-black text-emerald-200 tracking-wide text-xs sm:text-sm">نحن نقف مع فلسطين</span>
        <span class="text-emerald-400/40">•</span>
        <span class="text-white/95 font-semibold text-[11px] sm:text-xs">We Stand with Palestine</span>
        <span class="text-emerald-400/40 hidden md:inline">•</span>
        <span class="text-white/85 font-medium text-[11px] sm:text-xs hidden md:inline">Nous sommes avec la Palestine</span>
        <span class="text-emerald-300 text-xs">🌿</span>
    </div>

    <!-- English Slogan -->
    <div class="animate-fade-in">
        <p class="text-white font-black text-lg md:text-2xl uppercase tracking-[0.25em] drop-shadow-lg">
            Zin Tunisian Olive Oil Platform
        </p>
    </div>

    <!-- Main Brand: ZinToop -->
    <div class="relative group">
        <h1 id="zintoop-brand" class="text-7xl md:text-9xl font-black tracking-tighter text-transparent bg-clip-text bg-gradient-to-r from-[#C8A356] via-[#FFF9E0] to-[#C8A356] bg-[length:200%_auto] animate-shine drop-shadow-2xl">
            ZinToop
        </h1>
        <!-- Subtle Glow -->
        <div class="absolute -inset-8 bg-[#C8A356]/20 blur-3xl rounded-full -z-10 animate-pulse"></div>
    </div>

    <!-- Arabic Slogan -->
    <div class="animate-fade-in-delayed">
        <p class="text-[#C8A356] font-black text-3xl md:text-5xl drop-shadow-lg" dir="rtl">
            منصة الزين لزيت الزيتون التونسي
        </p>
    </div>
</div>

<style>
    @keyframes shine {
        0% { background-position: 200% center; }
        100% { background-position: -200% center; }
    }

    .animate-shine {
        animation: shine 4s linear infinite;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .animate-fade-in {
        animation: fadeIn 1s ease-out forwards;
    }

    .animate-fade-in-delayed {
        animation: fadeIn 1.5s ease-out forwards;
        opacity: 0;
    }
</style>
