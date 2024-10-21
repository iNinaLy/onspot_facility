<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Cleaner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ComplaintController extends Controller
{
    // Admin: Fetch complaints for admin
    public function index(Request $request) {
        $query = Complaint::query();
    
        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('comp_status', $request->status);
        }
    
        // Sort by status (optional sorting order can be added)
        $query->orderBy('comp_status', 'asc'); // 'asc' for ascending or 'desc' for descending
    
        // Paginate the results
        $complaints = $query->with(['officer'])->paginate(10);
    
        return view('admin.complaints.index', compact('complaints'));
    }
    
    public function batchUpdate(Request $request)
    {
        $validated = $request->validate([
            'complaints' => 'required|array',
            'new_status' => 'required|string',
        ]);

        foreach ($validated['complaints'] as $complaintId) {
            $complaint = Complaint::find($complaintId);
            $complaint->comp_status = $validated['new_status'];
            $complaint->save();
        }

        return redirect()->route('admin.complaints')->with('success', 'Status updated successfully!');
    }
    public function search(Request $request)
    {
        $query = $request->input('query');
        $complaints = Complaint::where('name', 'LIKE', "%{$query}%")
            ->orWhere('phone_number', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->paginate(10);
        return view('admin.complaints.index', compact('complaints'));
    }


    // Supervisor: Fetch complaints for supervisor
    public function supervisorIndex()
    {
        // Fetch complaints for the supervisor (you can add filters as needed)
        $complaints = Complaint::orderBy('comp_date', 'desc')->paginate(10);

        // Return the supervisor complaints view
        return view('supervisor.complaints.index', compact('complaints'));
    }

    // Admin: Show dashboard with the most recent complaint
    public function showDashboard()
    {
        // Fetch the most recent complaint
        $recentComplaint = Complaint::orderBy('comp_date', 'desc')
                                    ->orderBy('comp_time', 'desc')
                                    ->first();

        // Return the dashboard view with the recent complaint
        return view('dashboard', compact('recentComplaint'));
    }

    // Store a new complaint
    public function store(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'comp_desc' => 'required|string|max:255',
            'comp_location' => 'required|string|max:255',
            'comp_date' => 'required|date',
            'comp_time' => 'required|date_format:H:i',
            'comp_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Image validation
        ]);

        // Handle file upload if available
        if ($request->hasFile('comp_image')) {
            $filePath = $request->file('comp_image')->store('complaints', 'public'); // Store image in public storage
            $validated['comp_image'] = $filePath; // Save the file path to the database
        }

        // Create a new complaint record
        $validated['comp_status'] = 'Pending'; // Set default status to Pending
        Complaint::create($validated);

        // Redirect to the supervisor complaints page with a success message
        return redirect()->route('supervisor.complaints.index')->with('success', 'Complaint submitted successfully!');
    }

    // Supervisor: Show a specific complaint
    public function show($id)
    {
        // Fetch the complaint by ID with related data
        $complaint = Complaint::with('officer', 'cleaners')->findOrFail($id);

        // Fetch available cleaners only if the status is 'Pending'
        $cleaners = ($complaint->comp_status === 'Pending')
            ? Cleaner::where('status', 'available')->get()
            : collect(); // Return an empty collection if the status is not 'Pending'

        // Pass data to the view
        return view('supervisor.complaints.show', compact('complaint', 'cleaners'));
    }

    // Supervisor: Update an existing complaint
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

        // If validation fails, return with errors
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Update the complaint with validated data
        $complaint = Complaint::findOrFail($id);
        $complaint->update($request->all());

        // Return a JSON response with the updated complaint
        return response()->json($complaint, 200);
    }

    // Supervisor: Delete a complaint by ID
    public function destroy($id)
    {
        // Delete the complaint by its ID
        Complaint::destroy($id);

        // Return a JSON response indicating success
        return response()->json(null, 204);
    }

    // Supervisor: Assign cleaners to a complaint
    public function assignCleaner(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'no_of_cleaners' => 'required|integer|min:1|max:3',
            'cleaners' => 'required|array|max:' . $request->no_of_cleaners,
            'cleaners.*' => 'exists:cleaners,id', // Ensure selected cleaners exist
        ]);

        // Find the complaint by ID
        $complaint = Complaint::findOrFail($id);

        // Ensure the complaint is pending
        if ($complaint->comp_status !== 'Pending') {
            return redirect()->back()->with('error', 'Cleaners can only be assigned when the complaint is pending.');
        }

        // Update the complaint with assigned cleaner info
        $complaint->no_of_cleaners = $request->no_of_cleaners;
        $complaint->cleaner_id = $request->cleaners[0]; // Assuming first cleaner is in charge
        $complaint->assigned_by = Auth::user()->name; // Assuming supervisor's name
        $complaint->assigned_date = now(); // Store the date of assignment
        $complaint->comp_status = 'Ongoing'; // Update status to 'Ongoing'
        $complaint->save();

        // Sync assigned cleaners (many-to-many relationship)
        $complaint->cleaners()->sync($request->cleaners);

        // Redirect back with a success message
        return redirect()->route('supervisor.complaints.show', $complaint->id)->with('success', 'Cleaners assigned successfully!');
    }

 

}
