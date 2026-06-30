<?php

namespace Database\Factories;


use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Commodity>
 */
class CommodityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'item_code' => 'BRG-'.fake()->unique()->numberBetween(1000, 9999).fake()->numberBetween(100, 999),
            'name' => fake()->word(),
            'brand' => fake()->company(),
            'material' => fake()->word(),
            'year_of_purchase' => fake()->numberBetween(2010, now()->year),
            'condition' => fake()->randomElement([1, 2, 3]),
            'quantity' => fake()->numberBetween(50, 200),
            'price' => fake()->numberBetween(5000, 500000),
            'price_per_item' => fake()->numberBetween(2500, 150000),
            'note' => fake()->optional()->sentence(10),
        ];
    }
}
