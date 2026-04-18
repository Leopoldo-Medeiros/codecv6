<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\AuthorizationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService
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
        } catch (\App\Exceptions\AuthorizationException $e) {
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
}
