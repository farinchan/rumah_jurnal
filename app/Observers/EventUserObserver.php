<?php

namespace App\Observers;

use App\Models\EventUser;

class EventUserObserver
{
    /**
     * Handle the EventUser "created" event.
     */
    public function created(EventUser $eventUser): void
    {
        //
    }

    /**
     * Handle the EventUser "updated" event.
     */
    public function updated(EventUser $eventUser): void
    {
        //
    }

    /**
     * Handle the EventUser "deleted" event.
     */
    public function deleted(EventUser $eventUser): void
    {
        //
    }

    /**
     * Handle the EventUser "restored" event.
     */
    public function restored(EventUser $eventUser): void
    {
        //
    }

    /**
     * Handle the EventUser "force deleted" event.
     */
    public function forceDeleted(EventUser $eventUser): void
    {
        //
    }
}
