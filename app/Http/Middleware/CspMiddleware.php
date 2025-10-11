<?php
/**
 * Content-Security-Policy Middleware
 * وسيط سياسة أمان المحتوى
 * Injects script/style nonces and enforces strict CSP headers
 */
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CspMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        $nonce = base64_encode(Str::random(16));
        // Inject nonce into view share
        app('view')->share('cspNonce', $nonce);
        // Enforce CSP header
        $csp = "default-src 'self'; script-src 'self' 'nonce-$nonce'; style-src 'self' 'nonce-$nonce'; object-src 'none'; base-uri 'self'; form-action 'self';";
        $response->headers->set('Content-Security-Policy', $csp);
        return $response;
    }
}
