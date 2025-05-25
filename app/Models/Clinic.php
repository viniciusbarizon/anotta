<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Scopes\UserRelationScope;
use App\Observers\ClinicObserver;
use Database\Factories\ClinicFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Override;

#[ObservedBy([ClinicObserver::class])]
#[ScopedBy([UserRelationScope::class])]
final class Clinic extends Base
{
    /** @use HasFactory<ClinicFactory> */
    use HasFactory;

    protected array $cascadeDeletes = [
        'patientRecords',
        'patients',
        'users',
    ];

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
     * The "booted" method is a model event hook that is automatically called
     * when the model is being initialized. In this case, it is used to define
     * a "saving" event listener for the Clinic model.
     *
     * The "saving" event is triggered before a model is saved (either created
     * or updated). This listener ensures that the "name_normalized" attribute
     * is automatically set by normalizing the "name" attribute using the
     * "normalize_name" helper function before the model is persisted to the
     * database.
     */
    #[Override]
    protected static function booted(): void
    {
        self::saving(function ($clinic): void {
            $clinic->name_normalized = normalize_name($clinic->name);
        });
    }

    /**
     * Define an accessor/mutator for the "name" attribute.
     *
     * This method creates an attribute mutator that processes the input value
     * by removing extra whitespace and squishing it into a single string.
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => (string) str($value)->squish()
        );
    }
}
