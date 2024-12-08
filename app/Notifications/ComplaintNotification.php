<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class ComplaintNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $complaint;
    protected $officer;
    protected $isAssignment;

    /**
     * Initialize the notification with complaint, officer, and assignment flag.
     *
     * @param \App\Models\Complaint $complaint
     * @param \App\Models\User $officer
     * @param bool $isAssignment
     */
    public function __construct($complaint, $officer, $isAssignment = false)
    {
        $this->complaint = $complaint;
        $this->officer = $officer;
        $this->isAssignment = $isAssignment;
    }

    /**
     * Define the delivery channels for the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', FcmChannel::class];
    }

    /**
     * Store the notification in the database table.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        if ($this->isAssignment) {
            if ($notifiable->role === 'cleaner') {
                return [
                    'type'          => 'cleaner_assignment',
                    'complaint_id'  => $this->complaint->id,
                    'message'       => 'You have been assigned to Complaint #' . $this->complaint->id,
                    'comp_location' => $this->complaint->comp_location,
                    'assigned_date' => $this->complaint->assigned_date->format('d M Y H:i'),
                ];
            } elseif ($notifiable->role === 'officer') {
                return [
                    'type'           => 'officer_assignment',
                    'complaint_id'   => $this->complaint->id,
                    'message'        => 'Cleaners have been assigned to your Complaint #' . $this->complaint->id,
                    'no_of_cleaners' => $this->complaint->no_of_cleaners,
                    'assigned_by'    => $this->officer->name,
                ];
            }
        }

        // Fallback for other notifications
        return [
            'type'         => 'general_notification',
            'complaint_id' => $this->complaint->id,
            'message'      => 'Complaint #' . $this->complaint->id . ' has been updated.',
        ];
    }

    /**
     * Send the notification via FCM.
     *
     * @param mixed $notifiable
     * @return \NotificationChannels\Fcm\FcmMessage|null
     */
    public function toFcm($notifiable): ?FcmMessage
    {
        $tokens = $notifiable->notificationTokens()->pluck('device_token')->toArray();

        if (empty($tokens)) {
            return null;
        }

        $notificationData = $this->buildFcmNotification($notifiable);

        if ($notificationData === null) {
            return null;
        }

        return (new FcmMessage())
            ->setNotification($notificationData['notification'])
            ->setData($notificationData['data'])
            ->setAndroid([
                'notification' => [
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                ],
            ])
            ->setTokens($tokens);
    }

    /**
     * Build the FCM notification payload.
     *
     * @param mixed $notifiable
     * @return array|null
     */
    protected function buildFcmNotification($notifiable)
    {
        if ($this->isAssignment) {
            if ($notifiable->role === 'cleaner') {
                return [
                    'notification' => [
                        'title' => 'New Task Assigned',
                        'body'  => 'You have been assigned to Complaint #' . $this->complaint->id . ' at ' . $this->complaint->comp_location,
                    ],
                    'data' => [
                        'type'          => 'cleaner_assignment',
                        'complaint_id'  => $this->complaint->id,
                        'comp_location' => $this->complaint->comp_location,
                        'assigned_date' => $this->complaint->assigned_date->format('d M Y H:i'),
                    ],
                ];
            } elseif ($notifiable->role === 'officer') {
                return [
                    'notification' => [
                        'title' => 'Cleaners Assigned',
                        'body'  => 'Cleaners have been assigned to your Complaint #' . $this->complaint->id,
                    ],
                    'data' => [
                        'type'           => 'officer_assignment',
                        'complaint_id'   => $this->complaint->id,
                        'no_of_cleaners' => $this->complaint->no_of_cleaners,
                        'assigned_by'    => $this->officer->name,
                    ],
                ];
            }
        }

        // Fallback notification (if applicable)
        return [
            'notification' => [
                'title' => 'Complaint Updated',
                'body'  => 'Complaint #' . $this->complaint->id . ' has been updated.',
            ],
            'data' => [
                'type'         => 'general_notification',
                'complaint_id' => $this->complaint->id,
            ],
        ];
    }

    /**
     * Convert the notification to an array representation.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return $this->toDatabase($notifiable);
    }
}
