<?php

declare(strict_types=1);

namespace App\Filament\Resources\ClinicResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Presets\Actions\HeaderPresets;
use App\Filament\Resources\ClinicResource;
use Filament\Resources\Pages\ManageRecords;

final class ManageClinics extends ManageRecords
{
    protected static string $resource = ClinicResource::class;

    /**
     * Retrieves the header actions for the ManageClinics page.
     *
     * This method overrides the default `getHeaderActions` method to provide
     * a set of predefined header actions using the `HeaderPresets::default()` method.
     *
     * @return array<CreateAction> An array of header actions.
     */
    protected function getHeaderActions(): array
    {
        return HeaderPresets::default();
    }
}
