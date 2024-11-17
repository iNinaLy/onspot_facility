<?php

namespace App\Notifications;

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
            'database', // This will store notification data in `notifications` table
            FcmChannel::class, // This will send FCM notification
        ];
    }

    // Store notification in notifications table
    public function toDatabase($notifiable)
    {
        return [
            'complaint_id' => $this->complaint->id,
            'officer_name' => $this->officer->name,
            'message' => 'A new complaint has been submitted by Officer ' . $this->officer->name,
        ];
    }

    // Use device tokens to send FCM notification
    public function toFcm($notifiable): ?FcmMessage
    {
        $tokens = $notifiable->notificationTokens()->pluck('device_token');

        if ($tokens->isEmpty()) {
            return null; // No tokens to send notification
        }

        return (new FcmMessage(notification: new FcmNotification(
            title: 'New Complaint Submitted',
            body: 'A new complaint has been submitted by Officer ' . $this->officer->name,
            image: null,
        )))
            ->data([
                'complaint_id' => $this->complaint->id,
                'officer_name' => $this->officer->name,
            ])
            ->to($tokens->toArray()) // Specify tokens to send to
            ->custom([
                'android' => [
                    'notification' => [
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    ],
                ],
            ]);
    }
}


