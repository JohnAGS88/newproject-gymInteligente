<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Permisos granulares por recurso.
     *
     * @var list<string>
     */
    private const PERMISSIONS = [
        'view-branches',
        'manage-branches',
        'view-plans',
        'manage-plans',
        'view-members',
        'manage-members',
        'view-memberships',
        'manage-memberships',
        'view-own-membership',
        'view-payments',
        'record-payments',
        'refund-payments',
        'view-own-payments',
        'view-checkins',
        'manage-checkins',
        'validate-qr-checkin',
        'view-reports',
        'view-exercises',
        'manage-exercises',
        'issue-qr-token',
        'manage-roles',
    ];

    /**
     * @var array<string, list<string>>
     */
    private const ROLE_PERMISSIONS = [
        'superadmin' => self::PERMISSIONS,
        'admin' => [
            'view-branches',
            'view-plans',
            'manage-plans',
            'view-members',
            'manage-members',
            'view-memberships',
            'manage-memberships',
            'view-payments',
            'record-payments',
            'refund-payments',
            'view-checkins',
            'manage-checkins',
            'validate-qr-checkin',
            'view-reports',
            'view-exercises',
            'manage-exercises',
        ],
        'receptionist' => [
            'view-members',
            'view-memberships',
            'view-plans',
            'view-payments',
            'record-payments',
            'view-checkins',
            'manage-checkins',
            'validate-qr-checkin',
            'view-exercises',
        ],
        'trainer' => [
            'view-members',
            'view-memberships',
            'view-exercises',
            'manage-exercises',
        ],
        'member' => [
            'view-own-membership',
            'view-own-payments',
            'issue-qr-token',
            'view-plans',
            'view-exercises',
        ],
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (self::PERMISSIONS as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        foreach (self::ROLE_PERMISSIONS as $roleName => $permissions) {
            $role = Role::findOrCreate($roleName, 'web');
            $role->syncPermissions($permissions);
        }
    }
}