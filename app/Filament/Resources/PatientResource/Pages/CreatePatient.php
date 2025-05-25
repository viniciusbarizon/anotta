<?php

declare(strict_types=1);

namespace App\Filament\Resources\PatientResource\Pages;

use App\Filament\Resources\PatientResource;
use Filament\Resources\Pages\CreateRecord;

final class CreatePatient extends CreateRecord
{
    protected static string $resource = PatientResource::class;

    /**
     * Mutates the form data before creating a new patient record.
     *
     * This method normalizes the 'full_name' field using the `normalize_name` function
     * and stores the result in the 'full_name_normalized' key of the data array.
     *
     * @param  array<string,string>  $data  The form data submitted for patient creation.
     * @return array<string,string> The mutated form data with the normalized full name.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['full_name_normalized'] = normalize_name($data['full_name']);

        return $data;
    }
}
