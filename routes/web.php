<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StoryController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

// Progressive Profiling & Onboarding
Route::middleware('auth')->get('/onboarding/complete', [\App\Http\Controllers\Auth\OnboardingController::class, 'show'])->name('onboarding.complete');
Route::middleware('auth')->post('/onboarding/complete', [\App\Http\Controllers\Auth\OnboardingController::class, 'store'])->name('onboarding.store');

// Facebook Login Routes
Route::get('/auth/facebook/redirect', [\App\Http\Controllers\Auth\SocialLoginController::class, 'redirect'])->name('auth.facebook');
Route::get('/auth/facebook/callback', [\App\Http\Controllers\Auth\SocialLoginController::class, 'callback']);

// Google Login Routes
Route::get('/auth/google/redirect', [\App\Http\Controllers\Auth\SocialLoginController::class, 'redirectGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\SocialLoginController::class, 'callbackGoogle']);

// Token Login Bridge for Native App WebViews
Route::get('/auth/token-login', function (\Illuminate\Http\Request $request) {
    $token = $request->query('token');
    $redirect = $request->query('redirect', '/');
    
    if ($token) {
        // Find token in personal_access_tokens
        $tokenModel = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
        if ($tokenModel && $tokenModel->tokenable) {
            Auth::login($tokenModel->tokenable, true);
        }
    }
    
    return redirect($redirect);
})->name('auth.token-login');

// Language switcher — backward compat redirect to locale-prefixed URL
Route::get('/lang/{locale}', function (string $locale) {
    $supported = ['ar','fr','en'];
    if (!in_array($locale, $supported, true)) {
        $locale = config('app.fallback_locale', 'ar');
    }
    session(['locale' => $locale]);
    if (auth()->check() && auth()->user()->locale !== $locale) {
        auth()->user()->update(['locale' => $locale]);
    }
    // Redirect to the same page with locale prefix
    $previousPath = parse_url(url()->previous(), PHP_URL_PATH) ?? '/';
    $previousPath = preg_replace('#^/(ar|fr|en|es|zh|ja)#', '', $previousPath) ?: '/';
    return redirect('/' . $locale . $previousPath);
})->name('lang.switch');

// ═══════════════════════════════════════════════════════════════
// LOCALE-PREFIXED ROUTES — /ar/, /fr/, /en/ (SEO multilingual)
// ═══════════════════════════════════════════════════════════════
Route::prefix('{locale}')->where(['locale' => 'ar|fr|en|es|zh|ja'])->group(function () {

Route::middleware(['web', 'set.locale'])->group(function () {
    Route::get('/', function () {
        // Get all active listings with product details and seller addresses for location-based filtering
        $featuredListings = \Illuminate\Support\Facades\Cache::remember('home_featured_listings', now()->addMinutes(5), function () {
            // Get Top 6 Admin Featured listings
            $topFeatured = \App\Models\Listing::with(['product', 'seller.addresses'])
                ->where('status', 'active')
                ->where('is_featured', true)
                ->latest('updated_at')
                ->take(6)
                ->get();

            $featuredIds = $topFeatured->pluck('id')->toArray();

            // Get remaining active listings in normal chronological order
            $otherListings = \App\Models\Listing::with(['product', 'seller.addresses'])
                ->where('status', 'active')
                ->whereNotIn('id', $featuredIds)
                ->latest('created_at')
                ->get();

            return $topFeatured->concat($otherListings);
        });

        // Handle session new_listing_id for current user (Position #1 for creator's fresh listing)
        $newListingId = session('new_listing_id');
        if ($newListingId) {
            $freshItem = \App\Models\Listing::with(['product', 'seller.addresses'])->find($newListingId);
            if ($freshItem) {
                $featuredListings = collect([$freshItem])->concat($featuredListings->reject(fn($item) => $item->id == $newListingId))->values();
            }
        }
            
        $articles = \Illuminate\Support\Facades\Cache::remember('home_articles', now()->addMinutes(15), function () {
            return \App\Models\Article::where('is_active', true)->orderBy('id', 'asc')->get();
        });
        
        $deals = \Illuminate\Support\Facades\Cache::remember('home_deals', now()->addMinutes(10), function () {
            return \App\Models\Deal::with('user')->active()->latest()->take(6)->get();
        });

        $serviceProviders = \Illuminate\Support\Facades\Cache::remember('home_service_providers', now()->addMinutes(10), function () {
            return \App\Models\User::with('addresses')
                ->whereIn('role', [
                    'carrier', 'mill', 'packer', 'transiteur', 
                    'comptable', 'service_bureau', 'agri_equipment', 'agri_materials'
                ])
                ->latest()
                ->take(8)
                ->get();
        });
        
        $heroSlides = \App\Models\HeroSlide::where('is_active', true)->orderBy('order', 'asc')->get();
        
        $platformStats = \Illuminate\Support\Facades\Cache::remember('home_platform_stats', now()->addMinutes(30), function () {
            return [
                'visits' => \App\Models\Visitor::where('is_bot', false)->count(),
                'users' => \App\Models\User::count(),
                'producers' => \App\Models\User::where('role', 'farmer')->count(),
                'mills' => \App\Models\User::where('role', 'mill')->count(),
                'packers' => \App\Models\User::where('role', 'packer')->count(),
                'listings' => \App\Models\Listing::where('status', 'active')->count(),
            ];
        });
        
        return view('home_marketplace', compact('featuredListings', 'articles', 'deals', 'serviceProviders', 'heroSlides', 'platformStats'));
    })->name('home');
    
    // Redirect /products and /market to home page anchored at products section
    Route::get('/products', function () {
        return redirect(url(app()->getLocale() . '/#products'), 301);
    })->name('products');

    Route::get('/market', function () {
        return redirect(url(app()->getLocale() . '/#products'), 301);
    })->name('market');
    
    // Public & legal pages
    Route::view('/about', 'public.about')->name('about');
    Route::view('/how-it-works', 'public.how_it_works')->name('how-it-works');
    Route::view('/pricing', 'public.pricing')->name('pricing');
    Route::get('/servicehub', [\App\Http\Controllers\ServiceProviderController::class, 'index'])->name('services.index');
    Route::view('/services/pricing', 'public.services_pricing')->name('services.pricing');
    Route::get('/services/register', [\App\Http\Controllers\Auth\ServiceProviderRegisterController::class, 'create'])->name('services.register')->middleware('guest');
    Route::post('/services/register', [\App\Http\Controllers\Auth\ServiceProviderRegisterController::class, 'store'])->name('services.register.store')->middleware(['guest', 'throttle:30,1']);
    Route::get('/services/appointment/consultation', function(\Illuminate\Http\Request $request) {
        $name = $request->query('name', '');
        $phone = $request->query('phone', '');
        
        $message = "مرحباً منصة الزين، أود حجز موعد استشارة بخصوص تصدير وتجارة زيت الزيتون التونسي.";
        if ($name) {
            $message .= "\n\nالاسم: " . $name;
        }
        if ($phone) {
            $message .= "\nالهاتف: " . $phone;
        }
        
        $waUrl = "https://api.whatsapp.com/send/?phone=21625777926&text=" . urlencode($message);
        return redirect()->away($waUrl);
    })->name('services.appointment.consultation');
    Route::get('/services/appointment/{service}', [\App\Http\Controllers\MarketingServiceController::class, 'appointmentForm'])->name('services.appointment');
    Route::post('/services/appointment/{service}', [\App\Http\Controllers\MarketingServiceController::class, 'submitAppointment'])->name('services.appointment.submit');
    Route::view('/contact', 'public.contact')->name('public.contact');
    
    // Articles
    Route::get('/articles/{id}', function($id) {
        $article = \App\Models\Article::where('is_active', true)->findOrFail($id);
        $relatedArticles = \App\Models\Article::where('is_active', true)->where('id', '!=', $id)->latest()->get();
        return view('public.article', compact('article', 'relatedArticles'));
    })->name('articles.show');
    
    // Dedicated SEO Articles
    Route::view('/olive-varieties', 'public.article_varieties')->name('article.varieties');
    Route::post('/contact', function(Request $request){
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'message' => 'required|string|max:5000',
        ]);

        $to = config('app.contact_email') ?? config('mail.from.address');
        if (!$to) {
            return back()->withErrors(['email' => __('Contact email not configured.')])->withInput();
        }

        $body = "Contact message from {$data['name']} ({$data['email']})" . (empty($data['phone']) ? '' : "\nPhone: {$data['phone']}") . "\n\nMessage:\n{$data['message']}";

        Mail::raw($body, function($message) use ($to) {
            $message->to($to)->subject('New contact message');
        });

        return back()->with('status', __('Your message has been sent.'));
    })->name('public.contact.submit');
    Route::view('/terms', 'public.terms')->name('terms');
    Route::view('/privacy', 'public.privacy')->name('privacy');
    Route::view('/seller-policy', 'public.seller_policy')->name('seller-policy');
    Route::view('/commission-policy', 'public.commission_policy')->name('commission-policy');
    Route::view('/licensing-policy', 'public.licensing_policy')->name('licensing-policy');

    // Stories (public fetch)
    Route::get('/user/{user}/stories', [StoryController::class, 'index'])->name('user.stories');
    Route::get('/user/{user}', [\App\Http\Controllers\ProfileController::class, 'viewPublicProfile'])->name('user.profile');
});

Route::middleware(['auth', 'set.locale', 'onboarding'])->get('/dashboard', [\App\Http\Controllers\ProfileController::class, 'show'])->name('dashboard');

// User interaction routes (follow/like)
Route::middleware('set.locale')->get('/user/{user}/interaction-status', [\App\Http\Controllers\UserInteractionController::class, 'getStatus'])->name('user.interaction.status');
Route::middleware(['auth', 'set.locale'])->post('/user/{user}/toggle-follow', [\App\Http\Controllers\UserInteractionController::class, 'toggleFollow'])->name('user.toggle-follow');
Route::middleware(['auth', 'set.locale'])->post('/user/{user}/toggle-like', [\App\Http\Controllers\UserInteractionController::class, 'toggleLike'])->name('user.toggle-like');

Route::middleware(['auth', 'set.locale'])->group(function () {
    Route::post('/stories', [StoryController::class, 'store'])->name('stories.store');
    Route::delete('/stories/{story}', [StoryController::class, 'destroy'])->name('stories.destroy');
    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\NotificationController::class, 'index'])->name('index');
        Route::post('/mark-read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markOneAsRead'])->name('read');
    });

    // Load / Transport routes
    Route::post('/loads/summon', [\App\Http\Controllers\LoadController::class, 'summon'])->name('loads.summon');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Web Push subscriptions
    Route::post('/push/subscribe', [\App\Http\Controllers\PushSubscriptionController::class, 'subscribe'])->name('push.subscribe');
    Route::post('/push/unsubscribe', [\App\Http\Controllers\PushSubscriptionController::class, 'unsubscribe'])->name('push.unsubscribe');
    
    // Inline profile field updates (AJAX)
    Route::patch('/profile/field', [ProfileController::class, 'updateField'])->name('profile.update.field');
    Route::post('/profile/photo', [ProfileController::class, 'uploadPhoto'])->name('profile.upload.photo');
    Route::post('/profile/service-card', [ProfileController::class, 'addServiceCard'])->name('profile.add.service-card');
    Route::post('/profile/service-card/update', [ProfileController::class, 'updateServiceCard'])->name('profile.update.service-card');
    
    // Lab Analysis PDF upload & delete
    Route::post('/profile/lab-analysis', [ProfileController::class, 'uploadLabAnalysis'])->name('profile.lab_analysis.upload');
    Route::delete('/profile/lab-analysis/{id}', [ProfileController::class, 'deleteLabAnalysis'])->name('profile.lab_analysis.delete');
    
    // Messaging routes
    Route::get('/messages', [\App\Http\Controllers\MessageController::class, 'inbox'])->name('messages.inbox');
    Route::get('/messages/unread-count', [\App\Http\Controllers\MessageController::class, 'unreadCount'])->name('messages.unread');
    Route::get('/messages/{user}', [\App\Http\Controllers\MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{user}/send', [\App\Http\Controllers\MessageController::class, 'send'])->name('messages.send');
    Route::get('/messages/{user}/get', [\App\Http\Controllers\MessageController::class, 'getMessages'])->name('messages.get');
});

