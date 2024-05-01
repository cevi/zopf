<?php

namespace App\View\Components;

use Illuminate\View\Component;

class PageTitle extends Component
{
    public $help;

    public $title;

    public $header;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($title, $help = null, $header = true)
    {
        //
        $this->title = $title;
        $this->help = $help;
        $this->header = $header;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.page-title');
    }
}
