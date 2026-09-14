<?php

namespace Tests\Feature;

use App\Models\Listing;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_user_role()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $user = User::factory()->create([
            'name' => 'Mohamed Ali',
            'email' => 'm.ali@example.com',
            'phone' => '+21698123456',
            'role' => 'mill',
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.users.update', $user), [
            'name' => 'Mohamed Ali Updated',
            'email' => 'm.ali@example.com',
            'phone' => '+21698123456',
            'role' => 'packer',
            'show_contact_info' => '1',
            'show_address' => '0',
        ]);

        $response->assertRedirect(route('admin.users.edit', $user));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Mohamed Ali Updated',
            'role' => 'packer',
        ]);
    }

    public function test_admin_can_update_listing_unit()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $seller = User::factory()->create([
            'role' => 'farmer',
        ]);

        $product = Product::factory()->create([
            'seller_id' => $seller->id,
            'type' => 'oil',
            'variety' => 'chemlali',
        ]);

        $listing = Listing::factory()->create([
            'seller_id' => $seller->id,
            'product_id' => $product->id,
            'price' => 22.5,
            'quantity' => 1000,
            'unit' => 'liter',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.listings.update', $listing), [
            'category' => 'oil',
            'variety' => 'chemlali',
            'quality' => 'extra_virgin',
            'price' => 25.0,
            'quantity' => 50,
            'unit' => 'ton',
            'min_order' => 1,
            'status' => 'active',
        ]);

        $response->assertRedirect(route('admin.listings'));
        $this->assertDatabaseHas('listings', [
            'id' => $listing->id,
            'unit' => 'ton',
            'price' => 25.0,
        ]);
    }
}
