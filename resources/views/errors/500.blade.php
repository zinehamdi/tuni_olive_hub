<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ __('ltr') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 - {{ __('Internal Server Error | ZinToop') }}</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;900&family=Outfit:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (via CDN for standalone error pages to guarantee styling load independent of assets) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Cairo', 'Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Cairo', 'Outfit', sans-serif;
            background-color: #0b180c;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 overflow-hidden relative selection:bg-[#6A8F3B] selection:text-white">
    
    <!-- Premium background glowing lights -->
    <div class="absolute -right-32 -top-32 w-96 h-96 bg-[#6A8F3B]/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -left-32 -bottom-32 w-96 h-96 bg-[#C8A356]/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative z-10 w-full max-w-xl text-center">
        <!-- Brand Logo Header -->
        <div class="flex justify-center items-center gap-2 mb-8">
            <span class="text-3xl font-black text-white tracking-wider">Zin<span class="text-[#6A8F3B]">Toop</span></span>
        </div>

        <!-- 500 Glassmorphism Card -->
        <div class="bg-white/5 border border-white/10 rounded-3xl p-8 md:p-12 shadow-2xl backdrop-blur-md relative overflow-hidden">
            <!-- Inner light decoration -->
            <div class="absolute -right-10 -bottom-10 w-44 h-44 bg-[#6A8F3B]/10 rounded-full blur-3xl"></div>
            
            <!-- Error Code Banner -->
            <div class="inline-block px-4 py-1.5 rounded-full bg-rose-500/10 border border-rose-500/25 text-rose-400 text-sm font-black tracking-widest uppercase mb-6">
                🚨 {{ __('Error 500') }}
            </div>

            <!-- Gigantic Status Number -->
            <h1 class="text-7xl md:text-9xl font-black text-transparent bg-clip-text bg-gradient-to-br from-white via-white/80 to-white/20 leading-none mb-6">
                500
            </h1>

            <!-- Titles & Explanations -->
            <div class="space-y-4 mb-10">
                <h2 class="text-2xl md:text-3xl font-black text-[#a8d060]">
                    {{ __('Server Error') }}
                </h2>
                <p class="text-white/70 text-sm md:text-base leading-relaxed">
                    {{ __('Apologies, an unexpected issue occurred while processing your request. This has been logged, and the ZinToop engineering team will resolve it shortly.') }}
                </p>
            </div>

            <!-- Action Button -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="{{ url('/') }}" class="w-full sm:w-auto px-8 py-4 rounded-xl bg-gradient-to-r from-[#6A8F3B] to-[#5a7a2f] hover:from-[#5a7a2f] hover:to-[#4a6425] text-white font-bold text-base shadow-lg shadow-[#6A8F3B]/25 hover:shadow-xl hover:scale-[1.02] transition-all duration-200 flex items-center justify-center gap-2">
                    🏠 {{ __('Return Home') }}
                </a>
                @auth
                <a href="{{ route('dashboard') }}" class="w-full sm:w-auto px-8 py-4 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 text-white font-bold text-base transition duration-200 flex items-center justify-center gap-2">
                    📊 {{ __('Dashboard') }}
                </a>
                @endauth
            </div>
        </div>

        <!-- Footer -->
        <p class="text-white/40 text-xs mt-8">
            © {{ now()->year }} ZinToop. {{ __('All Rights Reserved.') }}
        </p>
    </div>
</body>
</html>
