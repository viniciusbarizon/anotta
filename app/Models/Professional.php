<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Professional extends Base
{
    protected $fillable = ['user_id'];

    /**
     * Define a many-to-many relationship between professionals and patients.
     */
    public function patients(): BelongsToMany
    {
        return $this->belongsToMany(Patient::class);
    }

    /**
     * Define a many-to-many relationship between professionals and specialties.
     */
    public function specialties(): BelongsToMany
    {
        return $this->belongsToMany(Specialty::class);
    }

    /**
     * Get the user that owns the professional.
     *
     * This defines an inverse one-to-many relationship between the Professional
     * model and the User model.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the patient records associated with the professional.
     */
    public function patientRecords(): HasMany
    {
        return $this->hasMany(PatientRecord::class);
    }
}
