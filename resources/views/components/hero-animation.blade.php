<div class="relative flex flex-col items-center justify-center py-12 px-4 text-center space-y-8 bg-transparent w-full max-w-4xl mx-auto">
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
