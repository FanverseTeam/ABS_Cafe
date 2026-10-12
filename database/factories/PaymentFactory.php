<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        $amount = fake()->numberBetween(20000, 120000);

        return [
            'order_id' => Order::factory(),
            'cashier_id' => null,
            'method' => 'tunai',
            'amount' => $amount,
            'cash_received' => $amount,
            'change_given' => 0,
            'reference' => null,
            'status' => 'lunas',
            'paid_at' => now(),
        ];
    }
}
