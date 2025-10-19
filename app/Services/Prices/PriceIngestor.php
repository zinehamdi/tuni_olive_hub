<?php

declare(strict_types=1);

namespace App\Services\Prices;

use App\Models\PriceDaily;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PriceIngestor
{
    public function ingestToday(): PriceDaily
    {
        $mode = env('PRICES_SOURCE_MODE', 'dummy');
        $today = now()->toDateString();
        if ($mode === 'dummy') {
            $seed = crc32($today);
            mt_srand($seed);
            $data = [
                'date' => $today,
                'global_oil_usd_ton' => round(mt_rand(1000, 1500) + mt_rand() / mt_getrandmax(), 2),
                'tunis_baz_tnd_kg' => round(3 + mt_rand() / mt_getrandmax(), 3),
                'organic_tnd_l' => round(7 + mt_rand() / mt_getrandmax(), 3),
                'by_governorate_json' => [
                    'Kairouan' => round(3.1 + mt_rand() / mt_getrandmax(), 3),
                    'Sousse' => round(3.2 + mt_rand() / mt_getrandmax(), 3),
                ],
            ];
        } elseif ($mode === 'api') {
            $client = app(\App\Services\Prices\PriceApiClient::class);
            $attempts = 0; $data = [];
            while ($attempts < 3 && empty($data)) {
                $attempts++;
                try {
                    $data = $client->fetchToday();
                } catch (\Throwable $e) {
                    Log::warning('PriceApiClient.fetchToday failed: '.$e->getMessage(), ['attempt' => $attempts]);
                }
                if (empty($data)) usleep(100000 * $attempts); // backoff 100ms, 200ms, 300ms
            }
            if (empty($data)) {
                // fallback to dummy if API not available
                Log::notice('PriceApiClient returned empty; falling back to dummy mode');
                $seed = crc32($today);
                mt_srand($seed);
                $data = [
                    'date' => $today,
                    'global_oil_usd_ton' => round(mt_rand(1000, 1500) + mt_rand() / mt_getrandmax(), 2),
                    'tunis_baz_tnd_kg' => round(3 + mt_rand() / mt_getrandmax(), 3),
                    'organic_tnd_l' => round(7 + mt_rand() / mt_getrandmax(), 3),
                    'by_governorate_json' => [
                        'Kairouan' => round(3.1 + mt_rand() / mt_getrandmax(), 3),
                        'Sousse' => round(3.2 + mt_rand() / mt_getrandmax(), 3),
                    ],
                ];
            }
        } else {
            // unknown mode; fallback minimal record
            $data = [ 'date' => $today ];
        }
        $row = PriceDaily::updateOrCreate(['date' => $today], $data);
        $ttl = (int) env('PRICES_CACHE_TTL', 600);
        Cache::put(self::cacheKeyToday(), $row->toArray(), $ttl);
        return $row;
    }

    public static function cacheKeyToday(): string
    {
        return 'prices:today:'.now()->toDateString();
    }
}
