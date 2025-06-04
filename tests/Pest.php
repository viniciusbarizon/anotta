<?php

declare(strict_types=1);

use App\Models\Clinic;
use App\Models\Patient;
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

/**
 * Creates a new User instance using the factory.
 *
 * Optionally marks the user as a super admin or unverified.
 *
 * @param  bool  $isSuperAdmin  Whether to create the user as a super admin. Defaults to false.
 * @param  bool  $unverified  Whether to create the user as unverified. Defaults to false.
 * @return User The created User instance.
 */
function createUser(bool $isSuperAdmin = false, bool $unverified = false): User
{
    $user = User::factory();

    if ($isSuperAdmin) {
        $user = $user->superAdmin();
    }

    if ($unverified) {
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
 */
function login(bool $isSuperAdmin = false): void
{
    $user = createUser(isSuperAdmin: $isSuperAdmin);

    /** @var Illuminate\Contracts\Auth\Authenticatable $user */
    actingAs($user);
}

/**
 * Logs out the currently authenticated user from the default authentication guard.
 *
 * This function calls the logout method on the default authentication guard,
 * effectively ending the user's session.
 *
 * @return void
 */
function logout(): void
{
    auth()->guard()->logout();
}
