<?php

declare(strict_types=1);

use App\Actions\User\ReverifyEmailAction;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

it('does not reverify if email is not verified', function () {
    $this->user = User::emailUnverified()->first();

    reverifyEmail();

    expect($this->user->isClean('email_verified_at'))
        ->toBeTrue();

    $this->assertDatabaseHas('users', [
        'id' => $this->user->id,
        'email_verified_at' => $this->user->email_verified_at,
    ]);
});

it('does not reverify if email has not been changed', function () {
    $this->user = getUserWithEmailVerified();

    reverifyEmail();

    $this->assertDatabaseHas('users', [
        'id' => $this->user->id,
        'email_verified_at' => $this->user->email_verified_at,
    ]);
});

it('does reverify the e-mail', function () {
    $this->user = getUserWithEmailVerified();

    $this->user->email = fake()->email();
    $this->user->save();

    Notification::fake();
    reverifyEmail();

    $this->user->refresh();

    expect($this->user->email_verified_at)
        ->toBeNull();
});


/**
 * Reverify the email address of the user.
 *
 * This method invokes the `ReverifyEmailAction` to handle the
 * process of re-verifying the email address associated with
 * the current user.
 *
 * @return void
 */
function reverifyEmail(): void
{
    (new ReverifyEmailAction)->handle(
        user: test()->user
    );
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
