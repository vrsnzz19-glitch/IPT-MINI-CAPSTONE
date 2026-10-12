<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Plant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Plant>
 */
class PlantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'plant_name' => fake()->unique()->words(2, true),
            'scientific_name' => fake()->optional()->words(2, true),
            'category_id' => Category::factory(),
            'quantity' => fake()->numberBetween(0, 50),
            'price' => fake()->randomFloat(2, 1, 500),
            'description' => fake()->optional()->sentence(),
        ];
    }
}
