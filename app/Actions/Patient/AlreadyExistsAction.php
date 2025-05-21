<?php

declare(strict_types=1);

namespace App\Actions\Patient;

use App\Models\Patient;

final class AlreadyExistsAction
{
    /**
     * Constructor for the AlreadyExistsAction class.
     *
     * @param  string  $birthDate  The patient's birth date.
     * @param  string  $clinicId  The unique identifier of the clinic.
     * @param  string|null  $currentId  The current patient ID, or null if not applicable.
     * @param  string  $fullName  The full name of the patient.
     * @param  string  $mobilePhone  The patient's mobile phone number.
     */
    public function __construct(
        private readonly string $birthDate,
        private readonly string $clinicId,
        private readonly ?string $currentId,
        private readonly string $fullName,
        private readonly string $mobilePhone
    ) {}

    /**
     * Handles the action to check if a patient already exists.
     *
     * @return bool Returns true if the patient exists, false otherwise.
     */
    public function handle(): bool
    {
        return $this->exists();
    }

    /**
     * Checks if a patient already exists in the database with the given clinic ID, birth date,
     * normalized full name, and mobile phone, excluding the current patient ID if provided.
     * Utilizes blind indexes for secure searching on sensitive fields.
     *
     * @return bool Returns true if a matching patient exists (including soft-deleted records), false otherwise.
     */
    private function exists(): bool
    {
        return Patient::query()
            ->withTrashed()
            ->where('clinic_id', $this->clinicId)
            ->where('birth_date', $this->birthDate)
            ->whereBlind('full_name_normalized', 'full_name_normalized_index', normalize_name($this->fullName))
            ->whereBlind('mobile_phone', 'mobile_phone_index', $this->mobilePhone)
            ->notCurrentId($this->currentId)
            ->exists();
    }
}
