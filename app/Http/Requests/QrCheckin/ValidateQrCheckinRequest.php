<?php

namespace App\Http\Requests\QrCheckin;

use App\Enums\QrCheckinType;
use App\Http\Requests\ApiRequest;
use Illuminate\Validation\Rule;

class ValidateQrCheckinRequest extends ApiRequest
{
    /**
     * @return array<string, array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'token' => ['required', 'string', 'max:2048'],
            'branch_id' => ['required', 'uuid', 'exists:branches,id'],
            'type' => ['required', Rule::in(QrCheckinType::values())],
            'device_id' => ['nullable', 'string', 'max:255'],
        ];
    }
}