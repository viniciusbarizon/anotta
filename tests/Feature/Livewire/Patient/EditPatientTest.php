<?php

declare(strict_types=1);

use App\Filament\Resources\PatientResource\Pages\EditPatient;
use App\Models\Patient;
use Livewire\Features\SupportTesting\Testable;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    $this->patient = createPatient();
});

function getComponent(Patient $patient): Testable
{
    return livewire(EditPatient::class, [
        'record' => test()->patient->getRouteKey(),
    ]);
}

it('retrieves the data ', function () {
    getComponent($this->patient)
        ->assertFormSet([
            'birth_date' => $this->patient->birth_date,
            'clinic_id' => $this->patient->clinic_id,
            'full_name' => $this->patient->full_name,
            'mobile_phone' => $this->patient->mobile_phone,
            'tax_country' => $this->patient->tax_country,
            'tax_id' => $this->patient->tax_id,
        ]);
});

it('updates the patient', function() {
    $newData = Patient::factory()->make();

    getComponent($this->patient)
        ->fillForm([
            'birth_date' => $newData->birth_date,
            'clinic_id' => $newData->clinic_id,
            'full_name' => $newData->full_name,
            'mobile_phone' => $newData->mobile_phone,
            'tax_country_id' => $newData->tax_country_id,
            'tax_id' => $newData->tax_id,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($this->patient->refresh())
        ->birth_date->toBe($newData->birth_date)
        ->clinic_id->toBe($newData->clinic_id)
        ->full_name->toBe($newData->full_name)
        ->mobile_phone->toBe($newData->mobile_phone)
        ->tax_country_id->toBe($newData->tax_country_id)
        ->tax_id->toBe($newData->tax_id);
});

it ('does not validate the name as duplicate', function () {
    getComponent($this->patient)
        ->fillForm([
            'birth_date' => $this->patient->birth_date,
            'clinic_id' => $this->patient->clinic_id,
            'full_name' => $this->patient->full_name,
            'mobile_phone' => $this->patient->mobile_phone,
            'tax_country_id' => $this->patient->tax_country_id,
            'tax_id' => $this->patient->tax_id,
        ])
        ->call('save')
        ->assertHasNoFormErrors();
});
