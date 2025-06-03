<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the given user can view any user records.
     *
     * @param  \App\Models\User  $user  The user making the request.
     * @return bool  True if the user is a super admin, false otherwise.
     */
    public function viewAny(User $user): bool
    {
        return $user->is_super_admin;
    }

    /**
     * Determine whether the user can view the resource.
     *
     * @return bool Always returns false, denying view permission.
     */
    public function view(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create a new user.
     *
     * @return bool Always returns false, denying permission to create users.
     */
    public function create(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the resource.
     *
     * @return bool Always returns false, denying update permission.
     */
    public function update(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the resource.
     *
     * @return bool Always returns false, denying delete permission.
     */
    public function delete(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return bool Always returns false, indicating that restore action is not permitted.
     */
    public function restore(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the resource.
     *
     * @return bool Always returns false, denying force deletion.
     */
    public function forceDelete(): bool
    {
        return false;
    }
}
