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
    protected $cleaner;
    protected $supervisor;

    public function __construct($complaint, $officer, $cleaner = null, $supervisor = null)
    {
        $this->complaint = $complaint;
        $this->officer = $officer;
        $this->cleaner = $cleaner;
        $this->supervisor = $supervisor;
    }

    public function via($notifiable)
    {
        return [
            'database', // This will store notification data in notifications table
            FcmChannel::class, // This will send FCM notification
        ];
    }

    // Store notification in notifications table
    public function toDatabase($notifiable)
    {
        if ($this->cleaner) {
            return [
                'complaint_id' => $this->complaint->id,
                'message' => 'You have been assigned a new complaint.',
            ];
        }

        if ($this->officer) {
            return [
                'complaint_id' => $this->complaint->id,
                'message' => 'Your complaint has been updated.',
            ];
        }

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

        if ($this->cleaner) {
            // Notification for cleaner
            return (new FcmMessage(notification: new FcmNotification(
                title: 'New Complaint Assigned',
                body: 'You have been assigned a new complaint.',
                image: null,
            )))
                ->data([
                    'complaint_id' => $this->complaint->id,
                    'cleaner_name' => $this->cleaner->name,
                ])
                ->to($tokens->toArray())
                ->custom([
                    'android' => [
                        'notification' => [
                            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                        ],
                    ],
                ]);
        }

        if ($this->officer) {
            // Notification for officer
            return (new FcmMessage(notification: new FcmNotification(
                title: 'Complaint Updates',
                
                image: null,
            )))
                ->data([
                    'complaint_id' => $this->complaint->id,
                    'officer_name' => $this->officer->name,
                ])
                ->to($tokens->toArray())
                ->custom([
                    'android' => [
                        'notification' => [
                            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                        ],
                    ],
                ]);
        }

        // Default notification
        return (new FcmMessage(notification: new FcmNotification(
            title: 'New Complaint Submitted',
            body: 'A new complaint has been submitted by Officer ' . $this->officer->name,
            image: null,
        )))
            ->data([
                'complaint_id' => $this->complaint->id,
                'officer_name' => $this->officer->name,
            ])
            ->to($tokens->toArray())
            ->custom([
                'android' => [
                    'notification' => [
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    ],
                ],
            ]);
    }
}
