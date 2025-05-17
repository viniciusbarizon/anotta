<?php

declare(strict_types=1);

use App\Filament\Resources\ClinicResource\Pages\ManageClinics;
use App\Models\Clinic;

use function Pest\Livewire\livewire;

it('can list clinics', function () {
    login();

    $clinics = Clinic::all();

    livewire(ManageClinics::class)
        ->assertCanSeeTableRecords($clinics);
});
