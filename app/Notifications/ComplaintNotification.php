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

    // Constructor to initialize the complaint, officer, and optionally the cleaners
    public function __construct($complaint, $officer, $cleaners = null, $isAssignment = false)
    {
        $this->complaint = $complaint;
        $this->officer = $officer;
        $this->cleaners = $cleaners; // Optional for assigned cleaners
        $this->isAssignment = $isAssignment; // Flag to check assignment notifications
    }

    // Define the channels the notification should be sent through
    public function via($notifiable)
    {
        return [
            'database', // Save to database notifications table
            FcmChannel::class, // Send FCM push notifications
        ];
    }

    // Store the notification in the database table
    public function toDatabase($notifiable)
    {
        // Check if it's an assignment notification to cleaners
        if ($this->cleaners && $this->isAssignment) {
            return [
                'complaint_id' => $this->complaint->id,
                'message' => 'You have been assigned to Complaint #' . $this->complaint->id,
            ];
        } 
        // Check if it's a notification for the officer about cleaner assignment
        elseif ($this->isAssignment) {
            return [
                'complaint_id' => $this->complaint->id,
                'message' => 'Cleaners have been assigned to your Complaint #' . $this->complaint->id,
            ];
        } 
        // Default notification for a new complaint submitted by officer
        else {
            return [
                'complaint_id' => $this->complaint->id,
                'officer_name' => $this->officer->name,
                'message' => 'A new complaint has been submitted by Officer ' . $this->officer->name,
            ];
        }
    }

    // Send the FCM notification to devices
    public function toFcm($notifiable): ?FcmMessage
    {
        $tokens = $notifiable->notificationTokens()->pluck('device_token');

        if ($tokens->isEmpty()) {
            return null; // No tokens available to send notifications
        }

        // If it's an assignment notification to cleaners
        if ($this->cleaners && $this->isAssignment) {
            return (new FcmMessage(notification: new FcmNotification(
                title: 'New Task Assigned',
                body: 'You have been assigned to Complaint #' . $this->complaint->id,
                image: null,
            )))
                ->data([
                    'complaint_id' => $this->complaint->id, // Send complaint_id in FCM payload
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

        // If it's for the officer, informing them about cleaner assignment
        elseif ($this->isAssignment) {
            return (new FcmMessage(notification: new FcmNotification(
                title: 'Cleaners Assigned to Your Complaint',
                body: 'Cleaners have been assigned to your Complaint #' . $this->complaint->id,
                image: null,
            )))
                ->data([
                    'complaint_id' => $this->complaint->id, // Include complaint ID in data
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

        // If it's for supervisors about a new complaint
        return (new FcmMessage(notification: new FcmNotification(
            title: 'New Complaint Submitted',
            body: 'A new complaint has been submitted by Officer ' . $this->officer->name,
            image: null,
        )))
        ->data([
            'complaint_id' => $this->complaint->id, // Pass complaint ID
            'officer_name' => $this->officer->name, // Optional, for context
        ])
        ->to($tokens->toArray())
        ->custom([
            'android' => [
                'notification' => [
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK', // Required for navigation
                ],
            ],
        ]);
    }
}
