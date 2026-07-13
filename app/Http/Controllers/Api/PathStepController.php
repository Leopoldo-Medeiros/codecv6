<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\AuthorizationException;
use App\Http\Controllers\Controller;
use App\Http\Resources\PathStepResource;
use App\Models\Path;
use App\Models\PathStep;
use App\Models\User;
use App\Models\UserStepProgress;
use App\Notifications\ClientPathCompleted;
use App\Notifications\ClientPathHalfway;
use App\Notifications\ClientStartedLearning;
use App\Services\Concerns\EnsuresResourceOwnership;
use App\Services\EntitlementService;
use App\Services\GamificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PathStepController extends Controller
{
    use EnsuresResourceOwnership;

    public function index(Path $path, EntitlementService $entitlements): JsonResponse
    {
        $steps = $path->steps()->with('course')->orderBy('order')->get();

        $user = Auth::user();
        $progressMap = UserStepProgress::where('user_id', $user->id)
            ->whereIn('path_step_id', $steps->pluck('id'))
            ->pluck('status', 'path_step_id');

        // Compute practice access once, not per step (it queries payments).
        $hasAccess = $entitlements->hasPracticeAccess($user);

        $data = $steps->map(fn (PathStep $step) => array_merge(
            (new PathStepResource($step))->resolve(),
            [
                'user_status' => $progressMap->get($step->id, 'not_started'),
                'locked' => ! $hasAccess && ! $entitlements->stepIsFree($step),
            ]
        ));

        return response()->json(['data' => $data]);
    }

    public function show(PathStep $step, EntitlementService $entitlements): JsonResponse
    {
        if (! $entitlements->canAccessStep(Auth::user(), $step)) {
            throw new AuthorizationException('This step is available on Practice Pro. Upgrade to unlock it.');
        }

        $step->load('course', 'challenge');

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

    /**
     * Grade a quiz submission server-side (practice funnel F5). The answer
     * key never leaves the API, so grading can't be gamed from the client.
     * Gated by the same F4 entitlement check as viewing the step.
     *
     * Also grades `incident` steps (observability track, Phase A): an incident
     * carries its diagnostic questions in the same `quiz` column, so the whole
     * grading path is reused — only the evidence (rendered client-side) differs.
     */
    public function submitQuiz(Request $request, PathStep $step, EntitlementService $entitlements): JsonResponse
    {
        if (! $entitlements->canAccessStep(Auth::user(), $step)) {
            throw new AuthorizationException('This content is available on Practice Pro. Upgrade to unlock it.');
        }

        abort_unless(
            in_array($step->type, ['quiz', 'incident'], true) && is_array($step->quiz) && $step->quiz !== [],
            404
        );

        $request->validate([
            // present (not required): submitting with nothing answered is valid.
            'answers' => 'present|array',
            'answers.*' => 'nullable|integer|min:0',
        ]);

        $answers = $request->input('answers');
        $results = [];
        $correct = 0;

        foreach ($step->quiz as $question) {
            $given = $answers[$question['id']] ?? null;
            $isCorrect = $given !== null && (int) $given === (int) $question['correct_index'];
            if ($isCorrect) {
                $correct++;
            }

            $results[] = [
                'id' => $question['id'],
                'correct' => $isCorrect,
                'correct_index' => $question['correct_index'],
                'explanation' => $question['explanation'] ?? null,
            ];
        }

        $total = count($step->quiz);

        return response()->json([
            'score' => $correct,
            'total' => $total,
            'passed' => $total > 0 && $correct === $total,
            'results' => $results,
        ]);
    }

    public function store(Request $request, Path $path): JsonResponse
    {
        $this->ensureOwnerOrAdmin($path->consultant_id, 'path');

        $validated = $request->validate($this->stepRules());

        if (! isset($validated['order'])) {
            $validated['order'] = $path->steps()->max('order') + 1;
        }

        $step = $path->steps()->create($validated);
        $step->load('course', 'challenge');

        return response()->json([
            'message' => 'Step created',
            'data' => new PathStepResource($step),
        ], 201);
    }

    public function update(Request $request, Path $path, PathStep $step): JsonResponse
    {
        abort_if($step->path_id !== $path->id, 404);
        $this->ensureOwnerOrAdmin($path->consultant_id, 'path');

        $validated = $request->validate($this->stepRules(forUpdate: true));

        $step->update($validated);
        $step->load('course', 'challenge');

        return response()->json([
            'message' => 'Step updated',
            'data' => new PathStepResource($step),
        ]);
    }

    private function stepRules(bool $forUpdate = false): array
    {
        $titleRule = $forUpdate ? 'sometimes|required|string|max:255' : 'required|string|max:255';

        return [
            'title' => $titleRule,
            'description' => 'nullable|string',
            'tldr' => 'nullable|string|max:500',
            'concept_content' => 'nullable|string',
            'estimated_minutes' => 'nullable|integer|min:1|max:600',
            'difficulty' => 'nullable|in:beginner,intermediate,advanced',
            'prerequisites' => 'nullable|array',
            'prerequisites.*.id' => 'required|integer',
            'prerequisites.*.title' => 'required|string|max:255',
            'concepts' => 'nullable|array',
            'concepts.*' => 'required|string|max:64',
            'has_playground' => 'nullable|boolean',
            'playground_starter_code' => 'nullable|string|max:10000',
            'course_id' => 'nullable|exists:courses,id',
            'resources' => 'nullable|array',
            'resources.*.label' => 'required|string|max:100',
            'resources.*.url' => 'required|url|max:500',
            'order' => 'nullable|integer|min:0',
            'type' => 'nullable|in:reading,lab,challenge,quiz,incident',
            'lab_url' => 'nullable|url|max:1000',
            'instructions' => 'nullable|array',
            'instructions.*.id' => 'required|integer',
            'instructions.*.text' => 'required|string|max:2000',
            'instructions.*.starter_code' => 'nullable|string|max:10000',
            'challenge_prompt' => 'nullable|string',
            'challenge_slug' => 'nullable|string|exists:challenges,slug',
            'quiz' => 'nullable|array|max:50',
            'quiz.*.id' => 'required|integer',
            'quiz.*.question' => 'required|string|max:1000',
            'quiz.*.options' => 'required|array|min:2|max:6',
            'quiz.*.options.*' => 'required|string|max:500',
            'quiz.*.correct_index' => 'required|integer|min:0',
            'quiz.*.explanation' => 'nullable|string|max:1000',
            // Incident evidence (observability track, Phase A) — display-only
            // telemetry. Validated loosely: authored server-side, not by end users.
            'evidence' => 'nullable|array',
            'evidence.scenario' => 'nullable|string|max:2000',
            'evidence.trace' => 'nullable|array',
            'evidence.trace.root' => 'nullable|string|max:255',
            'evidence.trace.spans' => 'nullable|array|max:200',
            'evidence.trace.spans.*.id' => 'required_with:evidence.trace.spans|string|max:64',
            'evidence.trace.spans.*.parent' => 'nullable|string|max:64',
            'evidence.trace.spans.*.name' => 'required_with:evidence.trace.spans|string|max:255',
            'evidence.trace.spans.*.service' => 'nullable|string|max:64',
            'evidence.trace.spans.*.start' => 'nullable|numeric',
            'evidence.trace.spans.*.dur' => 'nullable|numeric',
            'evidence.trace.spans.*.kind' => 'nullable|string|max:32',
            'evidence.trace.spans.*.repeat' => 'nullable|integer|min:1|max:100000',
            'evidence.metrics' => 'nullable|array|max:10',
            'evidence.metrics.*.title' => 'required_with:evidence.metrics|string|max:120',
            'evidence.metrics.*.unit' => 'nullable|string|max:20',
            'evidence.metrics.*.series' => 'required_with:evidence.metrics|array',
            'evidence.metrics.*.threshold' => 'nullable|numeric',
            'evidence.metrics.*.annotations' => 'nullable|array|max:10',
            'evidence.metrics.*.annotations.*.x' => 'required_with:evidence.metrics.*.annotations|numeric',
            'evidence.metrics.*.annotations.*.label' => 'nullable|string|max:60',
            'evidence.logs' => 'nullable|array|max:100',
            'evidence.logs.*.t' => 'nullable|string|max:32',
            'evidence.logs.*.level' => 'nullable|string|max:16',
            'evidence.logs.*.request_id' => 'nullable|string|max:64',
            'evidence.logs.*.msg' => 'required_with:evidence.logs|string|max:500',
        ];
    }

    public function destroy(Path $path, PathStep $step): JsonResponse
    {
        abort_if($step->path_id !== $path->id, 404);
        $this->ensureOwnerOrAdmin($path->consultant_id, 'path');

        $step->delete();

        return response()->json(['message' => 'Step deleted']);
    }

    public function reorder(Request $request, Path $path): JsonResponse
    {
        $this->ensureOwnerOrAdmin($path->consultant_id, 'path');

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:path_steps,id',
        ]);

        foreach ($request->ids as $index => $id) {
            PathStep::where('id', $id)->where('path_id', $path->id)->update(['order' => $index]);
        }

        return response()->json(['message' => 'Steps reordered']);
    }

    public function updateProgress(Request $request, PathStep $step, GamificationService $gamification): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:not_started,in_progress,done',
        ]);

        /** @var User $client */
        $client = Auth::user();
        $userId = $client->id;

        $previousStatus = UserStepProgress::where('user_id', $userId)
            ->where('path_step_id', $step->id)
            ->value('status');

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
            ['status' => $request->status]
        );

        $this->dispatchProgressNotifications(
            $client, $step, $request->status, $hadAnyProgress
        );

        $freshlyDone = $request->status === 'done' && $previousStatus !== 'done';

        $progress = $freshlyDone
            ? $gamification->recordStepCompletion($client, $step)
            : null;

        return response()->json([
            'message' => 'Progress updated',
            'status' => $request->status,
            'progress' => $progress,
            // A per-completion signal for the client (F6 coaching celebration).
            // Unlike the one-time `path_completed` badge, this is true every
            // time an action finishes a path in full, so the nudge can fire on
            // each path — not just the user's first one.
            'path_completed' => $freshlyDone && $this->pathIsFullyDone($client->id, $step->path_id),
        ]);
    }

    /** Whether the user has now marked every step of the path 'done'. */
    private function pathIsFullyDone(int $userId, int $pathId): bool
    {
        $stepIds = PathStep::where('path_id', $pathId)->pluck('id');

        if ($stepIds->isEmpty()) {
            return false;
        }

        $doneCount = UserStepProgress::where('user_id', $userId)
            ->whereIn('path_step_id', $stepIds)
            ->where('status', 'done')
            ->count();

        return $doneCount === $stepIds->count();
    }

    private function dispatchProgressNotifications(
        User $client,
        PathStep $step,
        string $newStatus,
        bool $hadAnyProgress,
    ): void {
        $consultant = $client->consultant;
        if (! $consultant) {
            return;
        }

        $path = $step->path;
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
