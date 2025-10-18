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
        
        // CSP with Alpine.js support and Leaflet.js (maps) support
        $csp = "default-src 'self'; " .
               "img-src 'self' data: blob: https://*.tile.openstreetmap.org https://unpkg.com; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://unpkg.com; " .
               "style-src 'self' 'unsafe-inline' https://unpkg.com; " .
               "connect-src 'self' ws: wss: https://*.tile.openstreetmap.org https://unpkg.com";
        
        $response->headers->set('Content-Security-Policy', $csp);
        return $response;
    }
}
