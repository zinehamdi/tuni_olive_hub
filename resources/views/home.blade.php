@php
    // Safe route helper: if a named route doesn't exist yet, use a sensible fallback URL.
    $router = app('router');
    $safeRoute = function (string $name, string $fallback, array $params = []) use ($router) {
        return $router->has($name) ? route($name, $params) : url($fallback);
    };
@endphp

@push('head')
    <meta name="bg-slides" content='["/images/HighTidebg.jpeg","/images/HighTidebg.jpeg"]'>
    <meta name="bg-interval" content="10000">
@endpush

<div dir="rtl" class="min-h-screen bg-white text-gray-900">

    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur border-b border-gray-200">
        <div id="prices-bar"
             class="w-full text-xs md:text-sm px-2 md:px-6 py-2 bg-emerald-600 text-white flex flex-wrap items-center gap-x-3 md:gap-x-6 gap-y-1 justify-center md:justify-start">
            <span class="font-semibold text-xs md:text-sm">{{ __('Today\'s Prices') }}:</span>
            <span id="price-global" class="opacity-90 text-xs md:text-sm whitespace-nowrap">
                <span class="font-medium">{{ __('Global Oil') }}</span> ({{ __('ton') }}): <span class="price-value">—</span>
            </span>
            <span id="price-baz" class="opacity-90 text-xs md:text-sm whitespace-nowrap">
                <span class="font-medium">{{ __('Tunisia Baz') }}</span> ({{ __('kg') }}): <span class="price-value">—</span>
            </span>
            <span id="price-organic" class="opacity-90 text-xs md:text-sm whitespace-nowrap">
                <span class="font-medium">{{ __('Organic') }}</span> ({{ __('liter') }}): <span class="price-value">—</span>
            </span>
            <span id="price-date" class="opacity-90 text-xs md:text-sm whitespace-nowrap md:ms-auto">{{ __('Date') }}: <span class="date-value">—</span></span>
        </div>

        <nav class="px-4 md:px-6 py-3">
            <div class="max-w-7xl mx-auto flex items-center justify-between gap-4">
                <a href="{{ url('/') }}" class="flex items-center gap-2 group">
                    <span class="inline-block w-9 h-9 rounded-xl bg-amber-700/90 group-hover:opacity-90 transition"></span>
                    <span class="text-lg md:text-xl font-bold text-gray-800">منصّة الزيت التونسي</span>
                </a>

                <div class="flex items-center gap-3 md:gap-4">
                    <div class="flex items-center gap-1 md:gap-2">
                        <a class="px-3 py-1 rounded-full bg-blue-600 text-white hover:opacity-90 focus:ring focus:outline-none"
                           href="{{ route('lang.switch', ['locale' => 'ar']) }}">AR</a>
                        <a class="px-3 py-1 rounded-full bg-blue-600 text-white hover:opacity-90 focus:ring focus:outline-none"
                           href="{{ route('lang.switch', ['locale' => 'fr']) }}">FR</a>
                        <a class="px-3 py-1 rounded-full bg-blue-600 text-white hover:opacity-90 focus:ring focus:outline-none"
                           href="{{ route('lang.switch', ['locale' => 'en']) }}">EN</a>
                    </div>

                    @auth
                        @if(auth()->user()->role === 'carrier')
                            <a href="{{ route('mobile.trip') }}"
                               class="px-4 py-2 rounded-xl bg-blue-600 text-white hover:opacity-90 focus:ring focus:outline-none">
                                رحلتي النشطة (للنّاقل)
                            </a>
                        @endif
                        <a href="{{ $safeRoute('dashboard', '/dashboard') }}"
                           class="px-4 py-2 rounded-xl bg-emerald-600 text-white hover:opacity-90 focus:ring focus:outline-none">
                            لوحة التحكم
                        </a>
                        <form method="POST" action="{{ $safeRoute('logout', '/logout') }}" class="inline">
                            @csrf
                            <button type="submit"
                                    class="px-4 py-2 rounded-xl bg-rose-600 text-white hover:opacity-90 focus:ring focus:outline-none">
                                خروج
                            </button>
                        </form>
                    @endauth
                    @guest
                        <a href="{{ $safeRoute('login', '/login') }}"
                           class="px-4 py-2 rounded-xl bg-emerald-600 text-white hover:opacity-90 focus:ring focus:outline-none">
                            تسجيل الدخول
                        </a>
                        <a href="{{ $safeRoute('register', '/register') }}"
                           class="px-4 py-2 rounded-xl bg-amber-700 text-white hover:opacity-90 focus:ring focus:outline-none">
                            إنشاء حساب
                        </a>
                    @endguest
                </div>
            </div>
        </nav>
    </header>

    <main class="relative z-0 pt-6 md:pt-10">
        <div aria-hidden="true"
             class="pointer-events-none absolute inset-0 -z-10 bg-gradient-to-b from-amber-50 via-white to-white"></div>

        <section class="px-4 md:px-6" style="background-image:url('/images/dealbackground.png');background-size:cover;background-position:center;background-repeat:no-repeat;">
            <div class="max-w-7xl mx-auto py-10 md:py-16">
                <div class="grid md:grid-cols-2 gap-8 md:gap-12 items-center">
                    <div class="space-y-4 text-right">
                        <h1 class="text-2xl md:text-4xl font-extrabold text-gray-900 leading-snug">
                            بيع وشراء زيت الزيتون التونسي بسهولة وشفافية
                        </h1>
                        <p class="text-gray-700 md:text-lg">
                            اعرض زيتك أو اطلب عولة بمنتهى السهولة، وتابع عروض التصدير المتميزة. المنصّة تجمع المنتجين،
                            الوسطاء، والمصدرين بواجهات حديثة وتجربة سلسة.
                        </p>

                        <div class="flex flex-wrap gap-3 md:gap-4 justify-end">
                            <a href="{{ $safeRoute('listings.create', '/listings/create') }}"
                               class="px-4 py-3 rounded-xl bg-emerald-600 text-white hover:opacity-90 focus:ring focus:outline-none">
                                اعرض زيتك/زيتونك اليوم
                            </a>

                            <a href="{{ $safeRoute('orders.requestAoula', '/orders/request-aoula') }}"
                               class="px-4 py-3 rounded-xl bg-amber-700 text-white hover:opacity-90 focus:ring focus:outline-none">
                                اطلب عولة
                            </a>

                            <a href="{{ $safeRoute('catalog', '/catalog') }}"
                               class="px-4 py-3 rounded-xl bg-blue-600 text-white hover:opacity-90 focus:ring focus:outline-none">
                                {{ __('Premium Export Catalog') }}
                            </a>
                        </div>
                    </div>

                    <div class="relative">
                        <div aria-hidden="true"
                             class="pointer-events-none absolute inset-0 rounded-3xl bg-emerald-50/60 -z-10"></div>
                        <div class="rounded-3xl border border-gray-200 p-6 bg-white shadow-sm">
                            <h2 class="text-xl font-bold text-gray-900 mb-4">لمحة سريعة</h2>
                            <ul class="space-y-2 text-gray-700">
                                <li>• تتبّع الطلبات والشحنات والمستندات.</li>
                                <li>• تقييمات موثوقة وترتيب للأفضل.</li>
                                <li>• عروض تصدير منتقاة للأسواق الخليجية.</li>
                            </ul>

                            <div class="mt-6 flex flex-wrap gap-3 justify-end">
                                <a href="{{ $safeRoute('public.sitemap', '/sitemap.xml') }}"
                                   class="px-4 py-3 rounded-xl bg-gray-800 text-white hover:opacity-90 focus:ring focus:outline-none">
                                    خريطة الموقع
                                </a>
                                <a href="{{ $safeRoute('public.rss', '/feed.rss') }}"
                                   class="px-4 py-3 rounded-xl bg-gray-700 text-white hover:opacity-90 focus:ring focus:outline-none">
                                    آخر الإضافات (RSS)
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <section class="mt-12 md:mt-16">
                    <h3 class="text-xl md:text-2xl font-extrabold text-gray-900 mb-6 text-right">
                        ليش المنصّة؟
                    </h3>
                    <div class="grid md:grid-cols-3 gap-6">
                        <article class="rounded-2xl border border-gray-200 p-5 bg-white">
                            <h4 class="font-bold text-gray-900 mb-2">سهولة الاستخدام</h4>
                            <p class="text-gray-700">واجهات بسيطة وواضحة لكل المستخدمين.</p>
                        </article>
                        <article class="rounded-2xl border border-gray-200 p-5 bg-white">
                            <h4 class="font-bold text-gray-900 mb-2">شفافية التسعير</h4>
                            <p class="text-gray-700">أسعار اليوم في متناولك دوماً.</p>
                        </article>
                        <article class="rounded-2xl border border-gray-200 p-5 bg-white">
                            <h4 class="font-bold text-gray-900 mb-2">جاهزية للتصدير</h4>
                            <p class="text-gray-700">عروض مختارة ومحتوى تسويقي جاهز.</p>
                        </article>
                    </div>
                </section>
            </div>
        </section>
    </main>

    <footer class="border-t border-gray-200 px-4 md:px-6 py-6 text-center text-sm text-gray-600">
        © {{ now()->year }} منصّة الزيت التونسي. جميع الحقوق محفوظة.
    </footer>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    const base = '{{ url('/') }}';
    const url = base + '/api/v1/prices/today';
    const sel = id => document.getElementById(id);
    const locale = '{{ app()->getLocale() }}';

    try {
        const resp = await fetch(url, { headers: { 'Accept': 'application/json' }});
        if (!resp.ok) return;
        const json = await resp.json();
        const d = json?.data || {};

        // Update only the price values, not the labels
        if (d.global_oil_usd_ton != null) {
            const priceValue = sel('price-global').querySelector('.price-value');
            if (priceValue) priceValue.textContent = Number(d.global_oil_usd_ton).toLocaleString(locale === 'ar' ? 'ar-TN' : locale === 'fr' ? 'fr-FR' : 'en-US');
        }
        if (d.tunis_baz_tnd_kg != null) {
            const priceValue = sel('price-baz').querySelector('.price-value');
            if (priceValue) priceValue.textContent = Number(d.tunis_baz_tnd_kg).toLocaleString(locale === 'ar' ? 'ar-TN' : locale === 'fr' ? 'fr-FR' : 'en-US');
        }
        if (d.organic_tnd_l != null) {
            const priceValue = sel('price-organic').querySelector('.price-value');
            if (priceValue) priceValue.textContent = Number(d.organic_tnd_l).toLocaleString(locale === 'ar' ? 'ar-TN' : locale === 'fr' ? 'fr-FR' : 'en-US');
        }
        if (d.date) {
            const dateValue = sel('price-date').querySelector('.date-value');
            if (dateValue) dateValue.textContent = d.date;
        }
    } catch (e) {
        // Silent fallback; keep defaults
    }
});
</script>
@extends('layouts.app')

