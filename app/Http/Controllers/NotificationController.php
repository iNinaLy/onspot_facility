<?php

namespace App\Http\Controllers;

use App\Models\Complaint;   // Make sure to import your Complaint model
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

        return view('supervisor.notifications.index', compact(
            'notifications',
            'unreadNotifications',
            'unreadNotificationsCount'
        ));
    }

    /**
     * Fetch all notifications via AJAX (if needed).
     * (Remove or adapt if you don't need this.)
     */
    public function fetchAll()
    {
        $notifications = Auth::user()->notifications()
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($notifications);
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead($id)
    {
        $user = Auth::user();
        $notification = $user->unreadNotifications->find($id);
    
        if ($notification) {
            $notification->markAsRead();
            return response()->json([
                'status' => 'success',
                'message' => 'Notification marked as read.'
            ]);
        }
    
        return response()->json([
            'status' => 'error',
            'message' => 'Notification not found.'
        ], 404);
    }

    /**
     * Mark all unread notifications as read.
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return response()->json(['status' => 'success']);
    }

    /**
     * Clear all notifications (delete them).
     */
    public function clearAll()
    {
        Auth::user()->notifications()->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'All notifications cleared successfully.'
        ]);
    }

    /**
     * Delete a specific notification.
     */
    public function destroy($id)
    {
        $notification = Auth::user()->notifications()->find($id);

        if ($notification) {
            $notification->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Notification deleted successfully.'
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Notification not found.'
        ], 404);
    }

    /**
     * NEW METHOD:
     * 1) Find the notification (read or unread).
     * 2) Extract the complaint_id from the notification’s data.
     * 3) Find the complaint in the DB, check its status.
     * 4) Redirect to the correct page:
     *    - Pending => supervisor.complaints.show
     *    - Ongoing/Completed => supervisor.history
     */
    public function redirectToComplaint($notificationId)
    {
        $user = Auth::user();

        // 1. Find the notification
        // Using ->notifications() retrieves all notifications (read & unread).
        $notification = $user->notifications()->find($notificationId);

        if (!$notification) {
            abort(404, 'Notification not found.');
        }

        // 2. Extract complaint_id
        $complaintId = $notification->data['complaint_id'] ?? null;
        if (!$complaintId) {
            abort(404, 'No associated complaint found in notification data.');
        }

        // 3. Find complaint in DB
        $complaint = Complaint::findOrFail($complaintId);

        // (Optional) You could also mark as read here if not already:
        // if (is_null($notification->read_at)) {
        //     $notification->markAsRead();
        // }

        // 4. Check status and redirect
        switch ($complaint->status) {
            case 'pending':
                // If complaint is still pending
                return redirect()->route('supervisor.complaints.show', $complaintId);

            case 'ongoing':
            case 'completed':
                // If ongoing or completed, go to history
                return redirect()->route('supervisor.history', $complaintId);

            default:
                // If unknown status or fallback
                return redirect()->route('supervisor.complaints.show', $complaintId);
        }
    }
}
