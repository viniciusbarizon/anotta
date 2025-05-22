<?php

declare(strict_types=1);

use App\Models\Clinic;
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
 * Creates a new Clinic instance using the factory.
 *
 * Optionally allows specifying a name for the clinic.
 *
 * @param  string|null  $name  The name of the clinic. Defaults to null.
 * @return Clinic The created Clinic instance.
 */
function createClinic(?string $name = null): Clinic
{
    if (is_null($name) === true) {
        return Clinic::factory()->create();
    }

    return Clinic::factory()->create([
        'name' => $name,
    ]);
}

/**
 * Creates a new User instance using the factory.
 *
 * Optionally marks the user as a super admin or unverified.
 *
 * @param  bool  $isSuperAdmin  Whether to create the user as a super admin. Defaults to false.
 * @param  bool  $unverified    Whether to create the user as unverified. Defaults to false.
 * @return User The created User instance.
 */
function createUser(bool $isSuperAdmin = false, bool $unverified = false): User
{
    $user = User::factory();

    if ($isSuperAdmin === true) {
        $user = $user->superAdmin();
    }

    if ($unverified === true) {
        $user = $user->unverified();
    }

    return $user->create();
}

/**
 * Creates a new User instance using the factory and logs in the user.
 *
 * Optionally marks the user as a super admin if the $isSuperAdmin parameter is true.
 *
 * @param  bool  $isSuperAdmin  Whether to create the user as a super admin. Defaults to false.
 * @return void
 */
function login(bool $isSuperAdmin = false): void
{
    $user = createUser(isSuperAdmin: $isSuperAdmin);

    /** @var Illuminate\Contracts\Auth\Authenticatable $user */
    actingAs($user);
}
