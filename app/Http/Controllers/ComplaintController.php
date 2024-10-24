<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Cleaner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ComplaintController extends Controller
{
    public function index()
    {
        // Fetch all complaints
        $complaints = Complaint::all();
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

    public function show($id)
    {
        // Fetch the complaint by its ID along with related tasks, officer, and cleaners
        $complaint = Complaint::with(['tasks', 'officer', 'cleaners'])
                              ->where('id', $id)
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
    
    
    
    public function getOfficerComplaints()
    {
        // Get the logged-in officer's ID
        $officerId = Auth::id();
        
        // Check if the officer is authenticated
        if (!$officerId) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    
        try {
            // Fetch complaints for the officer
            $complaints = Complaint::where('officer_id', $officerId)
                ->orderBy('comp_date', 'desc')
                ->orderBy('comp_time', 'desc')
                ->get();
    
            // Return the complaints in JSON format
            return response()->json($complaints, 200);
            
        } catch (\Exception $e) {
            // Catch any unexpected errors and return a 500 response
            return response()->json(['error' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    public function recentComplaint()
    {
        $officerId = auth()->user()->id; // Get the logged-in officer's ID
    
        // Fetch the most recent complaint for the logged-in officer
        $recentComplaint = Complaint::where('officer_id', $officerId)
                                    ->orderBy('created_at', 'desc')
                                    ->first();
    
        if ($recentComplaint) {
            return response()->json($recentComplaint, 200);
        }
    
        // If no complaint is found, return a default message
        return response()->json(['message' => 'No recent complaints found'], 404);
    }
    
    

}
