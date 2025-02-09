<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;
use App\Service\SupabaseService;
use Illuminate\Support\Facades\Log;

class ComplaintNotification extends Notification
{
    protected $complaint;
    protected $officer;
    protected $cleaner;
    protected $supervisor;

    public function __construct($complaint, $officer, $cleaner = null, $supervisor = null)
    {
        $this->complaint  = $complaint;
        $this->officer    = $officer;
        $this->cleaner    = $cleaner;
        $this->supervisor = $supervisor;
    }

    public function via($notifiable)
    {
        return [
            'database',
            FcmChannel::class,
        ];
    }

    /**
     * Store notification in the local notifications table.
     * Additionally, store the notification data in Supabase.
     */
    public function toDatabase($notifiable)
    {
        // Prepare the data array for local storage and Supabase.
        if ($this->cleaner) {
            $data = [
                'complaint_id' => $this->complaint->id,
                'message'      => 'You have been assigned a new complaint.',
            ];
        } elseif ($this->officer) {
            $data = [
                'complaint_id' => $this->complaint->id,
                'message'      => 'Your complaint has been updated.',
            ];
        } else {
            $data = [
                'complaint_id' => $this->complaint->id,
                'officer_name' => $this->officer->name,
                'message'      => 'A new complaint has been submitted by Officer ' . $this->officer->name,
            ];
        }

        // Attempt to store the notification in Supabase.
        try {
            $supabase = app(SupabaseService::class);
            // Here we assume that the notifications table in Supabase is named "notifications"
            // and that it accepts similar columns.
            $supabase->store('notifications', $data);
        } catch (\Exception $e) {
            // Log the error and continue (local notification is still stored).
            Log::error("Failed to store notification in Supabase: " . $e->getMessage());
        }

        return $data;
    }

    /**
     * Use device tokens fetched from Supabase to send FCM notifications.
     */
    public function toFcm($notifiable): ?FcmMessage
    {
        // Resolve SupabaseService from the container:
        $supabase = app(SupabaseService::class);

        // Fetch device tokens for $notifiable->id from Supabase.
        $tokens = $supabase->getDeviceTokensForUser($notifiable->id);

        if ($tokens->isEmpty()) {
            return null; // No tokens available, so no FCM notification is sent.
        }

        if ($this->cleaner) {
            // Notification for cleaner.
            return (new FcmMessage(
                notification: new FcmNotification(
                    title: 'New Complaint Assigned',
                    body: 'You have been assigned a new complaint.'
                )
            ))
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
            // Notification for officer.
            return (new FcmMessage(
                notification: new FcmNotification(
                    title: 'Complaint Updates',
                    body: 'Your complaint has been updated.'
                )
            ))
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

        // Default notification if neither cleaner nor officer is provided.
        return (new FcmMessage(
            notification: new FcmNotification(
                title: 'New Complaint Submitted',
                body: 'A new complaint has been submitted by Officer ' . $this->officer->name
            )
        ))
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

    /**
     * Static helper to send a notification assignment.
     */
    public static function notifyAssignment($complaint, $officer, $cleaner, $supervisor)
    {
        $cleaner->notify(new self($complaint, $officer, $cleaner, $supervisor));
    }
}
