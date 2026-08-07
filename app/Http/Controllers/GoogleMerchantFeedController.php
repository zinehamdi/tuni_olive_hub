<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Services\CurrencyConverter;
use Illuminate\Support\Facades\Cache;

class GoogleMerchantFeedController extends Controller
{
    /**
     * Google Shopping XML product feed.
     * - Only includes active olive OIL listings (not olives).
     * - Only includes listings with real product photos (no empty images or placeholders).
     * - All prices are converted to USD (globally accepted by Google Merchant Center).
     */
    public function feed()
    {
        $converter = app(CurrencyConverter::class);

        $xml = Cache::remember('google:merchant:feed', 3600, function () use ($converter) {
            $listings = Listing::query()
                ->with(['product', 'seller.addresses'])
                ->where('status', 'active')
                ->whereNotNull('price')
                ->where('price', '>', 0)
                ->whereHas('product', fn($q) => $q->where('type', 'oil')) // Oil only — no olives
                ->where(function ($query) {
                    // Must have real photos either on the listing or on the product
                    $query->where(function ($q) {
                        $q->whereNotNull('media')
                          ->where('media', '!=', '[]')
                          ->where('media', '!=', 'null')
                          ->where('media', '!=', '[""]');
                    })->orWhereHas('product', function ($pq) {
                        $pq->whereNotNull('media')
                           ->where('media', '!=', '[]')
                           ->where('media', '!=', 'null')
                           ->where('media', '!=', '[""]');
                    });
                })
                ->latest()
                ->get();

            return view('public.google_merchant_feed', [
                'listings'  => $listings,
                'converter' => $converter,
            ])->render();
        });

        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=utf-8')
            ->header('Cache-Control', 'public, max-age=3600');
    }
}
