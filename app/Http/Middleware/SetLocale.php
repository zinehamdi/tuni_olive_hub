<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App as AppFacade;
use Illuminate\Support\Facades\Session;

/**
 * Set the application locale from query (?lang=ar|fr|en) or session, defaulting to config('app.locale').
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
        $defaultLocale = 'ar'; // Always default to Arabic
        
        // Check for language in query parameter
        $lang = $request->query('lang');
        if ($lang && in_array($lang, $supported, true)) {
            Session::put('locale', $lang);
            
            // Save to user's profile if authenticated
            if ($request->user()) {
                $request->user()->update(['locale' => $lang]);
            }
        }

        // Priority: 1. Session, 2. User's saved locale, 3. Default to Arabic
        $locale = Session::get('locale');
        
        if (!$locale && $request->user() && $request->user()->locale) {
            $locale = $request->user()->locale;
            Session::put('locale', $locale);
        }
        
        if (!$locale) {
            $locale = $defaultLocale;
        }
        
        // Validate locale is supported, otherwise use Arabic
        if (! in_array($locale, $supported, true)) {
            $locale = $defaultLocale;
        }

        AppFacade::setLocale($locale);

        return $next($request);
    }
}
