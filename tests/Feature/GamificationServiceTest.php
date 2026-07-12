<?php

namespace Tests\Feature;

use App\Models\Challenge;
use App\Models\Path;
use App\Models\PathStep;
use App\Models\User;
use App\Models\UserStepProgress;
use App\Services\GamificationService;
use Database\Seeders\BadgesSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Coverage for GamificationService — the F1 practice-funnel engine (XP,
 * daily streaks, milestone badges). See docs/architecture-review.md
 * practice funnel design, stage 3.
 */
class GamificationServiceTest extends TestCase
{
    use RefreshDatabase;

    private GamificationService $gamification;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(BadgesSeeder::class);

        $this->gamification = app(GamificationService::class);
        $this->user = User::factory()->create();
        $this->user->assignRole('client');
    }

    // ── XP amounts ────────────────────────────────────────────

    public function test_challenge_completion_awards_xp_scaled_by_difficulty(): void
    {
        $cases = ['beginner' => 10, 'intermediate' => 20, 'advanced' => 35, 'expert' => 50];

        foreach ($cases as $difficulty => $expectedXp) {
            $user = User::factory()->create();
            $challenge = Challenge::factory()->create(['difficulty' => $difficulty]);

            $result = $this->gamification->recordChallengeCompletion($user, $challenge);

            $this->assertSame($expectedXp, $result['xp_awarded'], "difficulty={$difficulty}");
            $this->assertSame($expectedXp, $result['xp_points']);
        }
    }

    public function test_step_completion_awards_xp_scaled_by_difficulty(): void
    {
        $cases = ['beginner' => 10, 'intermediate' => 15, 'advanced' => 25];

        foreach ($cases as $difficulty => $expectedXp) {
            $user = User::factory()->create();
            $step = PathStep::factory()->create(['difficulty' => $difficulty]);

            $result = $this->gamification->recordStepCompletion($user, $step);

            $this->assertSame($expectedXp, $result['xp_awarded'], "difficulty={$difficulty}");
        }
    }

    public function test_step_with_no_difficulty_awards_the_default_xp(): void
    {
        $step = PathStep::factory()->create(['difficulty' => null]);

        $result = $this->gamification->recordStepCompletion($this->user, $step);

        $this->assertSame(10, $result['xp_awarded']);
    }

    public function test_completing_the_same_challenge_twice_awards_no_repeat_xp(): void
    {
        $challenge = Challenge::factory()->create(['difficulty' => 'beginner']);

        $first = $this->gamification->recordChallengeCompletion($this->user, $challenge);
        $second = $this->gamification->recordChallengeCompletion($this->user, $challenge);

        $this->assertNotNull($first);
        $this->assertNull($second);
        $this->assertSame(10, $this->user->progressStats->fresh()->xp_points);
    }

    public function test_xp_accumulates_across_multiple_completions(): void
    {
        $c1 = Challenge::factory()->create(['difficulty' => 'beginner']);
        $c2 = Challenge::factory()->create(['difficulty' => 'intermediate']);

        $this->gamification->recordChallengeCompletion($this->user, $c1);
        $result = $this->gamification->recordChallengeCompletion($this->user, $c2);

        $this->assertSame(30, $result['xp_points']);
    }

    // ── Streaks ───────────────────────────────────────────────

    public function test_first_ever_activity_starts_a_streak_of_one(): void
    {
        $challenge = Challenge::factory()->create();

        $result = $this->gamification->recordChallengeCompletion($this->user, $challenge);

        $this->assertSame(1, $result['current_streak']);
    }

    public function test_activity_on_consecutive_days_increments_the_streak(): void
    {
        $this->travelTo(Carbon::parse('2026-01-01 10:00:00'));
        $this->gamification->recordChallengeCompletion($this->user, Challenge::factory()->create());

        $this->travelTo(Carbon::parse('2026-01-02 09:00:00'));
        $result = $this->gamification->recordChallengeCompletion($this->user, Challenge::factory()->create());

        $this->assertSame(2, $result['current_streak']);
    }

    public function test_activity_on_the_same_day_does_not_double_count(): void
    {
        $this->travelTo(Carbon::parse('2026-01-01 09:00:00'));
        $this->gamification->recordChallengeCompletion($this->user, Challenge::factory()->create());

        $this->travelTo(Carbon::parse('2026-01-01 20:00:00'));
        $result = $this->gamification->recordChallengeCompletion($this->user, Challenge::factory()->create());

        $this->assertSame(1, $result['current_streak']);
    }

    public function test_missing_a_day_resets_the_streak(): void
    {
        $this->travelTo(Carbon::parse('2026-01-01 09:00:00'));
        $this->gamification->recordChallengeCompletion($this->user, Challenge::factory()->create());

        $this->travelTo(Carbon::parse('2026-01-02 09:00:00'));
        $this->gamification->recordChallengeCompletion($this->user, Challenge::factory()->create());

        $this->travelTo(Carbon::parse('2026-01-05 09:00:00')); // skipped 2 full days
        $result = $this->gamification->recordChallengeCompletion($this->user, Challenge::factory()->create());

        $this->assertSame(1, $result['current_streak']);
    }

    public function test_longest_streak_is_preserved_after_a_reset(): void
    {
        for ($day = 1; $day <= 3; $day++) {
            $this->travelTo(Carbon::parse("2026-01-0{$day} 09:00:00"));
            $this->gamification->recordChallengeCompletion($this->user, Challenge::factory()->create());
        }

        $this->travelTo(Carbon::parse('2026-01-10 09:00:00')); // streak resets
        $this->gamification->recordChallengeCompletion($this->user, Challenge::factory()->create());

        $stats = $this->user->progressStats()->first();
        $this->assertSame(1, $stats->current_streak);
        $this->assertSame(3, $stats->longest_streak);
    }

    // ── Badges ────────────────────────────────────────────────

    public function test_first_challenge_completion_awards_the_first_challenge_badge(): void
    {
        $result = $this->gamification->recordChallengeCompletion($this->user, Challenge::factory()->create());

        $badgeKeys = array_column($result['new_badges'], 'key');
        $this->assertContains('first_challenge', $badgeKeys);
    }

    public function test_first_challenge_badge_is_only_awarded_once(): void
    {
        $this->gamification->recordChallengeCompletion($this->user, Challenge::factory()->create());
        $result = $this->gamification->recordChallengeCompletion($this->user, Challenge::factory()->create());

        $badgeKeys = array_column($result['new_badges'], 'key');
        $this->assertNotContains('first_challenge', $badgeKeys);
        $this->assertSame(1, $this->user->badges()->where('key', 'first_challenge')->count());
    }

    public function test_a_seven_day_streak_awards_the_streak_badge(): void
    {
        $result = null;
        for ($day = 1; $day <= 7; $day++) {
            $this->travelTo(Carbon::parse("2026-02-0{$day} 09:00:00"));
            $result = $this->gamification->recordChallengeCompletion($this->user, Challenge::factory()->create());
        }

        $badgeKeys = array_column($result['new_badges'], 'key');
        $this->assertContains('streak_7', $badgeKeys);
    }

    public function test_completing_every_step_in_a_path_awards_the_path_completed_badge(): void
    {
        $path = Path::factory()->create();
        $steps = PathStep::factory()->count(3)->create(['path_id' => $path->id]);

        UserStepProgress::create(['user_id' => $this->user->id, 'path_step_id' => $steps[0]->id, 'status' => 'done']);
        UserStepProgress::create(['user_id' => $this->user->id, 'path_step_id' => $steps[1]->id, 'status' => 'done']);
        // In the real flow PathStepController writes this row before calling
        // the service — mirror that here.
        UserStepProgress::create(['user_id' => $this->user->id, 'path_step_id' => $steps[2]->id, 'status' => 'done']);

        $result = $this->gamification->recordStepCompletion($this->user, $steps[2]);

        $badgeKeys = array_column($result['new_badges'], 'key');
        $this->assertContains('path_completed', $badgeKeys);
    }

    public function test_completing_one_step_of_an_incomplete_path_does_not_award_the_badge(): void
    {
        $path = Path::factory()->create();
        $steps = PathStep::factory()->count(3)->create(['path_id' => $path->id]);
        UserStepProgress::create(['user_id' => $this->user->id, 'path_step_id' => $steps[0]->id, 'status' => 'done']);

        $result = $this->gamification->recordStepCompletion($this->user, $steps[0]);

        $badgeKeys = array_column($result['new_badges'], 'key');
        $this->assertNotContains('path_completed', $badgeKeys);
    }
}
