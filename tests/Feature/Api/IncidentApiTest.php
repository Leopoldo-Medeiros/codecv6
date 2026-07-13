<?php

namespace Tests\Feature\Api;

use App\Enums\PaymentStatus;
use App\Enums\PaymentTier;
use App\Models\Path;
use App\Models\PathStep;
use App\Models\Payment;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Coverage for the observability-track incident step (Phase A). An incident
 * reuses the F5 quiz grading pipeline: its diagnostic questions live in the
 * `quiz` column and are graded by submitQuiz, while a new `evidence` payload
 * (trace/metrics/logs) is rendered client-side. These tests verify the
 * evidence surfaces, the answer key stays stripped, grading works, and the
 * F4 gate still applies.
 */
class IncidentApiTest extends TestCase
{
    use RefreshDatabase;

    private User $client;

    private Path $path;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);

        $this->client = User::factory()->create();
        $this->client->assignRole('client');
        $this->grantPracticeAccess($this->client);

        $this->path = Path::factory()->create();
    }

    private function grantPracticeAccess(User $user): void
    {
        Payment::create([
            'user_id' => $user->id,
            'stripe_session_id' => 'cs_test_'.$user->id,
            'tier' => PaymentTier::PRACTICE,
            'amount' => 1200,
            'currency' => 'eur',
            'status' => PaymentStatus::PAID,
            'paid_at' => now(),
        ]);
    }

    private function incidentStep(int $order = 3): PathStep
    {
        return PathStep::factory()->create([
            'path_id' => $this->path->id,
            'order' => $order,
            'type' => 'incident',
            'title' => 'Incident: checkout latency',
            'evidence' => [
                'scenario' => 'Checkout p95 climbed from 200ms to 1.4s. Find out why.',
                'trace' => [
                    'root' => 'POST /checkout',
                    'spans' => [
                        ['id' => 'a', 'parent' => null, 'name' => 'POST /checkout', 'service' => 'web', 'start' => 0, 'dur' => 1400, 'kind' => 'server'],
                        ['id' => 'n1', 'parent' => 'a', 'name' => 'SELECT products WHERE id=?', 'service' => 'db', 'start' => 20, 'dur' => 4, 'kind' => 'db', 'repeat' => 120],
                        ['id' => 'p', 'parent' => 'a', 'name' => 'POST payments-gateway', 'service' => 'ext', 'start' => 1200, 'dur' => 180, 'kind' => 'client'],
                    ],
                ],
                'metrics' => [
                    ['title' => 'checkout p95 (ms)', 'unit' => 'ms', 'series' => [[0, 210], [10, 430], [20, 1400]]],
                ],
                'logs' => [
                    ['t' => '12:04:32', 'level' => 'WARN', 'request_id' => 'req_9f2', 'msg' => 'query executed 120 times in request'],
                ],
            ],
            'quiz' => [
                ['id' => 1, 'question' => 'Which operation dominates the request time?', 'options' => ['Payment call', 'Repeated product lookups', 'Auth middleware'], 'correct_index' => 1, 'explanation' => 'The product lookup repeats 120x — that is the N+1.'],
                ['id' => 2, 'question' => 'Root cause?', 'options' => ['N+1 query', 'Memory leak', 'Slow downstream'], 'correct_index' => 0, 'explanation' => 'One query per cart item.'],
            ],
        ]);
    }

    public function test_incident_show_exposes_evidence_without_the_answer_key(): void
    {
        $step = $this->incidentStep();

        $response = $this->actingAs($this->client, 'sanctum')
            ->getJson("/api/path-steps/{$step->id}")
            ->assertOk()
            ->assertJsonPath('data.type', 'incident')
            ->assertJsonPath('data.evidence.trace.root', 'POST /checkout')
            ->assertJsonPath('data.evidence.trace.spans.1.repeat', 120);

        // Quiz questions surface (for rendering) but the answer key does not.
        $quiz = $response->json('data.quiz');
        $this->assertCount(2, $quiz);
        $this->assertArrayHasKey('options', $quiz[0]);
        $this->assertArrayNotHasKey('correct_index', $quiz[0]);
        $this->assertArrayNotHasKey('explanation', $quiz[0]);
    }

    public function test_incident_grades_all_correct_as_passed(): void
    {
        $step = $this->incidentStep();

        $this->actingAs($this->client, 'sanctum')
            ->postJson("/api/path-steps/{$step->id}/quiz", ['answers' => [1 => 1, 2 => 0]])
            ->assertOk()
            ->assertJson(['score' => 2, 'total' => 2, 'passed' => true]);
    }

    public function test_incident_grades_partial_and_returns_review_data(): void
    {
        $step = $this->incidentStep();

        $response = $this->actingAs($this->client, 'sanctum')
            ->postJson("/api/path-steps/{$step->id}/quiz", ['answers' => [1 => 0, 2 => 0]])
            ->assertOk()
            ->assertJson(['score' => 1, 'total' => 2, 'passed' => false]);

        // Review data (correct_index + explanation) comes back only after submit.
        $this->assertSame(1, $response->json('results.0.correct_index'));
        $this->assertFalse($response->json('results.0.correct'));
        $this->assertNotEmpty($response->json('results.0.explanation'));
    }

    public function test_reading_step_still_404s_on_the_grading_endpoint(): void
    {
        $reading = PathStep::factory()->create([
            'path_id' => $this->path->id,
            'type' => 'reading',
            'quiz' => null,
        ]);

        $this->actingAs($this->client, 'sanctum')
            ->postJson("/api/path-steps/{$reading->id}/quiz", ['answers' => []])
            ->assertNotFound();
    }

    public function test_free_user_cannot_submit_a_locked_incident(): void
    {
        $freeUser = User::factory()->create();
        $freeUser->assignRole('client');

        // order >= 2 → not free; the F4 gate must block a user without access.
        $step = $this->incidentStep(order: 5);

        $this->actingAs($freeUser, 'sanctum')
            ->postJson("/api/path-steps/{$step->id}/quiz", ['answers' => [1 => 1, 2 => 0]])
            ->assertForbidden();
    }
}
