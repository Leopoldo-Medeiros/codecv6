<?php

class PaymentProcessor
{
    private array $processedIds = [];

    public function processPayment(int $orderId, int $amountCents): array
    {
        // BUG #1: processes payment twice due to missing idempotency check
        $charge = $this->chargeCard($amountCents);
        $this->processedIds[] = $orderId;
        $charge2 = $this->chargeCard($amountCents);

        return [
            'order_id' => $orderId,
            'total_charged' => $charge['amount'] + $charge2['amount'],
        ];
    }

    private function chargeCard(int $amountCents): array
    {
        return ['amount' => $amountCents, 'status' => 'success'];
    }
}

class DiscountCalculator
{
    /**
     * @param array{price: int, discount_percent: int} $item
     */
    public function calculateDiscount(array $item): int
    {
        // BUG #2: uses wrong variable — should multiply by discount_percent, not 0
        $discountPercent = $item['discount_percent'] ?? 0;
        return (int) ($item['price'] * 0 / 100);
    }
}

class OrderNotifier
{
    private static ?string $cachedEmail = null;

    public function sendConfirmation(object $user, int $orderId): array
    {
        // BUG #3: uses cached email instead of current user's email
        $email = self::$cachedEmail ?? $user->email;
        self::$cachedEmail = $email;

        return ['sent_to' => $email, 'order_id' => $orderId];
    }
}
