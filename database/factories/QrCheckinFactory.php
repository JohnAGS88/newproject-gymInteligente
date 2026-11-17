<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\QrCheckin;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QrCheckin>
 */
class QrCheckinFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'branch_id' => Branch::factory(),
            'type' => 'qr',
            'device_id' => fake()->uuid(),
            'token_jti' => hash('sha256', fake()->uuid()),
            'checked_in_at' => now(),
            'status' => 'valid',
            'ip_address' => fake()->ipv4(),
        ];
    }
}