<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\PriceDaily;
use App\Services\Prices\PriceIngestor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class PricesTest extends TestCase
{
    use RefreshDatabase;

    public function test_today_endpoint_returns_cached_payload(): void
    {
        $this->withoutExceptionHandling();
        // Seed a cached value
        $ingestor = app(PriceIngestor::class);
        $row = $ingestor->ingestToday();
        $key = PriceIngestor::cacheKeyToday();
        $this->assertTrue(Cache::has($key));

        $res = $this->getJson('/api/v1/prices/today');
        $res->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.date', now()->toDateString())
            ->assertJsonStructure(['success','data' => ['date','global_oil_usd_ton','tunis_baz_tnd_kg','organic_tnd_l','by_governorate']]);

        // Performance header in testing
        $qCount = (int) ($res->headers->get('X-Query-Count') ?? 0);
        $this->assertLessThanOrEqual(3, $qCount);

        // Ensure DB row exists and values match types
        $this->assertNotNull(PriceDaily::find(now()->toDateString()));
    }

    public function test_history_requires_valid_range_and_paginates(): void
    {
        $this->withoutExceptionHandling();
        // Seed 5 days
        for ($i = 0; $i < 5; $i++) {
            $d = now()->copy()->subDays($i)->toDateString();
            PriceDaily::updateOrCreate(['date' => $d], [
                'global_oil_usd_ton' => 1200 + $i,
                'tunis_baz_tnd_kg' => 3.5 + $i/10,
                'organic_tnd_l' => 7.2 + $i/10,
                'by_governorate_json' => ['Sousse' => 3.0 + $i/10],
            ]);
        }

        // Invalid: to before from
        $bad = $this->getJson('/api/v1/prices/history?from=2025-01-10&to=2025-01-01');
        $bad->assertStatus(422);

        $from = now()->subDays(4)->toDateString();
        $to = now()->toDateString();
        $res = $this->getJson("/api/v1/prices/history?from={$from}&to={$to}&per_page=2");
        $res->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['success','data', 'meta' => ['current_page','per_page','total','last_page','from','to']]);
        $this->assertCount(2, $res->json('data'));
    }
}
