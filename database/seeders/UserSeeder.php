<?php

namespace Database\Seeders;

use App\Models\Profile;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeders.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'fullname' => 'Admin User',
                'password' => Hash::make('password'), // Only set the password
            ]
        );

        Profile::factory()->create([
            'user_id' => $admin->id
        ]);

        $client = User::updateOrCreate(
            ['email' => 'client@client.com'],
            [
                'fullname' => 'Client User',
                'password' => Hash::make('password'), // Only set the password
            ]
        );

        Profile::factory()->create([
            'user_id' => $client->id
        ]);

        // Assign roles if you are using Spatie Permission
        $admin->assignRole('admin');
        $client->assignRole('client');
    }
}
