<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderStatus>
 */
class OrderStatusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => $this->faker->uuid,
            'title' => $this->faker->randomElement(['pending payment', 'paid', 'shipped', 'delivered', 'open', 'cancelled']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
