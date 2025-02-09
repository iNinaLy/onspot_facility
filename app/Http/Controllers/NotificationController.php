<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Service\SupabaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class NotificationController extends Controller
{
    protected $supabaseService;

    public function __construct(SupabaseService $supabaseService)
    {
        $this->supabaseService = $supabaseService;
    }

    /**
     * Display notifications.
     */
    public function index()
    {
        try {
            // Fetch all notifications from Supabase.
            $notifications = $this->supabaseService->getNotifications();

            // Filter notifications for the current user.
            $notifications = collect($notifications)->filter(function ($notif) {
                return isset($notif['user_id']) && $notif['user_id'] == Auth::id();
            });

            // Determine unread notifications (assuming a "read_at" column; if missing or null, treat as unread).
            $unreadNotifications = $notifications->filter(function ($notif) {
                return empty($notif['read_at']);
            });
            $unreadNotificationsCount = $unreadNotifications->count();

            // Option: You can implement manual pagination if needed.
            // For now, we pass the full collection to the view.
            return view('supervisor.notifications.index', [
                'notifications' => $notifications,
                'unreadNotifications' => $unreadNotifications,
                'unreadNotificationsCount' => $unreadNotificationsCount,
            ]);
        } catch (\Exception $e) {
            Log::error("Error fetching notifications from Supabase: " . $e->getMessage());
            return redirect()->back()->withErrors('Failed to fetch notifications from Supabase.');
        }
    }

    /**
     * Fetch all notifications via AJAX.
     */
    public function fetchAll()
    {
        try {
            $notifications = $this->supabaseService->getNotifications();
            $notifications = collect($notifications)->filter(function ($notif) {
                return isset($notif['user_id']) && $notif['user_id'] == Auth::id();
            });
            return response()->json($notifications);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead($id)
    {
        try {
            $data = ['read_at' => Carbon::now()->toIso8601String()];
            $result = $this->supabaseService->update('notifications', $id, $data);
            return response()->json([
                'status'  => 'success',
                'message' => 'Notification marked as read.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to mark notification as read: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Mark all unread notifications as read.
     */
    public function markAllAsRead()
    {
        try {
            // Supabase REST does not support bulk updates via our custom service method,
            // so we use the Http facade directly.
            $url = rtrim(env('SUPABASE_URL'), '/') . "/rest/v1/notifications?user_id=eq." . Auth::id() . "&read_at=is.null";
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'apikey' => env('SUPABASE_SECRET_KEY'),
                'Authorization' => 'Bearer ' . env('SUPABASE_SECRET_KEY'),
                'Content-Type'  => 'application/json',
            ])->patch($url, ['read_at' => Carbon::now()->toIso8601String()]);

            if ($response->failed()) {
                throw new \Exception($response->body());
            }
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to mark all as read: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear all notifications for the authenticated user.
     */
    public function clearAll()
    {
        try {
            $url = rtrim(env('SUPABASE_URL'), '/') . "/rest/v1/notifications?user_id=eq." . Auth::id();
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'apikey' => env('SUPABASE_SECRET_KEY'),
                'Authorization' => 'Bearer ' . env('SUPABASE_SECRET_KEY'),
                'Content-Type'  => 'application/json',
            ])->delete($url);

            if ($response->failed()) {
                throw new \Exception($response->body());
            }
            return response()->json([
                'status'  => 'success',
                'message' => 'All notifications cleared successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to clear notifications: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a specific notification.
     */
    public function destroy($id)
    {
        try {
            $url = rtrim(env('SUPABASE_URL'), '/') . "/rest/v1/notifications?id=eq." . $id;
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'apikey' => env('SUPABASE_SECRET_KEY'),
                'Authorization' => 'Bearer ' . env('SUPABASE_SECRET_KEY'),
                'Content-Type'  => 'application/json',
            ])->delete($url);

            if ($response->failed()) {
                throw new \Exception($response->body());
            }
            return response()->json([
                'status'  => 'success',
                'message' => 'Notification deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to delete notification: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Redirect to a complaint based on notification data.
     */
    public function redirectToComplaint($notificationId)
    {
        try {
            $url = rtrim(env('SUPABASE_URL'), '/') . "/rest/v1/notifications?id=eq." . $notificationId;
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'apikey' => env('SUPABASE_SECRET_KEY'),
                'Authorization' => 'Bearer ' . env('SUPABASE_SECRET_KEY'),
                'Content-Type'  => 'application/json',
            ])->get($url);

            if ($response->failed()) {
                throw new \Exception("Notification not found.");
            }

            $notifications = $response->json();
            if (empty($notifications)) {
                abort(404, 'Notification not found.');
            }
            $notification = collect($notifications)->first();
            $complaintId = $notification['complaint_id'] ?? null;
            if (!$complaintId) {
                abort(404, 'No associated complaint found in notification data.');
            }

            $complaint = Complaint::findOrFail($complaintId);

            switch ($complaint->comp_status) {
                case 'pending':
                    return redirect()->route('supervisor.complaints.show', $complaintId);
                case 'ongoing':
                case 'completed':
                    return redirect()->route('supervisor.history', $complaintId);
                default:
                    return redirect()->route('supervisor.complaints.show', $complaintId);
            }
        } catch (\Exception $e) {
            abort(404, $e->getMessage());
        }
    }
}
