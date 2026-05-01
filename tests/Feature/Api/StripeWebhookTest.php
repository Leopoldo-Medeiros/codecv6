<?php

namespace Tests\Feature\Api;

use App\Enums\PaymentStatus;
use App\Enums\PaymentTier;
use App\Models\Payment;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class StripeWebhookTest extends TestCase
{
    use RefreshDatabase;

    private const WEBHOOK_SECRET = 'whsec_test_secret';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        config(['services.stripe.webhook_secret' => self::WEBHOOK_SECRET]);
    }

    public function test_webhook_rejects_request_with_invalid_signature(): void
    {
        $event = $this->checkoutCompletedEvent('cs_test_bad_sig');

        $response = $this->postWebhook($event, signatureOverride: 't=1,v1=deadbeef');

        $response->assertStatus(400)->assertJson(['message' => 'Invalid signature']);
    }

    public function test_webhook_rejects_request_with_invalid_payload(): void
    {
        $response = $this->postWebhook([], rawPayloadOverride: 'not-json{');

        $response->assertStatus(400)->assertJson(['message' => 'Invalid payload']);
    }

    public function test_checkout_session_completed_marks_payment_paid(): void
    {
        $user = User::factory()->create();
        $payment = $this->makePendingPayment($user, 'cs_test_complete');

        $this->postWebhook($this->checkoutCompletedEvent('cs_test_complete', 'pi_test_complete'))
            ->assertOk()
            ->assertJson(['received' => true]);

        $payment->refresh();
        $this->assertSame(PaymentStatus::PAID, $payment->status);
        $this->assertSame('pi_test_complete', $payment->stripe_payment_intent_id);
        $this->assertNotNull($payment->paid_at);
    }

    public function test_async_payment_succeeded_marks_payment_paid(): void
    {
        $user = User::factory()->create();
        $payment = $this->makePendingPayment($user, 'cs_test_async_ok');

        $event = $this->checkoutCompletedEvent('cs_test_async_ok', 'pi_test_async');
        $event['type'] = 'checkout.session.async_payment_succeeded';
        $event['id'] = 'evt_test_async_ok';

        $this->postWebhook($event)->assertOk();

        $payment->refresh();
        $this->assertSame(PaymentStatus::PAID, $payment->status);
        $this->assertSame('pi_test_async', $payment->stripe_payment_intent_id);
    }

    public function test_async_payment_failed_marks_payment_failed(): void
    {
        $user = User::factory()->create();
        $payment = $this->makePendingPayment($user, 'cs_test_async_fail');

        $event = [
            'id' => 'evt_test_async_fail',
            'object' => 'event',
            'type' => 'checkout.session.async_payment_failed',
            'data' => [
                'object' => [
                    'id' => 'cs_test_async_fail',
                    'object' => 'checkout.session',
                    'payment_intent' => 'pi_test_failed',
                ],
            ],
        ];

        $this->postWebhook($event)->assertOk();

        $payment->refresh();
        $this->assertSame(PaymentStatus::FAILED, $payment->status);
        $this->assertNull($payment->paid_at);
    }

    public function test_duplicate_completed_event_is_idempotent(): void
    {
        $user = User::factory()->create();
        $payment = $this->makePendingPayment($user, 'cs_test_dup');

        $this->postWebhook($this->checkoutCompletedEvent('cs_test_dup', 'pi_first'))->assertOk();
        $payment->refresh();
        $firstPaidAt = $payment->paid_at;
        $this->assertSame(PaymentStatus::PAID, $payment->status);
        $this->assertSame('pi_first', $payment->stripe_payment_intent_id);

        $this->travel(5)->seconds();
        $this->postWebhook($this->checkoutCompletedEvent('cs_test_dup', 'pi_second_attempt'))->assertOk();

        $payment->refresh();
        $this->assertSame(PaymentStatus::PAID, $payment->status);
        $this->assertSame('pi_first', $payment->stripe_payment_intent_id);
        $this->assertEquals($firstPaidAt->timestamp, $payment->paid_at->timestamp);
    }

    public function test_event_for_unknown_session_returns_ok_without_creating_payment(): void
    {
        $event = $this->checkoutCompletedEvent('cs_test_orphan');

        $this->postWebhook($event)->assertOk()->assertJson(['received' => true]);

        $this->assertSame(0, Payment::count());
    }

    public function test_unhandled_event_type_is_ignored(): void
    {
        $event = [
            'id' => 'evt_test_other',
            'object' => 'event',
            'type' => 'customer.created',
            'data' => ['object' => ['id' => 'cus_123']],
        ];

        $this->postWebhook($event)->assertOk()->assertJson(['received' => true]);
    }

    private function postWebhook(
        array $event,
        ?string $signatureOverride = null,
        ?string $rawPayloadOverride = null,
    ): TestResponse {
        $payload = $rawPayloadOverride ?? json_encode($event);
        $timestamp = time();
        $signature = $signatureOverride
            ?? 't='.$timestamp.',v1='.hash_hmac('sha256', "{$timestamp}.{$payload}", self::WEBHOOK_SECRET);

        return $this->call(
            method: 'POST',
            uri: '/api/webhooks/stripe',
            server: [
                'HTTP_STRIPE_SIGNATURE' => $signature,
                'CONTENT_TYPE' => 'application/json',
            ],
            content: $payload,
        );
    }

    private function makePendingPayment(User $user, string $sessionId): Payment
    {
        return Payment::create([
            'user_id' => $user->id,
            'stripe_session_id' => $sessionId,
            'tier' => PaymentTier::ACCELERATOR,
            'amount' => 9900,
            'currency' => 'eur',
            'status' => PaymentStatus::PENDING,
        ]);
    }

    private function checkoutCompletedEvent(string $sessionId, string $paymentIntentId = 'pi_test_123'): array
    {
        return [
            'id' => 'evt_test_completed',
            'object' => 'event',
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => $sessionId,
                    'object' => 'checkout.session',
                    'payment_intent' => $paymentIntentId,
                ],
            ],
        ];
    }
}
