<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Base para todos los FormRequest de la API.
 *
 * - La autorización fina vive en las Policies (se invoca desde el controlador).
 * - Sanitiza los strings de entrada (strip_tags + trim) antes de validar.
 */
abstract class ApiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    abstract public function rules(): array;

    protected function prepareForValidation(): void
    {
        $sanitized = [];

        foreach ($this->all() as $key => $value) {
            if (is_string($value)) {
                $sanitized[$key] = trim(strip_tags($value));
            }
        }

        $this->merge($sanitized);
    }
}