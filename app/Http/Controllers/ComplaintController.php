<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Cleaner;
use App\Models\User;
use App\Models\ComplaintCleaner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewCleaningComplaint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ComplaintController extends Controller
{
        // Admin: Fetch complaints with optional filtering and sorting (Web)
        public function index(Request $request)
        {
            // Start building the query
            $query = Complaint::query();
    
            // Apply status filter if provided
            if ($request->filled('status')) {
                $query->where('comp_status', $request->status);
            }
    
            // Eager load related models to avoid N+1 query issues
            $complaints = $query->with(['officer', 'supervisor'])->paginate(10);
    
            return view('admin.complaints.index', compact('complaints'));
        }
    
        // Store a new complaint (Web)
        public function store(Request $request)
        {
            $request->validate([
                'comp_desc' => 'required|string|max:255',
                'comp_location' => 'required|string|max:255',
                'comp_date' => 'required|date',
                'comp_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'officer_id' => 'required|exists:users,id',
                'cleaner_id' => 'nullable|exists:users,id',
            ]);
    
            $complaint = new Complaint();
            $complaint->comp_desc = $request->input('comp_desc');
            $complaint->comp_location = $request->input('comp_location');
            $complaint->comp_date = $request->input('comp_date');
            $complaint->comp_status = 'pending';
            $complaint->officer_id = $request->input('officer_id');
            $complaint->cleaner_id = $request->input('cleaner_id');
            $complaint->save();
    
            // Handle the image upload using Spatie MediaLibrary
            if ($request->hasFile('comp_image')) {
                $complaint->addMedia($request->file('comp_image'))
                          ->toMediaCollection('complaint_images');
            }
    
            // Send notifications
            $adminUsers = User::where('is_admin', true)->get();
            Notification::send($adminUsers, new NewCleaningComplaint($complaint));
    
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
                'cleaners.*' => 'exists:users,id', // Assuming 'users' table stores cleaners
            ]);
    
            $complaint = Complaint::findOrFail($id);
    
            if ($complaint->cleaners()->exists()) {
                return redirect()->route('supervisor.complaints.show', $id)
                                 ->withErrors('Cleaners have already been assigned for this complaint.');
            }
    
            DB::transaction(function () use ($request, $complaint) {
                $assignments = [];
                foreach ($request->cleaners as $cleanerId) {
                    $assignments[$cleanerId] = [
                        'assigned_by' => Auth::id(),
                        'assigned_date' => now(),
                        'no_of_cleaners' => $request->no_of_cleaners,
                    ];
                }
    
                // Attach cleaners with pivot data
                $complaint->cleaners()->attach($assignments);
    
                // Update the complaint status to 'ongoing'
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
            $request->validate([
                'comp_date' => 'required|date',
                'comp_time' => 'required|date_format:H:i',
                'comp_desc' => 'required|string|max:255',
                'comp_location' => 'required|string|max:255',
                'comp_status' => 'required|string|in:Pending,Ongoing,Completed',
            ]);
    
            $complaint = Complaint::findOrFail($id);
            $complaint->update($request->only('comp_date', 'comp_time', 'comp_desc', 'comp_location', 'comp_status'));
    
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

    public function getComplaints()
    {
        // Retrieve all complaints with the specified fields where assigned_by is null
        $complaints = Complaint::select('id', 'comp_date', 'comp_location','comp_time', 'comp_desc', 'officer_id', 'assigned_by')
            ->whereNull('assigned_by')
            ->get();

        // Return the results as JSON
        
        return response()->json($complaints);
    }

  
    public function AssignTask(Request $request, $id)
    {
        // Retrieve the complaint details from the `complaints` table
        $complaint = Complaint::select('id', 'comp_location', 'comp_date', 'comp_desc', 'officer_id', 'comp_status')
            ->where('id', $id) // Filter by the specific complaint ID
            ->whereNull('assigned_by') // Include only unassigned complaints
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
        $cleaners = $availableCleaners->map(function ($cleaner) {
            return [
                'cleaner_id' => (string) $cleaner->id,
                'cleaner_name' => $cleaner->cleaner_name
            ];
        })->toArray();
    
        // Prepare data to send to frontend
        $complaintData = [
            'id' => (string) $complaint->id,
            'comp_location' => $complaint->comp_location ?? 'No Location',
            'comp_date' => (string) $complaint->comp_date,
            'comp_desc' => (string) $complaint->comp_desc,
            'officer_name' => (string) $officerName,
            'available_cleaners' => $cleaners,
            'comp_status' => $complaint->comp_status,
        ];
    
        // Check if there's data to be inserted (if provided in the request)
        if ($request->has('cleaner_ids') && $request->has('no_of_cleaners') && $request->has('assigned_by')) {
            $assignedDate = now();
            
            // Insert data into the complaint_cleaner table for each cleaner
            foreach ($request->input('cleaner_ids') as $cleanerId) {
                ComplaintCleaner::create([
                    'complaint_id' => $complaint->id,
                    'cleaner_id' => $cleanerId,
                    'no_of_cleaners' => $request->input('no_of_cleaners'),
                    'assigned_by' => $request->input('assigned_by'),
                    'assigned_date' => $assignedDate,
                ]);
    
                // Update cleaner status to "unavailable"
                Cleaner::where('id', $cleanerId)->update(['status' => 'unavailable']);
            }
    
            // Update the complaints table with assignment details and change status to "ongoing"
            $complaint->update([
                'comp_status' => 'ongoing',
                'assigned_by' => $request->input('assigned_by'),
                'assigned_date' => $assignedDate,
                'no_of_cleaners' => $request->input('no_of_cleaners'),
            ]);
        }
    
        // Retrieve assigned cleaners for the response
        $assignedCleaners = ComplaintCleaner::where('complaint_id', $complaint->id)
            ->join('cleaners', 'complaint_cleaner.cleaner_id', '=', 'cleaners.id')
            ->select('cleaners.id as cleaner_id', 'cleaners.cleaner_name', 'cleaners.status')
            ->groupBy('cleaners.id') // Ensures unique cleaner entries
            ->get()
            ->map(function ($cleaner) {
                return [
                    'cleaner_id' => (string) $cleaner->cleaner_id,
                    'cleaner_name' => $cleaner->cleaner_name,
                    'status' => $cleaner->status,
                ];
            });
    
        // Return both the retrieved data and success message
        return response()->json([
            'complaint_data' => $complaintData,
            'assigned_cleaners' => $assignedCleaners,
            'message' => 'Data retrieved and updated successfully'
        ]);
    }


    public function apigetComplaintDetails($id)
{
    // Retrieve specific fields from the complaints table for the given complaint ID
    $complaint = Complaint::select('id', 'comp_location', 'comp_date', 'comp_desc', 'officer_id', 'comp_status')
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
        'available_cleaners' => $availableCleaners->map(function ($cleaner) {
            return [
                'cleaner_id' => (string) $cleaner->id,
                'cleaner_name' => $cleaner->cleaner_name
            ];
        }),
        'comp_status' => $complaint->comp_status,
    ];

    // Return the data as JSON
    return response()->json($complaintData);
}

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


public function getHistoryDetails($id)
{
    // Retrieve the complaint details including location, description, etc.
    $complaint = Complaint::select('id', 'comp_desc', 'comp_time', 'comp_date', 'comp_location', 'officer_id', 'comp_status', 'no_of_cleaners', 'assigned_date')
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

    // Prepare the response data, including the complaint_id
    $responseData = [
        'complaint_id' => $complaint->id,
        'comp_desc' => $complaint->comp_desc,
        'comp_time' => $complaint->comp_time,
        'comp_date' => $complaint->comp_date,
        'comp_location' => $complaint->comp_location,
        'officer_name' => $officerName,
        'assigned_cleaners' => $assignedCleaners,
        'comp_status' => $complaint->comp_status,
        'no_of_cleaners' => $complaint->no_of_cleaners,
        'assigned_date' => $complaint->assigned_date,
    ];

    return response()->json($responseData);
} 
}    
