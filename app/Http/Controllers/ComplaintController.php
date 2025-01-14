<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Cleaner;
use App\Models\User;
use App\Service\SupabaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage; // Ensure Storage facade is imported
use App\Notifications\ComplaintNotification;


class ComplaintController extends Controller
{

    protected $supabase;

    // Constructor to initialize SupabaseService
    public function __construct(SupabaseService $supabase)
    {
        $this->supabase = $supabase;
    }

    /**
     * Display a listing of the complaints with optional filtering and sorting.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $perPage = $request->input('per_page', 10);

        $query = Complaint::with(['officer', 'assignedBy'])->orderBy('created_at', 'desc');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('comp_desc', 'like', "%{$search}%")
                  ->orWhere('comp_location', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('comp_status', $status);
        }

        $complaints = $query->paginate($perPage);

        $totalComplaints = Complaint::count();
        $pendingComplaints = Complaint::where('comp_status', 'pending')->count();
        $ongoingComplaints = Complaint::where('comp_status', 'ongoing')->count();
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
     * Handle bulk actions on complaints.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bulkAction(Request $request)
    {
        $action = $request->input('action');
        $complaintIds = $request->input('selected_complaints', []);

        if (empty($complaintIds)) {
            return redirect()->route('admin.complaints')->with('error', 'No complaints selected for the action.');
        }

        switch ($action) {
            case 'delete':
                $complaints = Complaint::whereIn('id', $complaintIds)->get();
                foreach ($complaints as $complaint) {
                    if ($complaint->comp_image) {
                        Storage::delete('public/' . $complaint->comp_image);
                    }
                    $complaint->delete();
                }

                $message = count($complaintIds) > 1
                    ? 'Selected complaints deleted successfully.'
                    : 'Complaint deleted successfully.';
                return redirect()->route('admin.complaints')->with('success', $message);

            case 'mark_completed':
                $complaints = Complaint::whereIn('id', $complaintIds)->get();
                foreach ($complaints as $complaint) {
                    $complaint->comp_status = 'completed';
                    $complaint->save();
                }

                $message = count($complaintIds) > 1
                    ? 'Selected complaints marked as completed.'
                    : 'Complaint marked as completed.';
                return redirect()->route('admin.complaints')->with('success', $message);

            default:
                return redirect()->route('admin.complaints')->with('error', 'Invalid action selected.');
        }
    }

    /**
     * Handle inline status updates.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id  Complaint ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function inlineUpdate(Request $request, $id)
    {
        $complaint = Complaint::findOrFail($id);
        $request->validate([
            'comp_status' => 'required|in:pending,ongoing,completed'
        ]);

        $complaint->comp_status = $request->comp_status;
        $complaint->save();

        return response()->json(['status' => 'success', 'message' => 'Status updated successfully.']);
    }

    /**
     * Show the form for editing the specified complaint.
     *
     * @param  int  $id  Complaint ID
     * @return \Illuminate\View\View
     */
    public function editComplaint($id)
    {
        $complaint = Complaint::findOrFail($id);
        $officers = Cleaner::all();

        return view('admin.complaints.edit', compact('complaint', 'officers'));
    }

