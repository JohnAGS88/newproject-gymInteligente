<?php

namespace App\Http\Requests\Membership;

use App\Http\Requests\ApiRequest;

class FreezeMembershipRequest extends ApiRequest
{
    /**
     * @return array<string, array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'frozen_until' => ['nullable', 'date', 'after:today'],
        ];
    }
}