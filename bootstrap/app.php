<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Auth;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Route middleware aliases
        $middleware->alias([
            'set.locale' => \App\Http\Middleware\SetLocale::class,
            'role' => \App\Http\Middleware\EnsureRole::class,
        ]);

        // Global middleware
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $e, $request) {
            // Handle CSRF token mismatch (419 Page Expired) - especially for logout
            if ($e instanceof \Illuminate\Session\TokenMismatchException) {
                // If it's a logout attempt, just redirect to home
                if ($request->is('logout') || $request->is('*/logout')) {
                    // Clear the session and redirect
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    return redirect('/')->with('status', __('You have been logged out.'));
                }
                // For other routes, redirect back with error
                return redirect()->back()->withErrors([
                    'csrf' => __('Your session has expired. Please try again.'),
                ]);
            }

            if ($request->expectsJson()) {
                if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                    return response()->json([
                        'success' => false,
                        'error' => [
                            'type' => 'Unauthenticated',
                            'message' => 'Unauthenticated.',
                        ],
                    ], 401);
                }

                if ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
                    return response()->json([
                        'success' => false,
                        'error' => [
                            'type' => 'Forbidden',
                            'message' => $e->getMessage() ?: 'Forbidden',
                        ],
                    ], 403);
                }

                if ($e instanceof \Illuminate\Validation\ValidationException) {
                    return response()->json([
                        'success' => false,
                        'error' => [
                            'type' => 'ValidationException',
                            'message' => 'The given data was invalid.',
                            'errors' => $e->errors(),
                        ],
                    ], 422);
                }

                if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                    return response()->json([
                        'success' => false,
                        'error' => [
                            'type' => 'NotFound',
                            'message' => 'Resource not found',
                        ],
                    ], 404);
                }

                $status = 500;
                if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface) {
                    $status = $e->getStatusCode();
                }
                return response()->json([
                    'success' => false,
                    'error' => [
                        'type' => class_basename($e),
                        'message' => config('app.debug') ? $e->getMessage() : 'Server error',
                    ],
                ], $status);
            }
        });
    })->create();
