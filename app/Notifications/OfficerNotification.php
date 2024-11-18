<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmMessage;

class OfficerNotification extends Notification
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
                'title' => 'Cleaners Assigned',
                'body' => $this->complaint->comp_desc,
                'complaint_id' => $this->complaint->id,
            ])
            ->setNotification([
                'title' => 'Cleaners Assigned',
                'body' => "Cleaners have been assigned to your complaint at {$this->complaint->comp_location}.",
            ]);
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "Cleaners have been assigned to your complaint at {$this->complaint->comp_location}.",
            'complaint_id' => $this->complaint->id,
        ];
    }
}
