<?php

namespace Tests\Feature;

use App\Models\Listing;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserDisplayNameTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_display_name_prefers_mill_name_for_mill_owner()
    {
        $user = User::factory()->create([
            'name' => 'Ezzine Farhani',
            'role' => 'mill',
            'mill_name' => 'معصرة الفرحاني النموذجية',
        ]);

        $this->assertEquals('معصرة الفرحاني النموذجية', $user->display_name);
    }

    public function test_user_display_name_prefers_packer_name_for_packer()
    {
        $user = User::factory()->create([
            'name' => 'Mohamed Ali',
            'role' => 'packer',
            'packer_name' => 'شركة تعبئة زيت الشمال',
        ]);

        $this->assertEquals('شركة تعبئة زيت الشمال', $user->display_name);
    }

    public function test_user_display_name_prefers_company_name_or_farm_name()
    {
        $user = User::factory()->create([
            'name' => 'Salah Ben Amor',
            'role' => 'farmer',
            'farm_name' => 'ضيعة الزيتونة الخضراء',
        ]);

        $this->assertEquals('ضيعة الزيتونة الخضراء', $user->display_name);
    }

    public function test_user_display_name_falls_back_to_personal_name()
    {
        $user = User::factory()->create([
            'name' => 'Ali Mansour',
            'role' => 'normal',
            'mill_name' => null,
            'packer_name' => null,
            'company_name' => null,
            'farm_name' => null,
        ]);

        $this->assertEquals('Ali Mansour', $user->display_name);
    }

    public function test_homepage_and_catalog_render_display_name()
    {
        $user = User::factory()->create([
            'name' => 'Ezzine Farhani',
            'role' => 'mill',
            'mill_name' => 'معصرة القيروان الكبرى',
        ]);

        $product = Product::factory()->create([
            'seller_id' => $user->id,
            'type' => 'oil',
            'variety' => 'chetoui',
        ]);

        $listing = Listing::factory()->create([
            'seller_id' => $user->id,
            'product_id' => $product->id,
            'status' => 'active',
            'price' => 24.5,
        ]);

        $response = $this->get(route('home', ['locale' => 'ar']));
        $response->assertOk();
        $response->assertSee('معصرة القيروان الكبرى');
    }
}
