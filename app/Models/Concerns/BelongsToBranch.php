<?php

namespace App\Models\Concerns;

use App\Models\Scopes\BranchScope;

/**
 * Aplica el alcance global de sucursal a los modelos operativos
 * que llevan `branch_id` (memberships, payments, qr_checkins...).
 */
trait BelongsToBranch
{
    public static function bootBelongsToBranch(): void
    {
        static::addGlobalScope(new BranchScope);
    }
}