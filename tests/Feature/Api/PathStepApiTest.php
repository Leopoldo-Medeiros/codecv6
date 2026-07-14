<?php

namespace Tests\Feature\Api;

use App\Enums\PaymentStatus;
use App\Enums\PaymentTier;
use App\Models\Course;
use App\Models\Path;
use App\Models\PathStep;
use App\Models\Payment;
use App\Models\User;
use Database\Seeders\BadgesSeeder;
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
        $this->seed(BadgesSeeder::class);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->consultant = User::factory()->create();
        $this->consultant->assignRole('consultant');

        $this->client = User::factory()->create();
        $this->client->assignRole('client');

        $this->path = Path::factory()->create(['consultant_id' => $this->consultant->id]);
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

    // ── Quiz (F5) ─────────────────────────────────────────────

    private function quizStep(): PathStep
    {
        return PathStep::factory()->create([
            'path_id' => $this->path->id,
            'order' => 0,
            'type' => 'quiz',
            'quiz' => [
                ['id' => 1, 'question' => '2 + 2?', 'options' => ['3', '4', '5'], 'correct_index' => 1, 'explanation' => 'Basic arithmetic.'],
                ['id' => 2, 'question' => 'PHP array function?', 'options' => ['map()', 'array_map()'], 'correct_index' => 1, 'explanation' => null],
            ],
        ]);
    }

    public function test_quiz_step_exposes_questions_without_the_answer_key(): void
    {
        $step = $this->quizStep();

        $data = $this->actingAs($this->client, 'sanctum')
            ->getJson("/api/path-steps/{$step->id}")->json('data');

        $this->assertCount(2, $data['quiz']);
        $this->assertArrayHasKey('question', $data['quiz'][0]);
        $this->assertArrayHasKey('options', $data['quiz'][0]);
        $this->assertArrayNotHasKey('correct_index', $data['quiz'][0]);
        $this->assertArrayNotHasKey('explanation', $data['quiz'][0]);
        // Whole-payload scan: the answer key must not leak anywhere.
        $this->assertStringNotContainsString('correct_index', json_encode($data));
    }

    public function test_quiz_submission_grades_all_correct(): void
    {
        $step = $this->quizStep();

        $response = $this->actingAs($this->client, 'sanctum')
            ->postJson("/api/path-steps/{$step->id}/quiz", ['answers' => [1 => 1, 2 => 1]])
            ->assertOk();

        $response->assertJson(['score' => 2, 'total' => 2, 'passed' => true]);
        $this->assertTrue($response->json('results.0.correct'));
        $this->assertSame('Basic arithmetic.', $response->json('results.0.explanation'));
    }

    public function test_quiz_submission_grades_partial(): void
    {
        $step = $this->quizStep();

        $response = $this->actingAs($this->client, 'sanctum')
            ->postJson("/api/path-steps/{$step->id}/quiz", ['answers' => [1 => 0, 2 => 1]])
            ->assertOk();

        $response->assertJson(['score' => 1, 'total' => 2, 'passed' => false]);
        $this->assertFalse($response->json('results.0.correct'));
        $this->assertTrue($response->json('results.1.correct'));
    }

    public function test_quiz_submission_returns_the_correct_index_for_review(): void
    {
        $step = $this->quizStep();

        $response = $this->actingAs($this->client, 'sanctum')
            ->postJson("/api/path-steps/{$step->id}/quiz", ['answers' => []])
            ->assertOk();

        $this->assertSame(1, $response->json('results.0.correct_index'));
    }

    public function test_quiz_submission_on_a_non_quiz_step_returns_404(): void
    {
        $step = PathStep::factory()->create(['path_id' => $this->path->id, 'order' => 0, 'type' => 'reading']);

        $this->actingAs($this->client, 'sanctum')
            ->postJson("/api/path-steps/{$step->id}/quiz", ['answers' => []])
            ->assertNotFound();
    }

    public function test_free_user_cannot_submit_a_locked_quiz(): void
    {
        $step = PathStep::factory()->create([
            'path_id' => $this->path->id,
            'order' => 5,
            'type' => 'quiz',
            'quiz' => [['id' => 1, 'question' => 'q', 'options' => ['a', 'b'], 'correct_index' => 0]],
        ]);

        $this->actingAs($this->client, 'sanctum')
            ->postJson("/api/path-steps/{$step->id}/quiz", ['answers' => [1 => 0]])
            ->assertForbidden();
    }

    // ── F4 content gate ───────────────────────────────────────

    public function test_step_list_marks_later_steps_locked_for_free_users(): void
    {
        PathStep::factory()->create(['path_id' => $this->path->id, 'order' => 0, 'title' => 'Free 1']);
        PathStep::factory()->create(['path_id' => $this->path->id, 'order' => 1, 'title' => 'Free 2']);
        PathStep::factory()->create(['path_id' => $this->path->id, 'order' => 2, 'title' => 'Locked']);

        $data = collect($this->actingAs($this->client, 'sanctum')
            ->getJson("/api/paths/{$this->path->id}/steps")->json('data'));

        $this->assertFalse($data->firstWhere('title', 'Free 1')['locked']);
        $this->assertFalse($data->firstWhere('title', 'Free 2')['locked']);
        $this->assertTrue($data->firstWhere('title', 'Locked')['locked']);
    }

    public function test_free_user_cannot_open_a_locked_step(): void
    {
        $step = PathStep::factory()->create(['path_id' => $this->path->id, 'order' => 5]);

        $this->actingAs($this->client, 'sanctum')
            ->getJson("/api/path-steps/{$step->id}")
            ->assertForbidden();
    }

    public function test_free_user_can_open_an_early_step(): void
    {
        $step = PathStep::factory()->create(['path_id' => $this->path->id, 'order' => 0]);

        $this->actingAs($this->client, 'sanctum')
            ->getJson("/api/path-steps/{$step->id}")
            ->assertOk();
    }

    public function test_practice_pro_user_can_open_a_locked_step(): void
    {
        $step = PathStep::factory()->create(['path_id' => $this->path->id, 'order' => 5]);
        Payment::create([
            'user_id' => $this->client->id,
            'stripe_session_id' => 'cs_test_gate',
            'tier' => PaymentTier::PRACTICE,
            'amount' => 1200, 'currency' => 'eur',
            'status' => PaymentStatus::PAID, 'paid_at' => now(),
        ]);

        $this->actingAs($this->client, 'sanctum')
            ->getJson("/api/path-steps/{$step->id}")
            ->assertOk();
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

    public function test_other_consultant_cannot_create_step_on_path_they_dont_own(): void
    {
        $otherConsultant = User::factory()->create();
        $otherConsultant->assignRole('consultant');

        $this->actingAs($otherConsultant, 'sanctum')
            ->postJson("/api/paths/{$this->path->id}/steps", ['title' => 'Hijack'])
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

    public function test_other_consultant_cannot_update_step_on_path_they_dont_own(): void
    {
        $step = PathStep::factory()->create(['path_id' => $this->path->id]);
        $otherConsultant = User::factory()->create();
        $otherConsultant->assignRole('consultant');

        $this->actingAs($otherConsultant, 'sanctum')
            ->putJson("/api/paths/{$this->path->id}/steps/{$step->id}", ['title' => 'Hijack'])
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

    public function test_other_consultant_cannot_delete_step_on_path_they_dont_own(): void
    {
        $step = PathStep::factory()->create(['path_id' => $this->path->id]);
        $otherConsultant = User::factory()->create();
        $otherConsultant->assignRole('consultant');

        $this->actingAs($otherConsultant, 'sanctum')
            ->deleteJson("/api/paths/{$this->path->id}/steps/{$step->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('path_steps', ['id' => $step->id]);
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

    public function test_owning_consultant_can_reorder_steps(): void
    {
        $steps = PathStep::factory()->count(2)->create(['path_id' => $this->path->id]);

        $this->actingAs($this->consultant, 'sanctum')
            ->postJson("/api/paths/{$this->path->id}/steps/reorder", [
                'ids' => $steps->pluck('id')->reverse()->values()->toArray(),
            ])->assertOk();
    }

    public function test_other_consultant_cannot_reorder_steps_on_path_they_dont_own(): void
    {
        $steps = PathStep::factory()->count(2)->create(['path_id' => $this->path->id]);
        $otherConsultant = User::factory()->create();
        $otherConsultant->assignRole('consultant');

        $this->actingAs($otherConsultant, 'sanctum')
            ->postJson("/api/paths/{$this->path->id}/steps/reorder", [
                'ids' => $steps->pluck('id')->reverse()->values()->toArray(),
            ])->assertForbidden();
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

    // ── Gamification (F1) ─────────────────────────────────────

    public function test_marking_a_step_done_awards_xp(): void
    {
        $step = PathStep::factory()->create(['path_id' => $this->path->id, 'difficulty' => 'advanced']);

        $response = $this->actingAs($this->client, 'sanctum')
            ->putJson("/api/path-steps/{$step->id}/progress", ['status' => 'done'])
            ->assertOk();

        $response->assertJsonPath('progress.xp_awarded', 25);
        $response->assertJsonPath('progress.xp_points', 25);
    }

    public function test_marking_an_already_done_step_done_again_awards_no_xp(): void
    {
        $step = PathStep::factory()->create(['path_id' => $this->path->id]);

        $this->actingAs($this->client, 'sanctum')
            ->putJson("/api/path-steps/{$step->id}/progress", ['status' => 'done'])
            ->assertOk();

        $second = $this->actingAs($this->client, 'sanctum')
            ->putJson("/api/path-steps/{$step->id}/progress", ['status' => 'done'])
            ->assertOk();

        $this->assertNull($second->json('progress'));
    }

    public function test_marking_a_step_in_progress_awards_no_xp(): void
    {
        $step = PathStep::factory()->create(['path_id' => $this->path->id]);

        $response = $this->actingAs($this->client, 'sanctum')
            ->putJson("/api/path-steps/{$step->id}/progress", ['status' => 'in_progress'])
            ->assertOk();

        $this->assertNull($response->json('progress'));
    }

    public function test_completing_the_final_step_flags_path_completed(): void
    {
        // Two-step path; finish step 1, then step 2 completes the whole path.
        $first = PathStep::factory()->create(['path_id' => $this->path->id, 'order' => 0]);
        $last = PathStep::factory()->create(['path_id' => $this->path->id, 'order' => 1]);

        $this->actingAs($this->client, 'sanctum')
            ->putJson("/api/path-steps/{$first->id}/progress", ['status' => 'done'])
            ->assertOk()
            ->assertJsonPath('path_completed', false);

        $this->actingAs($this->client, 'sanctum')
            ->putJson("/api/path-steps/{$last->id}/progress", ['status' => 'done'])
            ->assertOk()
            ->assertJsonPath('path_completed', true);
    }

    public function test_path_completed_flag_fires_on_every_path_not_just_the_first(): void
    {
        // The one-time path_completed badge only lands once; the response flag
        // must still be true when a SECOND path is finished (F6 celebration).
        $pathA = Path::factory()->create(['consultant_id' => $this->consultant->id]);
        $stepA = PathStep::factory()->create(['path_id' => $pathA->id, 'order' => 0]);
        $pathB = Path::factory()->create(['consultant_id' => $this->consultant->id]);
        $stepB = PathStep::factory()->create(['path_id' => $pathB->id, 'order' => 0]);

        $this->actingAs($this->client, 'sanctum')
            ->putJson("/api/path-steps/{$stepA->id}/progress", ['status' => 'done'])
            ->assertJsonPath('path_completed', true);

        $this->actingAs($this->client, 'sanctum')
            ->putJson("/api/path-steps/{$stepB->id}/progress", ['status' => 'done'])
            ->assertJsonPath('path_completed', true);
    }

    public function test_re_marking_the_final_step_done_does_not_reflag_path_completed(): void
    {
        $step = PathStep::factory()->create(['path_id' => $this->path->id, 'order' => 0]);

        $this->actingAs($this->client, 'sanctum')
            ->putJson("/api/path-steps/{$step->id}/progress", ['status' => 'done'])
            ->assertJsonPath('path_completed', true);

        // No fresh transition → no repeat celebration.
        $this->actingAs($this->client, 'sanctum')
            ->putJson("/api/path-steps/{$step->id}/progress", ['status' => 'done'])
            ->assertJsonPath('path_completed', false);
    }

    public function test_completing_a_certification_path_awards_the_seal(): void
    {
        $path = Path::factory()->create([
            'consultant_id' => $this->consultant->id,
            'badge_key' => 'observability_certified',
        ]);
        $first = PathStep::factory()->create(['path_id' => $path->id, 'order' => 0]);
        $last = PathStep::factory()->create(['path_id' => $path->id, 'order' => 1]);

        $this->actingAs($this->client, 'sanctum')->putJson("/api/path-steps/{$first->id}/progress", ['status' => 'done'])->assertOk();
        $this->actingAs($this->client, 'sanctum')->putJson("/api/path-steps/{$last->id}/progress", ['status' => 'done'])->assertOk();

        $this->assertTrue(
            $this->client->fresh()->badges()->where('key', 'observability_certified')->exists(),
            'Completing every step of a badge_key path should award the certification seal.'
        );
    }

    public function test_completing_a_normal_path_awards_no_certification_seal(): void
    {
        // $this->path has no badge_key → only the generic path_completed badge.
        $step = PathStep::factory()->create(['path_id' => $this->path->id, 'order' => 0]);

        $this->actingAs($this->client, 'sanctum')->putJson("/api/path-steps/{$step->id}/progress", ['status' => 'done'])->assertOk();

        $this->assertFalse(
            $this->client->fresh()->badges()->where('key', 'observability_certified')->exists()
        );
        $this->assertTrue(
            $this->client->fresh()->badges()->where('key', 'path_completed')->exists()
        );
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
