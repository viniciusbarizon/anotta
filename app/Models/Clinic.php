<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Clinic extends Base
{
    protected $fillable = [
        'name',
    ];

    /**
     * Get the patient records associated with the clinic.
     *
     * This defines a one-to-many relationship between the Clinic model and the
     * PatientRecord model, where a clinic can have multiple patient records.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
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
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class);
    }

    /**
     * Define a many-to-many relationship between the Clinic model and the User model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
