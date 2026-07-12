<?php

namespace Tests\Feature\Api;

use App\Models\Challenge;
use App\Models\User;
use App\Services\GamificationService;
use Database\Seeders\BadgesSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Coverage for GET /me/progress — the F1 gamification read endpoint
 * (docs/architecture-review.md practice funnel design, stage 3).
 */
class ProgressApiTest extends TestCase
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
    }

    public function test_returns_zeroed_stats_for_a_user_with_no_activity(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/me/progress')
            ->assertOk()
            ->assertJson([
                'xp_points' => 0,
                'current_streak' => 0,
                'longest_streak' => 0,
                'last_practiced_at' => null,
                'badges' => [],
            ]);
    }

    public function test_returns_accumulated_xp_streak_and_badges(): void
    {
        app(GamificationService::class)->recordChallengeCompletion(
            $this->user,
            Challenge::factory()->create(['difficulty' => 'intermediate'])
        );

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/me/progress')
            ->assertOk();

        $response->assertJson([
            'xp_points' => 20,
            'current_streak' => 1,
        ]);
        $this->assertNotNull($response->json('last_practiced_at'));
        $badgeKeys = array_column($response->json('badges'), 'key');
        $this->assertContains('first_challenge', $badgeKeys);
    }

    public function test_unauthenticated_cannot_view_progress(): void
    {
        $this->getJson('/api/me/progress')->assertUnauthorized();
    }
}
