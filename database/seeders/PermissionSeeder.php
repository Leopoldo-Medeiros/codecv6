<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Creating roles and permissions
        $role = Role::create(['name' => 'admin']);
        $permission = Permission::create(['name' => 'edit articles']);
    }
}
