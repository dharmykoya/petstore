<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['credit_card', 'cash_on_delivery', 'bank_transfer']);
        $details = $this->getPaymentDetails($type);

        return [
            'uuid' => $this->faker->uuid,
            'type' => $type,
            'details' => json_encode($details),
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ];
    }

    private function getPaymentDetails($type)
    {
        return match ($type) {
            "credit_card" => [
                'last_six_digits' => $this->faker->creditCardNumber(null, true),
                'expiry' => $this->faker->creditCardExpirationDateString,
            ],
            "cash_on_delivery" => [
                'first_name' => $this->faker->firstName,
                'last_name' => $this->faker->lastName,
                'address' => $this->faker->address,
            ],
            'bank_transfer' => [
                'bank_swift_code' => $this->faker->swiftBicNumber,
                'iban' => $this->faker->iban(null),
                'name' => $this->faker->name,
                'ref_code' => $this->faker->uuid,
            ],
            default => [],
        };
    }
}


