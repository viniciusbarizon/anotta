<?php

declare(strict_types=1);

namespace App\Actions\Clinic;

use App\Models\Clinic;

final class HasAnotherWithSameNameAction
{
    private readonly ?string $currentId;

    private readonly string $name;

    /**
     * Handles the check for a duplicated clinic name.
     *
     * @param  string|null  $currentId  The ID of the current clinic, or null if not applicable.
     * @param  string  $name  The name of the clinic to check for duplication.
     * @return bool Returns true if a clinic with the same name already exists, false otherwise.
     */
    public function handle(?string $currentId, string $name): bool
    {
        $this->currentId = $currentId;
        $this->name = normalize_name(name: $name);

        return $this->has();
    }

    /**
     * Checks if there is another clinic with the same normalized name
     * associated with the current authenticated user, excluding the clinic
     * with the specified current ID.
     *
     * @return bool True if another clinic with the same name exists, false otherwise.
     */
    private function has(): bool
    {
        return Clinic::query()
            ->withTrashed()
            ->fromUserLogged()
            ->where('name_normalized', $this->name)
            ->where('id', '!=', $this->currentId)
            ->exists();
    }
}
