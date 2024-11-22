<?php

namespace Database\Seeders;

use App\Enums\Permission as PermissionEnum;
use App\Enums\Role as RoleEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        /**
         * Reset cached roles and permissions
         *
         * @see https://spatie.be/docs/laravel-permission/v6/advanced-usage/seeding
         */
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Standard user permissions
        Permission::create(['name' => PermissionEnum::VIEW_PROFILE]);
        Permission::create(['name' => PermissionEnum::UPDATE_PROFILE]);
        /** @var Role $standardRole */
        $standardRole = Role::create(['name' => RoleEnum::USER]);
        $standardRole->givePermissionTo(Permission::all());

        // Admin Permissions
        Permission::create(['name' => PermissionEnum::CREATE_USERS]);
        Permission::create(['name' => PermissionEnum::UPDATE_USERS]);
        Permission::create(['name' => PermissionEnum::DELETE_USERS]);
        Permission::create(['name' => PermissionEnum::VIEW_USERS]);
        Permission::create(['name' => PermissionEnum::VIEW_ROLES]);
        Permission::create(['name' => PermissionEnum::VIEW_PERMISSIONS]);
        /** @var Role $adminRole */
        $adminRole = Role::create(['name' => RoleEnum::ADMIN]);
        $adminRole->givePermissionTo(Permission::all());

        /**
         * Superuser role. We allow all permissions through here
         *
         * @see \App\Providers\AuthServiceProvider
         *
         * @var Role $superUserRole
         */
        Role::create(['name' => RoleEnum::SUPER_USER]);

        /**
         * Reset cached roles and permissions
         *
         * @see https://spatie.be/docs/laravel-permission/v6/advanced-usage/seeding
         */
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
