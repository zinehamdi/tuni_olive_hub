@extends('layouts.app')

@section('title', __('varieties_article.title'))
@section('description', __('varieties_article.intro'))
@section('og_title', __('varieties_article.title'))
@section('og_description', __('varieties_article.intro'))
@section('og_image', asset('images/chetoui_leaf.png'))

@section('content')
<main class="pt-24 pb-16 bg-gray-50">
    <div class="container mx-auto px-4 max-w-4xl">
        
        <!-- Article Header -->
        <header class="text-center mb-12">
            <span class="text-[#6A8F3B] font-bold tracking-wider uppercase text-sm mb-4 block">{{ __('varieties_article.subtitle') }}</span>
            <h1 class="text-4xl md:text-5xl font-black text-gray-900 leading-tight mb-6">{{ __('varieties_article.title') }}</h1>
            <div class="w-24 h-1 bg-[#C8A356] mx-auto rounded-full"></div>
        </header>

        <!-- Article Content -->
        <article class="bg-white rounded-3xl shadow-xl p-8 md:p-12 prose prose-lg prose-olive max-w-none" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
            
            <p class="text-xl text-gray-700 leading-relaxed mb-8">
                {{ __('varieties_article.intro') }}
            </p>

            <h2 class="text-2xl font-bold text-[#6A8F3B] mt-10 mb-4 {{ app()->getLocale() == 'ar' ? 'border-r-4 pr-4' : 'border-l-4 pl-4' }} border-[#C8A356]">{{ __('varieties_article.why_tunisia') }}</h2>
            <p class="mb-6 text-gray-700">
                {{ __('varieties_article.why_tunisia_text') }}
            </p>

            <h2 class="text-2xl font-bold text-[#6A8F3B] mt-10 mb-6 {{ app()->getLocale() == 'ar' ? 'border-r-4 pr-4' : 'border-l-4 pl-4' }} border-[#C8A356]">{{ __('varieties_article.famous_varieties') }}</h2>

            <!-- Varieties Table -->
            <div class="overflow-x-auto my-8">
                <table class="w-full {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }} border-collapse bg-white rounded-xl overflow-hidden shadow-sm">
                    <thead class="bg-[#6A8F3B] text-white">
                        <tr>
                            <th class="p-4 font-bold">{{ __('varieties_article.table_image') }}</th>
                            <th class="p-4 font-bold">{{ __('varieties_article.table_name_ar') }}</th>
                            <th class="p-4 font-bold">{{ __('varieties_article.table_name_en') }}</th>
                            <th class="p-4 font-bold">{{ __('varieties_article.table_name_fr') }}</th>
                            <th class="p-4 font-bold">{{ __('varieties_article.table_features') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 align-middle">
                                <img src="{{ asset('images/chemlali_leaf.png') }}" alt="Chemlali" class="w-16 h-16 object-cover rounded-full border-2 border-[#C8A356]">
                            </td>
                            <td class="p-4 font-bold text-gray-900">الشملالي</td>
                            <td class="p-4 text-gray-600">Chemlali</td>
                            <td class="p-4 text-gray-600">Chemlali</td>
                            <td class="p-4 text-sm text-gray-700">{{ __('varieties_article.chemlali_features') }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 align-middle">
                                <img src="{{ asset('images/chetoui_leaf.png') }}" alt="Chetoui" class="w-16 h-16 object-cover rounded-full border-2 border-[#C8A356]">
                            </td>
                            <td class="p-4 font-bold text-gray-900">الشتوي</td>
                            <td class="p-4 text-gray-600">Chetoui</td>
                            <td class="p-4 text-gray-600">Chétoui</td>
                            <td class="p-4 text-sm text-gray-700">{{ __('varieties_article.chetoui_features') }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 align-middle">
                                <img src="{{ asset('images/oueslati_leaf.png') }}" alt="Oueslati" class="w-16 h-16 object-cover rounded-full border-2 border-[#C8A356]">
                            </td>
                            <td class="p-4 font-bold text-gray-900">الوسلاتي</td>
                            <td class="p-4 text-gray-600">Oueslati</td>
                            <td class="p-4 text-gray-600">Oueslati</td>
                            <td class="p-4 text-sm text-gray-700">{{ __('varieties_article.oueslati_features') }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition bg-gray-50/50">
                            <td class="p-4 align-middle">
                                <img src="{{ asset('images/zalmati_leaf.png') }}" alt="Zalmati" class="w-16 h-16 object-cover rounded-full border-2 border-[#C8A356]">
                            </td>
                            <td class="p-4 font-bold text-gray-900">الزلماتي</td>
                            <td class="p-4 text-gray-600">Zalmati</td>
                            <td class="p-4 text-gray-600">Zalmati</td>
                            <td class="p-4 text-sm text-gray-700">{{ __('varieties_article.zalmati_features') }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 align-middle">
                                <img src="{{ asset('images/zarazi_leaf.png') }}" alt="Zarazi" class="w-16 h-16 object-cover rounded-full border-2 border-[#C8A356]">
                            </td>
                            <td class="p-4 font-bold text-gray-900">الزرازي</td>
                            <td class="p-4 text-gray-600">Zarazi</td>
                            <td class="p-4 text-gray-600">Zarazi</td>
                            <td class="p-4 text-sm text-gray-700">{{ __('varieties_article.zarazi_features') }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition bg-gray-50/50">
                            <td class="p-4 align-middle">
                                <img src="{{ asset('images/barouni_leaf.png') }}" alt="Barouni" class="w-16 h-16 object-cover rounded-full border-2 border-[#C8A356]">
                            </td>
                            <td class="p-4 font-bold text-gray-900">الباروني</td>
                            <td class="p-4 text-gray-600">Barouni</td>
                            <td class="p-4 text-gray-600">Barouni</td>
                            <td class="p-4 text-sm text-gray-700">{{ __('varieties_article.barouni_features') }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 align-middle">
                                <img src="{{ asset('images/meski_leaf.png') }}" alt="Meski" class="w-16 h-16 object-cover rounded-full border-2 border-[#C8A356]">
                            </td>
                            <td class="p-4 font-bold text-gray-900">مسكي</td>
                            <td class="p-4 text-gray-600">Meski</td>
                            <td class="p-4 text-gray-600">Meski</td>
                            <td class="p-4 text-sm text-gray-700">{{ __('varieties_article.meski_features') }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition bg-gray-50/50">
                            <td class="p-4 align-middle">
                                <img src="{{ asset('images/chemchali_leaf.png') }}" alt="Chemchali" class="w-16 h-16 object-cover rounded-full border-2 border-[#C8A356]">
                            </td>
                            <td class="p-4 font-bold text-gray-900">الشمشالي</td>
                            <td class="p-4 text-gray-600">Chemchali</td>
                            <td class="p-4 text-gray-600">Chemchali</td>
                            <td class="p-4 text-sm text-gray-700">{{ __('varieties_article.chemchali_features') }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 align-middle">
                                <img src="{{ asset('images/gerboui_leaf.png') }}" alt="Gerboui" class="w-16 h-16 object-cover rounded-full border-2 border-[#C8A356]">
                            </td>
                            <td class="p-4 font-bold text-gray-900">الجربوعي</td>
                            <td class="p-4 text-gray-600">Gerboui</td>
                            <td class="p-4 text-gray-600">Gerboui</td>
                            <td class="p-4 text-sm text-gray-700">{{ __('varieties_article.gerboui_features') }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition bg-gray-50/50">
                            <td class="p-4 align-middle">
                                <img src="{{ asset('images/sayali_leaf.png') }}" alt="Sayali" class="w-16 h-16 object-cover rounded-full border-2 border-[#C8A356]">
                            </td>
                            <td class="p-4 font-bold text-gray-900">السيالي</td>
                            <td class="p-4 text-gray-600">Sayali</td>
                            <td class="p-4 text-gray-600">Sayali</td>
                            <td class="p-4 text-sm text-gray-700">{{ __('varieties_article.sayali_features') }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 align-middle">
                                <img src="{{ asset('images/sahli_leaf.png') }}" alt="Sahli" class="w-16 h-16 object-cover rounded-full border-2 border-[#C8A356]">
                            </td>
                            <td class="p-4 font-bold text-gray-900">الساحلي</td>
                            <td class="p-4 text-gray-600">Sahli</td>
                            <td class="p-4 text-gray-600">Sahli</td>
                            <td class="p-4 text-sm text-gray-700">{{ __('varieties_article.sahli_features') }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition bg-gray-50/50">
                            <td class="p-4 align-middle">
                                <img src="{{ asset('images/fakhari_leaf.png') }}" alt="Fakhari" class="w-16 h-16 object-cover rounded-full border-2 border-[#C8A356]">
                            </td>
                            <td class="p-4 font-bold text-gray-900">الفخاري</td>
                            <td class="p-4 text-gray-600">Fakhari</td>
                            <td class="p-4 text-gray-600">Fakhari</td>
                            <td class="p-4 text-sm text-gray-700">{{ __('varieties_article.fakhari_features') }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 align-middle">
                                <img src="{{ asset('images/tounsi_leaf.png') }}" alt="Tounsi" class="w-16 h-16 object-cover rounded-full border-2 border-[#C8A356]">
                            </td>
                            <td class="p-4 font-bold text-gray-900">التونسي</td>
                            <td class="p-4 text-gray-600">Tounsi</td>
                            <td class="p-4 text-gray-600">Tounsi</td>
                            <td class="p-4 text-sm text-gray-700">{{ __('varieties_article.tounsi_features') }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition bg-gray-50/50">
                            <td class="p-4 align-middle">
                                <img src="{{ asset('images/neb_jmel_leaf.png') }}" alt="Neb Jmel" class="w-16 h-16 object-cover rounded-full border-2 border-[#C8A356]">
                            </td>
                            <td class="p-4 font-bold text-gray-900">ناب الجمل</td>
                            <td class="p-4 text-gray-600">Neb Jmel</td>
                            <td class="p-4 text-gray-600">Neb Jmel</td>
                            <td class="p-4 text-sm text-gray-700">{{ __('varieties_article.neb_jmel_features') }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 align-middle">
                                <img src="{{ asset('images/rkhami_leaf.png') }}" alt="Rkhami" class="w-16 h-16 object-cover rounded-full border-2 border-[#C8A356]">
                            </td>
                            <td class="p-4 font-bold text-gray-900">الرخامي</td>
                            <td class="p-4 text-gray-600">Rkhami</td>
                            <td class="p-4 text-gray-600">Rkhami</td>
                            <td class="p-4 text-sm text-gray-700">{{ __('varieties_article.rkhami_features') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <h3 class="text-xl font-bold text-gray-900 mt-8 mb-3">{{ __('varieties_article.chemlali_title') }}</h3>
            <p class="mb-4 text-gray-700">
                {{ __('varieties_article.chemlali_text') }}
            </p>

            <h3 class="text-xl font-bold text-gray-900 mt-8 mb-3">{{ __('varieties_article.chetoui_title') }}</h3>
            <p class="mb-4 text-gray-700">
                {{ __('varieties_article.chetoui_text') }}
            </p>

            <h3 class="text-xl font-bold text-gray-900 mt-8 mb-3">{{ __('varieties_article.oueslati_title') }}</h3>
            <p class="mb-4 text-gray-700">
                {{ __('varieties_article.oueslati_text') }}
            </p>

            <h3 class="text-xl font-bold text-gray-900 mt-8 mb-3">{{ __('varieties_article.meski_title') }}</h3>
            <p class="mb-4 text-gray-700">
                {{ __('varieties_article.meski_text') }}
            </p>

            <h2 class="text-2xl font-bold text-[#6A8F3B] mt-10 mb-4 {{ app()->getLocale() == 'ar' ? 'border-r-4 pr-4' : 'border-l-4 pl-4' }} border-[#C8A356]">{{ __('varieties_article.conclusion_title') }}</h2>
            <p class="mb-6 text-gray-700">
                {{ __('varieties_article.conclusion_text') }}
            </p>

            <!-- Call to Action -->
            <div class="mt-12 p-8 bg-gradient-to-r from-[#6A8F3B]/10 to-[#C8A356]/10 rounded-2xl text-center border border-[#6A8F3B]/20">
                <h3 class="text-2xl font-bold text-gray-900 mb-4">{{ __('varieties_article.cta_title') }}</h3>
                <p class="text-gray-700 mb-6">{{ __('varieties_article.cta_text') }}</p>
                <a href="{{ route('home') }}#products" class="inline-flex items-center gap-2 px-8 py-4 bg-[#6A8F3B] text-white rounded-full font-bold shadow-lg hover:bg-[#5a7a2f] transition-all hover:-translate-y-1">
                    <span>{{ __('varieties_article.cta_button') }}</span>
                    <svg class="w-5 h-5 {{ app()->getLocale() == 'ar' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                </a>
            </div>

        </article>
    </div>
</main>
@endsection