    /**
     * Update the specified complaint in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id  Complaint ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'comp_status'    => 'required|string|in:pending,ongoing,completed',
            'comp_location'  => 'required|string|max:255',
            'comp_desc'      => 'required|string',
            'no_of_cleaners' => 'required|integer|min:1',
            'comp_image'     => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $complaint = Complaint::findOrFail($id);
        $complaint->comp_status = $request->comp_status;
        $complaint->comp_location = $request->comp_location;
        $complaint->comp_desc = $request->comp_desc;
        $complaint->no_of_cleaners = $request->no_of_cleaners;

        if ($request->hasFile('comp_image')) {
            if ($complaint->comp_image) {
                Storage::delete($complaint->comp_image);
            }
            $path = $request->file('comp_image')->store('complaints');
            $complaint->comp_image = $path;
        }

        $complaint->save();

        return redirect()->route('admin.complaints.index')->with('success', 'Complaint updated successfully.');
    }

    /**
     * Remove the specified complaint from storage.
     *
     * @param  Request $request
     * @param  int|null  $id  Complaint ID
     * @return \Illuminate\Http\RedirectResponse
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
            $complaint->delete();
        }

        return redirect()->route('admin.complaints')->with('success', 'Complaint(s) deleted successfully.');
    }

    public function assignCleaner(Request $request, $id)
    {
        Log::info('AssignCleaner process started', [
            'complaint_id'  => $id,
            'supervisor_id' => Auth::id(),
            'request_data'  => $request->all(),
        ]);
    
        try {
            $validated = $request->validate([
                'no_of_cleaners' => 'required|integer|min:1|max:3',
                'cleaners'       => 'required|array|size:' . $request->no_of_cleaners,
                'cleaners.*'     => 'exists:cleaners,user_id',
            ]);
    
            Log::info('Validation passed', ['validated_data' => $validated]);
    
            $complaint = Complaint::findOrFail($id);
            Log::info('Complaint fetched', ['complaint' => $complaint]);
    
            if ($complaint->comp_status !== Complaint::STATUS_PENDING) {
                Log::warning('Attempt to assign cleaners to a non-pending complaint', ['complaint_id' => $id]);
                return redirect()->route('supervisor.complaints.show', $id)
                    ->withErrors('Cleaners have already been assigned or the complaint is not pending.');
            }
    
            $complaint->assignCleaners($validated['cleaners'], Auth::id(), $validated['no_of_cleaners']);
    
            // Notify assigned cleaners
            foreach ($validated['cleaners'] as $cleanerId) {
                $cleaner = Cleaner::find($cleanerId); 
                if ($cleaner) {
                    $cleaner->notify(new ComplaintNotification(
                        $complaint,
                        $complaint->officer,
                        $cleaner
                    ));
                    Log::info('Notification sent to cleaner', ['cleaner_id' => $cleanerId]);
                }
            }
    
            // Notify the officer who submitted the complaint
            $officer = $complaint->officer; // Assuming `officer` relationship is defined in the `Complaint` model
            if ($officer) {
                $officer->notify(new ComplaintNotification(
                    $complaint,
                    $officer
                ));
                Log::info('Notification sent to officer', ['officer_id' => $officer->id]);
            }
    
            Log::info('Assign Cleaner process completed successfully', ['complaint_id' => $id]);
    
            return redirect()->route('supervisor.complaints.show', $id)
                ->with('success', 'Cleaners assigned successfully.');
        } catch (\Exception $e) {
            Log::error('Error assigning cleaners', [
                'complaint_id'  => $id,
                'supervisor_id' => Auth::id(),
                'error'         => $e->getMessage(),
                'trace'         => $e->getTraceAsString(),
            ]);
    
            if (app()->environment('local')) {
                return redirect()->route('supervisor.complaints.show', $id)
                    ->withErrors($e->getMessage());
            }
    
            return redirect()->route('supervisor.complaints.show', $id)
                ->withErrors('An error occurred while assigning cleaners. Please try again.');
        }
    }

    /**
     * Display the specified complaint details.
     *
     * @param  int  $id  Complaint ID
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $complaint = Complaint::with(['supervisor', 'cleaners'])->findOrFail($id);
        $availableCleaners = Cleaner::where('status', 'available')->get();

        return view('supervisor.complaints.show', compact('complaint', 'availableCleaners'));
    }

    /**
     * Show recent complaint on the dashboard (Web)
     *
     * @return \Illuminate\View\View
     */
    public function showDashboard()
    {
        $recentComplaint = Complaint::latest('comp_date')->latest('comp_time')->first();
        return view('dashboard', compact('recentComplaint'));
    }

    // API Functions

    /**
     * Store a new complaint (API)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
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


        //$supervisors = User::role('supervisor')->get();
        //Notification::send($supervisors, new ComplaintNotification($complaint, Auth::user(), false));

        try {
            $response = $this->supabase->store('complaint', [
                'officer_id'    => $complaint->officer_id,
                'comp_date'     => $complaint->comp_date,
                'comp_time'     => $complaint->comp_time,
                'comp_desc'     => $complaint->comp_desc,
                'comp_location' => $complaint->comp_location,
                'comp_status'   => $complaint->comp_status,
                'comp_image'    => $complaint->comp_image ?? null, // Media URL if available
            ]);

            return response()->json([
                'message'    => 'Complaint submitted successfully!',
                'complaint'  => $complaint,
                'supabase'   => $response,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to store complaint in Supabase', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Complaint submitted locally, but failed to store in Supabase.',
                'error'   => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message'   => 'Complaint submitted successfully!',
            'complaint' => $complaint,
        ]);
    }

    /**
     * Get officer complaints (API)
     *
     * @return \Illuminate\Http\JsonResponse
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
     * Get details of a specific complaint (API)
     *
     * @param  int  $id  Complaint ID
     * @return \Illuminate\Http\JsonResponse
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
     * Get pending complaints (API)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPendingComplaints()
    {
        $pendingComplaints = Complaint::where('comp_status', 'pending')->get();
        return response()->json($pendingComplaints);
    }

    /**
     * Assign cleaner to complaint (API)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignCleanerToComplaint(Request $request)
    {
        $validated = $request->validate([
            'complaint_id' => 'required|exists:complaints,id',
            'cleaner_id'   => 'required|exists:users,id',
        ]);

        DB::transaction(function () use ($validated) {
            DB::table('complaint_cleaner')->insert([
                'complaint_id'  => $validated['complaint_id'],
                'cleaner_id'    => $validated['cleaner_id'],
                'assigned_date' => now(),
                'assigned_by'   => Auth::id(),
            ]);

            Complaint::where('id', $validated['complaint_id'])->update(['comp_status' => 'ongoing']);
            User::where('id', $validated['cleaner_id'])->update(['status' => 'unavailable']);
        });

        return response()->json(['message' => 'Cleaner assigned successfully']);
    }

    /**
     * Notify relevant users about a complaint
     *
     * @param  \App\Models\Complaint  $complaint
     * @return void
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
     * Display a listing of the complaints for the supervisor site.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function supervisorIndex(Request $request)
    {
        // Always include only 'pending' complaints
        $query = Complaint::where('comp_status', 'pending');

        // Check if a date filter is provided
        if ($request->filled('date')) {
            $query->whereDate('comp_date', $request->date);
        }

        // Order by date and paginate
        $complaints = $query->orderBy('comp_date', 'desc')->paginate(10);

        return view('supervisor.complaints.index', compact('complaints'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $complaint = Complaint::findOrFail($id);
        return view('supervisor.complaints.edit', compact('complaint'));
    }

    
}

