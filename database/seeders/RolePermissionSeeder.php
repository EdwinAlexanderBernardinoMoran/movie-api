<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    private const GUARD = 'api';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $rolePermissions = [
            'viewer' => ['view'],
            'editor' => ['view', 'write'],
            'admin' => ['view', 'write', 'delete'],
        ];

        $permissions = collect($rolePermissions)->flatten()->unique();

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => self::GUARD]);
        }

        foreach ($rolePermissions as $role => $rolePermissionNames) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => self::GUARD])->syncPermissions($rolePermissionNames);
        }
    }
}
