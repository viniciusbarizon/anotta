<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

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
 */
function logout(): void
{
    auth()->guard()->logout();
}
