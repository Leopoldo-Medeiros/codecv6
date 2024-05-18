<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin role
        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);

        // Permissions
        $editArticlePermission = Permission::create(['name' => 'edit articles', 'guard_name' => 'web']);

        // Assign permissions to roles
        $adminRole->givePermissionTo($editArticlePermission);
    }
}
