<?php

namespace App\View\Components;

use Illuminate\View\Component;

class NotificationItem extends Component
{
    public $user;

    public $content;

    public $time;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($user, $content, $time)
    {
        $this->user = $user;
        $this->content = $content;
        $this->time = $time;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.notification-item');
    }
}
