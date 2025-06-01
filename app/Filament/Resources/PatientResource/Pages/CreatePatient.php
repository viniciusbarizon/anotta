<?php

declare(strict_types=1);

namespace App\Filament\Resources\PatientResource\Pages;

use App\Filament\Resources\PatientResource;
use Filament\Resources\Pages\CreateRecord;
use Override;

final class CreatePatient extends CreateRecord
{
    protected static string $resource = PatientResource::class;

    /**
     * Returns the URL to redirect to after creating a patient.
     *
     * This method retrieves the index URL of the associated resource,
     * typically used to redirect the user back to the patient list page
     * after a successful creation.
     *
     * @return string The URL to redirect to.
     */
    protected function getRedirectUrl(): string
    {
        /** @var string $url */
        $url = $this->getResource()::getUrl('index');

        return $url;
    }

    /**
     * Mutates the form data before creating a new patient record.
     *
     * This method normalizes the 'full_name' field using the `normalize_name` function
     * and stores the result in the 'full_name_normalized' key of the data array.
     *
     * @param  array<string,string>  $data  The form data submitted for patient creation.
     * @return array<string,string> The mutated form data with the normalized full name.
     */
    #[Override]
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['full_name_normalized'] = normalize_name($data['full_name']);

        return $data;
    }
}
