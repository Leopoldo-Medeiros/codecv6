<?php

namespace App\Services\Concerns;

use App\Enums\RoleEnum;
use App\Exceptions\AuthorizationException;
use Illuminate\Support\Facades\Auth;

/**
 * Temporary owner-or-admin guard for the consultant-owned resources (Plan,
 * Path, Job). This is Phase 0 of docs/architecture-review.md — kept in one
 * place per resource so Phase 2 can swap it for a Policy call without
 * hunting through controllers.
 */
trait EnsuresResourceOwnership
{
    protected function ensureOwnerOrAdmin(?int $ownerId, string $resourceName): void
    {
        $actor = Auth::user();

        if ($actor->hasRole(RoleEnum::ADMIN->value)) {
            return;
        }

        if ($ownerId !== $actor->id) {
            throw new AuthorizationException("You do not own this {$resourceName}.");
        }
    }
}
