<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition(): array
    {
        $labels = ['Warehouse', 'Farm', 'Mill', 'Store', 'Office', 'Main Location'];
        
        return [
            'user_id' => User::factory(),
            'governorate' => $this->faker->randomElement(['Tunis','Sfax','Sousse','Kairouan','Gabes','Nabeul']),
            'delegation' => null,
            'lat' => $this->faker->latitude(33,37),
            'lng' => $this->faker->longitude(8,12),
            'label' => $this->faker->randomElement($labels),
        ];
    }
}
