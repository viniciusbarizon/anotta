<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\User;
use Filament\Facades\Filament;
use Filament\Notifications\Auth\VerifyEmail;

final class UserObserver
{
    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        if ($user->isClean('email')) {
            return;
        }

        $user->email_verified_at = null;
        $user->saveQuietly();

        $notification = app(VerifyEmail::class);
        $notification->url = Filament::getVerifyEmailUrl($user);

        $user->notify($notification);
    }
}
