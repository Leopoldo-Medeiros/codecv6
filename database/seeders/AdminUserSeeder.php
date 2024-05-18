<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Certifique-se de que a role 'admin' existe
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        // Encontre ou crie o usuário admin
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'], // Substitua pelo e-mail do seu usuário admin
            ['name' => 'Admin User', 'password' => bcrypt('password')] // Substitua 'password' pela senha desejada
        );

        // Associe a role 'admin' ao usuário
        $adminUser->assignRole($adminRole);
    }
}
