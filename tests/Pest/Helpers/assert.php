<?php

declare(strict_types=1);

use Illuminate\Support\Collection;
use Livewire\Features\SupportTesting\Testable;

/**
 * Asserts that a table search displays only the records matching the given column and value.
 *
 * @param  Collection  $collection  The collection of records to filter and assert against.
 * @param  string  $column  The column name to filter by.
 * @param  string  $value  The value to search for in the specified column.
 * @param  Testable  $page  The page object used to perform the search and assertions.
 */
function assertSearch(Collection $collection, string $column, Testable $page, string $value): void
{
    $page->searchTable($value)
        ->assertCanSeeTableRecords(
            $collection->filter(
                fn ($item) => str_contains(mb_strtolower((string) $item[$column]), mb_strtolower($value))
            )
        )
        ->assertCanNotSeeTableRecords(
            $collection->filter(
                fn ($item) => ! str_contains(mb_strtolower((string) $item[$column]), mb_strtolower($value))
            )
        );
}

/**
 * Asserts that a table can be sorted by a given column in both ascending and descending order,
 * and that the records are displayed in the correct order.
 *
 * @param  Collection  $collection  The collection of records to be sorted and asserted.
 * @param  string  $column  The column name to sort by.
 * @param  Testable  $page  The page object that provides table sorting and assertion methods.
 * @param  int  $flag  The sorting flag to use (default is SORT_REGULAR).
 */
function assertSort(
    Collection $collection,
    string $column,
    Testable $page,
    int $flag = SORT_REGULAR,
    ?array $sortColumns = null
): void {
    $page->sortTable($column)
        ->assertCanSeeTableRecords($collection->sortBy($sortColumns ?? $column, $flag), inOrder: true)
        ->sortTable($column, 'desc')
        ->assertCanSeeTableRecords($collection->sortByDesc($sortColumns ?? $column, $flag), inOrder: true);
}
