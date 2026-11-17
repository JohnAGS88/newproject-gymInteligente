<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        Branch::query()->upsert([
            [
                'id' => (string) \Illuminate\Support\Str::uuid7(),
                'name' => 'Sucursal Centro',
                'address' => 'Av. Principal 123, Centro',
                'phone' => '+52 55 1000 0001',
                'timezone' => 'America/Mexico_City',
                'is_active' => true,
            ],
            [
                'id' => (string) \Illuminate\Support\Str::uuid7(),
                'name' => 'Sucursal Norte',
                'address' => 'Blvd. Norte 456, Zona Norte',
                'phone' => '+52 55 1000 0002',
                'timezone' => 'America/Mexico_City',
                'is_active' => true,
            ],
        ], ['id'], ['name', 'address', 'phone', 'timezone', 'is_active']);
    }
}