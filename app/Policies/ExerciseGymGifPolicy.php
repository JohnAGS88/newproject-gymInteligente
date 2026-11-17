<?php

namespace App\Policies;

use App\Models\ExerciseGymGif;
use App\Models\User;

class ExerciseGymGifPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view-exercises');
    }

    public function view(User $user, ExerciseGymGif $exerciseGymGif): bool
    {
        return $user->can('view-exercises');
    }

    public function create(User $user): bool
    {
        return $user->can('manage-exercises');
    }

    public function update(User $user, ExerciseGymGif $exerciseGymGif): bool
    {
        return $user->can('manage-exercises');
    }

    public function delete(User $user, ExerciseGymGif $exerciseGymGif): bool
    {
        return $user->can('manage-exercises');
    }
}