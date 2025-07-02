<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

final class UserPolicy
{
    /**
     * Determine whether the user can view any users.
     *
     * @param  User  $user  The user attempting to view the list of users.
     * @return bool True if the user is a super admin or has not verified their email, false otherwise.
     */
    public function viewAny(User $user): bool
    {
        if (is_null($user->email_verified_at)) {
            return true;
        }

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
     * @param  User  $user  The user attempting to update the model.
     * @param  User  $model  The user model being updated.
     * @return bool True if the user is updating their own profile, false otherwise.
     */
    public function update(User $user, User $model): bool
    {
        return $user->id === $model->id;
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
