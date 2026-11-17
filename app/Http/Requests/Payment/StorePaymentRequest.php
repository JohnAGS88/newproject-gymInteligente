<?php

namespace App\Http\Requests\Payment;

use App\Enums\PaymentMethod;
use App\Http\Requests\ApiRequest;
use Illuminate\Validation\Rule;

class StorePaymentRequest extends ApiRequest
{
    /**
     * @return array<string, array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'membership_id' => ['required', 'uuid', 'exists:memberships,id'],
            'amount' => ['required', 'integer', 'min:1'],
            'currency' => ['required', 'string', 'size:3'],
            'method' => ['required', Rule::in(PaymentMethod::values())],
            'reference' => ['nullable', 'string', 'max:64'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}