<?php

namespace App\Listeners;

use App\Events\NotificationCreate;
use App\Notifications\CreateLogEntry;
use Notification;

class NotificationCreateListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(NotificationCreate $event)
    {
        //
        Notification::send($event->action, new CreateLogEntry($event->input));
    }
}
