<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Cleaner;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ComplaintCleaner;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewCleaningComplaint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class ComplaintController extends Controller
{
    // Admin: Fetch complaints with optional filtering and sorting (Web)
    public function index(Request $request)
    {
        $query = Complaint::query();

        if ($request->filled('status')) {
            $query->where('comp_status', $request->status);
        }

        $complaints = $query->with(['officer', 'supervisor'])->paginate(10);
        return view('admin.complaints.index', compact('complaints'));
    }

    // Store a new complaint (Web)
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'comp_desc' => 'required|string|max:255',
            'comp_location' => 'required|string|max:255',
            'comp_date' => 'required|date',
            'comp_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'officer_id' => 'required|exists:users,id',
            'cleaner_id' => 'nullable|exists:users,id',
        ]);

        $complaint = Complaint::create(array_merge($validatedData, [
            'comp_status' => 'pending',
        ]));

        if ($request->hasFile('comp_image')) {
            $complaint->addMedia($request->file('comp_image'))
                      ->toMediaCollection('complaint_images');
        }

        $supervisors = User::where('role', 'supervisor')->get();
        Notification::send($supervisors, new NewCleaningComplaint($complaint));

        return redirect()->route('supervisor.complaints.index')
                         ->with('success', 'Complaint created and notification sent successfully.');
    }

    // Supervisor: Show complaint details with available cleaners (Web)
    public function show($id)
    {
        $complaint = Complaint::with('cleaners')->findOrFail($id);
        $availableCleaners = Cleaner::where('status', 'available')->get();
        return view('supervisor.complaints.show', compact('complaint', 'availableCleaners'));
    }

    // Assign cleaners to the complaint (Web)
    public function assignCleaner(Request $request, $id)
    {
        $request->validate([
            'no_of_cleaners' => 'required|integer|min:1|max:3',
            'cleaners' => 'required|array|size:' . $request->no_of_cleaners,
            'cleaners.*' => 'exists:users,id',
        ]);

        $complaint = Complaint::findOrFail($id);

        // Check if cleaners have already been assigned to this complaint
        if ($complaint->cleaners()->exists()) {
            return redirect()->route('supervisor.complaints.show', $id)
                            ->withErrors('Cleaners have already been assigned for this complaint.');
        }

        // Use a transaction to ensure atomicity
        DB::transaction(function () use ($request, $complaint) {
            // Prepare assignments with the supervisor ID, date, and number of cleaners
            $assignments = [];
            foreach ($request->cleaners as $cleanerId) {
                $assignments[$cleanerId] = [
                    'assigned_by' => Auth::id(),
                    'assigned_date' => now(),
                    'no_of_cleaners' => $request->no_of_cleaners,
                ];
            }

            // Attach cleaners with pivot data and update complaint status
            $complaint->cleaners()->attach($assignments);
            $complaint->update([
                'comp_status' => 'ongoing',
                'no_of_cleaners' => $request->no_of_cleaners,
                'assigned_by' => Auth::id(),
                'assigned_date' => now(),
            ]);
        });

        return redirect()->route('supervisor.complaints.show', $id)
                        ->with('success', 'Cleaners assigned successfully.');
    }


    // Supervisor: List complaints (Web)
    public function supervisorIndex()
    {
        $complaints = Complaint::orderBy('comp_date', 'desc')->paginate(10);
        return view('supervisor.complaints.index', compact('complaints'));
    }

    // Admin: Show recent complaint on the dashboard (Web)
    public function showDashboard()
    {
        $recentComplaint = Complaint::latest('comp_date')->latest('comp_time')->first();
        return view('dashboard', compact('recentComplaint'));
    }

    // Update complaint details (Web)
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'comp_date' => 'required|date',
            'comp_time' => 'required|date_format:H:i',
            'comp_desc' => 'required|string|max:255',
            'comp_location' => 'required|string|max:255',
            'comp_status' => 'required|string|in:pending,ongoing,completed',
        ]);

        $complaint = Complaint::findOrFail($id);
        $complaint->update($validatedData);

        return response()->json($complaint, 200);
    }

    // Delete a complaint by ID (Web)
    public function destroy($id)
    {
        Complaint::findOrFail($id)->delete();
        return response()->json(null, 204);
    }

    // Update cleaner assignment (Web)
    public function updateAssignment(Request $request, $complaintId)
    {
        $complaint = Complaint::findOrFail($complaintId);

        if ($complaint->comp_status !== 'pending') {
            return redirect()->back()->with('error', 'Cannot assign cleaners as the complaint is not pending.');
        }

        $request->validate([
            'no_of_cleaners' => 'required|integer|min:1|max:3',
            'cleaners' => 'required|array|min:1|max:' . $request->no_of_cleaners,
            'cleaners.*' => 'exists:cleaners,id',
        ]);

        $complaint->cleaners()->syncWithPivotValues($request->cleaners, [
            'assigned_by' => Auth::id(),
            'assigned_date' => now(),
            'no_of_cleaners' => $request->no_of_cleaners,
        ]);

        $complaint->update(['comp_status' => 'ongoing']);
        return redirect()->route('supervisor.complaints.show', $complaintId)
                         ->with('success', 'Cleaners assigned successfully.');
    }
    
        // **API Functions (From Current Version)**
    
        // Store a new complaint (API)
        public function apistore(Request $request)
        {
            $request->validate([
                'comp_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'comp_date' => 'required|date',
                'comp_time' => 'required',
                'comp_desc' => 'required|string',
                'comp_location' => 'required|string',
            ]);
    
            $complaint = Complaint::create([
                'comp_date' => $request->comp_date,
                'comp_time' => $request->comp_time,
                'comp_desc' => $request->comp_desc,
                'comp_location' => $request->comp_location,
                'officer_id' => Auth::id(),
                'assigned_by' => null,
                'cleaner_id' => null,
                'comp_status' => Complaint::STATUS_PENDING,
                'comp_image' => null,
            ]);
    
            // Check if an image is uploaded and attach it using the media library
            if ($request->hasFile('comp_image')) {
                $media = $complaint->addMediaFromRequest('comp_image')
                            ->toMediaCollection('complaint_images', 'public');
    
                Log::info('Media uploaded:', ['media' => $media]);
    
                $complaint->update(['comp_image' => $media->getUrl()]);
            } else {
                Log::info('No image was uploaded');
            }
    
            return response()->json([
                'message' => 'Complaint submitted successfully!',
                'complaint' => $complaint,
            ], 201);
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
        // Retrieve the complaint by its ID
        $complaint = Complaint::where('id', $id)->first();

        // Check if the complaint exists
        if (!$complaint) {
            return response()->json(['error' => 'Complaint not found'], 404);
        }

        // Get the media URL for the complaint image if it exists
        $compImageUrl = $complaint->getFirstMediaUrl('complaint_images') ? url($complaint->getFirstMediaUrl('complaint_images')) : null;


        // Format the complaint details for response
        $complaintDetails = [
            'id' => $complaint->id,
            'comp_date' => $complaint->comp_date,
            'comp_time' => $complaint->comp_time,
            'comp_desc' => $complaint->comp_desc,
            'comp_location' => $complaint->comp_location,
            'comp_image' => $compImageUrl,  // Use the media URL
            'officer_id' => $complaint->officer_id,
            'assigned_by' => $complaint->assigned_by,
            'assigned_date' => $complaint->assigned_date,
            'no_of_cleaners' => $complaint->no_of_cleaners,
            'cleaner_id' => $complaint->cleaner_id,
            'created_at' => $complaint->created_at,
            'updated_at' => $complaint->updated_at,
            'comp_status' => $complaint->comp_status,
        ];

        return response()->json($complaintDetails, 200);
    }

   
    public function getPendingComplaints()
    {
        // Fetch complaints with 'Pending' status
        $pendingComplaints = Complaint::where('comp_status', 'Pending')->get();

        // Return as JSON response
        return response()->json($pendingComplaints);
    }

    public function assignCleanerToComplaint(Request $request)
    {
        // Validate incoming data
        $validated = $request->validate([
            'complaint_id' => 'required|exists:complaints,id',
            'cleaner_id' => 'required|exists:cleaners,id',
        ]);

        // Assign cleaner to complaint with supervisor ID
        DB::table('complaint_cleaner')->insert([
            'complaint_id' => $validated['complaint_id'],
            'cleaner_id' => $validated['cleaner_id'],
            'assigned_at' => now(),
            'assigned_by' => Auth::id(), // Add supervisor ID
        ]);

        // Update complaint status to 'Ongoing'
        Complaint::where('id', $validated['complaint_id'])->update(['comp_status' => 'Ongoing']);

        return response()->json(['message' => 'Cleaner assigned successfully']);
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

    
  
    // assign complaint to cleaner
    public function AssignTask(Request $request, $id)
    {
        // Retrieve the complaint details
        $complaint = Complaint::select('id', 'comp_location', 'comp_date', 'comp_desc', 'officer_id', 'comp_status', 'comp_image')
            ->where('id', $id)
            ->whereNull('assigned_by') // Only unassigned complaints
            ->first();
    
        if (!$complaint) {
            return response()->json(['message' => 'Complaint not found'], 404);
        }
    
        // Validate request payload
        $validated = $request->validate([
            'cleaner_ids' => 'required|array|min:1',
            'no_of_cleaners' => 'required|integer|min:1',
            'assigned_by' => 'required|integer|exists:users,id',
        ]);
    
        $assignedDate = now();
    
        // Insert data into complaint_cleaner table
        foreach ($validated['cleaner_ids'] as $cleanerId) {
            ComplaintCleaner::create([
                'complaint_id' => $complaint->id,
                'cleaner_id' => $cleanerId,
                'no_of_cleaners' => $validated['no_of_cleaners'],
                'assigned_by' => $validated['assigned_by'],
                'assigned_date' => $assignedDate,
            ]);
    
            // Update cleaner status to "unavailable"
            Cleaner::where('id', $cleanerId)->update(['status' => 'unavailable']);
        }
    
        // Update complaint table
        $complaint->update([
            'comp_status' => Complaint::STATUS_ON_GOING,
            'assigned_by' => $validated['assigned_by'],
            'assigned_date' => $assignedDate,
            'no_of_cleaners' => $validated['no_of_cleaners'],
        ]);
    
        return response()->json([
            'message' => 'Task assigned successfully',
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
    
        // Retrieve available cleaners' IDs and names
        $availableCleaners = Cleaner::select('id', 'cleaner_name')
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
                    'cleaner_id' => (string) $cleaner->id,
                    'cleaner_name' => $cleaner->cleaner_name,
                ];
            }),
        ];
    
        return response()->json($complaintData);
    }
    
    
    // fetch assigned complaint history
    public function getHistory(Request $request)
    {
        // Get supervisor's ID from request (assuming it's passed with the token)
        $supervisorId = $request->user()->id; // Adjust this if needed based on your auth setup

        // Fetch tasks assigned by this supervisor
        $tasks = Complaint::where('assigned_by', $supervisorId)
            ->select('id', 'comp_desc', 'no_of_cleaners', 'comp_status', 'comp_date')
            ->orderBy('comp_date', 'desc') // Order by date, latest first
            ->get();

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
    
        // Retrieve assigned cleaners for the task
        $assignedCleaners = ComplaintCleaner::where('complaint_id', $complaint->id)
            ->join('cleaners', 'complaint_cleaner.cleaner_id', '=', 'cleaners.id')
            ->select('cleaners.id as cleaner_id', 'cleaners.cleaner_name')
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
