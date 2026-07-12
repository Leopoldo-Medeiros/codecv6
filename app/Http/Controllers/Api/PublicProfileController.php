<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Services\PublicProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Both sides of the public skill profile (practice funnel stage 5 / F3):
 * the authenticated owner toggles visibility; anyone with the link reads
 * the allow-listed payload. Like PublicChallengeController, the
 * unauthenticated surface lives in its own small file so its access rule
 * (is_public only) is easy to audit.
 */
class PublicProfileController extends Controller
{
    public function __construct(
        private readonly PublicProfileService $publicProfileService
    ) {}

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate(['is_public' => 'required|boolean']);

        $profile = $this->publicProfileService->setVisibility(
            $request->user(),
            $validated['is_public']
        );

        return response()->json([
            'message' => $profile->is_public
                ? 'Your skill profile is now public.'
                : 'Your skill profile is now private.',
            'is_public' => $profile->is_public,
            'public_slug' => $profile->public_slug,
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $profile = Profile::where('public_slug', $slug)
            ->where('is_public', true)
            ->first();

        abort_if($profile === null, 404);

        return response()->json([
            'data' => $this->publicProfileService->buildPayload($profile),
        ]);
    }
}
