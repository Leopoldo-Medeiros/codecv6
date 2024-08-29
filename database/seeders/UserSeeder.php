<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criando o usuário administrador
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin',
                'role' => 'admin', // Ensure role is included
                'password' => Hash::make('admin_password')
            ]
        );

        // Criando o usuário 'client'
        $clientUser = User::updateOrCreate(
            ['email' => 'client@client.com'],
            [
                'name' => 'Client',
                'role' => 'client', // Ensure role is included
                'password' => Hash::make('client_password')
            ]
        );

        // Atribuindo a role de administrador ao usuário 'admin'
        $adminUser->assignRole('admin');

        // Atribuindo a role de usuário ao usuário 'client'
        $clientUser->assignRole('client');
    }
}
