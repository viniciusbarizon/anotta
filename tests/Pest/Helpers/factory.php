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
 * @param  mixed  $model  The model class or instance to create.
 * @param  bool  $hasDeletedAt  Whether the model has a 'deleted_at' attribute. Defaults to true.
 * @param  int  $total  The total number of instances to create. Defaults to 10.
 * @return Collection A collection of created model instances.
 */
function createMultiples(mixed $model, $hasDeletedAt = true, $total = 10): Collection
{
    collect(range(0, $total - 1))->each(function (int $i) use ($hasDeletedAt, $model): void {
        if ($hasDeletedAt && $i === 0) {
            $model::factory()
                ->softDeleted()
                ->create();

            return;
        }

        $model::factory()
            ->create();
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
