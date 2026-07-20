<?php

namespace Tests\Feature\Api;

use App\Enums\PaymentStatus;
use App\Enums\PaymentTier;
use App\Models\Challenge;
use App\Models\ChallengeSubmission;
use App\Models\Payment;
use App\Models\User;
use App\Models\UserChallengeCompletion;
use Database\Seeders\BadgesSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Coverage for /api/challenges — previously untested despite executing
 * user-submitted code via Judge0 (docs/architecture-review.md Phase 1).
 * Every Judge0 call is faked; Http::preventStrayRequests() (tests/TestCase)
 * means a missed fake fails the test instead of hitting the real API.
 *
 * $this->user holds Practice Pro access (a paid payment) so the run/show
 * mechanics tests aren't affected by the F4 content gate regardless of the
 * factory's random difficulty — the gate itself is covered separately at
 * the bottom with a dedicated free user.
 */
class ChallengeApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(BadgesSeeder::class);

        $this->user = User::factory()->create();
        $this->user->assignRole('client');
        $this->grantPracticeAccess($this->user);
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

    public function test_list_marks_the_users_solved_challenges(): void
    {
        $solved = Challenge::factory()->create();
        $unsolved = Challenge::factory()->create();
        UserChallengeCompletion::create([
            'user_id' => $this->user->id,
            'challenge_id' => $solved->id,
            'completed_at' => now(),
        ]);

        $rows = collect(
            $this->actingAs($this->user, 'sanctum')
                ->getJson('/api/challenges')
                ->assertOk()
                ->json('data')
        );

        $this->assertTrue($rows->firstWhere('id', $solved->id)['solved']);
        $this->assertFalse($rows->firstWhere('id', $unsolved->id)['solved']);
    }

    public function test_solved_flag_is_per_user(): void
    {
        $challenge = Challenge::factory()->create();
        $otherUser = User::factory()->create();
        $otherUser->assignRole('client');
        UserChallengeCompletion::create([
            'user_id' => $otherUser->id,
            'challenge_id' => $challenge->id,
            'completed_at' => now(),
        ]);

        $rows = collect(
            $this->actingAs($this->user, 'sanctum')
                ->getJson('/api/challenges')
                ->assertOk()
                ->json('data')
        );

        $this->assertFalse($rows->firstWhere('id', $challenge->id)['solved']);
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

    // ── Iterations (submission history) ───────────────────────

    public function test_a_run_records_a_submission(): void
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

        $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/challenges/{$challenge->slug}/run", ['code' => '<?php echo 1;'])
            ->assertOk();

        $this->assertDatabaseHas('challenge_submissions', [
            'user_id' => $this->user->id,
            'challenge_id' => $challenge->id,
            'code' => '<?php echo 1;',
            'passed' => true,
            'failed_count' => 0,
        ]);
    }

    public function test_a_failing_run_is_also_recorded_as_an_iteration(): void
    {
        $challenge = Challenge::factory()->create();
        $this->fakeJudge0([
            'status' => ['id' => 3, 'description' => 'Accepted'],
            'stdout' => $this->b64Tests([
                ['name' => 'It works', 'passed' => false, 'message' => 'nope'],
            ]),
            'stderr' => null,
            'exit_code' => 0,
            'time' => '0.05',
        ]);

        $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/challenges/{$challenge->slug}/run", ['code' => '<?php broken']);

        $this->assertDatabaseHas('challenge_submissions', [
            'user_id' => $this->user->id,
            'challenge_id' => $challenge->id,
            'passed' => false,
            'failed_count' => 1,
        ]);
    }

    public function test_submissions_returns_only_the_callers_history_newest_first(): void
    {
        $challenge = Challenge::factory()->create();
        $other = User::factory()->create();
        $other->assignRole('client');

        ChallengeSubmission::create(['user_id' => $this->user->id, 'challenge_id' => $challenge->id, 'code' => 'v1', 'passed' => false, 'failed_count' => 1, 'duration_ms' => 40]);
        ChallengeSubmission::create(['user_id' => $this->user->id, 'challenge_id' => $challenge->id, 'code' => 'v2', 'passed' => true, 'failed_count' => 0, 'duration_ms' => 35]);
        ChallengeSubmission::create(['user_id' => $other->id, 'challenge_id' => $challenge->id, 'code' => 'not mine', 'passed' => true, 'failed_count' => 0, 'duration_ms' => 10]);

        $data = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/challenges/{$challenge->slug}/submissions")
            ->assertOk()
            ->json('data');

        $this->assertCount(2, $data);
        $this->assertSame('v2', $data[0]['code']);
        $this->assertSame('v1', $data[1]['code']);
    }

    public function test_submission_history_is_capped_at_twenty(): void
    {
        $challenge = Challenge::factory()->create();
        for ($i = 1; $i <= 20; $i++) {
            ChallengeSubmission::create(['user_id' => $this->user->id, 'challenge_id' => $challenge->id, 'code' => "v$i", 'passed' => false, 'failed_count' => 1, 'duration_ms' => 5]);
        }

        $this->fakeJudge0([
            'status' => ['id' => 3, 'description' => 'Accepted'],
            'stdout' => $this->b64Tests([
                ['name' => 'It works', 'passed' => true, 'message' => null],
            ]),
            'stderr' => null,
            'exit_code' => 0,
            'time' => '0.05',
        ]);

        $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/challenges/{$challenge->slug}/run", ['code' => 'v21'])
            ->assertOk();

        $this->assertSame(20, ChallengeSubmission::where('user_id', $this->user->id)->where('challenge_id', $challenge->id)->count());
        $this->assertDatabaseMissing('challenge_submissions', ['user_id' => $this->user->id, 'code' => 'v1']);
        $this->assertDatabaseHas('challenge_submissions', ['user_id' => $this->user->id, 'code' => 'v21']);
    }

    public function test_unauthenticated_cannot_list_submissions(): void
    {
        $challenge = Challenge::factory()->create();

        $this->getJson("/api/challenges/{$challenge->slug}/submissions")->assertUnauthorized();
    }

    // ── Gamification (F1) ─────────────────────────────────────

    public function test_passing_a_challenge_awards_xp_and_a_badge(): void
    {
        $challenge = Challenge::factory()->create(['difficulty' => 'intermediate']);
        $this->fakeJudge0Pass();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/challenges/{$challenge->slug}/run", ['code' => '<?php'])
            ->assertOk();

        $response->assertJsonPath('progress.xp_awarded', 20);
        $response->assertJsonPath('progress.xp_points', 20);
        $badgeKeys = array_column($response->json('progress.new_badges'), 'key');
        $this->assertContains('first_challenge', $badgeKeys);
    }

    public function test_failing_a_challenge_awards_no_xp(): void
    {
        $challenge = Challenge::factory()->create();
        $this->fakeJudge0([
            'status' => ['id' => 3, 'description' => 'Accepted'],
            'stdout' => $this->b64Tests([['name' => 'It works', 'passed' => false, 'message' => 'nope']]),
            'stderr' => null,
            'exit_code' => 0,
            'time' => '0.02',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/challenges/{$challenge->slug}/run", ['code' => '<?php'])
            ->assertOk();

        $this->assertNull($response->json('progress'));
    }

    public function test_passing_the_same_challenge_twice_awards_xp_only_once(): void
    {
        $challenge = Challenge::factory()->create(['difficulty' => 'beginner']);
        $this->fakeJudge0Pass();

        $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/challenges/{$challenge->slug}/run", ['code' => '<?php'])
            ->assertOk();

        $second = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/challenges/{$challenge->slug}/run", ['code' => '<?php'])
            ->assertOk();

        $this->assertNull($second->json('progress'));
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

    // ── F4 content gate ───────────────────────────────────────

    public function test_free_user_cannot_open_a_premium_challenge(): void
    {
        $free = User::factory()->create();
        $free->assignRole('client');
        $challenge = Challenge::factory()->create(['difficulty' => 'advanced', 'is_teaser' => false]);

        $this->actingAs($free, 'sanctum')
            ->getJson("/api/challenges/{$challenge->slug}")
            ->assertForbidden();
    }

    public function test_free_user_cannot_run_a_premium_challenge(): void
    {
        $free = User::factory()->create();
        $free->assignRole('client');
        $challenge = Challenge::factory()->create(['difficulty' => 'advanced', 'is_teaser' => false]);

        $this->actingAs($free, 'sanctum')
            ->postJson("/api/challenges/{$challenge->slug}/run", ['code' => '<?php'])
            ->assertForbidden();
    }

    public function test_free_user_can_open_a_beginner_challenge(): void
    {
        $free = User::factory()->create();
        $free->assignRole('client');
        $challenge = Challenge::factory()->create(['difficulty' => 'beginner', 'is_teaser' => false]);

        $this->actingAs($free, 'sanctum')
            ->getJson("/api/challenges/{$challenge->slug}")
            ->assertOk();
    }

    public function test_practice_pro_user_can_open_a_premium_challenge(): void
    {
        $challenge = Challenge::factory()->create(['difficulty' => 'advanced', 'is_teaser' => false]);

        // $this->user holds Practice Pro access (see setUp)
        $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/challenges/{$challenge->slug}")
            ->assertOk();
    }

    public function test_list_marks_premium_challenges_locked_and_hides_their_code_for_free_users(): void
    {
        $free = User::factory()->create();
        $free->assignRole('client');
        Challenge::factory()->create(['title' => 'Beginner Free', 'difficulty' => 'beginner', 'is_teaser' => false]);
        Challenge::factory()->create(['title' => 'Advanced Locked', 'difficulty' => 'advanced', 'is_teaser' => false]);

        $data = collect($this->actingAs($free, 'sanctum')->getJson('/api/challenges')->json('data'));

        $begin = $data->firstWhere('title', 'Beginner Free');
        $adv = $data->firstWhere('title', 'Advanced Locked');

        $this->assertFalse($begin['locked']);
        $this->assertNotNull($begin['boilerplate_code']);
        $this->assertTrue($adv['locked']);
        $this->assertNull($adv['boilerplate_code']);
        $this->assertNull($adv['tests_code']);
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
