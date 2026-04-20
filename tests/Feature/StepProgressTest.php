<?php

namespace Tests\Feature;

use App\Models\Path;
use App\Models\PathStep;
use App\Models\User;
use App\Models\UserStepProgress;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StepProgressTest extends TestCase
{
    use RefreshDatabase;

    private User $client;

    private Path $path;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);

        $this->client = User::factory()->create();
        $this->path = Path::create(['name' => 'Test Path', 'consultant_id' => $this->client->id]);
    }

    private function makeStep(int $order): PathStep
    {
        return PathStep::create([
            'path_id' => $this->path->id,
            'title' => "Step {$order}",
            'order' => $order,
        ]);
    }

    private function setProgress(PathStep $step, string $status): void
    {
        UserStepProgress::updateOrCreate(
            ['user_id' => $this->client->id, 'path_step_id' => $step->id],
            ['status' => $status]
        );
    }

    private function putProgress(PathStep $step, string $status)
    {
        return $this->actingAs($this->client, 'sanctum')
            ->putJson("/api/path-steps/{$step->id}/progress", ['status' => $status]);
    }

    // ── Basic ────────────────────────────────────────────────────────────────

    public function test_client_can_update_step_progress(): void
    {
        $step = $this->makeStep(1);

        $this->putProgress($step, 'in_progress')->assertOk()->assertJson(['status' => 'in_progress']);

        $this->assertDatabaseHas('user_step_progress', [
            'user_id' => $this->client->id,
            'path_step_id' => $step->id,
            'status' => 'in_progress',
        ]);
    }

    public function test_unauthenticated_request_is_rejected(): void
    {
        $step = $this->makeStep(1);

        $this->putJson("/api/path-steps/{$step->id}/progress", ['status' => 'done'])
            ->assertUnauthorized();
    }

    public function test_invalid_status_is_rejected(): void
    {
        $step = $this->makeStep(1);

        $this->putProgress($step, 'watching_netflix')->assertUnprocessable();
    }

    // ── One in_progress per path ─────────────────────────────────────────────

    public function test_setting_in_progress_demotes_other_in_progress_sibling(): void
    {
        $step1 = $this->makeStep(1);
        $step2 = $this->makeStep(2);

        // step1 is already in progress (done first, so order allows step2 to start)
        $this->setProgress($step1, 'done');

        // step2 in progress
        $this->setProgress($step2, 'in_progress');

        // now mark step1 somehow back to in_progress (order 1 < order 2, so no block)
        $this->putProgress($step1, 'in_progress')->assertOk();

        // step2 should have been reset to not_started
        $this->assertDatabaseHas('user_step_progress', [
            'user_id' => $this->client->id,
            'path_step_id' => $step2->id,
            'status' => 'not_started',
        ]);
    }

    public function test_setting_in_progress_does_not_affect_other_paths(): void
    {
        $otherPath = Path::create(['name' => 'Other Path', 'consultant_id' => $this->client->id]);
        $stepA = $this->makeStep(1);
        $stepB = PathStep::create(['path_id' => $otherPath->id, 'title' => 'Other step', 'order' => 1]);

        $this->setProgress($stepB, 'in_progress');

        $this->putProgress($stepA, 'in_progress')->assertOk();

        // stepB on a different path must remain in_progress
        $this->assertDatabaseHas('user_step_progress', [
            'user_id' => $this->client->id,
            'path_step_id' => $stepB->id,
            'status' => 'in_progress',
        ]);
    }

    // ── Step ordering enforcement ────────────────────────────────────────────

    public function test_cannot_start_step_while_preceding_step_is_in_progress(): void
    {
        $step1 = $this->makeStep(1);
        $step2 = $this->makeStep(2);

        $this->setProgress($step1, 'in_progress');

        $this->putProgress($step2, 'in_progress')->assertUnprocessable();

        // step2 must remain unchanged
        $this->assertDatabaseMissing('user_step_progress', [
            'user_id' => $this->client->id,
            'path_step_id' => $step2->id,
            'status' => 'in_progress',
        ]);
    }

    public function test_can_start_step_when_all_preceding_steps_are_done(): void
    {
        $step1 = $this->makeStep(1);
        $step2 = $this->makeStep(2);

        $this->setProgress($step1, 'done');

        $this->putProgress($step2, 'in_progress')->assertOk();
    }

    public function test_can_start_first_step_with_no_prerequisites(): void
    {
        $step1 = $this->makeStep(1);

        $this->putProgress($step1, 'in_progress')->assertOk();
    }

    public function test_marking_done_is_never_blocked_by_preceding_in_progress(): void
    {
        $step1 = $this->makeStep(1);
        $step2 = $this->makeStep(2);

        $this->setProgress($step1, 'in_progress');

        // Marking step2 done (skipping) is allowed — only in_progress is guarded
        $this->putProgress($step2, 'done')->assertOk();
    }

    public function test_can_revert_step_to_not_started_regardless_of_siblings(): void
    {
        $step1 = $this->makeStep(1);
        $step2 = $this->makeStep(2);

        $this->setProgress($step1, 'in_progress');
        $this->setProgress($step2, 'in_progress');

        $this->putProgress($step2, 'not_started')->assertOk();
    }
}
