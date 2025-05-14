<?php

declare(strict_types=1);

namespace App\Filament\Resources\PatientResource\Pages;

use App\Filament\Resources\PatientResource;
use Filament\Resources\Pages\CreateRecord;

final class CreatePatient extends CreateRecord
{
    protected static string $resource = PatientResource::class;
}
