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
        if ($notifiable->hasRole('supervisor') && !$this->cleaner) {
            return [
                'complaint_id' => $this->complaint->id,
                'officer_name' => $this->officer->name,
                'message' => 'A new complaint has been submitted by Officer ' . $this->officer->name,
            ];
        } elseif ($notifiable->hasRole('cleaner') && $this->cleaner) {
            return [
                'complaint_id' => $this->complaint->id,
                'cleaner_name' => $this->cleaner->name,
                'supervisor_name' => $this->supervisor->name,
                'message' => 'You have been assigned a new complaint by Supervisor ' . $this->supervisor->name,
            ];
        }
    
        return [];
    }    

    // Use device tokens to send FCM notification
    public function toFcm($notifiable): ?FcmMessage
    {
        $tokens = $notifiable->notificationTokens()->pluck('device_token');

        if ($tokens->isEmpty()) {
            return null; // No tokens to send notification
        }

        if ($notifiable->hasRole('supervisor') && !$this->cleaner) {
            // Notify supervisors of new complaints only
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

        // Ensure this notification is sent only to the cleaner
        if ($notifiable->hasRole('cleaner') && $this->cleaner) {
            $tokens = $this->cleaner->notificationTokens()->pluck('device_token');

            if ($tokens->isEmpty()) {
                return null; // No tokens to send notification
            }

            return (new FcmMessage(notification: new FcmNotification(
                title: 'Tugasan Baru',
                body: 'Anda telah ditugaskan tugasan baru oleh ' . $this->supervisor->name,
            )))
                ->data([
                    'complaint_id' => $this->complaint->id,
                    'supervisor_name' => $this->supervisor->name,
                ])
                ->to($tokens->toArray()) // Only send to this cleaner's tokens
                ->custom([
                    'android' => [
                        'notification' => [
                            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                        ],
                    ],
                ]);
        }
        return null;
    }

    // Static helper to notify cleaner and officer
    public static function notifyAssignment($complaint, $officer, $cleaner, $supervisor)
    {
        foreach ($cleaners as $cleaner) {
            // Ensure the cleaner is not already notified
            $alreadyNotified = $cleaner->notifications()
                ->where('data->complaint_id', $complaint->id)
                ->exists();
    
            if ($alreadyNotified) {
                \Log::info("Cleaner already notified for complaint", [
                    'cleaner_id' => $cleaner->id,
                    'complaint_id' => $complaint->id,
                ]);
                continue;
            }
    
            // Send notification to cleaner
            $cleaner->notify(new self($complaint, $officer, $cleaner, $supervisor));
            \Log::info("Notification sent to cleaner", [
                'cleaner_id' => $cleaner->id,
                'complaint_id' => $complaint->id,
            ]);
        }

        // Notify Officer
        if ($officer) {
            $officer->notify(new self($complaint, $officer));
        }
    }
}