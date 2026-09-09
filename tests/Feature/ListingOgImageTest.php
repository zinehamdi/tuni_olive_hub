<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Listing;
use App\Models\Product;
use App\Models\User;
use App\Services\OgImageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListingOgImageTest extends TestCase
{
    use RefreshDatabase;

    public function test_og_image_endpoint_returns_jpeg_and_caches(): void
    {
        $user = User::factory()->create();
        $product = Product::create([
            'seller_id' => $user->id,
            'type' => 'oil',
            'variety' => 'Chemlali',
            'quality' => 'EVOO',
            'price' => '15.00',
            'stock' => '1000',
            'volume_liters' => '1000',
        ]);

        $listing = Listing::create([
            'product_id' => $product->id,
            'seller_id' => $user->id,
            'status' => 'active',
            'price' => '15.00',
            'currency' => 'TND',
            'unit' => 'L',
        ]);

        $response = $this->get(route('listings.og_image', $listing));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/jpeg');

        // Test show page includes the dynamic og:image route
        $showResponse = $this->get('/ar/listings/' . $listing->getRouteKey());
        $showResponse->assertStatus(200);
        $showResponse->assertSee($listing->getRouteKey() . '/og-image.jpg', false);
    }
}
