<?php

use App\Actions\ReverifyEmailAction;
use App\Models\User;

it('does not reverify if email has not been changed', function () {
    $user = User::factory()->create();

    (new ReverifyEmailAction())->handle($user);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'email_verified_at' => $user->email_verified_at,
    ]);
});
