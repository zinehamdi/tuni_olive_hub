<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Address;
use App\Models\ExportOffer;
use App\Models\Listing;
use App\Models\Load;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        if (!\App\Models\User::where('email', 'admin@olive.local')->exists()) {
            User::factory()->create([
                'name' => 'Admin',
                'email' => 'admin@olive.local',
                'role' => Role::Admin->value,
            ]);
        }

        // Create at least one user per role
        $roles = [
            Role::Farmer, Role::Mill, Role::Packer, Role::Carrier, Role::TraderCarrier,
            Role::Restaurant, Role::Consumer, Role::Exporter,
        ];

        $usersByRole = [];
        foreach ($roles as $r) {
            $usersByRole[$r->value] = User::factory()->create(['role' => $r->value]);
        }

        // Additional random users
        User::factory(5)->create();

        // Addresses for some users
        Address::factory(10)->create();

        // Products and listings
        $products = Product::factory(20)->create();
        foreach ($products as $product) {
            Listing::factory()->create([
                'product_id' => $product->id,
                'seller_id' => $product->seller_id,
            ]);
        }

        // Orders
        Order::factory(5)->create();

        // Loads
        Load::factory(5)->create(['owner_id' => $usersByRole[Role::Farmer->value]->id]);

        // Export offers
        ExportOffer::factory(2)->create(['seller_id' => $usersByRole[Role::Exporter->value]->id]);

        // Trips for carrier
        Trip::factory(3)->create(['carrier_id' => $usersByRole[Role::Carrier->value]->id]);

        // Reviews
        Review::factory(5)->create();
    }
}
