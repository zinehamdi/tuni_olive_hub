<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App as AppFacade;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

/**
 * Set the application locale from the {locale} URL prefix, session, or user profile.
 *
 * Priority: 1. Route param ({locale} from URL prefix)
 *           2. Session (persisted from previous visit)
 *           3. Authenticated user's saved locale
 *           4. config('app.fallback_locale')
 */
class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $supported = ['ar', 'fr', 'en'];
        $fallback  = config('app.fallback_locale', 'ar');

        // 1. Read locale from the URL prefix ({locale} route parameter)
        $routeLocale = $request->route('locale');
        if ($routeLocale && in_array($routeLocale, $supported, true)) {
            $locale = $routeLocale;
            Session::put('locale', $locale);

            // Persist to user profile only when it actually changes
            if ($request->user() && $request->user()->locale !== $locale) {
                $request->user()->update(['locale' => $locale]);
            }
        } else {
            // 2. Fallback: session → user → config
            $locale = Session::get('locale');

            if (!$locale && $request->user() && $request->user()->locale) {
                $locale = $request->user()->locale;
                Session::put('locale', $locale);
            }

            if (!$locale || !in_array($locale, $supported, true)) {
                $locale = $fallback;
            }
        }

        AppFacade::setLocale($locale);

        // Set URL defaults so route() helper auto-includes the locale
        URL::defaults(['locale' => $locale]);

        // Remove the locale parameter so it is not passed to controller methods
        if ($request->route()) {
            $request->route()->forgetParameter('locale');
        }

        $response = $next($request);
        if (method_exists($response, 'header')) {
            // Prevent LiteSpeed from caching pages globally across different user languages,
            // but allow individual user browsers to cache content for performance.
            $response->header('X-LiteSpeed-Cache-Control', 'private, max-age=60');
            $response->header('Cache-Control', 'private, no-transform, max-age=60');
        }
        return $response;
    }
}
