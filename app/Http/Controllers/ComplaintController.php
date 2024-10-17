<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Cleaner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

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

    public function apistore(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'comp_date' => 'required|date',
            'comp_time' => 'required|date_format:H:i',
            'comp_desc' => 'required|string',
            'comp_location' => 'required|in:Floor 1,Floor 2,Floor 3,Floor 4', // Update based on your enum
            'comp_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust size if needed
        ]);

        // Handle file upload
        $imagePath = null;
        if ($request->hasFile('comp_image')) {
            $imagePath = $request->file('comp_image')->store('complaints', 'public');
        }

        // Create a new complaint
        $complaint = Complaint::create([
            'comp_date' => $request->comp_date,
            'comp_time' => $request->comp_time,
            'comp_desc' => $request->comp_desc,
            'comp_location' => $request->comp_location,
            'comp_image' => $imagePath,
            'officer_id' => Auth::id(), // Assuming you're using Auth to get the officer
            'comp_status' => Complaint::STATUS_PENDING, // Default status
        ]);

        // Return a response (you can customize this)
        return response()->json(['message' => 'Complaint submitted successfully!', 'complaint' => $complaint], 201);
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
    
}    
