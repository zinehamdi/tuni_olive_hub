<?php

namespace Database\Factories;

use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition(): array
    {
        return [
            'reviewer_id' => User::factory(),
            'target_user_id' => User::factory(),
            'object_type' => null,
            'object_id' => null,
            'rating' => $this->faker->numberBetween(3, 5),
            'title' => $this->faker->sentence(3),
            'comment' => $this->faker->paragraph(),
            'photos' => [],
            'is_verified_purchase' => $this->faker->boolean(50),
            'is_visible' => true,
        ];
    }
}
