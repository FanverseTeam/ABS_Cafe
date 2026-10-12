<?php

namespace Database\Factories;

use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reservation>
 */
class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    public function definition(): array
    {
        return [
            'code' => 'RSV-'.fake()->unique()->numerify('######').'-001',
            'customer_name' => fake()->name(),
            'phone' => fake()->numerify('08##########'),
            'party_size' => fake()->numberBetween(1, 6),
            'reservation_date' => today(),
            'reservation_time' => fake()->randomElement(['10:00', '13:00', '19:00', '20:30']),
            'table_id' => null,
            'status' => 'menunggu',
            'notes' => null,
            'created_by' => null,
        ];
    }
}
