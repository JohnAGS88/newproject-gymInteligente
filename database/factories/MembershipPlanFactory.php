<?php

namespace Database\Factories;

use App\Models\MembershipPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MembershipPlan>
 */
class MembershipPlanFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'price' => fake()->numberBetween(2000, 15000), // cents
            'billing_period' => fake()->randomElement(['weekly', 'monthly', 'quarterly', 'semiannual', 'annual']),
            'duration_days' => fake()->numberBetween(7, 365),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}