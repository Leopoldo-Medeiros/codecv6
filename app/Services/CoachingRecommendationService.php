<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Enums\RoleEnum;
use App\Models\PathStep;
use App\Models\User;
use App\Models\UserStepProgress;

/**
 * Recommends the single most relevant coaching tier for a practicing user
 * (practice funnel F6 — "Get Coached"). This is the bridge from self-serve
 * practice into the high-touch coaching tiers.
 *
 * The philosophy is *earned* nudges: a recommendation only surfaces once the
 * user has proven something (finished a path, built a streak), so it reads as
 * a natural next step rather than an ad. A brand-new user gets nothing — that
 * stage belongs to the gamification widget (F1).
 *
 * The ladder is priority-ordered (config/coaching.php): the first tier the
 * user qualifies for (any single threshold met) and has NOT already purchased
 * wins. Owned tiers are suppressed by reading PAID payments, mirroring how
 * EntitlementService reads entitlements — a one-time tier (accelerator,
 * bootcamp) once bought never nudges again; a recurring tier (mentorship)
 * likewise stops nudging once active.
 */
class CoachingRecommendationService
{
    /**
     * @return array{tier: string, name: string, headline: string, body: string, cta: string, prices: array<string, int>}|null
     *                                                                                                                         null when the user hasn't earned any nudge yet, or already owns every tier they'd qualify for
     */
    public function recommend(User $user): ?array
    {
        // Coaching nudges target clients — the people being coached. Admins and
        // consultants (who always hold practice access via role) are never
        // nudged to buy coaching, even if test activity gives them signals.
        if ($user->hasAnyRole([RoleEnum::ADMIN->value, RoleEnum::CONSULTANT->value])) {
            return null;
        }

        $signals = $this->signals($user);
        $owned = $this->ownedTiers($user);

        foreach (config('coaching.priority', []) as $tier) {
            if (in_array($tier, $owned, true)) {
                continue;
            }

            $nudge = config("coaching.nudges.{$tier}");
            if (! $nudge || ! $this->qualifies($signals, $nudge['thresholds'] ?? [])) {
                continue;
            }

            $pricing = config("pricing.tiers.{$tier}");
            if (! $pricing) {
                continue;
            }

            return [
                'tier' => $tier,
                'name' => $pricing['name'],
                'headline' => $nudge['headline'],
                'body' => $nudge['body'],
                'cta' => $nudge['cta'],
                'prices' => $pricing['prices'],
            ];
        }

        return null;
    }

    /**
     * A tier qualifies when ANY one of its thresholds is met (OR semantics),
     * so a single strong signal is enough to earn the nudge.
     *
     * @param  array{xp: int, longest_streak: int, challenges: int, paths_completed: int}  $signals
     * @param  array<string, int>  $thresholds
     */
    private function qualifies(array $signals, array $thresholds): bool
    {
        foreach ($thresholds as $signal => $min) {
            if (($signals[$signal] ?? 0) >= $min) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array{xp: int, longest_streak: int, challenges: int, paths_completed: int}
     */
    private function signals(User $user): array
    {
        $stats = $user->progressStats;

        return [
            'xp' => $stats->xp_points ?? 0,
            'longest_streak' => $stats->longest_streak ?? 0,
            'challenges' => $user->challengeCompletions()->count(),
            'paths_completed' => $this->pathsCompletedCount($user),
        ];
    }

    /**
     * Number of paths where the user has marked every step 'done'. Compared
     * per-path so a path is only "completed" when done steps == total steps.
     */
    private function pathsCompletedCount(User $user): int
    {
        $doneByPath = UserStepProgress::query()
            ->join('path_steps', 'path_steps.id', '=', 'user_step_progress.path_step_id')
            ->where('user_step_progress.user_id', $user->id)
            ->where('user_step_progress.status', 'done')
            ->selectRaw('path_steps.path_id, count(*) as done_count')
            ->groupBy('path_steps.path_id')
            ->pluck('done_count', 'path_id');

        if ($doneByPath->isEmpty()) {
            return 0;
        }

        $totalByPath = PathStep::query()
            ->whereIn('path_id', $doneByPath->keys())
            ->selectRaw('path_id, count(*) as total')
            ->groupBy('path_id')
            ->pluck('total', 'path_id');

        return $doneByPath->filter(
            fn ($done, $pathId) => (int) $done === (int) ($totalByPath[$pathId] ?? -1)
        )->count();
    }

    /**
     * Coaching tiers the user has already paid for — suppressed from nudges.
     *
     * @return list<string>
     */
    private function ownedTiers(User $user): array
    {
        return $user->payments()
            ->where('status', PaymentStatus::PAID)
            ->pluck('tier')
            ->map(fn ($tier) => $tier instanceof \BackedEnum ? $tier->value : $tier)
            ->unique()
            ->values()
            ->all();
    }
}
