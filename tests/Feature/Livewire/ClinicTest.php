<?php

declare(strict_types=1);

use App\Filament\Resources\ClinicResource\Pages\ManageClinics;
use App\Models\Clinic;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\RestoreAction;
use Illuminate\Support\Collection;

use function Pest\Livewire\livewire;

describe('user not super admin', function () {
    beforeEach(function () {
        login();

        $this->clinics = Clinic::withTrashed()->get();
        $this->manageClinics = livewire(ManageClinics::class);
    });

    it('lists', function () {
        test()->manageClinics
            ->assertCanSeeTableRecords(test()->clinics)
            ->assertCanRenderTableColumn('name')
            ->assertCanRenderTableColumn('created_at')
            ->assertCanRenderTableColumn('updated_at')
            ->assertCanRenderTableColumn('deleted_at')
            ->assertCountTableRecords(test()->clinics->count())
            ->assertTableColumnHidden('id');
    });

    it('sorts by name', function () {
        assertSort(column: 'name');
    });

    it('sorts by created_at', function () {
        assertSort(column: 'created_at');
    });

    it('sorts by updated_at', function () {
        edit();

        assertSortWithNull(column: 'updated_at');
    });

    it('sorts by deleted_at', function () {
        delete();

        assertSortWithNull(column: 'deleted_at');
    });

    it('searches by name', function () {
        $name = getClinic()->name;

        test()->manageClinics
            ->searchTable($name)
            ->assertCanSeeTableRecords(test()->clinics->where('name', $name))
            ->assertCanNotSeeTableRecords(test()->clinics->where('name', '!=', $name));
    });

    it('has an deleted filter', function () {
        test()->manageClinics
            ->assertTableFilterExists('trashed');
    });

    it('deletes', function () {
        $id = delete();

        $this->assertSoftDeleted(
            test()->clinics->find($id)
        );
    });

    it('filters by only active', function () {
        delete();

        filterTrashed(
            canNotSee: test()->clinics->whereNotNull('deleted_at'),
            canSee: test()->clinics->whereNull('deleted_at'),
            value: ''
        );
    });

    it('filters by only deleted', function () {
        delete();

        filterTrashed(
            canNotSee: test()->clinics->whereNull('deleted_at'),
            canSee: test()->clinics->whereNotNull('deleted_at'),
            value: 0
        );
    });

    it('filters by active and deleted', function () {
        delete();

        filterTrashed(
            canSee: test()->clinics,
            value: 1
        );
    });

    it('edits', function () {
        $edit = edit();

        expect($edit['clinic'])
            ->name->toBe($edit['name']);
    });

    it('restores', function () {
        $id = delete();

        $clinic = test()->clinics->find($id);

        $this->assertSoftDeleted($clinic);

        livewire(ManageClinics::class)
            ->callTableAction(RestoreAction::class, $id);

        $this->assertNotSoftDeleted($clinic->fresh());
    });

    /**
     * Assert sorting behavior for a table column.
     *
     * This method tests table sorting functionality by:
     * - Sorting the table by the specified column in ascending order
     * - Verifying the table records match the ascending sorted collection
     * - Sorting the table by the specified column in descending order
     * - Verifying the table records match the descending sorted collection
     *
     * @param  string  $column  The column to test sorting on
     */
    function assertSort(string $column): void
    {
        test()->manageClinics
            ->sortTable($column)
            ->assertCanSeeTableRecords(test()->clinics->sortBy($column, SORT_LOCALE_STRING), inOrder: true)
            ->sortTable($column, 'desc')
            ->assertCanSeeTableRecords(test()->clinics->sortByDesc($column, SORT_LOCALE_STRING), inOrder: true);
    }

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
     * @param  Collection|null  $canNotSee  Optional collection of records not expected to be visible
     */
    function filterTrashed(Collection $canSee, int|string $value, ?Collection $canNotSee = null): void
    {
        test()->manageClinics
            ->filterTable('trashed', $value)
            ->assertCanSeeTableRecords($canSee);

        if (is_null($canNotSee) === true) {
            return;
        }

        test()->manageClinics->assertCanNotSeeTableRecords($canNotSee);
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

    /**
     * Retrieve the first clinic from the current test's clinics collection.
     *
     * @return Clinic The first clinic in the collection
     */
    function getClinic(): Clinic
    {
        return test()->clinics->first();
    }
});

describe('user super admin', function () {
    it('shows the ID column', function () {
        login(isSuperAdmin: true);

        livewire(ManageClinics::class)
            ->assertTableColumnVisible('id');
    });
});
