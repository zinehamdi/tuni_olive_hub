<?php

namespace Database\Factories;

use App\Models\Listing;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $qty = $this->faker->randomFloat(3, 1, 100);
        $price = $this->faker->randomFloat(3, 5, 50);
        return [
            'buyer_id' => User::factory(),
            'seller_id' => User::factory(),
            'listing_id' => Listing::factory(),
            'qty' => $qty,
            'unit' => $this->faker->randomElement(['l','kg','ton']),
            'price_unit' => $price,
            'total' => $qty * $price,
            'payment_method' => $this->faker->randomElement(['cod','d17','flouci']),
            'status' => 'pending',
        ];
    }
}
