<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class complaintNotification extends Notification
{
    protected $complaint;
    protected $officer;
    protected $cleaners; // For notifying assigned cleaners
    protected $isAssignment; // Flag to differentiate assignment notifications

    public function __construct($complaint, $officer, $cleaners = null, $isAssignment = false)
    {
        $this->complaint = $complaint;
        $this->officer = $officer;
        $this->cleaners = $cleaners; // Optional for assigned cleaners
        $this->isAssignment = $isAssignment; // Flag to check assignment notifications
    }

    public function via($notifiable)
    {
        return [
            'database',
            FcmChannel::class,
        ];
    }

    // Store notification in notifications table
    public function toDatabase($notifiable)
    {
        if ($this->cleaners && $this->isAssignment) {
            // Notification for assigned cleaners
            return [
                'complaint_id' => $this->complaint->id,
                'message' => 'You have been assigned to Complaint #' . $this->complaint->id,
            ];
        } elseif ($this->isAssignment) {
            // Notification for the officer about cleaner assignment
            return [
                'complaint_id' => $this->complaint->id,
                'message' => 'Cleaners have been assigned to your Complaint #' . $this->complaint->id,
            ];
        } else {
            // Notification for supervisors about a new complaint
            return [
                'complaint_id' => $this->complaint->id,
                'officer_name' => $this->officer->name,
                'message' => 'A new complaint has been submitted by Officer ' . $this->officer->name,
            ];
        }
    }

    // Use FCM to notify devices
    public function toFcm($notifiable): ?FcmMessage
    {
        $tokens = $notifiable->notificationTokens()->pluck('device_token');

        if ($tokens->isEmpty()) {
            return null; // No tokens to send notification
        }

        if ($this->cleaners && $this->isAssignment) {
            // FCM message for assigned cleaners
            return (new FcmMessage(notification: new FcmNotification(
                title: 'New Task Assigned',
                body: 'You have been assigned to Complaint #' . $this->complaint->id,
                image: null,
            )))
                ->data([
                    'complaint_id' => $this->complaint->id,
                ])
                ->to($tokens->toArray())
                ->custom([
                    'android' => [
                        'notification' => [
                            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                        ],
                    ],
                ]);
        } elseif ($this->isAssignment) {
            // FCM message for the officer about cleaner assignment
            return (new FcmMessage(notification: new FcmNotification(
                title: 'Cleaners Assigned to Your Complaint',
                body: 'Cleaners have been assigned to your Complaint #' . $this->complaint->id,
                image: null,
            )))
                ->data([
                    'complaint_id' => $this->complaint->id,
                ])
                ->to($tokens->toArray())
                ->custom([
                    'android' => [
                        'notification' => [
                            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                        ],
                    ],
                ]);
        } else {
            // FCM message for supervisors about a new complaint
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
}
