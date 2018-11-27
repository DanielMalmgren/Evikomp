<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'list users']);
        Permission::create(['name' => 'list all lessons']);

        // create roles and assign created permissions

        $role = Role::create(['name' => 'Admin']);
        //$user = User::where('email', 'daniel.malmgren@itsam.se')->get()->first();
        //$user->assignRole('Admin');

        $role = Role::create(['name' => 'WorkplaceAdmin']);
        $role->givePermissionTo('list users');
    }
}
