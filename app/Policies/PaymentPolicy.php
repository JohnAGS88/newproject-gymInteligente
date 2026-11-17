<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view-payments') || $user->can('view-own-payments');
    }

    public function view(User $user, Payment $payment): bool
    {
        if ($user->can('view-payments')) {
            return true;
        }

        return $user->can('view-own-payments') && $this->owns($user, $payment);
    }

    public function create(User $user): bool
    {
        return $user->can('record-payments');
    }

    public function refund(User $user, Payment $payment): bool
    {
        return $user->can('refund-payments');
    }

    private function owns(User $user, Payment $payment): bool
    {
        return $user->getKey() === $payment->user_id;
    }
}