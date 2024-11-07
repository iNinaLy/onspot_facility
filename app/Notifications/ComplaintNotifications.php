<?php

namespace App\Notifications;

use Illuminate\Notifications\Channels\DatabaseChannel;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class ComplaintNotification extends Notification
{
    protected $complaint;
    protected $officer;

    public function __construct($complaint, $officer)
    {
        $this->complaint = $complaint;
        $this->officer = $officer;
    }

    public function via($notifiable)
    {
        return [
            DatabaseChannel::class,
            FcmChannel::class,
        ];
    }

    public function toFcm($notifiable): FcmMessage
    {
        return (new FcmMessage(notification: new FcmNotification(
            title: 'New Complaint Submitted',
            body: 'A new complaint has been submitted by Officer ' . $this->officer->name,
            image: null,
        )))
            ->data([
                'complaint_id' => $this->complaint->id,
                'officer_name' => $this->officer->name,
            ])
            ->custom([
                'android' => [
                    'notification' => [
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    ],
                ],
            ]);
    }

    public function toDatabase($notifiable)
    {
        return [
            'complaint_id' => $this->complaint->id,
            'officer_name' => $this->officer->name,
            'message' => 'A new complaint has been submitted by Officer ' . $this->officer->name,
        ];
    }
}
