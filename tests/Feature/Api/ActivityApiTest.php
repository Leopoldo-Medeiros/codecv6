<?php

namespace Tests\Feature\Api;

use App\Models\Challenge;
use App\Models\User;
use App\Models\UserChallengeCompletion;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Coverage for GET /me/activity — the daily practice counts that feed the
 * dashboard contribution heatmap. Derived from timestamped completions, so
 * no per-user activity log is needed.
 */
class ActivityApiTest extends TestCase
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

    public function test_returns_empty_activity_for_a_new_user(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/me/activity')
            ->assertOk()
            ->assertJson(['activity' => []]);
    }

    public function test_counts_challenge_completions_per_day(): void
    {
        UserChallengeCompletion::create([
            'user_id' => $this->user->id,
            'challenge_id' => Challenge::factory()->create()->id,
            'completed_at' => '2026-07-10 09:00:00',
        ]);
        UserChallengeCompletion::create([
            'user_id' => $this->user->id,
            'challenge_id' => Challenge::factory()->create()->id,
            'completed_at' => '2026-07-10 14:00:00',
        ]);
        UserChallengeCompletion::create([
            'user_id' => $this->user->id,
            'challenge_id' => Challenge::factory()->create()->id,
            'completed_at' => '2026-07-11 10:00:00',
        ]);

        $activity = collect(
            $this->actingAs($this->user, 'sanctum')
                ->getJson('/api/me/activity')
                ->assertOk()
                ->json('activity')
        )->keyBy('date');

        $this->assertSame(2, $activity['2026-07-10']['count']);
        $this->assertSame(1, $activity['2026-07-11']['count']);
    }

    public function test_activity_is_per_user(): void
    {
        UserChallengeCompletion::create([
            'user_id' => $this->user->id,
            'challenge_id' => Challenge::factory()->create()->id,
            'completed_at' => now(),
        ]);
        $other = User::factory()->create();
        $other->assignRole('client');

        $this->actingAs($other, 'sanctum')
            ->getJson('/api/me/activity')
            ->assertOk()
            ->assertJson(['activity' => []]);
    }

    public function test_unauthenticated_cannot_read_activity(): void
    {
        $this->getJson('/api/me/activity')->assertUnauthorized();
    }
}
