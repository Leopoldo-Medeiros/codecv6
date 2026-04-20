<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Ensures role-based access control works correctly.
 * A client must never be able to perform admin-only actions.
 */
class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $client;

    private User $otherClient;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);

        $this->admin = User::factory()->create(['password' => Hash::make('Password1!')]);
        $this->admin->assignRole('admin');

        $this->client = User::factory()->create(['password' => Hash::make('Password1!')]);
        $this->client->assignRole('client');

        $this->otherClient = User::factory()->create(['password' => Hash::make('Password1!')]);
        $this->otherClient->assignRole('client');
    }

    // ── Courses ──────────────────────────────────────────────

    public function test_client_cannot_create_course(): void
    {
        $this->actingAs($this->client, 'sanctum')
            ->postJson('/api/courses', ['name' => 'Hack Course', 'slug' => 'hack'])
            ->assertForbidden();
    }

    public function test_client_cannot_delete_course(): void
    {
        $course = Course::factory()->create(['user_id' => $this->admin->id]);

        $this->actingAs($this->client, 'sanctum')
            ->deleteJson("/api/courses/{$course->id}")
            ->assertForbidden();
    }

    public function test_admin_can_create_course(): void
    {
        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/courses', [
                'name' => 'Laravel Fundamentals',
                'slug' => 'laravel-fundamentals',
                'description' => 'A course about Laravel.',
            ])
            ->assertCreated();
    }

    public function test_client_can_read_courses(): void
    {
        $this->actingAs($this->client, 'sanctum')
            ->getJson('/api/courses')
            ->assertOk();
    }

    // ── Paths ─────────────────────────────────────────────────

    public function test_client_cannot_create_path(): void
    {
        $this->actingAs($this->client, 'sanctum')
            ->postJson('/api/paths', ['name' => 'Hack Path'])
            ->assertForbidden();
    }

    public function test_admin_can_create_path(): void
    {
        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/paths', ['name' => 'Laravel Developer Roadmap'])
            ->assertCreated();
    }

    // ── Users ─────────────────────────────────────────────────

    public function test_client_cannot_list_all_users(): void
    {
        $this->actingAs($this->client, 'sanctum')
            ->getJson('/api/users')
            ->assertForbidden();
    }

    public function test_client_cannot_view_another_clients_profile(): void
    {
        $this->actingAs($this->client, 'sanctum')
            ->getJson("/api/users/{$this->otherClient->id}")
            ->assertForbidden();
    }

    public function test_client_can_view_own_profile(): void
    {
        $this->actingAs($this->client, 'sanctum')
            ->getJson("/api/users/{$this->client->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $this->client->id);
    }

    public function test_client_cannot_delete_user(): void
    {
        $this->actingAs($this->client, 'sanctum')
            ->deleteJson("/api/users/{$this->otherClient->id}")
            ->assertForbidden();
    }

    public function test_admin_can_list_all_users(): void
    {
        $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/users')
            ->assertOk()
            ->assertJsonStructure(['data', 'meta']);
    }

    // ── GDPR ──────────────────────────────────────────────────

    public function test_client_can_export_own_data(): void
    {
        $this->actingAs($this->client, 'sanctum')
            ->getJson("/api/users/{$this->client->id}/export")
            ->assertOk()
            ->assertJsonStructure(['exported_at', 'account', 'profile']);
    }

    public function test_client_cannot_export_another_users_data(): void
    {
        $this->actingAs($this->client, 'sanctum')
            ->getJson("/api/users/{$this->otherClient->id}/export")
            ->assertForbidden();
    }

    public function test_only_admin_can_erase_user_data(): void
    {
        $this->actingAs($this->client, 'sanctum')
            ->deleteJson("/api/users/{$this->otherClient->id}/erase")
            ->assertForbidden();
    }

    public function test_admin_can_erase_client_data(): void
    {
        $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/users/{$this->otherClient->id}/erase")
            ->assertOk()
            ->assertJsonPath('message', 'User data permanently erased.');
    }

    // ── Unauthenticated ───────────────────────────────────────

    public function test_unauthenticated_user_cannot_access_api(): void
    {
        $this->getJson('/api/users')->assertUnauthorized();
        $this->getJson('/api/courses')->assertUnauthorized();
        $this->getJson('/api/paths')->assertUnauthorized();
    }
}
