<?php

namespace App\Http\Requests\Membership;

use App\Enums\MembershipStatus;
use App\Http\Requests\ApiRequest;
use Illuminate\Validation\Rule;

class UpdateMembershipRequest extends ApiRequest
{
    /**
     * @return array<string, array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['sometimes', 'required', 'uuid', 'exists:users,id'],
            'branch_id' => ['sometimes', 'required', 'uuid', 'exists:branches,id'],
            'membership_plan_id' => ['sometimes', 'required', 'uuid', 'exists:membership_plans,id'],
            'status' => ['sometimes', Rule::in(MembershipStatus::values())],
            'starts_at' => ['sometimes', 'required', 'date'],
            'ends_at' => ['sometimes', 'required', 'date', 'after:starts_at'],
            'frozen_until' => ['nullable', 'date'],
        ];
    }
}