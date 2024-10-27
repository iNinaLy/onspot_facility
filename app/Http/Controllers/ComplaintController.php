<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Cleaner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\User;


class ComplaintController extends Controller
{
    public function index()
    {
        $complaints = Complaint::all(); // Fetch all complaints
        return view('supervisor.complaints.index', compact('complaints'));
    }

    public function showDashboard()
    {
        // Fetch the most recent complaint
        $recentComplaint = Complaint::orderBy('comp_date', 'desc')
                                    ->orderBy('comp_time', 'desc')
                                    ->first();

        return view('dashboard', compact('recentComplaint'));
    }

    public function show($comp_id)
    {
        // Fetch the complaint by its ID along with related tasks, officer, and cleaners
        $complaint = Complaint::with(['tasks', 'officer', 'cleaners']) // Ensure these relationships exist in your models
                              ->where('comp_id', $comp_id)
                              ->first();

        // If the complaint doesn't exist, redirect back with an error
        if (!$complaint) {
            return redirect()->route('supervisor.complaints.index')->with('error', 'Complaint not found.');
        }

        // Fetch only available cleaners for assigning (when the status is pending)
        $cleaners = Cleaner::where('status', 'available')->get();

        // Return the view with the complaint data and available cleaners
        return view('supervisor.complaints.show', compact('complaint', 'cleaners'));

    }

        public function apishow($comp_id)
    {
        // Fetch the complaint by its ID along with related tasks, officer, and cleaners
        $complaint = Complaint::with(['tasks', 'officer', 'cleaners']) // Ensure these relationships exist
                            ->where('comp_id', $comp_id)
                            ->first();

        // If the complaint doesn't exist, return a 404 error response
        if (!$complaint) {
            return response()->json(['error' => 'Complaint not found.'], 404);
        }

        // Fetch only available cleaners for assigning (optional)
        $cleaners = Cleaner::where('status', 'available')->get();

        // Return a JSON response with the complaint and cleaners data
        return response()->json(['complaint' => $complaint, 'cleaners' => $cleaners], 200);
    }
    public function getEnumValues($column, $table)
    {
        // Get the enum values from the database schema
        $type = \DB::select(\DB::raw("SHOW COLUMNS FROM $table WHERE Field = '$column'"))[0]->Type;
        preg_match('/^enum\((.*)\)$/', $type, $matches);
        $enum = array();
        foreach (explode(',', $matches[1]) as $value) {
            $enum[] = trim($value, "'");
        }

        return response()->json($enum, 200);
    }

    public function getLocationsApi() {
        // Retrieve the enum values from the complaint table's 'comp_location' field
        $locations = Complaint::getEnumValues('comp_location');
        return response()->json($locations);
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

    public function apistore(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'comp_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate a single image file
            'comp_date' => 'required|date',
            'comp_time' => 'required',
            'comp_desc' => 'required|string',
            'comp_location' => 'required|string',
        ]);
    
        // Create a new complaint record in the database
        $complaint = Complaint::create([
            'comp_date' => $request->comp_date,
            'comp_time' => $request->comp_time,
            'comp_desc' => $request->comp_desc,
            'comp_location' => $request->comp_location,
            'officer_id' => Auth::id(),
            'assigned_by' => null,
            'cleaner_id' => null,
            'comp_status' => Complaint::STATUS_PENDING,
            'comp_image' => null, // Initialize as null, we'll update this later if an image is uploaded
        ]);
    
        // Check if an image is uploaded and attach it using the media library
        if ($request->hasFile('comp_image')) {
            $media = $complaint->addMediaFromRequest('comp_image')
                        ->toMediaCollection('complaint_images', 'public'); // Explicitly use the 'public' disk
    
            // Log the media object for debugging
            Log::info('Media uploaded:', ['media' => $media]);
    
            // Store the image URL in the 'comp_image' field
            $complaint->update([
                'comp_image' => $media->getUrl() // Save the image URL in the 'comp_image' column
            ]);
        } else {
            Log::info('No image was uploaded');
        }
    
        // Return the response in JSON format with the complaint data
        return response()->json([
            'message' => 'Complaint submitted successfully!',
            'complaint' => $complaint, // The 'comp_image' field now contains the URL of the image
        ], 201);
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
        }

        // Update the complaints table with assignment details
        $complaint->update([
            'assigned_by' => $request->input('assigned_by'),
            'assigned_date' => $assignedDate,
            'no_of_cleaners' => $request->input('no_of_cleaners'),
            'cleaner_id' => implode(',', $request->input('cleaner_ids')), // Join multiple cleaner IDs as comma-separated
        ]);
    }

    // Return both the retrieved data and success message
    return response()->json([
        'complaint_data' => $complaintData,
        'message' => 'Data retrieved and updated successfully'
    ]);
}

    
}    
