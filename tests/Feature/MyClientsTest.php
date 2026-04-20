<?php

namespace Tests\Feature;

use App\Models\Path;
use App\Models\Plan;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MyClientsTest extends TestCase
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
        $this->client->update(['consultant_id' => $this->consultant->id]);
    }

    private function asConsultant(): static
    {
        return $this->actingAs($this->consultant, 'sanctum');
    }

    // ── Auth & Role guards ───────────────────────────────────────────────────

    public function test_unauthenticated_cannot_access_my_clients(): void
    {
        $this->getJson('/api/my-clients')->assertUnauthorized();
    }

    public function test_client_role_cannot_access_my_clients(): void
    {
        $this->actingAs($this->client, 'sanctum')
            ->getJson('/api/my-clients')
            ->assertForbidden();
    }

    public function test_admin_can_access_my_clients(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/my-clients')
            ->assertOk();
    }

    // ── List clients ─────────────────────────────────────────────────────────

    public function test_consultant_sees_only_their_own_clients(): void
    {
        $otherConsultant = User::factory()->create();
        $otherConsultant->assignRole('consultant');

        $otherClient = User::factory()->create();
        $otherClient->update(['consultant_id' => $otherConsultant->id]);

        $response = $this->asConsultant()->getJson('/api/my-clients')->assertOk();

        $ids = collect($response->json('data'))->pluck('id');
        $this->assertTrue($ids->contains($this->client->id));
        $this->assertFalse($ids->contains($otherClient->id));
    }

    public function test_client_list_includes_progress_fields(): void
    {
        $response = $this->asConsultant()->getJson('/api/my-clients')->assertOk();

        $item = collect($response->json('data'))->firstWhere('id', $this->client->id);
        $this->assertNotNull($item);
        $this->assertArrayHasKey('path_count', $item);
        $this->assertArrayHasKey('progress_pct', $item);
        $this->assertArrayHasKey('done_steps', $item);
        $this->assertArrayHasKey('total_steps', $item);
    }

    // ── Client detail ────────────────────────────────────────────────────────

    public function test_consultant_can_fetch_client_detail(): void
    {
        $this->asConsultant()
            ->getJson("/api/my-clients/{$this->client->id}")
            ->assertOk()
            ->assertJsonStructure(['user' => ['id', 'fullname', 'email'], 'paths']);
    }

    public function test_consultant_cannot_fetch_another_consultants_client(): void
    {
        $stranger = User::factory()->create();

        $this->asConsultant()
            ->getJson("/api/my-clients/{$stranger->id}")
            ->assertForbidden();
    }

    // ── Assign path ──────────────────────────────────────────────────────────

    public function test_consultant_can_assign_path_to_client(): void
    {
        $path = Path::create(['name' => 'Laravel Track', 'consultant_id' => $this->consultant->id]);

        $this->asConsultant()
            ->postJson("/api/my-clients/{$this->client->id}/paths", ['path_id' => $path->id])
            ->assertOk();

        // A plan must exist linking consultant → client → path
        $plan = Plan::where('consultant_id', $this->consultant->id)
            ->whereHas('clients', fn ($q) => $q->where('users.id', $this->client->id))
            ->whereHas('paths', fn ($q) => $q->where('paths.id', $path->id))
            ->first();

        $this->assertNotNull($plan);
    }

    public function test_assigning_path_twice_does_not_duplicate(): void
    {
        $path = Path::create(['name' => 'Laravel Track', 'consultant_id' => $this->consultant->id]);

        $this->asConsultant()->postJson("/api/my-clients/{$this->client->id}/paths", ['path_id' => $path->id]);
        $this->asConsultant()->postJson("/api/my-clients/{$this->client->id}/paths", ['path_id' => $path->id]);

        $count = Plan::where('consultant_id', $this->consultant->id)
            ->whereHas('clients', fn ($q) => $q->where('users.id', $this->client->id))
            ->first()
            ?->paths()->where('paths.id', $path->id)->count();

        $this->assertEquals(1, $count);
    }

    public function test_assign_requires_valid_path_id(): void
    {
        $this->asConsultant()
            ->postJson("/api/my-clients/{$this->client->id}/paths", ['path_id' => 99999])
            ->assertUnprocessable();
    }

    // ── Remove path ──────────────────────────────────────────────────────────

    public function test_consultant_can_remove_path_from_client(): void
    {
        $path = Path::create(['name' => 'Laravel Track', 'consultant_id' => $this->consultant->id]);

        $this->asConsultant()->postJson("/api/my-clients/{$this->client->id}/paths", ['path_id' => $path->id]);

        $this->asConsultant()
            ->deleteJson("/api/my-clients/{$this->client->id}/paths/{$path->id}")
            ->assertOk();

        $count = Plan::where('consultant_id', $this->consultant->id)
            ->whereHas('clients', fn ($q) => $q->where('users.id', $this->client->id))
            ->first()
            ?->paths()->where('paths.id', $path->id)->count();

        $this->assertEquals(0, $count ?? 0);
    }
}
