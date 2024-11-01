<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\User;

class CoursesTableSeeder extends Seeder
{
    public function run()
    {
        $user1 = User::find(1);
        $user2 = User::find(2);

        if ($user1) {
            Course::create([
                'name' => 'Curso Batatinha',
                'slug' => 'curso-batatinha',
                'description' => 'Como fritar batata',
                'user_id' => $user1->id,
            ]);
        }

        if ($user2) {
            Course::create([
                'name' => 'Curso Cadeirada',
                'slug' => 'curso-cadeirada',
                'description' => 'Como tacar a cadeira no seu amiguinho',
                'user_id' => $user2->id,
            ]);
        }
    }
}
