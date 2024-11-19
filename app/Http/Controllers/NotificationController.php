<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display all notifications and unread notifications for the user.
     */
    public function index()
    {
        $user = auth('web')->user();

        if ($user) {
            $notifications = $user->notifications()->latest()->paginate(10); // Paginate all notifications
            $unreadNotifications = $user->unreadNotifications;               // Fetch unread notifications

            return view('notifications.index', compact('notifications', 'unreadNotifications'));
        }

        return redirect()->route('login')->with('error', 'User not authenticated.');
    }

    public function fetchAll(Request $request)
    {
        $user = auth('web')->user();

        if ($user) {
            $notifications = $user->notifications()->latest()->paginate(10); // Fetch all notifications, paginated
            return response()->json($notifications);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * Mark a specific notification as read without deleting it.
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead($id)
    {
        $notification = auth('web')->user()->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
            return response()->json(['message' => 'Notification marked as read.']);
        }
        return response()->json(['error' => 'Notification not found.'], 404);
    }


    /**
     * Mark all unread notifications as read without deleting them.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAllAsRead()
    {
        $user = auth('web')->user();

        if ($user) {
            $user->unreadNotifications->markAsRead();

            return response()->json(['success' => true, 'message' => 'All notifications marked as read.']);
        }

        return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
    }
}
