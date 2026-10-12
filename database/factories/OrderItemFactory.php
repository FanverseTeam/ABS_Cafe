<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        $price = fake()->numberBetween(15000, 40000);
        $quantity = fake()->numberBetween(1, 3);

        return [
            'order_id' => Order::factory(),
            'menu_id' => null,
            'variant_id' => null,
            'name' => ucfirst(fake()->words(2, true)),
            'price' => $price,
            'quantity' => $quantity,
            'subtotal' => $price * $quantity,
            'notes' => null,
        ];
    }
}
