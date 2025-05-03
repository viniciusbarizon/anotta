<?php

declare(strict_types=1);

namespace App\Actions\Clinic;

use App\Models\Clinic;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

final class HasAnotherSameNameAction
{
    private ?string $currentId;

    private string $name;

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
        $this->name = $this->getNameUpperTrim(name: $name);

        return $this->has();
    }

    /**
     * Converts the given name to uppercase and trims any surrounding whitespace.
     *
     * This method uses multibyte string functions to ensure proper handling
     * of characters in multibyte encodings.
     *
     * @param  string  $name  The input name to be processed.
     * @return string The processed name in uppercase and trimmed of whitespace.
     */
    private function getNameUpperTrim(string $name): string
    {
        return mb_strtoupper(
            mb_trim($name)
        );
    }

    /**
     * Checks if there is another clinic with the same name.
     *
     * This method queries the database to determine if there is another clinic
     * (including soft-deleted ones) that has the same name as the current clinic,
     * but with a different ID. Additionally, it ensures that the clinic is
     * associated with the currently authenticated user.
     *
     * @return bool True if another clinic with the same name exists, false otherwise.
     */
    private function has(): bool
    {
        return Clinic::withTrashed()
            ->where(DB::raw('UPPER(name)'), $this->name)
            ->where('id', '!=', $this->currentId)
            ->whereRelation('users', 'user_id', Auth::user()->id)
            ->exists();
    }
}
