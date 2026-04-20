<?php

namespace Tests\Feature\Api;

use App\Models\Path;
use App\Models\Plan;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MyPathsApiTest extends TestCase
{
    use RefreshDatabase;

    private User $consultant;

    private User $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);

        $this->consultant = User::factory()->create();
        $this->consultant->assignRole('consultant');

        $this->client = User::factory()->create();
        $this->client->assignRole('client');
    }

    public function test_client_only_sees_paths_assigned_via_their_plan(): void
    {
        $assignedPath = Path::factory()->create();
        $unassignedPath = Path::factory()->create();

        $plan = Plan::factory()->create(['consultant_id' => $this->consultant->id]);
        $plan->clients()->attach($this->client->id);
        $plan->paths()->attach($assignedPath->id);

        $response = $this->actingAs($this->client, 'sanctum')
            ->getJson('/api/my-paths')
            ->assertOk();

        $ids = collect($response->json('data'))->pluck('id')->toArray();

        $this->assertContains($assignedPath->id, $ids);
        $this->assertNotContains($unassignedPath->id, $ids);
    }

    public function test_client_sees_no_paths_when_not_enrolled_in_any_plan(): void
    {
        Path::factory()->count(3)->create();

        $this->actingAs($this->client, 'sanctum')
            ->getJson('/api/my-paths')
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function test_client_sees_paths_from_multiple_plans(): void
    {
        $path1 = Path::factory()->create();
        $path2 = Path::factory()->create();

        $plan1 = Plan::factory()->create(['consultant_id' => $this->consultant->id]);
        $plan1->clients()->attach($this->client->id);
        $plan1->paths()->attach($path1->id);

        $plan2 = Plan::factory()->create(['consultant_id' => $this->consultant->id]);
        $plan2->clients()->attach($this->client->id);
        $plan2->paths()->attach($path2->id);

        $response = $this->actingAs($this->client, 'sanctum')
            ->getJson('/api/my-paths')
            ->assertOk();

        $ids = collect($response->json('data'))->pluck('id')->toArray();

        $this->assertContains($path1->id, $ids);
        $this->assertContains($path2->id, $ids);
    }

    public function test_client_does_not_see_paths_from_other_clients_plan(): void
    {
        $otherClient = User::factory()->create();
        $otherClient->assignRole('client');

        $path = Path::factory()->create();
        $plan = Plan::factory()->create(['consultant_id' => $this->consultant->id]);
        $plan->clients()->attach($otherClient->id);
        $plan->paths()->attach($path->id);

        $response = $this->actingAs($this->client, 'sanctum')
            ->getJson('/api/my-paths')
            ->assertOk();

        $ids = collect($response->json('data'))->pluck('id')->toArray();
        $this->assertNotContains($path->id, $ids);
    }

    public function test_unauthenticated_cannot_access_my_paths(): void
    {
        $this->getJson('/api/my-paths')->assertUnauthorized();
    }

    public function test_response_has_correct_structure(): void
    {
        $path = Path::factory()->create();
        $plan = Plan::factory()->create(['consultant_id' => $this->consultant->id]);
        $plan->clients()->attach($this->client->id);
        $plan->paths()->attach($path->id);

        $this->actingAs($this->client, 'sanctum')
            ->getJson('/api/my-paths')
            ->assertOk()
            ->assertJsonStructure(['data' => [['id', 'name']]]);
    }
}
