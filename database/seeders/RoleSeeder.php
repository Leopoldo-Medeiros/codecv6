<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criando a role de administrador
        Role::updateOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        // Criando a role de usuário
        Role::updateOrCreate(['name' => 'client', 'guard_name' => 'web']);
    }
}
