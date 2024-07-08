<?php

namespace Database\Factories;

use App\Models\OrderStatus;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::inRandomOrder()->first();
        $orderStatus = OrderStatus::inRandomOrder()->first();
        $payment = null;

        if (in_array($orderStatus->title, ['Paid', 'Shipped'])) {
            $payment = Payment::factory()->create();
        }

        return [
            'user_id' => $user->id,
            'order_status_id' => $orderStatus->id,
            'payment_id' => $payment ? $payment->id : null,
            'uuid' => $this->faker->uuid,
            'products' => json_encode([
                ['product_id' => $this->faker->numberBetween(1, 10), 'quantity' => $this->faker->numberBetween(1, 5), 'amount' => $this->faker->randomFloat(2, 2, 20)],
                ['product_id' => $this->faker->numberBetween(1, 10), 'quantity' => $this->faker->numberBetween(1, 5), 'amount' => $this->faker->randomFloat(2, 10, 60)]
            ]),
            'address' => json_encode([
                'line1' => $this->faker->streetAddress,
                'city' => $this->faker->city,
                'state' => $this->faker->state,
                'zip' => $this->faker->postcode
            ]),
            'delivery_fee' => $this->faker->randomFloat(2, 5, 20),
            'amount' => $this->faker->randomFloat(2, 20, 200),
            'shipped_at' => in_array($orderStatus->title, ['Shipped', 'Delivered']) ? now() : null,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ];
    }
}
