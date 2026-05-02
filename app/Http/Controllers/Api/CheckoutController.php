<?php

namespace App\Http\Controllers\Api;

use App\Enums\PaymentTier;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\StripeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Stripe\Exception\ApiErrorException;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly StripeService $stripe
    ) {}

    public function createSession(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tier' => 'required|string|in:'.implode(',', PaymentTier::values()),
            'currency' => 'required|string|in:'.implode(',', config('pricing.supported_currencies', [])),
        ]);

        try {
            $session = $this->stripe->createCheckoutSession(
                $request->user(),
                PaymentTier::from($validated['tier']),
                $validated['currency'],
            );

            return response()->json([
                'session_id' => $session->id,
                'url' => $session->url,
            ]);
        } catch (ApiErrorException $e) {
            return response()->json([
                'message' => 'Stripe error: '.$e->getMessage(),
            ], 502);
        }
    }

    public function status(Request $request, string $sessionId): JsonResponse
    {
        $payment = Payment::where('stripe_session_id', $sessionId)
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        return response()->json([
            'status' => $payment->status->value,
            'tier' => $payment->tier->value,
            'amount' => $payment->amount,
            'currency' => $payment->currency,
            'paid_at' => $payment->paid_at,
        ]);
    }
}
