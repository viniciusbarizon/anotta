<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Clinician extends Base
{
    protected $fillable = [
        'user_id',
    ];

    /**
     * Define a many-to-many relationship between the clinician and clinics.
     *
     * This method proxies the relationship through the associated user model.
     */
    public function clinics(): BelongsToMany
    {
        return $this->user->clinics();
    }

    /**
     * Get the patient records associated with the professional.
     */
    public function patientRecords(): HasMany
    {
        return $this->hasMany(PatientRecord::class);
    }

    /**
     * Define a many-to-many relationship between professionals and patients.
     */
    public function patients(): BelongsToMany
    {
        return $this->belongsToMany(Patient::class);
    }

    /**
     * Get the user that owns the clinician.
     *
     * This defines an inverse one-to-many relationship between the Clinician model
     * and the User model.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
