<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'name' => ucfirst(fake()->unique()->word()),
            'icon' => 'Coffee',
            'order' => fake()->numberBetween(1, 10),
            'is_active' => true,
            'description' => fake()->sentence(),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
