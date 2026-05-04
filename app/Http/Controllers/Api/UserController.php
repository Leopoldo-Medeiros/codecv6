<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\AuthorizationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\Path;
use App\Models\User;
use App\Models\UserStepProgress;
use App\Notifications\NewClientOnboarded;
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

        $user->update(['consultant_id' => $request->consultant_id]);
        $user->load(['profile', 'roles', 'consultant']);

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
            ->get()
            ->map(function (User $client) {
                $paths = Path::whereHas('plans', function ($q) use ($client) {
                    $q->whereHas('clients', fn ($q2) => $q2->where('users.id', $client->id));
                })->withCount('steps')->get();

                $stepIds = $paths->flatMap(fn ($p) => $p->steps()->pluck('id'));
                $doneCount = UserStepProgress::where('user_id', $client->id)
                    ->whereIn('path_step_id', $stepIds)
                    ->where('status', 'done')
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

        return response()->json(['data' => $clients]);
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
            'stack' => 'required|array|min:1',
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
}
