<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\ClinicObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy([ClinicObserver::class])]
final class Clinic extends Base
{
    protected $cascadeDeletes = [
        'patientRecords',
        'patients',
        'users',
    ];

    protected $fillable = ['name'];

    /**
     * Get the patient records associated with the clinic.
     *
     * This defines a one-to-many relationship between the Clinic model and the
     * PatientRecord model, where a clinic can have multiple patient records.
     */
    public function patientRecords(): HasMany
    {
        return $this->hasMany(PatientRecord::class);
    }

    /**
     * Get the patients associated with the clinic.
     *
     * This relationship defines a one-to-many association between the Clinic
     * model and the Patient model, where a clinic can have multiple patients.
     */
    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class);
    }

    /**
     * Define a many-to-many relationship between the Clinic model and the User model.
     *
     * This method establishes a relationship where a clinic can have multiple users,
     * and a user can belong to multiple clinics. The relationship is managed through
     * the pivot model `ClinicUser`.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->using(ClinicUser::class);
    }

    /**
     * Define an accessor/mutator for the "name" attribute.
     *
     * This method creates an attribute mutator that processes the input value
     * by removing extra whitespace (squishing) before saving it to the database.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => str($value)->squish()
        );
    }
}
