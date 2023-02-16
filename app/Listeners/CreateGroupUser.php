<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Models\Group;
use App\Models\GroupUser;

class CreateGroupUser
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
        $group = Group::where('global', true)->first();
        $group_user = GroupUser::firstOrCreate(['group_id' => $group['id'], 'user_id' => $event->user['id']]);
        $group_user->update([
            'role_id' => config('status.role_leader'),
        ]);
        if (! $event->user->group) {
            $event->user->update([
                'group_id' => $group['id'],
            ]);
        }
        if (! $event->user->role) {
            $event->user->update([
                'role_id' => config('status.role_leader'),
            ]);
        }
    }
}