// ═══════════════════════════════════════════════════════════════
// PRICE HUBS (🇹🇳 National Tunisian Hub + 🌍 International Hub)
// ═══════════════════════════════════════════════════════════════
Route::middleware('set.locale')->group(function () {
    // 🇹🇳 1. National Tunisian Market Price Hub
    Route::get('/prices', [\App\Http\Controllers\PriceController::class, 'index'])->name('prices.index');
    Route::get('/souks', [\App\Http\Controllers\PriceController::class, 'souks'])->name('prices.souks');

    // 🌍 2. International & Global Olive Oil Benchmark Price Hub
    Route::get('/international-olive-oil-prices', [\App\Http\Controllers\PriceController::class, 'international'])->name('prices.international');
    Route::get('/global-prices', [\App\Http\Controllers\PriceController::class, 'international'])->name('prices.global');
    Route::get('/prix-huile-olive-international', [\App\Http\Controllers\PriceController::class, 'international'])->name('prices.international.fr');
    Route::get('/أسعار-زيت-الزيتون-العالمية', [\App\Http\Controllers\PriceController::class, 'international'])->name('prices.international.ar');
    Route::get('/prices/world', [\App\Http\Controllers\PriceController::class, 'world'])->name('prices.world');

    // Programmatic B2B SEO Landing Pages
    Route::get('/bulk-tunisian-olive-oil', [\App\Http\Controllers\SeoLandingController::class, 'bulkOliveOil'])->name('seo.bulk');
    Route::get('/huile-olive-tunisienne-en-vrac', [\App\Http\Controllers\SeoLandingController::class, 'bulkOliveOil'])->name('seo.bulk.fr');
    Route::get('/زيت-الزيتون-التونسي-بالجملة', [\App\Http\Controllers\SeoLandingController::class, 'bulkOliveOil'])->name('seo.bulk.ar');

    Route::get('/tunisian-olive-oil-suppliers', [\App\Http\Controllers\SeoLandingController::class, 'suppliers'])->name('seo.suppliers');
    Route::get('/fournisseurs-huile-olive-tunisienne', [\App\Http\Controllers\SeoLandingController::class, 'suppliers'])->name('seo.suppliers.fr');
    Route::get('/موردي-زيت-الزيتون-التونسي', [\App\Http\Controllers\SeoLandingController::class, 'suppliers'])->name('seo.suppliers.ar');

    Route::get('/olive-oil-mills-tunisia', [\App\Http\Controllers\SeoLandingController::class, 'mills'])->name('seo.mills');
    Route::get('/moulins-huile-olive-tunisie', [\App\Http\Controllers\SeoLandingController::class, 'mills'])->name('seo.mills.fr');
    Route::get('/معاصر-الزيتون-تونس', [\App\Http\Controllers\SeoLandingController::class, 'mills'])->name('seo.mills.ar');

    Route::get('/olive-oil-packers-tunisia', [\App\Http\Controllers\SeoLandingController::class, 'packers'])->name('seo.packers');
    Route::get('/conditionneurs-huile-olive-tunisie', [\App\Http\Controllers\SeoLandingController::class, 'packers'])->name('seo.packers.fr');
    Route::get('/تعبئة-زيت-الزيتون-تونس', [\App\Http\Controllers\SeoLandingController::class, 'packers'])->name('seo.packers.ar');

    Route::get('/private-label-olive-oil-tunisia', [\App\Http\Controllers\SeoLandingController::class, 'privateLabel'])->name('seo.private_label');
    Route::get('/marque-privee-huile-olive-tunisie', [\App\Http\Controllers\SeoLandingController::class, 'privateLabel'])->name('seo.private_label.fr');
    Route::get('/علامة-خاصة-زيت-زيتون-تونس', [\App\Http\Controllers\SeoLandingController::class, 'privateLabel'])->name('seo.private_label.ar');
});

}); // end locale prefix group 1

