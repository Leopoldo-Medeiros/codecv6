<?php

namespace Tests\Feature\Api;

use App\Models\Challenge;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Coverage for /api/challenges — previously untested despite executing
 * user-submitted code via Judge0 (docs/architecture-review.md Phase 1).
 * Every Judge0 call is faked; Http::preventStrayRequests() (tests/TestCase)
 * means a missed fake fails the test instead of hitting the real API.
 */
class ChallengeApiTest extends TestCase
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

    // ── Index / Show ──────────────────────────────────────────

    public function test_authenticated_user_can_list_challenges(): void
    {
        Challenge::factory()->count(3)->create();

        $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/challenges')
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_unauthenticated_cannot_list_challenges(): void
    {
        $this->getJson('/api/challenges')->assertUnauthorized();
    }

    public function test_authenticated_user_can_view_challenge_by_slug(): void
    {
        $challenge = Challenge::factory()->create(['slug' => 'reverse-a-string']);

        $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/challenges/reverse-a-string')
            ->assertOk()
            ->assertJsonPath('data.slug', 'reverse-a-string');
    }

    public function test_show_returns_404_for_missing_slug(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/challenges/does-not-exist')
            ->assertNotFound();
    }

    // ── Run: validation ───────────────────────────────────────

    public function test_run_requires_code(): void
    {
        $challenge = Challenge::factory()->create();

        $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/challenges/{$challenge->slug}/run", [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('code');
    }

    public function test_run_accepts_code_at_the_65535_char_limit(): void
    {
        $challenge = Challenge::factory()->create();
        $this->fakeJudge0Pass();

        $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/challenges/{$challenge->slug}/run", [
                'code' => str_repeat('a', 65535),
            ])->assertOk();
    }

    public function test_run_rejects_code_over_the_65535_char_limit(): void
    {
        $challenge = Challenge::factory()->create();

        $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/challenges/{$challenge->slug}/run", [
                'code' => str_repeat('a', 65536),
            ])->assertUnprocessable()
            ->assertJsonValidationErrors('code');
    }

    public function test_unauthenticated_cannot_run_challenge(): void
    {
        $challenge = Challenge::factory()->create();

        $this->postJson("/api/challenges/{$challenge->slug}/run", ['code' => '<?php'])
            ->assertUnauthorized();
    }

    // ── Run: Judge0 outcomes ──────────────────────────────────

    public function test_run_reports_all_tests_passing(): void
    {
        $challenge = Challenge::factory()->create();
        $this->fakeJudge0([
            'status' => ['id' => 3, 'description' => 'Accepted'],
            'stdout' => $this->b64Tests([
                ['name' => 'It works', 'passed' => true, 'message' => null],
            ]),
            'stderr' => null,
            'exit_code' => 0,
            'time' => '0.05',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/challenges/{$challenge->slug}/run", ['code' => '<?php'])
            ->assertOk();

        $response->assertJson([
            'passed' => true,
            'failedCount' => 0,
        ]);
        $this->assertCount(1, $response->json('tests'));
    }

    public function test_run_reports_failing_tests(): void
    {
        $challenge = Challenge::factory()->create();
        $this->fakeJudge0([
            'status' => ['id' => 3, 'description' => 'Accepted'],
            'stdout' => $this->b64Tests([
                ['name' => 'It works', 'passed' => true, 'message' => null],
                ['name' => 'It handles edge case', 'passed' => false, 'message' => 'Expected 2, got 1'],
            ]),
            'stderr' => null,
            'exit_code' => 0,
            'time' => '0.05',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/challenges/{$challenge->slug}/run", ['code' => '<?php'])
            ->assertOk();

        $response->assertJson(['passed' => false, 'failedCount' => 1]);
    }

    public function test_run_handles_time_limit_exceeded(): void
    {
        $challenge = Challenge::factory()->create();
        $this->fakeJudge0([
            'status' => ['id' => 5, 'description' => 'Time Limit Exceeded'],
            'stdout' => null,
            'stderr' => null,
            'exit_code' => null,
            'time' => '10.0',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/challenges/{$challenge->slug}/run", ['code' => 'while(true){}'])
            ->assertOk();

        $response->assertJson(['passed' => false]);
        $this->assertStringContainsString('time limit', $response->json('tests.0.message'));
    }

    public function test_run_handles_compile_error(): void
    {
        $challenge = Challenge::factory()->create();
        $this->fakeJudge0([
            'status' => ['id' => 6, 'description' => 'Compilation Error'],
            'stdout' => null,
            'stderr' => base64_encode('PHP Parse error: syntax error, unexpected token "}"'),
            'exit_code' => 255,
            'time' => '0.01',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/challenges/{$challenge->slug}/run", ['code' => '<?php }'])
            ->assertOk();

        $response->assertJson(['passed' => false]);
        $this->assertStringContainsString('syntax error', $response->json('tests.0.message'));
    }

    public function test_run_handles_judge0_being_down(): void
    {
        $challenge = Challenge::factory()->create();
        Http::fake(['*' => Http::response('Service unavailable', 503)]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/challenges/{$challenge->slug}/run", ['code' => '<?php'])
            ->assertOk();

        $response->assertJson(['passed' => false]);
        $this->assertStringContainsString('Judge0 API request failed', $response->json('tests.0.message'));
    }

    /** @param array<string, mixed> $overrides */
    private function fakeJudge0(array $overrides): void
    {
        Http::fake(['*' => Http::response($overrides, 200)]);
    }

    private function fakeJudge0Pass(): void
    {
        $this->fakeJudge0([
            'status' => ['id' => 3, 'description' => 'Accepted'],
            'stdout' => $this->b64Tests([['name' => 'It works', 'passed' => true, 'message' => null]]),
            'stderr' => null,
            'exit_code' => 0,
            'time' => '0.05',
        ]);
    }

    private function b64Tests(array $tests): string
    {
        return base64_encode(json_encode($tests));
    }
}
