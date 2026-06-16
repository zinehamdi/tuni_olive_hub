<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiV1RoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_ping_is_public(): void
    {
        $this->getJson('/api/v1/ping')->assertOk();
    }

    public function test_listings_is_public(): void
    {
        $response = $this->getJson('/api/v1/listings');
        $response->assertOk();
    }

    public function test_listings_ok_when_authenticated(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->getJson('/api/v1/listings')->assertOk();
    }
}
