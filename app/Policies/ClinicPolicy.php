<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Clinic;
use App\Models\User;
use App\Traits\SuperAdminOverride;

final class ClinicPolicy
{
    use SuperAdminOverride;

    /**
     * Determine whether the user can view any clinics.
     */
    public function viewAny(): bool
    {
        return true;
    }

    /**
     * Determine whether the given user can view the specified clinic.
     *
     * Authorization is based on whether the user is associated with the clinic.
     *
     * @param  User  $user  The user attempting to view the clinic.
     * @param  Clinic  $clinic  The clinic being accessed.
     * @return bool True if the user belongs to the clinic, false otherwise.
     */
    public function view(User $user, Clinic $clinic): bool
    {
        return $this->doesTheUserContainTheClinic(
            clinic: $clinic,
            user: $user,
        );
    }

    /**
     * Determine whether the given user can create a clinic.
     *
     * Authorization is based on the user's role or permissions.
     *
     * @return bool True if the user can create a clinic, false otherwise.
     */
    public function create(): bool
    {
        return true;
    }

    /**
     * Determine whether the given user can update the specified clinic.
     *
     * Authorization is based on whether the user is associated with the clinic.
     *
     * @param  User  $user  The user attempting to update the clinic.
     * @param  Clinic  $clinic  The clinic being updated.
     * @return bool True if the user belongs to the clinic, false otherwise.
     */
    public function update(User $user, Clinic $clinic): bool
    {
        return $this->doesTheUserContainTheClinic(
            clinic: $clinic,
            user: $user,
        );
    }

    /**
     * Determine whether the given user can delete the specified clinic.
     *
     * Authorization is based on whether the user is associated with the clinic.
     *
     * @param  User  $user  The user attempting to delete the clinic.
     * @param  Clinic  $clinic  The clinic being deleted.
     * @return bool True if the user belongs to the clinic, false otherwise.
     */
    public function delete(User $user, Clinic $clinic): bool
    {
        return $this->doesTheUserContainTheClinic(
            clinic: $clinic,
            user: $user,
        );
    }

    /**
     * Determine whether the user can restore the soft-deleted clinic.
     *
     * @param  User  $user  The user attempting to restore the clinic.
     * @param  Clinic  $clinic  The clinic instance being restored.
     * @return bool True if the user belongs to the clinic (including soft-deleted clinics), false otherwise.
     */
    public function restore(User $user, Clinic $clinic): bool
    {
        return $user->clinicsWithTrashed->contains($clinic);
    }

    /**
     * Determine whether the user can permanently delete the clinic.
     *
     * @return bool Returns false, indicating the user cannot permanently delete the clinic.
     */
    public function forceDelete(): bool
    {
        return false;
    }

    /**
     * Check if the user belongs to the specified clinic.
     *
     * @param  Clinic  $clinic  The clinic to check membership for.
     * @param  User  $user  The user to verify clinic membership.
     * @return bool True if the user belongs to the clinic, false otherwise.
     */
    private function doesTheUserContainTheClinic(Clinic $clinic, User $user): bool
    {
        return $user->clinics->contains($clinic);
    }
}
