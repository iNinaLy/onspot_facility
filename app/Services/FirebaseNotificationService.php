<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

class FirebaseNotificationService
{
    protected $messaging;

    public function __construct()
    {
        // Initialize Firebase with the service account credentials
        $firebase = (new Factory)->withServiceAccount(config('firebase.credentials'));
        $this->messaging = $firebase->createMessaging();
    }

    /**
     * Send a notification to a specific device using its FCM token.
     *
     * @param string $deviceToken
     * @param string $title
     * @param string $body
     * @param array $data (Optional) Additional data to include with the notification
     * @return void
     */
    public function sendNotificationToDevice($deviceToken, $title, $body, $data = [])
    {
        $message = CloudMessage::withTarget('token', $deviceToken)
            ->withNotification(["title" => $title, "body" => $body])
            ->withData($data);

        try {
            $this->messaging->send($message);
        } catch (\Kreait\Firebase\Exception\MessagingException $e) {
            // Log or handle the error as needed
            \Log::error('Firebase Notification Error: ' . $e->getMessage());
        }
    }

    /**
     * Send a notification to a specific topic.
     *
     * @param string $topic
     * @param string $title
     * @param string $body
     * @param array $data (Optional) Additional data to include with the notification
     * @return void
     */
    public function sendNotificationToTopic($topic, $title, $body, $data = [])
    {
        $message = CloudMessage::withTarget('topic', $topic)
            ->withNotification(["title" => $title, "body" => $body])
            ->withData($data);

        try {
            $this->messaging->send($message);
        } catch (\Kreait\Firebase\Exception\MessagingException $e) {
            // Log or handle the error as needed
            \Log::error('Firebase Topic Notification Error: ' . $e->getMessage());
        }
    }

    /**
     * Send a notification when a complaint is submitted.
     *
     * @param string $title
     * @param string $body
     * @param array $data (Optional) Additional data to include with the notification
     * @return void
     */
    public function sendComplaintNotification($title, $body, $data = [])
    {
        $supervisorTopic = 'supervisors'; // Assuming supervisors are subscribed to this topic
        $this->sendNotificationToTopic($supervisorTopic, $title, $body, $data);
    }
}
