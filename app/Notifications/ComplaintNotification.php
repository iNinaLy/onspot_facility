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
    protected $cleaners;
    protected $isAssignment;

    /**
     * Initialize the notification with complaint, officer, cleaners, and assignment flag.
     */
    public function __construct($complaint, $officer, $cleaners = null, $isAssignment = false)
    {
        $this->complaint = $complaint;
        $this->officer = $officer;
        $this->cleaners = $cleaners;
        $this->isAssignment = $isAssignment;
    }

    /**
     * Define the delivery channels for the notification.
     */
    public function via($notifiable)
    {
        return ['database', FcmChannel::class];
    }

    /**
     * Store the notification in the database table.
     */
    public function toDatabase($notifiable)
    {
        // Include 'type' to distinguish notification types
        if ($this->cleaners && $this->isAssignment) {
            // Assignment notification for cleaners
            return [
                'type' => 'cleaner_assignment',
                'complaint_id' => $this->complaint->id,
                'message' => 'You have been assigned to Complaint #' . $this->complaint->id,
                'comp_location' => $this->complaint->comp_location,
                'assigned_date' => $this->complaint->assigned_date,
            ];
        } elseif ($this->isAssignment) {
            // Notification for officers about cleaner assignment
            return [
                'type' => 'officer_assignment',
                'complaint_id' => $this->complaint->id,
                'message' => 'Cleaners have been assigned to your Complaint #' . $this->complaint->id,
                'no_of_cleaners' => $this->complaint->no_of_cleaners,
                'assigned_by' => $this->complaint->assigned_by,
            ];
        } else {
            // Default notification for supervisors about new complaints
            return [
                'type' => 'new_complaint',
                'complaint_id' => $this->complaint->id,
                'officer_name' => $this->officer->name,
                'message' => 'A new complaint has been submitted by Officer ' . $this->officer->name,
                'comp_desc' => $this->complaint->comp_desc,
                'comp_location' => $this->complaint->comp_location,
                'comp_date' => $this->complaint->comp_date,
                'comp_time' => $this->complaint->comp_time,
            ];
        }
    }

    /**
     * Send the notification via FCM.
     */
    public function toFcm($notifiable): ?FcmMessage
    {
        $tokens = $notifiable->notificationTokens()->pluck('device_token');

        if ($tokens->isEmpty()) {
            return null;
        }

        if ($this->cleaners && $this->isAssignment) {
            // Assignment notification for cleaners
            return (new FcmMessage(notification: new FcmNotification(
                title: 'New Task Assigned',
                body: 'You have been assigned to Complaint #' . $this->complaint->id . ' at ' . $this->complaint->comp_location,
            )))
                ->data([
                    'type' => 'cleaner_assignment',
                    'complaint_id' => $this->complaint->id,
                    'comp_location' => $this->complaint->comp_location,
                    'assigned_date' => $this->complaint->assigned_date,
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
            // Notification for officers about cleaner assignment
            return (new FcmMessage(notification: new FcmNotification(
                title: 'Cleaners Assigned',
                body: 'Cleaners have been assigned to your Complaint #' . $this->complaint->id,
            )))
                ->data([
                    'type' => 'officer_assignment',
                    'complaint_id' => $this->complaint->id,
                    'no_of_cleaners' => $this->complaint->no_of_cleaners,
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

        // Default notification for supervisors about new complaints
        return (new FcmMessage(notification: new FcmNotification(
            title: 'New Complaint Submitted',
            body: 'A new complaint has been submitted by Officer ' . $this->officer->name,
        )))
            ->data([
                'type' => 'new_complaint',
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

    /**
     * Convert the notification to an array representation.
     */
    public function toArray($notifiable)
    {
        // This method is used for broadcasting and can be useful for JSON representation
        return $this->toDatabase($notifiable);
    }
}
