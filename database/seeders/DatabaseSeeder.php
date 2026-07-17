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
            CoursesTableSeeder::class,
            // Challenges + learning paths + steps, imported from database/content
            // via content:sync (idempotent). Replaces the old heredoc seeders.
            ContentSeeder::class,
            PlanSeeder::class,
            BadgesSeeder::class,
        ]);

        $fakeClientes = $this->command->askWithCompletion('Do you want add fake clients', ['yes', 'no'], 'no');

        if ($fakeClientes === 'yes') {
            $this->call([
                ClientsSeeder::class,
            ]);
        }
    }
}
