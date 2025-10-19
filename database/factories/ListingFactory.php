<?php

namespace Database\Factories;

use App\Models\Listing;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ListingFactory extends Factory
{
    protected $model = Listing::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'seller_id' => User::factory(),
            'status' => 'active',
            'min_order' => $this->faker->randomFloat(3, 1, 100),
            'payment_methods' => ['cash','d17'],
            'delivery_options' => ['pickup','carrier'],
        ];
    }
}
