<?php

declare(strict_types=1);

use App\Filament\Resources\ClinicResource\Pages\ManageClinics;
use App\Models\Clinic;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Illuminate\Support\Collection;
use Livewire\Features\SupportTesting\Testable;

use function Pest\Livewire\livewire;

setlocale(LC_COLLATE, 'pt_PT.UTF-8');

beforeEach(function (): void {
    $this->clinics = createMultiples(Clinic::class);
    $this->manageClinics = getResource();
});

/**
 * Create a Livewire testable instance for the ManageClinics resource.
 *
 * @return Testable A Livewire testable component for the ManageClinics page
 */
function getResource(): Testable
{
    return livewire(ManageClinics::class);
}

describe('not impersonated', function (): void {
    /**
     * Assert sorting behavior for a given column, including null value handling.
     *
     * This method tests table sorting functionality by:
     * - Retrieving clinics ordered by the specified column in ascending and descending order
     * - Sorting the table by the column in both directions
     * - Verifying that the table records match the expected sorted order
     *
     * @param  string  $column  The column to test sorting on
     */
    function assertSortWithNull(string $column): void
    {
        $asc = getClinicsOrderedBy(column: $column);
        $desc = getClinicsOrderedBy(column: $column, orderBy: 'desc');

        test()->manageClinics
            ->sortTable($column)
            ->assertCanSeeTableRecords($asc, inOrder: true)
            ->sortTable($column, 'desc')
            ->assertCanSeeTableRecords($desc, inOrder: true);
    }

    /**
     * Delete a clinic and return its ID.
     *
     * This method performs a delete table action on a clinic,
     * refreshes the clinics collection, and returns the deleted clinic's ID.
     *
     * @return string The ID of the deleted clinic
     */
    function delete(): string
    {
        $clinic = getClinic();

        test()->manageClinics
            ->callTableAction(DeleteAction::class, $clinic);

        test()->clinics = test()->clinics->fresh();

        return $clinic->id;
    }

    /**
     * Deletes a bulk of clinics (specifically the first two) using the DeleteBulkAction.
     *
     * Retrieves the first two clinics from the test context, performs a bulk delete action
     * on them via the manageClinics component, and returns the collection of clinics that were targeted.
     *
     * @return Collection The collection of clinics that were selected for deletion.
     */
    function deleteInBulk(): Collection
    {
        $clinics = test()->clinics->take(2);

        test()->manageClinics
            ->callTableBulkAction(DeleteBulkAction::class, $clinics);

        return $clinics;
    }

    /**
     * Edit a clinic and return the updated clinic and new name.
     *
     * This method performs an edit table action on the first clinic in the collection,
     * updates the clinic's name with a fake company name, and verifies no table action errors.
     *
     * @return array An array containing the original clinic and the new name
     */
    function edit(): array
    {
        $clinic = getClinic();

        test()->manageClinics
            ->callTableAction(EditAction::class, $clinic, data: [
                'name' => $name = fake()->company(),
            ])
            ->assertHasNoTableActionErrors();

        test()->clinics = test()->clinics->fresh();

        return [
            'clinic' => $clinic->fresh(),
            'name' => $name,
        ];
    }

    /**
     * Filter table records by trashed status and assert visibility.
     *
     * This method filters a table by its trashed status and verifies the visibility
     * of records based on the provided collections. It allows checking which records
     * are displayed after applying a trashed filter.
     *
     * @param  Collection  $canSee  Collection of records expected to be visible after filtering
     * @param  int|string  $value  The trashed filter value (e.g., 0 for non-trashed, 1 for trashed)
     * @param  Collection|null  $collection  Optional collection of records not expected to be visible
     */
    function filterTrashed(Collection $canSee, int|string $value, ?Collection $canNotSee = null): void
    {
        test()->manageClinics
            ->filterTable('trashed', $value)
            ->assertCanSeeTableRecords($canSee);

        if (is_null($canNotSee)) {
            return;
        }

        test()->manageClinics
            ->assertCanNotSeeTableRecords($canNotSee);
    }

    /**
     * Retrieves the first Clinic instance from the test clinics collection.
     *
     * @return Clinic The first Clinic object from the test clinics.
     */
    function getClinic(): Clinic
    {
        return test()->clinics->first();
    }

    /**
     * Retrieve clinics ordered by a specific column and sort direction.
     *
     * This method fetches all clinics, including trashed records, and orders them
     * by the specified column in the given sort direction.
     *
     * @param  string  $column  The column to order the clinics by
     * @param  string  $orderBy  The sort direction ('asc' or 'desc'), defaults to 'asc'
     * @return Collection A collection of clinics ordered by the specified column
     */
    function getClinicsOrderedBy(string $column, string $orderBy = 'asc'): Collection
    {
        return Clinic::query()
            ->withTrashed()
            ->orderBy($column, $orderBy)
            ->get();
    }

    it('has all actions in expected order', function (): void {
        $this->manageClinics
            ->assertTableActionsExistInOrder(['edit', 'delete', 'restore'])
            ->assertTableActionDoesNotExist('forceDelete')
            ->assertTableBulkActionsExistInOrder(['delete', 'restore']);
    });

    it('lists', function (): void {
        $this->manageClinics
            ->assertCanSeeTableRecords($this->clinics)
            ->assertCanRenderTableColumn('name')
            ->assertCanRenderTableColumn('created_at')
            ->assertCountTableRecords(10)
            ->assertTableColumnHidden('id');
    });

    it('sorts by name', function (): void {
        echo 'Locale ativo: ' . setlocale(LC_COLLATE, 0) . PHP_EOL;
        assertSort(
            collection: test()->clinics,
            column: 'name',
            flag: SORT_LOCALE_STRING,
            page: $this->manageClinics
        );
    });

    it('sorts by created at', function (): void {
        assertSort(
            collection: test()->clinics,
            column: 'created_at',
            page: $this->manageClinics
        );
    });

    it('searches by name', function (): void {
        assertSearch(
            collection: test()->clinics,
            column: 'name',
            page: $this->manageClinics,
            value: getClinic()->name
        );
    });

    it('has an deleted filter', fn () => $this->manageClinics->assertTableFilterExists('trashed'));

    it('filters by only active', function (): void {
        delete();

        filterTrashed(
            canNotSee: test()->clinics->whereNotNull('deleted_at'),
            canSee: test()->clinics->whereNull('deleted_at'),
            value: ''
        );
    });

    it('filters by only deleted', function (): void {
        assertOnlyDeleted(
            collection: test()->clinics,
            page: $this->manageClinics
        );
    });

    it('filters by active and deleted', function (): void {
        delete();

        filterTrashed(
            canSee: test()->clinics,
            value: 1
        );
    });

    it('validates name as required when creating', function (): void {
        $this->manageClinics
            ->callAction('create', data: [
                'name' => null,
            ])
            ->assertHasActionErrors(['name' => ['required']]);
    });

    it('validates name as unique when creating', function (): void {
        $name = getClinic()->name;

        $this->manageClinics
            ->callAction('create', data: [
                'name' => $name,
            ])
            ->assertHasActionErrors(['name' => 'Este nome de clínica já está associado à tua conta.']);
    });

    it('creates', function (): void {
        $name = fake()->company();

        $this->manageClinics
            ->callAction('create', data: [
                'name' => $name,
            ])
            ->assertHasNoActionErrors();

        $this->assertDatabaseHas(Clinic::class, [
            'name' => $name,
        ]);
    });

    it('validates name as required when editing', function (): void {
        $clinic = getClinic();

        $this->manageClinics
            ->callTableAction(EditAction::class, $clinic, data: [
                'name' => null,
            ])
            ->assertHasTableActionErrors(['name' => ['required']]);
    });

    it('edits', function (): void {
        $edit = edit();

        expect($edit['clinic'])
            ->name->toBe($edit['name']);
    });

    it('deletes', function (): void {
        $id = delete();

        $this->assertSoftDeleted(
            test()->clinics->find($id)
        );
    });

    it('restores', function (): void {
        $id = delete();

        $clinic = test()->clinics->find($id);

        $this->manageClinics
            ->callTableAction(RestoreAction::class, $id);

        $this->assertNotSoftDeleted($clinic->fresh());
    });

    it('deletes in bulk', function (): void {
        $clinics = deleteInBulk();

        foreach ($clinics as $clinic) {
            $this->assertSoftDeleted($clinic);
        }
    });

    it('restores in bulk', function (): void {
        $clinics = deleteInBulk();

        $this->manageClinics
            ->callTableBulkAction(RestoreBulkAction::class, $clinics);

        foreach ($clinics as $clinic) {
            $this->assertNotSoftDeleted($clinic);
        }
    });
});

describe('impersonated', function (): void {
    beforeEach(function (): void {
        impersonateUser();
    });

    it('shows the id column', function (): void {
        getResource()->assertTableColumnVisible('id');
    });

    it('searches by id', function (): void {
        $id = getClinic()->id;

        $this->manageClinics
            ->searchTable($id)
            ->assertCanSeeTableRecords(test()->clinics->where('id', $id))
            ->assertCanNotSeeTableRecords(test()->clinics->where('id', '!=', $id));
    });
});
