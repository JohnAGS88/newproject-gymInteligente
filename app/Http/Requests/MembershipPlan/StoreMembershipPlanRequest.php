<?php

namespace App\Http\Requests\MembershipPlan;

use App\Enums\BillingPeriod;
use App\Http\Requests\ApiRequest;
use Illuminate\Validation\Rule;

class StoreMembershipPlanRequest extends ApiRequest
{
    /**
     * @return array<string, array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'integer', 'min:0'],
            'billing_period' => ['required', Rule::in(BillingPeriod::values())],
            'duration_days' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}