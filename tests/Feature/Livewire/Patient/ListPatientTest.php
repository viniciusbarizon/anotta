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
 * Creates a collection of 5 Patient instances using the factory.
 *
 * @return Collection A collection of created Patient models.
 */
function createPatients(): Collection
{
    return Patient::factory()
        ->count(5)
        ->create();
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

it('renders the page', fn () => $this->listPatients->assertSuccessful());

describe('not impersonated', function (): void {
    it('lists', function (): void {
        $this->listPatients
            ->assertCanSeeTableRecords($this->patients)
            ->assertCanRenderTableColumn('clinic.name')
            ->assertCanRenderTableColumn('birth_date')
            ->assertCanRenderTableColumn('mobile_phone')
            ->assertCanRenderTableColumn('tax')
            ->assertCanRenderTableColumn('created_at')
            ->assertCountTableRecords(5)
            ->assertTableColumnHidden('id');
    });

    it('sorts by name', function (): void {
        assertSort(
            collection: $this->patients,
            column: 'name',
            page: $this->listPatients
        );
    });

    it('sorts by birth date', function (): void {
        assertSort(
            collection: $this->patients,
            column: 'birth_date',
            page: $this->listPatients
        );
    });

    /* it('sorts by created at', function (): void {
        dd($this->patients);
        assertSortDate(
            collection: $this->patients,
            column: 'created_at',
            //flag: SORT_STRING,
            page: livewire(ListPatients::class)
        );
    }); */
});
