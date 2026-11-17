<?php

namespace App\Models\Scopes;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

/**
 * Alcance global de sucursal.
 *
 * Filtra las tablas operativas por la sucursal del usuario autenticado.
 * `superadmin` y los contextos sin usuario autenticado (consola, seeders,
 * tests unitarios) no se filtran. Un usuario sin sucursal asignada queda
 * "fail-closed": no ve filas de otras sucursales.
 */
class BranchScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $user = Auth::user();

        if (! $user instanceof User || $user->isSuperAdmin()) {
            return;
        }

        $branchId = $user->branch_id;

        if ($branchId !== null) {
            $builder->where($model->getTable().'.branch_id', $branchId);
        }
    }
}