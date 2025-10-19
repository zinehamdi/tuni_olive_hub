<?php

namespace Database\Factories;

use App\Models\ExportOffer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExportOfferFactory extends Factory
{
    protected $model = ExportOffer::class;

    public function definition(): array
    {
        return [
            'seller_id' => User::factory(),
            'variety' => $this->faker->randomElement(['chemlali','north']),
            'spec' => $this->faker->sentence(),
            'qty_tons' => $this->faker->randomFloat(3, 10, 200),
            'incoterm' => $this->faker->randomElement(['fob','cif']),
            'port_from' => 'Rades',
            'port_to' => 'Marseilles',
            'currency' => $this->faker->randomElement(['usd','eur']),
            'unit_price' => $this->faker->randomFloat(3, 800, 2500),
            'docs' => [],
            'status' => 'active',
        ];
    }
}
