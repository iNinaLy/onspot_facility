<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function getNotifications(Request $request)
    {
        // Check authenticated user
        $user = $request->user();

        // Sample notifications
        $notifications = $user->notifications()->orderBy('created_at', 'desc')->get();

        // Ensure JSON response
        return response()->json([
            'success' => true,
            'notifications' => $notifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->data['title'] ?? 'Notification',
                    'body' => $notification->data['message'] ?? '',
                    'created_at' => $notification->created_at->toDateTimeString(),
                ];
            }),
        ]);
    }
}
