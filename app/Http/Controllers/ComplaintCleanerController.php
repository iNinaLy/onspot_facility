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
    public function unnotifiedTasks(Request $request, $cleaner_id)
    {
        try {
            \Log::info("Fetching ongoing tasks for cleaner_id: $cleaner_id where assigned_by IS NOT NULL");
    
            $tasks = ComplaintCleaner::where('cleaner_id', $cleaner_id)
                ->whereNotNull('assigned_by')
                ->whereHas('complaint', function ($query) {
                    $query->where('comp_status', Complaint::STATUS_ONGOING);
                })
                ->with(['complaint'])
                ->get()
                ->map(function ($task) {
                    return [
                        'complaint_id' => $task->complaint->id,
                        'comp_desc' => $task->complaint->comp_desc,
                        'comp_location' => $task->complaint->comp_location,
                        'comp_date' => $task->complaint->comp_date,
                        'comp_time' => $task->complaint->comp_time,
                        'comp_status' => $task->complaint->comp_status,
                        'assigned_date' => $task->assigned_date,
                        'no_of_cleaners' => $task->no_of_cleaners,
                        'assigned_by' => $task->assigned_by,
                        'is_notified' => $task->is_notified, // ✅ Include acknowledgment status
                    ];
                });
    
            return response()->json(['status' => 'success', 'data' => $tasks], 200);
        } catch (\Exception $e) {
            \Log::error("Error fetching tasks: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
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
            $complaint = Complaint::find($id);
    
            if (!$complaint) {
                return response()->json(['error' => 'Complaint not found'], 404);
            }
    
            $compImageUrl = $complaint->getFirstMediaUrl('complaint_images') 
                ? url($complaint->getFirstMediaUrl('complaint_images')) // Full URL for public access
                : null;
    
            $officer = DB::table('users')->where('id', $complaint->officer_id)->select('id', 'name')->first();
            $supervisor = DB::table('users')->where('id', $complaint->assigned_by)->select('id', 'name')->first();
    
            $hasCleanerAssignment = DB::table('complaint_cleaner')->where('complaint_id', $id)->exists();
    
            if (!$hasCleanerAssignment) {
                return response()->json([
                    'id'             => $complaint->id,
                    'comp_date'      => $complaint->comp_date,
                    'comp_time'      => $complaint->comp_time,
                    'comp_desc'      => $complaint->comp_desc,
                    'comp_location'  => $complaint->comp_location,
                    'comp_image'     => $compImageUrl,
                    'officer'        => $officer,
                    'supervisor'     => $supervisor,
                    'assigned_date'  => $complaint->assigned_date,
                    'no_of_cleaners' => $complaint->no_of_cleaners,
                    'comp_status'    => $complaint->comp_status,
                    'created_at'     => $complaint->created_at,
                    'updated_at'     => $complaint->updated_at,
                    'cleaners'       => [],
                ], 200);
            }
    
            $cleaners = DB::table('complaint_cleaner')
                ->join('users', 'complaint_cleaner.cleaner_id', '=', 'users.id')
                ->where('complaint_cleaner.complaint_id', $id)
                ->select('users.id as cleaner_id', 'users.name as cleaner_name')
                ->get();
    
            return response()->json([
                'id'             => $complaint->id,
                'comp_date'      => $complaint->comp_date,
                'comp_time'      => $complaint->comp_time,
                'comp_desc'      => $complaint->comp_desc,
                'comp_location'  => $complaint->comp_location,
                'comp_image'     => $compImageUrl,
                'officer'        => $officer,
                'supervisor'     => $supervisor,
                'assigned_date'  => $complaint->assigned_date,
                'no_of_cleaners' => $complaint->no_of_cleaners,
                'comp_status'    => $complaint->comp_status,
                'created_at'     => $complaint->created_at,
                'updated_at'     => $complaint->updated_at,
                'cleaners'       => $cleaners,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to retrieve complaint details.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
    
    public function getHistoryTasks(Request $request, $cleaner_id)
    {
        try {
            // Fetch only completed complaints where is_notified is true for the specified cleaner_id
            $tasks = ComplaintCleaner::where('cleaner_id', $cleaner_id)
                ->whereHas('complaint', function ($query) {
                    $query->where('comp_status', Complaint::STATUS_COMPLETED);
                })
                ->with('complaint') // Include related complaint details
                ->get()
                ->map(function ($complaintCleaner) {
                    if (!$complaintCleaner->complaint) {
                        return null; // Skip tasks without a matching complaint
                    }
    
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
                        'is_notified' => $complaintCleaner->is_notified,
                    ];
                })
                ->filter()
                ->values();
    
            return response()->json(['status' => 'success', 'data' => $tasks], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
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


        public function getLatestTask($cleaner_id)
    {
        try {
            // Fetch the latest assigned task for the given cleaner where comp_status is 'ongoing'
            $latestTask = ComplaintCleaner::where('cleaner_id', $cleaner_id)
                ->whereHas('complaint', function ($query) {
                    $query->where('comp_status', 'ongoing');
                })
                ->with('complaint') // Load related complaint details
                ->orderBy('assigned_date', 'desc') // Sort by assigned date (latest first)
                ->first(); // Get the latest task

            if (!$latestTask) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No latest task found for this cleaner.',
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'complaint_id' => $latestTask->complaint->id,
                    'comp_desc' => $latestTask->complaint->comp_desc,
                    'comp_location' => $latestTask->complaint->comp_location,
                    'comp_date' => $latestTask->complaint->comp_date,
                    'comp_time' => $latestTask->complaint->comp_time,
                    'assigned_date' => $latestTask->assigned_date,
                    'no_of_cleaners' => $latestTask->no_of_cleaners,
                    'assigned_by' => $latestTask->assigned_by,
                    'is_notified' => $latestTask->is_notified,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error fetching latest task: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function acknowledgeTask(Request $request)
    {
        try {
            $complaintId = $request->input('complaint_id');
            $cleanerId = $request->input('cleaner_id');

            // ✅ Find the assigned task for this cleaner
            $task = ComplaintCleaner::where('complaint_id', $complaintId)
                                    ->where('cleaner_id', $cleanerId)
                                    ->first();

            if (!$task) {
                return response()->json(['status' => 'error', 'message' => 'Task not found'], 404);
            }

            // ✅ Update is_notified to true (mark as acknowledged)
            $task->is_notified = true;
            $task->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Task acknowledged successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to acknowledge task',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
}