<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
*/

uses(Tests\TestCase::class)->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
*/

/**
 * Crea un usuario autenticado con un rol dado (para tests de permisos).
 */
function actingAsRole(string $role, array $attributes = []): App\Models\User
{
    $user = App\Models\User::factory()->create($attributes);
    $user->assignRole($role);

    test()->actingAs($user);

    return $user;
}