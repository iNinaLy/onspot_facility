<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\ComplaintCleaner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ComplaintCleanerController extends Controller
{
    /**
     * Get tasks (complaints) assigned to a specific cleaner.
     *
     * @param int $cleaner_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCleanerTasks(Request $request, $cleaner_id)
    {
        try {
            $notified = $request->query('notified', null); // Optional 'notified' filter (true/false)
    
            // Log the filter parameters for debugging
            \Log::info("Fetching ongoing tasks for cleaner_id: $cleaner_id with notified: $notified");
    
            // Retrieve complaints assigned to the cleaner
            $tasks = ComplaintCleaner::where('cleaner_id', $cleaner_id)
                ->when(!is_null($notified), function ($query) use ($notified) {
                    $query->where('is_notified', filter_var($notified, FILTER_VALIDATE_BOOLEAN)); // Apply 'is_notified' filter
                })
                ->whereHas('complaint', function ($query) {
                    $query->where('comp_status', 'ongoing'); // Filter by 'ongoing' status in the 'complaints' table
                })
                ->with('complaint') // Include complaint details
                ->get()
                ->map(function ($complaintCleaner) {
                    if (!$complaintCleaner->complaint) {
                        return null; // Skip tasks without a matching complaint
                    }
    
                    // Return the structured task data
                    return [
                        'complaint_id' => $complaintCleaner->complaint->id,
                        'comp_desc' => $complaintCleaner->complaint->comp_desc,
                        'comp_location' => $complaintCleaner->complaint->comp_location,
                        'comp_date' => $complaintCleaner->complaint->comp_date,
                        'comp_time' => $complaintCleaner->complaint->comp_time,
                        'comp_status' => $complaintCleaner->complaint->comp_status,
                        'assigned_date' => $complaintCleaner->assigned_date,
                        'no_of_cleaners' => $complaintCleaner->no_of_cleaners,
                        'assigned_by' => $complaintCleaner->assigned_by,
                        'is_notified' => $complaintCleaner->is_notified, // Include the notification status
                    ];
                })
                ->filter() // Remove null values
                ->values(); // Re-index the array
    
            // Log the fetched tasks for debugging
            \Log::info("Ongoing tasks fetched: " . json_encode($tasks));
    
            return response()->json([
                'status' => 'success',
                'data' => $tasks,
            ], 200);
    
        } catch (\Exception $e) {
            // Log the error
            \Log::error("Error fetching ongoing tasks: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve ongoing tasks.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }    
      
    
    public function toggleNotification(Request $request, $complaint_id)
    {
        try {
            $complaintCleaner = ComplaintCleaner::where('complaint_id', $complaint_id)->first();

            if (!$complaintCleaner) {
                return response()->json(['error' => 'Complaint not found'], 404);
            }

            // Toggle is_notified status
            $complaintCleaner->is_notified = !$complaintCleaner->is_notified;
            $complaintCleaner->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Notification status updated.',
                'is_notified' => $complaintCleaner->is_notified,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update notification status.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Get complaints created by the authenticated officer.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOfficerComplaints()
    {
        $officerId = Auth::id();  // Retrieve the authenticated user's ID

        if (!$officerId) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            // Retrieve complaints created by the authenticated officer
            $complaints = Complaint::where('officer_id', $officerId)
                ->orderBy('comp_date', 'desc')
                ->orderBy('comp_time', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $complaints
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve officer complaints.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get details of a specific complaint.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getComplaintDetailsConditional($id)
    {
        try {
            // Check if the complaint exists in the `complaints` table
            $complaint = Complaint::find($id);
    
            if (!$complaint) {
                return response()->json(['error' => 'Complaint not found'], 404);
            }
    
            // Check if the complaint has an entry in `complaint_cleaner` table
            $hasCleanerAssignment = DB::table('complaint_cleaner')->where('complaint_id', $id)->exists();
    
            $compImageUrl = $complaint->getFirstMediaUrl('complaint_images') ?: null;
    
            if (!$hasCleanerAssignment) {
                // No entry in `complaint_cleaner`, fetch basic details for officer
                $complaintDetails = [
                    'id' => $complaint->id,
                    'comp_date' => $complaint->comp_date,
                    'comp_time' => $complaint->comp_time,
                    'comp_desc' => $complaint->comp_desc,
                    'comp_location' => $complaint->comp_location,
                    'comp_image' => $compImageUrl,
                    'officer_id' => $complaint->officer_id,
                    'assigned_by' => $complaint->assigned_by,
                    'assigned_date' => $complaint->assigned_date,
                    'no_of_cleaners' => $complaint->no_of_cleaners,
                    'comp_status' => $complaint->comp_status,
                    'created_at' => $complaint->created_at,
                    'updated_at' => $complaint->updated_at,
                    'cleaners' => [], // Empty cleaners list
                ];
    
                return response()->json($complaintDetails, 200);
            } else {
                // Fetch cleaner details from the users table
                $cleaners = DB::table('complaint_cleaner')
                    ->join('users', 'complaint_cleaner.cleaner_id', '=', 'users.id') // cleaner_id references users.id
                    ->where('complaint_cleaner.complaint_id', $id)
                    ->select('users.id as cleaner_id', 'users.name as cleaner_name')
                    ->get();
    
                // Prepare the extended complaint details
                $complaintDetails = [
                    'id' => $complaint->id,
                    'comp_date' => $complaint->comp_date,
                    'comp_time' => $complaint->comp_time,
                    'comp_desc' => $complaint->comp_desc,
                    'comp_location' => $complaint->comp_location,
                    'comp_image' => $compImageUrl,
                    'officer_id' => $complaint->officer_id,
                    'assigned_by' => $complaint->assigned_by,
                    'assigned_date' => $complaint->assigned_date,
                    'no_of_cleaners' => $complaint->no_of_cleaners,
                    'comp_status' => $complaint->comp_status,
                    'created_at' => $complaint->created_at,
                    'updated_at' => $complaint->updated_at,
                    'cleaners' => $cleaners, // List of cleaner details
                ];
    
                return response()->json($complaintDetails, 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve complaint details.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function markCleanerUnavailable(Request $request, $complaint_id)
    {
        try {
            // Find the complaint-cleaner record
            $complaintCleaner = ComplaintCleaner::where('complaint_id', $complaint_id)->first();
    
            if (!$complaintCleaner) {
                return response()->json(['error' => 'Complaint not found'], 404);
            }
    
            // Find the cleaner and update their status to 'unavailable'
            $cleaner = User::find($complaintCleaner->cleaner_id);
            if ($cleaner) {
                $cleaner->status = 'unavailable'; // Update status
                $cleaner->save();
            }
    
            return response()->json([
                'status' => 'success',
                'message' => 'Cleaner marked as unavailable.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update cleaner status.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
}