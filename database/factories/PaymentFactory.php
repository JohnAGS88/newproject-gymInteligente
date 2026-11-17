<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'membership_id' => Membership::factory(),
            'branch_id' => Branch::factory(),
            'amount' => fake()->numberBetween(2000, 15000),
            'currency' => 'usd',
            'method' => 'cash',
            'status' => 'succeeded',
            'stripe_payment_intent_id' => null,
            'reference' => fake()->bothify('REF-####-????'),
            'paid_at' => now(),
            'metadata' => null,
        ];
    }
}