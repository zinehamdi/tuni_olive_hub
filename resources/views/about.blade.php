@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-amber-50 via-orange-50 to-amber-100" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <!-- Hero Section -->
    <div class="relative bg-gradient-to-r from-[#6A8F3B] via-[#7a9f4b] to-[#C8A356] text-white py-20">
        <div class="absolute inset-0 bg-black/20"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-4">
                {{ __(app()->getLocale() === 'ar' ? 'brand.name_ar' : 'brand.name_latin') }}
            </h1>
            <p class="text-xl md:text-2xl text-white/90 max-w-3xl mx-auto">
                {{ __('Tunisian Olive Oil Platform - Connecting Tunisia\'s Olive Industry Through Innovation') }}
            </p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        
        <!-- Founder Section -->
        <div class="bg-gradient-to-br from-white to-amber-50 rounded-3xl shadow-2xl overflow-hidden mb-12 transform transition-all duration-300 hover:shadow-3xl hover:scale-[1.02]">
            <div class="bg-gradient-to-r from-[#6A8F3B] to-[#C8A356] px-8 py-6">
                <h2 class="text-3xl md:text-4xl font-bold text-white text-center">
                    {{ __('Meet the Founder') }}
                </h2>
            </div>

            <div class="p-8 md:p-12">
                <div class="grid md:grid-cols-3 gap-8 items-start">
                    <!-- Profile Image -->
                    <div class="md:col-span-1">
                        <div class="relative group">
                            <div class="w-48 h-48 mx-auto rounded-full overflow-hidden shadow-2xl ring-4 ring-[#6A8F3B] ring-offset-4 transform transition-all duration-500 group-hover:scale-110 group-hover:rotate-3">
                                <img src="/images/profili2.jpg" alt="Hamdi Ezzine" class="w-full h-full object-cover object-top">
                            </div>
                            <div class="absolute -bottom-4 left-1/2 transform -translate-x-1/2 bg-gradient-to-r from-amber-500 to-orange-600 text-white px-6 py-2 rounded-full font-bold text-sm shadow-lg transition-all duration-300 hover:scale-110">
                                Co-Founder & CEO
                            </div>
                        </div>
                    </div>

                    <!-- Profile Details -->
                    <div class="md:col-span-2 space-y-6">
                        <div class="bg-white rounded-2xl p-6 shadow-lg border-2 border-gray-100 transform transition-all duration-300 hover:scale-105 hover:shadow-2xl hover:border-[#6A8F3B]">
                            <h3 class="text-3xl font-bold text-gray-900 mb-2">Hamdi Ezzine</h3>
                            <p class="text-xl text-[#6A8F3B] font-semibold mb-4">ZINDEV - Co-Founder</p>
                            
                            <!-- Contact Info -->
                            <div class="flex flex-wrap gap-4 mb-6">
                                <a href="tel:+21625777926" class="flex items-center gap-2 px-4 py-2 bg-green-100 text-green-800 rounded-full hover:bg-green-200 transition-all duration-300 transform hover:scale-110 hover:shadow-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    <span class="font-medium">+216 25 777 926</span>
                                </a>
                                
                                <a href="mailto:Zinehamdi8@gmail.com" class="flex items-center gap-2 px-4 py-2 bg-blue-100 text-blue-800 rounded-full hover:bg-blue-200 transition-all duration-300 transform hover:scale-110 hover:shadow-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    <span class="font-medium">Zinehamdi8@gmail.com</span>
                                </a>
                                
                                <div class="flex items-center gap-2 px-4 py-2 bg-amber-100 text-amber-800 rounded-full transition-all duration-300 transform hover:scale-110 hover:shadow-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span class="font-medium">Kairouan, Tunisia</span>
                                </div>
                            </div>
                        </div>

                        <!-- Professional Summary -->
                        <div class="bg-gradient-to-br from-amber-50 to-orange-50 border-2 border-amber-200 rounded-2xl p-6 shadow-lg transform transition-all duration-300 hover:scale-105 hover:shadow-2xl hover:border-amber-400">
                            <h4 class="text-xl font-bold text-gray-900 mb-3 flex items-center gap-2">
                                <svg class="w-6 h-6 text-[#6A8F3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                {{ __('Professional Summary') }}
                            </h4>
                            <p class="text-gray-700 leading-relaxed">
                                @if(app()->getLocale() === 'ar')
                                    مطور ويب متكامل و Scrum Master & Product Owner معتمد مع أساس قوي في PHP و Laravel و Angular وإدارة المشاريع الرشيقة. ماهر في بناء تطبيقات ويب قابلة للتطوير وآمنة، وتطبيق منهجيات إدارة المشاريع، والاستفادة من المعرفة التجارية لتقديم قيمة. خبرة في التطوير المستقل وإدارة العمليات، مع قدرة مثبتة على التكيف والتعلم السريع ودفع المشاريع نحو النجاح.
                                @elseif(app()->getLocale() === 'fr')
                                    Développeur Full Stack et Scrum Master & Product Owner certifié avec une solide base en PHP, Laravel, Angular et gestion de projet Agile. Compétent dans la création d'applications web évolutives et sécurisées, l'application de méthodologies de gestion de projet et l'exploitation des connaissances commerciales pour offrir de la valeur. Expérimenté en développement freelance et gestion des opérations, avec une capacité prouvée à s'adapter, apprendre rapidement et mener des projets vers le succès.
                                @else
                                    Results-driven Full Stack Web Developer and Certified Scrum Master & Product Owner with a strong foundation in PHP, Laravel, Angular, and Agile project management. Skilled in building scalable, secure web applications, applying project management methodologies, and leveraging business knowledge to deliver value. Experienced in freelance development and operations management, with a proven ability to adapt, learn fast, and drive projects to success.
                                @endif
                            </p>
                        </div>

                        <!-- Skills & Expertise -->
                        <div class="bg-white rounded-2xl p-6 shadow-lg border-2 border-gray-100 transform transition-all duration-300 hover:scale-105 hover:shadow-2xl hover:border-blue-300">
                            <h4 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <svg class="w-6 h-6 text-[#6A8F3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                </svg>
                                {{ __('Core Competencies') }}
                            </h4>
                            <div class="grid md:grid-cols-2 gap-4">
                                <div class="flex items-start gap-3 bg-gradient-to-br from-green-50 to-emerald-50 p-4 rounded-xl border-2 border-green-200 shadow transform transition-all duration-300 hover:scale-110 hover:shadow-xl hover:border-green-400">
                                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h5 class="font-bold text-gray-900">{{ __('Fast Learner') }}</h5>
                                        <p class="text-sm text-gray-600">{{ __('Hands-on approach with quick adaptation') }}</p>
                                    </div>
                                </div>

                                <div class="flex items-start gap-3 bg-gradient-to-br from-blue-50 to-cyan-50 p-4 rounded-xl border-2 border-blue-200 shadow transform transition-all duration-300 hover:scale-110 hover:shadow-xl hover:border-blue-400">
                                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h5 class="font-bold text-gray-900">{{ __('Problem Solver') }}</h5>
                                        <p class="text-sm text-gray-600">{{ __('Strong debugging and analytical skills') }}</p>
                                    </div>
                                </div>

                                <div class="flex items-start gap-3 bg-gradient-to-br from-amber-50 to-orange-50 p-4 rounded-xl border-2 border-amber-200 shadow transform transition-all duration-300 hover:scale-110 hover:shadow-xl hover:border-amber-400">
                                    <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h5 class="font-bold text-gray-900">{{ __('Bridge Builder') }}</h5>
                                        <p class="text-sm text-gray-600">{{ __('Connect technical and business needs') }}</p>
                                    </div>
                                </div>

                                <div class="flex items-start gap-3 bg-gradient-to-br from-purple-50 to-pink-50 p-4 rounded-xl border-2 border-purple-200 shadow transform transition-all duration-300 hover:scale-110 hover:shadow-xl hover:border-purple-400">
                                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h5 class="font-bold text-gray-900">{{ __('Entrepreneurial') }}</h5>
                                        <p class="text-sm text-gray-600">{{ __('Value-focused delivery mindset') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Education & Certifications -->
                        <div class="bg-gradient-to-br from-white to-purple-50 border-2 border-purple-200 rounded-2xl p-6 shadow-lg transform transition-all duration-300 hover:scale-105 hover:shadow-2xl hover:border-purple-400">
                            <h4 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <svg class="w-6 h-6 text-[#6A8F3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                {{ __('Education & Training') }}
                            </h4>
                            <div class="space-y-3">
                                <div class="flex items-start gap-3 p-3 bg-white rounded-xl transform transition-all duration-300 hover:scale-105 hover:shadow-md">
                                    <div class="w-2 h-2 bg-[#6A8F3B] rounded-full mt-2 flex-shrink-0"></div>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ __('Baccalaureate in Experimental Sciences') }}</p>
                                        <p class="text-sm text-gray-600">Tunisia</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3 p-3 bg-white rounded-xl transform transition-all duration-300 hover:scale-105 hover:shadow-md">
                                    <div class="w-2 h-2 bg-[#6A8F3B] rounded-full mt-2 flex-shrink-0"></div>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ __('Business English & Telecommunications') }}</p>
                                        <p class="text-sm text-gray-600">{{ __('1 Year Program') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3 p-3 bg-white rounded-xl transform transition-all duration-300 hover:scale-105 hover:shadow-md">
                                    <div class="w-2 h-2 bg-[#C8A356] rounded-full mt-2 flex-shrink-0"></div>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ __('PHP Development Intensive') }}</p>
                                        <p class="text-sm text-gray-600">{{ __('320 Hours Specialized Training') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3 p-3 bg-white rounded-xl transform transition-all duration-300 hover:scale-105 hover:shadow-md">
                                    <div class="w-2 h-2 bg-[#C8A356] rounded-full mt-2 flex-shrink-0"></div>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ __('Certified Scrum Master & Product Owner') }}</p>
                                        <p class="text-sm text-gray-600">{{ __('Agile Project Management') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Platform Overview -->
        <div class="bg-white rounded-3xl shadow-2xl p-8 md:p-12 mb-12 transform transition-all duration-300 hover:scale-[1.02] hover:shadow-3xl">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    {{ __('Our Platform') }}
                </h2>
                <div class="w-24 h-1 bg-gradient-to-r from-[#6A8F3B] to-[#C8A356] mx-auto mb-6"></div>
                <p class="text-lg text-gray-700 max-w-3xl mx-auto leading-relaxed">
                    @if(app()->getLocale() === 'ar')
                        منصة تونس لزيت الزيتون (TOOP) هي سوق إلكتروني شامل يربط جميع أطراف سلسلة إنتاج الزيتون في تونس - من المزارعين إلى المعاصر، من الناقلين إلى المعبئين، ومن التجار إلى المستهلكين.
                    @elseif(app()->getLocale() === 'fr')
                        La Plateforme Tunisienne de l'Huile d'Olive (TOOP) est une plateforme complète qui connecte tous les acteurs de la chaîne de production d'olives en Tunisie - des agriculteurs aux moulins, des transporteurs aux conditionneurs, et des commerçants aux consommateurs.
                    @else
                        Tunisian Olive Oil Platform (TOOP) is a comprehensive marketplace connecting all stakeholders in Tunisia's olive production chain - from farmers to mills, carriers to packers, and traders to consumers.
                    @endif
                </p>
            </div>

            <!-- Features Grid -->
            <div class="grid md:grid-cols-3 gap-8 mb-12">
                <div class="text-center p-6 bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl border-2 border-green-200 shadow-lg transform transition-all duration-300 hover:scale-110 hover:shadow-2xl hover:border-green-400">
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4 transform transition-all duration-300 hover:rotate-12">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('Connected Community') }}</h3>
                    <p class="text-gray-700">{{ __('Unite all stakeholders in one platform') }}</p>
                </div>

                <div class="text-center p-6 bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl border-2 border-blue-200 shadow-lg transform transition-all duration-300 hover:scale-110 hover:shadow-2xl hover:border-blue-400">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-full flex items-center justify-center mx-auto mb-4 transform transition-all duration-300 hover:rotate-12">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('Trusted & Secure') }}</h3>
                    <p class="text-gray-700">{{ __('Verification system and secure transactions') }}</p>
                </div>

                <div class="text-center p-6 bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl border-2 border-amber-200 shadow-lg transform transition-all duration-300 hover:scale-110 hover:shadow-2xl hover:border-amber-400">
                    <div class="w-16 h-16 bg-gradient-to-br from-amber-500 to-orange-600 rounded-full flex items-center justify-center mx-auto mb-4 transform transition-all duration-300 hover:rotate-12">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('Real-Time Data') }}</h3>
                    <p class="text-gray-700">{{ __('Live prices and market information') }}</p>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="bg-gradient-to-r from-[#6A8F3B] to-[#C8A356] rounded-3xl shadow-2xl p-8 md:p-12 text-center text-white transform transition-all duration-300 hover:scale-105 hover:shadow-3xl">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">{{ __('Join Our Growing Community') }}</h2>
            <p class="text-xl text-white/90 mb-8 max-w-2xl mx-auto">
                {{ __('Be part of the digital transformation of Tunisia\'s olive industry') }}
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ route('register') }}" class="px-8 py-4 bg-white text-[#6A8F3B] font-bold rounded-full hover:shadow-2xl transform hover:scale-110 transition-all duration-300 hover:bg-amber-50">
                    {{ __('Get Started') }}
                </a>
                <a href="mailto:Zinehamdi8@gmail.com" class="px-8 py-4 bg-white/10 backdrop-blur-sm text-white font-bold rounded-full border-2 border-white hover:bg-white/20 transform hover:scale-110 transition-all duration-300">
                    {{ __('Contact Us') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
