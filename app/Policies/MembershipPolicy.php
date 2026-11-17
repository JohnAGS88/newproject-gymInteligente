<?php

namespace App\Policies;

use App\Models\Membership;
use App\Models\User;

class MembershipPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view-memberships') || $user->can('view-own-membership');
    }

    public function view(User $user, Membership $membership): bool
    {
        if ($user->can('view-memberships')) {
            return true;
        }

        return $user->can('view-own-membership') && $this->owns($user, $membership);
    }

    public function create(User $user): bool
    {
        return $user->can('manage-memberships');
    }

    public function update(User $user, Membership $membership): bool
    {
        return $user->can('manage-memberships');
    }

    public function delete(User $user, Membership $membership): bool
    {
        return $user->can('manage-memberships');
    }

    public function freeze(User $user, Membership $membership): bool
    {
        return $user->can('manage-memberships');
    }

    public function unfreeze(User $user, Membership $membership): bool
    {
        return $user->can('manage-memberships');
    }

    private function owns(User $user, Membership $membership): bool
    {
        return $user->getKey() === $membership->user_id;
    }
}