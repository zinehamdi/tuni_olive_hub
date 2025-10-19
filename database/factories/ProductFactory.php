<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $isOil = $this->faker->boolean(50);
        $oilQualities = ['EVOO','VIRGIN','LAMPANTE','premium','medium','foodservice'];
        $oliveQualities = ['premium','medium','foodservice'];
        
        // Updated variety system - all 17 Tunisian olive varieties
        // These match the translation keys in lang files
        $allVarieties = [
            // Local Tunisian varieties (most common)
            'chemlali',
            'chetoui',
            'oueslati',
            'zalmati',
            'zarrazi',
            'barouni',
            'meski',
            'chemchali',
            'gerboui',
            'sayali',
            // Imported varieties
            'arbequina',
            'arbosana',
            'koroneiki',
            'picholine',
            // Rare/special varieties
            'adefou',
            'boudaoud',
            'fougi-gtar',
            // Blends
            'blend'
        ];
        
        return [
            'seller_id' => User::factory(),
            'type' => $isOil ? 'oil' : 'olive',
            'variety' => $this->faker->randomElement($allVarieties),
            'quality' => $isOil ? $this->faker->randomElement($oilQualities) : $this->faker->randomElement($oliveQualities),
            'is_organic' => $this->faker->boolean(30),
            'volume_liters' => $isOil ? $this->faker->randomFloat(2, 0, 1000) : null,
            'weight_kg' => !$isOil ? $this->faker->randomFloat(2, 0, 1000) : null,
            'price' => $this->faker->randomFloat(3, 5, 50),
            'stock' => $this->faker->randomFloat(3, 0, 1000),
            'media' => [],
        ];
    }
}
