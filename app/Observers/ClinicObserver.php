<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Clinic;
use App\Models\User;

final class ClinicObserver
{
    /**
     * Handle the Clinic "created" event.
     *
     * This method is triggered when a new Clinic instance is created.
     * It attaches the currently authenticated user to the clinic's users.
     *
     * @param  Clinic  $clinic  The newly created Clinic instance.
     */
    public function created(Clinic $clinic): void
    {
        /** @var User $user */
        $user = auth()->user();

        $clinic->users()->attach(
            $user->id
        );
    }
}
