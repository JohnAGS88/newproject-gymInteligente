<?php

namespace Database\Factories;

use App\Models\ExerciseGymGif;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExerciseGymGif>
 */
class ExerciseGymGifFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'muscle_group' => fake()->randomElement(['pecho', 'espalda', 'piernas', 'hombros', 'bíceps', 'tríceps', 'core']),
            'equipment' => fake()->randomElement(['mancuernas', 'barra', 'máquina', 'peso corporal', 'polea']),
            'difficulty' => fake()->randomElement(['principiante', 'intermedio', 'avanzado']),
            'gif_url' => 'https://example.com/gifs/'.fake()->slug().'.gif',
            'instructions' => [fake()->sentence(), fake()->sentence()],
            'is_active' => true,
        ];
    }
}