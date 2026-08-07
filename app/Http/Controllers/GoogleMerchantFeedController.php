<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Support\Facades\Cache;

class GoogleMerchantFeedController extends Controller
{
    /**
     * Google Shopping XML product feed.
     * Includes ALL active marketplace listings with correct product images.
     */
    public function feed()
    {
        $xml = Cache::remember('google:merchant:feed', 3600, function () {
            $listings = Listing::query()
                ->with(['product', 'seller'])
                ->where('status', 'active')
                ->whereNotNull('price')
                ->where('price', '>', 0)
                ->latest()
                ->get();

            return view('public.google_merchant_feed', ['listings' => $listings])->render();
        });

        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=utf-8')
            ->header('Cache-Control', 'public, max-age=3600');
    }
}