// Admin Routes - Rate limited to prevent abuse
// Limit: 60 requests per minute per user
Route::middleware(['auth', 'role:admin', 'set.locale', 'throttle:60,1'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\AdminController::class, 'index'])->name('dashboard');
    Route::get('/analytics/visitors', [\App\Http\Controllers\Admin\VisitorAnalyticsController::class, 'index'])->name('analytics.visitors');
    Route::get('/analytics/marketing', [\App\Http\Controllers\Admin\VisitorAnalyticsController::class, 'marketing'])->name('analytics.marketing');
    Route::get('/subscribers', [\App\Http\Controllers\Admin\SubscriberController::class, 'index'])->name('subscribers.index');
    Route::post('/subscribers/bulk-message', [\App\Http\Controllers\Admin\SubscriberController::class, 'bulkMessage'])->name('subscribers.bulk-message');
    Route::post('/subscribers/upload-image', [\App\Http\Controllers\Admin\SubscriberController::class, 'uploadEmailImage'])->name('subscribers.upload-image');
    Route::get('/users', [\App\Http\Controllers\AdminController::class, 'users'])->name('users');
    Route::get('/users/{user}/edit', [\App\Http\Controllers\AdminController::class, 'editUser'])->name('users.edit');
    Route::patch('/users/{user}', [\App\Http\Controllers\AdminController::class, 'updateUser'])->name('users.update');
    Route::get('/listings', [\App\Http\Controllers\AdminController::class, 'listings'])->name('listings');
    Route::get('/listings/{listing}/edit', [\App\Http\Controllers\AdminController::class, 'editListing'])->name('listings.edit');
    Route::patch('/listings/{listing}', [\App\Http\Controllers\AdminController::class, 'updateListing'])->name('listings.update');
    
    // Listing moderation
    Route::post('/listings/{listing}/approve', [\App\Http\Controllers\AdminController::class, 'approveListing'])->name('listings.approve');
    Route::post('/listings/{listing}/reject', [\App\Http\Controllers\AdminController::class, 'rejectListing'])->name('listings.reject');
    
    // Marketing Appointments Management
    Route::prefix('marketing-appointments')->name('marketing.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\MarketingAppointmentController::class, 'index'])->name('index');
        Route::get('/{appointment}/edit', [\App\Http\Controllers\Admin\MarketingAppointmentController::class, 'edit'])->name('edit');
        Route::patch('/{appointment}', [\App\Http\Controllers\Admin\MarketingAppointmentController::class, 'update'])->name('update');
        Route::patch('/{appointment}/status', [\App\Http\Controllers\Admin\MarketingAppointmentController::class, 'updateStatus'])->name('update-status');
        Route::delete('/{appointment}', [\App\Http\Controllers\Admin\MarketingAppointmentController::class, 'destroy'])->name('destroy');
    });

    // Price Management - Souk Prices
    Route::prefix('prices/souk')->name('prices.souk.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\PriceManagementController::class, 'indexSouk'])->name('index');
        Route::post('/refresh-dates', [\App\Http\Controllers\Admin\PriceManagementController::class, 'refreshSoukDates'])->name('refresh-dates');
        Route::get('/create', [\App\Http\Controllers\Admin\PriceManagementController::class, 'createSouk'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\PriceManagementController::class, 'storeSouk'])->name('store');
        Route::get('/{price}/edit', [\App\Http\Controllers\Admin\PriceManagementController::class, 'editSouk'])->name('edit');
        Route::put('/{price}', [\App\Http\Controllers\Admin\PriceManagementController::class, 'updateSouk'])->name('update');
        Route::delete('/{price}', [\App\Http\Controllers\Admin\PriceManagementController::class, 'destroySouk'])->name('destroy');
    });

    // Articles Management
    Route::resource('articles', \App\Http\Controllers\Admin\ArticleController::class)->except(['show']);
    
    // Deals Management
    Route::resource('deals', \App\Http\Controllers\Admin\DealController::class)->except(['show']);
    Route::get('deal-requests', [\App\Http\Controllers\Admin\DealRequestController::class, 'index'])->name('deals.requests.index');
    Route::patch('deal-requests/{dealRequest}/status', [\App\Http\Controllers\Admin\DealRequestController::class, 'updateStatus'])->name('deals.requests.status');
    Route::delete('deal-requests/{dealRequest}', [\App\Http\Controllers\Admin\DealRequestController::class, 'destroy'])->name('deals.requests.destroy');
    
    // Price Management - World Prices
    Route::prefix('prices/world')->name('prices.world.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\PriceManagementController::class, 'indexWorld'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\PriceManagementController::class, 'createWorld'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\PriceManagementController::class, 'storeWorld'])->name('store');
        Route::get('/{price}/edit', [\App\Http\Controllers\Admin\PriceManagementController::class, 'editWorld'])->name('edit');
        Route::put('/{price}', [\App\Http\Controllers\Admin\PriceManagementController::class, 'updateWorld'])->name('update');
        Route::delete('/{price}', [\App\Http\Controllers\Admin\PriceManagementController::class, 'destroyWorld'])->name('destroy');
    });

    Route::delete('/listings/{listing}', [\App\Http\Controllers\AdminController::class, 'deleteListing'])->name('listings.delete');
    Route::post('/listings/{listing}/toggle-featured', [\App\Http\Controllers\AdminController::class, 'toggleFeatured'])->name('listings.toggle_featured');
    
    // User moderation
    Route::post('/users/{user}/ban', [\App\Http\Controllers\AdminController::class, 'banUser'])->name('users.ban');
    Route::delete('/users/{user}', [\App\Http\Controllers\AdminController::class, 'deleteUser'])->name('users.delete');

    // Hero Slideshow Management
    Route::post('hero-slides/catalog-bg', [\App\Http\Controllers\Admin\HeroSlideController::class, 'updateCatalogBg'])->name('hero-slides.catalog-bg');
    Route::resource('hero-slides', \App\Http\Controllers\Admin\HeroSlideController::class)->except(['show']);
});

