<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of the notifications.
     */
    public function index()
    {
        $user = Auth::user();

        // Retrieve all notifications with pagination
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Retrieve unread notifications
        $unreadNotifications = $user->unreadNotifications;

        // Retrieve unread notifications count
        $unreadNotificationsCount = $unreadNotifications->count();

        return view('supervisor.notifications.index', compact('notifications', 'unreadNotifications', 'unreadNotificationsCount'));
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead($id)
    {
        $user = Auth::user(); // Ensure the user is authenticated
        $notification = $user->unreadNotifications->find($id);
    
        if ($notification) {
            $notification->markAsRead();
            return response()->json(['status' => 'success', 'message' => 'Notification marked as read.']);
        }
    
        return response()->json(['status' => 'error', 'message' => 'Notification not found.'], 404);
    }
    

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return response()->json(['status' => 'success']);
    }

    /**
     * Clear all notifications.
     */
    public function clearAll()
    {
        // Clear all notifications for the authenticated user
        Auth::user()->notifications()->delete();

        // Return a JSON response
        return response()->json(['status' => 'success', 'message' => 'All notifications cleared successfully.']);
    }

    /**
     * Delete a specific notification.
     */
    public function destroy($id)
    {
        $notification = Auth::user()->notifications()->find($id);

        if ($notification) {
            $notification->delete();
            return response()->json(['status' => 'success', 'message' => 'Notification deleted successfully.']);
        }

        return response()->json(['status' => 'error', 'message' => 'Notification not found.'], 404);
    }
}