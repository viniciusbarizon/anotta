<?php

declare(strict_types=1);

use App\Actions\User\ReverifyEmailAction;
use Illuminate\Support\Facades\Notification;

/**
 * Reverify the email address of the user.
 *
 * This method invokes the `ReverifyEmailAction` to handle the
 * process of re-verifying the email address associated with
 * the current user.
 */
function reverifyEmail(): void
{
    (new ReverifyEmailAction)->handle(
        user: test()->user
    );
}

it('does not reverify if email is not verified', function () {
    $this->user = createUser(unverified: true);

    reverifyEmail();

    expect($this->user->isClean('email_verified_at'))
        ->toBeTrue();

    $this->assertDatabaseHas('users', [
        'id' => $this->user->id,
        'email_verified_at' => $this->user->email_verified_at,
    ]);
});

it('does not reverify if email has not been changed', function () {
    $this->user = createUser();

    reverifyEmail();

    $this->assertDatabaseHas('users', [
        'id' => $this->user->id,
        'email_verified_at' => $this->user->email_verified_at,
    ]);
});

it('does reverify the e-mail', function () {
    $this->user = createUser();
    $this->user->email = fake()->email();
    $this->user->save();

    Notification::fake();
    reverifyEmail();

    $this->user->refresh();

    expect($this->user->email_verified_at)
        ->toBeNull();
});
