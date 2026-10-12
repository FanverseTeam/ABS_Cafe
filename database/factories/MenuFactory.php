<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Menu>
 */
class MenuFactory extends Factory
{
    protected $model = Menu::class;

    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => ucfirst(fake()->unique()->words(2, true)),
            'price' => fake()->numberBetween(15000, 50000),
            'description' => fake()->sentence(),
            'image' => null,
            'is_available' => true,
            'is_best_seller' => false,
            'stock' => 50,
            'min_stock' => 10,
            'unit' => 'porsi',
            'sold_count' => 0,
        ];
    }

    public function unavailable(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_available' => false,
            'stock' => 0,
        ]);
    }

    public function bestSeller(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_best_seller' => true,
        ]);
    }

    public function lowStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => 3,
            'min_stock' => 10,
        ]);
    }
}
