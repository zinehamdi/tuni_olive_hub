<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * We intentionally regenerate the session token BEFORE authentication
     * so users arriving from Facebook/WhatsApp links (which carry a prefetch
     * session with a stale CSRF token) get a fresh session, eliminating the
     * "login fails on first attempt" issue.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Capture intended URL BEFORE regenerating session (it lives in the session)
        $intended = $request->session()->get('url.intended');

        // Skip the internal unread-count redirect trap
        if ($intended && str_contains($intended, '/messages/unread-count')) {
            $intended = null;
        }

        // Authenticate the user
        $request->authenticate();

        // Regenerate session to prevent session fixation
        $request->session()->regenerate();

        // Re-store intended URL after regeneration if it was valid
        if ($intended) {
            $request->session()->put('url.intended', $intended);
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
