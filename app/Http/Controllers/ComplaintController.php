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

    public function store(Request $request)
    {
        // Validate incoming request
        $validated = $request->validate([
            'comp_desc' => 'required|string|max:255',
            'comp_location' => 'required|string|max:255',
            'comp_date' => 'required|date',
            'comp_time' => 'required|date_format:H:i',
            'comp_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate image
        ]);

        // Handle file upload
        if ($request->hasFile('comp_image')) {
            $filePath = $request->file('comp_image')->store('complaints', 'public'); // Store image in storage/app/public/complaints
            $validated['comp_image'] = $filePath; // Save the file path to the database
        }

        // Create a new complaint record
        $validated['comp_status'] = 'Pending'; // Set default status to Pending
        Complaint::create($validated);
        return redirect()->route('complaints')->with('success', 'Complaint submitted successfully!');
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

    public function update(Request $request, $id)
    {
        // Validation logic for updating
        $validator = Validator::make($request->all(), [
            'comp_date' => 'required|date',
            'comp_time' => 'required|date_format:H:i',
            'comp_desc' => 'required|string|max:255',
            'comp_location' => 'required|string|max:255',
            'comp_status' => 'required|string|in:Pending,Notified,Ongoing,Completed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Update the complaint
        $complaint = Complaint::findOrFail($id);
        $complaint->update($request->all());
        return response()->json($complaint, 200);
    }

    public function destroy($id)
    {
        Complaint::destroy($id);
        return response()->json(null, 204);
    }
    public function assignCleaner(Request $request, $id)
    {
        Log::info('Assign Cleaner called with ID: ' . $id);
        dd('Assign cleaner function reached');
    
        // Validate the incoming request
        $request->validate([
            'number_of_cleaners' => 'required|integer|min:1|max:3', // Validate number of cleaners
            'cleaner' => 'required|array|max:3', // Validate that cleaners is an array
            'cleaner.*' => 'exists:cleaners,id', // Ensure that each selected cleaner exists
        ]);
    
        $complaint = Complaint::findOrFail($id);
    
        // Sync cleaners to the complaint and update pivot table details
        $complaint->cleaners()->sync($request->cleaner);
    
        foreach ($request->cleaner as $cleaner_id) {
            $complaint->cleaners()->updateExistingPivot($cleaner_id, [
                'no_of_cleaners' => $request->number_of_cleaners,
                $assignedBy = auth()->guard('supervisor')->user()->name,// Handle the logged-in user or fallback to 'System'
                'assigned_date' => now(),
            ]);
        }
    
        // Update complaint status to Notified
        $complaint->comp_status = 'Notified';
        $complaint->save();
    
        return redirect()->route('supervisor.complaints.index')->with('success', 'Cleaners assigned successfully!');

    }
}    
