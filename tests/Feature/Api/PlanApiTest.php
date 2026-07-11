<?php

namespace Tests\Feature\Api;

use App\Models\Path;
use App\Models\Plan;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Full CRUD coverage for /api/plans.
 * Also covers attach/detach client operations.
 */
class PlanApiTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $consultant;

    private User $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->consultant = User::factory()->create();
        $this->consultant->assignRole('consultant');

        $this->client = User::factory()->create();
        $this->client->assignRole('client');
    }

    // ── Index ─────────────────────────────────────────────────

    #[DataProvider('authenticatedRoleProvider')]
    public function test_authenticated_users_can_list_plans(string $role): void
    {
        Plan::factory()->count(3)->create();

        $this->actingAs($this->userForRole($role), 'sanctum')
            ->getJson('/api/plans')
            ->assertOk()
            ->assertJsonStructure(['data', 'meta']);
    }

    public static function authenticatedRoleProvider(): array
    {
        return [
            'admin' => ['admin'],
            'consultant' => ['consultant'],
            'client' => ['client'],
        ];
    }

    public function test_unauthenticated_cannot_list_plans(): void
    {
        $this->getJson('/api/plans')->assertUnauthorized();
    }

    public function test_plans_list_is_paginated(): void
    {
        Plan::factory()->count(15)->create();

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/plans?per_page=5')
            ->assertOk();

        $this->assertCount(5, $response->json('data'));
    }

    // ── Show ──────────────────────────────────────────────────

    #[DataProvider('authenticatedRoleProvider')]
    public function test_authenticated_users_can_view_plan(string $role): void
    {
        $plan = Plan::factory()->create();

        $this->actingAs($this->userForRole($role), 'sanctum')
            ->getJson("/api/plans/{$plan->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $plan->id);
    }

    public function test_show_returns_404_for_missing_plan(): void
    {
        $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/plans/99999')
            ->assertNotFound();
    }

    public function test_unauthenticated_cannot_view_plan(): void
    {
        $plan = Plan::factory()->create();
        $this->getJson("/api/plans/{$plan->id}")->assertUnauthorized();
    }

    // ── Store ─────────────────────────────────────────────────

    public function test_admin_can_create_plan(): void
    {
        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/plans', [
                'name' => 'Junior Dev Kickstart',
                'price' => 99.99,
            ])->assertCreated()
            ->assertJsonStructure(['message', 'plan']);
    }

    public function test_consultant_can_create_plan(): void
    {
        $this->actingAs($this->consultant, 'sanctum')
            ->postJson('/api/plans', ['name' => 'Consultant Plan'])
            ->assertCreated();
    }

    public function test_client_cannot_create_plan(): void
    {
        $this->actingAs($this->client, 'sanctum')
            ->postJson('/api/plans', ['name' => 'Hack Plan'])
            ->assertForbidden();
    }

    public function test_unauthenticated_cannot_create_plan(): void
    {
        $this->postJson('/api/plans', ['name' => 'Hack'])->assertUnauthorized();
    }

    public function test_plan_persisted_to_database(): void
    {
        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/plans', ['name' => 'DB Plan']);

        $this->assertDatabaseHas('plans', ['name' => 'DB Plan']);
    }

    // ── consultant_id spoofing (regression) ──────────────────

    public function test_consultant_cannot_create_plan_owned_by_another_consultant(): void
    {
        $otherConsultant = User::factory()->create();
        $otherConsultant->assignRole('consultant');

        $this->actingAs($this->consultant, 'sanctum')
            ->postJson('/api/plans', [
                'name' => 'Spoofed Plan',
                'consultant_id' => $otherConsultant->id,
            ])->assertCreated();

        $this->assertDatabaseHas('plans', [
            'name' => 'Spoofed Plan',
            'consultant_id' => $this->consultant->id,
        ]);
    }

    public function test_admin_can_assign_plan_to_a_consultant(): void
    {
        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/plans', [
                'name' => 'Assigned Plan',
                'consultant_id' => $this->consultant->id,
            ])->assertCreated();

        $this->assertDatabaseHas('plans', [
            'name' => 'Assigned Plan',
            'consultant_id' => $this->consultant->id,
        ]);
    }

    public function test_admin_cannot_assign_plan_to_a_client(): void
    {
        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/plans', [
                'name' => 'Bad Owner Plan',
                'consultant_id' => $this->client->id,
            ])->assertStatus(422)
            ->assertJsonValidationErrors('consultant_id');
    }

    #[DataProvider('invalidPlanStoreProvider')]
    public function test_plan_creation_fails_validation(array $overrides): void
    {
        $valid = ['name' => 'Valid Plan'];

        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/plans', array_merge($valid, $overrides))
            ->assertUnprocessable();
    }

    public static function invalidPlanStoreProvider(): array
    {
        return [
            'missing name' => [['name' => '']],
            'name too long' => [['name' => str_repeat('x', 256)]],
            'negative price' => [['price' => -1]],
            'non-numeric price' => [['price' => 'free']],
            'invalid client_ids' => [['client_ids' => [99999]]],
            'invalid path_ids' => [['path_ids' => [99999]]],
        ];
    }

    public function test_plan_creation_accepts_valid_price(): void
    {
        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/plans', ['name' => 'Priced Plan', 'price' => 149.99])
            ->assertCreated();
    }

    public function test_plan_creation_accepts_free_plan(): void
    {
        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/plans', ['name' => 'Free Plan', 'price' => 0])
            ->assertCreated();
    }

    public function test_plan_creation_accepts_paths(): void
    {
        $path = Path::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/plans', [
                'name' => 'Plan with Paths',
                'path_ids' => [$path->id],
            ])->assertCreated();
    }

    // ── Update ────────────────────────────────────────────────

    public function test_admin_can_update_plan(): void
    {
        $plan = Plan::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/plans/{$plan->id}", ['name' => 'Updated Plan'])
            ->assertOk()
            ->assertJsonPath('plan.name', 'Updated Plan');
    }

    public function test_consultant_can_update_plan(): void
    {
        $plan = Plan::factory()->create(['consultant_id' => $this->consultant->id]);

        $this->actingAs($this->consultant, 'sanctum')
            ->putJson("/api/plans/{$plan->id}", ['name' => 'Consultant Updated'])
            ->assertOk();
    }

    public function test_consultant_cannot_update_another_consultants_plan(): void
    {
        $otherConsultant = User::factory()->create();
        $otherConsultant->assignRole('consultant');
        $plan = Plan::factory()->create(['consultant_id' => $otherConsultant->id]);

        $this->actingAs($this->consultant, 'sanctum')
            ->putJson("/api/plans/{$plan->id}", ['name' => 'Hijacked'])
            ->assertForbidden();
    }

    public function test_admin_can_update_any_consultants_plan(): void
    {
        $plan = Plan::factory()->create(['consultant_id' => $this->consultant->id]);

        $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/plans/{$plan->id}", ['name' => 'Admin Updated'])
            ->assertOk();
    }

    public function test_client_cannot_update_plan(): void
    {
        $plan = Plan::factory()->create();

        $this->actingAs($this->client, 'sanctum')
            ->putJson("/api/plans/{$plan->id}", ['name' => 'Hack'])
            ->assertForbidden();
    }

    public function test_patch_update_works(): void
    {
        $plan = Plan::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->patchJson("/api/plans/{$plan->id}", ['name' => 'Patched'])
            ->assertOk();
    }

    #[DataProvider('invalidPlanUpdateProvider')]
    public function test_plan_update_fails_validation(array $payload): void
    {
        $plan = Plan::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/plans/{$plan->id}", $payload)
            ->assertUnprocessable();
    }

    public static function invalidPlanUpdateProvider(): array
    {
        return [
            'empty name' => [['name' => '']],
            'name too long' => [['name' => str_repeat('x', 256)]],
            'negative price' => [['name' => 'OK', 'price' => -1]],
            'non-numeric' => [['name' => 'OK', 'price' => 'abc']],
        ];
    }

    // ── Destroy ───────────────────────────────────────────────

    public function test_admin_can_delete_plan(): void
    {
        $plan = Plan::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/plans/{$plan->id}")
            ->assertOk()
            ->assertJsonPath('message', 'Plan deleted successfully');
    }

    public function test_consultant_can_delete_plan(): void
    {
        $plan = Plan::factory()->create(['consultant_id' => $this->consultant->id]);

        $this->actingAs($this->consultant, 'sanctum')
            ->deleteJson("/api/plans/{$plan->id}")
            ->assertOk();
    }

    public function test_consultant_cannot_delete_another_consultants_plan(): void
    {
        $otherConsultant = User::factory()->create();
        $otherConsultant->assignRole('consultant');
        $plan = Plan::factory()->create(['consultant_id' => $otherConsultant->id]);

        $this->actingAs($this->consultant, 'sanctum')
            ->deleteJson("/api/plans/{$plan->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('plans', ['id' => $plan->id]);
    }

    public function test_client_cannot_delete_plan(): void
    {
        $plan = Plan::factory()->create();

        $this->actingAs($this->client, 'sanctum')
            ->deleteJson("/api/plans/{$plan->id}")
            ->assertForbidden();
    }

    public function test_unauthenticated_cannot_delete_plan(): void
    {
        $plan = Plan::factory()->create();
        $this->deleteJson("/api/plans/{$plan->id}")->assertUnauthorized();
    }

    public function test_delete_soft_deletes_the_plan(): void
    {
        $plan = Plan::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/plans/{$plan->id}");

        $this->assertSoftDeleted('plans', ['id' => $plan->id]);
    }

    // ── Attach / Detach Client ────────────────────────────────

    public function test_admin_can_attach_client_to_plan(): void
    {
        $plan = Plan::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/plans/{$plan->id}/clients", [
                'client_id' => $this->client->id,
            ])->assertOk()
            ->assertJsonPath('message', 'Client attached successfully');
    }

    public function test_consultant_can_attach_client_to_plan(): void
    {
        $plan = Plan::factory()->create(['consultant_id' => $this->consultant->id]);

        $this->actingAs($this->consultant, 'sanctum')
            ->postJson("/api/plans/{$plan->id}/clients", [
                'client_id' => $this->client->id,
            ])->assertOk();
    }

    public function test_consultant_cannot_attach_client_to_another_consultants_plan(): void
    {
        $otherConsultant = User::factory()->create();
        $otherConsultant->assignRole('consultant');
        $plan = Plan::factory()->create(['consultant_id' => $otherConsultant->id]);

        $this->actingAs($this->consultant, 'sanctum')
            ->postJson("/api/plans/{$plan->id}/clients", [
                'client_id' => $this->client->id,
            ])->assertForbidden();
    }

    public function test_client_cannot_attach_to_plan(): void
    {
        $plan = Plan::factory()->create();
        $anotherClient = User::factory()->create();
        $anotherClient->assignRole('client');

        $this->actingAs($this->client, 'sanctum')
            ->postJson("/api/plans/{$plan->id}/clients", [
                'client_id' => $anotherClient->id,
            ])->assertForbidden();
    }

    public function test_attach_fails_with_invalid_client_id(): void
    {
        $plan = Plan::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/plans/{$plan->id}/clients", [
                'client_id' => 99999,
            ])->assertUnprocessable();
    }

    public function test_attach_requires_client_id(): void
    {
        $plan = Plan::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/plans/{$plan->id}/clients", [])
            ->assertUnprocessable();
    }

    public function test_admin_can_detach_client_from_plan(): void
    {
        $plan = Plan::factory()->create();
        $plan->clients()->attach($this->client->id);

        $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/plans/{$plan->id}/clients/{$this->client->id}")
            ->assertOk()
            ->assertJsonPath('message', 'Client detached successfully');
    }

    public function test_client_cannot_detach_from_plan(): void
    {
        $plan = Plan::factory()->create();
        $plan->clients()->attach($this->client->id);

        $this->actingAs($this->client, 'sanctum')
            ->deleteJson("/api/plans/{$plan->id}/clients/{$this->client->id}")
            ->assertForbidden();
    }

    // ── Helpers ───────────────────────────────────────────────

    private function userForRole(string $role): User
    {
        return match ($role) {
            'admin' => $this->admin,
            'consultant' => $this->consultant,
            'client' => $this->client,
        };
    }
}
