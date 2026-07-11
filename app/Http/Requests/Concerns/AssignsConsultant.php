<?php

namespace App\Http\Requests\Concerns;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;

/**
 * Shared consultant_id handling for the consultant-owned resources
 * (Plan, Path, Job). Prevents non-admins from creating or reassigning
 * resources on behalf of another consultant.
 */
trait AssignsConsultant
{
    /**
     * Normalise consultant_id before validation:
     * - non-admins can only ever own their own resources, so any client-
     *   supplied consultant_id is overwritten with their own id;
     * - admins may set it explicitly (validated in rules), and default to
     *   themselves only when creating without one.
     */
    protected function normaliseConsultantId(): void
    {
        $user = Auth::user();

        if ($user === null) {
            return;
        }

        if (! $user->hasRole(RoleEnum::ADMIN->value)) {
            $this->merge(['consultant_id' => $user->getAuthIdentifier()]);

            return;
        }

        if ($this->isMethod('POST') && ! $this->filled('consultant_id')) {
            $this->merge(['consultant_id' => $user->getAuthIdentifier()]);
        }
    }

    /**
     * When an admin sets consultant_id explicitly, the target must actually
     * be able to own the resource (consultant or admin).
     */
    protected function validateConsultantOwner(Validator $validator): void
    {
        $consultantId = $this->input('consultant_id');

        if ($consultantId === null) {
            return;
        }

        $target = User::find($consultantId);

        if ($target === null || ! $target->hasAnyRole([RoleEnum::CONSULTANT->value, RoleEnum::ADMIN->value])) {
            $validator->errors()->add('consultant_id', 'The selected consultant is invalid.');
        }
    }
}
