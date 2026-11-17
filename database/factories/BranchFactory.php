<?php

namespace Database\Factories;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Branch>
 */
class BranchFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company().' Sucursal',
            'address' => fake()->streetAddress(),
            'phone' => fake()->phoneNumber(),
            'timezone' => fake()->timezone(),
            'is_active' => true,
        ];
    }
}