<?php

namespace App\Services;

use App\Enums\RoleEnum;
use App\Exceptions\AuthorizationException;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class UserService
{
    public function __construct(
        private readonly FileUploadService $fileUploadService
    ) {}

    public function paginate(?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        $query = User::query()->with(['profile', 'roles']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('fullname', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage);
    }

    public function find(int $id): User
    {
        return User::with(['profile', 'roles'])->findOrFail($id);
    }

    public function create(array $data): User
    {
        $user = User::create([
            'fullname' => $data['fullname'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        $this->syncRole($user, $data['role']);
        $this->updateProfile($user, $data['profile'] ?? [], $data['profile_image'] ?? null);

        return $user->load(['profile', 'roles']);
    }

    public function update(User $user, array $data): User
    {
        $updateData = [
            'fullname' => $data['fullname'],
            'email' => $data['email'],
        ];

        if (! empty($data['password'])) {
            $updateData['password'] = $data['password'];
        }

        $user->update($updateData);

        if (isset($data['role'])) {
            $this->syncRole($user, $data['role']);
        }

        $this->updateProfile($user, $data['profile'] ?? [], $data['profile_image'] ?? null);

        return $user->fresh(['profile', 'roles']);
    }

    public function delete(User $user): void
    {
        $currentUser = Auth::user();

        if ($user->id === $currentUser->id) {
            throw new AuthorizationException('You cannot delete yourself.');
        }

        if ($user->hasRole(RoleEnum::ADMIN->value)) {
            $adminCount = User::role(RoleEnum::ADMIN->value)->count();

            if ($adminCount <= 1) {
                throw new AuthorizationException('You cannot delete the only admin.');
            }
        }

        if ($user->profile) {
            if ($user->profile->profile_image) {
                $this->fileUploadService->delete($user->profile->profile_image);
            }
            $user->profile->delete();
        }

        $user->delete();
    }

    public function updateAvatar(User $user, UploadedFile $file): string
    {
        $oldImage = $user->profile?->profile_image;
        $path = $this->fileUploadService->replace($oldImage, $file, 'profile_images');

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            ['profile_image' => $path]
        );

        return $path;
    }

    private function syncRole(User $user, int $roleId): void
    {
        $role = Role::findById($roleId);
        $roleEnum = RoleEnum::fromId($roleId);

        if ($roleEnum === RoleEnum::ADMIN && ! Auth::user()->hasRole(RoleEnum::ADMIN->value)) {
            throw new AuthorizationException('You cannot assign admin role.');
        }

        $user->syncRoles($role->name);
    }

    private function updateProfile(User $user, array $profileData, ?UploadedFile $image = null): void
    {
        if ($image) {
            $oldImage = $user->profile?->profile_image;
            $profileData['profile_image'] = $this->fileUploadService->replace(
                $oldImage,
                $image,
                'profile_images'
            );
        }

        if (! empty($profileData)) {
            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                $profileData
            );
        }
    }
}
