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

        // Retrieve the latest 10 notifications using Eloquent query
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(10); // Use paginate for proper pagination

        // Optionally, retrieve unread notifications count
        $unreadNotificationsCount = $user->unreadNotifications->count();

        return view('supervisor.notifications.index', compact('notifications', 'unreadNotificationsCount'));
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->unreadNotifications->find($id);

        if ($notification) {
            $notification->markAsRead();
            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'error', 'message' => 'Notification not found'], 404);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return response()->json(['status' => 'success']);
    }

    public function clearAll()
    {
        // Clear all notifications for the authenticated user
        Auth::user()->notifications()->delete();

        // Redirect back with a success message
        return redirect()->back()->with('success', 'All notifications cleared successfully.');
    }
}
