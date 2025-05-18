<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\User;

trait SuperAdminOverride
{
    /**
     * Determines authorization for specific abilities based on user's super admin status.
     *
     * This method is typically used in policy authorization checks to provide special
     * permissions for super administrators. It allows or prevents certain actions
     * based on the user's super admin status and the specific ability being checked.
     *
     * @param User $user The user attempting to perform the action
     * @param string $ability The action or ability being checked
     * @return bool|null Returns true for super admins, null otherwise, allowing fallback to other authorization checks
     */
    public function before(User $user, string $ability): ?bool
    {
        if (in_array($ability, ['forceDelete', 'update']) === true) {
            return null;
        }

        if ($user->is_super_admin === false) {
            return null;
        }

        return true;
    }
}
