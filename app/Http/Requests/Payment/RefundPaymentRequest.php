<?php

namespace App\Http\Requests\Payment;

use App\Http\Requests\ApiRequest;

class RefundPaymentRequest extends ApiRequest
{
    /**
     * @return array<string, array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'reason' => ['nullable', 'string', 'max:255'],
        ];
    }
}