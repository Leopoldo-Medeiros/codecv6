<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChallengeResource;
use App\Models\Challenge;
use App\Services\ChallengeExecutionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ChallengeController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $challenges = Challenge::orderBy('difficulty')->orderBy('title')->get();

        return ChallengeResource::collection($challenges);
    }

    public function show(Challenge $challenge): ChallengeResource
    {
        return new ChallengeResource($challenge);
    }

    public function run(Request $request, Challenge $challenge, ChallengeExecutionService $executor): JsonResponse
    {
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
