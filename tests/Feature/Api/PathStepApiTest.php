<?php

namespace Tests\Feature\Api;

use App\Models\Course;
use App\Models\Path;
use App\Models\PathStep;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Full coverage for path steps CRUD, reorder, and progress tracking.
 */
class PathStepApiTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $consultant;

    private User $client;

    private Path $path;

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

        $this->path = Path::factory()->create();
    }

    // ── Index ─────────────────────────────────────────────────

    #[DataProvider('allRolesProvider')]
    public function test_authenticated_user_can_list_steps(string $role): void
    {
        PathStep::factory()->count(3)->create(['path_id' => $this->path->id]);

        $this->actingAs($this->userForRole($role), 'sanctum')
            ->getJson("/api/paths/{$this->path->id}/steps")
            ->assertOk()
            ->assertJsonStructure(['data']);
    }

    public static function allRolesProvider(): array
    {
        return [
            'admin' => ['admin'],
            'consultant' => ['consultant'],
            'client' => ['client'],
        ];
    }

    public function test_unauthenticated_cannot_list_steps(): void
    {
        $this->getJson("/api/paths/{$this->path->id}/steps")->assertUnauthorized();
    }

    public function test_steps_are_returned_in_order(): void
    {
        PathStep::factory()->create(['path_id' => $this->path->id, 'order' => 2, 'title' => 'Second']);
        PathStep::factory()->create(['path_id' => $this->path->id, 'order' => 0, 'title' => 'First']);
        PathStep::factory()->create(['path_id' => $this->path->id, 'order' => 1, 'title' => 'Middle']);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/paths/{$this->path->id}/steps")
            ->assertOk();

        $titles = collect($response->json('data'))->pluck('title')->toArray();
        $this->assertSame(['First', 'Middle', 'Second'], $titles);
    }

    public function test_steps_include_user_status_field(): void
    {
        PathStep::factory()->create(['path_id' => $this->path->id]);

        $response = $this->actingAs($this->client, 'sanctum')
            ->getJson("/api/paths/{$this->path->id}/steps")
            ->assertOk();

        $this->assertArrayHasKey('user_status', $response->json('data.0'));
    }

    public function test_steps_default_user_status_is_not_started(): void
    {
        PathStep::factory()->create(['path_id' => $this->path->id]);

        $response = $this->actingAs($this->client, 'sanctum')
            ->getJson("/api/paths/{$this->path->id}/steps")
            ->assertOk();

        $this->assertSame('not_started', $response->json('data.0.user_status'));
    }

    // ── Store ─────────────────────────────────────────────────

    public function test_admin_can_create_step(): void
    {
        $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/paths/{$this->path->id}/steps", [
                'title' => 'Introduction to Laravel',
            ])->assertCreated()
            ->assertJsonStructure(['message', 'data']);
    }

    public function test_consultant_can_create_step(): void
    {
        $this->actingAs($this->consultant, 'sanctum')
            ->postJson("/api/paths/{$this->path->id}/steps", [
                'title' => 'Consultant Step',
            ])->assertCreated();
    }

    public function test_client_cannot_create_step(): void
    {
        $this->actingAs($this->client, 'sanctum')
            ->postJson("/api/paths/{$this->path->id}/steps", ['title' => 'Hack'])
            ->assertForbidden();
    }

    public function test_unauthenticated_cannot_create_step(): void
    {
        $this->postJson("/api/paths/{$this->path->id}/steps", ['title' => 'Hack'])
            ->assertUnauthorized();
    }

    public function test_step_creation_accepts_linked_course(): void
    {
        $course = Course::factory()->create();

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/paths/{$this->path->id}/steps", [
                'title' => 'Step with Course',
                'course_id' => $course->id,
            ])->assertCreated();

        $this->assertSame($course->id, $response->json('data.course.id'));
    }

    public function test_step_creation_accepts_resources(): void
    {
        $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/paths/{$this->path->id}/steps", [
                'title' => 'Resourced Step',
                'resources' => [
                    ['label' => 'Docs', 'url' => 'https://laravel.com/docs'],
                    ['label' => 'Video', 'url' => 'https://laracasts.com'],
                ],
            ])->assertCreated();
    }

    public function test_step_auto_assigns_order(): void
    {
        PathStep::factory()->create(['path_id' => $this->path->id, 'order' => 0]);
        PathStep::factory()->create(['path_id' => $this->path->id, 'order' => 1]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/paths/{$this->path->id}/steps", ['title' => 'Third'])
            ->assertCreated();

        $this->assertSame(2, $response->json('data.order'));
    }

    #[DataProvider('invalidStepStoreProvider')]
    public function test_step_creation_fails_validation(array $overrides): void
    {
        $valid = ['title' => 'Valid Step'];

        $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/paths/{$this->path->id}/steps", array_merge($valid, $overrides))
            ->assertUnprocessable();
    }

    public static function invalidStepStoreProvider(): array
    {
        return [
            'missing title' => [['title' => '']],
            'title too long' => [['title' => str_repeat('x', 256)]],
            'invalid course_id' => [['course_id' => 99999]],
            'negative order' => [['order' => -1]],
            'resource missing label' => [['resources' => [['url' => 'https://example.com']]]],
            'resource missing url' => [['resources' => [['label' => 'Docs']]]],
            'resource invalid url' => [['resources' => [['label' => 'X', 'url' => 'not-a-url']]]],
        ];
    }

    // ── Update ────────────────────────────────────────────────

    public function test_admin_can_update_step(): void
    {
        $step = PathStep::factory()->create(['path_id' => $this->path->id]);

        $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/paths/{$this->path->id}/steps/{$step->id}", [
                'title' => 'Updated Step',
            ])->assertOk()
            ->assertJsonPath('data.title', 'Updated Step');
    }

    public function test_consultant_can_update_step(): void
    {
        $step = PathStep::factory()->create(['path_id' => $this->path->id]);

        $this->actingAs($this->consultant, 'sanctum')
            ->putJson("/api/paths/{$this->path->id}/steps/{$step->id}", ['title' => 'Updated'])
            ->assertOk();
    }

    public function test_client_cannot_update_step(): void
    {
        $step = PathStep::factory()->create(['path_id' => $this->path->id]);

        $this->actingAs($this->client, 'sanctum')
            ->putJson("/api/paths/{$this->path->id}/steps/{$step->id}", ['title' => 'Hack'])
            ->assertForbidden();
    }

    public function test_update_fails_when_step_belongs_to_different_path(): void
    {
        $otherPath = Path::factory()->create();
        $step = PathStep::factory()->create(['path_id' => $otherPath->id]);

        $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/paths/{$this->path->id}/steps/{$step->id}", ['title' => 'Wrong'])
            ->assertNotFound();
    }

    #[DataProvider('invalidStepUpdateProvider')]
    public function test_step_update_fails_validation(array $payload): void
    {
        $step = PathStep::factory()->create(['path_id' => $this->path->id]);

        $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/paths/{$this->path->id}/steps/{$step->id}", $payload)
            ->assertUnprocessable();
    }

    public static function invalidStepUpdateProvider(): array
    {
        return [
            'empty title' => [['title' => '']],
            'title too long' => [['title' => str_repeat('x', 256)]],
            'invalid course_id' => [['course_id' => 99999]],
            'resource missing url' => [['resources' => [['label' => 'X']]]],
            'resource bad url format' => [['resources' => [['label' => 'X', 'url' => 'bad-url']]]],
        ];
    }

    // ── Destroy ───────────────────────────────────────────────

    public function test_admin_can_delete_step(): void
    {
        $step = PathStep::factory()->create(['path_id' => $this->path->id]);

        $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/paths/{$this->path->id}/steps/{$step->id}")
            ->assertOk()
            ->assertJsonPath('message', 'Step deleted');
    }

    public function test_consultant_can_delete_step(): void
    {
        $step = PathStep::factory()->create(['path_id' => $this->path->id]);

        $this->actingAs($this->consultant, 'sanctum')
            ->deleteJson("/api/paths/{$this->path->id}/steps/{$step->id}")
            ->assertOk();
    }

    public function test_client_cannot_delete_step(): void
    {
        $step = PathStep::factory()->create(['path_id' => $this->path->id]);

        $this->actingAs($this->client, 'sanctum')
            ->deleteJson("/api/paths/{$this->path->id}/steps/{$step->id}")
            ->assertForbidden();
    }

    public function test_unauthenticated_cannot_delete_step(): void
    {
        $step = PathStep::factory()->create(['path_id' => $this->path->id]);
        $this->deleteJson("/api/paths/{$this->path->id}/steps/{$step->id}")->assertUnauthorized();
    }

    public function test_delete_removes_step_from_database(): void
    {
        $step = PathStep::factory()->create(['path_id' => $this->path->id]);

        $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/paths/{$this->path->id}/steps/{$step->id}");

        $this->assertDatabaseMissing('path_steps', ['id' => $step->id]);
    }

    // ── Reorder ───────────────────────────────────────────────

    public function test_admin_can_reorder_steps(): void
    {
        $steps = PathStep::factory()->count(3)->sequence(
            ['path_id' => $this->path->id, 'order' => 0],
            ['path_id' => $this->path->id, 'order' => 1],
            ['path_id' => $this->path->id, 'order' => 2],
        )->create();

        $reversedIds = $steps->pluck('id')->reverse()->values()->toArray();

        $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/paths/{$this->path->id}/steps/reorder", [
                'ids' => $reversedIds,
            ])->assertOk()
            ->assertJsonPath('message', 'Steps reordered');
    }

    public function test_client_cannot_reorder_steps(): void
    {
        $steps = PathStep::factory()->count(2)->create(['path_id' => $this->path->id]);

        $this->actingAs($this->client, 'sanctum')
            ->postJson("/api/paths/{$this->path->id}/steps/reorder", [
                'ids' => $steps->pluck('id')->toArray(),
            ])->assertForbidden();
    }

    public function test_reorder_updates_order_column(): void
    {
        $first = PathStep::factory()->create(['path_id' => $this->path->id, 'order' => 0]);
        $second = PathStep::factory()->create(['path_id' => $this->path->id, 'order' => 1]);

        $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/paths/{$this->path->id}/steps/reorder", [
                'ids' => [$second->id, $first->id],
            ])->assertOk();

        $this->assertDatabaseHas('path_steps', ['id' => $second->id, 'order' => 0]);
        $this->assertDatabaseHas('path_steps', ['id' => $first->id, 'order' => 1]);
    }

    public function test_reorder_requires_ids(): void
    {
        $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/paths/{$this->path->id}/steps/reorder", [])
            ->assertUnprocessable();
    }

    public function test_reorder_rejects_invalid_step_ids(): void
    {
        $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/paths/{$this->path->id}/steps/reorder", [
                'ids' => [99999],
            ])->assertUnprocessable();
    }

    // ── Progress ──────────────────────────────────────────────

    #[DataProvider('validProgressStatusProvider')]
    public function test_any_authenticated_user_can_update_step_progress(string $status): void
    {
        $step = PathStep::factory()->create(['path_id' => $this->path->id]);

        $this->actingAs($this->client, 'sanctum')
            ->putJson("/api/path-steps/{$step->id}/progress", ['status' => $status])
            ->assertOk()
            ->assertJsonPath('status', $status);
    }

    public static function validProgressStatusProvider(): array
    {
        return [
            'not started' => ['not_started'],
            'in progress' => ['in_progress'],
            'done' => ['done'],
        ];
    }

    public function test_progress_persisted_to_database(): void
    {
        $step = PathStep::factory()->create(['path_id' => $this->path->id]);

        $this->actingAs($this->client, 'sanctum')
            ->putJson("/api/path-steps/{$step->id}/progress", ['status' => 'done']);

        $this->assertDatabaseHas('user_step_progress', [
            'user_id' => $this->client->id,
            'path_step_id' => $step->id,
            'status' => 'done',
        ]);
    }

    public function test_progress_update_is_idempotent(): void
    {
        $step = PathStep::factory()->create(['path_id' => $this->path->id]);

        $this->actingAs($this->client, 'sanctum')
            ->putJson("/api/path-steps/{$step->id}/progress", ['status' => 'in_progress']);

        $this->actingAs($this->client, 'sanctum')
            ->putJson("/api/path-steps/{$step->id}/progress", ['status' => 'done'])
            ->assertOk();

        $this->assertDatabaseCount('user_step_progress', 1);
    }

    public function test_progress_is_per_user(): void
    {
        $step = PathStep::factory()->create(['path_id' => $this->path->id]);

        $this->actingAs($this->client, 'sanctum')
            ->putJson("/api/path-steps/{$step->id}/progress", ['status' => 'done']);

        $otherClient = User::factory()->create();
        $otherClient->assignRole('client');

        $this->actingAs($otherClient, 'sanctum')
            ->putJson("/api/path-steps/{$step->id}/progress", ['status' => 'in_progress']);

        $this->assertDatabaseHas('user_step_progress', [
            'user_id' => $this->client->id, 'status' => 'done',
        ]);
        $this->assertDatabaseHas('user_step_progress', [
            'user_id' => $otherClient->id, 'status' => 'in_progress',
        ]);
    }

    #[DataProvider('invalidProgressStatusProvider')]
    public function test_progress_rejects_invalid_status(string $status): void
    {
        $step = PathStep::factory()->create(['path_id' => $this->path->id]);

        $this->actingAs($this->client, 'sanctum')
            ->putJson("/api/path-steps/{$step->id}/progress", ['status' => $status])
            ->assertUnprocessable();
    }

    public static function invalidProgressStatusProvider(): array
    {
        return [
            'empty' => [''],
            'invalid' => ['completed'],
            'random' => ['whatever'],
            'uppercase' => ['DONE'],
        ];
    }

    public function test_unauthenticated_cannot_update_progress(): void
    {
        $step = PathStep::factory()->create(['path_id' => $this->path->id]);
        $this->putJson("/api/path-steps/{$step->id}/progress", ['status' => 'done'])
            ->assertUnauthorized();
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
