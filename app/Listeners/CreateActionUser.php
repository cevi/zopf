<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Models\Action;
use App\Models\ActionUser;

class CreateActionUser
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
    public function handle(UserCreated $event)
    {
        //
        $action = Action::where('global', true)->first();
        $action_user = ActionUser::firstOrCreate(['action_id' => $action['id'], 'user_id' => $event->user['id']]);
        $action_user->update([
            'role_id' => config('status.role_leader'),
        ]);
        if (! $event->user->action) {
            $event->user->update([
                'action_id' => $action['id'],
            ]);
        }
        if (!$event->user->role) {
            $event->user->update([
                'role_id' => config('status.role_leader'),
            ]);
        }
    }
}
