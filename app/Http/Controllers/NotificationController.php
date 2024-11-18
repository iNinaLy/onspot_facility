<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display all notifications for the authenticated user.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        $user = auth('web')->user();

        if ($user) {
            $notifications = $user->notifications; // Fetch all notifications
            $unreadNotifications = $user->unreadNotifications; // Fetch unread notifications

            return view('notifications.index', compact('notifications', 'unreadNotifications'));
        }

        return redirect()->route('login')->with('error', 'User not authenticated.');
    }

    /**
     * Mark a notification as read and remove it.
     *
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAsRead($id)
    {
        $user = auth('web')->user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'User not authenticated.');
        }

        $notification = $user->notifications()->find($id);

        if ($notification) {
            $notification->markAsRead();
            $notification->delete();

            return redirect()->route('supervisor.notifications.index')->with('success', 'Notification marked as read and removed.');
        }

        return redirect()->route('supervisor.notifications.index')->with('error', 'Notification not found.');
    }

    /**
     * Remove all read notifications for the authenticated user.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeReadNotifications()
    {
        $user = auth('web')->user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'User not authenticated.');
        }

        $user->readNotifications()->delete();

        return redirect()->back()->with('success', 'All read notifications have been removed.');
    }
}