// Dynamic OG Image for listings
Route::get('/og-image/listing/{id}', [\App\Http\Controllers\OgImageController::class, 'generate'])->name('og.listing.image');

Route::middleware('set.locale')->group(function () {
    require __DIR__.'/auth.php';
});

\Illuminate\Support\Facades\Broadcast::routes(['middleware' => ['web']]);

// Public storefront feeds + SEO (no locale prefix — feeds are language-neutral)
Route::group([], function(){
    Route::get('landing.json', [\App\Http\Controllers\PublicController::class, 'landing']);
    Route::get('public/landing.json', [\App\Http\Controllers\PublicController::class, 'landing']);
    Route::get('sitemap.xml', [\App\Http\Controllers\PublicController::class, 'sitemap'])->name('public.sitemap');
    Route::get('public/sitemap.xml', [\App\Http\Controllers\PublicController::class, 'sitemap']);
    Route::get('feed.rss', [\App\Http\Controllers\PublicController::class, 'rss'])->name('public.rss');
    Route::get('public/feed.rss', [\App\Http\Controllers\PublicController::class, 'rss']);
    Route::get('og/products/{product}', [\App\Http\Controllers\PublicController::class, 'ogListing']);
    // Google Shopping product feed — all active marketplace listings with correct images
    Route::get('google-merchant-feed.xml', [\App\Http\Controllers\GoogleMerchantFeedController::class, 'feed'])->name('google.merchant.feed');
    Route::get('shopping-feed.xml', [\App\Http\Controllers\GoogleMerchantFeedController::class, 'feed']);
});

