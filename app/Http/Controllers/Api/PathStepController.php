<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PathStepResource;
use App\Models\Path;
use App\Models\PathStep;
use App\Models\UserStepProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PathStepController extends Controller
{
    /**
     * List steps for a path, annotated with the current user's progress.
     */
    public function index(Path $path): JsonResponse
    {
        $steps = $path->steps()->with('course')->get();

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

    /**
     * Create a new step (admin/consultant only — enforced by route middleware).
     */
    public function store(Request $request, Path $path): JsonResponse
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'course_id'   => 'nullable|exists:courses,id',
            'resources'   => 'nullable|array',
            'resources.*.label' => 'required|string|max:100',
            'resources.*.url'   => 'required|url|max:500',
            'order'       => 'nullable|integer|min:0',
        ]);

        // Default order: append at end
        if (! isset($validated['order'])) {
            $validated['order'] = $path->steps()->max('order') + 1;
        }

        $step = $path->steps()->create($validated);
        $step->load('course');

        return response()->json([
            'message' => 'Step created',
            'data'    => new PathStepResource($step),
        ], 201);
    }

    /**
     * Update a step.
     */
    public function update(Request $request, Path $path, PathStep $step): JsonResponse
    {
        abort_if($step->path_id !== $path->id, 404);

        $validated = $request->validate([
            'title'       => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'course_id'   => 'nullable|exists:courses,id',
            'resources'   => 'nullable|array',
            'resources.*.label' => 'required|string|max:100',
            'resources.*.url'   => 'required|url|max:500',
            'order'       => 'nullable|integer|min:0',
        ]);

        $step->update($validated);
        $step->load('course');

        return response()->json([
            'message' => 'Step updated',
            'data'    => new PathStepResource($step),
        ]);
    }

    /**
     * Delete a step.
     */
    public function destroy(Path $path, PathStep $step): JsonResponse
    {
        abort_if($step->path_id !== $path->id, 404);
        $step->delete();

        return response()->json(['message' => 'Step deleted']);
    }

    /**
     * Reorder steps by passing an ordered array of step IDs.
     */
    public function reorder(Request $request, Path $path): JsonResponse
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'integer|exists:path_steps,id',
        ]);

        foreach ($request->ids as $index => $id) {
            PathStep::where('id', $id)->where('path_id', $path->id)->update(['order' => $index]);
        }

        return response()->json(['message' => 'Steps reordered']);
    }

    /**
     * Update the authenticated user's progress on a step.
     */
    public function updateProgress(Request $request, PathStep $step): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:not_started,in_progress,done',
        ]);

        UserStepProgress::updateOrCreate(
            ['user_id' => Auth::id(), 'path_step_id' => $step->id],
            ['status' => $request->status]
        );

        return response()->json(['message' => 'Progress updated', 'status' => $request->status]);
    }
}
