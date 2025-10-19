<?php
/**
 * RoleAuthorization Middleware
 * وسيط التحقق من الصلاحيات حسب الدور
 * Checks user role against allowed roles for the route
 */
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RoleAuthorization
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, $roles, true)) {
            // تعليق: رفض الوصول إذا لم يكن الدور مصرحًا به
            // EN: Deny access if role is not authorized
            throw new HttpException(403, __('auth.forbidden_action'));
        }
        return $next($request);
    }
}
