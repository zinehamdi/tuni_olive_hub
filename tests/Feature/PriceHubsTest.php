<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PriceHubsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. Test Locked Legacy Redirect for /prices
     * Must be 301 to /ar/prices in 1 hop with 0 redirect chains
     */
    public function test_locked_legacy_prices_redirect(): void
    {
        $response = $this->get('/prices');
        $response->assertStatus(301);
        $response->assertRedirect('/ar/prices');

        // Verify destination page returns 200 OK with exact self-referencing canonical
        $dest = $this->get('/ar/prices');
        $dest->assertOk();
        
        $expectedCanonical = '<link rel="canonical" href="' . url('ar/prices') . '">';
        $dest->assertSee($expectedCanonical, false);
    }

    /**
     * 2. Test Exact Canonical URLs & Hreflang for National Tunisian Price Hub
     */
    public function test_national_price_hub_exact_canonicals(): void
    {
        $locales = ['ar', 'fr', 'en'];

        foreach ($locales as $lang) {
            $response = $this->get('/' . $lang . '/prices');
            $response->assertOk();

            // Assert exact canonical tag
            $expectedCanonical = '<link rel="canonical" href="' . url($lang . '/prices') . '">';
            $response->assertSee($expectedCanonical, false);

            // Assert exact hreflang tags
            $response->assertSee('<link rel="alternate" hreflang="ar" href="' . url('ar/prices') . '">', false);
            $response->assertSee('<link rel="alternate" hreflang="fr" href="' . url('fr/prices') . '">', false);
            $response->assertSee('<link rel="alternate" hreflang="en" href="' . url('en/prices') . '">', false);
            $response->assertSee('<link rel="alternate" hreflang="x-default" href="' . url('ar/prices') . '">', false);
        }
    }

    /**
     * 3. Test Exact Canonical & Hreflang for International Global Price Hub with Localized Semantic Slugs
     */
    public function test_international_price_hub_localized_semantic_slugs(): void
    {
        $arUrl = '/ar/' . rawurlencode('أسعار-زيت-الزيتون-العالمية');
        $frUrl = '/fr/prix-huile-olive-international';
        $enUrl = '/en/international-olive-oil-prices';

        // English Hub (Canonical 200 OK)
        $resEn = $this->get($enUrl);
        $resEn->assertOk();
        $resEn->assertSee('<link rel="canonical" href="' . url('en/international-olive-oil-prices') . '">', false);
        $resEn->assertSee('<link rel="alternate" hreflang="en" href="' . url('en/international-olive-oil-prices') . '">', false);
        $resEn->assertSee('<link rel="alternate" hreflang="fr" href="' . url('fr/prix-huile-olive-international') . '">', false);
        $resEn->assertSee('<link rel="alternate" hreflang="ar" href="' . url('ar/' . rawurlencode('أسعار-زيت-الزيتون-العالمية')) . '">', false);

        // French Hub (Canonical 200 OK)
        $resFr = $this->get($frUrl);
        $resFr->assertOk();
        $resFr->assertSee('<link rel="canonical" href="' . url('fr/prix-huile-olive-international') . '">', false);

        // Arabic Hub (Canonical 200 OK)
        $resAr = $this->get($arUrl);
        $resAr->assertOk();
        $resAr->assertSee('<link rel="canonical" href="' . url('ar/' . rawurlencode('أسعار-زيت-الزيتون-العالمية')) . '">', false);

        // Aliases must 301 redirect to the localized canonical slug
        $aliasAr = $this->get('/ar/international-olive-oil-prices');
        $aliasAr->assertStatus(301);
        $aliasAr->assertRedirect($arUrl);

        $aliasFr = $this->get('/fr/international-olive-oil-prices');
        $aliasFr->assertStatus(301);
        $aliasFr->assertRedirect($frUrl);
    }

    /**
     * 4. Test Sitemap XML contains ONLY 200 Canonical URLs and NO 301 redirects
     */
    public function test_sitemap_contains_only_canonical_urls_and_no_redirects(): void
    {
        $response = $this->get('/sitemap.xml');
        $response->assertOk();
        $content = $response->getContent();

        // Must contain all 3 localized canonical national price URLs
        $this->assertStringContainsString('<loc>' . url('ar/prices') . '</loc>', $content);
        $this->assertStringContainsString('<loc>' . url('fr/prices') . '</loc>', $content);
        $this->assertStringContainsString('<loc>' . url('en/prices') . '</loc>', $content);

        // Must contain all 3 localized semantic canonical international price URLs
        $this->assertStringContainsString('<loc>' . url('ar/' . rawurlencode('أسعار-زيت-الزيتون-العالمية')) . '</loc>', $content);
        $this->assertStringContainsString('<loc>' . url('fr/prix-huile-olive-international') . '</loc>', $content);
        $this->assertStringContainsString('<loc>' . url('en/international-olive-oil-prices') . '</loc>', $content);

        // Must NOT contain the 301 legacy URL (/prices without locale prefix)
        $this->assertStringNotContainsString('<loc>' . url('prices') . '</loc>', $content);
        $this->assertStringNotContainsString('<loc>https://zintoop.com/prices</loc>', $content);
    }
}
