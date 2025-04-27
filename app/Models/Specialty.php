<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class Specialty extends Base
{
    protected $fillable = ['name'];

    /**
     * Define a many-to-many relationship between specialties and professionals.
     */
    public function professionals(): BelongsToMany
    {
        return $this->belongsToMany(Professional::class);
    }
}
