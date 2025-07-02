<?php

declare(strict_types=1);

namespace App\Actions\Clinic;

use App\Models\Clinic;

final class HasAnotherWithSameNameAction
{
    private string $name;

    public function __construct(private readonly mixed $currentId) {}

    /**
     * Handles the action of checking if another clinic with the same name exists.
     *
     * @param  string  $name  The name of the clinic to check.
     * @return bool True if another clinic with the same normalized name exists, false otherwise.
     */
    public function handle(string $name): bool
    {
        $this->name = normalize_name(name: $name);

        return $this->has();
    }

    /**
     * Checks if there is another Clinic record (including soft-deleted ones)
     * with the same normalized name but a different ID.
     *
     * @return bool True if another clinic with the same normalized name exists, false otherwise.
     */
    private function has(): bool
    {
        return Clinic::query()
            ->withTrashed()
            ->where('name_normalized', $this->name)
            ->where('id', '!=', $this->currentId)
            ->exists();
    }
}
