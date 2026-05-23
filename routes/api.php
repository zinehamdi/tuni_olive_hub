<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

// Named rate limiters (global definition before route usage)
RateLimiter::for('reports', function(Request $request){
    return \Illuminate\Cache\RateLimiting\Limit::perHour(10)
        ->by(optional($request->user())->id ?: $request->ip())
        ->response(fn() => response()->json(['success'=>false,'error'=>'Too Many Requests'], 429));
});
RateLimiter::for('contracts', function(Request $request){
    return \Illuminate\Cache\RateLimiting\Limit::perHour(20)
        ->by(optional($request->user())->id ?: $request->ip())
        ->response(fn() => response()->json(['success'=>false,'error'=>'Too Many Requests'], 429));
});
RateLimiter::for('pod-photos', function(Request $request){
    $tripParam = $request->route('trip');
    $tripId = is_object($tripParam) ? ($tripParam->id ?? 'n/a') : ($tripParam ?? $request->route('id') ?? 'n/a');
    return \Illuminate\Cache\RateLimiting\Limit::perHour(30)
        ->by('trip:'.$tripId)
        ->response(fn() => response()->json(['success'=>false,'error'=>'Too Many Requests'], 429));
});

Route::prefix('v1')->name('api.')->group(function () {
    Route::get('/ping', fn() => response()->json(['ok' => true, 'v' => 1]));

    Route::post('login', [\App\Http\Controllers\Api\V1\ApiAuthController::class, 'login']);
    Route::post('register', [\App\Http\Controllers\Api\V1\ApiAuthController::class, 'register']);

    // Public routes
    Route::get('listings', [\App\Http\Controllers\Api\V1\ListingController::class, 'index']);
    Route::get('listings/{listing}', [\App\Http\Controllers\Api\V1\ListingController::class, 'show']);
    Route::get('articles', function () {
        $articles = \App\Models\Article::where('is_active', true)->latest()->take(10)->get();
        return response()->json(['success' => true, 'data' => $articles]);
    });
    Route::post('chat', [\App\Http\Controllers\ChatbotController::class, 'chat']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [\App\Http\Controllers\Api\V1\ApiAuthController::class, 'logout']);
        
        // Listings restricted operations
        Route::post('listings', [\App\Http\Controllers\Api\V1\ListingController::class, 'store']);
        Route::match(['put', 'patch'], 'listings/{listing}', [\App\Http\Controllers\Api\V1\ListingController::class, 'update']);
        Route::delete('listings/{listing}', [\App\Http\Controllers\Api\V1\ListingController::class, 'destroy']);

        Route::apiResource('loads', \App\Http\Controllers\Api\V1\LoadController::class);

    // Orders
    Route::get('orders', [\App\Http\Controllers\Api\V1\OrderController::class, 'index']);
    Route::get('orders/{order}', [\App\Http\Controllers\Api\V1\OrderController::class, 'show']);
    Route::post('orders', [\App\Http\Controllers\Api\V1\OrderController::class, 'store']);
    Route::patch('orders/{order}/transition', [\App\Http\Controllers\Api\V1\OrderController::class, 'transition']);

        // Carrier Offers
    Route::post('loads/{load}/offers', [\App\Http\Controllers\Api\V1\CarrierOfferController::class, 'store']);
    Route::patch('offers/{offer}/accept', [\App\Http\Controllers\Api\V1\CarrierOfferController::class, 'accept']);

        // Trips
        Route::post('trips', [\App\Http\Controllers\Api\V1\TripController::class, 'store']);
        Route::post('trips/{trip}/start', [\App\Http\Controllers\Api\V1\TripController::class, 'start']);
        Route::post('trips/{trip}/pod', [\App\Http\Controllers\Api\V1\TripController::class, 'submitPod']);
        Route::post('trips/{trip}/complete', [\App\Http\Controllers\Api\V1\TripController::class, 'complete']);
    });

    // Payments - webhooks (HMAC verify)
    Route::post('payments/flouci/webhook', [\App\Http\Controllers\Api\V1\PaymentsWebhookController::class, 'flouci']);
    Route::post('payments/d17/webhook', [\App\Http\Controllers\Api\V1\PaymentsWebhookController::class, 'd17']);

    // Mill overview
    Route::get('mill/overview', [\App\Http\Controllers\Api\V1\MillOverviewController::class, 'show'])->middleware('auth:sanctum');

    // Public Prices endpoints
    Route::get('prices/today', [\App\Http\Controllers\Api\V1\PricesController::class, 'today']);
    Route::get('prices/history', [\App\Http\Controllers\Api\V1\PricesController::class, 'history']);

    // AI Smart Yield Estimator
    Route::post('ai/yield-estimate', [\App\Http\Controllers\Api\V1\AiYieldController::class, 'estimate']);

    // Reviews
    Route::middleware('auth:sanctum')->group(function(){
        Route::post('reviews', [\App\Http\Controllers\Api\V1\ReviewsController::class, 'store']);
        Route::post('reviews/{review}/reply', [\App\Http\Controllers\Api\V1\ReviewsController::class, 'reply']);
        Route::post('reviews/{review}/report', [\App\Http\Controllers\Api\V1\ReviewsController::class, 'report'])->middleware('throttle:reports');
        // Financing
        Route::post('financings', [\App\Http\Controllers\Api\V1\FinancingController::class, 'store']);
        Route::patch('financings/{financing}/accept', [\App\Http\Controllers\Api\V1\FinancingController::class, 'accept']);
        Route::patch('financings/{financing}/settle', [\App\Http\Controllers\Api\V1\FinancingController::class, 'settle']);
        Route::get('financings', [\App\Http\Controllers\Api\V1\FinancingController::class, 'index']);
        // Mills storage & yields
        Route::get('mills/storage/overview', [\App\Http\Controllers\Api\V1\MillsController::class, 'storageOverview']);
        Route::get('mills/yield/summary', [\App\Http\Controllers\Api\V1\MillsController::class, 'yieldSummary']);

        // Moderation (admin only)
        Route::post('mod/reports/{report}/resolve', [\App\Http\Controllers\Api\V1\ModerationController::class, 'resolve']);
        Route::get('mod/queue', [\App\Http\Controllers\Api\V1\ModerationController::class, 'queue']);
        Route::patch('messages/{id}/hide', [\App\Http\Controllers\Api\V1\ModerationController::class, 'hideMessage']);
        Route::patch('reviews/{id}/hide', [\App\Http\Controllers\Api\V1\ModerationController::class, 'hideReview']);

        // Financing Risk
        Route::get('financings/{financing}/risk', [\App\Http\Controllers\Api\V1\FinancingRiskController::class, 'show']);

        // Mill Batches
        Route::get('mills/batches', [\App\Http\Controllers\Api\V1\MillBatchesController::class, 'index']);
        Route::get('mills/batches/{id}', [\App\Http\Controllers\Api\V1\MillBatchesController::class, 'show']);
        Route::post('mills/batches', [\App\Http\Controllers\Api\V1\MillBatchesController::class, 'store']);
        Route::patch('mills/batches/{id}/complete', [\App\Http\Controllers\Api\V1\MillBatchesController::class, 'complete']);

    // Export RFQ routes moved to throttle:contracts group below
    });
    Route::get('reviews', [\App\Http\Controllers\Api\V1\ReviewsController::class, 'index']);
    Route::get('users/{user}/reputation', [\App\Http\Controllers\Api\V1\ReviewsController::class, 'reputation']);

    // Gulf Premium catalog (public)
    Route::get('gulf/catalog', [\App\Http\Controllers\Api\V1\GulfCatalogController::class, 'index']);
    Route::get('gulf/products/{product}', [\App\Http\Controllers\Api\V1\GulfCatalogController::class, 'show']);
    // Export workflow throttles
    Route::middleware(['auth:sanctum','throttle:contracts'])->group(function(){
        Route::post('export/offers/{offer}/rfq', [\App\Http\Controllers\Api\V1\ExportWorkflowController::class, 'rfq']);
        Route::post('export/contracts', [\App\Http\Controllers\Api\V1\ExportWorkflowController::class, 'createContract']);
        Route::patch('export/contracts/{contract}/sign', [\App\Http\Controllers\Api\V1\ExportWorkflowController::class, 'sign']);
        Route::patch('export/contracts/{contract}/fund', [\App\Http\Controllers\Api\V1\ExportWorkflowController::class, 'fund']);
        Route::patch('export/contracts/{contract}/ship', [\App\Http\Controllers\Api\V1\ExportWorkflowController::class, 'ship']);
        Route::patch('export/contracts/{contract}/close', [\App\Http\Controllers\Api\V1\ExportWorkflowController::class, 'close']);
    });

    // Mobile carrier aids (Public tracking links)
    // Placed below active to avoid route parameter collision

    Route::middleware(['auth:sanctum'])->group(function(){
        Route::get('mobile/trips/active', [\App\Http\Controllers\Api\V1\MobileController::class, 'activeTrip']);
        
        // These need to be below active to avoid shadowed routes
        Route::get('mobile/trips/{trip}', [\App\Http\Controllers\Api\V1\MobileController::class, 'showTrip'])->withoutMiddleware('auth:sanctum');
        Route::get('mobile/loads/{load}/trip', [\App\Http\Controllers\Api\V1\MobileController::class, 'loadTrip'])->withoutMiddleware('auth:sanctum');

        Route::get('mobile/loads/pending', [\App\Http\Controllers\Api\V1\MobileController::class, 'pendingLoads']);
        Route::post('mobile/loads/{load}/accept', [\App\Http\Controllers\Api\V1\MobileController::class, 'acceptLoad']);
        Route::post('mobile/loads/{load}/reject', [\App\Http\Controllers\Api\V1\MobileController::class, 'rejectLoad']);
        Route::post('mobile/trips/{trip}/pod/photo', [\App\Http\Controllers\Api\V1\MobileController::class, 'podPhoto'])->middleware('throttle:pod-photos');
        Route::post('mobile/trips/{trip}/location', [\App\Http\Controllers\Api\V1\MobileController::class, 'updateLocation']);
        Route::get('threads/{thread}/messages', [\App\Http\Controllers\Api\V1\MessagesController::class, 'index']);
        
        // Chat endpoints for mobile app
        Route::get('messages/inbox', [\App\Http\Controllers\MessageController::class, 'apiInbox']);
        Route::get('messages/unread-count', [\App\Http\Controllers\MessageController::class, 'unreadCount']);
        Route::get('messages/{user}/get', [\App\Http\Controllers\MessageController::class, 'getMessages']);
        Route::post('messages/{user}/send', [\App\Http\Controllers\MessageController::class, 'send']);

        // Export Shipments
        Route::get('export/shipments', [\App\Http\Controllers\Api\V1\ExportShipmentsController::class, 'index']);
        Route::post('export/shipments', [\App\Http\Controllers\Api\V1\ExportShipmentsController::class, 'store'])->middleware('throttle:contracts');
        Route::patch('export/shipments/{shipment}', [\App\Http\Controllers\Api\V1\ExportShipmentsController::class, 'update']);
        Route::post('export/shipments/{shipment}/documents', [\App\Http\Controllers\Api\V1\ExportShipmentsController::class, 'attachDocument']);
        Route::patch('export/shipments/{shipment}/documents/{document}/verify', [\App\Http\Controllers\Api\V1\ExportShipmentsController::class, 'verifyDocument']);
        Route::patch('export/shipments/{shipment}/transition', [\App\Http\Controllers\Api\V1\ExportShipmentsController::class, 'transition']);

        // Invoices
        Route::post('invoices/issue', [\App\Http\Controllers\Api\V1\InvoiceController::class, 'issue']);
        Route::get('invoices/{invoice}', [\App\Http\Controllers\Api\V1\InvoiceController::class, 'show']);

        // Payouts
        Route::post('payouts/request', [\App\Http\Controllers\Api\V1\PayoutsController::class, 'request']);
        Route::get('payouts/{payout}', [\App\Http\Controllers\Api\V1\PayoutsController::class, 'show']);
    });

    // Payout provider webhook (public)
    Route::post('payouts/webhook', [\App\Http\Controllers\Api\V1\PayoutsController::class, 'webhook']);
});

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('analytics', [\App\Http\Controllers\Admin\AnalyticsController::class, 'dashboard'])->name('admin.analytics.dashboard');
});
