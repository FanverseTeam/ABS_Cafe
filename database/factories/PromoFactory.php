<?php

namespace Database\Factories;

use App\Models\Promo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Promo>
 */
class PromoFactory extends Factory
{
    protected $model = Promo::class;

    public function definition(): array
    {
        return [
            'title' => ucfirst(fake()->unique()->words(3, true)),
            'code' => strtoupper(fake()->unique()->bothify('PROMO###??')),
            'description' => fake()->sentence(),
            'type' => 'persen',
            'value' => 10,
            'applies_to' => 'semua',
            'target_category_id' => null,
            'start_date' => now()->subDays(2),
            'end_date' => now()->addDays(5),
            'banner' => null,
            'is_active' => true,
            'min_spend' => 0,
            'quota' => null,
        ];
    }

    public function nominal(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'nominal',
            'value' => 10000,
            'min_spend' => 30000,
        ]);
    }

    public function buyXGetY(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'buy_x_get_y',
            'value' => 100,
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'start_date' => now()->subDays(10),
            'end_date' => now()->subDays(3),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
