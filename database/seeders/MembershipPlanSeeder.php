<?php

namespace Database\Seeders;

use App\Models\MembershipPlan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MembershipPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Básico Mensual',
                'price' => 49900, // $499.00 MXN en centavos
                'billing_period' => 'monthly',
                'duration_days' => 30,
                'description' => 'Acceso a sala de pesas en horario regular.',
            ],
            [
                'name' => 'Premium Trimestral',
                'price' => 129900,
                'billing_period' => 'quarterly',
                'duration_days' => 90,
                'description' => 'Acceso completo + clases grupales.',
            ],
            [
                'name' => 'Elite Anual',
                'price' => 449900,
                'billing_period' => 'annual',
                'duration_days' => 365,
                'description' => 'Acceso total, entrenador y beneficios premium.',
            ],
        ];

        foreach ($plans as $plan) {
            MembershipPlan::query()->updateOrCreate(
                ['name' => $plan['name']],
                [
                    'id' => (string) Str::uuid7(),
                    ...$plan,
                    'is_active' => true,
                ],
            );
        }
    }
}