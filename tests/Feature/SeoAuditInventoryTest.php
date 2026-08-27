<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Article;
use App\Models\Listing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\StrategicSeoArticlesSeeder;

class SeoAuditInventoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_real_seo_url_inventory(): void
    {
        // Seed Strategic Articles
        $this->seed(StrategicSeoArticlesSeeder::class);

        $urls = [
            // Legacy Locked Redirects
            ['/prices', 301, '/ar/prices'],
            ['/?lang=en', 301, '/en'],
            ['/?lang=fr', 301, '/fr'],

            // National Tunisian Price Hub
            ['/ar/prices', 200, null],
            ['/fr/prices', 200, null],
            ['/en/prices', 200, null],

            // International Global Price Hub (Localized Semantic Slugs)
            ['/ar/' . rawurlencode('أسعار-زيت-الزيتون-العالمية'), 200, null],
            ['/fr/prix-huile-olive-international', 200, null],
            ['/en/international-olive-oil-prices', 200, null],

            // International Aliases (Single-hop 301 redirect to canonical)
            ['/ar/international-olive-oil-prices', 301, '/ar/' . rawurlencode('أسعار-زيت-الزيتون-العالمية')],
            ['/fr/international-olive-oil-prices', 301, '/fr/prix-huile-olive-international'],

            // Programmatic B2B Pages
            ['/en/bulk-tunisian-olive-oil', 200, null],
            ['/fr/huile-olive-tunisienne-en-vrac', 200, null],
            ['/en/tunisian-olive-oil-suppliers', 200, null],
            ['/en/olive-oil-mills-tunisia', 200, null],
            ['/en/olive-oil-packers-tunisia', 200, null],
            ['/en/private-label-olive-oil-tunisia', 200, null],

            // Core Static Pages
            ['/ar/about', 200, null],
            ['/fr/about', 200, null],
            ['/en/about', 200, null],
            ['/ar/catalog', 200, null],
            ['/en/catalog', 200, null],
            ['/sitemap.xml', 200, null],
        ];

        // Seeded & Dynamic Active Articles in DB
        foreach (Article::where('is_active', true)->get() as $art) {
            $urls[] = ["/en/articles/{$art->id}", 200, null];
            $urls[] = ["/fr/articles/{$art->id}", 200, null];
            $urls[] = ["/ar/articles/{$art->id}", 200, null];
        }

        $passed = 0;
        foreach ($urls as $item) {
            [$url, $expectedStatus, $expectedTarget] = $item;
            $response = $this->get($url);

            $this->assertEquals($expectedStatus, $response->getStatusCode(), "Failed checking status for {$url}");

            if ($expectedStatus === 301 && $expectedTarget) {
                $response->assertRedirect($expectedTarget);
            }
            $passed++;
        }

        $this->assertEquals(count($urls), $passed);
    }
}
