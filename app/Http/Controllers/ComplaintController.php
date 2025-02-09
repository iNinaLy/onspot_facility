<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Cleaner;
use App\Models\User;
use App\Models\ComplaintCleaner;
use App\Service\SupabaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use App\Notifications\ComplaintNotification;

class ComplaintController extends Controller
{
    protected $supabaseService;

    public function __construct(SupabaseService $supabaseService)
    {
        $this->supabaseService = $supabaseService;
    }

    /**
     * Display a paginated listing of complaints (from MySQL).
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $query = Complaint::with(['officer', 'assignedBy']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('comp_desc', 'like', "%{$search}%")
                  ->orWhere('comp_location', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('comp_status', $status);
        }

        $complaints = $query->orderBy('comp_date', 'desc')->paginate(10);

        // Metrics
        $totalComplaints     = Complaint::count();
        $pendingComplaints   = Complaint::where('comp_status', 'pending')->count();
        $ongoingComplaints   = Complaint::where('comp_status', 'ongoing')->count();
        $completedComplaints = Complaint::where('comp_status', 'completed')->count();

        return view('admin.complaints.index', compact(
            'complaints',
            'totalComplaints',
            'pendingComplaints',
            'ongoingComplaints',
            'completedComplaints'
        ));
    }

    /**
     * Perform bulk actions on complaints (delete or mark as completed) and sync with Supabase.
     */
    public function bulkAction(Request $request)
    {
        $action = $request->input('action');
        $complaintIds = $request->input('selected_complaints', []);

        if (empty($complaintIds)) {
            return redirect()->route('admin.complaints')
                ->with('error', 'No complaints selected for the action.');
        }

        switch ($action) {
            case 'delete':
                $complaints = Complaint::whereIn('id', $complaintIds)->get();
                foreach ($complaints as $complaint) {
                    // Delete image from storage if exists
                    if ($complaint->comp_image) {
                        Storage::delete('public/' . $complaint->comp_image);
                    }
                    // Remove pivot rows from MySQL (if any)
                    $complaint->cleaners()->detach();
                    // Delete complaint from MySQL
                    $complaint->delete();

                    // Sync deletion with Supabase
                    try {
                        $this->supabaseService->delete('complaint', $complaint->id);
                        $this->supabaseService->deleteComplaintCleanerByComplaintId($complaint->id);
                    } catch (\Exception $e) {
                        Log::error('Failed to delete complaint from Supabase', [
                            'complaint_id' => $complaint->id,
                            'error'        => $e->getMessage()
                        ]);
                    }
                }

                $message = (count($complaintIds) > 1)
                    ? 'Selected complaints deleted successfully.'
                    : 'Complaint deleted successfully.';
                return redirect()->route('admin.complaints')->with('success', $message);

            case 'mark_completed':
                $complaints = Complaint::whereIn('id', $complaintIds)->get();
                foreach ($complaints as $complaint) {
                    // Update status in MySQL
                    $complaint->comp_status = 'completed';
                    $complaint->save();

                    // Sync status update with Supabase
                    try {
                        $this->supabaseService->update('complaint', $complaint->id, [
                            'comp_status' => 'completed'
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Failed to update complaint status in Supabase', [
                            'complaint_id' => $complaint->id,
                            'error'        => $e->getMessage()
                        ]);
                    }

                    // Set assigned cleaners to available (if any)
                    if (in_array($complaint->comp_status, ['completed', 'pending'])) {
                        foreach ($complaint->cleaners as $cleaner) {
                            $cleaner->status = 'available';
                            $cleaner->save();
                        }
                    }
                }

                $message = (count($complaintIds) > 1)
                    ? 'Selected complaints marked as completed.'
                    : 'Complaint marked as completed.';
                return redirect()->route('admin.complaints')->with('success', $message);

            default:
                return redirect()->route('admin.complaints')
                    ->with('error', 'Invalid action selected.');
        }
    }

