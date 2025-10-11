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
        $lang = $request->query('lang');
        if ($lang && in_array($lang, $supported, true)) {
            Session::put('locale', $lang);
        }

        $locale = Session::get('locale', config('app.locale'));
        if (! in_array($locale, $supported, true)) {
            $locale = config('app.fallback_locale');
        }

        AppFacade::setLocale($locale);

        return $next($request);
    }
}
