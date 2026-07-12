<?php

namespace App\Services;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * The public skill profile (practice funnel stage 5 / F3): the piece that
 * turns practice history into shareable proof. Opt-in only; the payload is
 * built from an explicit allow-list of fields — anything not named here
 * (email, birth_date, private social links, onboarding answers like
 * availability or product interest) never leaves the API.
 */
class PublicProfileService
{
    public function setVisibility(User $user, bool $isPublic): Profile
    {
        /** @var Profile $profile */
        $profile = $user->profile()->firstOrCreate(['user_id' => $user->id]);

        // The slug is minted once and kept stable across visibility toggles,
        // so a link someone already shared keeps working after re-enabling.
        if ($isPublic && $profile->public_slug === null) {
            $profile->public_slug = $this->generateSlug($user);
        }

        $profile->is_public = $isPublic;
        $profile->save();

        return $profile;
    }

    /**
     * @return array<string, mixed>
     */
    public function buildPayload(Profile $profile): array
    {
        $user = $profile->user;
        $stats = $user->progressStats;

        return [
            'fullname' => $user->fullname,
            'profession' => $profile->profession,
            'level' => $profile->level,
            'stack' => $profile->stack ?? [],
            'goal' => $profile->goal,
            'links' => array_filter([
                'github' => $profile->github,
                'linkedin' => $profile->linkedin,
                'website' => $profile->website,
            ]),
            'member_since' => $user->created_at?->toDateString(),
            'stats' => [
                'xp_points' => $stats->xp_points ?? 0,
                'current_streak' => $stats->current_streak ?? 0,
                'longest_streak' => $stats->longest_streak ?? 0,
            ],
            'badges' => $user->badges()
                ->orderBy('user_badges.earned_at')
                ->get()
                ->map(fn ($badge) => [
                    'key' => $badge->key,
                    'name' => $badge->name,
                    'description' => $badge->description,
                    'icon' => $badge->icon,
                    'earned_at' => $badge->pivot->earned_at,
                ])
                ->values(),
            'completed_challenges' => $user->challengeCompletions()
                ->with('challenge:id,title,difficulty')
                ->orderByDesc('completed_at')
                ->get()
                ->map(fn ($completion) => [
                    'title' => $completion->challenge->title,
                    'difficulty' => $completion->challenge->difficulty->value,
                    'completed_at' => $completion->completed_at->toDateString(),
                ])
                ->values(),
        ];
    }

    private function generateSlug(User $user): string
    {
        do {
            $slug = Str::slug($user->fullname).'-'.Str::lower(Str::random(6));
        } while (Profile::where('public_slug', $slug)->exists());

        return $slug;
    }
}
