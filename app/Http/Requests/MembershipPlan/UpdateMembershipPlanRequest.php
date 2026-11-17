<?php

namespace App\Http\Requests\MembershipPlan;

use App\Enums\BillingPeriod;
use App\Http\Requests\ApiRequest;
use Illuminate\Validation\Rule;

class UpdateMembershipPlanRequest extends ApiRequest
{
    /**
     * @return array<string, array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'price' => ['sometimes', 'required', 'integer', 'min:0'],
            'billing_period' => ['sometimes', 'required', Rule::in(BillingPeriod::values())],
            'duration_days' => ['sometimes', 'required', 'integer', 'min:1'],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}