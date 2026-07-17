<?php

namespace Database\Seeders;

use App\Models\Path;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $consultant = User::where('email', 'consultant@consultant.com')->firstOrFail();
        $client = User::where('email', 'client@client.com')->firstOrFail();

        $plan = Plan::create([
            'name' => 'PHP Developer Track',
            'description' => 'Full track covering modern PHP, debugging, observability, database performance, and Git workflow.',
            'consultant_id' => $consultant->id,
        ]);

        $pathIds = Path::pluck('id')->toArray();
        $plan->paths()->sync($pathIds);
        $plan->clients()->attach($client->id);

        $this->command->info("Created plan '{$plan->name}' with ".count($pathIds).' paths, assigned to client@client.com');
    }
}