    /**
     * Handle inline status update for a complaint and sync with Supabase.
     */
    public function inlineUpdate(Request $request, $id)
    {
        $complaint = Complaint::findOrFail($id);

        $request->validate([
            'comp_status' => 'required|in:pending,ongoing,completed'
        ]);

        $newStatus = $request->comp_status;
        $complaint->comp_status = $newStatus;

        if ($newStatus === 'pending') {
            // Remove all assigned cleaners from MySQL pivot
            $complaint->cleaners()->detach();

            // Clear assignment details in MySQL
            $complaint->assigned_by   = null;
            $complaint->assigned_date = null;

            // Sync removal of pivot rows in Supabase
            try {
                $this->supabaseService->deleteComplaintCleanerByComplaintId($complaint->id);
            } catch (\Exception $e) {
                Log::error('Failed to delete complaint_cleaner pivot rows from Supabase', [
                    'complaint_id' => $complaint->id,
                    'error'        => $e->getMessage()
                ]);
            }
        } elseif ($newStatus === 'completed') {
            foreach ($complaint->cleaners as $cleaner) {
                $cleaner->status = 'available';
                $cleaner->save();
            }
        }

        $complaint->save();

        // Sync complaint update to Supabase
        try {
            $this->supabaseService->update('complaint', $complaint->id, [
                'comp_status'   => $newStatus,
                'assigned_by'   => $complaint->assigned_by,
                'assigned_date' => $complaint->assigned_date,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to sync inline update to Supabase', [
                'complaint_id' => $complaint->id,
                'error'        => $e->getMessage()
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Status updated successfully.'
        ]);
    }

    /**
     * Show the form for editing the specified complaint.
     */
    public function editComplaint($id)
    {
        $complaint = Complaint::findOrFail($id);
        $officers = Cleaner::all();

        return view('admin.complaints.edit', compact('complaint', 'officers'));
    }

    /**
     * Update a complaint in MySQL and sync with Supabase.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'comp_status'   => 'required|string|in:pending,ongoing,completed',
            'comp_location' => 'required|string|max:255',
            'comp_desc'     => 'required|string',
            'no_of_cleaners'=> 'required|integer|min:1',
            'comp_image'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Fetch the complaint from MySQL
        $complaint = Complaint::findOrFail($id);

        // Update fields
        $complaint->comp_status    = $validated['comp_status'];
        $complaint->comp_location  = $validated['comp_location'];
        $complaint->comp_desc      = $validated['comp_desc'];
        $complaint->no_of_cleaners = $validated['no_of_cleaners'];

        // Handle image upload if provided
        if ($request->hasFile('comp_image')) {
            if ($complaint->comp_image) {
                Storage::delete($complaint->comp_image);
            }
            $path = $request->file('comp_image')->store('complaints');
            $complaint->comp_image = $path;
        }

        // If status is set to "pending", remove any assigned cleaners
        if ($validated['comp_status'] === 'pending') {
            $complaint->cleaners()->detach();
            $complaint->assigned_by   = null;
            $complaint->assigned_date = null;
        }

        $complaint->save();

        // Sync updated complaint data with Supabase
        try {
            $this->supabaseService->update('complaint', $complaint->id, [
                'comp_status'    => $complaint->comp_status,
                'comp_location'  => $complaint->comp_location,
                'comp_desc'      => $complaint->comp_desc,
                'no_of_cleaners' => $complaint->no_of_cleaners,
                'comp_image'     => $complaint->comp_image,
                'assigned_by'    => $complaint->assigned_by,
                'assigned_date'  => $complaint->assigned_date,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to sync complaint update to Supabase', [
                'complaint_id' => $complaint->id,
                'error'        => $e->getMessage()
            ]);
        }

        return redirect()->route('admin.complaints.index')
            ->with('success', 'Complaint updated successfully.');
    }

    /**
     * Delete a complaint (or multiple complaints) in MySQL and sync with Supabase.
     */
    public function destroy(Request $request, $id = null)
    {
        if ($id) {
            $complaintIds = [$id];
        } else {
            $complaintIds = $request->input('selected_complaints');
            if (!$complaintIds || !is_array($complaintIds)) {
                return redirect()->route('admin.complaints')->with('error', 'No complaints selected for deletion.');
            }
        }

        $complaints = Complaint::whereIn('id', $complaintIds)->get();
        foreach ($complaints as $complaint) {
            if ($complaint->comp_image) {
                Storage::delete('public/' . $complaint->comp_image);
            }
            // Remove any related pivot rows in MySQL first
            $complaint->cleaners()->detach();
            $complaint->delete();

            // Sync deletion with Supabase
            try {
                $this->supabaseService->delete('complaint', $complaint->id);
                $this->supabaseService->deleteComplaintCleanerByComplaintId($complaint->id);
            } catch (\Exception $e) {
                Log::error('Failed to sync complaint deletion to Supabase', [
                    'complaint_id' => $complaint->id,
                    'error'        => $e->getMessage()
                ]);
            }
        }

        return redirect()->route('admin.complaints')->with('success', 'Complaint(s) deleted successfully.');
    }

    /**
     * Assign cleaners to a complaint (creates pivot rows) and sync both MySQL and Supabase.
     */
    public function assignCleaner(Request $request, $id)
    {
        Log::info('Starting the task assignment process', [
            'complaint_id' => $id,
            'request_data' => $request->all(),
        ]);

        // Fetch the complaint from MySQL if it's unassigned.
        $complaint = Complaint::where('id', $id)
            ->whereNull('assigned_by')
            ->first();

        if (!$complaint) {
            Log::warning('Complaint not found or already assigned', ['complaint_id' => $id]);
            return redirect()->route('supervisor.complaints.show', $id)
                ->withErrors('Complaint not found or already assigned');
        }

        $validated = $request->validate([
            'no_of_cleaners' => 'required|integer|min:1',
            'assigned_by'    => 'required|integer',
            'cleaners'       => 'required|array|min:1',
            'cleaners.*'     => 'exists:cleaners,id',
        ]);

        $assignedDate = now();

        try {
            foreach ($validated['cleaners'] as $cleanerId) {
                // Create the pivot record in MySQL.
                ComplaintCleaner::create([
                    'complaint_id'   => $complaint->id,
                    'cleaner_id'     => $cleanerId,
                    'no_of_cleaners' => $validated['no_of_cleaners'],
                    'assigned_by'    => $validated['assigned_by'],
                    'assigned_date'  => $assignedDate,
                ]);

                // Update the cleaner's status in MySQL.
                Cleaner::where('id', $cleanerId)->update(['status' => 'unavailable']);
            }

            // Update the complaint record in MySQL.
            $complaint->update([
                'comp_status'    => 'ongoing',
                'assigned_by'    => $validated['assigned_by'],
                'assigned_date'  => $assignedDate,
                'no_of_cleaners' => $validated['no_of_cleaners'],
            ]);

            Log::info('Complaint & pivot updated in MySQL', [
                'complaint_id' => $complaint->id,
                'comp_status'  => $complaint->comp_status,
            ]);
        } catch (\Exception $e) {
            Log::error('Error assigning task to cleaners in MySQL', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('supervisor.complaints.show', $id)
                ->withErrors('Failed to assign task to cleaners');
        }

        // Sync data with Supabase.
        try {
            $this->supabaseService->update('complaint', $complaint->id, [
                'comp_status' => $complaint->comp_status,
            ]);

            foreach ($validated['cleaners'] as $cleanerId) {
                $data = [
                    'complaint_id'   => $complaint->id,
                    'cleaner_id'     => $cleanerId,
                    'no_of_cleaners' => $validated['no_of_cleaners'],
                    'assigned_by'    => $validated['assigned_by'],
                    'assigned_date'  => $assignedDate->toIso8601String(),
                ];
                $this->supabaseService->storeComplaintCleaner($data);
            }

            Log::info('Data synced to Supabase', [
                'complaint_id' => $complaint->id,
                'cleaners'     => $validated['cleaners'],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to sync data with Supabase', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('supervisor.complaints.show', $id)
                ->withErrors('Task assigned locally but failed to sync with Supabase.');
        }

        return redirect()->route('supervisor.complaints.show', $id)
            ->with('success', 'Task assigned successfully.');
    }

    /**
     * Display the details of a complaint.
     */
    public function show($id)
    {
        $complaint = Complaint::with(['supervisor', 'cleaners'])->findOrFail($id);
        $availableCleaners = Cleaner::where('status', 'available')->get();

        return view('supervisor.complaints.show', compact('complaint', 'availableCleaners'));
    }

    /**
     * Show the most recent complaint on the dashboard.
     */
    public function showDashboard()
    {
        $recentComplaint = Complaint::latest('comp_date')->latest('comp_time')->first();
        return view('dashboard', compact('recentComplaint'));
    }

    // API Functions

    /**
     * Store a new complaint via API and sync with Supabase.
     */
    public function apistore(Request $request)
    { 
        $validatedData = $request->validate([
            'comp_image'    => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'comp_date'     => 'required|date',
            'comp_time'     => 'required',
            'comp_desc'     => 'required|string',
            'comp_location' => 'required|string',
        ]);

        $complaint = Complaint::create(array_merge($validatedData, [
            'officer_id'  => Auth::id(),
            'comp_status' => 'pending',
        ]));

        if ($request->hasFile('comp_image')) {
            $media = $complaint->addMediaFromRequest('comp_image')->toMediaCollection('complaint_images', 'public');
            $complaint->update(['comp_image' => $media->getUrl()]);
            Log::info('Media uploaded:', ['media' => $media]);
        }

        try {
            $response = $this->supabaseService->store('complaint', [
                'officer_id'    => $complaint->officer_id,
                'comp_date'     => $complaint->comp_date,
                'comp_time'     => $complaint->comp_time,
                'comp_desc'     => $complaint->comp_desc,
                'comp_location' => $complaint->comp_location,
                'comp_status'   => $complaint->comp_status,
                'comp_image'    => $complaint->comp_image ?? null, 
            ]);

            return response()->json([
                'message'   => 'Complaint submitted successfully!',
                'complaint' => $complaint,
                'supabase'  => $response,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to store complaint in Supabase', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Complaint submitted locally, but failed to store in Supabase.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get complaints of the currently authenticated officer.
     */
    public function getOfficerComplaints()
    {
        $officerId = Auth::id();

        if (!$officerId) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $complaints = Complaint::where('officer_id', $officerId)
            ->orderBy('comp_date', 'desc')
            ->orderBy('comp_time', 'desc')
            ->get();

        return response()->json($complaints, 200);
    }

    /**
     * Get detailed information of a specific complaint.
     */
    public function getComplaintDetails($id)
    {
        $complaint = Complaint::find($id);

        if (!$complaint) {
            return response()->json(['error' => 'Complaint not found'], 404);
        }

        $compImageUrl = $complaint->getFirstMediaUrl('complaint_images')
            ? url($complaint->getFirstMediaUrl('complaint_images'))
            : null;

        $complaintDetails = [
            'id'             => $complaint->id,
            'comp_date'      => $complaint->comp_date,
            'comp_time'      => $complaint->comp_time,
            'comp_desc'      => $complaint->comp_desc,
            'comp_location'  => $complaint->comp_location,
            'comp_image'     => $compImageUrl,
            'officer_id'     => $complaint->officer_id,
            'assigned_by'    => $complaint->assigned_by,
            'assigned_date'  => $complaint->assigned_date,
            'no_of_cleaners' => $complaint->no_of_cleaners,
            'created_at'     => $complaint->created_at,
            'updated_at'     => $complaint->updated_at,
            'comp_status'    => $complaint->comp_status,
        ];

        return response()->json($complaintDetails, 200);
    }

    /**
     * Get all pending complaints.
     */
    public function getPendingComplaints()
    {
        $pendingComplaints = Complaint::where('comp_status', 'pending')->get();
        return response()->json($pendingComplaints);
    }

    /**
     * Notify relevant users (supervisors, cleaners, and the officer) about a complaint.
     */
    public function notifyRelevantUsers(Complaint $complaint)
    {
        $supervisors = User::role('supervisor')->where('is_active', true)->get();

        foreach ($supervisors as $supervisor) {
            $supervisor->notify(new ComplaintNotification($complaint, $complaint->officer, false));
        }

        $cleaners = $complaint->cleaners;
        foreach ($cleaners as $cleaner) {
            $cleaner->notify(new ComplaintNotification($complaint, $complaint->officer, $cleaner, $complaint->supervisor));
        }

        $officer = $complaint->officer;
        $officer->notify(new ComplaintNotification($complaint, $officer, true));
    }

    /**
     * Display a listing of complaints for the supervisor site (only pending complaints).
     */
    public function supervisorIndex(Request $request)
    {
        $query = Complaint::where('comp_status', 'pending');

        if ($request->filled('date')) {
            $query->whereDate('comp_date', $request->date);
        }

        $complaints = $query->orderBy('comp_date', 'desc')->paginate(10);

        return view('supervisor.complaints.index', compact('complaints'));
    }

    /**
     * Show the form for editing a complaint on the supervisor site.
     */
    public function edit($id)
    {
        $complaint = Complaint::findOrFail($id);
        return view('supervisor.complaints.edit', compact('complaint'));
    }
}
