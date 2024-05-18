<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class DefaultUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verifica se o usuário (default) já existe, se nao existir, cria um novo
        $defaultRole = Role::firstOrCreate(['name' => 'default', 'guard_name' => 'web']);

        // Encontra ou cria um usuário com o email 'default@default' e senha 'default'
        $defaultUser = User::firstOrCreate(
            ['name' => 'Default user', 'password' => bcrypt('password')], // Substituir a senha por uma senha segura
            ['email' => 'default@default']
        );

        // Associar o usuário ao papel padrão
        $defaultUser->assignRole($defaultRole);
    }
}
