<?php

namespace Database\Factories;

use App\Models\CashMovement;
use App\Models\CashShift;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CashMovement>
 */
class CashMovementFactory extends Factory
{
    protected $model = CashMovement::class;

    public function definition(): array
    {
        return [
            'cash_shift_id' => CashShift::factory(),
            'user_id' => null,
            'type' => 'keluar',
            'amount' => fake()->numberBetween(5000, 50000),
            'reason' => fake()->sentence(3),
        ];
    }

    public function cashIn(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'masuk',
        ]);
    }
}
