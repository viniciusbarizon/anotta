<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Clinic;
use App\Models\User;

final class ClinicPolicy
{
    /**
     * Determine whether the user can view any clinics.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the given user can view the specified clinic.
     *
     * Authorization is based on whether the user is associated with the clinic.
     *
     * @param  User  $user  The authenticated user.
     * @param  Clinic  $clinic  The clinic being accessed.
     * @return bool True if the user belongs to the clinic.
     */
    public function view(User $user, Clinic $clinic): bool
    {
        return $this->belongsToClinic(
            clinicId: $clinic->id,
            user: $user
        );
    }

    /**
     * Determine whether the given clinic can be viewed by the user.
     *
     * @param  User  $user  The user attempting to view the clinic.
     * @param  Clinic  $clinic  The clinic being accessed.
     * @return bool True if the user belongs to the clinic, false otherwise.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the given clinic can be updated by the user.
     *
     * @param  User  $user  The user attempting to perform the update.
     * @param  Clinic  $clinic  The clinic being updated.
     * @return bool True if the user belongs to the clinic and can update it, false otherwise.
     */
    public function update(User $user, Clinic $clinic): bool
    {
        return $this->belongsToClinic(
            clinicId: $clinic->id,
            user: $user
        );
    }

    /**
     * Determine whether the given clinic can be deleted by the user.
     *
     * @param  User  $user  The user attempting to delete the clinic.
     * @param  Clinic  $clinic  The clinic instance to be deleted.
     * @return bool True if the user is authorized to delete the clinic, false otherwise.
     */
    public function delete(User $user, Clinic $clinic): bool
    {
        return $this->belongsToClinic(
            clinicId: $clinic->id,
            user: $user
        );
    }

    /**
     * Determine whether the user can restore the given clinic.
     *
     * @param  User  $user  The user making the request.
     * @param  Clinic  $clinic  The clinic instance to be restored.
     * @return bool True if the user belongs to the clinic, false otherwise.
     */
    public function restore(User $user, Clinic $clinic): bool
    {
        return $this->belongsToClinic(
            clinicId: $clinic->id,
            user: $user
        );
    }

    /**
     * Determine whether the user can permanently delete the clinic.
     *
     * @param  User  $user  The user making the request.
     * @param  Clinic  $clinic  The clinic instance being evaluated.
     * @return bool Returns false, indicating the user cannot permanently delete the clinic.
     */
    public function forceDelete(User $user, Clinic $clinic): bool
    {
        return false;
    }

    /**
     * Determine if the given clinic ID belongs to the user's associated clinics.
     *
     * @param  int  $clinicId  The ID of the clinic to check.
     * @param  User  $user  The user instance to verify against.
     * @return bool True if the user belongs to the clinic, false otherwise.
     */
    private function belongsToClinic(string $clinicId, User $user): bool
    {
        return $user->belongsToClinic($clinicId)
            ->exists();
    }
}
