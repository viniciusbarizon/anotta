<?php

declare(strict_types=1);

namespace App\Actions\Patient;

use App\Models\Patient;

final class AlreadyExistsAction
{
    public function __construct(
        private readonly string $birthDate,
        private readonly string $clinicId,
        private readonly ?string $currentId,
        private readonly string $fullName,
        private readonly string $mobilePhone
    ) {}

    public function handle(): bool
    {
        return $this->exists();
    }

    private function exists(): bool
    {
        return Patient::query()
            ->withTrashed()
            ->where('clinic_id', $this->clinicId)
            ->where('birth_date', $this->birthDate)
            ->whereBlind('full_name_normalized', 'full_name_normalized_index', normalize_name($this->fullName))
            ->whereBlind('mobile_phone', 'mobile_phone_index', $this->mobilePhone)
            //->when(is_null())
            ->exists();
    }
}