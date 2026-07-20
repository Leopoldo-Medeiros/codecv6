<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\AuthorizationException;
use App\Http\Controllers\Controller;
use App\Http\Resources\ChallengeResource;
use App\Models\Challenge;
use App\Models\ChallengeSubmission;
use App\Models\UserChallengeCompletion;
use App\Services\ChallengeExecutionService;
use App\Services\EntitlementService;
use App\Services\GamificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ChallengeController extends Controller
{
    public function __construct(
        private readonly EntitlementService $entitlements
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $challenges = Challenge::orderBy('difficulty')->orderBy('title')->get();

        // One query for the user's completions; each model carries a transient
        // `solved` attribute for the resource (no per-row lookups).
        $solvedIds = UserChallengeCompletion::where('user_id', $request->user()->id)
            ->pluck('challenge_id')
            ->flip();
        $challenges->each(fn (Challenge $c) => $c->setAttribute('solved', $solvedIds->has($c->id)));

        // The resource reads the current user to compute `locked` and to
        // blank the code fields of locked challenges (see ChallengeResource).
        return ChallengeResource::collection($challenges);
    }

    public function show(Request $request, Challenge $challenge): ChallengeResource
    {
        $this->authorizeAccess($request, $challenge);

        return new ChallengeResource($challenge);
    }

    public function run(
        Request $request,
        Challenge $challenge,
        ChallengeExecutionService $executor,
        GamificationService $gamification
    ): JsonResponse {
        $this->authorizeAccess($request, $challenge);

        $request->validate(['code' => ['required', 'string', 'max:65535']]);

        $result = $executor->run($request->input('code'), $challenge->tests_code);

        $failedCount = collect($result['tests'])->filter(fn ($t) => ! $t['passed'])->count();

        $this->recordSubmission($request->user()->id, $challenge->id, [
            'code' => $request->input('code'),
            'passed' => $result['passed'],
            'failed_count' => $failedCount,
            'duration_ms' => (int) $result['duration_ms'],
        ]);

        $progress = $result['passed']
            ? $gamification->recordChallengeCompletion($request->user(), $challenge)
            : null;

        return response()->json([
            'passed' => $result['passed'],
            'duration' => $result['duration_ms'],
            'failedCount' => $failedCount,
            'tests' => $result['tests'],
            'progress' => $progress,
        ]);
    }

    /**
     * The user's own iteration history for a challenge, newest first.
     * Only ever returns the caller's submissions — no entitlement gate:
     * this is their code, not gated content.
     */
    public function submissions(Request $request, Challenge $challenge): JsonResponse
    {
        $submissions = ChallengeSubmission::where('user_id', $request->user()->id)
            ->where('challenge_id', $challenge->id)
            ->orderByDesc('id')
            ->get(['id', 'code', 'passed', 'failed_count', 'duration_ms', 'created_at']);

        return response()->json(['data' => $submissions]);
    }

    /**
     * Every run is an iteration; keep only the most recent per (user,
     * challenge) so 64KB code blobs can't grow the table unbounded.
     */
    private const MAX_SUBMISSIONS_KEPT = 20;

    private function recordSubmission(int $userId, int $challengeId, array $attributes): void
    {
        ChallengeSubmission::create([
            'user_id' => $userId,
            'challenge_id' => $challengeId,
            ...$attributes,
        ]);

        $stale = ChallengeSubmission::where('user_id', $userId)
            ->where('challenge_id', $challengeId)
            ->orderByDesc('id')
            ->skip(self::MAX_SUBMISSIONS_KEPT)
            ->take(self::MAX_SUBMISSIONS_KEPT)
            ->pluck('id');

        if ($stale->isNotEmpty()) {
            ChallengeSubmission::whereIn('id', $stale)->delete();
        }
    }

    private function authorizeAccess(Request $request, Challenge $challenge): void
    {
        if (! $this->entitlements->canAccessChallenge($request->user(), $challenge)) {
            throw new AuthorizationException('This challenge is available on Practice Pro. Upgrade to unlock it.');
        }
    }
}
