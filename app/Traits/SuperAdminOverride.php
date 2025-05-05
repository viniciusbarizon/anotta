<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\User;

trait SuperAdminOverride
{
    /**
     * Determine if the given ability should be overridden for the user.
     *
     * This method is executed before any other authorization checks. It allows
     * for overriding the authorization logic based on the user's role or the
     * specific ability being checked.
     *
     * @param  User  $user  The user instance being checked.
     * @param  string  $ability  The ability being checked (e.g., 'view', 'update', etc.).
     * @return bool|null Returns `true` if the user is a super admin and the ability
     *                   is not 'forceDelete', allowing the action. Returns `null`
     *                   if no override is applied, allowing further checks.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($ability === 'forceDelete') {
            return null;
        }

        if ($user->is_super_admin === false) {
            return null;
        }

        return true;
    }
}
