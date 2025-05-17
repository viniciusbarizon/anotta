<?php

declare(strict_types=1);

use App\Filament\Resources\ClinicResource\Pages\ManageClinics;
use App\Models\Clinic;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Illuminate\Support\Collection;

use function Pest\Livewire\livewire;

describe('user not super admin', function () {
    beforeEach(function () {
        login();

        $this->clinics = Clinic::all();
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
        assertSort(column: 'updated_at');
    });

    it('sorts by deleted_at', function () {
        delete();

        assertSortDates(column: 'deleted_at');
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
        $clinic = delete();

        $this->assertSoftDeleted($clinic);
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
        $clinic = getClinic();

        test()->manageClinics
            ->callTableAction(EditAction::class, $clinic, data: [
                'name' => $name = fake()->company(),
            ])
            ->assertHasNoTableActionErrors();

        expect($clinic->refresh())
            ->name->toBe($name);
    });

    /**
     * Assert sorting functionality for a given column in the table.
     *
     * This method tests both ascending and descending sorting of table records
     * by verifying the order of records matches the sorted collection.
     *
     * @param  string  $column  The name of the column to test sorting on
     */
    function assertSort(string $column): void
    {
        test()->manageClinics
            ->sortTable($column)
            ->assertCanSeeTableRecords(test()->clinics->sortBy($column), inOrder: true)
            ->sortTable($column, 'desc')
            ->assertCanSeeTableRecords(test()->clinics->sortByDesc($column), inOrder: true);
    }

    /**
     * Assert sorting functionality for date columns in the table.
     *
     * This method tests both ascending and descending sorting of table records
     * by verifying the order of records matches the sorted collection for date-based columns.
     *
     * @param  string  $column  The name of the date column to test sorting on
     */
    function assertSortDates(string $column)
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
     * Delete a clinic and update the clinics collection with trashed records.
     *
     * This method performs a soft delete on the first clinic in the collection,
     * calls the delete table action, and refreshes the clinics collection to include
     * trashed records.
     *
     * @return Clinic The deleted clinic
     */
    function delete(): Clinic
    {
        $clinic = getClinic();

        test()->manageClinics
            ->callTableAction(DeleteAction::class, $clinic);

        test()->clinics = Clinic::withTrashed()->get();

        return $clinic;
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
