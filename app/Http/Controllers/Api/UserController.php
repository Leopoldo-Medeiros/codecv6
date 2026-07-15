<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\AuthorizationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\Path;
use App\Models\User;
use App\Models\UserStepProgress;
use App\Notifications\ClientAssigned;
use App\Notifications\NewClientOnboarded;
use App\Notifications\PathAssigned;
use App\Services\CoachingRecommendationService;
use App\Services\PlanService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService,
        private readonly PlanService $planService,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $users = $this->userService->paginate(
            $request->input('search'),
            $request->input('per_page', 10)
        );

        return UserResource::collection($users);
    }

    public function show(User $user): UserResource|JsonResponse
    {
        if (Auth::id() !== $user->id && ! Auth::user()->hasRole('admin')) {
            return response()->json(['message' => 'You are not authorized to view this user.'], 403);
        }

        $user = $this->userService->find($user->id);

        return new UserResource($user);
    }

    public function store(UserRequest $request): JsonResponse
    {
        try {
            $user = $this->userService->create($request->validated());

            return response()->json([
                'message' => 'User created successfully',
                'user' => new UserResource($user),
            ], 201);
        } catch (AuthorizationException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }
    }

    public function update(UserRequest $request, User $user): JsonResponse
    {
        if (Auth::id() !== $user->id && ! Auth::user()->hasRole('admin')) {
            return response()->json(['message' => 'You are not authorized to update this user.'], 403);
        }

        try {
            $user = $this->userService->update($user, $request->validated());

            return response()->json([
                'message' => 'User updated successfully',
                'user' => new UserResource($user),
            ]);
        } catch (AuthorizationException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }
    }

    public function destroy(User $user): JsonResponse
    {
        try {
            $this->userService->delete($user);

            return response()->json(['message' => 'User deleted successfully']);
        } catch (AuthorizationException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }
    }

    /**
     * GDPR Art. 20 — Export all personal data for this user.
     */
    public function exportData(User $user): JsonResponse
    {
        if (Auth::id() !== $user->id && ! Auth::user()->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        return response()->json($this->userService->exportPersonalData($user));
    }

    /**
     * GDPR Art. 17 — Permanently erase all personal data (admin only).
     */
    public function eraseData(User $user): JsonResponse
    {
        if (! Auth::user()->hasRole('admin')) {
            return response()->json(['message' => 'Only admins can permanently erase user data.'], 403);
        }

        try {
            $this->userService->erasePersonalData($user);

            return response()->json(['message' => 'User data permanently erased.']);
        } catch (AuthorizationException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }
    }

    public function updateAvatar(Request $request, User $user): JsonResponse
    {
        if (Auth::id() !== $user->id && ! Auth::user()->hasRole('admin')) {
            return response()->json(['message' => 'You are not authorized to update this user\'s avatar.'], 403);
        }

        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $path = $this->userService->updateAvatar($user, $request->file('avatar'));

        return response()->json([
            'message' => 'Avatar updated successfully',
            'path' => $path,
            'url' => asset('storage/'.$path),
        ]);
    }

    public function consultants(): AnonymousResourceCollection
    {
        $consultants = User::role('consultant')->with(['profile', 'roles'])->get();

        return UserResource::collection($consultants);
    }

    public function assignConsultant(Request $request, User $user): JsonResponse
    {
        if (! Auth::user()->hasRole('admin')) {
            return response()->json(['message' => 'Only admins can assign consultants.'], 403);
        }

        $request->validate([
            'consultant_id' => 'nullable|exists:users,id',
        ]);

        if ($request->consultant_id) {
            $consultant = User::findOrFail($request->consultant_id);
            if (! $consultant->hasRole('consultant')) {
                return response()->json(['message' => 'Selected user is not a consultant.'], 422);
            }
        }

        $previousConsultantId = $user->consultant_id;

        $user->update(['consultant_id' => $request->consultant_id]);
        $user->load(['profile', 'roles', 'consultant']);

        if ($request->consultant_id && $request->consultant_id !== $previousConsultantId) {
            $consultant->notify(new ClientAssigned($user));
        }

        return response()->json([
            'message' => 'Consultant assigned successfully.',
            'user' => new UserResource($user),
        ]);
    }

    public function myClients(): JsonResponse
    {
        $consultant = Auth::user();

        $clients = User::where('consultant_id', $consultant->id)
            ->with(['profile', 'roles'])
            ->get();

        if ($clients->isEmpty()) {
            return response()->json(['data' => []]);
        }

        $clientIds = $clients->pluck('id');

        // Load all paths for all clients in a single query instead of N queries
        $allPaths = Path::whereHas('plans', function ($q) use ($clientIds) {
            $q->whereHas('clients', fn ($q2) => $q2->whereIn('users.id', $clientIds));
        })
            ->with([
                'steps:id,path_id',
                'plans.clients' => fn ($q) => $q->whereIn('users.id', $clientIds)->select('users.id'),
            ])
            ->get();

        // Build client → paths map (keyed by path ID to deduplicate across plans)
        $pathsByClient = [];
        foreach ($allPaths as $path) {
            foreach ($path->plans as $plan) {
                foreach ($plan->clients as $planClient) {
                    $pathsByClient[$planClient->id][$path->id] = $path;
                }
            }
        }

        // Load all done progress for all clients in a single query
        $allStepIds = $allPaths->flatMap(fn ($p) => $p->steps->pluck('id'));
        $progressByClient = UserStepProgress::whereIn('user_id', $clientIds)
            ->whereIn('path_step_id', $allStepIds)
            ->where('status', 'done')
            ->get(['user_id', 'path_step_id'])
            ->groupBy('user_id');

        $data = $clients->map(function (User $client) use ($pathsByClient, $progressByClient) {
            $paths = collect($pathsByClient[$client->id] ?? [])->values();
            $stepIds = $paths->flatMap(fn ($p) => $p->steps->pluck('id'));
            $doneCount = ($progressByClient->get($client->id) ?? collect())
                ->whereIn('path_step_id', $stepIds->all())
                ->count();
            $totalSteps = $stepIds->count();

            return [
                'id' => $client->id,
                'fullname' => $client->fullname,
                'email' => $client->email,
                'profile' => $client->profile ? [
                    'profession' => $client->profile->profession,
                    'level' => $client->profile->level,
                    'profile_image_url' => $client->profile->profile_image
                        ? asset('storage/'.$client->profile->profile_image)
                        : null,
                ] : null,
                'path_count' => $paths->count(),
                'progress_pct' => $totalSteps > 0 ? (int) round($doneCount / $totalSteps * 100) : 0,
                'done_steps' => $doneCount,
                'total_steps' => $totalSteps,
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function clientDetail(User $client): JsonResponse
    {
        $consultant = Auth::user();

        if ($client->consultant_id !== $consultant->id && ! $consultant->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $client->load(['profile', 'roles']);

        $paths = Path::whereHas('plans', function ($q) use ($client) {
            $q->whereHas('clients', fn ($q2) => $q2->where('users.id', $client->id));
        })->with(['steps.course'])->get();

        $stepIds = $paths->flatMap(fn ($p) => $p->steps->pluck('id'));
        $progressMap = UserStepProgress::where('user_id', $client->id)
            ->whereIn('path_step_id', $stepIds)
            ->pluck('status', 'path_step_id');

        $pathData = $paths->map(fn ($path) => [
            'id' => $path->id,
            'name' => $path->name,
            'description' => $path->description,
            'steps' => $path->steps->map(fn ($step) => [
                'id' => $step->id,
                'order' => $step->order,
                'title' => $step->title,
                'type' => $step->type,
                'course' => $step->course ? ['id' => $step->course->id, 'name' => $step->course->name] : null,
                'user_status' => $progressMap->get($step->id, 'not_started'),
            ])->sortBy('order')->values(),
            'done_count' => $path->steps->filter(fn ($s) => $progressMap->get($s->id) === 'done')->count(),
            'total_count' => $path->steps->count(),
        ]);

        return response()->json([
            'user' => new UserResource($client),
            'paths' => $pathData->values(),
        ]);
    }

    public function assignPath(Request $request, User $client): JsonResponse
    {
        $consultant = Auth::user();

        if ($client->consultant_id !== $consultant->id && ! $consultant->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $request->validate(['path_id' => 'required|exists:paths,id']);

        $this->planService->assignPathToClient($consultant->id, $client->id, $request->path_id);

        $path = Path::findOrFail($request->path_id);
        $client->notify(new PathAssigned($consultant, $path));

        return response()->json(['message' => 'Path assigned successfully.']);
    }

    public function removePath(User $client, Path $path): JsonResponse
    {
        $consultant = Auth::user();

        if ($client->consultant_id !== $consultant->id && ! $consultant->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $this->planService->removePathFromClient($consultant->id, $client->id, $path->id);

        return response()->json(['message' => 'Path removed successfully.']);
    }

    public function completeOnboarding(Request $request): JsonResponse
    {
        $user = Auth::user();

        $data = $request->validate([
            'profession' => 'required|string|max:255',
            'level' => 'required|in:junior,mid,senior,manager',
            'stack' => 'required|array|min:1|max:20',
            'stack.*' => 'string|max:50',
            'product_interest' => 'required|in:self-serve,bootcamp,mentorship',
            'availability_hours' => 'required|integer|min:1|max:80',
            'timeline' => 'required|in:1-3m,3-6m,6-12m,flexible',
            'goal' => 'required|string|max:255',
        ]);

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $data
        );

        $user->update(['needs_onboarding' => false]);
        $user->load('profile', 'roles');

        User::role('admin')->each(fn (User $admin) => $admin->notify(new NewClientOnboarded($user)));

        return response()->json([
            'message' => 'Onboarding complete',
            'user' => new UserResource($user),
        ]);
    }

    /**
     * The authenticated user's gamification snapshot (practice funnel stage
     * 3 / F1): cumulative XP, current streak, and earned badges.
     */
    public function progress(Request $request): JsonResponse
    {
        $user = $request->user();
        $stats = $user->progressStats;

        return response()->json([
            'xp_points' => $stats->xp_points ?? 0,
            'current_streak' => $stats->current_streak ?? 0,
            'longest_streak' => $stats->longest_streak ?? 0,
            'last_practiced_at' => $stats?->last_practiced_at,
            'badges' => $user->badges()
                ->orderBy('user_badges.earned_at')
                ->get()
                ->map(fn ($badge) => [
                    'key' => $badge->key,
                    'category' => $badge->category,
                    'name' => $badge->name,
                    'description' => $badge->description,
                    'icon' => $badge->icon,
                    'earned_at' => $badge->pivot->earned_at,
                ]),
        ]);
    }

    /**
     * Daily practice activity for the GitHub-style contribution heatmap.
     * Counts, per day, solved challenges (UserChallengeCompletion.completed_at)
     * plus completed incident steps (approximated by UserStepProgress.updated_at
     * for type=incident). Derived from existing timestamps — no activity log.
     */
    public function activity(Request $request): JsonResponse
    {
        $user = $request->user();

        $byDay = [];

        $user->challengeCompletions()
            ->selectRaw('DATE(completed_at) as d, COUNT(*) as c')
            ->groupBy('d')
            ->pluck('c', 'd')
            ->each(function ($c, $d) use (&$byDay) {
                $byDay[$d] = ($byDay[$d] ?? 0) + (int) $c;
            });

        UserStepProgress::query()
            ->join('path_steps', 'path_steps.id', '=', 'user_step_progress.path_step_id')
            ->where('user_step_progress.user_id', $user->id)
            ->where('user_step_progress.status', 'done')
            ->where('path_steps.type', 'incident')
            ->selectRaw('DATE(user_step_progress.updated_at) as d, COUNT(*) as c')
            ->groupBy('d')
            ->pluck('c', 'd')
            ->each(function ($c, $d) use (&$byDay) {
                $byDay[$d] = ($byDay[$d] ?? 0) + (int) $c;
            });

        $activity = collect($byDay)
            ->map(fn ($count, $date) => ['date' => $date, 'count' => $count])
            ->values();

        return response()->json(['activity' => $activity]);
    }

    /**
     * Practice funnel F6 — the coaching upsell nudge for the current user.
     * Returns the single most relevant tier they've earned and don't yet own,
     * or {recommendation: null} when they haven't earned a nudge.
     */
    public function coachingRecommendation(Request $request, CoachingRecommendationService $coaching): JsonResponse
    {
        return response()->json([
            'recommendation' => $coaching->recommend($request->user()),
        ]);
    }
}
