<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\Challenge;
use App\Models\Path;
use App\Models\PathStep;
use App\Models\User;
use App\Models\UserChallengeCompletion;
use App\Models\UserProgressStats;
use App\Models\UserStepProgress;

/**
 * Awards XP, tracks daily practice streaks, and unlocks milestone badges.
 * Called from ChallengeController::run (on a passing submission) and
 * PathStepController::updateProgress (on a step's first transition to
 * 'done'), kept out of those controllers so progress tracking there stays
 * focused on its own concern. Practice funnel stage 3 (F1) of the
 * 2026-07-12 funnel design.
 *
 * Idempotency ownership differs by caller on purpose: challenges have no
 * existing "already passed" signal, so this service owns that check itself
 * via UserChallengeCompletion. Steps already have one (UserStepProgress's
 * current status) which the controller inspects before calling in — mirroring
 * how PathStepController already pre-computes $hadAnyProgress for
 * notifications rather than re-deriving it here.
 */
class GamificationService
{
    private const CHALLENGE_XP = [
        'beginner' => 10,
        'intermediate' => 20,
        'advanced' => 35,
        'expert' => 50,
    ];

    private const STEP_XP = [
        'beginner' => 10,
        'intermediate' => 15,
        'advanced' => 25,
    ];

    private const STEP_XP_DEFAULT = 10;

    private const STREAK_BADGE_THRESHOLD = 7;

    /**
     * @return array{xp_awarded: int, xp_points: int, current_streak: int, new_badges: list<array<string, string>>}|null
     *                                                                                                                   null if this challenge was already completed before (no repeat XP)
     */
    public function recordChallengeCompletion(User $user, Challenge $challenge): ?array
    {
        $alreadyCompleted = UserChallengeCompletion::where('user_id', $user->id)
            ->where('challenge_id', $challenge->id)
            ->exists();

        if ($alreadyCompleted) {
            return null;
        }

        UserChallengeCompletion::create([
            'user_id' => $user->id,
            'challenge_id' => $challenge->id,
            'completed_at' => now(),
        ]);

        $xp = self::CHALLENGE_XP[$challenge->difficulty->value] ?? self::STEP_XP_DEFAULT;

        return $this->applyProgress(
            $user,
            $xp,
            fn ($stats) => $this->challengeBadgeChecks($user, $stats)
        );
    }

    /**
     * Caller must only invoke this on a fresh (not-already-'done') status
     * transition — see the class docblock on idempotency ownership.
     *
     * @return array{xp_awarded: int, xp_points: int, current_streak: int, new_badges: list<array<string, string>>}
     */
    public function recordStepCompletion(User $user, PathStep $step): array
    {
        $xp = self::STEP_XP[$step->difficulty ?? ''] ?? self::STEP_XP_DEFAULT;

        return $this->applyProgress(
            $user,
            $xp,
            fn ($stats) => $this->stepBadgeChecks($user, $stats, $step)
        );
    }

    /**
     * @return array{xp_awarded: int, xp_points: int, current_streak: int, new_badges: list<array<string, string>>}
     */
    private function applyProgress(User $user, int $xpAmount, \Closure $badgeCheck): array
    {
        $stats = UserProgressStats::firstOrCreate(['user_id' => $user->id]);

        $stats->xp_points += $xpAmount;
        $this->bumpStreak($stats);
        $stats->save();

        return [
            'xp_awarded' => $xpAmount,
            'xp_points' => $stats->xp_points,
            'current_streak' => $stats->current_streak,
            'new_badges' => $badgeCheck($stats),
        ];
    }

    private function bumpStreak(UserProgressStats $stats): void
    {
        $last = $stats->last_practiced_at;

        if ($last === null || ! $last->isToday()) {
            $stats->current_streak = ($last !== null && $last->isYesterday())
                ? $stats->current_streak + 1
                : 1;
            $stats->longest_streak = max($stats->longest_streak, $stats->current_streak);
        }
        // else: already practiced today — streak was counted once already today

        $stats->last_practiced_at = now();
    }

    /** @return list<array<string, string>> */
    private function challengeBadgeChecks(User $user, UserProgressStats $stats): array
    {
        $newBadges = [];

        $isFirstEver = UserChallengeCompletion::where('user_id', $user->id)->count() === 1;
        if ($isFirstEver && ($badge = $this->awardBadge($user, 'first_challenge'))) {
            $newBadges[] = $badge;
        }

        if ($badge = $this->checkStreakBadge($user, $stats)) {
            $newBadges[] = $badge;
        }

        return $newBadges;
    }

    /** @return list<array<string, string>> */
    private function stepBadgeChecks(User $user, UserProgressStats $stats, PathStep $step): array
    {
        $newBadges = [];

        if ($badge = $this->checkStreakBadge($user, $stats)) {
            $newBadges[] = $badge;
        }

        // First production incident diagnosed (observability track). awardBadge
        // is idempotent, so this fires once — on the first solved incident.
        if ($step->type === 'incident' && ($badge = $this->awardBadge($user, 'incident_solved'))) {
            $newBadges[] = $badge;
        }

        $stepIds = PathStep::where('path_id', $step->path_id)->pluck('id');
        $totalSteps = $stepIds->count();

        if ($totalSteps > 0) {
            $doneSteps = UserStepProgress::where('user_id', $user->id)
                ->whereIn('path_step_id', $stepIds)
                ->where('status', 'done')
                ->count();

            if ($doneSteps === $totalSteps) {
                if ($badge = $this->awardBadge($user, 'path_completed')) {
                    $newBadges[] = $badge;
                }

                // A path may confer a certification seal on full completion
                // (e.g. the Observability track). Award it on top of the
                // generic path_completed milestone.
                $badgeKey = Path::where('id', $step->path_id)->value('badge_key');
                if ($badgeKey && ($badge = $this->awardBadge($user, $badgeKey))) {
                    $newBadges[] = $badge;
                }
            }
        }

        return $newBadges;
    }

    private function checkStreakBadge(User $user, UserProgressStats $stats): ?array
    {
        if ($stats->current_streak < self::STREAK_BADGE_THRESHOLD) {
            return null;
        }

        return $this->awardBadge($user, 'streak_7');
    }

    /** @return array<string, string>|null */
    private function awardBadge(User $user, string $key): ?array
    {
        $badge = Badge::where('key', $key)->first();

        // Badge not seeded — fail soft, a missing badge row must never
        // break the challenge/step response it's piggybacking on.
        if (! $badge) {
            return null;
        }

        $alreadyHas = $user->badges()->where('badges.id', $badge->id)->exists();
        if ($alreadyHas) {
            return null;
        }

        $user->badges()->attach($badge->id, ['earned_at' => now()]);

        return [
            'key' => $badge->key,
            'category' => $badge->category,
            'name' => $badge->name,
            'description' => $badge->description,
            'icon' => $badge->icon,
        ];
    }
}
