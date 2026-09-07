<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Listing;
use App\Models\Article;
use App\Helpers\Obfuscator;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SeoAndObfuscationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. Obfuscator Helper: Reversible Base62 + Feistel-salt encoding & decoding.
     */
    public function test_obfuscator_helper_encode_decode_roundtrip(): void
    {
        $testIds = [1, 2, 42, 100, 999, 12345, 65535, 100000];

        foreach (['listing', 'user', 'transaction'] as $context) {
            foreach ($testIds as $id) {
                $hash = Obfuscator::encode($id, $context);
                $this->assertNotEmpty($hash);
                $this->assertGreaterThanOrEqual(6, strlen($hash));

                $decoded = Obfuscator::decode($hash, $context);
                $this->assertEquals($id, $decoded, "Failed decoding {$hash} for context {$context} and ID {$id}");
            }
        }

        // Invalid or corrupted hashes should return null
        $this->assertNull(Obfuscator::decode('invalid_xyz', 'listing'));
        $this->assertNull(Obfuscator::decode('', 'listing'));
        $this->assertNull(Obfuscator::decode('000000', 'listing'));
    }

    /**
     * 2. Listing Model: getRouteKey generates Hashid, resolveRouteBinding handles both Hashid and numeric ID.
     */
    public function test_listing_hashid_resolution_and_legacy_backward_compatibility(): void
    {
        $user = User::factory()->create();
        $listing = Listing::factory()->create([
            'seller_id' => $user->id,
            'status' => 'active',
        ]);

        $hashid = $listing->getRouteKey();
        $this->assertNotEmpty($hashid);
        $this->assertNotEquals((string) $listing->id, $hashid);

        // Access via new Hashid URL -> 200 OK
        $responseHash = $this->get("/ar/listings/{$hashid}");
        $responseHash->assertStatus(200);

        // Access via legacy numeric URL -> 200 OK (100% backward compatibility for shared WhatsApp/FB links)
        $responseLegacy = $this->get("/ar/listings/{$listing->id}");
        $responseLegacy->assertStatus(200);

        // Invalid hash -> 404
        $responseInvalid = $this->get('/ar/listings/xyzInvalidHash99');
        $responseInvalid->assertStatus(404);
    }

    /**
     * 3. User Model: getRouteKey generates Hashid and resolves properly.
     */
    public function test_user_hashid_resolution_and_legacy_compatibility(): void
    {
        $user = User::factory()->create();
        $hashid = $user->getRouteKey();

        $this->assertNotEmpty($hashid);
        $this->assertEquals($user->id, Obfuscator::decode($hashid, 'user'));

        // resolveRouteBinding with hashid
        $resolved = (new User)->resolveRouteBinding($hashid);
        $this->assertEquals($user->id, $resolved->id);

        // resolveRouteBinding with legacy numeric id
        $resolvedLegacy = (new User)->resolveRouteBinding((string) $user->id);
        $this->assertEquals($user->id, $resolvedLegacy->id);
    }

    /**
     * 4. Component A: x-default must point to /en (never /ar) in HTML output.
     */
    public function test_x_default_points_to_english_in_layouts(): void
    {
        // Public homepage /ar
        $responseAr = $this->get('/ar');
        $responseAr->assertStatus(200);
        $responseAr->assertSee('hreflang="x-default"', false);
        $this->assertStringContainsString('/en', $responseAr->getContent());

        // Guest layout (e.g. /ar/login)
        $responseLogin = $this->get('/ar/login');
        $responseLogin->assertStatus(200);
        $responseLogin->assertSee('hreflang="x-default"', false);
        $this->assertStringContainsString('/en/login', $responseLogin->getContent());
        $this->assertStringContainsString('/ar/login', $responseLogin->getContent());
        $this->assertStringContainsString('/fr/login', $responseLogin->getContent());
    }

    /**
     * 5. Component B: Root '/' Smart Accept-Language Detection (302).
     */
    public function test_root_accept_language_detection(): void
    {
        // Arabic preference -> /ar
        $resAr = $this->withHeaders(['Accept-Language' => 'ar,ar-TN;q=0.9'])->get('/');
        $resAr->assertStatus(302);
        $resAr->assertRedirect('/ar');

        // French preference -> /fr
        $resFr = $this->withHeaders(['Accept-Language' => 'fr-FR,fr;q=0.9,en;q=0.8'])->get('/');
        $resFr->assertStatus(302);
        $resFr->assertRedirect('/fr');

        // English preference -> /en
        $resEn = $this->withHeaders(['Accept-Language' => 'en-US,en;q=0.9'])->get('/');
        $resEn->assertStatus(302);
        $resEn->assertRedirect('/en');

        // Explicit ?lang= parameter takes priority
        $resQuery = $this->get('/?lang=fr');
        $resQuery->assertStatus(302);
        $resQuery->assertRedirect('/fr');
    }

    /**
     * 6. Component B: Legacy 301 Redirects & Fallback soft-404 handling.
     */
    public function test_legacy_301_redirects_and_soft_404_handling(): void
    {
        // /gulf/catalog -> /#products
        $resGulf = $this->get('/gulf/catalog');
        $resGulf->assertStatus(301);
        $this->assertStringContainsString('#products', $resGulf->headers->get('Location'));

        $resGulfSub = $this->get('/gulf/catalog/item-123');
        $resGulfSub->assertStatus(301);
        $this->assertStringContainsString('#products', $resGulfSub->headers->get('Location'));

        // Double-locale paths -> single locale
        $resDouble = $this->get('/fr/zh/about');
        $resDouble->assertStatus(301);
        $resDouble->assertRedirect('/fr/about');

        // Deleted or missing numeric listing -> redirect to /#products (soft-404 fix)
        $resDeletedListing = $this->get('/ar/listings/999999');
        $resDeletedListing->assertStatus(301);
        $resDeletedListing->assertRedirect('/ar/#products');

        // Deleted or missing article -> redirect to /articles
        $resDeletedArticle = $this->get('/en/articles/999999');
        $resDeletedArticle->assertStatus(301);
        $resDeletedArticle->assertRedirect('/en/articles');
    }

    /**
     * 7. Component C: robots.txt disallow rules for private / administrative endpoints.
     */
    public function test_robots_txt_contains_disallow_rules(): void
    {
        $robotsPath = public_path('robots.txt');
        $this->assertFileExists($robotsPath);

        $content = file_get_contents($robotsPath);
        $this->assertStringContainsString('Disallow: /admin/', $content);
        $this->assertStringContainsString('Disallow: /dashboard', $content);
        $this->assertStringContainsString('Disallow: /api/', $content);
        $this->assertStringContainsString('Disallow: /profile', $content);
        $this->assertStringContainsString('Disallow: /messages/', $content);
        $this->assertStringContainsString('Sitemap: https://zintoop.com/sitemap.xml', $content);
    }

    /**
     * 8. Component D: Sitemap XML generates obfuscated listing URLs and x-default points to /en.
     */
    public function test_sitemap_xml_contains_obfuscated_urls_and_correct_hreflang(): void
    {
        $user = User::factory()->create();
        $listing = Listing::factory()->create([
            'seller_id' => $user->id,
            'status' => 'active',
        ]);

        $response = $this->get('/sitemap.xml');
        $response->assertStatus(200);

        $content = $response->getContent();

        // Check x-default points to /en
        $this->assertStringContainsString('hreflang="x-default"', $content);
        $this->assertStringContainsString('/en', $content);

        // Check listing URL uses Hashid (not numeric ID)
        $hashid = $listing->getRouteKey();
        $this->assertStringContainsString('/listings/' . $hashid, $content);
        $this->assertStringNotContainsString('/listings/' . $listing->id . '<', $content);
    }
}
