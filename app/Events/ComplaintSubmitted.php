<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class ComplaintSubmitted implements ShouldBroadcast
{
    use SerializesModels;

    public $complaint;

    public function __construct($complaint)
    {
        $this->complaint = $complaint;
    }

    public function broadcastOn()
    {
        // Define the channel that supervisors will listen to
        return new Channel('supervisors');
    }

    public function broadcastAs()
    {
        // Optionally set a custom event name
        return 'complaint.submitted';
    }
}