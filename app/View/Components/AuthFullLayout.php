<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AuthFullLayout extends Component
{
    public $title;
    public $chrome;

    public function __construct($title = null, $chrome = false)
    {
        $this->title = $title;
        $this->chrome = $chrome;
    }

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.auth-full');
    }
}
