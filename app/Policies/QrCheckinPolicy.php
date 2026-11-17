<?php

namespace App\Policies;

use App\Models\QrCheckin;
use App\Models\User;

class QrCheckinPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view-checkins');
    }

    public function view(User $user, QrCheckin $qrCheckin): bool
    {
        return $user->can('view-checkins');
    }

    public function validate(User $user): bool
    {
        return $user->can('validate-qr-checkin');
    }

    public function issue(User $user): bool
    {
        return $user->can('issue-qr-token');
    }
}