<?php

namespace Database\Factories;

use App\Models\Table;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Table>
 */
class TableFactory extends Factory
{
    protected $model = Table::class;

    public function definition(): array
    {
        return [
            'number' => fake()->unique()->numberBetween(1, 999),
            'capacity' => fake()->randomElement([2, 4, 6]),
            'status' => 'tersedia',
            'location_notes' => 'Lantai 1 Indoor',
        ];
    }

    public function occupied(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'terpakai',
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'nonaktif',
        ]);
    }
}