// Gulf storefront (locale-prefixed — user-facing pages)
Route::prefix('{locale}')->where(['locale' => 'ar|fr|en|es|zh|ja'])->middleware('set.locale')->group(function () {
    Route::get('catalog', [\App\Http\Controllers\PublicController::class, 'catalog'])->name('catalog');
});

// Named routes for CTAs under allowed prefixes per CI guard
Route::prefix('{locale}')->where(['locale' => 'ar|fr|en|es|zh|ja'])->group(function () {
Route::middleware('set.locale')->group(function(){
    // Listing creation form (requires auth)
    Route::get('listings/create', [\App\Http\Controllers\ListingController::class, 'create'])
        ->middleware(['auth', 'onboarding'])
        ->name('listings.create');
    
    // Limit listing creation to 10 per hour per user (prevents spam)
    Route::post('listings/store', [\App\Http\Controllers\ListingController::class, 'store'])
        ->middleware(['auth', 'onboarding', 'throttle:10,60'])
        ->name('listings.store');
    
    // Add routes for edit and delete
    Route::get('listings/{listing}/edit', [\App\Http\Controllers\ListingController::class, 'edit'])
        ->middleware(['auth', 'onboarding'])
        ->name('listings.edit');
    
    // Limit updates to 20 per hour per user
    Route::put('listings/{listing}', [\App\Http\Controllers\ListingController::class, 'update'])
        ->middleware(['auth', 'onboarding', 'throttle:20,60'])
        ->name('listings.update');
    
    // Limit deletes to 10 per hour per user
    Route::delete('listings/{listing}', [\App\Http\Controllers\ListingController::class, 'destroy'])
        ->middleware(['auth', 'onboarding', 'throttle:10,60'])
        ->name('listings.destroy');
    
    Route::post('listings/{listing}/quick-upload', [\App\Http\Controllers\ListingController::class, 'quickUploadMedia'])
        ->middleware(['auth', 'throttle:30,60'])
        ->name('listings.quick_upload');

    // Add route for viewing single listing
    Route::get('listings/{listing}', [\App\Http\Controllers\ListingController::class, 'show'])->name('listings.show');

    // Aoula order request form (requires auth)
    Route::get('orders/request-aoula', function(){
        if (!Auth::check()) {
            return view('public.forms.auth_required', [
                'title' => 'طلب عولة',
                'hint' => 'الرجاء تسجيل الدخول لإرسال طلب.'
            ]);
        }
        $listings = \App\Models\Listing::query()
            ->with(['product:id,variety,quality,price','seller:id,name'])
            ->where('status','active')
            ->latest('id')->limit(50)->get(['id','product_id','seller_id']);
        return view('public.forms.request_aoula', [
            'listings' => $listings,
            'userId' => Auth::id(),
        ]);
    })->name('orders.requestAoula');

    // Carrier mobile UI
    Route::get('mobile/trip', function(){
        return view('public.mobile.trip');
    })->name('mobile.trip');

    // gulf.catalog now defined above
    Route::post('deals/{deal}/request', [\App\Http\Controllers\DealRequestController::class, 'store'])->name('deals.request.submit');

    // Ezzitouni AI Chatbot Route
    Route::post('/api/chat', [\App\Http\Controllers\ChatbotController::class, 'chat'])->name('chatbot.chat');
    // Localized auth views (no names, to avoid conflict with auth.php)
    Route::get('login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'create']);
    Route::get('register', function () { return view('auth.register'); });
    Route::get('register/role', function (\Illuminate\Http\Request $request) {
        $role = $request->query('role');
        if (!in_array($role, ['farmer','carrier','mill','packer','normal'])) {
            return redirect('/' . app()->getLocale() . '/register');
        }
        return view('auth.register_' . $role, compact('role'));
    })->name('register.role');
    Route::get('forgot-password', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'create']);
    Route::get('reset-password/{token}', [\App\Http\Controllers\Auth\NewPasswordController::class, 'create']);

});
}); // end locale prefix group 2

