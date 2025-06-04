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
 * Creates a collection of 10 Patient instances using the factory.
 *
 * @return Collection A collection of created Patient models.
 */
function createPatients(): Collection
{
    return Patient::factory()
        ->count(10)
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

describe('not impersonated', function (): void {
    it('renders the page', function () {
        $this->listPatients->assertSuccessful();
    });

    it('lists', function (): void {
        $this->listPatients
            ->assertCanSeeTableRecords($this->patients)
            ->assertCanRenderTableColumn('clinic.name')
            ->assertCanRenderTableColumn('birth_date')
            ->assertCanRenderTableColumn('mobile_phone')
            ->assertCanRenderTableColumn('tax')
            ->assertCanRenderTableColumn('created_at')
            ->assertCountTableRecords(10)
            ->assertTableColumnHidden('id');
    });
});
