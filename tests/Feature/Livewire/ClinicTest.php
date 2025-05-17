<?php

declare(strict_types=1);

use App\Filament\Resources\ClinicResource\Pages\ManageClinics;
use App\Models\Clinic;

use function Pest\Livewire\livewire;

describe('user not super admin', function () {
    beforeEach(function () {
        login();

        $this->clinics = Clinic::all();
        $this->manageClinics = livewire(ManageClinics::class);
    });

    it('can list', function () {
        test()->manageClinics
            ->assertCanSeeTableRecords(test()->clinics)
            ->assertCanRenderTableColumn('name')
            ->assertCanRenderTableColumn('created_at')
            ->assertCanRenderTableColumn('updated_at')
            ->assertCanRenderTableColumn('deleted_at')
            ->assertCountTableRecords(test()->clinics->count())
            ->assertTableColumnHidden('id');
    });

    it('can sort by name', function () {
        assertSort(column: 'name');
    });

    it('can sort by created_at', function () {
        assertSort(column: 'created_at');
    });

    it('can sort by updated_at', function () {
        assertSort(column: 'updated_at');
    });

    it('can sort by deleted_at', function () {
        assertSort(column: 'deleted_at');
    });

    it('can search by name', function () {
        $name = test()->clinics->first()->name;

        test()->manageClinics
            ->searchTable($name)
            ->assertCanSeeTableRecords(test()->clinics->where('name', $name))
            ->assertCanNotSeeTableRecords(test()->clinics->where('name', '!=', $name));
    });

    it('has an trashed filter', function () {
        test()->manageClinics
            ->assertTableFilterExists('trashed');
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
});

describe('user super admin', function () {
    it('must list ID column if the user is super admin', function () {
        login(isSuperAdmin: true);

        livewire(ManageClinics::class)
            ->assertTableColumnVisible('id');
    });
});
