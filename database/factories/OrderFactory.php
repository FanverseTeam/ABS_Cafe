<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        static $sequence = 0;

        $subtotal = fake()->numberBetween(20000, 120000);
        $tax = round($subtotal * 0.1, 2);

        return [
            'order_code' => '#TST-'.str_pad((string) (++$sequence), 5, '0', STR_PAD_LEFT),
            'customer_name' => fake()->name(),
            'table_number' => null,
            'order_type' => 'take_away',
            'source' => 'offline',
            'subtotal' => $subtotal,
            'tax' => $tax,
            'service_charge' => 0,
            'discount' => 0,
            'total' => $subtotal + $tax,
            'payment_method' => 'tunai',
            'payment_status' => 'menunggu_pembayaran',
            'order_status' => 'diproses',
            'cashier_name' => null,
            'paid_at' => null,
            'received_at' => now(),
        ];
    }

    public function paid(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'payment_status' => 'lunas',
                'order_status' => 'selesai',
                'paid_at' => now(),
            ];
        });
    }

    public function web(): static
    {
        return $this->state(fn (array $attributes) => [
            'source' => 'web',
            'order_status' => 'baru',
            'payment_method' => null,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'dibatalkan',
            'order_status' => 'dibatalkan',
            'cancel_reason' => 'Dibatalkan oleh test',
        ]);
    }
}
