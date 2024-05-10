<?php

namespace App\View\Components;

use Illuminate\View\Component;

class LogbookItem extends Component
{
    public $user;

    public $content;

    public $time;

    public $id;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($user, $content, $time, $id)
    {
        $this->user = $user;
        $this->content = $content;
        $this->time = $time;
        $this->id = $id;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.logbook-item');
    }
}
