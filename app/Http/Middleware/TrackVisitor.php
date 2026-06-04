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
        $deviceUuid = $request->cookie('zintoop_device_uuid');
        if (!$deviceUuid) {
            $deviceUuid = (string) \Illuminate\Support\Str::uuid();
        }

        // Only track GET requests and ignore api/admin/livewire requests
        if ($request->isMethod('GET') && !$request->expectsJson() && !$request->is('admin*') && !$request->is('livewire*')) {
            $ip = $request->ip();
            $date = now()->toDateString();
            
            $cacheKey = "visitor_{$ip}_{$date}";
            
            if (!Cache::has($cacheKey)) {
                try {
                    $userAgent = $request->header('User-Agent');
                    $truncatedUserAgent = $userAgent ? substr($userAgent, 0, 255) : null;
                    $device = $this->detectDevice($userAgent);
                    
                    $country = null;
                    $city = null;
                    if ($ip && $ip !== '127.0.0.1' && $ip !== '::1') {
                        try {
                            $geoResponse = \Illuminate\Support\Facades\Http::timeout(3)->get("http://ip-api.com/json/{$ip}");
                            if ($geoResponse->successful() && $geoResponse->json('status') === 'success') {
                                $country = $geoResponse->json('country');
                                $city = $geoResponse->json('city');
                            }
                        } catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::warning("Geolocation failed: " . $e->getMessage());
                        }
                    }

                    Visitor::updateOrCreate(
                        ['ip_address' => $ip, 'visited_date' => $date],
                        [
                            'user_agent' => $truncatedUserAgent,
                            'device' => $device,
                            'country' => $country,
                            'city' => $city,
                        ]
                    )->increment('hits');
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning("Visitor tracking failed: " . $e->getMessage());
                }
                
                Cache::put($cacheKey, true, now()->addMinutes(60));
            }
        }

        $response = $next($request);

        // Attach persistent cookie lasting 5 years if not already present
        if (!$request->hasCookie('zintoop_device_uuid')) {
            $response->headers->setCookie(cookie()->forever('zintoop_device_uuid', $deviceUuid));
        }

        return $response;
    }

    private function detectDevice($userAgent)
    {
        if (!$userAgent) return 'Unknown';
        
        $isMobile = preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $userAgent);
        return $isMobile ? 'Mobile' : 'Desktop';
    }
}
