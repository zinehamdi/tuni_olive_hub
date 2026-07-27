@php
    $locale = app()->getLocale();
    $isRTL = $locale === 'ar';
    $coverPhotos = ($coverPhotos ?? collect())->filter()->values();
    $profilePhotoUrl = $user->profile_picture ? Storage::url($user->profile_picture) : null;
    $isOwner = auth()->check() && auth()->id() === $user->id;
    $contactInfo = [
        'phone' => $user->phone ?? $user->phone_number ?? null,
        'email' => $user->email ?? null,
    ];
    if (isset($showContact) && !$showContact) {
        $contactInfo = ['phone' => null, 'email' => null];
    }
@endphp

<x-app-layout>
    <div class="min-h-screen bg-gray-50" dir="{{ $isRTL ? 'rtl' : 'ltr' }}">
        
        <!-- TOP COVER & AVATAR -->
        <div class="bg-white shadow-sm border-b border-gray-100">
            <div class="max-w-[1400px] mx-auto relative">
                <!-- Cover Photo -->
                <div class="h-48 md:h-64 lg:h-72 w-full relative overflow-hidden bg-gradient-to-r from-emerald-500 to-teal-600">
                    @if($coverPhotos->count() > 0)
                        <img src="{{ $coverPhotos[0] }}" class="w-full h-full object-cover opacity-90" loading="lazy">
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                </div>
                <!-- Avatar & Name -->
                <div class="px-4 sm:px-8 pb-6 relative flex flex-col sm:flex-row gap-4 sm:gap-6 items-center sm:items-end -mt-16 sm:-mt-20 z-10">
                    <div class="relative group shrink-0">
                        <div class="relative w-32 h-32 sm:w-40 sm:h-40 rounded-full overflow-hidden ring-4 ring-white shadow-xl bg-white shrink-0 flex items-center justify-center">
                            @if($profilePhotoUrl)
                                <img src="{{ $profilePhotoUrl }}" style="width: 128px; height: 128px; min-width: 128px; min-height: 128px;" class="w-full h-full object-cover" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            @endif
                            <div class="{{ $profilePhotoUrl ? 'hidden' : 'flex' }} w-full h-full rounded-full bg-gradient-to-br from-emerald-500 to-teal-500 items-center justify-center text-white text-5xl font-bold">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        </div>
                        <div class="absolute bottom-2 {{ $isRTL ? 'left-2' : 'right-2' }} w-6 h-6 bg-green-500 rounded-full ring-4 ring-white shadow-md"></div>
                    </div>
                    
                    <div class="flex-1 w-full flex flex-col md:flex-row items-center justify-between gap-4 mt-2 sm:mt-0 pt-2 sm:pt-20">
                        <!-- User Name -->
                        <div class="text-center sm:text-start shrink-0">
                            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 flex items-center justify-center sm:justify-start gap-2">
                                <span>{{ $user->name }}</span>
                                @if($user->trust_score > 80)
                                    <span title="{{ __('حساب موثق وموثوق') }}">
                                        <svg class="w-6 h-6 text-blue-500 shadow-sm" fill="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                    </span>
                                @endif
                            </h1>
                        </div>

                        <!-- Role & Business Badge (Placed Prominently in the Middle) -->
                        <div class="flex justify-center items-center flex-1 my-1 sm:my-0">
                            <div class="inline-flex items-center gap-2.5 bg-gradient-to-r from-emerald-50 via-teal-50 to-amber-50/90 text-emerald-950 px-5 py-2.5 rounded-2xl border border-emerald-200/90 shadow-md font-extrabold text-base sm:text-lg backdrop-blur-md transform hover:scale-[1.02] transition duration-300">
                                @if($user->role === 'farmer' && ($user->farm_name || $user->farm_name_ar))
                                    <span class="text-xl">🌾</span>
                                    <span>{{ $user->farm_name ?? $user->farm_name_ar }}</span>
                                @elseif($user->role === 'mill' && $user->mill_name)
                                    <span class="text-xl">🏭</span>
                                    <span>{{ $user->mill_name }}</span>
                                @elseif($user->role === 'carrier' && $user->company_name)
                                    <span class="text-xl">🚛</span>
                                    <span>{{ $user->company_name }}</span>
                                @elseif($user->role === 'packer' && $user->packer_name)
                                    <span class="text-xl">📦</span>
                                    <span>{{ $user->packer_name }}</span>
                                @else
                                    @php
                                        $roleNames = [
                                            'farmer' => ['ar' => '🌾 مزارع زيتون', 'en' => '🌾 Olive Grower'],
                                            'carrier' => ['ar' => '🚛 ناقل بري وبحري', 'en' => '🚛 Transporter'],
                                            'mill' => ['ar' => '🏭 معصرة زيتون', 'en' => '🏭 Oil Mill'],
                                            'packer' => ['ar' => '📦 وحدة تعبئة وتغليف', 'en' => '📦 Packaging Facility'],
                                            'transiteur' => ['ar' => '🛃 مخلص جمركي', 'en' => '🛃 Customs Broker'],
                                            'comptable' => ['ar' => '📊 محاسب خبير', 'en' => '📊 Accountant'],
                                            'service_bureau' => ['ar' => '📝 مكتب خدمات إدارية', 'en' => '📝 Service Bureau'],
                                            'agri_equipment' => ['ar' => '🚜 معدات وآليات فلاحية', 'en' => '🚜 Agri-Equipment'],
                                            'agri_materials' => ['ar' => '🌱 مواد فلاحية وأسمدة', 'en' => '🌱 Agri-Materials'],
                                            'agri_study_office' => ['ar' => '📐 مكتب دراسات فلاحية', 'en' => '📐 Agri-Study Office'],
                                        ];
                                        $locale = app()->getLocale();
                                        $roleName = $roleNames[$user->role][$locale === 'ar' ? 'ar' : 'en'] ?? ($locale === 'ar' ? '✨ عضو منصة الزين' : '✨ Member');
                                    @endphp
                                    <span>{{ $roleName }}</span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Actions (Right Side) -->
                        @if(!$isOwner)
                        <div x-data="userInteraction()" x-init="init()" class="flex justify-center sm:justify-end gap-2 w-full md:w-auto shrink-0">
                            <button @click="confirmFollow()" :class="followed ? 'bg-gray-100 text-gray-800' : 'bg-gradient-to-r from-emerald-500 to-teal-600 text-white'" class="bg-gradient-to-r from-emerald-500 to-teal-600 text-white flex-1 sm:flex-none justify-center px-5 py-2.5 rounded-xl font-bold text-sm shadow-md hover:shadow-lg transition flex items-center gap-2">
                                <span x-text="followed ? '{{ __('Following') }}' : '{{ __('Follow') }}'">{{ __('Follow') }}</span>
                                <span class="px-2 py-0.5 rounded-full text-xs bg-white/20" :class="followed ? 'bg-white' : 'bg-white/20'" x-text="followerCount !== null ? followerCount : '-'">-</span>
                            </button>
                            <button @click="confirmLike()" :class="liked ? 'bg-rose-50 text-rose-600 border-rose-200' : 'bg-white text-gray-700 border-gray-200'" class="bg-white text-gray-700 border border-gray-200 px-4 py-2.5 rounded-xl font-bold text-sm shadow-sm hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 transition flex items-center gap-2">
                                <svg class="w-5 h-5 text-gray-400" :class="liked ? 'fill-rose-500 text-rose-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                                <span x-text="likeCount !== null ? likeCount : '-'">-</span>
                            </button>
                            <a href="{{ auth()->check() ? route('messages.show', $user) : route('login') }}" class="px-4 py-2.5 bg-gray-900 text-white rounded-xl font-bold text-sm shadow-md hover:bg-gray-800 transition flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- MAIN 3-COLUMN GRID -->
        <div class="max-w-[1400px] mx-auto px-4 py-8">
            <div class="flex flex-col xl:flex-row gap-6 items-start">
                
                <!-- LEFT SIDEBAR: PROFILE DETAILS -->
                <aside class="w-full xl:w-72 flex-shrink-0 flex flex-col gap-4 order-3 xl:order-1">
                    <!-- Rating Card -->
                    @if($user->rating_avg > 0)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center text-2xl font-bold text-amber-500">{{ number_format($user->rating_avg, 1) }}</div>
                            <div>
                                <div class="flex text-amber-400">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= round($user->rating_avg) ? 'text-amber-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    @endfor
                                </div>
                                <span class="text-xs text-gray-500 font-medium">{{ $user->rating_count }} {{ __('reviews') }}</span>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- About Us (من نحن) Card -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 space-y-4">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                                <span class="text-lg">ℹ️</span>
                                <span>{{ __('About') }}</span>
                            </h3>
                            <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-100">
                                @if($user->role === 'farmer')
                                    🌾 {{ app()->getLocale() === 'ar' ? 'مزارع زيتون' : 'Olive Grower' }}
                                @elseif($user->role === 'mill')
                                    🏭 {{ app()->getLocale() === 'ar' ? 'معصرة زيتون' : 'Oil Mill' }}
                                @elseif($user->role === 'packer')
                                    📦 {{ app()->getLocale() === 'ar' ? 'وحدة تعبئة' : 'Packaging' }}
                                @elseif($user->role === 'carrier')
                                    🚛 {{ app()->getLocale() === 'ar' ? 'ناقل بري' : 'Transporter' }}
                                @else
                                    ✨ {{ app()->getLocale() === 'ar' ? 'عضو منصة الزين' : 'Member' }}
                                @endif
                            </span>
                        </div>

                        <!-- Farm / Business Summary -->
                        @php
                            $userBio = $user->meta_data['service_description'] ?? $user->farm_location ?? $user->bio ?? null;
                        @endphp
                        @if($userBio)
                            <div class="text-xs text-gray-700 bg-gray-50 rounded-xl p-3.5 border border-gray-100 leading-relaxed font-medium">
                                {{ $userBio }}
                            </div>
                        @endif

                        <!-- Business Details Grid -->
                        <div class="space-y-2.5 text-xs">
                            @if($user->role === 'farmer' && !empty($user->tree_number))
                                <div class="flex items-center justify-between bg-emerald-50/50 p-2.5 rounded-xl border border-emerald-100/50">
                                    <span class="text-gray-600 font-bold flex items-center gap-1.5">
                                        <span>🌳</span>
                                        <span>{{ app()->getLocale() === 'ar' ? 'عدد أشجار الزيتون' : 'Olive Trees' }}</span>
                                    </span>
                                    <span class="font-extrabold text-emerald-900">{{ number_format($user->tree_number) }} {{ app()->getLocale() === 'ar' ? 'شجرة' : 'trees' }}</span>
                                </div>
                            @endif

                            @if($user->role === 'mill' && !empty($user->mill_name))
                                <div class="flex items-center justify-between bg-emerald-50/50 p-2.5 rounded-xl border border-emerald-100/50">
                                    <span class="text-gray-600 font-bold flex items-center gap-1.5">
                                        <span>🏭</span>
                                        <span>{{ app()->getLocale() === 'ar' ? 'اسم المعصرة' : 'Mill Name' }}</span>
                                    </span>
                                    <span class="font-extrabold text-emerald-900">{{ $user->mill_name }}</span>
                                </div>
                            @endif

                            <!-- Location / Governorate -->
                            @php
                                $userLocation = $addresses->first()?->governorate ?? $user->governorate ?? $user->farm_location ?? null;
                                $userDelegation = $addresses->first()?->delegation ?? null;
                            @endphp
                            @if($userLocation)
                                <div class="flex items-center gap-2.5 text-gray-700 pt-1">
                                    <div class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                                        📍
                                    </div>
                                    <span class="font-bold">{{ $userLocation }} @if($userDelegation) — {{ $userDelegation }} @endif</span>
                                </div>
                            @endif

                            @if($contactInfo['phone'])
                            <div class="flex items-center gap-2.5 pt-1">
                                <div class="w-7 h-7 rounded-lg bg-green-50 text-green-600 flex items-center justify-center shrink-0">
                                    📞
                                </div>
                                @auth
                                    <a href="tel:{{ $contactInfo['phone'] }}" class="font-bold text-gray-800 hover:text-green-600 transition" dir="ltr">{{ $contactInfo['phone'] }}</a>
                                @else
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-gray-400 blur-[2px] select-none" dir="ltr">+216 XX XXX XXX</span>
                                        <a href="{{ route('register') }}" class="text-[10px] font-bold bg-[#6A8F3B]/10 text-[#6A8F3B] hover:bg-[#6A8F3B] hover:text-white px-2 py-0.5 rounded-md transition">
                                            🔒 {{ app()->getLocale() === 'ar' ? 'سجل لإظهار الرقم' : 'Register to unlock' }}
                                        </a>
                                    </div>
                                @endauth
                            </div>
                            @endif

                            @if($contactInfo['email'])
                            <div class="flex items-center gap-2.5 pt-1">
                                <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                                    ✉️
                                </div>
                                @auth
                                    <a href="mailto:{{ $contactInfo['email'] }}" class="font-bold text-gray-800 hover:text-blue-600 transition truncate">{{ $contactInfo['email'] }}</a>
                                @else
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-gray-400 blur-[2px] select-none">contact@******.com</span>
                                        <a href="{{ route('register') }}" class="text-[10px] font-bold bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white px-2 py-0.5 rounded-md transition">
                                            🔒 {{ app()->getLocale() === 'ar' ? 'سجل لإظهار الإيميل' : 'Register to unlock' }}
                                        </a>
                                    </div>
                                @endauth
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Certified PDF Laboratory Analysis Card (Under About Us with Live PDF Preview & Full Screen Viewer) -->
                    @php
                        $userLabAnalyses = collect($user->lab_analyses ?? [])->filter()->values();
                    @endphp
                    <div class="bg-white rounded-3xl shadow-xl border border-amber-100/80 overflow-hidden space-y-4" x-data="{ activePdfModal: null }">
                        <div class="px-5 py-4 bg-gradient-to-r from-amber-500/10 via-emerald-500/5 to-amber-500/10 border-b border-amber-100 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-500 to-emerald-600 flex items-center justify-center text-white text-lg shadow-md font-bold">
                                    📜
                                </div>
                                <div>
                                    <h3 class="text-sm font-extrabold text-gray-900 flex items-center gap-1.5">
                                        <span>{{ app()->getLocale() === 'ar' ? 'التحاليل والشهادات المخبرية' : 'Certified Lab Reports' }}</span>
                                    </h3>
                                    <p class="text-[11px] text-amber-800/80 font-medium">
                                        🛡️ {{ app()->getLocale() === 'ar' ? 'شهادات جودة معتمدة رسمياً' : 'Officially Verified Quality Certificates' }}
                                    </p>
                                </div>
                            </div>
                            <span class="text-[10px] font-black bg-red-600 text-white px-2.5 py-0.5 rounded-full uppercase tracking-wider shadow-sm">
                                PDF
                            </span>
                        </div>

                        <div class="p-4 space-y-4">
                            @if($userLabAnalyses->count() > 0)
                                @foreach($userLabAnalyses as $index => $lab)
                                    @php
                                        $pdfUrl = !empty($lab['file_path']) ? Storage::disk('public')->url($lab['file_path']) : null;
                                        
                                        $rawTitle = $lab['title'] ?? '';
                                        $titleTranslations = [
                                            'award_gold_medal' => [
                                                'ar' => '🏆 جائزة وميدالية ذهبية دولية للجودة',
                                                'en' => '🏆 International Gold Medal Award for Quality',
                                                'fr' => '🏆 Médaille d\'Or Internationale de Qualité',
                                            ],
                                            'iso_cert' => [
                                                'ar' => '📜 شهادة مطابقة وجودة إيزو (ISO / Bio)',
                                                'en' => '📜 Official ISO & Organic Bio Certificate',
                                                'fr' => '📜 Certificat Officiel ISO & Bio Biologique',
                                            ],
                                            'acidity_peroxide' => [
                                                'ar' => '🧪 تحليل نسبة الحموضة والتأكسد',
                                                'en' => '🧪 Acidity & Peroxide Value Analysis',
                                                'fr' => '🧪 Analyse d\'Acidité & Indice de Peroxydes',
                                            ],
                                            'comprehensive_quality' => [
                                                'ar' => '🏅 تحليل الجودة الشاملة والتصنيف الرسمية',
                                                'en' => '🏅 Comprehensive Quality & Grade Certificate',
                                                'fr' => '🏅 Certificat de Qualité Globale & Grade',
                                            ],
                                            'fatty_acids' => [
                                                'ar' => '🔬 تحليل التركيب الكيميائي والأحماض الدهنية',
                                                'en' => '🔬 Fatty Acid Profile & Composition',
                                                'fr' => '🔬 Profil des Acides Gras & Composition',
                                            ],
                                            'pesticides_screen' => [
                                                'ar' => '🌱 تحليل بقايا المبيدات والملوثات',
                                                'en' => '🌱 Pesticide Residues & Contaminants Screen',
                                                'fr' => '🌱 Analyse des Résidus de Pesticides',
                                            ],
                                            'sensory_panel' => [
                                                'ar' => '👅 تحليل التذوق الحسي والتقييم الأورجانوستيك',
                                                'en' => '👅 Organoleptic & Sensory Panel Evaluation',
                                                'fr' => '👅 Profil Sensoriel & Dégustation Organoleptique',
                                            ],
                                            'other_lab_report' => [
                                                'ar' => '📋 شهادة أو تقرير تحليل مخبري رسمي',
                                                'en' => '📋 Official Laboratory Certificate or Report',
                                                'fr' => '📋 Certificat ou Rapport d\'Analyse Officiel',
                                            ],
                                        ];

                                        $locale = app()->getLocale();
                                        $displayTitle = $titleTranslations[$rawTitle][$locale] 
                                            ?? $titleTranslations[$rawTitle]['ar'] 
                                            ?? null;

                                        if (!$displayTitle) {
                                            if (str_contains($rawTitle, 'حموضة') || str_contains(strtolower($rawTitle), 'acidity')) {
                                                $displayTitle = $titleTranslations['acidity_peroxide'][$locale] ?? $rawTitle;
                                            } elseif (str_contains($rawTitle, 'جودة') || str_contains(strtolower($rawTitle), 'quality')) {
                                                $displayTitle = $titleTranslations['comprehensive_quality'][$locale] ?? $rawTitle;
                                            } else {
                                                $displayTitle = $rawTitle;
                                            }
                                        }
                                    @endphp
                                    @if($pdfUrl)
                                    <div class="bg-gradient-to-b from-gray-50 to-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition group">
                                        
                                        <!-- PDF Document Header Info -->
                                        <div class="p-4 border-b border-gray-100 bg-white">
                                            <div class="flex items-start justify-between gap-2 mb-2">
                                                <h4 class="text-xs font-black text-gray-900 leading-snug group-hover:text-emerald-700 transition">
                                                    📄 {{ $displayTitle }}
                                                </h4>
                                                <span class="text-[10px] font-bold text-gray-400 shrink-0 bg-gray-100 px-2 py-0.5 rounded-md">
                                                    {{ $lab['file_size'] ?? 'PDF' }}
                                                </span>
                                            </div>
                                            
                                            <div class="flex flex-wrap items-center gap-2 text-[11px] text-gray-600 font-medium">
                                                @if(!empty($lab['lab_name']))
                                                    <span class="bg-amber-50 text-amber-800 border border-amber-100 px-2 py-0.5 rounded-md font-bold">
                                                        🔬 {{ $lab['lab_name'] }}
                                                    </span>
                                                @endif
                                                @if(!empty($lab['analysis_date']))
                                                    <span class="bg-gray-100 text-gray-700 px-2 py-0.5 rounded-md">
                                                        📅 {{ $lab['analysis_date'] }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Visual PDF Document Embedded Live Preview Box -->
                                        <div class="relative h-44 sm:h-52 bg-slate-900 overflow-hidden group/pdf cursor-pointer" @click="activePdfModal = '{{ $pdfUrl }}'">
                                            <!-- Live Iframe / Object Preview -->
                                            <iframe src="{{ $pdfUrl }}#toolbar=0&navpanes=0&scrollbar=0" class="w-full h-full border-0 pointer-events-none opacity-85 group-hover/pdf:opacity-100 group-hover/pdf:scale-105 transition-all duration-500"></iframe>
                                            
                                            <!-- Gradient Hover Overlay -->
                                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-900/40 to-transparent flex flex-col items-center justify-end p-4 text-center">
                                                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-red-600 to-amber-600 text-white font-extrabold text-xs shadow-lg transform group-hover/pdf:scale-105 transition duration-300">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    <span>{{ app()->getLocale() === 'ar' ? 'معاينة وثيقة PDF بالكامل' : 'Open Full PDF Preview' }}</span>
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Action Bar (View Only) -->
                                        <div class="p-3 bg-gray-50 flex items-center justify-center text-xs">
                                            <button @click="activePdfModal = '{{ $pdfUrl }}'" class="w-full py-2 px-3 bg-white border border-gray-200 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-200 text-gray-700 font-bold rounded-xl shadow-sm transition flex items-center justify-center gap-1.5">
                                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                <span>{{ app()->getLocale() === 'ar' ? 'تكبير واستعراض الوثيقة' : 'Full Screen Preview' }}</span>
                                            </button>
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
                            @else
                                <div class="p-6 bg-gray-50/80 rounded-2xl text-center text-xs text-gray-500 border border-dashed border-gray-200">
                                    <span class="text-3xl block mb-2">📜</span>
                                    <span>{{ app()->getLocale() === 'ar' ? 'لا تتوفر تحاليل مخبرية مرفوعة حالياً لهذا البروفايل.' : 'No PDF lab analysis reports available yet.' }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Fullscreen Interactive PDF Viewer Modal -->
                        <div x-show="activePdfModal" class="fixed inset-0 z-[200] overflow-y-auto" x-cloak style="display: none;">
                            <div class="flex items-center justify-center min-h-screen p-2 sm:p-4 text-center">
                                <div class="fixed inset-0 bg-black/80 backdrop-blur-md transition-opacity" @click="activePdfModal = null"></div>
                                
                                <div class="inline-block bg-white rounded-3xl text-right overflow-hidden shadow-2xl transform transition-all max-w-5xl w-full h-[90vh] flex flex-col z-10 relative border border-gray-100">
                                    <!-- Modal Header -->
                                    <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between shrink-0">
                                        <div class="flex items-center gap-3">
                                            <span class="px-2.5 py-1 bg-red-600 text-white rounded-lg font-black text-xs">PDF</span>
                                            <h3 class="font-bold text-sm sm:text-base text-white">
                                                {{ app()->getLocale() === 'ar' ? 'معاينة وثيقة التحليل المخبري الرسمي' : 'Official PDF Laboratory Certificate' }}
                                            </h3>
                                        </div>
                                        <button @click="activePdfModal = null" class="w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition">
                                            ✕
                                        </button>
                                    </div>

                                    <!-- PDF Viewer Iframe Body -->
                                    <div class="flex-1 bg-slate-100 relative">
                                        <template x-if="activePdfModal">
                                            <iframe :src="activePdfModal" class="w-full h-full border-0"></iframe>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </aside>

                <!-- MIDDLE COLUMN: PRODUCTS (LISTINGS) -->
                <main class="flex-1 min-w-0 w-full order-2 xl:order-2">
                    @if(in_array($user->role, ['carrier', 'mill', 'packer', 'transiteur', 'comptable', 'service_bureau', 'agri_equipment', 'agri_materials', 'agri_study_office']))
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                        <div class="px-5 py-4 border-b border-gray-100 bg-[#6A8F3B]/5">
                            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                <span class="text-xl">💼</span>
                                {{ app()->getLocale() === 'ar' ? 'الخدمات والعروض المتوفرة' : 'Services & Solutions Offered' }}
                            </h2>
                        </div>
                        
                        <div class="p-6 space-y-6">
                            @php
                                $desc = $user->meta_data['service_description'] ?? '';
                                $priceType = $user->meta_data['price_type'] ?? 'quote';
                                $price = $user->meta_data['service_price'] ?? null;
                                $providerType = $user->meta_data['provider_type'] ?? '';
                                
                                $providerTypeLabels = [
                                    'freelancer' => ['ar' => 'مستقل / Freelancer', 'color' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                                    'bureau' => ['ar' => 'مكتب / Bureau', 'color' => 'bg-sky-50 text-sky-700 border-sky-200'],
                                    'societe' => ['ar' => 'شركة / Société', 'color' => 'bg-purple-50 text-purple-700 border-purple-200'],
                                ];
                                $typeData = $providerTypeLabels[$providerType] ?? null;
                            @endphp

                            <div class="flex flex-wrap items-center gap-3">
                                @if($typeData)
                                    <span class="px-3 py-1 rounded-lg border text-xs font-bold {{ $typeData['color'] }}">
                                        {{ $typeData['ar'] }}
                                    </span>
                                @endif
                                <span class="px-3 py-1 rounded-lg bg-gray-100 text-gray-700 text-xs font-bold">
                                    💰 {{ $priceType === 'fixed' && !empty($price) ? number_format($price, 0) . ' TND' : (app()->getLocale() === 'ar' ? 'السعر حسب الطلب' : 'Price Upon Request') }}
                                </span>
                            </div>

                            @if($desc)
                            <div class="prose max-w-none text-gray-700 text-sm leading-relaxed whitespace-pre-line bg-gray-50 rounded-2xl p-5 border border-gray-100 font-medium">
                                {{ $desc }}
                            </div>
                            @endif

                            @php
                                $servicesList = $user->meta_data['services'] ?? [];
                            @endphp

                            @if(!empty($servicesList))
                            <div class="mt-6 pt-6 border-t border-gray-100">
                                <h3 class="text-sm font-bold text-gray-900 mb-4">{{ app()->getLocale() === 'ar' ? 'الخدمات المتوفرة والأسعار' : 'Services Offered & Prices' }}</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach($servicesList as $srv)
                                    <div class="border border-gray-100 rounded-2xl p-5 bg-white shadow-sm flex flex-col justify-between hover:shadow-md hover:border-indigo-100 transition duration-300 text-right">
                                        <div class="space-y-2">
                                            @if(!empty($srv['image']))
                                            <div class="w-full h-36 rounded-xl overflow-hidden mb-3 relative bg-gray-50 border border-gray-100">
                                                <img src="{{ asset($srv['image']) }}" class="w-full h-full object-cover">
                                            </div>
                                            @endif
                                            <h4 class="font-bold text-gray-900 text-sm">{{ $srv['title'] }}</h4>
                                            <div class="flex items-center gap-2">
                                                <span class="text-[10px] font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-lg border border-indigo-100">
                                                    @if(($srv['price_type'] ?? 'fixed') === 'fixed')
                                                        {{ number_format($srv['price'] ?? 0, 0) }} TND
                                                    @else
                                                        {{ app()->getLocale() === 'ar' ? 'سعر عند الطلب' : 'Price Upon Request' }}
                                                    @endif
                                                </span>
                                            </div>
                                            @if(!empty($srv['description']))
                                            <p class="text-xs text-gray-600 leading-relaxed">{{ $srv['description'] }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            <div class="pt-4 border-t border-gray-100 flex flex-wrap gap-4">
                                @auth
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $user->phone) }}" target="_blank" class="px-6 py-3 rounded-xl bg-green-500 hover:bg-green-600 text-white font-bold text-sm flex items-center gap-2 transition">
                                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.246 2.248 3.484 5.232 3.484 8.412-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.309 1.656zm6.224-3.52s.126.074.39.231c1.56.93 3.351 1.421 5.22 1.422 5.513 0 10-4.487 10-10 0-2.673-1.04-5.186-2.93-7.076-1.89-1.889-4.403-2.928-7.07-2.929-5.515 0-10.002 4.487-10.002 10 0 1.763.461 3.486 1.332 5.012l.145.255-1.111 4.056 4.126-1.082z"/></svg>
                                        {{ app()->getLocale() === 'ar' ? 'تواصل عبر واتساب' : 'Contact on WhatsApp' }}
                                    </a>
                                    @if($user->email)
                                    <a href="mailto:{{ $user->email }}" class="px-6 py-3 rounded-xl border border-gray-300 hover:bg-gray-50 text-gray-700 font-bold text-sm flex items-center gap-2 transition">
                                        ✉️ {{ $user->email }}
                                    </a>
                                    @endif
                                @else
                                    <a href="{{ route('register') }}" class="px-6 py-3 rounded-xl bg-[#6A8F3B] hover:bg-[#5a7a2f] text-white font-bold text-sm flex items-center gap-2 transition shadow-lg">
                                        🔒 {{ app()->getLocale() === 'ar' ? 'سجل مجاناً للتواصل مع هذا المزود' : 'Register Free to Contact Provider' }}
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                {{ __('Products & Listings') }}
                            </h2>
                        </div>
                        
                        <div class="p-4">
                            @if($listings->count() > 0)
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-4">
                                @foreach($listings as $listing)
                                @php
                                    $productImage = null;
                                    if($listing->product && $listing->product->media && is_array($listing->product->media) && count($listing->product->media) > 0) { $productImage = $listing->product->media[0]; }
                                    elseif($listing->media && is_array($listing->media) && count($listing->media) > 0) { $productImage = $listing->media[0]; }
                                @endphp
                                <div class="group border border-gray-100 rounded-2xl overflow-hidden hover:border-emerald-500/50 hover:shadow-xl transition-all duration-300 flex flex-col">
                                    <div class="relative h-40 bg-gradient-to-br from-emerald-500 to-teal-600 overflow-hidden flex-shrink-0">
                                        @if($productImage)
                                        <img src="{{ Storage::url($productImage) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                                        @else
                                        @php
                                            $type = $listing->product?->type ?? $listing->category ?? 'oil';
                                            $variety = $listing->product?->variety ?? $user->name ?? 'Zintoop';
                                            $words = preg_split('/\s+/', trim($variety));
                                            $initials = count($words) >= 2 ? mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1) : mb_substr($variety, 0, 2);
                                            $initials = mb_strtoupper($initials);
                                            $bgStyle = ($type === 'olive') 
                                                ? 'background: linear-gradient(135deg, #143618 0%, #2A5C2E 50%, #47824B 100%);' 
                                                : 'background: linear-gradient(135deg, #7A5A1B 0%, #C8A356 50%, #4F6C28 100%);';
                                        @endphp
                                        <div class="w-full h-full flex flex-col items-center justify-center p-3 relative overflow-hidden" style="{{ $bgStyle }}">
                                            <div class="w-12 h-12 rounded-full bg-white/20 backdrop-blur-md border border-white/40 flex items-center justify-center shadow-lg mb-1">
                                                <span class="text-white text-sm font-black uppercase">{{ $initials }}</span>
                                            </div>
                                            <span class="text-white/90 text-[10px] font-bold px-2 py-0.5 rounded-full bg-black/20 backdrop-blur-sm border border-white/10">
                                                {{ $type === 'olive' ? '🫒 ' . (app()->getLocale() === 'ar' ? 'زيتون' : __('Olives')) : '🫗 ' . (app()->getLocale() === 'ar' ? 'زيت زيتون' : __('Olive Oil')) }}
                                            </span>
                                        </div>
                                        @endif
                                        <div class="absolute top-2 right-2">
                                            @if($listing->status==='active')<span class="bg-green-500 text-white px-2 py-0.5 rounded-full text-xs font-bold shadow">✓ {{ __('Active') }}</span>
                                            @elseif($listing->status==='pending')<span class="bg-amber-500 text-white px-2 py-0.5 rounded-full text-xs font-bold shadow">⏳ {{ __('Pending') }}</span>
                                            @elseif($listing->status==='sold')<span class="bg-gray-600 text-white px-2 py-0.5 rounded-full text-xs font-bold shadow">✓ {{ __('Sold') }}</span>
                                            @else<span class="bg-red-500 text-white px-2 py-0.5 rounded-full text-xs font-bold shadow">✕ {{ __('Inactive') }}</span>@endif
                                        </div>
                                        @if($listing->product)<div class="absolute bottom-2 left-2"><span class="bg-white/90 backdrop-blur text-emerald-700 px-2 py-0.5 rounded-lg text-xs font-bold">{{ $listing->product->type==='oil' ? '🫗 '.__('Oil') : '🫒 '.__('Olives') }}</span></div>@endif
                                    </div>
                                    <div class="p-4 flex flex-col flex-1">
                                        <h3 class="font-bold text-gray-900 group-hover:text-emerald-600 transition text-sm mb-1 truncate">{{ $listing->product?->variety ?? __('Product') }}@if($listing->product?->quality)<span class="text-xs text-gray-400 font-normal"> — {{ $listing->product->quality }}</span>@endif</h3>
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-emerald-600 font-bold">{{ $listing->price ?? '-' }} <span class="text-xs text-gray-400">{{ $listing->currency ?? 'TND' }}</span></span>
                                            <span class="text-gray-400 text-xs">{{ $listing->product?->quantity ?? '-' }} {{ __('units') }}</span>
                                        </div>
                                        <p class="text-xs text-gray-400 mb-3">{{ $listing->created_at->diffForHumans() }}</p>
                                        <div class="mt-auto pt-2">
                                            <a href="{{ url('/listings/'.$listing->id) }}" class="block w-full text-center bg-gray-50 text-gray-700 border border-gray-200 px-3 py-2 rounded-xl hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-200 transition font-bold text-xs">{{ __('View Details') }}</a>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <div class="mt-4">{{ $listings->links() }}</div>
                            @else
                            <div class="text-center py-12">
                                <svg class="w-16 h-16 mx-auto text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                <h3 class="text-lg font-bold text-gray-500 mb-2">{{ __('No listings yet') }}</h3>
                                <p class="text-gray-400 text-sm mb-5">{{ __('This user has not added any products.') }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Trees Highlight (for farmers) -->
                    @if($user->role === 'farmer' && $user->tree_number)
                    <div class="bg-gradient-to-r from-emerald-600 to-teal-700 rounded-2xl shadow-md overflow-hidden mb-6 text-white relative">
                        <div class="absolute inset-0 bg-black/10"></div>
                        <div class="relative p-6 sm:p-8 flex items-center justify-between">
                            <div>
                                <h3 class="text-emerald-100 font-bold uppercase tracking-wider text-xs mb-1">{{ __('Number of olive trees') }}</h3>
                                <div class="text-4xl sm:text-5xl font-black">{{ number_format($user->tree_number) }} <span class="text-2xl opacity-80 text-emerald-200">🌳</span></div>
                                <div class="flex gap-2 mt-3">
                                    @if($user->olive_type)<span class="px-2.5 py-1 rounded-lg bg-white/20 backdrop-blur text-xs font-bold">{{ $user->olive_type }}</span>@endif
                                    @if($user->farm_location)<span class="px-2.5 py-1 rounded-lg bg-white/20 backdrop-blur text-xs font-bold">{{ $user->farm_location }}</span>@endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </main>

                <!-- RIGHT SIDEBAR: STORIES & GALLERY -->
                <aside class="w-full xl:w-72 flex-shrink-0 flex flex-col gap-4 order-1 xl:order-3">
                    
                    <!-- Stories Section -->
                    <div id="stories-section" x-data="{
                        stories:[],current:null,currentIndex:0,loading:true,error:false,userId:{{ $user->id }},progress:0,timer:null,
                        fetchStories(){fetch(`/user/${this.userId}/stories`).then(r=>r.json()).then(d=>{this.stories=d;}).catch(()=>{this.error=true;}).finally(()=>{this.loading=false;});},
                        openStory(story,index){this.current=story;this.currentIndex=index;this.startProgress();},
                        closeStory(){this.current=null;this.stopProgress();},
                        nextStory(){if(this.currentIndex<this.stories.length-1){this.currentIndex++;this.current=this.stories[this.currentIndex];this.startProgress();}else{this.closeStory();}},
                        prevStory(){if(this.currentIndex>0){this.currentIndex--;this.current=this.stories[this.currentIndex];this.startProgress();}},
                        startProgress(){this.progress=0;this.stopProgress();if(this.current?.media_type==='image'){this.timer=setInterval(()=>{this.progress+=2;if(this.progress>=100)this.nextStory();},100);}},
                        stopProgress(){if(this.timer)clearInterval(this.timer);}
                    }" x-init="fetchStories()" @keydown.escape.window="closeStory()" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-4 py-3 bg-gradient-to-r from-amber-500 to-orange-500 flex justify-between items-center">
                            <h2 class="font-bold text-white text-sm flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                                {{ __('Stories') }}
                            </h2>
                            <span class="px-2 py-0.5 bg-white/20 rounded-lg text-[10px] text-white font-bold uppercase tracking-wider">{{ __('Live') }}</span>
                        </div>
                        <div class="p-4 bg-gray-50/50">
                            <template x-if="loading"><div class="animate-pulse flex gap-2 overflow-hidden"><div class="w-16 h-16 bg-gray-200 rounded-xl"></div><div class="w-16 h-16 bg-gray-200 rounded-xl"></div></div></template>
                            
                            <template x-if="!loading && !error && stories.length === 0">
                                <div class="bg-white border-2 border-dashed border-gray-200 rounded-xl p-6 text-center">
                                    <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-gray-100 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <p class="text-gray-600 font-bold text-sm">{{ __('No stories yet') }}</p>
                                    <p class="text-xs text-gray-400 mt-1">{{ __('Check back soon!') }}</p>
                                </div>
                            </template>
                            
                            <!-- Inline Stories Feed Grid -->
                            <div x-show="!loading && !error && stories.length > 0" x-cloak class="grid grid-cols-2 gap-2">
                                <template x-for="(story, index) in stories" :key="story.id">
                                    <button type="button" @click="openStory(story, index)" class="group relative aspect-[3/4] bg-black rounded-xl overflow-hidden focus:outline-none focus:ring-2 focus:ring-amber-500 shadow-sm">
                                        <template x-if="story.media_type === 'image'">
                                            <img :src="story.url" class="absolute inset-0 w-full h-full object-cover opacity-90 group-hover:opacity-100 group-hover:scale-105 transition duration-500">
                                        </template>
                                        <template x-if="story.media_type === 'video'">
                                            <video :src="story.url" autoplay muted loop playsinline class="absolute inset-0 w-full h-full object-cover opacity-90 group-hover:opacity-100 group-hover:scale-105 transition duration-500 pointer-events-none"></video>
                                        </template>
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                                        <div class="absolute bottom-2 left-2 right-2 text-left">
                                            <span x-show="story.caption" class="text-[10px] text-white font-medium line-clamp-2 leading-tight" x-text="story.caption"></span>
                                        </div>
                                    </button>
                                </template>
                            </div>

                            <!-- Fullscreen Viewer -->
                            <div x-show="current" x-cloak class="fixed inset-0 z-[200] flex items-center justify-center bg-black/95 backdrop-blur-md">
                                <div class="absolute top-4 left-4 right-4 flex gap-1 z-50">
                                    <template x-for="(s,i) in stories" :key="s.id"><div class="flex-1 h-1 bg-white/30 rounded-full overflow-hidden"><div class="h-full bg-white rounded-full transition-all duration-100" :style="{width: i<currentIndex?'100%':(i===currentIndex?progress+'%':'0%')}"></div></div></template>
                                </div>
                                <button @click="prevStory()" class="absolute left-0 top-0 bottom-0 w-1/3 z-10 focus:outline-none" x-show="currentIndex>0"></button>
                                <button @click="nextStory()" class="absolute right-0 top-0 bottom-0 w-1/3 z-10 focus:outline-none"></button>
                                <div class="relative w-full max-w-sm mx-auto h-[80vh] flex items-center justify-center">
                                    <button type="button" @click="closeStory()" class="absolute -top-12 right-0 text-white/80 hover:text-white p-2 z-50"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                                    <template x-if="current && current.media_type==='image'"><img :src="current.url" class="max-w-full max-h-full object-contain rounded-2xl shadow-2xl"></template>
                                    <template x-if="current && current.media_type==='video'"><video :src="current.url" controls autoplay playsinline class="max-w-full max-h-full rounded-2xl shadow-2xl"></video></template>
                                    <div x-show="current?.caption" class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/90 to-transparent rounded-b-2xl"><p class="text-white text-sm font-medium text-center" x-text="current?.caption"></p></div>
                                </div>
                                <button x-show="currentIndex>0" @click="prevStory()" class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-white z-50"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg></button>
                                <button x-show="currentIndex < stories.length-1" @click="nextStory()" class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-white z-50"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></button>
                            </div>
                        </div>
                    </div>

                    <!-- Gallery Widget -->
                    @if($coverPhotos->count() > 0)
                    @php $galleryPhotos = $coverPhotos->take(6); @endphp
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden" x-data="{ photos: {{ json_encode($galleryPhotos->values()->toArray()) }}, selectedPhoto: null, selectedIndex: 0 }" @keydown.escape.window="selectedPhoto = null" @keydown.arrow-right.window="selectedPhoto && selectedIndex < photos.length - 1 && (selectedIndex++, selectedPhoto = photos[selectedIndex])" @keydown.arrow-left.window="selectedPhoto && selectedIndex > 0 && (selectedIndex--, selectedPhoto = photos[selectedIndex])">
                        <div class="px-4 py-3 border-b border-gray-100 flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <h3 class="text-sm font-bold text-gray-900">{{ __('Photo Gallery') }}</h3>
                        </div>
                        <div class="p-4 grid grid-cols-2 gap-2">
                            @foreach($galleryPhotos as $index => $photo)
                            <button type="button" @click="selectedPhoto = photos[{{ $index }}]; selectedIndex = {{ $index }}" class="aspect-square rounded-xl overflow-hidden hover:opacity-90 transition focus:outline-none focus:ring-2 focus:ring-gray-900">
                                <img src="{{ $photo }}" class="w-full h-full object-cover">
                            </button>
                            @endforeach
                        </div>
                        
                        <!-- Lightbox -->
                        <div x-show="selectedPhoto" x-cloak class="fixed inset-0 z-[200] flex items-center justify-center bg-black/95 backdrop-blur-sm">
                            <button @click="selectedPhoto = null" class="absolute top-4 right-4 text-white/80 hover:text-white p-2 z-50"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                            <img :src="selectedPhoto" class="max-w-[90vw] max-h-[90vh] object-contain rounded-xl shadow-2xl">
                            <button x-show="selectedIndex > 0" @click="selectedIndex--; selectedPhoto = photos[selectedIndex]" class="absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white z-50"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg></button>
                            <button x-show="selectedIndex < photos.length - 1" @click="selectedIndex++; selectedPhoto = photos[selectedIndex]" class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white z-50"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></button>
                        </div>
                    </div>
                    @endif
                </aside>

            </div>
        </div>

    </div>

    @if(!$isOwner)
    <script>
        function userInteraction() {
            return {
                liked: false, followed: false, likeCount: null, followerCount: null, loading: false, userId: {{ $user->id }}, isLoggedIn: {{ auth()->check() ? 'true' : 'false' }},
                init() {
                    fetch(`/user/${this.userId}/interaction-status`).then(res => res.json()).then(data => {
                        this.liked = data.has_liked; this.followed = data.is_following; this.likeCount = data.likes_count; this.followerCount = data.followers_count;
                    }).catch(err => console.error('Failed to load interaction status:', err));
                },
                confirmLike() { if (!this.isLoggedIn) { window.location.href = '{{ route('login') }}'; return; } const action = this.liked ? '{{ __('Unlike this profile?') }}' : '{{ __('Like this profile?') }}'; if (confirm(action)) { this.toggleLike(); } },
                confirmFollow() { if (!this.isLoggedIn) { window.location.href = '{{ route('login') }}'; return; } const action = this.followed ? '{{ __('Unfollow this user?') }}' : '{{ __('Follow this user?') }}'; if (confirm(action)) { this.toggleFollow(); } },
                toggleLike() {
                    this.loading = true;
                    fetch(`/user/${this.userId}/toggle-like`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } })
                    .then(res => res.json()).then(data => { if (data.success) { this.liked = data.liked; this.likeCount = data.likes_count; } else { showToast(data.message || '{{ __('An error occurred') }}', 'error'); } }).catch(err => showToast('{{ __('An error occurred') }}', 'error')).finally(() => this.loading = false);
                },
                toggleFollow() {
                    this.loading = true;
                    fetch(`/user/${this.userId}/toggle-follow`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } })
                    .then(res => res.json()).then(data => { if (data.success) { this.followed = data.followed; this.followerCount = data.followers_count; } else { showToast(data.message || '{{ __('An error occurred') }}', 'error'); } }).catch(err => showToast('{{ __('An error occurred') }}', 'error')).finally(() => this.loading = false);
                }
            };
        }
    </script>
    @endif
</x-app-layout>
