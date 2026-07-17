<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WaitlistRequest;
use App\Models\WaitlistEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * "Coming soon" waitlist — the demand-sensing instrument. Anonymous visitors
 * (or logged-in users) register interest in an unbuilt track; the admin view
 * aggregates signups per topic so we build the wedge the market actually wants.
 */
class WaitlistController extends Controller
{
    /**
     * Join a waitlist. Public + idempotent: re-submitting the same email for
     * the same topic updates the row instead of creating a duplicate.
     */
    public function store(WaitlistRequest $request): JsonResponse
    {
        $data = $request->validated();

        // A logged-in vote is always tied to that user's own email — never a
        // client-supplied one — and links the signup back to the user.
        $email = Auth::user()?->email ?? $data['email'];

        WaitlistEntry::updateOrCreate(
            ['email' => $email, 'topic' => $data['topic']],
            ['source' => $data['source'] ?? null, 'user_id' => Auth::id()],
        );

        return response()->json([
            'message' => "You're on the list — we'll email you the moment it's ready.",
        ], 201);
    }

    /**
     * Admin: signup counts per topic (the demand-sensing readout). Every
     * configured topic is present, even at zero, so the picture is complete.
     */
    public function index(): JsonResponse
    {
        $counts = WaitlistEntry::selectRaw('topic, COUNT(*) as signups')
            ->groupBy('topic')
            ->pluck('signups', 'topic');

        $topics = collect(config('waitlist.topics'))->map(fn ($meta, $slug) => [
            'topic' => $slug,
            'title' => $meta['title'],
            'signups' => (int) ($counts[$slug] ?? 0),
        ])->values();

        return response()->json([
            'topics' => $topics,
            'total' => (int) $counts->sum(),
        ]);
    }
}
