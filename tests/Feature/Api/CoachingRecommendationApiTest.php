<?php

namespace Tests\Feature\Api;

use App\Enums\PaymentStatus;
use App\Enums\PaymentTier;
use App\Models\Challenge;
use App\Models\Path;
use App\Models\PathStep;
use App\Models\Payment;
use App\Models\User;
use App\Models\UserChallengeCompletion;
use App\Models\UserProgressStats;
use App\Models\UserStepProgress;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

/**
 * Coverage for GET /me/coaching-recommendation — the F6 coaching upsell
 * nudge. Verifies the earned-ladder logic in CoachingRecommendationService:
 * priority order, OR-threshold matching, suppression of owned tiers, and no
 * nudge for a brand-new user.
 */
class CoachingRecommendationApiTest extends TestCase
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

    private function fetch(): TestResponse
    {
        return $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/me/coaching-recommendation');
    }

    private function giveChallengeCompletions(int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            UserChallengeCompletion::create([
                'user_id' => $this->user->id,
                'challenge_id' => Challenge::factory()->create()->id,
                'completed_at' => now(),
            ]);
        }
    }

    private function giveStats(array $attributes): void
    {
        UserProgressStats::updateOrCreate(['user_id' => $this->user->id], $attributes);
    }

    private function giveCompletedPath(int $steps = 3): void
    {
        $path = Path::factory()->create();
        for ($order = 0; $order < $steps; $order++) {
            $step = PathStep::factory()->create(['path_id' => $path->id, 'order' => $order]);
            UserStepProgress::create([
                'user_id' => $this->user->id,
                'path_step_id' => $step->id,
                'status' => 'done',
            ]);
        }
    }

    private function givePaidTier(PaymentTier $tier): void
    {
        Payment::create([
            'user_id' => $this->user->id,
            'stripe_session_id' => 'cs_test_'.$tier->value,
            'tier' => $tier,
            'amount' => 1000,
            'currency' => 'eur',
            'status' => PaymentStatus::PAID,
            'paid_at' => now(),
        ]);
    }

    public function test_brand_new_user_gets_no_nudge(): void
    {
        $this->fetch()
            ->assertOk()
            ->assertJson(['recommendation' => null]);
    }

    public function test_three_challenges_earns_the_bootcamp_nudge(): void
    {
        $this->giveChallengeCompletions(3);

        $this->fetch()
            ->assertOk()
            ->assertJsonPath('recommendation.tier', 'bootcamp')
            ->assertJsonPath('recommendation.name', 'Laravel + NR Bootcamp');
    }

    public function test_five_challenges_earns_the_accelerator_nudge(): void
    {
        $this->giveChallengeCompletions(5);

        $this->fetch()
            ->assertOk()
            ->assertJsonPath('recommendation.tier', 'accelerator');
    }

    public function test_completing_a_path_earns_the_accelerator_nudge(): void
    {
        $this->giveCompletedPath();

        $this->fetch()
            ->assertOk()
            ->assertJsonPath('recommendation.tier', 'accelerator');
    }

    public function test_long_streak_earns_the_mentorship_nudge(): void
    {
        $this->giveStats(['longest_streak' => 7]);

        $this->fetch()
            ->assertOk()
            ->assertJsonPath('recommendation.tier', 'mentorship');
    }

    public function test_high_xp_earns_the_mentorship_nudge(): void
    {
        $this->giveStats(['xp_points' => 300]);

        $this->fetch()
            ->assertOk()
            ->assertJsonPath('recommendation.tier', 'mentorship');
    }

    public function test_mentorship_outranks_lower_tiers_when_several_qualify(): void
    {
        // Qualifies for all three; the highest-priority tier must win.
        $this->giveChallengeCompletions(5);
        $this->giveStats(['xp_points' => 300, 'longest_streak' => 7]);

        $this->fetch()
            ->assertOk()
            ->assertJsonPath('recommendation.tier', 'mentorship');
    }

    public function test_owned_tier_is_suppressed_and_falls_through(): void
    {
        // Power user who already bought mentorship: the next qualifying tier
        // (accelerator, via 5 challenges) should surface instead.
        $this->giveStats(['xp_points' => 300]);
        $this->giveChallengeCompletions(5);
        $this->givePaidTier(PaymentTier::MENTORSHIP);

        $this->fetch()
            ->assertOk()
            ->assertJsonPath('recommendation.tier', 'accelerator');
    }

    public function test_recommendation_carries_copy_and_prices(): void
    {
        $this->giveStats(['longest_streak' => 7]);

        $response = $this->fetch()->assertOk();

        $this->assertNotEmpty($response->json('recommendation.headline'));
        $this->assertNotEmpty($response->json('recommendation.body'));
        $this->assertNotEmpty($response->json('recommendation.cta'));
        $this->assertIsInt($response->json('recommendation.prices.eur'));
    }

    public function test_consultant_gets_no_nudge_even_with_signals(): void
    {
        // Coaching nudges target clients — a consultant with practice signals
        // (e.g. from creating/testing content) must never be nudged to buy.
        $consultant = User::factory()->create();
        $consultant->assignRole('consultant');
        UserProgressStats::create([
            'user_id' => $consultant->id,
            'xp_points' => 500,
            'longest_streak' => 30,
        ]);

        $this->actingAs($consultant, 'sanctum')
            ->getJson('/api/me/coaching-recommendation')
            ->assertOk()
            ->assertJson(['recommendation' => null]);
    }

    public function test_unauthenticated_cannot_get_a_recommendation(): void
    {
        $this->getJson('/api/me/coaching-recommendation')->assertUnauthorized();
    }
}
