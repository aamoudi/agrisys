<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissions
        $permissions = [
            'view farms',   'create farms',   'edit farms',   'delete farms',
            'view crops',   'create crops',   'edit crops',   'delete crops',
            'view inputs',  'create inputs',  'edit inputs',  'delete inputs',
            'view harvests','create harvests','edit harvests','delete harvests',
            'view reports', 'view lookup', 'edit lookup', 'delete lookup',
            'manage users',
        ];

        //Create Permiisions
        foreach($permissions as $prm){
            Permission::create(['name'=>$prm]);
        }
    }
}
