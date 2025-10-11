<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Onboarding dispatcher
// Route::middleware('auth')->get('/onboarding', [\App\Http\Controllers\ProfileController::class, 'onboarding'])->name('onboarding');
// Route::middleware('auth')->post('/onboarding/{role}', [\App\Http\Controllers\ProfileController::class, 'submitOnboarding'])->name('onboarding.submit');

Route::middleware('set.locale')->group(function () {
    Route::get('/', function () {
        return view('home');
    })->name('home');

    Route::get('/lang/{locale}', function (string $locale) {
        $supported = ['ar','fr','en'];
        if (in_array($locale, $supported, true)) {
            session(['locale' => $locale]);
        }
        return redirect()->back();
    })->name('lang.switch');
});

Route::middleware('auth')->get('/dashboard', [\App\Http\Controllers\ProfileController::class, 'show'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Public storefront + SEO
Route::prefix('public')->group(function(){
    Route::get('landing.json', [\App\Http\Controllers\PublicController::class, 'landing']);
    Route::get('sitemap.xml', [\App\Http\Controllers\PublicController::class, 'sitemap'])->name('public.sitemap');
    Route::get('feed.rss', [\App\Http\Controllers\PublicController::class, 'rss'])->name('public.rss');
    Route::get('og/products/{product}', [\App\Http\Controllers\PublicController::class, 'ogListing']);
    // Real HTML pages for storefront
    Route::get('gulf/catalog', [\App\Http\Controllers\PublicController::class, 'gulfCatalog'])->name('gulf.catalog');
    Route::get('gulf/products/{product}', [\App\Http\Controllers\PublicController::class, 'gulfProduct'])->name('gulf.product');
});

// Named routes for CTAs under allowed prefixes per CI guard
Route::prefix('public')->group(function(){
    // Listing creation form (requires auth)
    if (!app('router')->has('listings.create')) {
        Route::get('listings/create', function(){
            if (!Auth::check()) {
                return view('public.forms.auth_required', [
                    'title' => 'إنشاء عرض جديد',
                    'hint' => 'الرجاء تسجيل الدخول لإنشاء عرض.'
                ]);
            }
            $products = \App\Models\Product::query()->latest('id')->get(['id','variety','quality','price']);
            return view('listings.create', [
                'products' => $products,
                'userId' => Auth::id(),
            ]);
        })->name('listings.create');
        // Add POST route for listing store
        Route::post('listings/store', [\App\Http\Controllers\ListingController::class, 'store'])->name('listings.store');
    }

    // Aoula order request form (requires auth)
    if (!app('router')->has('orders.requestAoula')) {
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
    }

    // Carrier mobile UI
    if (!app('router')->has('mobile.trip')) {
        Route::get('mobile/trip', function(){
            return view('public.mobile.trip');
        })->name('mobile.trip');
    }

    // Contact placeholder (public)
    if (!app('router')->has('public.contact')) {
        Route::get('contact', function(){
            return view('stubs.cta', ['title' => 'تواصل معنا']);
        })->name('public.contact');
    }
    // gulf.catalog now defined above
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
