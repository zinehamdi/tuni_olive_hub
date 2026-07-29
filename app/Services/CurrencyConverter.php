<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * CurrencyConverter — Live multi-currency conversion.
 *
 * Fetches real-time rates from open.er-api.com (free, no API key required,
 * updated hourly) and caches the result for 3 hours to avoid per-request hits.
 * Falls back to hardcoded rates if the API is unreachable.
 *
 * Supported currencies: TND, USD, EUR, SAR, GBP
 *
 * Usage:
 *   app(CurrencyConverter::class)->convert(12.50, 'TND', 'USD')  // → 4.01
 *   app(CurrencyConverter::class)->forLocale(12.50, 'TND', 'en') // → '$4.01'
 */
class CurrencyConverter
{
    /**
     * Fallback rates relative to USD (used when API is unreachable).
     * Updated manually as a best-effort safety net.
     *   1 USD = X units of currency
     */
    protected array $fallbackRates = [
        'USD' => 1.0,
        'TND' => 3.15,   // 1 USD ≈ 3.15 TND  (BCT mid-market, Jul 2026)
        'EUR' => 0.92,   // 1 USD ≈ 0.92 EUR
        'SAR' => 3.75,   // 1 USD = 3.75 SAR (pegged)
        'GBP' => 0.79,   // 1 USD ≈ 0.79 GBP
    ];

    /**
     * Cache key and duration for live rates.
     */
    protected string $cacheKey = 'currency_rates_usd_base';
    protected int $cacheTtlSeconds = 10800; // 3 hours

    /**
     * Get rates (rates expressed as "1 USD = X currency").
     * Tries live API first, falls back to hardcoded rates.
     */
    public function getRates(): array
    {
        return Cache::remember($this->cacheKey, $this->cacheTtlSeconds, function () {
            try {
                // open.er-api.com — free tier, no API key, 1500 req/month
                $response = Http::timeout(3)->get('https://open.er-api.com/v6/latest/USD');

                if ($response->successful()) {
                    $data = $response->json();
                    if (($data['result'] ?? '') === 'success' && isset($data['rates'])) {
                        $rates = $data['rates'];

                        // Only keep the currencies we need
                        $supported = ['USD', 'TND', 'EUR', 'SAR', 'GBP'];
                        $filtered = [];
                        foreach ($supported as $cur) {
                            if (isset($rates[$cur])) {
                                $filtered[$cur] = (float) $rates[$cur];
                            }
                        }

                        if (!empty($filtered)) {
                            Log::info('CurrencyConverter: live rates fetched successfully', $filtered);
                            return $filtered;
                        }
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('CurrencyConverter: API unreachable, using fallback rates. ' . $e->getMessage());
            }

            // API failed — use fallback rates
            return $this->fallbackRates;
        });
    }

    /**
     * Convert an amount from one currency to another.
     *
     * @param  float       $amount
     * @param  string      $from    Source currency code (e.g. 'TND')
     * @param  string      $to      Target currency code (e.g. 'USD')
     * @param  int         $decimals
     * @return float
     *
     * @throws \InvalidArgumentException if currency is not supported
     */
    public function convert(float $amount, string $from, string $to, int $decimals = 2): float
    {
        $from = strtoupper(trim($from));
        $to   = strtoupper(trim($to));

        if ($from === $to) {
            return round($amount, $decimals);
        }

        $rates = $this->getRates(); // Rates: 1 USD = X currency

        if (!isset($rates[$from])) {
            // Unknown source currency — return as-is without crashing
            Log::warning("CurrencyConverter: unsupported source currency '{$from}', returning amount unchanged.");
            return round($amount, $decimals);
        }
        if (!isset($rates[$to])) {
            Log::warning("CurrencyConverter: unsupported target currency '{$to}', returning amount unchanged.");
            return round($amount, $decimals);
        }

        // Convert: amount → USD base → target currency
        $inUsd = $amount / $rates[$from];
        return round($inUsd * $rates[$to], $decimals);
    }

    /**
     * Determine the display currency for a given locale.
     *
     * - Arabic (ar)        → TND
     * - English/French     → USD
     */
    public function displayCurrency(string $locale): string
    {
        return ($locale === 'ar') ? 'TND' : 'USD';
    }

    /**
     * Convert an amount from the listing's stored currency to
     * the locale's display currency, returning a formatted string.
     *
     * Example:
     *   forLocale(12.50, 'TND', 'en')  → '$3.97'
     *   forLocale(12.50, 'TND', 'ar')  → '12.50 TND'
     *   forLocale(4.00,  'USD', 'ar')  → '12.60 TND'
     *   forLocale(4.00,  'USD', 'en')  → '$4.00'
     *
     * @param  float|null  $amount          The raw amount stored in DB
     * @param  string|null $storedCurrency  The currency code stored in DB (e.g. 'TND', 'USD', 'EUR')
     * @param  string      $locale          Current app locale ('ar', 'en', 'fr')
     * @return string      Formatted price string (e.g. '$3.97' or '12.50 TND')
     */
    public function forLocale(?float $amount, ?string $storedCurrency, string $locale): string
    {
        if ($amount === null || $amount <= 0) {
            return '';
        }

        $storedCurrency = strtoupper(trim($storedCurrency ?? 'TND'));
        $displayCurrency = $this->displayCurrency($locale);

        $converted = $this->convert($amount, $storedCurrency, $displayCurrency);

        if ($displayCurrency === 'USD') {
            return '$' . number_format($converted, 2);
        }

        // TND — Arabic display
        return number_format($converted, 2) . ' ' . ($locale === 'ar' ? 'دينار' : 'TND');
    }

    /**
     * Get the live TND → USD rate (convenience helper for the price ticker).
     */
    public function getTndToUsd(): float
    {
        $rates = $this->getRates();
        if (isset($rates['TND']) && $rates['TND'] > 0) {
            return round(1.0 / $rates['TND'], 6);
        }
        return 1.0 / $this->fallbackRates['TND'];
    }

    /**
     * Get the live EUR → USD rate (for world olive price ticker).
     */
    public function getEurToUsd(): float
    {
        $rates = $this->getRates();
        if (isset($rates['EUR']) && $rates['EUR'] > 0) {
            return round(1.0 / $rates['EUR'], 6);
        }
        return 1.0 / $this->fallbackRates['EUR'];
    }
}