// Removed old register routes as they are now handled by the localized group and auth.php

// ═══════════════════════════════════════════════════════════════
// NON-LOCALISED INFRASTRUCTURE ROUTES
// ═══════════════════════════════════════════════════════════════
Route::get('/healthz', function(){ return 'OK'; });
Route::get('/email-preview', function(){ return view('emails.platform_update_announcement'); })->name('email.preview');
Route::get('/email-preview/welcome', function(){
    $user = \App\Models\User::first() ?? (object)['name' => 'محمد الفلاح'];
    return view('emails.welcome', compact('user'));
});
Route::get('/email-preview/listing', function(){
    $listing = \App\Models\Listing::with('product')->first();
    if (!$listing) return 'No listings found';
    return view('emails.listing_created', compact('listing'));
});
Route::get('/email-preview/bulk', function(){
    return view('emails.bulk_subscriber', [
        'subjectTitle' => '🚀 مثال على رسالة جماعية | Exemple de message',
        'messageBody' => '<p style="direction:rtl; text-align:right;">هذا مثال على محتوى الرسالة الجماعية التي سيتم إرسالها لجميع المشتركين.</p><p style="direction:rtl; text-align:right;"><strong>يمكن تضمين أي محتوى HTML هنا</strong> — عروض، تحديثات، أسعار، إلخ.</p>'
    ]);
});
Route::get('/email-preview/onboarding', function(){
    return view('emails.onboarding');
});
Route::get('/email-preview/new-listing', function(){
    $listing = \App\Models\Listing::with(['product', 'seller'])->first();
    if (!$listing) return 'No listings found — create one first.';
    return view('emails.new_listing_notification', compact('listing'));
});

