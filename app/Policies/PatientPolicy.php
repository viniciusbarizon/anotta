<?php

declare(strict_types=1);

namespace App\Policies;

final class PatientPolicy
{
    /**
     * Determine whether any patient records can be viewed.
     *
     * @return bool Always returns true, allowing viewing of any patient records.
     */
    public function viewAny(): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create a new patient.
     *
     * @return bool Always returns true, allowing creation of patients.
     */
    public function create(): bool
    {
        return true;
    }
}
