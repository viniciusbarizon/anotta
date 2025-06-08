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
        Patient::factory()->create([
            'created_at' => now()->subDays($i),
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

function getPatientInfo(string $column): string
{
    return test()->patients
        ->first()
        ->{$column};
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

    it('sorts by full name', function (): void {
        assertSort(
            collection: $this->patients,
            column: 'full_name',
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

    it('searches by full name', function (): void {
        assertSearch(
            collection: $this->patients,
            column: 'full_name',
            page: $this->listPatients,
            value: getPatientInfo(column: 'full_name')
        );
    });
});
