<?php

namespace App\Models;

use App\Models\Concerns\HasUuidsV7;
use Database\Factories\ExerciseGymGifFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExerciseGymGif extends Model
{
    /** @use HasFactory<ExerciseGymGifFactory> */
    use HasFactory, HasUuidsV7;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'muscle_group',
        'equipment',
        'difficulty',
        'gif_url',
        'instructions',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'instructions' => 'array',
            'is_active' => 'boolean',
        ];
    }
}