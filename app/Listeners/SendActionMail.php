<?php

namespace App\Listeners;

use App\Events\ActionCreated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class SendActionMail
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
     * @param  object  $event
     * @return void
     */
    public function handle(ActionCreated $event)
    {
        //
        Mail::send(new \App\Mail\ActionCreated(Auth::user(), $event->action));
    }
}
