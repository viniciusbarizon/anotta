<?php

declare(strict_types=1);

use App\Actions\ReverifyEmailAction;
use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Support\Facades\Notification;

it('does not reverify if email is not verified', function () {
    $user = getUserWithEmailUnverified();

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

function getUserWithEmailUnverified(): User
{
    return getUserFactory()
        ->unverified()
        ->create();
}

function getUserWithEmailVerified(): User
{
    return getUserFactory()
        ->create();
}

function getUserFactory(): UserFactory
{
    return User::factory();
}

function reverifyEmail(User $user): void
{
    (new ReverifyEmailAction())->handle($user);
}
