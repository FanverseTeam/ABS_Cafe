<?php

namespace Database\Factories;

use App\Models\Menu;
use App\Models\MenuVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MenuVariant>
 */
class MenuVariantFactory extends Factory
{
    protected $model = MenuVariant::class;

    public function definition(): array
    {
        return [
            'menu_id' => Menu::factory(),
            'name' => fake()->randomElement(['Hot', 'Iced', 'Large Iced']),
            'price' => fake()->numberBetween(15000, 50000),
            'is_default' => true,
        ];
    }
}
