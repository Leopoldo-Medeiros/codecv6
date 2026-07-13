<?php

namespace App\Services;

use App\Enums\ChallengeDifficulty;
use App\Enums\PaymentStatus;
use App\Enums\PaymentTier;
use App\Enums\RoleEnum;
use App\Models\Challenge;
use App\Models\PathStep;
use App\Models\User;

/**
 * Single source of truth for what practice content a user may access
 * (practice funnel F4 gate). Two questions live here so controllers and
 * resources never re-derive the rule:
 *   1. Is this content free (available to everyone, no subscription)?
 *   2. Does this user hold practice access (via role or a paid tier)?
 *
 * Free-tier rule (from the approved funnel design — "a fixed slice"):
 *   - challenges: teasers and every beginner-difficulty challenge
 *   - path steps: the first two steps (order 0 and 1) of any path
 *
 * Access-granting tiers: PRACTICE, BOOTCAMP, MENTORSHIP (the higher tiers
 * already bundle practice materials); ACCELERATOR (CV-only) does not.
 * Admins and consultants always have access.
 *
 * KNOWN LIMITATION: a subscription is a single PAID Payment row; the
 * webhook does not yet handle subscription cancellation/expiry, so access
 * here means "has ever paid for a practice-granting tier", not "has an
 * active subscription this month". Lapse handling is a separate follow-up
 * (needs customer.subscription.deleted etc. from Stripe).
 */
class EntitlementService
{
    private const ACCESS_TIERS = [
        PaymentTier::PRACTICE,
        PaymentTier::BOOTCAMP,
        PaymentTier::MENTORSHIP,
    ];

    private const FREE_STEP_COUNT = 2;

    public function hasPracticeAccess(User $user): bool
    {
        if ($user->hasAnyRole([RoleEnum::ADMIN->value, RoleEnum::CONSULTANT->value])) {
            return true;
        }

        return $user->payments()
            ->where('status', PaymentStatus::PAID)
            ->whereIn('tier', array_map(fn (PaymentTier $t) => $t->value, self::ACCESS_TIERS))
            ->exists();
    }

    public function challengeIsFree(Challenge $challenge): bool
    {
        return $challenge->is_teaser
            || $challenge->difficulty === ChallengeDifficulty::Beginner;
    }

    public function stepIsFree(PathStep $step): bool
    {
        return $step->order < self::FREE_STEP_COUNT;
    }

    public function canAccessChallenge(User $user, Challenge $challenge): bool
    {
        return $this->challengeIsFree($challenge) || $this->hasPracticeAccess($user);
    }

    public function canAccessStep(User $user, PathStep $step): bool
    {
        return $this->stepIsFree($step) || $this->hasPracticeAccess($user);
    }
}
