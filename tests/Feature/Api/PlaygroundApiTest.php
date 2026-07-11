<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Coverage for /api/playground/run — previously untested despite executing
 * arbitrary user-submitted code via Judge0 (docs/architecture-review.md
 * Phase 1). Every Judge0 call is faked; Http::preventStrayRequests()
 * (tests/TestCase) means a missed fake fails the test instead of hitting
 * the real API.
 */
class PlaygroundApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);

        $this->user = User::factory()->create();
        $this->user->assignRole('client');
    }

    // ── Validation ────────────────────────────────────────────

    public function test_run_requires_code(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/playground/run', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('code');
    }

    public function test_run_accepts_code_at_the_10000_char_limit(): void
    {
        $this->fakeJudge0Ok();

        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/playground/run', ['code' => str_repeat('a', 10000)])
            ->assertOk();
    }

    public function test_run_rejects_code_over_the_10000_char_limit(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/playground/run', ['code' => str_repeat('a', 10001)])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('code');
    }

    public function test_run_accepts_language_php(): void
    {
        $this->fakeJudge0Ok();

        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/playground/run', ['code' => '<?php echo 1;', 'language' => 'php'])
            ->assertOk();
    }

    public function test_run_rejects_language_other_than_php(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/playground/run', ['code' => 'print(1)', 'language' => 'python'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('language');
    }

    public function test_unauthenticated_cannot_run_playground(): void
    {
        $this->postJson('/api/playground/run', ['code' => '<?php'])
            ->assertUnauthorized();
    }

    // ── Judge0 outcomes ───────────────────────────────────────

    public function test_run_passes_through_stdout_and_stderr_on_success(): void
    {
        $this->fakeJudge0([
            'status' => ['id' => 3, 'description' => 'Accepted'],
            'stdout' => base64_encode("hello world\n"),
            'stderr' => base64_encode(''),
            'exit_code' => 0,
            'time' => '0.03',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/playground/run', ['code' => '<?php echo "hello world\n";'])
            ->assertOk();

        $response->assertJson([
            'ok' => true,
            'stdout' => "hello world\n",
            'stderr' => '',
            'status' => 'ok',
        ]);
    }

    public function test_run_reports_runtime_error_on_nonzero_exit(): void
    {
        $this->fakeJudge0([
            'status' => ['id' => 3, 'description' => 'Accepted'],
            'stdout' => base64_encode(''),
            'stderr' => base64_encode('PHP Fatal error: Uncaught Error'),
            'exit_code' => 255,
            'time' => '0.02',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/playground/run', ['code' => '<?php throw new Error("x");'])
            ->assertOk();

        $response->assertJson(['ok' => false, 'status' => 'runtime_error']);
    }

    public function test_run_handles_compile_error(): void
    {
        $this->fakeJudge0([
            'status' => ['id' => 6, 'description' => 'Compilation Error'],
            'stdout' => null,
            'stderr' => base64_encode('PHP Parse error: syntax error'),
            'compile_output' => base64_encode('PHP Parse error: syntax error'),
            'exit_code' => 255,
            'time' => '0.01',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/playground/run', ['code' => '<?php }'])
            ->assertOk();

        $response->assertJson(['ok' => false, 'status' => 'compile_error']);
        $this->assertStringContainsString('syntax error', $response->json('stderr'));
    }

    public function test_run_handles_time_limit_exceeded(): void
    {
        $this->fakeJudge0([
            'status' => ['id' => 5, 'description' => 'Time Limit Exceeded'],
            'stdout' => base64_encode(''),
            'stderr' => null,
            'exit_code' => null,
            'time' => '5.0',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/playground/run', ['code' => 'while(true){}'])
            ->assertOk();

        $response->assertJson(['ok' => false, 'status' => 'timeout']);
        $this->assertStringContainsString('Time limit exceeded', $response->json('stderr'));
    }

    public function test_run_handles_judge0_being_down(): void
    {
        Http::fake(['*' => Http::response('Service unavailable', 503)]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/playground/run', ['code' => '<?php'])
            ->assertOk();

        $response->assertJson(['ok' => false, 'status' => 'request_failed']);
        $this->assertStringContainsString('503', $response->json('stderr'));
    }

    // ── Throttle ──────────────────────────────────────────────

    public function test_run_is_throttled_after_30_per_minute(): void
    {
        $this->fakeJudge0Ok();
        $this->actingAs($this->user, 'sanctum');

        for ($i = 0; $i < 30; $i++) {
            $this->postJson('/api/playground/run', ['code' => '<?php echo 1;'])
                ->assertOk();
        }

        $this->postJson('/api/playground/run', ['code' => '<?php echo 1;'])
            ->assertStatus(429);
    }

    /** @param array<string, mixed> $overrides */
    private function fakeJudge0(array $overrides): void
    {
        Http::fake(['*' => Http::response($overrides, 200)]);
    }

    private function fakeJudge0Ok(): void
    {
        $this->fakeJudge0([
            'status' => ['id' => 3, 'description' => 'Accepted'],
            'stdout' => base64_encode('ok'),
            'stderr' => base64_encode(''),
            'exit_code' => 0,
            'time' => '0.01',
        ]);
    }
}
