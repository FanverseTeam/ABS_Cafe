<?php

namespace Database\Factories;

use App\Models\CashShift;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CashShift>
 */
class CashShiftFactory extends Factory
{
    protected $model = CashShift::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->kasir(),
            'opened_at' => now()->subHours(3),
            'closed_at' => null,
            'opening_cash' => 200000,
            'expected_cash' => 200000,
            'closing_cash' => 0,
            'cash_difference' => 0,
            'total_sales' => 0,
            'total_transactions' => 0,
            'status' => 'buka',
            'notes' => null,
        ];
    }

    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'closed_at' => now(),
            'status' => 'tutup',
        ]);
    }
}
