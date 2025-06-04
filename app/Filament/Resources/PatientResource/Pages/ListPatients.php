<?php

declare(strict_types=1);

namespace App\Filament\Resources\PatientResource\Pages;

use App\Filament\Resources\PatientResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

final class ListPatients extends ListRecords
{
    protected static string $resource = PatientResource::class;

    /**
     * Returns an array of header actions for the patient list page.
     *
     * This method defines the actions that appear in the header section of the patient resource list.
     * By default, it includes a "Create" action that allows users to add new patients.
     *
     * @return array<CreateAction>
     */
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
