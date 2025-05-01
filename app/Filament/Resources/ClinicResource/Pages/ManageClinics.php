<?php

declare(strict_types=1);

namespace App\Filament\Resources\ClinicResource\Pages;

use App\Filament\Resources\ClinicResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

final class ManageClinics extends ManageRecords
{
    protected static string $resource = ClinicResource::class;

    /**
     * Defines the header actions for the ManageClinics page.
     *
     * @return array The array of header actions, including a CreateAction
     *               with the "create another" option disabled.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->createAnother(false),
        ];
    }
}
