<?php

namespace App\Mail;

use App\Models\Action;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ActionCreated extends Mailable
{
    use Queueable, SerializesModels;
    
    protected $action;

    protected $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, Action $action)
    {
        //
        $this->action = $action;
        $this->user = $user;
    }


    public function build()
    {
        return $this->markdown('mail.action-created', ['action' => $this->action])
            ->to($this->user['email'], $this->user['username'])
            ->bcc(config('mail.action.address'), config('mail.action.name'))
            ->subject('Du hast eine neue Zopf-Aktion erstellt.');
    }
}