// ═══════════════════════════════════════════════════════════════
// ROOT URL + CATCH-ALL REDIRECTS (old non-prefixed & ?lang= URLs)
// ═══════════════════════════════════════════════════════════════

// Root URL → redirect to target locale (handles ?lang=en → /en)
Route::get('/', function (Request $request) {
    $supported = ['ar', 'fr', 'en', 'es', 'zh', 'ja'];
    $reqLang = $request->query('lang');
    $locale = ($reqLang && in_array($reqLang, $supported, true))
        ? $reqLang
        : session('locale', config('app.fallback_locale', 'ar'));

    $query = $request->except('lang');
    $queryString = !empty($query) ? '?' . http_build_query($query) : '';

    return redirect('/' . $locale . $queryString, 301);
});

// Fallback route: 301 single-hop redirect for old non-prefixed & query-parameter URLs
Route::fallback(function (Request $request) {
    $path = trim($request->path(), '/');
    $supported = ['ar', 'fr', 'en', 'es', 'zh', 'ja'];
    $reqLang = $request->query('lang');
    $locale = ($reqLang && in_array($reqLang, $supported, true))
        ? $reqLang
        : session('locale', config('app.fallback_locale', 'ar'));

    // Preserve all query parameters except 'lang'
    $query = $request->except('lang');
    $queryString = !empty($query) ? '?' . http_build_query($query) : '';

    return redirect('/' . $locale . ($path ? '/' . $path : '') . $queryString, 301);
});
