<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;

class CoursesTableSeeder extends Seeder
{
    public function run()
    {
        $user1 = User::find(1);
        $user2 = User::find(2);

        if ($user1) {
            Course::create([
                'name' => 'PHP Fundamentals',
                'slug' => 'php-fundamentals',
                'description' => 'An introduction to modern PHP basics, types, and object-oriented programming.',
                'user_id' => $user1->id,
            ]);
        }

        if ($user2) {
            Course::create([
                'name' => 'Laravel Essentials',
                'slug' => 'laravel-essentials',
                'description' => 'Core Laravel concepts for beginners: routing, controllers, Eloquent, and Blade.',
                'user_id' => $user2->id,
            ]);
        }
    }
}
