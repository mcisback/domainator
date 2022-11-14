<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'users.manage']);
        Permission::create(['name' => 'domains.manage']);

        $userPermissions = [
            Permission::create(['name' => 'domains.request']),
        ];

        // create roles and assign created permissions

        // this can be done as separate statements

//        $role->givePermissionTo('edit articles');

        // or may be done by chaining
        $staff = Role::create(['name' => 'staff']);

        foreach ($userPermissions as $permission) {
            $staff->givePermissionTo($permission);
        }

        $role = Role::create(['name' => 'admin']);
        $role->givePermissionTo(Permission::all());

    }
}
