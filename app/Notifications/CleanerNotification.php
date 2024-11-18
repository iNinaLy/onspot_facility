<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmMessage;

class CleanerNotification extends Notification
{
    use Queueable;

    private $complaint;

    public function __construct($complaint)
    {
        $this->complaint = $complaint;
    }

    public function via($notifiable)
    {
        return ['database', 'fcm'];
    }

    public function toFcm($notifiable)
    {
        return FcmMessage::create()
            ->setData([
                'title' => 'New Task Assigned',
                'body' => $this->complaint->comp_desc,
                'complaint_id' => $this->complaint->id,
            ])
            ->setNotification([
                'title' => 'New Task Assigned',
                'body' => "Task at {$this->complaint->comp_location} has been assigned.",
            ]);
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "You have been assigned a new task at {$this->complaint->comp_location}.",
            'complaint_id' => $this->complaint->id,
        ];
    }
}
