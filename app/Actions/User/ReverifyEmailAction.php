<?php

declare(strict_types=1);

namespace App\Actions\User;

use App\Models\User;
use Filament\Facades\Filament;
use Filament\Notifications\Auth\VerifyEmail;

final class ReverifyEmailAction
{
    private User $user;

    /**
     * Handles the process of re-verifying a user's email address.
     *
     * @param  mixed  $user  The user object whose email needs to be re-verified.
     * @return void
     *
     * This method checks if the user's email has been modified. If the email
     * has not changed (is clean), the method exits early. Otherwise, it
     * proceeds to unverify the email and send a new verification request.
     */
    public function handle(User $user): void
    {
        $this->user = $user;

        if ($this->user->isClean('email')) {
            return;
        }

        $this->unverifyEmail();
        $this->sendVerification();
    }

    /**
     * Marks the user's email as unverified by setting the `email_verified_at` attribute to null
     * and saves the change to the database without triggering any model events.
     */
    private function unverifyEmail(): void
    {
        $this->user->email_verified_at = null;
        $this->user->saveQuietly();
    }

    /**
     * Sends a verification email to the user.
     *
     * This method creates an instance of the VerifyEmail notification,
     * sets the verification URL using the Filament helper, and sends
     * the notification to the user.
     */
    private function sendVerification(): void
    {
        $notification = app(VerifyEmail::class);
        $notification->url = Filament::getVerifyEmailUrl($this->user);

        $this->user->notify($notification);
    }
}