@section('content')
<div class="space-y-12 {{ app()->getLocale()==='ar' ? 'text-right' : '' }}">
    <x-hero-animation />
    
    <x-price-ticker />

    <div class="grid sm:grid-cols-3 gap-4">
        <a href="{{ $safeRoute('listings.create', '/listings/create') }}" class="block border rounded p-4 hover:shadow bg-[#6A8F3B] text-white text-center font-bold transition">اعرض زيتك/زيتونك اليوم</a>
        <a href="{{ $safeRoute('orders.requestAoula', '/orders/request-aoula') }}" class="block border rounded p-4 hover:shadow bg-[#C8A356] text-white text-center font-bold transition">اطلب عولة</a>
        <a href="{{ $safeRoute('catalog', '/catalog') }}" class="block border rounded p-4 hover:shadow bg-blue-600 text-white text-center font-bold transition">{{ __('Premium Export Catalog') }}</a>
    </div>

    <!-- Latest Products Section -->
    <div class="mt-8" id="products">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 text-right">أحدث المنتجات المعروضة</h2>
        
        @if(isset($featuredListings) && $featuredListings->count() > 0)
            <div class="grid md:grid-cols-3 gap-6">
                @foreach($featuredListings as $listing)
                    <div class="border rounded-lg overflow-hidden bg-white shadow-md hover:shadow-xl transition">
                        @if($listing->product)
                            <!-- Product Image -->
                            <div class="relative w-full aspect-video bg-gradient-to-br from-[#6A8F3B] to-[#C8A356] overflow-hidden">
                                @php
                                    // Try to get image from product media first, then listing media
                                    $productImage = null;
                                    if($listing->product->media && is_array($listing->product->media) && count($listing->product->media) > 0) {
                                        $productImage = $listing->product->media[0];
                                    } elseif($listing->media && is_array($listing->media) && count($listing->media) > 0) {
                                        $productImage = $listing->media[0];
                                    }
                                @endphp
                                
                                @if($productImage)
                                    <!-- Display actual product image -->
                                    <img src="{{ Storage::url($productImage) }}" 
                                         alt="{{ $listing->product->variety }}" 
                                         class="w-full h-full object-cover"
                                         loading="lazy">
                                    
                                    <!-- Product Type Badge -->
                                    @if($listing->product->type === 'oil')
                                        <span class="absolute top-3 right-3 bg-white/95 text-[#6A8F3B] px-3 py-1 rounded-full text-xs font-bold shadow-md">🫗 زيت زيتون</span>
                                    @else
                                        <span class="absolute top-3 right-3 bg-white/95 text-[#6A8F3B] px-3 py-1 rounded-full text-xs font-bold shadow-md">🫒 زيتون</span>
                                    @endif
                                @else
                                    <!-- Fallback to icon if no image -->
                                    <div class="flex items-center justify-center w-full h-full">
                                        <svg class="w-24 h-24 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            @if($listing->product->type === 'oil')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                                            @endif
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="p-4">
                                @php
                                    $cardProductType = $listing->product->type === 'olive' ? (app()->getLocale() === 'ar' ? 'زيتون' : __('Olives')) : (app()->getLocale() === 'ar' ? 'زيت زيتون' : __('Olive Oil'));
                                    $cardQuality = $listing->product->quality ? ' ' . $listing->product->quality : '';
                                    $cardCity = $listing->seller->addresses->first() ? (app()->getLocale() === 'ar' ? ' من ' : ' from ') . ($listing->seller->addresses->first()->governorate ?? '') : '';
                                @endphp
                                <div class="font-bold text-lg text-gray-900 mb-2 leading-tight">
                                    {{ $cardProductType }} {{ $listing->product->variety }}{{ $cardQuality }}{{ $cardCity }}
                                </div>
                                
                                <div class="flex items-center gap-2 mb-3 flex-wrap">
                                    <span class="px-2 py-1 rounded-full bg-[#6A8F3B] text-white text-xs font-semibold flex items-center gap-1">
                                        <span>{{ $listing->seller?->role === 'farmer' ? '🌿' : ($listing->seller?->role === 'mill' ? '🏭' : ($listing->seller?->role === 'packer' ? '📦' : '👤')) }}</span>
                                        <span>{{ $listing->seller?->farm_name ?? $listing->seller?->mill_name ?? $listing->seller?->company_name ?? $listing->seller?->name ?? __('Seller') }}</span>
                                    </span>
                                    @if($listing->product->quality)
                                        <span class="px-2 py-1 rounded-full bg-[#C8A356] text-white text-xs font-semibold">
                                            @if($listing->product->quality === 'bio')
                                                {{ __('بيولوجي (Bio)') }}
                                            @elseif($listing->product->quality === 'evoo')
                                                {{ __('بكر ممتاز (EVOO)') }}
                                            @elseif($listing->product->quality === 'virgin')
                                                {{ __('بكر (Virgin)') }}
                                            @elseif($listing->product->quality === 'raffinee')
                                                {{ __('مكرر (Raffinée)') }}
                                            @elseif($listing->product->quality === 'pomace')
                                                {{ __('زيت فيتورة (Pomace)') }}
                                            @else
                                                {{ $listing->product->quality }}
                                            @endif
                                        </span>
                                    @endif
                                </div>
                                
                                @if($listing->product->price)
                                    <div class="text-lg font-bold text-[#6A8F3B] mb-3">
                                        {{ number_format($listing->product->price, 2) }} دينار
                                    </div>
                                @endif
                                
                                <div class="text-sm text-gray-600 mb-3">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <span>{{ $listing->seller->name ?? 'بائع' }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 mt-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>{{ $listing->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                                
                                @if($listing->delivery_options)
                                    @php
                                        $__dloc = app()->getLocale();
                                        $__dlabels = $__dloc === 'fr' ? [
                                            'pickup' => '📍 Sur place',
                                            'local_delivery' => '🚚 Livraison',
                                            'export' => '🚢 Export',
                                            'carrier' => '📦 Transporteur',
                                        ] : ($__dloc === 'en' ? [
                                            'pickup' => '📍 Pickup',
                                            'local_delivery' => '🚚 Local Delivery',
                                            'export' => '🚢 Export',
                                            'carrier' => '📦 Carrier',
                                        ] : [
                                            'pickup' => '📍 استلام',
                                            'local_delivery' => '🚚 توصيل',
                                            'export' => '🚢 تصدير',
                                            'carrier' => '📦 ناقل',
                                        ]);
                                        $__draww = $listing->delivery_options;
                                        $__dkeys = is_array($__draww) ? $__draww
                                            : (is_string($__draww) ? (json_decode($__draww, true) ?: preg_split('/\s*,\s*/', $__draww, -1, PREG_SPLIT_NO_EMPTY)) : []);
                                    @endphp
                                    <div class="flex flex-wrap gap-1 mb-3">
                                        @foreach($__dkeys as $__dk)
                                            @if(isset($__dlabels[trim($__dk)]))
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                                    {{ $__dlabels[trim($__dk)] }}
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif

                                <div class="mt-2">
                                    <a href="{{ route('listings.show', $listing) }}" class="block w-full text-center px-4 py-2 bg-[#6A8F3B] text-white rounded-lg hover:bg-[#5a7a2f] transition font-semibold">
                                        {{ app()->getLocale() === 'fr' ? 'Voir les détails' : (app()->getLocale() === 'en' ? 'View Details' : 'عرض التفاصيل') }}
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12 bg-gray-50 rounded-lg">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <p class="text-gray-600 text-lg mb-4">لا توجد منتجات معروضة حالياً</p>
                <a href="{{ $safeRoute('listings.create', '/listings/create') }}" class="inline-block bg-[#6A8F3B] text-white font-bold py-2 px-6 rounded-xl hover:bg-[#5a7a2f] transition">
                    كن أول من يعرض منتجاته
                </a>
            </div>
        @endif
    </div>

    <x-awareness-toast type="confirm">✅ تأكيدك يُلزمك قانونياً. راجع التفاصيل.</x-awareness-toast>
    <x-awareness-toast type="trip">🚚 POD ضروري لتثبيت COD.</x-awareness-toast>
    <x-awareness-toast type="export">📜 وثائق صحيحة إلزامية. أي غش = حجب نهائي.</x-awareness-toast>
</div>
@endsection
