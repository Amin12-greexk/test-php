<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $productionPrice = $this->faker->numberBetween(10000, 50000);
        return [
            'name' => $this->faker->words(3, true),
            'production_price' => $productionPrice,
            'selling_price' => $productionPrice * $this->faker->randomFloat(2, 1.2, 1.5),
        ];
    }
}