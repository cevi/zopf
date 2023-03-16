<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class LogbookNotification
{

    public function send($notifiable, Notification $notification)
    {
        $data = $notification->toDatabase($notifiable);

        // set custom message in another variable and unset it from default array.
        $quantity = $data['quantity'];
        $cut = $data['cut'];
        $user = $data['user'];
        $when = $data['when'];
        $text = $data['text'];
        $route_id = $data['route_id'];

        // lets create a DB row now with our custom field message text

        return $notifiable->routeNotificationFor('database')->create([

            'id' => $notification->id,
            'quantity' => $quantity, //<-- comes from toDatabase() Method, this is my customised column
            'cut' => $cut, //<-- comes from toDatabase() Method, this is my customised column
            'user' => $user, //<-- comes from toDatabase() Method, this is my customised column
            'when' => $when, //<-- comes from toDatabase() Method, this is my customised column
            'route_id' => $route_id, //<-- comes from toDatabase() Method, this is my customised column
            'notifiable_type'=> \Auth::user()->id ?? 1,
            'type' => get_class($notification),
            'content' => $text,
            'read_at' => null,
            'data' => $data,
        ]);
    }
}
