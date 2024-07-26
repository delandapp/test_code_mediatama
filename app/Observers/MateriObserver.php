<?php

namespace App\Observers;

use App\Models\Materi\Materi;
use Illuminate\Support\Facades\Auth;

class MateriObserver
{
    /**
     * Handle the Materi "created" event.
     */
    public function created(Materi $materi): void
    {
        $materi->id_user = Auth::id();
    }

    /**
     * Handle the Materi "updated" event.
     */
    public function updated(Materi $materi): void
    {
        //
    }

    /**
     * Handle the Materi "deleted" event.
     */
    public function deleted(Materi $materi): void
    {
        //
    }

    /**
     * Handle the Materi "restored" event.
     */
    public function restored(Materi $materi): void
    {
        //
    }

    /**
     * Handle the Materi "force deleted" event.
     */
    public function forceDeleted(Materi $materi): void
    {
        //
    }
}
