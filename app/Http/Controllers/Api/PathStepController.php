<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PathStepResource;
use App\Models\Path;
use App\Models\PathStep;
use App\Models\User;
use App\Models\UserStepProgress;
use App\Notifications\ClientPathCompleted;
use App\Notifications\ClientPathHalfway;
use App\Notifications\ClientStartedLearning;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PathStepController extends Controller
{
    public function index(Path $path): JsonResponse
    {
        $steps = $path->steps()->with('course')->orderBy('order')->get();

        $userId = Auth::id();
        $progressMap = UserStepProgress::where('user_id', $userId)
            ->whereIn('path_step_id', $steps->pluck('id'))
            ->pluck('status', 'path_step_id');

        $data = $steps->map(fn (PathStep $step) => array_merge(
            (new PathStepResource($step))->resolve(),
            ['user_status' => $progressMap->get($step->id, 'not_started')]
        ));

        return response()->json(['data' => $data]);
    }

    public function show(PathStep $step): JsonResponse
    {
        $step->load('course');

        $userStatus = UserStepProgress::where('user_id', Auth::id())
            ->where('path_step_id', $step->id)
            ->value('status') ?? 'not_started';

        return response()->json([
            'data' => array_merge(
                (new PathStepResource($step))->resolve(),
                ['user_status' => $userStatus]
            ),
        ]);
    }

    public function store(Request $request, Path $path): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'course_id' => 'nullable|exists:courses,id',
            'resources' => 'nullable|array',
            'resources.*.label' => 'required|string|max:100',
            'resources.*.url' => 'required|url|max:500',
            'order' => 'nullable|integer|min:0',
            'type' => 'nullable|in:reading,lab,challenge,quiz',
            'lab_url' => 'nullable|url|max:1000',
            'instructions' => 'nullable|array',
            'instructions.*.id' => 'required|integer',
            'instructions.*.text' => 'required|string|max:500',
            'challenge_prompt' => 'nullable|string',
        ]);

        if (! isset($validated['order'])) {
            $validated['order'] = $path->steps()->max('order') + 1;
        }

        $step = $path->steps()->create($validated);
        $step->load('course');

        return response()->json([
            'message' => 'Step created',
            'data' => new PathStepResource($step),
        ], 201);
    }

    public function update(Request $request, Path $path, PathStep $step): JsonResponse
    {
        abort_if($step->path_id !== $path->id, 404);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'course_id' => 'nullable|exists:courses,id',
            'resources' => 'nullable|array',
            'resources.*.label' => 'required|string|max:100',
            'resources.*.url' => 'required|url|max:500',
            'order' => 'nullable|integer|min:0',
            'type' => 'nullable|in:reading,lab,challenge,quiz',
            'lab_url' => 'nullable|url|max:1000',
            'instructions' => 'nullable|array',
            'instructions.*.id' => 'required|integer',
            'instructions.*.text' => 'required|string|max:500',
            'challenge_prompt' => 'nullable|string',
        ]);

        $step->update($validated);
        $step->load('course');

        return response()->json([
            'message' => 'Step updated',
            'data' => new PathStepResource($step),
        ]);
    }

    public function destroy(Path $path, PathStep $step): JsonResponse
    {
        abort_if($step->path_id !== $path->id, 404);
        $step->delete();

        return response()->json(['message' => 'Step deleted']);
    }

    public function reorder(Request $request, Path $path): JsonResponse
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:path_steps,id',
        ]);

        foreach ($request->ids as $index => $id) {
            PathStep::where('id', $id)->where('path_id', $path->id)->update(['order' => $index]);
        }

        return response()->json(['message' => 'Steps reordered']);
    }

    public function updateProgress(Request $request, PathStep $step): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:not_started,in_progress,done',
        ]);

        /** @var User $client */
        $client = Auth::user();
        $userId = $client->id;

        if ($request->status === 'in_progress') {
            $precedingIds = PathStep::where('path_id', $step->path_id)
                ->where('order', '<', $step->order)
                ->pluck('id');

            $blocker = UserStepProgress::where('user_id', $userId)
                ->whereIn('path_step_id', $precedingIds)
                ->where('status', 'in_progress')
                ->with('step')
                ->first();

            if ($blocker) {
                return response()->json([
                    'message' => 'Complete the current step first.',
                    'blocking_step' => $blocker->step?->title,
                ], 422);
            }

            $siblingIds = PathStep::where('path_id', $step->path_id)
                ->where('id', '!=', $step->id)
                ->pluck('id');

            UserStepProgress::where('user_id', $userId)
                ->whereIn('path_step_id', $siblingIds)
                ->where('status', 'in_progress')
                ->update(['status' => 'not_started']);
        }

        // Snapshot: any prior progress at all (used for "first step" detection)
        $hadAnyProgress = UserStepProgress::where('user_id', $userId)->exists();

        UserStepProgress::updateOrCreate(
            ['user_id' => $userId, 'path_step_id' => $step->id],
            ['status'  => $request->status]
        );

        $this->dispatchProgressNotifications(
            $client, $step, $request->status, $hadAnyProgress
        );

        return response()->json(['message' => 'Progress updated', 'status' => $request->status]);
    }

    private function dispatchProgressNotifications(
        User     $client,
        PathStep $step,
        string   $newStatus,
        bool     $hadAnyProgress,
    ): void {
        $consultant = $client->consultant;
        if (! $consultant) {
            return;
        }

        $path       = $step->path;
        $allStepIds = PathStep::where('path_id', $step->path_id)->pluck('id');
        $totalSteps = $allStepIds->count();

        if ($totalSteps === 0) {
            return;
        }

        $doneCount = UserStepProgress::where('user_id', $client->id)
            ->whereIn('path_step_id', $allStepIds)
            ->where('status', 'done')
            ->count();

        // 1. First ever step started
        if ($newStatus === 'in_progress' && ! $hadAnyProgress) {
            $consultant->notify(new ClientStartedLearning($client, $path));
            return;
        }

        // 2. Path completed (all steps done)
        if ($newStatus === 'done' && $doneCount === $totalSteps) {
            $consultant->notify(new ClientPathCompleted($client, $path, $totalSteps));
            return;
        }

        // 3. Path halfway (exactly hits the midpoint step)
        $midpoint = (int) ceil($totalSteps / 2);
        if ($newStatus === 'done' && $doneCount === $midpoint && $totalSteps > 1) {
            $consultant->notify(new ClientPathHalfway($client, $path, $doneCount, $totalSteps));
        }
    }
}
