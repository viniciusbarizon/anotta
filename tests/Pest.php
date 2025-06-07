<?php

declare(strict_types=1);

require __DIR__ . '/Pest/Helpers/auth.php';

use App\Models\Clinic;
use App\Models\Patient;
use Illuminate\Support\Collection;
use Livewire\Features\SupportTesting\Testable;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->beforeEach(function (): void {
        login();
    })
    ->in('Feature');

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', fn () => $this->toBe(1));

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/


/**
 * Asserts that a table can be sorted by a given column in both ascending and descending order,
 * and that the records are displayed in the correct order.
 *
 * @param Collection $collection The collection of records to be sorted and asserted.
 * @param string $column The column name to sort by.
 * @param Testable $page The page object that provides table sorting and assertion methods.
 * @param int $flag The sorting flag to use (default is SORT_REGULAR).
 *
 * @return void
 */
function assertSort(
    Collection $collection,
    string $column,
    Testable $page,
    int $flag = SORT_REGULAR,
    ?array $sortColumns = null
): void
{
    $page->sortTable($column)
        ->assertCanSeeTableRecords($collection->sortBy($sortColumns ?? $column, $flag), inOrder: true)
        ->sortTable($column, 'desc')
        ->assertCanSeeTableRecords($collection->sortByDesc($sortColumns ?? $column, $flag), inOrder: true);
}

/**
 * Asserts that a table can be sorted by a date column in both ascending and descending order,
 * and that the records are displayed in the correct order based on the formatted date.
 *
 * @param Collection $collection The collection of records to be sorted and asserted.
 * @param string $column The date column name to sort by.
 * @param Testable $page The page object that provides table sorting and assertion methods.
 *
 * @return void
 */
function assertSortDate(Collection $collection, string $column, Testable $page): void
{
    $page->sortTable($column)
        ->assertCanSeeTableRecords(
            $collection->sortBy(fn ($model) => $model->{$column}->format('Y-m-d H:i:s')),
            inOrder: true
        )
        ->sortTable($column, 'desc')
        ->assertCanSeeTableRecords(
            $collection->sortByDesc(fn ($model) => $model->{$column}->format('Y-m-d H:i:s')),
            inOrder: true
        );
}

/**
 * Creates a new Clinic instance using the factory.
 *
 * Optionally allows specifying a name for the clinic.
 *
 * @param  string|null  $name  The name of the clinic. Defaults to null.
 * @return Clinic The created Clinic instance.
 */
function createClinic(?string $name = null): Clinic
{
    if (is_null($name)) {
        return Clinic::factory()->create();
    }

    return Clinic::factory()->create([
        'name' => $name,
    ]);
}

/**
 * Creates and persists a new Patient instance using the factory.
 *
 * @return Patient The newly created Patient model instance.
 */
function createPatient(): Patient
{
    return Patient::factory()->create();
}
