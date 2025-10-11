<?php

namespace Database\Factories;

use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TripFactory extends Factory
{
    protected $model = Trip::class;

    public function definition(): array
    {
        return [
            'carrier_id' => User::factory(),
            'load_ids' => [],
            'route_polyline' => null,
            'start_at' => now(),
            'delivered_at' => null,
            'distance_km' => $this->faker->randomFloat(2, 10, 800),
            'earning' => $this->faker->randomFloat(3, 100, 1200),
            'sr_code' => strtoupper($this->faker->bothify('TRP###??')),
        ];
    }
}
