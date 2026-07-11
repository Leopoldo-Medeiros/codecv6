<?php

namespace Tests\Feature\Api;

use App\Enums\PaymentTier;
use App\Models\Challenge;
use App\Models\User;
use App\Services\StripeService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Mockery\MockInterface;
use Stripe\Checkout\Session;
use Tests\TestCase;

/**
 * Regression coverage for Phase 0 step 0.4 (docs/architecture-review.md):
 * the expensive endpoints (Judge0 sandbox runs, Gemini analysis calls,
 * Stripe Checkout session creation) must throttle per-user so a single
 * account can't exhaust the upstream quota or the API workers.
 */
class RateLimitingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_challenge_run_is_throttled_after_20_per_minute(): void
    {
        Http::fake(['*' => Http::response(['status' => ['id' => 6], 'message' => 'fake'], 500)]);

        $user = User::factory()->create();
        $user->assignRole('client');

        $challenge = Challenge::create([
            'title' => 'Throttle Test Challenge',
            'slug' => 'throttle-test-challenge',
            'description' => 'Used only to exercise the run endpoint throttle.',
            'boilerplate_code' => '<?php',
            'tests_code' => '<?php',
            'created_by' => $user->id,
        ]);

        $this->actingAs($user, 'sanctum');

        for ($i = 0; $i < 20; $i++) {
            $this->postJson("/api/challenges/{$challenge->slug}/run", ['code' => '<?php'])
                ->assertOk();
        }

        $this->postJson("/api/challenges/{$challenge->slug}/run", ['code' => '<?php'])
            ->assertStatus(429);
    }

    public function test_cv_analyze_is_throttled_after_5_per_minute(): void
    {
        $user = User::factory()->create();
        $user->assignRole('client');
        $this->actingAs($user, 'sanctum');

        // No file is attached, so every request fails validation (422) well
        // before reaching the controller's Gemini-key check — but that's all
        // this test needs: it stays under the throttle limit until the 6th.
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/cv/analyze', ['job_description' => str_repeat('a', 60)])
                ->assertStatus(422);
        }

        $this->postJson('/api/cv/analyze', ['job_description' => str_repeat('a', 60)])
            ->assertStatus(429);
    }

    public function test_linkedin_analyze_is_throttled_after_5_per_minute(): void
    {
        $user = User::factory()->create();
        $user->assignRole('client');
        $this->actingAs($user, 'sanctum');

        // No file is attached, so every request fails validation (422) well
        // before reaching the controller's Gemini-key check — but that's all
        // this test needs: it stays under the throttle limit until the 6th.
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/linkedin/analyze', ['target_role' => 'Backend Engineer'])
                ->assertStatus(422);
        }

        $this->postJson('/api/linkedin/analyze', ['target_role' => 'Backend Engineer'])
            ->assertStatus(429);
    }

    public function test_checkout_session_is_throttled_after_10_per_minute(): void
    {
        $user = User::factory()->create();
        $user->assignRole('client');

        $fakeSession = Session::constructFrom([
            'id' => 'cs_test_throttle',
            'object' => 'checkout.session',
            'url' => 'https://checkout.stripe.com/c/pay/cs_test_throttle',
        ]);

        $this->mock(StripeService::class, function (MockInterface $mock) use ($fakeSession) {
            $mock->shouldReceive('createCheckoutSession')
                ->times(10)
                ->andReturn($fakeSession);
        });

        $this->actingAs($user, 'sanctum');

        for ($i = 0; $i < 10; $i++) {
            $this->postJson('/api/checkout/session', [
                'tier' => PaymentTier::ACCELERATOR->value,
                'currency' => 'eur',
            ])->assertOk();
        }

        $this->postJson('/api/checkout/session', [
            'tier' => PaymentTier::ACCELERATOR->value,
            'currency' => 'eur',
        ])->assertStatus(429);
    }
}
