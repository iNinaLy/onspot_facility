<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Cleaner;
use App\Models\User;
use App\Services\SupabaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ComplaintCleaner;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage; // Ensure Storage facade is imported
use App\Notifications\ComplaintNotification;
use Illuminate\Support\Facades\Log;


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

    /**
     * Assign cleaner to the specified complaint.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
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

            /**
             * ADDITION: Notify each assigned cleaner AND the officer about the assignment.
             */
            foreach ($validated['cleaners'] as $cleanerId) {
                $cleanerUser = User::find($cleanerId);
                if ($cleanerUser) {
                    ComplaintNotification::notifyAssignment($complaint, $complaint->officer, $cleanerUser, Auth::user());
                }
            }

            if ($complaint->officer) {
                ComplaintNotification::notifyAssignment($complaint, $complaint->officer, null, Auth::user());
            }

            Log::info('AssignCleaner process completed successfully', ['complaint_id' => $id]);

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
     * [REMOVED] Submit a new complaint via Web.
     * (As requested, the original submitComplaint() method has been removed.)
     */

    /**
     * Show the form for creating a new complaint.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $officers = Cleaner::all();

        return view('admin.complaints.create', compact('officers'));
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
    
        // **API Functions (From Current Version)**
    
        // Store a new complaint (API)
        public function apistore(Request $request)
        {
            // Step 1: Validate the request data
            $validatedData = $request->validate([
                'comp_image'    => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'comp_date'     => 'required|date',
                'comp_time'     => 'required',
                'comp_desc'     => 'required|string',
                'comp_location' => 'required|string',
            ]);
        
            // Step 2: Create the complaint in MySQL
            $complaint = Complaint::create(array_merge($validatedData, [
                'officer_id'    => Auth::id(),
                'comp_status'   => 'pending',
            ]));
        
            // Step 3: Handle optional image upload
            if ($request->hasFile('comp_image')) {
                $media = $complaint->addMediaFromRequest('comp_image')
                                   ->toMediaCollection('complaint_images', 'public');
                $complaint->update(['comp_image' => $media->getUrl()]);
                Log::info('Media uploaded:', ['media' => $media]);
            }
        
            // Step 4: Sync complaint data to Supabase
            try {
                $response = $this->supabase->store('complaint', [
                    'id'            => $complaint->id, // Explicitly use the same ID to avoid mismatches
                    'officer_id'    => $complaint->officer_id,
                    'comp_date'     => $complaint->comp_date,
                    'comp_time'     => $complaint->comp_time,
                    'comp_desc'     => $complaint->comp_desc,
                    'comp_location' => $complaint->comp_location,
                    'comp_status'   => $complaint->comp_status,
                    'comp_image'    => $complaint->comp_image ?? null, // Include image URL if uploaded
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
        
            // Step 5: Return a fallback success response
            return response()->json([
                'message'   => 'Complaint submitted successfully!',
                'complaint' => $complaint,
            ]);
        }        
    
        // Get officer complaints (API)
        public function getOfficerComplaints()
        {
            $officerId = Auth::id();
    
            if (!$officerId) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
    
            try {
                $complaints = Complaint::where('officer_id', $officerId)
                    ->orderBy('comp_date', 'desc')
                    ->orderBy('comp_time', 'desc')
                    ->get();
    
                return response()->json($complaints, 200);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Server error: ' . $e->getMessage()], 500);
            }
        }

    // Get details of a specific complaint
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

   
    public function getPendingComplaints()
    {
        $pendingComplaints = Complaint::where('comp_status', 'pending')->get();
        return response()->json($pendingComplaints);
    }

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


    public function edit($id)
    {
        $complaint = Complaint::findOrFail($id);
        return view('supervisor.complaints.edit', compact('complaint'));
    }
    

    // fetch complaints that are unassigned and have a status of 'pending'
    public function getComplaints()
    {
        // Retrieve all complaints where assigned_by is null and comp_status is pending
        $complaints = Complaint::select('id', 'comp_date', 'comp_location', 'comp_time', 'comp_desc', 'officer_id', 'assigned_by', 'comp_status') 
            ->whereNull('assigned_by')
            ->where('comp_status', Complaint::STATUS_PENDING) // Filter by pending status
            ->get();

        return response()->json($complaints);
    } 


    public function AssignTask(Request $request, $id)
    {
        // Step 1: Fetch the complaint from MySQL (complaints table)
        $complaint = Complaint::where('id', $id)
            ->whereNull('assigned_by') // Only fetch unassigned complaints
            ->first();
    
        if (!$complaint) {
            Log::warning('Complaint not found or already assigned', ['complaint_id' => $id]);
            return response()->json(['message' => 'Complaint not found or already assigned'], 404);
        }
    
        Log::info('Complaint found for assignment', ['complaint' => $complaint]);
    
        // Step 2: Validate the request
        $validated = $request->validate([
            'cleaner_ids' => 'required|array|min:1',
            'no_of_cleaners' => 'required|integer|min:1',
            'assigned_by' => 'required|integer',
        ]);
    
        Log::info('Validated task assignment request', [
            'complaint_id' => $complaint->id,
            'validated_data' => $validated,
        ]);
    
        $assignedDate = now();
    
        // Step 3: Assign task to cleaners and update complaint_cleaner table in MySQL
        try {
            foreach ($validated['cleaner_ids'] as $cleanerUserId) {
                $cleaner = Cleaner::where('user_id', $cleanerUserId)->firstOrFail();
    
                ComplaintCleaner::create([
                    'complaint_id' => $complaint->id,
                    'cleaner_id' => $cleaner->user_id,
                    'no_of_cleaners' => $validated['no_of_cleaners'],
                    'assigned_by' => $validated['assigned_by'],
                    'assigned_date' => $assignedDate,
                ]);
    
                Log::info('Task assigned to cleaner in MySQL', [
                    'complaint_id' => $complaint->id,
                    'cleaner_id' => $cleaner->user_id,
                ]);
    
                // Update cleaner's status to unavailable
                $cleaner->update(['status' => 'unavailable']);
            }
    
            // Notify cleaners via ComplaintNotification
            foreach ($validated['cleaner_ids'] as $cleanerUserId) {
                $cleaner = Cleaner::where('user_id', $cleanerUserId)->first();
                if (!$cleaner) {
                    Log::warning('Cleaner not found in database', ['cleaner_id' => $cleanerUserId]);
                } elseif (!$cleaner->name) {
                    Log::warning('Cleaner found but missing name', ['cleaner_id' => $cleanerUserId]);
                } else {
                    // Proceed with notification
                    ComplaintNotification::notifyAssignment($complaint, Auth::user(), $cleaner, null);
                    Log::info('Notification sent to cleaner', ['cleaner_id' => $cleaner->user_id]);
                }
                
            }
            
        } catch (\Exception $e) {
            Log::error('Error assigning task to cleaners in MySQL', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to assign task to cleaners'], 500);
        }
    
        // Step 4: Update the complaint with assignment details in MySQL
        try {
            $complaint->update([
                'comp_status' => Complaint::STATUS_ONGOING,
                'assigned_by' => $validated['assigned_by'],
                'assigned_date' => $assignedDate,
                'no_of_cleaners' => $validated['no_of_cleaners'],
            ]);
    
            Log::info('Complaint updated with assignment details in MySQL', [
                'complaint_id' => $complaint->id,
                'status' => Complaint::STATUS_ONGOING,
                'assigned_by' => $validated['assigned_by'],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update complaint in MySQL', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to update complaint details'], 500);
        }
    
        $supabaseComplaintCleaner = [];
        $supabaseComplaintUpdate = null;
    
        // Step 5: Sync data to Supabase
        try {
            $supabaseComplaintUpdate = $this->supabase->update('complaint', $complaint->id, [
                'comp_status' => Complaint::STATUS_ONGOING,
            ]);
    
            Log::info('Updated complaint status in Supabase', ['complaint_id' => $complaint->id]);
    
            foreach ($validated['cleaner_ids'] as $cleanerUserId) {
                $mysqlCleaner = User::where('id', $cleanerUserId)->first();
                if (!$mysqlCleaner) {
                    Log::error('Cleaner not found in MySQL', ['cleaner_id' => $cleanerUserId]);
                    continue;
                }
    
                $data = [
                    'complaint_id' => $complaint->id,
                    'cleaner_id' => $mysqlCleaner->id,
                    'no_of_cleaners' => $validated['no_of_cleaners'],
                    'assigned_by' => $validated['assigned_by'],
                    'assigned_date' => $assignedDate->toIso8601String(),
                ];
                $supabaseComplaintCleaner[] = $this->supabase->store('complaint_cleaner', $data);
                Log::info('Inserted task into Supabase (complaint_cleaner)', $data);
            }
        } catch (\Exception $e) {
            Log::error('Failed to sync data with Supabase', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Task assigned locally but failed to sync with Supabase.',
                'error' => $e->getMessage(),
            ], 500);
        }
    
        // Step 6: Return success response with MySQL and Supabase data
        return response()->json([
            'message' => 'Task assigned successfully',
            'mysql' => [
                'complaint' => $complaint->only(['id', 'status', 'assigned_date']),
                'complaint_cleaners' => ComplaintCleaner::where('complaint_id', $complaint->id)->pluck('cleaner_id'),
            ],
            'supabase' => [
                'complaint' => $supabaseComplaintUpdate,
                'complaint_cleaners' => $supabaseComplaintCleaner,
            ],
        ]);
        
    }    
    

    // fetch complaint details
    public function apigetComplaintDetails($id)
    {
        // Retrieve specific fields from the complaints table for the given complaint ID
        $complaint = Complaint::select('id', 'comp_location', 'comp_date', 'comp_desc', 'officer_id', 'comp_status', 'comp_image')
            ->where('id', $id)
            ->first();

        // Check if complaint was found
        if (!$complaint) {
            return response()->json(['message' => 'Complaint not found'], 404);
        }

        // Retrieve officer name from `users` table
        $user = User::select('name')
            ->where('id', $complaint->officer_id)
            ->first();
        $officerName = $user ? $user->name : 'Unknown Officer';

        // Retrieve available cleaners' `user_id` and `cleaner_name`
        $availableCleaners = Cleaner::select('user_id', 'cleaner_name')
            ->where('status', 'available')
            ->get();

        // Prepare data to send to frontend
        $complaintData = [
            'id' => (string) $complaint->id,
            'comp_location' => $complaint->comp_location ?? 'No Location',
            'comp_date' => (string) $complaint->comp_date,
            'comp_desc' => (string) $complaint->comp_desc,
            'officer_name' => $officerName,
            'comp_status' => $complaint->comp_status,
            'comp_image_url' => $complaint->comp_image && file_exists(storage_path('app/public/' . $complaint->comp_image))
                ? url('storage/' . $complaint->comp_image)
                : null, // Return null if no image
            'available_cleaners' => $availableCleaners->map(function ($cleaner) {
                return [
                    'cleaner_id' => (string) $cleaner->user_id, // Use user_id instead of id
                    'cleaner_name' => $cleaner->cleaner_name,
                ];
            }),
        ];

        return response()->json($complaintData);
    }
        
    public function getHistory(Request $request)
    {
        // Get supervisor's ID from request (assuming it's passed with the token)
        $supervisorId = $request->user()->id;
    
        // Get the optional filters from the request and sanitize them
        $statusFilter = trim($request->query('comp_status', ''));
        $monthFilter = trim($request->query('month', ''));
    
        // Fetch tasks assigned by this supervisor with optional filtering
        $tasks = Complaint::where('assigned_by', $supervisorId)
            ->when($statusFilter, function ($query) use ($statusFilter) {
                if (!empty($statusFilter)) {
                    $query->where('comp_status', $statusFilter);
                }
            })
            ->when($monthFilter, function ($query) use ($monthFilter) {
                if (!empty($monthFilter)) {
                    $query->whereMonth('comp_date', $monthFilter); // Filter by month (expects numeric value)
                }
            })
            ->select('id', 'comp_desc', 'no_of_cleaners', 'comp_status', 'comp_date')
            ->orderBy('comp_date', 'desc') // Order by date, latest first
            ->get();
    
        // Return tasks in JSON format
        return response()->json($tasks);
    }    
    

    // fetch history details
    public function getHistoryDetails($id)
    {
        // Retrieve the complaint details including location, description, etc.
        $complaint = Complaint::select('id', 'comp_desc', 'comp_time', 'comp_date', 'comp_location', 'officer_id', 'comp_status', 'no_of_cleaners', 'assigned_date', 'comp_image')
            ->where('id', $id)
            ->first();

        if (!$complaint) {
            return response()->json(['message' => 'Complaint not found'], 404);
        }

        // Retrieve the officer's name based on the officer_id
        $officer = User::select('name')->where('id', $complaint->officer_id)->first();
        $officerName = $officer ? $officer->name : 'Unknown Officer';

        // Retrieve assigned cleaners for the task using `user_id` from the `cleaners` table
        $assignedCleaners = ComplaintCleaner::where('complaint_id', $complaint->id)
            ->join('cleaners', 'complaint_cleaner.cleaner_id', '=', 'cleaners.user_id') // Use `user_id` for the join
            ->select('cleaners.user_id as cleaner_id', 'cleaners.cleaner_name') // Select `user_id` as `cleaner_id`
            ->get();

        // Check if image exists and construct URL
        $compImageUrl = $complaint->comp_image && file_exists(storage_path('app/public/' . $complaint->comp_image))
            ? url('storage/' . $complaint->comp_image)
            : null;

        // Prepare the response data
        $responseData = [
            'complaint_id' => $complaint->id,
            'comp_desc' => $complaint->comp_desc,
            'comp_time' => $complaint->comp_time,
            'comp_date' => $complaint->comp_date,
            'comp_location' => $complaint->comp_location,
            'officer_name' => $officerName,
            'comp_image_url' => $compImageUrl, // Return null if no image
            'assigned_cleaners' => $assignedCleaners,
            'comp_status' => $complaint->comp_status,
            'no_of_cleaners' => $complaint->no_of_cleaners,
            'assigned_date' => $complaint->assigned_date,
        ];

        return response()->json($responseData);
    }


    // Laravel Controller Method
public function getUserNames(Request $request)
{
    $userIds = $request->input('user_ids');

    if (!$userIds || !is_array($userIds)) {
        return response()->json(['message' => 'Invalid user IDs'], 400);
    }

    // Fetch user names directly from MySQL `users` table
    $users = DB::table('users')
        ->whereIn('id', $userIds)
        ->select('id', 'name')
        ->get();

    return response()->json($users);
}

    public function completeComplaint(Request $request, $id)
        {
            // Find the complaint and ensure it exists
            $complaint = Complaint::findOrFail($id);
        
            // Validate the current status of the complaint
            if ($complaint->comp_status !== 'ongoing') {
                return response()->json(['error' => 'Only ongoing complaints can be marked as complete.'], 400);
            }
        
            DB::transaction(function () use ($complaint) {
                // Update the complaint's status to 'completed'
                $complaint->update([
                    'comp_status' => 'completed',
                    'completed_by' => Auth::id(), // Optional: Track who marked it as completed
                    'completed_at' => now(), // Optional: Add timestamp for completion
                ]);
        
                // Update all assigned cleaners' status to 'available'
                $cleaners = $complaint->cleaners; // Assuming a relationship exists
                foreach ($cleaners as $cleaner) {
                    $cleaner->update(['status' => 'available']);
                }
            });
        
            return response()->json([
                'message' => 'Complaint marked as completed, and cleaners updated to available.',
                'complaint' => $complaint,
            ]);
        }        
    
}    
