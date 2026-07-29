<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        // CSP with Alpine.js support, Leaflet.js (maps), Google Fonts, and Meta WebView support
        $csp = "default-src 'self'; " .
               "img-src 'self' data: blob: https://*.tile.openstreetmap.org https://unpkg.com https://www.facebook.com https://*.fbcdn.net https://*.cdninstagram.com; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://unpkg.com https://cdn.jsdelivr.net https://connect.facebook.net; " .
               "style-src 'self' 'unsafe-inline' https://unpkg.com https://cdn.jsdelivr.net https://fonts.googleapis.com; " .
               "font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net; " .
               "connect-src 'self' ws: wss: https://*.tile.openstreetmap.org https://unpkg.com https://cdn.jsdelivr.net https://open.er-api.com";
        
        $response->headers->set('Content-Security-Policy', $csp);
        return $response;
    }
}
