<?php

declare(strict_types=1);

use App\Actions\User\ReverifyEmailAction;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

it('does not reverify if email is not verified', function () {
    $user = User::emailUnverified()->first();

    reverifyEmail($user);

    expect($user->isClean('email_verified_at'))
        ->toBeTrue();

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'email_verified_at' => $user->email_verified_at,
    ]);
});

it('does not reverify if email has not been changed', function () {
    $user = getUserWithEmailVerified();

    reverifyEmail($user);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'email_verified_at' => $user->email_verified_at,
    ]);
});

it('does reverify the e-mail', function () {
    $user = getUserWithEmailVerified();

    $user->email = fake()->email();
    $user->save();

    Notification::fake();
    reverifyEmail($user);

    $user->refresh();

    expect($user->email_verified_at)->toBeNull();
});

/**
 * Re-verifies the email address of the given user.
 *
 * This function utilizes the ReverifyEmailAction to handle the process
 * of re-verifying the email address associated with the provided user.
 *
 * @param  User  $user  The user whose email address needs to be re-verified.
 */
function reverifyEmail(User $user): void
{
    (new ReverifyEmailAction)->handle($user);
}

/**
 * Retrieve the first user with a verified email address.
 *
 * This method queries the database for a user whose email has been verified
 * and returns the first matching user instance.
 *
 * @return User The first user with a verified email address.
 */
function getUserWithEmailVerified(): User
{
    return User::emailVerified()->first();
}
