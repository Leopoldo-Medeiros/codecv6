<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TeaserChallengeResource;
use App\Models\Challenge;
use App\Services\ChallengeExecutionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Anonymous, no-account entry point into the practice product (funnel
 * stage 1 / F2): a visitor can run a handful of curated challenges before
 * ever signing up. Deliberately separate from ChallengeController — this
 * is the app's only unauthenticated code-execution surface, so its access
 * rules (is_teaser only) live in one small, easy-to-audit file. No
 * progress, XP, or badges are recorded here; there is no user to attach
 * them to.
 */
class PublicChallengeController extends Controller
{
    public function teaser(): AnonymousResourceCollection
    {
        $challenges = Challenge::where('is_teaser', true)
            ->orderBy('difficulty')
            ->orderBy('title')
            ->get();

        return TeaserChallengeResource::collection($challenges);
    }

    public function run(Request $request, Challenge $challenge, ChallengeExecutionService $executor): JsonResponse
    {
        abort_unless($challenge->is_teaser, 404);

        $request->validate(['code' => ['required', 'string', 'max:65535']]);

        $result = $executor->run($request->input('code'), $challenge->tests_code);

        return response()->json([
            'passed' => $result['passed'],
            'duration' => $result['duration_ms'],
            'failedCount' => collect($result['tests'])->filter(fn ($t) => ! $t['passed'])->count(),
            'tests' => $result['tests'],
        ]);
    }
}
