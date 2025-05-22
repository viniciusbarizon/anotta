<?php

declare(strict_types=1);

use App\Models\Clinic;
use App\Models\Scopes\UserRelationScope;
use App\Models\User;

use function Pest\Laravel\actingAs;

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

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

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
 * Retrieves the first clinic associated with the currently authenticated user.
 *
 * @return Clinic The first clinic instance belonging to the authenticated user.
 */
function getClinic(): Clinic
{
    return auth()->user()
        ->clinics()
        ->first();
}

/**
 * Retrieves the first clinic associated with a user other than the currently authenticated user.
 *
 * @return Clinic The first clinic instance belonging to another user.
 */
function getClinicFromAnotherUser(): Clinic
{
    return Clinic::query()
        ->withoutGlobalScope(UserRelationScope::class)
        ->whereRelation('users', 'id', '!=', auth()->user()->id)
        ->first();
}

/**
 * Logs in a random email verified user for testing purposes.
 *
 * This helper function selects a random email verified user from the database
 * and sets them as the authenticated user for the current test context.
 */
function login(bool $isSuperAdmin = false): void
{
    /* $user = User::emailVerified()
        ->where('is_super_admin', $isSuperAdmin)
        ->inRandomOrder()
        ->firstOrFail();

    actingAs($user); */
    $user = User::factory()->create();

    /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
    actingAs($user);
}