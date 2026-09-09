<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Services\OgImageService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ListingOgImageController extends Controller
{
    /**
     * Serve or generate the 1200x630 Facebook / WhatsApp Open Graph card.
     */
    public function show(Request $request, Listing $listing, OgImageService $ogService)
    {
        try {
            $locale = (string) ($request->route('locale') ?: app()->getLocale() ?: 'ar');
            $imagePath = $ogService->getOrGenerate($listing, $locale);

            if (file_exists($imagePath)) {
                $lastModified = filemtime($imagePath);
                $etag = md5($listing->id . '_' . $lastModified);

                // Handle If-None-Match (304 Not Modified)
                if ($request->headers->get('If-None-Match') === $etag) {
                    return response('', 304);
                }

                return response()->file($imagePath, [
                    'Content-Type' => 'image/jpeg',
                    'Cache-Control' => 'public, max-age=604800, stale-while-revalidate=86400',
                    'ETag' => $etag,
                ]);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('ListingOgImageController error: ' . $e->getMessage());
        }

        // Fallback to default logo
        return response()->file(public_path('images/zintoop-logo.png'), [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
