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
 * Creates multiple instances of the given Eloquent model using its factory.
 *
 * This function generates 10 model instances with sequentially decreasing dates.
 * The most recent instance (i = 0) will have its 'deleted_at' field set,
 * while the others will have 'deleted_at' as null.
 *
 * @param  mixed  $model  The Eloquent model class name.
 * @return Collection The collection of all model instances.
 */
function createMultiples(mixed $model): Collection
{
    collect(range(0, 9))->each(function (int $i) use ($model) {
        $date = now()->subDays($i);
        $deletedAt = $i === 0 ? $date : null;

        $model::factory()
            ->create([
                'created_at' => $date,
                'updated_at' => $date,
                'deleted_at' => $deletedAt,
            ]);
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
