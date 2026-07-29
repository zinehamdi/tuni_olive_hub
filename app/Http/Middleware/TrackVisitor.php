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
        // ─── EARLY BOT BAIL-OUT ───────────────────────────────────────────────────
        // Meta (Facebook/WhatsApp) and other social scrapers send OG-prefetch
        // requests BEFORE the user even clicks the link. These crawlers have no
        // session cookie, so every request triggers a new DB+GeoIP write, causing
        // the server queue to back up → 25-second "site inaccessible" for real users.
        // We detect scrapers early and skip ALL tracking for them.
        $userAgentRaw = $request->header('User-Agent', '');
        if ($this->isSocialScraper($userAgentRaw)) {
            return $next($request);
        }
        // ─────────────────────────────────────────────────────────────────────────

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
                    $userAgent = $userAgentRaw;
                    $truncatedUserAgent = $userAgent ? substr($userAgent, 0, 255) : null;
                    $device = $this->detectDevice($userAgent);
                    $isBot = $this->detectBot($userAgent);
                    
                    $country = null;
                    $city = null;
                    
                    // Only fetch GeoIP for real users to save server resources and API limits
                    if (!$isBot && $ip && $ip !== '127.0.0.1' && $ip !== '::1') {
                        try {
                            // Check if GeoIP is already cached for this IP to prevent duplicate calls
                            $geoCacheKey = "geoip_{$ip}";
                            $geoData = Cache::remember($geoCacheKey, now()->addHours(24), function () use ($ip) {
                                $geoResponse = \Illuminate\Support\Facades\Http::timeout(1.0)->get("http://ip-api.com/json/{$ip}");
                                if ($geoResponse->successful() && $geoResponse->json('status') === 'success') {
                                    return [
                                        'country' => $geoResponse->json('country'),
                                        'city' => $geoResponse->json('city')
                                    ];
                                }
                                return null;
                            });

                            if ($geoData) {
                                $country = $geoData['country'];
                                $city = $geoData['city'];
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
                            'is_bot' => $isBot,
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

    /**
     * Detect social media scrapers and link-preview crawlers.
     * These fire before the user even clicks, causing server load spikes.
     */
    private function isSocialScraper(string $userAgent): bool
    {
        if (empty($userAgent)) return false;
        
        $scraperPatterns = [
            'facebookexternalhit',  // Facebook link preview
            'facebookcatalog',       // Facebook catalog crawler
            'WhatsApp',              // WhatsApp link preview
            'Twitterbot',            // Twitter card crawler
            'LinkedInBot',           // LinkedIn preview
            'TelegramBot',           // Telegram preview
            'Slackbot',              // Slack unfurl
            'Discordbot',            // Discord embeds
            'SkypeUriPreview',       // Skype preview
            'viber',                 // Viber preview
            'ia_archiver',           // Wayback Machine
            'Applebot',              // Apple bot
            'DuckDuckBot',           // DuckDuckGo
        ];

        foreach ($scraperPatterns as $pattern) {
            if (stripos($userAgent, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }


    private function detectDevice($userAgent)
    {
        if (!$userAgent) return 'Unknown';
        
        $isMobile = preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $userAgent);
        return $isMobile ? 'Mobile' : 'Desktop';
    }

    private function detectBot($userAgent)
    {
        if (!$userAgent) return false;
        
        $botPatterns = '/(bot|crawler|spider|slurp|facebook|whatsapp|telegram|twitter|linkedin|slack|discord|skype|viber|applebot|bingbot|googlebot|yandex|yahoo)/i';
        return (bool) preg_match($botPatterns, $userAgent);
    }
}
