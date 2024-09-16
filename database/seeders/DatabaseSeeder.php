<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeders.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
        ]);

        $fakeClientes = $this->command->askWithCompletion('Do you want add fake clients', ['yes',
            'no'], 'no');

        if($fakeClientes === 'yes') {
            $this->call([
                ClientsSeeder::class,
            ]);
        }
    }
}
