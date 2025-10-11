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
             class="w-full text-sm px-4 md:px-6 py-2 bg-emerald-600 text-white flex flex-wrap items-center gap-x-6 gap-y-1">
            <span class="font-semibold">أسعار اليوم:</span>
            <span id="price-global" class="opacity-90">الزيت العالمي (طن): —</span>
            <span id="price-baz" class="opacity-90">باز تونس (كغ): —</span>
            <span id="price-organic" class="opacity-90">عضوي (لتر): —</span>
            <span id="price-date" class="ms-auto opacity-90">التاريخ: —</span>
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

                            <a href="{{ $safeRoute('gulf.catalog', '/public/gulf/catalog') }}"
                               class="px-4 py-3 rounded-xl bg-blue-600 text-white hover:opacity-90 focus:ring focus:outline-none">
                                شوف عروض التصدير
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
                                <a href="{{ $safeRoute('public.sitemap', '/public/sitemap.xml') }}"
                                   class="px-4 py-3 rounded-xl bg-gray-800 text-white hover:opacity-90 focus:ring focus:outline-none">
                                    خريطة الموقع
                                </a>
                                <a href="{{ $safeRoute('public.rss', '/public/feed.rss') }}"
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

    try {
        const resp = await fetch(url, { headers: { 'Accept': 'application/json' }});
        if (!resp.ok) return;
        const json = await resp.json();
        const d = json?.data || {};

        if (d.global_oil_usd_ton != null) sel('price-global').textContent = `الزيت العالمي (طن): ${Number(d.global_oil_usd_ton).toLocaleString('ar-TN')}`;
        if (d.tunis_baz_tnd_kg != null) sel('price-baz').textContent = `باز تونس (كغ): ${Number(d.tunis_baz_tnd_kg).toLocaleString('ar-TN')}`;
        if (d.organic_tnd_l != null) sel('price-organic').textContent = `عضوي (لتر): ${Number(d.organic_tnd_l).toLocaleString('ar-TN')}`;
        if (d.date) sel('price-date').textContent = `التاريخ: ${d.date}`;
    } catch (e) {
        // Silent fallback; keep defaults
    }
});
</script>
@extends('layouts.app')

@section('content')
<div class="space-y-6 {{ app()->getLocale()==='ar' ? 'text-right' : '' }}">
    <x-price-ticker />

    <div class="grid sm:grid-cols-3 gap-4">
        <a href="#" class="block border rounded p-4 hover:shadow bg-olive text-white text-center">اعرض زيتك/زيتونك اليوم</a>
        <a href="#" class="block border rounded p-4 hover:shadow bg-gold text-white text-center">اطلب عولة</a>
        <a href="#" class="block border rounded p-4 hover:shadow bg-sky text-white text-center">شوف عروض التصدير</a>
    </div>

    <div class="grid md:grid-cols-3 gap-4">
                <div class="border rounded overflow-hidden bg-white">
                    <img src="/images/zitchemlali.PNG" alt="زيت زيتون شملالي" class="w-full aspect-video object-cover" />
                    <div class="p-3 ">
                        <div class="font-semibold">زيت زيتون شملالي</div>
                        <div class="text-sm text-gray-600 flex items-center gap-2 ">
                            <span class="px-2 py-0.5 rounded bg-olive text-white text-xs">chemlali</span>
                            <span class="px-2 py-0.5 rounded bg-gold text-white text-xs">premium</span>
                        </div>
                        <div class="mt-2 font-medium text-olive">18.500 TND/L</div>
                        <div class="mt-2">
                            <a href="#" class="inline-block px-3 py-1 bg-sky text-white rounded text-sm">تفاصيل</a>
                        </div>
                    </div>
                </div>
                <div class="border rounded overflow-hidden bg-white">
                    <img src="/images/zitounchamal.jpg" alt="زيتون شمالي" class="w-full aspect-video object-cover" />
                    <div class="p-3 ">
                        <div class="font-semibold">زيتون شمالي</div>
                        <div class="text-sm text-gray-600 flex items-center gap-2 ">
                            <span class="px-2 py-0.5 rounded bg-olive text-white text-xs">north</span>
                            <span class="px-2 py-0.5 rounded bg-gold text-white text-xs">foodservice</span>
                        </div>
                        <div class="mt-2 font-medium text-olive">2.800 TND/Kg</div>
                        <div class="mt-2">
                            <a href="#" class="inline-block px-3 py-1 bg-sky text-white rounded text-sm">تفاصيل</a>
                        </div>
                    </div>
                </div>
                <div class="border rounded overflow-hidden bg-white">
                    <img src="/images/zitzitoun.png" alt="زيت زيتون" class="w-full aspect-video object-cover" />
                    <div class="p-3 ">
                        <div class="font-semibold">زيت زيتون</div>
                        <div class="text-sm text-gray-600 flex items-center gap-2 ">
                            <span class="px-2 py-0.5 rounded bg-olive text-white text-xs">south</span>
                            <span class="px-2 py-0.5 rounded bg-gold text-white text-xs">medium</span>
                        </div>
                        <div class="mt-2 font-medium text-olive">16.900 TND/L</div>
                        <div class="mt-2">
                            <a href="#" class="inline-block px-3 py-1 bg-sky text-white rounded text-sm">تفاصيل</a>
                        </div>
                    </div>
                </div>
    </div>

    <x-awareness-toast type="confirm">✅ تأكيدك يُلزمك قانونياً. راجع التفاصيل.</x-awareness-toast>
    <x-awareness-toast type="trip">🚚 POD ضروري لتثبيت COD.</x-awareness-toast>
    <x-awareness-toast type="export">📜 وثائق صحيحة إلزامية. أي غش = حجب نهائي.</x-awareness-toast>
</div>
@endsection
