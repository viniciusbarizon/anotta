<?php

declare(strict_types=1);

use App\Filament\Resources\PatientResource\Pages\ListPatients;
use App\Models\Patient;
use Illuminate\Support\Collection;
use Livewire\Features\SupportTesting\Testable;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    $this->patients = createPatients();
    $this->listPatients = getListPatients();
});

/**
 * Creates a collection of patients with different creation dates.
 *
 * @return Collection A collection of Patient models.
 */
function createPatients(): Collection
{
    collect(range(0, 4))->each(function (int $i) {
        $date = now()->subDays($i);

        Patient::factory()
            ->create([
                'created_at' => $date,
                'updated_at' => $date,
            ]);
    });

    return Patient::all();
}

/**
 * Returns a Livewire instance for the ListPatients page.
 *
 * @return Testable A Livewire testable instance for the ListPatients component.
 */
function getListPatients(): Testable
{
    return livewire(ListPatients::class);
}

/**
 * Retrieves the first Patient instance from the test patients collection.
 *
 * @return Patient The first patient from the test data.
 */
function getPatient(): Patient
{
    return test()->patients
        ->first();
}

describe('not impersonated', function (): void {
    it('lists', function (): void {
        $this->listPatients
            ->assertCanSeeTableRecords($this->patients)
            ->assertCanRenderTableColumn('clinic.name')
            ->assertCanRenderTableColumn('full_name')
            ->assertCanRenderTableColumn('birth_date')
            ->assertCanRenderTableColumn('mobile_phone')
            ->assertCanRenderTableColumn('tax')
            ->assertCanRenderTableColumn('created_at')
            ->assertCountTableRecords(5)
            ->assertTableColumnHidden('id');
    });

    it('sorts by clinic', function (): void {
        assertSort(
            collection: $this->patients,
            column: 'clinic.name',
            flag: SORT_LOCALE_STRING,
            page: $this->listPatients
        );
    });

    it('sorts by full name', function (): void {
        assertSort(
            collection: $this->patients,
            column: 'full_name',
            flag: SORT_LOCALE_STRING,
            page: $this->listPatients
        );
    });

    it('sorts by created at', function (): void {
        assertSort(
            collection: $this->patients,
            column: 'created_at',
            page: $this->listPatients
        );
    });

    it('searches by clinic', function (): void {
        $clinic = getPatient()->clinic->name;

        $this->listPatients
            ->searchTable(getPatient()->clinic->name)
            ->assertCanSeeTableRecords(
                $this->patients->filter(
                    fn (Patient $patient) => str_contains(
                        mb_strtolower($patient->clinic->name),
                        mb_strtolower($clinic)
                    )
                )
            )
            ->assertCanNotSeeTableRecords(
                $this->patients->filter(
                    fn (Patient $patient) => str_contains(
                        mb_strtolower($patient->clinic->name),
                        mb_strtolower($clinic)
                    ) === false
                )
            );
    });

    it('searches by full name', function (): void {
        assertSearch(
            collection: $this->patients,
            column: 'full_name',
            page: $this->listPatients,
            value: getPatient()->full_name
        );
    });

    it('searches by birth date', function (): void {
        assertSearch(
            collection: $this->patients,
            column: 'birth_date',
            page: $this->listPatients,
            value: getPatient()->birth_date
        );
    });

    it('searches by mobile phone', function (): void {
        assertSearch(
            collection: $this->patients,
            column: 'mobile_phone',
            page: $this->listPatients,
            value: getPatient()->mobile_phone
        );
    });

    it('searches by tax id', function (): void {
        assertSearch(
            collection: $this->patients,
            column: 'tax_id',
            page: $this->listPatients,
            value: getPatient()->tax_id
        );
    });

    it('searches by created at', function (): void {
        assertSearch(
            collection: $this->patients,
            column: 'created_at',
            page: $this->listPatients,
            value: getPatient()->created_at->format('Y-m-d')
        );
    });
});
