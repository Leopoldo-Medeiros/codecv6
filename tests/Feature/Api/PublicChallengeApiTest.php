<?php

namespace Tests\Feature\Api;

use App\Models\Challenge;
use App\Models\UserChallengeCompletion;
use App\Models\UserProgressStats;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Coverage for the anonymous /api/public/challenges surface — practice
 * funnel stage 1 / F2 (docs/architecture-review.md practice funnel design).
 * No authenticated actor in any test here — RoleSeeder is only needed
 * because ChallengeFactory's created_by spins up a User, whose factory
 * auto-assigns the client role. Every Judge0 call is Http::fake'd.
 */
class PublicChallengeApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    // ── Teaser list ───────────────────────────────────────────

    public function test_teaser_list_only_returns_teaser_challenges(): void
    {
        Challenge::factory()->create(['title' => 'Public One', 'is_teaser' => true]);
        Challenge::factory()->create(['title' => 'Hidden', 'is_teaser' => false]);

        $response = $this->getJson('/api/public/challenges/teaser')->assertOk();

        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.title', 'Public One');
    }

    public function test_teaser_list_does_not_expose_tests_code_or_premium_fields(): void
    {
        Challenge::factory()->create(['is_teaser' => true, 'tests_code' => '<?php // secret assertions']);

        $response = $this->getJson('/api/public/challenges/teaser')->assertOk();

        $challengeJson = $response->json('data.0');
        $this->assertArrayNotHasKey('tests_code', $challengeJson);
        $this->assertArrayNotHasKey('is_premium', $challengeJson);
        $this->assertArrayNotHasKey('created_by', $challengeJson);
        $this->assertArrayNotHasKey('id', $challengeJson);
    }

    public function test_teaser_list_requires_no_authentication(): void
    {
        Challenge::factory()->create(['is_teaser' => true]);

        $this->getJson('/api/public/challenges/teaser')->assertOk();
    }

    // ── Run ───────────────────────────────────────────────────

    public function test_can_run_a_teaser_challenge_without_authentication(): void
    {
        $challenge = Challenge::factory()->create(['is_teaser' => true]);
        $this->fakeJudge0Pass();

        $this->postJson("/api/public/challenges/{$challenge->slug}/run", ['code' => '<?php'])
            ->assertOk()
            ->assertJson(['passed' => true, 'failedCount' => 0]);
    }

    public function test_running_a_non_teaser_challenge_returns_404(): void
    {
        $challenge = Challenge::factory()->create(['is_teaser' => false]);

        $this->postJson("/api/public/challenges/{$challenge->slug}/run", ['code' => '<?php'])
            ->assertNotFound();
    }

    public function test_running_an_unknown_slug_returns_404(): void
    {
        $this->postJson('/api/public/challenges/does-not-exist/run', ['code' => '<?php'])
            ->assertNotFound();
    }

    public function test_run_requires_code(): void
    {
        $challenge = Challenge::factory()->create(['is_teaser' => true]);

        $this->postJson("/api/public/challenges/{$challenge->slug}/run", [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('code');
    }

    public function test_run_rejects_code_over_the_65535_char_limit(): void
    {
        $challenge = Challenge::factory()->create(['is_teaser' => true]);

        $this->postJson("/api/public/challenges/{$challenge->slug}/run", [
            'code' => str_repeat('a', 65536),
        ])->assertUnprocessable();
    }

    public function test_run_records_no_progress_xp_or_completion(): void
    {
        $challenge = Challenge::factory()->create(['is_teaser' => true]);
        $this->fakeJudge0Pass();

        $response = $this->postJson("/api/public/challenges/{$challenge->slug}/run", ['code' => '<?php'])
            ->assertOk();

        $this->assertArrayNotHasKey('progress', $response->json());
        $this->assertSame(0, UserChallengeCompletion::count());
        $this->assertSame(0, UserProgressStats::count());
    }

    public function test_run_is_throttled_after_5_per_minute(): void
    {
        $challenge = Challenge::factory()->create(['is_teaser' => true]);
        $this->fakeJudge0Pass();

        for ($i = 0; $i < 5; $i++) {
            $this->postJson("/api/public/challenges/{$challenge->slug}/run", ['code' => '<?php'])
                ->assertOk();
        }

        $this->postJson("/api/public/challenges/{$challenge->slug}/run", ['code' => '<?php'])
            ->assertStatus(429);
    }

    private function fakeJudge0Pass(): void
    {
        Http::fake(['*' => Http::response([
            'status' => ['id' => 3, 'description' => 'Accepted'],
            'stdout' => base64_encode(json_encode([
                ['name' => 'It works', 'passed' => true, 'message' => null],
            ])),
            'stderr' => null,
            'exit_code' => 0,
            'time' => '0.03',
        ], 200)]);
    }
}
