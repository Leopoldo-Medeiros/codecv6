<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Enums\PaymentTier;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Stripe\Checkout\Session;
use Stripe\Event;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey((string) config('services.stripe.secret'));
    }

    public function createCheckoutSession(User $user, PaymentTier $tier, string $currency): Session
    {
        $currency = strtolower($currency);
        $tierConfig = config("pricing.tiers.{$tier->value}");

        if (! $tierConfig) {
            throw new InvalidArgumentException("Unknown tier: {$tier->value}");
        }

        if (! in_array($currency, config('pricing.supported_currencies', []), true)) {
            throw new InvalidArgumentException("Unsupported currency: {$currency}");
        }

        $amount = $tierConfig['prices'][$currency] ?? null;

        if (! $amount) {
            throw new InvalidArgumentException("No price for {$tier->value} in {$currency}");
        }

        $frontendUrl = rtrim((string) config('app.frontend_url'), '/');
        $isRecurring = (bool) ($tierConfig['recurring'] ?? false);

        $priceData = [
            'currency' => $currency,
            'product_data' => [
                'name' => $tierConfig['name'],
                'description' => $tierConfig['description'] ?? null,
            ],
            'unit_amount' => $amount,
        ];

        if ($isRecurring) {
            $priceData['recurring'] = [
                'interval' => $tierConfig['interval'] ?? 'month',
            ];
        }

        $session = Session::create([
            'mode' => $isRecurring ? 'subscription' : 'payment',
            'customer_email' => $user->email,
            'success_url' => $frontendUrl.'/payment?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $frontendUrl.'/pricing?cancelled=1',
            'line_items' => [[
                'price_data' => $priceData,
                'quantity' => 1,
            ]],
            'metadata' => [
                'user_id' => $user->id,
                'tier' => $tier->value,
            ],
        ]);

        Payment::create([
            'user_id' => $user->id,
            'stripe_session_id' => $session->id,
            'tier' => $tier,
            'amount' => $amount,
            'currency' => $currency,
            'status' => PaymentStatus::PENDING,
            'metadata' => ['mode' => $session->mode],
        ]);

        return $session;
    }

    public function constructEvent(string $payload, string $signature): Event
    {
        return Webhook::constructEvent(
            $payload,
            $signature,
            (string) config('services.stripe.webhook_secret')
        );
    }

    public function handleEvent(Event $event): void
    {
        match ($event->type) {
            'checkout.session.completed',
            'checkout.session.async_payment_succeeded' => $this->markSessionPaid($event->data->object),
            'checkout.session.async_payment_failed' => $this->markSessionFailed($event->data->object),
            default => Log::info("Stripe event ignored: {$event->type}"),
        };
    }

    private function markSessionPaid(Session $session): void
    {
        $payment = Payment::where('stripe_session_id', $session->id)->first();

        if (! $payment) {
            Log::warning("Stripe checkout.session.completed for unknown session: {$session->id}");

            return;
        }

        if ($payment->isPaid()) {
            return;
        }

        $payment->update([
            'stripe_payment_intent_id' => $session->payment_intent,
            'status' => PaymentStatus::PAID,
            'paid_at' => now(),
        ]);
    }

    private function markSessionFailed(Session $session): void
    {
        $payment = Payment::where('stripe_session_id', $session->id)->first();

        if (! $payment) {
            return;
        }

        $payment->update(['status' => PaymentStatus::FAILED]);
    }
}
