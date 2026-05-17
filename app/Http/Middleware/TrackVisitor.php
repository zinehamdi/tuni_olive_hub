<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Models\Visitor;
use Illuminate\Support\Facades\Cache;

class TrackVisitor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only track GET requests and ignore api/admin/livewire requests
        if ($request->isMethod('GET') && !$request->expectsJson() && !$request->is('admin*') && !$request->is('livewire*')) {
            $ip = $request->ip();
            $date = now()->toDateString();
            
            $cacheKey = "visitor_{$ip}_{$date}";
            
            if (!Cache::has($cacheKey)) {
                $userAgent = $request->header('User-Agent');
                $device = $this->detectDevice($userAgent);

                Visitor::updateOrCreate(
                    ['ip_address' => $ip, 'visited_date' => $date],
                    [
                        'user_agent' => $userAgent,
                        'device' => $device,
                    ]
                )->increment('hits');
                
                Cache::put($cacheKey, true, now()->addMinutes(60));
            }
        }

        return $next($request);
    }

    private function detectDevice($userAgent)
    {
        if (!$userAgent) return 'Unknown';
        
        $isMobile = preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $userAgent);
        return $isMobile ? 'Mobile' : 'Desktop';
    }
}
