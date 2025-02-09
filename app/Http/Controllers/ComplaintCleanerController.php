<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\ComplaintCleaner;
use App\Models\User;
use App\Models\Cleaner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class ComplaintCleanerController extends Controller
{
    protected $supabaseService;

    public function __construct(\App\Service\SupabaseService $supabaseService)
    {
        $this->supabaseService = $supabaseService;
    }

    public function store(Request $request)
    {
        try {
            // 1) Validate the incoming request
            $validated = $request->validate([
                'complaint_id' => 'required|integer|exists:complaints,id',
                'cleaner_id'   => 'required|integer|exists:cleaners,id', 
                
            ]);

            // 2) Fetch the complaint from MySQL
            $complaint = Complaint::findOrFail($validated['complaint_id']);

            // 3) Fetch the cleaner (if needed)
            $cleaner = Cleaner::findOrFail($validated['cleaner_id']);

            // 4) Create the pivot record in MySQL
            $pivot = ComplaintCleaner::create([
                'complaint_id' => $complaint->id,
                'cleaner_id'   => $cleaner->id,
                'assigned_date' => now(), // or $request->input('assigned_date')
                // any other fields (e.g. no_of_cleaners, assigned_by, etc.)
                'no_of_cleaners' => $request->input('no_of_cleaners', 1),
                'assigned_by'    => Auth::id() ?? 1, 
            ]);

            Log::info('Pivot record created in MySQL', ['pivot_id' => $pivot->id]);

            // 5) Also store in Supabase
            $data = [
                'complaint_id' => $complaint->id,
                'cleaner_id'   => $cleaner->id,
                'assigned_date'=> now()->toIso8601String(),
                // Mirror the other fields if needed
                'no_of_cleaners' => $request->input('no_of_cleaners', 1),
                'assigned_by'    => Auth::id() ?? 1, 
            ];

            $insertResult = $this->supabaseService->storeComplaintCleaner($data);

            Log::info('Record inserted into Supabase', [
                'complaint_id' => $complaint->id,
                'cleaner_id'   => $cleaner->id,
                'result'       => $insertResult,
            ]);

            // 6) Redirect with success
            return redirect()
                ->route('complaints.details', $complaint->id)
                ->with('success', 'Cleaner assigned successfully (MySQL + Supabase).');

        } catch (\Exception $e) {
            Log::error('Error in store method', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function unnotifiedTasks(Request $request, $cleaner_id)
    {
        try {
        

            // Retrieve from MySQL
            $tasks = ComplaintCleaner::where('cleaner_id', $cleaner_id)
                ->where('is_notified', false)
                ->whereNotNull('assigned_by')
                ->whereHas('complaint', function ($query) {
                    $query->where('comp_status', 'ongoing');
                })
                ->with('complaint')
                ->get();

            // Pass the tasks to a Blade view
            return view('complaints.unnotified_tasks', [
                'tasks' => $tasks
            ]);

        } catch (\Exception $e) {
            
            return redirect()->back()->withErrors('Failed to load tasks.');
        }
    }

    /**
     * Get complaints created by the authenticated officer (for a web page).
     */
    public function getOfficerComplaints()
    {
        $officerId = Auth::id();
        if (!$officerId) {
            return redirect()->route('login')->withErrors('You must be logged in.');
        }

        try {
            $complaints = Complaint::where('officer_id', $officerId)
                ->orderBy('comp_date', 'desc')
                ->orderBy('comp_time', 'desc')
                ->get();

            return view('complaints.officer_complaints', [
                'complaints' => $complaints
            ]);

        } catch (\Exception $e) {
        
            return redirect()->back()->withErrors('Failed to retrieve officer complaints.');
        }
    }

    /**
     * Show details of a specific complaint.
     */
    public function getComplaintDetailsConditional($id)
    {
        try {
            $complaint = Complaint::find($id);
            if (!$complaint) {
                return redirect()->back()->withErrors('Complaint not found.');
            }

            // If using spatie/laravel-medialibrary for images:
            $compImageUrl = $complaint->getFirstMediaUrl('complaint_images')
                ? url($complaint->getFirstMediaUrl('complaint_images'))
                : null;

            // Optional: fetch officer/supervisor from DB
            $officer = DB::table('users')
                ->where('id', $complaint->officer_id)
                ->select('id', 'name')
                ->first();

            $supervisor = DB::table('users')
                ->where('id', $complaint->assigned_by)
                ->select('id', 'name')
                ->first();

            // Check if any cleaners assigned
            $hasCleanerAssignment = DB::table('complaint_cleaner')
                ->where('complaint_id', $id)
                ->exists();

            // If no cleaners, pass an empty array
            $cleaners = [];
            if ($hasCleanerAssignment) {
                $cleaners = DB::table('complaint_cleaner')
                    ->join('users', 'complaint_cleaner.cleaner_id', '=', 'users.id')
                    ->where('complaint_cleaner.complaint_id', $id)
                    ->select('users.id as cleaner_id', 'users.name as cleaner_name')
                    ->get();
            }

            return view('complaints.details', [
                'complaint'    => $complaint,
                'compImageUrl' => $compImageUrl,
                'officer'      => $officer,
                'supervisor'   => $supervisor,
                'cleaners'     => $cleaners,
            ]);

        } catch (\Exception $e) {
           
            return redirect()->back()->withErrors('Failed to retrieve complaint details.');
        }
    }

    /**
     * Mark a task as notified. Instead of returning JSON,
     * we redirect back with success or error messages.
     */
    public function notifiedTasks(Request $request)
    {
        $complaintId = $request->input('complaint_id');
        $cleanerId   = $request->input('cleaner_id');

        try {
            $task = ComplaintCleaner::where('complaint_id', $complaintId)
                ->where('cleaner_id', $cleanerId)
                ->first();

            if (!$task) {
                return redirect()->back()->withErrors('Task not found.');
            }

            $task->is_notified = true;
            $task->save();

            return redirect()->back()->with('success', 'Task marked as completed.');
        } catch (\Exception $e) {
            
            return redirect()->back()->withErrors('Failed to mark task as completed.');
        }
    }

    /**
     * Get history tasks (is_notified = true, etc.) for a specific cleaner.
     * Returns a Blade view instead of JSON.
     */
    public function getHistoryTasks($cleaner_id)
    {
        try {
            $tasks = ComplaintCleaner::where('cleaner_id', $cleaner_id)
                ->with('complaint')
                ->get();

            // If you only want tasks where is_notified = true, add:
            // ->where('is_notified', true) above.

            return view('complaints.history', [
                'tasks' => $tasks
            ]);

        } catch (\Exception $e) {
           
            return redirect()->back()->withErrors('Failed to retrieve history.');
        }
    }

    /**
     * Mark a cleaner as unavailable for a given complaint, then redirect.
     */
    public function markCleanerUnavailable($complaint_id)
    {
        try {
            $complaintCleaner = ComplaintCleaner::where('complaint_id', $complaint_id)->first();

            if (!$complaintCleaner) {
                return redirect()->back()->withErrors('Complaint not found.');
            }

            // Update the user to 'unavailable'
            $cleaner = User::find($complaintCleaner->cleaner_id);
            if ($cleaner) {
                $cleaner->status = 'unavailable';
                $cleaner->save();
            }

            return redirect()->back()->with('success', 'Cleaner marked as unavailable.');
        } catch (\Exception $e) {
        
            return redirect()->back()->withErrors('Failed to update cleaner status.');
        }
    }

}
