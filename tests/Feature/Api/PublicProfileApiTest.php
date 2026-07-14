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
 * Coverage for the public skill profile — practice funnel stage 5 / F3:
 * PATCH /me/public-profile (owner toggles visibility) and
 * GET /public/profile/{slug} (anyone with the link reads the allow-listed
 * payload).
 */
class PublicProfileApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(BadgesSeeder::class);

        $this->user = User::factory()->create(['fullname' => 'Ada Lovelace']);
        $this->user->assignRole('client');
    }

    private function makePublic(): string
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->patchJson('/api/me/public-profile', ['is_public' => true]);

        return $response->json('public_slug');
    }

    // ── Visibility toggle ─────────────────────────────────────

    public function test_enabling_visibility_generates_a_slug(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->patchJson('/api/me/public-profile', ['is_public' => true])
            ->assertOk()
            ->assertJson(['is_public' => true]);

        $slug = $response->json('public_slug');
        $this->assertNotNull($slug);
        $this->assertStringStartsWith('ada-lovelace-', $slug);
    }

    public function test_profile_is_private_by_default(): void
    {
        $this->assertFalse((bool) ($this->user->profile?->is_public));
    }

    public function test_disabling_visibility_keeps_the_slug_stable(): void
    {
        $slug = $this->makePublic();

        $this->actingAs($this->user, 'sanctum')
            ->patchJson('/api/me/public-profile', ['is_public' => false])
            ->assertOk()
            ->assertJson(['is_public' => false, 'public_slug' => $slug]);

        $this->actingAs($this->user, 'sanctum')
            ->patchJson('/api/me/public-profile', ['is_public' => true])
            ->assertOk()
            ->assertJson(['public_slug' => $slug]);
    }

    public function test_toggle_requires_the_is_public_field(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->patchJson('/api/me/public-profile', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('is_public');
    }

    public function test_unauthenticated_cannot_toggle_visibility(): void
    {
        $this->patchJson('/api/me/public-profile', ['is_public' => true])
            ->assertUnauthorized();
    }

    // ── Public view ───────────────────────────────────────────

    public function test_public_profile_is_viewable_without_authentication(): void
    {
        $slug = $this->makePublic();

        $this->getJson("/api/public/profile/{$slug}")
            ->assertOk()
            ->assertJsonPath('data.fullname', 'Ada Lovelace');
    }

    public function test_private_profile_returns_404_even_with_the_right_slug(): void
    {
        $slug = $this->makePublic();

        $this->actingAs($this->user, 'sanctum')
            ->patchJson('/api/me/public-profile', ['is_public' => false]);

        $this->getJson("/api/public/profile/{$slug}")->assertNotFound();
    }

    public function test_unknown_slug_returns_404(): void
    {
        $this->getJson('/api/public/profile/nobody-here-abc123')->assertNotFound();
    }

    public function test_payload_includes_practice_history_and_stats(): void
    {
        app(GamificationService::class)->recordChallengeCompletion(
            $this->user,
            Challenge::factory()->create(['title' => 'Fix The Bug', 'difficulty' => 'intermediate'])
        );
        $slug = $this->makePublic();

        $response = $this->getJson("/api/public/profile/{$slug}")->assertOk();

        $response->assertJsonPath('data.stats.xp_points', 20);
        $response->assertJsonPath('data.completed_challenges.0.title', 'Fix The Bug');
        $response->assertJsonPath('data.completed_challenges.0.difficulty', 'intermediate');
        $badgeKeys = array_column($response->json('data.badges'), 'key');
        $this->assertContains('first_challenge', $badgeKeys);

        // Badges carry a category so the profile can render certifications
        // (the seal) distinctly from achievements.
        $firstChallenge = collect($response->json('data.badges'))->firstWhere('key', 'first_challenge');
        $this->assertSame('achievement', $firstChallenge['category']);
    }

    public function test_payload_never_exposes_private_fields(): void
    {
        $this->user->profile()->update([
            'birth_date' => '1990-01-01',
            'instagram' => 'https://instagram.com/ada',
            'availability_hours' => 10,
        ]);
        $slug = $this->makePublic();

        $data = $this->getJson("/api/public/profile/{$slug}")->json('data');

        $this->assertArrayNotHasKey('email', $data);
        $this->assertArrayNotHasKey('birth_date', $data);
        $this->assertArrayNotHasKey('availability_hours', $data);
        $this->assertArrayNotHasKey('product_interest', $data);
        $this->assertArrayNotHasKey('instagram', $data['links']);
        $this->assertStringNotContainsString($this->user->email, json_encode($data));
    }
}
