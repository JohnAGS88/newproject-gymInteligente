<?php

namespace App\Policies;

use App\Models\MembershipPlan;
use App\Models\User;

class MembershipPlanPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view-plans');
    }

    public function view(User $user, MembershipPlan $membershipPlan): bool
    {
        return $user->can('view-plans');
    }

    public function create(User $user): bool
    {
        return $user->can('manage-plans');
    }

    public function update(User $user, MembershipPlan $membershipPlan): bool
    {
        return $user->can('manage-plans');
    }

    public function delete(User $user, MembershipPlan $membershipPlan): bool
    {
        return $user->can('manage-plans');
    }
}