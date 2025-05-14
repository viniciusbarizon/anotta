<?php

namespace App\Filament\Resources\PatientResource\Pages;

use App\Filament\Resources\PatientResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Get;

class CreatePatient extends CreateRecord
{
    protected static string $resource = PatientResource::class;
}
