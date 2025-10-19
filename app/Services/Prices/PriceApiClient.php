<?php

declare(strict_types=1);

namespace App\Services\Prices;

use Illuminate\Support\Facades\Http;

class PriceApiClient
{
    public function fetchToday(): array
    {
        $url = env('PRICES_SOURCE_URL');
        $key = env('PRICES_API_KEY');
        if (!$url || !$key) {
            return [];
        }
        $resp = Http::withHeaders(['Authorization' => 'Bearer '.$key])->get(rtrim($url,'/').'/today');
        if (!$resp->ok()) return [];
        $json = $resp->json();
        // Basic shape normalization
        return [
            'date' => $json['date'] ?? now()->toDateString(),
            'global_oil_usd_ton' => $json['global_oil_usd_ton'] ?? null,
            'tunis_baz_tnd_kg' => $json['tunis_baz_tnd_kg'] ?? null,
            'organic_tnd_l' => $json['organic_tnd_l'] ?? null,
            'by_governorate_json' => $json['by_governorate_json'] ?? null,
        ];
    }
}
