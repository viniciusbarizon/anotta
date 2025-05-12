<?php

declare(strict_types=1);

namespace App\Filament\Resources\PatientResource\Pages;

use App\Filament\Presets\Actions\HeaderPresets;
use App\Filament\Resources\PatientResource;
use Filament\Resources\Pages\ManageRecords;

final class ManagePatients extends ManageRecords
{
    protected static string $resource = PatientResource::class;

    /**
     * Get the header actions for the manage patients page.
     *
     * @return array The default header actions from HeaderPresets
     */
    protected function getHeaderActions(): array
    {
        return HeaderPresets::default();
    }
}
