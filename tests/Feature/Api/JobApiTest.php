<?php

namespace Tests\Feature\Api;

use App\Models\Job;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Full CRUD coverage for /api/jobs.
 */
class JobApiTest extends TestCase
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
    public function test_authenticated_users_can_list_jobs(string $role): void
    {
        Job::factory()->count(3)->create();
        $user = $this->userForRole($role);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/jobs')
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

    public function test_unauthenticated_cannot_list_jobs(): void
    {
        $this->getJson('/api/jobs')->assertUnauthorized();
    }

    public function test_jobs_list_is_paginated(): void
    {
        Job::factory()->count(15)->create();

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/jobs?per_page=5')
            ->assertOk();

        $this->assertCount(5, $response->json('data'));
    }

    // ── Show ──────────────────────────────────────────────────

    #[DataProvider('authenticatedRoleProvider')]
    public function test_authenticated_users_can_view_job(string $role): void
    {
        $job = Job::factory()->create();
        $user = $this->userForRole($role);

        $this->actingAs($user, 'sanctum')
            ->getJson("/api/jobs/{$job->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $job->id);
    }

    public function test_show_returns_404_for_missing_job(): void
    {
        $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/jobs/99999')
            ->assertNotFound();
    }

    public function test_unauthenticated_cannot_view_job(): void
    {
        $job = Job::factory()->create();
        $this->getJson("/api/jobs/{$job->id}")->assertUnauthorized();
    }

    // ── Store ─────────────────────────────────────────────────

    public function test_admin_can_create_job(): void
    {
        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/jobs', [
                'title' => 'Senior PHP Developer',
                'company' => 'Acme Corp',
            ])->assertCreated()
            ->assertJsonStructure(['message', 'job']);
    }

    public function test_consultant_can_create_job(): void
    {
        $this->actingAs($this->consultant, 'sanctum')
            ->postJson('/api/jobs', [
                'title' => 'Junior Dev',
                'company' => 'StartupXYZ',
            ])->assertCreated();
    }

    public function test_client_cannot_create_job(): void
    {
        $this->actingAs($this->client, 'sanctum')
            ->postJson('/api/jobs', ['title' => 'Hack', 'company' => 'Evil Corp'])
            ->assertForbidden();
    }

    public function test_unauthenticated_cannot_create_job(): void
    {
        $this->postJson('/api/jobs', ['title' => 'Hack', 'company' => 'Evil'])
            ->assertUnauthorized();
    }

    public function test_job_persisted_to_database(): void
    {
        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/jobs', [
                'title' => 'DB Test Job',
                'company' => 'Test Corp',
            ]);

        $this->assertDatabaseHas('job_listings', ['title' => 'DB Test Job']);
    }

    #[DataProvider('invalidJobStoreProvider')]
    public function test_job_creation_fails_validation(array $overrides): void
    {
        $valid = ['title' => 'Valid Title', 'company' => 'Valid Co'];

        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/jobs', array_merge($valid, $overrides))
            ->assertUnprocessable();
    }

    public static function invalidJobStoreProvider(): array
    {
        return [
            'missing title' => [['title' => '']],
            'title too long' => [['title' => str_repeat('x', 256)]],
            'missing company' => [['company' => '']],
            'company too long' => [['company' => str_repeat('x', 256)]],
            'location too long' => [['location' => str_repeat('x', 256)]],
            'salary too long' => [['salary' => str_repeat('x', 256)]],
        ];
    }

    public function test_job_creation_accepts_all_optional_fields(): void
    {
        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/jobs', [
                'title' => 'Full Stack Dev',
                'company' => 'Tech Corp',
                'description' => 'A great role.',
                'location' => 'Dublin, Ireland',
                'salary' => '€60k–€80k',
            ])->assertCreated();
    }

    // ── Update ────────────────────────────────────────────────

    public function test_admin_can_update_any_job(): void
    {
        $job = Job::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/jobs/{$job->id}", [
                'title' => 'Updated Title',
                'company' => 'Updated Corp',
            ])->assertOk();
    }

    public function test_consultant_can_update_own_job(): void
    {
        $job = Job::factory()->create(['consultant_id' => $this->consultant->id]);

        $this->actingAs($this->consultant, 'sanctum')
            ->putJson("/api/jobs/{$job->id}", [
                'title' => 'Updated',
                'company' => 'Corp',
            ])->assertOk();
    }

    public function test_client_cannot_update_job(): void
    {
        $job = Job::factory()->create();

        $this->actingAs($this->client, 'sanctum')
            ->putJson("/api/jobs/{$job->id}", ['title' => 'Hack', 'company' => 'Bad'])
            ->assertForbidden();
    }

    public function test_unauthenticated_cannot_update_job(): void
    {
        $job = Job::factory()->create();
        $this->putJson("/api/jobs/{$job->id}", ['title' => 'Hack', 'company' => 'Bad'])
            ->assertUnauthorized();
    }

    public function test_patch_update_works(): void
    {
        $job = Job::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->patchJson("/api/jobs/{$job->id}", ['title' => 'Patched'])
            ->assertOk();
    }

    #[DataProvider('invalidJobUpdateProvider')]
    public function test_job_update_fails_validation(array $payload): void
    {
        $job = Job::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/jobs/{$job->id}", $payload)
            ->assertUnprocessable();
    }

    public static function invalidJobUpdateProvider(): array
    {
        return [
            'empty title' => [['title' => '', 'company' => 'Corp']],
            'title too long' => [['title' => str_repeat('x', 256), 'company' => 'Corp']],
            'empty company' => [['title' => 'Title', 'company' => '']],
            'company too long' => [['title' => 'Title', 'company' => str_repeat('x', 256)]],
        ];
    }

    // ── Destroy ───────────────────────────────────────────────

    public function test_admin_can_delete_job(): void
    {
        $job = Job::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/jobs/{$job->id}")
            ->assertOk();
    }

    public function test_consultant_can_delete_own_job(): void
    {
        $job = Job::factory()->create(['consultant_id' => $this->consultant->id]);

        $this->actingAs($this->consultant, 'sanctum')
            ->deleteJson("/api/jobs/{$job->id}")
            ->assertOk();
    }

    public function test_client_cannot_delete_job(): void
    {
        $job = Job::factory()->create();

        $this->actingAs($this->client, 'sanctum')
            ->deleteJson("/api/jobs/{$job->id}")
            ->assertForbidden();
    }

    public function test_unauthenticated_cannot_delete_job(): void
    {
        $job = Job::factory()->create();
        $this->deleteJson("/api/jobs/{$job->id}")->assertUnauthorized();
    }

    public function test_delete_soft_deletes_the_job(): void
    {
        $job = Job::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/jobs/{$job->id}")
            ->assertOk();

        $this->assertSoftDeleted('job_listings', ['id' => $job->id]);
    }

    public function test_delete_returns_404_for_missing_job(): void
    {
        $this->actingAs($this->admin, 'sanctum')
            ->deleteJson('/api/jobs/99999')
            ->assertNotFound();
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
