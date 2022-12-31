<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

//Broadcast::channel('App.User.{id}', function ($user, $id) {
//    return (int) $user->id === (int) $id;
//});

use App\Models\Action;

Broadcast::channel('notification-create.{actionId}', function ($user, $actionId) {
    return true;
//    return $user->action['id'] === $action->id;
});
