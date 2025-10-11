<?php

namespace Database\Factories;

use App\Models\Load;
use App\Models\User;
use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoadFactory extends Factory
{
    protected $model = Load::class;

    public function definition(): array
    {
        return [
            'owner_id' => User::factory(),
            'kind' => $this->faker->randomElement(['oil','olive']),
            'qty' => $this->faker->randomFloat(3, 100, 10000),
            'unit' => $this->faker->randomElement(['l','kg','ton']),
            'pickup_addr_id' => Address::factory(),
            'dropoff_addr_id' => Address::factory(),
            'deadline_at' => now()->addDays($this->faker->numberBetween(1, 14)),
            'price_floor' => $this->faker->randomFloat(3, 100, 500),
            'price_ceiling' => $this->faker->randomFloat(3, 600, 1200),
            'status' => 'new',
        ];
    }
}
