<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Clinic;

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
        $clinic->users()->attach(
            auth()->user()->id
        );
    }

    /**
     * Handle the Clinic "updated" event.
     */
    public function updated(Clinic $clinic): void
    {
        //
    }

    /**
     * Handle the Clinic "deleted" event.
     */
    public function deleted(Clinic $clinic): void
    {
        //
    }

    /**
     * Handle the Clinic "restored" event.
     */
    public function restored(Clinic $clinic): void
    {
        //
    }

    /**
     * Handle the Clinic "force deleted" event.
     */
    public function forceDeleted(Clinic $clinic): void
    {
        //
    }
}
