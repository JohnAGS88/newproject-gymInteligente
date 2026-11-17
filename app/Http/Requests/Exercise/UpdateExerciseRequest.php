<?php

namespace App\Http\Requests\Exercise;

use App\Http\Requests\ApiRequest;

class UpdateExerciseRequest extends ApiRequest
{
    /**
     * @return array<string, array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'muscle_group' => ['nullable', 'string', 'max:255'],
            'equipment' => ['nullable', 'string', 'max:255'],
            'difficulty' => ['nullable', 'string', 'max:32'],
            'gif_url' => ['sometimes', 'required', 'url', 'max:2048'],
            'instructions' => ['nullable', 'array'],
            'instructions.*' => ['string', 'max:500'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}