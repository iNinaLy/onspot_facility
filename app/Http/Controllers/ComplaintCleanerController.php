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
    public function getCleanerTasks($cleaner_id)
    {
        try {
            // Use Eloquent relationships to retrieve complaints assigned to a specific cleaner
            $tasks = ComplaintCleaner::with('complaint:id,comp_desc,comp_location,comp_date,comp_time,comp_status')
                ->where('cleaner_id', $cleaner_id)
                ->get()
                ->map(function ($complaintCleaner) {
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
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data' => $tasks
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve tasks.',
                'error' => $e->getMessage()
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
    
            if (!$hasCleanerAssignment) {
                // No entry in `complaint_cleaner`, fetch basic details for officer
                $compImageUrl = $complaint->getFirstMediaUrl('complaint_images') ? url($complaint->getFirstMediaUrl('complaint_images')) : null;
    
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
                ];
    
                return response()->json($complaintDetails, 200);
    
            } else {
                // Entry exists in `complaint_cleaner`, fetch extended details with relationships for supervisors/cleaners
                $complaint = Complaint::with(['cleaners', 'officer', 'supervisor'])
                    ->findOrFail($id);
    
                $compImageUrl = $complaint->getFirstMediaUrl('complaint_images') ?: null;
    
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
    
    

        public function getLatestTask()
    {
        try {
            // Retrieve the latest complaint based on comp_date and comp_time
            $complaint = Complaint::orderBy('comp_date', 'desc')
                ->orderBy('comp_time', 'desc')
                ->first();

            if (!$complaint) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No complaints found.'
                ], 404);
            }

            // Get the media URL for the complaint image if it exists
            $compImageUrl = $complaint->getFirstMediaUrl('complaint_images') ?: null;

            // Format the complaint details for response
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
            ];

            return response()->json([
                'status' => 'success',
                'data' => $complaintDetails
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve the latest complaint.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
