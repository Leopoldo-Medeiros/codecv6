<?php

namespace Tests\Feature\Api;

use App\Models\Path;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Full CRUD coverage for /api/paths.
 */
class PathApiTest extends TestCase
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
    public function test_authenticated_users_can_list_paths(string $role): void
    {
        Path::factory()->count(3)->create();

        $this->actingAs($this->userForRole($role), 'sanctum')
            ->getJson('/api/paths')
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

    public function test_unauthenticated_cannot_list_paths(): void
    {
        $this->getJson('/api/paths')->assertUnauthorized();
    }

    public function test_paths_list_is_paginated(): void
    {
        Path::factory()->count(15)->create();

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/paths?per_page=5')
            ->assertOk();

        $this->assertCount(5, $response->json('data'));
    }

    // ── Show ──────────────────────────────────────────────────

    #[DataProvider('authenticatedRoleProvider')]
    public function test_authenticated_users_can_view_path(string $role): void
    {
        $path = Path::factory()->create();

        $this->actingAs($this->userForRole($role), 'sanctum')
            ->getJson("/api/paths/{$path->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $path->id);
    }

    public function test_show_returns_404_for_missing_path(): void
    {
        $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/paths/99999')
            ->assertNotFound();
    }

    public function test_unauthenticated_cannot_view_path(): void
    {
        $path = Path::factory()->create();
        $this->getJson("/api/paths/{$path->id}")->assertUnauthorized();
    }

    // ── Store ─────────────────────────────────────────────────

    public function test_admin_can_create_path(): void
    {
        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/paths', [
                'name' => 'Laravel Developer Roadmap',
                'description' => 'A complete path.',
            ])->assertCreated()
            ->assertJsonStructure(['message', 'path']);
    }

    public function test_consultant_can_create_path(): void
    {
        $this->actingAs($this->consultant, 'sanctum')
            ->postJson('/api/paths', ['name' => 'Vue.js Path'])
            ->assertCreated();
    }

    public function test_client_cannot_create_path(): void
    {
        $this->actingAs($this->client, 'sanctum')
            ->postJson('/api/paths', ['name' => 'Hack Path'])
            ->assertForbidden();
    }

    public function test_unauthenticated_cannot_create_path(): void
    {
        $this->postJson('/api/paths', ['name' => 'Hack'])->assertUnauthorized();
    }

    public function test_path_persisted_to_database(): void
    {
        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/paths', ['name' => 'DB Path']);

        $this->assertDatabaseHas('paths', ['name' => 'DB Path']);
    }

    #[DataProvider('invalidPathStoreProvider')]
    public function test_path_creation_fails_validation(array $overrides): void
    {
        $valid = ['name' => 'Valid Path'];

        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/paths', array_merge($valid, $overrides))
            ->assertUnprocessable();
    }

    public static function invalidPathStoreProvider(): array
    {
        return [
            'missing name' => [['name' => '']],
            'name too long' => [['name' => str_repeat('x', 256)]],
            'invalid plan_ids' => [['plan_ids' => [99999]]],
        ];
    }

    // ── Update ────────────────────────────────────────────────

    public function test_admin_can_update_any_path(): void
    {
        $path = Path::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/paths/{$path->id}", ['name' => 'Updated Path'])
            ->assertOk();
    }

    public function test_consultant_can_update_path(): void
    {
        $path = Path::factory()->create();

        $this->actingAs($this->consultant, 'sanctum')
            ->putJson("/api/paths/{$path->id}", ['name' => 'Consultant Updated'])
            ->assertOk();
    }

    public function test_client_cannot_update_path(): void
    {
        $path = Path::factory()->create();

        $this->actingAs($this->client, 'sanctum')
            ->putJson("/api/paths/{$path->id}", ['name' => 'Hack'])
            ->assertForbidden();
    }

    public function test_unauthenticated_cannot_update_path(): void
    {
        $path = Path::factory()->create();
        $this->putJson("/api/paths/{$path->id}", ['name' => 'Hack'])->assertUnauthorized();
    }

    public function test_patch_update_works(): void
    {
        $path = Path::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->patchJson("/api/paths/{$path->id}", ['name' => 'Patched'])
            ->assertOk();
    }

    #[DataProvider('invalidPathUpdateProvider')]
    public function test_path_update_fails_validation(array $payload): void
    {
        $path = Path::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/paths/{$path->id}", $payload)
            ->assertUnprocessable();
    }

    public static function invalidPathUpdateProvider(): array
    {
        return [
            'empty name' => [['name' => '']],
            'name too long' => [['name' => str_repeat('x', 256)]],
            'invalid plan_id' => [['name' => 'OK', 'plan_ids' => [99999]]],
        ];
    }

    // ── Destroy ───────────────────────────────────────────────

    public function test_admin_can_delete_path(): void
    {
        $path = Path::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/paths/{$path->id}")
            ->assertOk();
    }

    public function test_consultant_can_delete_path(): void
    {
        $path = Path::factory()->create();

        $this->actingAs($this->consultant, 'sanctum')
            ->deleteJson("/api/paths/{$path->id}")
            ->assertOk();
    }

    public function test_client_cannot_delete_path(): void
    {
        $path = Path::factory()->create();

        $this->actingAs($this->client, 'sanctum')
            ->deleteJson("/api/paths/{$path->id}")
            ->assertForbidden();
    }

    public function test_unauthenticated_cannot_delete_path(): void
    {
        $path = Path::factory()->create();
        $this->deleteJson("/api/paths/{$path->id}")->assertUnauthorized();
    }

    public function test_delete_soft_deletes_the_path(): void
    {
        $path = Path::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/paths/{$path->id}")
            ->assertOk();

        $this->assertSoftDeleted('paths', ['id' => $path->id]);
    }

    public function test_soft_deleted_path_not_in_index(): void
    {
        $path = Path::factory()->create();
        $path->delete();

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/paths')
            ->assertOk();

        $ids = collect($response->json('data'))->pluck('id')->toArray();
        $this->assertNotContains($path->id, $ids);
    }

    public function test_delete_returns_404_for_missing_path(): void
    {
        $this->actingAs($this->admin, 'sanctum')
            ->deleteJson('/api/paths/99999')
            ->assertNotFound();
    }

    // ── Steps list via path ───────────────────────────────────

    public function test_authenticated_user_can_list_path_steps(): void
    {
        $path = Path::factory()->create();

        $this->actingAs($this->client, 'sanctum')
            ->getJson("/api/paths/{$path->id}/steps")
            ->assertOk();
    }

    public function test_unauthenticated_cannot_list_path_steps(): void
    {
        $path = Path::factory()->create();
        $this->getJson("/api/paths/{$path->id}/steps")->assertUnauthorized();
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
