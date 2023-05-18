<?php

namespace App\Notifications;

use Auth;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CreateLogEntry extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var mixed
     */
    public mixed $input;
    /**
     * @var false|mixed
     */
    private mixed $cut;
    /**
     * @var int|mixed
     */
    private mixed $quantity;
    private mixed $user;
    /**
     * @var \Illuminate\Support\Carbon|mixed
     */
    private mixed $when;
    /**
     * @var mixed|string
     */
    private mixed $text;
    /**
     * @var mixed|null
     */
    private mixed $route_id;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($input)
    {
        //
        $this->cut = $input['cut'] ?? false;
        $this->quantity = $input['quantity'] ?? 0;
        $this->user = $input['user'] ?? Auth::user()->username;
        $this->when = $input['when'] ?? now();
        $this->text = $input['text'] ?? '';
        $this->route_id = $input['route_id'] ?? NULL;

//        $this->afterCommit();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [LogbookNotification::class];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'cut' => $this->cut,
            'text' => $this->text,
            'quantity' => $this->quantity,
            'user' => $this->user,
            'when' => $this->when,
            'route_id' => $this->route_id,
            'route_orders' => $this->route_id,
            'route_amount' => $this->route_id,
        ];
    }
}
