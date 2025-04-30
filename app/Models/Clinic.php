<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Clinic extends Base
{
    protected $fillable = [
        'name',
    ];

    /**
     * Define a many-to-many relationship between Clinic and Professional models.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function professionals(): BelongsToMany
    {
        return $this->belongsToMany(Professional::class);
    }
}
