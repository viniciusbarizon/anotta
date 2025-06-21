<?php

use App\Models\Clinic;
use App\Models\Patient;

/**
 * Creates a new Clinic instance using the factory.
 *
 * Optionally allows specifying a name for the clinic.
 *
 * @param  string|null  $name  The name of the clinic. Defaults to null.
 * @return Clinic The created Clinic instance.
 */
function createClinic(?string $name = null): Clinic
{
    if (is_null($name)) {
        return Clinic::factory()->create();
    }

    return Clinic::factory()->create([
        'name' => $name,
    ]);
}

/**
 * Creates and persists a new Patient instance using the factory.
 *
 * @return Patient The newly created Patient model instance.
 */
function createPatient(): Patient
{
    return Patient::factory()->create();
}
