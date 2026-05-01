<?php

namespace Tests\Feature\Api;

use App\Enums\PaymentStatus;
use App\Enums\PaymentTier;
use App\Models\Payment;
use App\Models\User;
use App\Services\StripeService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Stripe\Checkout\Session;
use Tests\TestCase;

class CheckoutApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_create_session_requires_authentication(): void
    {
        $this->postJson('/api/checkout/session', [
            'tier' => 'accelerator',
            'currency' => 'eur',
        ])->assertUnauthorized();
    }

    public function test_create_session_validates_tier(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/checkout/session', [
                'tier' => 'invalid',
                'currency' => 'eur',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('tier');
    }

    public function test_create_session_validates_currency(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/checkout/session', [
                'tier' => 'accelerator',
                'currency' => 'usd',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('currency');
    }

    public function test_create_session_returns_url_and_id_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $fakeSession = Session::constructFrom([
            'id' => 'cs_test_abc123',
            'object' => 'checkout.session',
            'url' => 'https://checkout.stripe.com/c/pay/cs_test_abc123',
        ]);

        $this->mock(StripeService::class, function (MockInterface $mock) use ($user, $fakeSession) {
            $mock->shouldReceive('createCheckoutSession')
                ->once()
                ->withArgs(function ($u, $tier, $currency) use ($user) {
                    return $u->is($user)
                        && $tier === PaymentTier::ACCELERATOR
                        && $currency === 'eur';
                })
                ->andReturn($fakeSession);
        });

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/checkout/session', [
                'tier' => 'accelerator',
                'currency' => 'eur',
            ])
            ->assertOk()
            ->assertJson([
                'session_id' => 'cs_test_abc123',
                'url' => 'https://checkout.stripe.com/c/pay/cs_test_abc123',
            ]);
    }

    public function test_status_returns_payment_for_owner(): void
    {
        $user = User::factory()->create();
        Payment::create([
            'user_id' => $user->id,
            'stripe_session_id' => 'cs_test_xyz',
            'tier' => PaymentTier::ACCELERATOR,
            'amount' => 9900,
            'currency' => 'eur',
            'status' => PaymentStatus::PAID,
            'paid_at' => now(),
        ]);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/checkout/cs_test_xyz/status')
            ->assertOk()
            ->assertJson([
                'status' => 'paid',
                'tier' => 'accelerator',
                'amount' => 9900,
                'currency' => 'eur',
            ]);
    }

    public function test_status_returns_404_for_other_users_session(): void
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();

        Payment::create([
            'user_id' => $owner->id,
            'stripe_session_id' => 'cs_test_xyz',
            'tier' => PaymentTier::ACCELERATOR,
            'amount' => 9900,
            'currency' => 'eur',
            'status' => PaymentStatus::PAID,
        ]);

        $this->actingAs($stranger, 'sanctum')
            ->getJson('/api/checkout/cs_test_xyz/status')
            ->assertNotFound();
    }

    public function test_status_returns_404_for_unknown_session(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/checkout/cs_test_nope/status')
            ->assertNotFound();
    }
}
