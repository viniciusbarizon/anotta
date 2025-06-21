<?php

declare(strict_types=1);

use App\Models\Clinic;
use App\Models\Patient;
use Illuminate\Support\Collection;

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
 * Creates multiple instances of a given model using the factory.
 *
 * This function generates 10 instances of the specified model, each with
 * a unique `created_at` and `updated_at` timestamp. If `$hasDeletedAt` is true,
 * it also sets the `deleted_at` field for the first instance.
 *
 * @param  mixed  $model  The model class to create instances of.
 * @param  bool  $hasDeletedAt  Whether to include a `deleted_at` field.
 * @return Collection A collection of created model instances.
 */
function createMultiples(mixed $model, $hasDeletedAt = true): Collection
{
    collect(range(0, 9))->each(function (int $i) use ($hasDeletedAt, $model) {
        $date = now()->subDays($i);

        $attributes = [
            'created_at' => $date,
            'updated_at' => $date,
        ];

        if ($hasDeletedAt) {
            $deletedAt = $i === 0 ? $date : null;
            $attributes['deleted_at'] = $deletedAt ?? null;
        }

        $model::factory()
            ->create($attributes);
    });

    return $model::all();
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
