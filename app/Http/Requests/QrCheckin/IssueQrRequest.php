<?php

namespace App\Http\Requests\QrCheckin;

use App\Http\Requests\ApiRequest;

class IssueQrRequest extends ApiRequest
{
    /**
     * @return array<string, array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'device_id' => ['required', 'string', 'max:255'],
        ];
    }
}