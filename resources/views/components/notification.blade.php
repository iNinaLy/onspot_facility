<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Notification extends Component
{
    public $unreadNotifications;

    public function __construct($unreadNotifications)
    {
        $this->unreadNotifications = $unreadNotifications;
    }

    public function render()
    {
        return view('components.notification');
    }
}

