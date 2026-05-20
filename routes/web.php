<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StoryController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

// Onboarding dispatcher
// Route::middleware('auth')->get('/onboarding', [\App\Http\Controllers\ProfileController::class, 'onboarding'])->name('onboarding');
// Route::middleware('auth')->post('/onboarding/{role}', [\App\Http\Controllers\ProfileController::class, 'submitOnboarding'])->name('onboarding.submit');

// Language switcher - must be outside middleware groups and available globally
Route::get('/lang/{locale}', function (string $locale) {
    $supported = ['ar','fr','en'];
    if (in_array($locale, $supported, true)) {
        session(['locale' => $locale]);
        
        // Save to user's profile if authenticated
        if (auth()->check()) {
            auth()->user()->update(['locale' => $locale]);
        }
    }
    return redirect()->back();
})->name('lang.switch');

Route::middleware('set.locale')->group(function () {
    Route::get('/', function () {
        // Get all active listings with product details and seller addresses for location-based filtering
        $featuredListings = \App\Models\Listing::with(['product', 'seller.addresses'])
            ->where('status', 'active')
            ->latest()
            ->get();
            
        $articles = \App\Models\Article::where('is_active', true)->latest()->take(3)->get();
        
        $deals = \App\Models\Deal::with('user')->active()->latest()->take(6)->get();
        
        return view('home_marketplace', compact('featuredListings', 'articles', 'deals'));
    })->name('home');
    
    // Public & legal pages
    Route::view('/about', 'public.about')->name('about');
    Route::view('/how-it-works', 'public.how_it_works')->name('how-it-works');
    Route::view('/pricing', 'public.pricing')->name('pricing');
    Route::view('/services/pricing', 'public.services_pricing')->name('services.pricing');
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
        return view('public.article', compact('article'));
    })->name('articles.show');
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
});

Route::middleware(['auth', 'set.locale'])->get('/dashboard', [\App\Http\Controllers\ProfileController::class, 'show'])->name('dashboard');

// Public user profile - accessible to anyone (view seller/publisher profiles)
Route::middleware('set.locale')->get('/user/{user}', [\App\Http\Controllers\ProfileController::class, 'viewPublicProfile'])->name('user.profile');

// User interaction routes (follow/like)
Route::middleware('set.locale')->get('/user/{user}/interaction-status', [\App\Http\Controllers\UserInteractionController::class, 'getStatus'])->name('user.interaction.status');
Route::middleware(['auth', 'set.locale'])->post('/user/{user}/toggle-follow', [\App\Http\Controllers\UserInteractionController::class, 'toggleFollow'])->name('user.toggle-follow');
Route::middleware(['auth', 'set.locale'])->post('/user/{user}/toggle-like', [\App\Http\Controllers\UserInteractionController::class, 'toggleLike'])->name('user.toggle-like');

Route::middleware(['auth', 'set.locale'])->group(function () {
    Route::post('/stories', [StoryController::class, 'store'])->name('stories.store');
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
    
    // Messaging routes
    Route::get('/messages', [\App\Http\Controllers\MessageController::class, 'inbox'])->name('messages.inbox');
    Route::get('/messages/unread-count', [\App\Http\Controllers\MessageController::class, 'unreadCount'])->name('messages.unread');
    Route::get('/messages/{user}', [\App\Http\Controllers\MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{user}/send', [\App\Http\Controllers\MessageController::class, 'send'])->name('messages.send');
    Route::get('/messages/{user}/get', [\App\Http\Controllers\MessageController::class, 'getMessages'])->name('messages.get');
});

// Price Routes (Public)
Route::middleware('set.locale')->group(function () {
    Route::get('/prices', [\App\Http\Controllers\PriceController::class, 'index'])->name('prices.index');
    Route::get('/prices/souks', [\App\Http\Controllers\PriceController::class, 'souks'])->name('prices.souks');
    Route::get('/prices/world', [\App\Http\Controllers\PriceController::class, 'world'])->name('prices.world');
});

// Admin Routes - Rate limited to prevent abuse
// Limit: 60 requests per minute per user
Route::middleware(['auth', 'role:admin', 'set.locale', 'throttle:60,1'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\AdminController::class, 'index'])->name('dashboard');
    Route::get('/analytics/visitors', [\App\Http\Controllers\Admin\VisitorAnalyticsController::class, 'index'])->name('analytics.visitors');
    Route::get('/analytics/marketing', [\App\Http\Controllers\Admin\VisitorAnalyticsController::class, 'marketing'])->name('analytics.marketing');
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
    
    // User moderation
    Route::post('/users/{user}/ban', [\App\Http\Controllers\AdminController::class, 'banUser'])->name('users.ban');
    Route::delete('/users/{user}', [\App\Http\Controllers\AdminController::class, 'deleteUser'])->name('users.delete');
});

require __DIR__.'/auth.php';

\Illuminate\Support\Facades\Broadcast::routes(['middleware' => ['web']]);

// Public storefront + SEO
Route::group([], function(){
    Route::get('landing.json', [\App\Http\Controllers\PublicController::class, 'landing']);
    Route::get('public/landing.json', [\App\Http\Controllers\PublicController::class, 'landing']);
    Route::get('sitemap.xml', [\App\Http\Controllers\PublicController::class, 'sitemap'])->name('public.sitemap');
    Route::get('public/sitemap.xml', [\App\Http\Controllers\PublicController::class, 'sitemap']);
    Route::get('feed.rss', [\App\Http\Controllers\PublicController::class, 'rss'])->name('public.rss');
    Route::get('public/feed.rss', [\App\Http\Controllers\PublicController::class, 'rss']);
    Route::get('og/products/{product}', [\App\Http\Controllers\PublicController::class, 'ogListing']);
    // Real HTML pages for storefront
    Route::get('gulf/catalog', [\App\Http\Controllers\PublicController::class, 'gulfCatalog'])->name('gulf.catalog');
    Route::get('gulf/products/{product}', [\App\Http\Controllers\PublicController::class, 'gulfProduct'])->name('gulf.product');
});

// Named routes for CTAs under allowed prefixes per CI guard
Route::middleware('set.locale')->group(function(){
    // Listing creation form (requires auth)
    Route::get('listings/create', [\App\Http\Controllers\ListingController::class, 'create'])
        ->middleware('auth')
        ->name('listings.create');
    
    // Limit listing creation to 10 per hour per user (prevents spam)
    Route::post('listings/store', [\App\Http\Controllers\ListingController::class, 'store'])
        ->middleware(['auth', 'throttle:10,60'])
        ->name('listings.store');
    
    // Add routes for edit and delete
    Route::get('listings/{listing}/edit', [\App\Http\Controllers\ListingController::class, 'edit'])
        ->middleware('auth')
        ->name('listings.edit');
    
    // Limit updates to 20 per hour per user
    Route::put('listings/{listing}', [\App\Http\Controllers\ListingController::class, 'update'])
        ->middleware(['auth', 'throttle:20,60'])
        ->name('listings.update');
    
    // Limit deletes to 10 per hour per user
    Route::delete('listings/{listing}', [\App\Http\Controllers\ListingController::class, 'destroy'])
        ->middleware(['auth', 'throttle:10,60'])
        ->name('listings.destroy');
    
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
});

// Authentication routes
Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::get('/register/role', function (\Illuminate\Http\Request $request) {
    $role = $request->query('role');
    if (!in_array($role, ['farmer','carrier','mill','packer','normal'])) {
        return redirect()->route('register');
    }
    return view('auth.register_' . $role, compact('role'));
})->name('register.role');
Route::get('/healthz', function(){ return 'OK'; });

