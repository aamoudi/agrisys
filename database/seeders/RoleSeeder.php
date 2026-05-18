<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Roles
        $admin = Role::create(['name' => 'admin']);
        $user = Role::create(['name' => 'user']);

        // 2. Assign ALL permissions to owner
        $admin->givePermissionTo(Permission::all());

        // 3. Assign specific permissions to the User
        $user->givePermissionTo([
            'view farms',   'create farms',   'edit farms',   'delete farms',
            'view crops',   'create crops',   'edit crops',   'delete crops',
            'view inputs',  'create inputs',  'edit inputs',  'delete inputs',
            'view harvests','create harvests','edit harvests','delete harvests',
            'view reports',
        ]);    
    }
}
