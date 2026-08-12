<?php

namespace Database\Seeders;

use App\Models\System\Permission;
use App\Models\System\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $modules = ['temple', 'devotee', 'seva', 'donation', 'accounting', 'crowd', 'property', 'inventory', 'staff'];
        $actions = ['view', 'create', 'update', 'delete'];

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(
                    ['slug' => "{$module}.{$action}"],
                    ['name' => ucfirst($action)." {$module}", 'module' => $module]
                );
            }
        }

        $admin = Role::firstOrCreate(
            ['slug' => 'admin'],
            ['name' => 'Administrator', 'description' => 'Full access to all temple modules', 'is_system' => true]
        );
        $admin->permissions()->sync(Permission::pluck('id'));

        Role::firstOrCreate(
            ['slug' => 'manager'],
            ['name' => 'Temple Manager', 'description' => 'Manages day-to-day temple operations', 'is_system' => true]
        );

        Role::firstOrCreate(
            ['slug' => 'staff'],
            ['name' => 'Staff', 'description' => 'Front-desk / counter staff', 'is_system' => true]
        );

        Role::firstOrCreate(
            ['slug' => 'devotee'],
            ['name' => 'Devotee', 'description' => 'Devotee portal access only', 'is_system' => true]
        );
    }
}
