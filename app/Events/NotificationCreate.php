<?php

namespace App\Events;

use App\Models\Action;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationCreate implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $input;

    public Action $action;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Action $action, array $input)
    {
        //
        $this->action = $action;
        $this->input = $input;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('notification-create.'.$this->action['id']);
    }
}
