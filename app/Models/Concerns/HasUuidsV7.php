<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;

/**
 * Claves primarias UUID v7 (ordenables en el tiempo, no adivinables).
 *
 * Convención del proyecto: todas las tablas de dominio expuestas por la API
 * usan UUID v7 como clave primaria para impedir enumeración/IDOR.
 */
trait HasUuidsV7
{
    use HasUuids;

    /**
     * Genera un nuevo UUID v7 (time-ordered, mejor localidad de índice).
     */
    public function newUniqueId(): string
    {
        return (string) Str::uuid7();
    }
}