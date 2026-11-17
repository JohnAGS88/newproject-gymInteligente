<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Exercise\StoreExerciseRequest;
use App\Http\Requests\Exercise\UpdateExerciseRequest;
use App\Models\ExerciseGymGif;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExerciseGymGifController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', ExerciseGymGif::class);

        return response()->json(ExerciseGymGif::where('is_active', true)->orderBy('name')->get());
    }

    public function store(StoreExerciseRequest $request): JsonResponse
    {
        $this->authorize('create', ExerciseGymGif::class);

        $exercise = ExerciseGymGif::create($request->validated());

        return response()->json($exercise, 201);
    }

    public function show(ExerciseGymGif $exercise): JsonResponse
    {
        $this->authorize('view', $exercise);

        return response()->json($exercise);
    }

    public function update(UpdateExerciseRequest $request, ExerciseGymGif $exercise): JsonResponse
    {
        $this->authorize('update', $exercise);

        $exercise->update($request->validated());

        return response()->json($exercise);
    }

    public function destroy(Request $request, ExerciseGymGif $exercise): JsonResponse
    {
        $this->authorize('delete', $exercise);

        $before = $exercise->toArray();

        $exercise->delete();

        activity()
            ->performedOn($exercise)
            ->causedBy($request->user())
            ->withProperties([
                'ip_address' => $request->ip(),
                'before' => $before,
                'after' => null,
            ])
            ->log('Ejercicio eliminado del catálogo');

        return response()->json(null, 204);
    }
}