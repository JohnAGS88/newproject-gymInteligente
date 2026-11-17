<?php

namespace App\Http\Requests\Membership;

use App\Enums\MembershipStatus;
use App\Http\Requests\ApiRequest;
use Illuminate\Validation\Rule;

class StoreMembershipRequest extends ApiRequest
{
    /**
     * @return array<string, array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'uuid', 'exists:users,id'],
            'branch_id' => ['required', 'uuid', 'exists:branches,id'],
            'membership_plan_id' => ['required', 'uuid', 'exists:membership_plans,id'],
            'status' => ['sometimes', Rule::in(MembershipStatus::values())],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'frozen_until' => ['nullable', 'date'],
        ];
    }
}