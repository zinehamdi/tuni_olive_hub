@extends('layouts.app')

@section('content')
<div dir="rtl" class="min-h-screen bg-gradient-to-b from-gray-50 to-white" x-data="marketplace">
    
    <!-- Hero Section with Search -->
    <section class="relative bg-gradient-to-br from-[#6A8F3B] to-[#5a7a2f] text-white py-16 px-4 overflow-hidden">
        <!-- Background Image -->
        <div class="absolute inset-0 bg-cover bg-no-repeat" style="background-image: url('{{ asset('images/dealbackground.png') }}'); background-position: center 30%;"></div>
        <!-- Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-br from-[#6A8F3B]/40 to-[#5a7a2f]/40"></div>
        <!-- Dark Overlay -->
        <div class="absolute inset-0 bg-black/10"></div>
        <div class="max-w-7xl mx-auto relative z-10">
            <div class="text-center mb-8">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">منصة زيت الزيتون التونسي</h1>
                <p class="text-xl text-white/90 mb-8">اكتشف أفضل العروض من المنتجين المباشرين</p>
            </div>

            <!-- Search Bar -->
            <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-2xl p-2">
                <div class="flex flex-col md:flex-row gap-2">
                    <div class="flex-1 relative">
                        <input type="text" 
                               x-model="searchQuery"
                               @input="filterListings"
                               placeholder="ابحث عن المنتج (زيت، زيتون، شملالي...)"
                               class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-[#6A8F3B] focus:outline-none text-gray-900">
                        <svg class="absolute left-4 top-4 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <button @click="filterListings" class="px-8 py-3 bg-[#6A8F3B] text-white rounded-xl hover:bg-[#5a7a2f] transition font-bold">
                        بحث
                    </button>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8 max-w-4xl mx-auto">
                <div class="bg-white/10 backdrop-blur rounded-xl p-4 text-center">
                    <div class="text-3xl font-bold" x-text="totalListings"></div>
                    <div class="text-sm text-white/80">إعلان نشط</div>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-xl p-4 text-center">
                    <div class="text-3xl font-bold" x-text="oilCount"></div>
                    <div class="text-sm text-white/80">زيت زيتون</div>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-xl p-4 text-center">
                    <div class="text-3xl font-bold" x-text="oliveCount"></div>
                    <div class="text-sm text-white/80">زيتون</div>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-xl p-4 text-center">
                    <div class="text-3xl font-bold" x-text="filteredListings.length"></div>
                    <div class="text-sm text-white/80">نتيجة البحث</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="max-w-7xl mx-auto px-4 py-12">
        <div class="flex flex-col lg:flex-row gap-6">
            
            <!-- Sidebar Filters -->
            <aside class="lg:w-64 flex-shrink-0">
                <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-4 max-h-[calc(100vh-2rem)] overflow-y-auto">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-[#6A8F3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        تصفية النتائج
                    </h3>

                    <!-- Product Type Filter -->
                    <div class="mb-6">
                        <label class="block font-bold text-gray-900 mb-3">نوع المنتج</label>
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" x-model="filters.type" value="all" @change="filterListings" class="text-[#6A8F3B] focus:ring-[#6A8F3B]">
                                <span>الكل</span>
                                <span class="mr-auto text-sm text-gray-500" x-text="'(' + totalListings + ')'"></span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" x-model="filters.type" value="oil" @change="filterListings" class="text-[#6A8F3B] focus:ring-[#6A8F3B]">
                                <span>زيت زيتون</span>
                                <span class="mr-auto text-sm text-gray-500" x-text="'(' + oilCount + ')'"></span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" x-model="filters.type" value="olive" @change="filterListings" class="text-[#6A8F3B] focus:ring-[#6A8F3B]">
                                <span>زيتون</span>
                                <span class="mr-auto text-sm text-gray-500" x-text="'(' + oliveCount + ')'"></span>
                            </label>
                        </div>
                    </div>

                    <!-- Quality Filter -->
                    <div class="mb-6">
                        <label class="block font-bold text-gray-900 mb-3">الجودة</label>
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" x-model="filters.qualities" value="premium" @change="filterListings" class="text-[#6A8F3B] focus:ring-[#6A8F3B] rounded">
                                <span>ممتاز (Premium)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" x-model="filters.qualities" value="extra" @change="filterListings" class="text-[#6A8F3B] focus:ring-[#6A8F3B] rounded">
                                <span>إضافي (Extra)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" x-model="filters.qualities" value="standard" @change="filterListings" class="text-[#6A8F3B] focus:ring-[#6A8F3B] rounded">
                                <span>عادي (Standard)</span>
                            </label>
                        </div>
                    </div>

                    <!-- Price Range Filter -->
                    <div class="mb-6">
                        <label class="block font-bold text-gray-900 mb-3">نطاق السعر</label>
                        <div class="space-y-3">
                            <div>
                                <input type="number" x-model="filters.priceMin" @input="filterListings" placeholder="من" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-[#6A8F3B] focus:border-transparent">
                            </div>
                            <div>
                                <input type="number" x-model="filters.priceMax" @input="filterListings" placeholder="إلى" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-[#6A8F3B] focus:border-transparent">
                            </div>
                        </div>
                    </div>

                    <!-- Sort Options -->
                    <div class="mb-6">
                        <label class="block font-bold text-gray-900 mb-3">ترتيب حسب</label>
                        <select x-model="filters.sortBy" @change="filterListings" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-[#6A8F3B] focus:border-transparent">
                            <option value="newest">الأحدث</option>
                            <option value="oldest">الأقدم</option>
                            <option value="price_low">السعر: من الأقل للأعلى</option>
                            <option value="price_high">السعر: من الأعلى للأقل</option>
                        </select>
                    </div>

                    <!-- Reset Button -->
                    <button @click="resetFilters" class="w-full px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-bold">
                        إعادة تعيين
                    </button>
                </div>
            </aside>

            <!-- Product Listings Grid -->
            <main class="flex-1">
                <!-- View Toggle & Results Count -->
                <div class="flex justify-between items-center mb-6">
                    <div class="text-gray-700">
                        <span class="font-bold" x-text="filteredListings.length"></span>
                        <span>نتيجة</span>
                    </div>
                    <div class="flex gap-2">
                        <button @click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'bg-[#6A8F3B] text-white' : 'bg-gray-200 text-gray-700'" class="p-2 rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                        </button>
                        <button @click="viewMode = 'list'" :class="viewMode === 'list' ? 'bg-[#6A8F3B] text-white' : 'bg-gray-200 text-gray-700'" class="p-2 rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Products Grid View -->
                <div x-show="viewMode === 'grid'" class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">
                    <template x-for="listing in filteredListings" :key="listing.id">
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                            <!-- Product Image -->
                            <div class="h-48 bg-gradient-to-br from-[#6A8F3B] to-[#C8A356] flex items-center justify-center relative">
                                <svg class="w-24 h-24 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path x-show="listing.product.type === 'oil'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    <path x-show="listing.product.type === 'olive'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                                </svg>
                                <div class="absolute top-3 right-3">
                                    <span class="px-3 py-1 rounded-full text-white text-xs font-bold bg-white/20 backdrop-blur" x-text="listing.product.type === 'olive' ? 'زيتون' : 'زيت زيتون'"></span>
                                </div>
                            </div>

                            <!-- Product Details -->
                            <div class="p-5">
                                <h3 class="text-xl font-bold text-gray-900 mb-2" x-text="listing.product.variety"></h3>
                                
                                <div class="flex items-center gap-2 mb-3 flex-wrap">
                                    <span x-show="listing.product.quality" class="px-2 py-1 rounded-full bg-[#C8A356] text-white text-xs font-semibold" x-text="listing.product.quality"></span>
                                    <span x-show="listing.status === 'active'" class="px-2 py-1 rounded-full bg-green-500 text-white text-xs font-semibold">نشط</span>
                                </div>

                                <div class="text-2xl font-bold text-[#6A8F3B] mb-4">
                                    <span x-text="Number(listing.product.price).toFixed(2)"></span>
                                    <span class="text-sm text-gray-600">دينار</span>
                                </div>

                                <div class="space-y-2 text-sm text-gray-600 mb-4">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <span x-text="listing.seller.name"></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span x-text="formatDate(listing.created_at)"></span>
                                    </div>
                                </div>

                                <div class="flex gap-2">
                                    <a :href="'/listings/' + listing.id" class="flex-1 text-center px-4 py-2 bg-[#6A8F3B] text-white rounded-lg hover:bg-[#5a7a2f] transition font-bold">
                                        عرض التفاصيل
                                    </a>
                                    <button class="px-4 py-2 bg-[#C8A356] text-white rounded-lg hover:bg-[#b08a3c] transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Products List View -->
                <div x-show="viewMode === 'list'" class="space-y-4">
                    <template x-for="listing in filteredListings" :key="listing.id">
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 flex">
                            <!-- Product Image -->
                            <div class="w-48 bg-gradient-to-br from-[#6A8F3B] to-[#C8A356] flex items-center justify-center flex-shrink-0">
                                <svg class="w-20 h-20 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path x-show="listing.product.type === 'oil'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    <path x-show="listing.product.type === 'olive'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                                </svg>
                            </div>

                            <!-- Product Details -->
                            <div class="flex-1 p-6 flex justify-between items-center">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <h3 class="text-2xl font-bold text-gray-900" x-text="listing.product.variety"></h3>
                                        <span class="px-3 py-1 rounded-full text-white text-xs font-bold bg-[#6A8F3B]" x-text="listing.product.type === 'olive' ? 'زيتون' : 'زيت زيتون'"></span>
                                        <span x-show="listing.product.quality" class="px-3 py-1 rounded-full bg-[#C8A356] text-white text-xs font-semibold" x-text="listing.product.quality"></span>
                                    </div>
                                    <div class="flex items-center gap-4 text-sm text-gray-600">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            <span x-text="listing.seller.name"></span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span x-text="formatDate(listing.created_at)"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-left">
                                    <div class="text-3xl font-bold text-[#6A8F3B] mb-4">
                                        <span x-text="Number(listing.product.price).toFixed(2)"></span>
                                        <span class="text-sm text-gray-600">دينار</span>
                                    </div>
                                    <div class="flex gap-2">
                                        <a :href="'/listings/' + listing.id" class="px-6 py-2 bg-[#6A8F3B] text-white rounded-lg hover:bg-[#5a7a2f] transition font-bold">
                                            عرض التفاصيل
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Empty State -->
                <div x-show="filteredListings.length === 0" class="text-center py-16">
                    <svg class="w-24 h-24 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-2xl font-bold text-gray-700 mb-2">لا توجد نتائج</h3>
                    <p class="text-gray-500 mb-6">جرب تغيير معايير البحث أو الفلترة</p>
                    <button @click="resetFilters" class="px-6 py-3 bg-[#6A8F3B] text-white rounded-xl hover:bg-[#5a7a2f] transition font-bold">
                        إعادة تعيين البحث
                    </button>
                </div>
            </main>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="bg-gradient-to-br from-[#6A8F3B] to-[#5a7a2f] text-white py-16 px-4 mt-12">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">هل لديك منتج للبيع؟</h2>
            <p class="text-xl text-white/90 mb-8">انضم لآلاف البائعين واعرض منتجك اليوم</p>
            <a href="{{ route('listings.create') }}" class="inline-block px-8 py-4 bg-white text-[#6A8F3B] rounded-xl hover:bg-gray-100 transition font-bold text-lg shadow-lg">
                أضف إعلانك مجاناً
            </a>
        </div>
    </section>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('marketplace', () => ({
        listings: @json($featuredListings ?? []),
        filteredListings: [],
        searchQuery: '',
        viewMode: 'grid',
        filters: {
            type: 'all',
            qualities: [],
            priceMin: '',
            priceMax: '',
            sortBy: 'newest'
        },

        init() {
            this.filteredListings = this.listings;
        },

        get totalListings() {
            return this.listings.length;
        },

        get oilCount() {
            return this.listings.filter(l => l.product?.type === 'oil').length;
        },

        get oliveCount() {
            return this.listings.filter(l => l.product?.type === 'olive').length;
        },

        filterListings() {
            let results = [...this.listings];

            // Search filter
            if (this.searchQuery.trim()) {
                const query = this.searchQuery.toLowerCase();
                results = results.filter(listing => 
                    listing.product?.variety?.toLowerCase().includes(query) ||
                    listing.product?.quality?.toLowerCase().includes(query) ||
                    listing.seller?.name?.toLowerCase().includes(query)
                );
            }

            // Type filter
            if (this.filters.type !== 'all') {
                results = results.filter(listing => listing.product?.type === this.filters.type);
            }

            // Quality filter
            if (this.filters.qualities.length > 0) {
                results = results.filter(listing => 
                    this.filters.qualities.includes(listing.product?.quality?.toLowerCase())
                );
            }

            // Price range filter
            if (this.filters.priceMin !== '') {
                results = results.filter(listing => 
                    Number(listing.product?.price) >= Number(this.filters.priceMin)
                );
            }
            if (this.filters.priceMax !== '') {
                results = results.filter(listing => 
                    Number(listing.product?.price) <= Number(this.filters.priceMax)
                );
            }

            // Sort
            switch (this.filters.sortBy) {
                case 'newest':
                    results.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
                    break;
                case 'oldest':
                    results.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
                    break;
                case 'price_low':
                    results.sort((a, b) => Number(a.product?.price || 0) - Number(b.product?.price || 0));
                    break;
                case 'price_high':
                    results.sort((a, b) => Number(b.product?.price || 0) - Number(a.product?.price || 0));
                    break;
            }

            this.filteredListings = results;
        },

        resetFilters() {
            this.searchQuery = '';
            this.filters = {
                type: 'all',
                qualities: [],
                priceMin: '',
                priceMax: '',
                sortBy: 'newest'
            };
            this.filterListings();
        },

        formatDate(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffTime = Math.abs(now - date);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            if (diffDays === 0) return 'اليوم';
            if (diffDays === 1) return 'أمس';
            if (diffDays < 7) return `منذ ${diffDays} أيام`;
            if (diffDays < 30) return `منذ ${Math.floor(diffDays / 7)} أسابيع`;
            return `منذ ${Math.floor(diffDays / 30)} أشهر`;
        }
    }));
});
</script>
@endsection
