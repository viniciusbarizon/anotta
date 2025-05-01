<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Clinic;
use Illuminate\Support\Facades\Auth;

final class ClinicObserver
{
    /**
     * Handle the Clinic "created" event.
     */
    public function created(Clinic $clinic): void
    {
        $clinic->users()->attach(Auth::user()->id);
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
