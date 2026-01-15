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
                'password' => Hash::make('password'),
            ]
        );

        Profile::factory()->create([
            'user_id' => $admin->id,
        ]);

        $consultant = User::updateOrCreate(
            ['email' => 'consultant@consultant.com'],
            [
                'fullname' => 'Consultant User',
                'password' => Hash::make('password'),
            ]
        );

        Profile::factory()->create([
            'user_id' => $consultant->id,
        ]);

        $client = User::updateOrCreate(
            ['email' => 'client@client.com'],
            [
                'fullname' => 'Client User',
                'password' => Hash::make('password'),
            ]
        );

        Profile::factory()->create([
            'user_id' => $client->id,
        ]);

        // Assign roles if you are using Spatie Permission
        $admin->assignRole('admin');
        $consultant->assignRole('consultant');
        $client->assignRole('client');
    }
}
