<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Broadcasting\PrivateChannel;

class NewCleaningComplaint extends Notification implements ShouldQueue
{
    use Queueable;

    public $complaint;

    /**
     * Create a new notification instance.
     *
     * @param array $complaint
     */
    public function __construct($complaint)
    {
        $this->complaint = $complaint;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Cleaning Complaint Received')
            ->line('A new complaint has been submitted.')
            ->line('Details:')
            ->line('Date: ' . $this->complaint['comp_date'])
            ->line('Time: ' . $this->complaint['comp_time'])
            ->line('Location: ' . $this->complaint['comp_location'])
            ->line('Description: ' . $this->complaint['comp_desc'])
            ->line('Submitted by Officer ID: ' . $this->complaint['officer_id'])
            ->line('Assigned Cleaner ID: ' . $this->complaint['cleaner_id'])
            ->action('View Complaint', url('/complaints/' . $this->complaint['id']))
            ->line('Thank you for addressing this complaint promptly.');
    }

    /**
     * Get the array representation of the notification for the database.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'id' => $this->complaint['id'],
            'comp_date' => $this->complaint['comp_date'],
            'comp_time' => $this->complaint['comp_time'],
            'comp_desc' => $this->complaint['comp_desc'],
            'comp_location' => $this->complaint['comp_location'],
            'officer_id' => $this->complaint['officer_id'],
            'cleaner_id' => $this->complaint['cleaner_id'],
            'submitted_at' => now(),
        ];
    }

    /**
     * Get the broadcastable data representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toBroadcast($notifiable)
    {
        return [
            'id' => $this->complaint['id'],
            'comp_date' => $this->complaint['comp_date'],
            'comp_time' => $this->complaint['comp_time'],
            'comp_desc' => $this->complaint['comp_desc'],
            'comp_location' => $this->complaint['comp_location'],
            'officer_id' => $this->complaint['officer_id'],
            'cleaner_id' => $this->complaint['cleaner_id'],
            'submitted_at' => now(),
        ];
    }

    /**
     * Specify the broadcasting channel for real-time notifications.
     *
     * @return \Illuminate\Broadcasting\PrivateChannel
     */
    public function broadcastOn()
    {
        return new PrivateChannel('notifications');
    }
}
