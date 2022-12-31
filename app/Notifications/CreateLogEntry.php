<?php

namespace App\Notifications;

use Auth;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CreateLogEntry extends Notification
{
    use Queueable;

    public $input;

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
        $this->when = $input['wann'] ?? now();
        $this->text = $input['text'] ?? '';
        $this->route_id = $input['route_id'] ?? NULL;
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
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
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
