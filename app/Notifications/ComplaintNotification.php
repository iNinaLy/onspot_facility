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
                'cleaner_name' => $this->cleaner->name,
                'supervisor_name' => $this->supervisor->name,
                'message' => 'You have been assigned a new complaint by Supervisor ' . $this->supervisor->name,
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
            return (new FcmMessage(notification: new FcmNotification(
                title: 'New Complaint Assigned',
                body: 'You have been assigned a new complaint by Supervisor ' . $this->supervisor->name,
                image: null,
            )))
                ->data([
                    'complaint_id' => $this->complaint->id,
                    'supervisor_name' => $this->supervisor->name,
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

    // Static helper to notify cleaner and officer
    public static function notifyAssignment($complaint, $officer, $cleaner, $supervisor)
    {
        // Notify Cleaner
        if ($cleaner) {
            $cleaner->notify(new self($complaint, $officer, $cleaner, $supervisor));
        }

        // Notify Officer
        if ($officer) {
            $officer->notify(new self($complaint, $officer));
        }
    }
}