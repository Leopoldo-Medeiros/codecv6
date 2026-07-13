<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\AuthorizationException;
use App\Http\Controllers\Controller;
use App\Http\Resources\ChallengeResource;
use App\Models\Challenge;
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

    public function index(): AnonymousResourceCollection
    {
        $challenges = Challenge::orderBy('difficulty')->orderBy('title')->get();

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

        $progress = $result['passed']
            ? $gamification->recordChallengeCompletion($request->user(), $challenge)
            : null;

        return response()->json([
            'passed' => $result['passed'],
            'duration' => $result['duration_ms'],
            'failedCount' => collect($result['tests'])->filter(fn ($t) => ! $t['passed'])->count(),
            'tests' => $result['tests'],
            'progress' => $progress,
        ]);
    }

    private function authorizeAccess(Request $request, Challenge $challenge): void
    {
        if (! $this->entitlements->canAccessChallenge($request->user(), $challenge)) {
            throw new AuthorizationException('This challenge is available on Practice Pro. Upgrade to unlock it.');
        }
    }
}
