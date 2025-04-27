<?php

declare(strict_types=1);

namespace App\Observers;

use App\Actions\ReverifyEmailAction;
use App\Models\User;

final class UserObserver
{
    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        (new ReverifyEmailAction)->handle($user);
    }
}
