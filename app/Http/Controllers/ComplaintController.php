<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Cleaner;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
<<<<<<< HEAD
use Illuminate\Support\Facades\Log;
=======
use Illuminate\Support\Facades\Notification;  // Correct import for Notification
use App\Notifications\NewCleaningComplaint;
use Illuminate\Support\Facades\DB;
>>>>>>> origin/of

class ComplaintController extends Controller
{
    // Admin: Fetch complaints with optional filtering and sorting
    public function index(Request $request)
    {
<<<<<<< HEAD
        // Fetch all complaints
        $complaints = Complaint::all();
=======
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


    // Batch update complaint statuses
    public function batchUpdate(Request $request)
    {
        $request->validate([
            'complaints' => 'required|array',
            'new_status' => 'required|string',
        ]);

        Complaint::whereIn('id', $request->complaints)->update([
            'comp_status' => $request->new_status,
        ]);

        return redirect()->route('admin.complaints.index')->with('success', 'Status updated successfully!');
    }

    // Search complaints by description
    public function search(Request $request)
    {
        $query = $request->input('query');
        $complaints = Complaint::where('comp_desc', 'LIKE', "%{$query}%")->paginate(10);

        return view('admin.complaints.index', compact('complaints'));
    }

    // Supervisor: List complaints
    public function supervisorIndex()
    {
        $complaints = Complaint::orderBy('comp_date', 'desc')->paginate(10);
>>>>>>> origin/of
        return view('supervisor.complaints.index', compact('complaints'));
    }

    // Admin: Show recent complaint on the dashboard
    public function showDashboard()
    {
        $recentComplaint = Complaint::latest('comp_date')->latest('comp_time')->first();
        return view('dashboard', compact('recentComplaint'));
    }

<<<<<<< HEAD
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
    
    

=======
    // Store a new complaint

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

        // Check if there's an uploaded image
        if ($request->hasFile('comp_image')) {
            $image = file_get_contents($request->file('comp_image')->getRealPath());
            $complaint->comp_image = $image; // Store the binary image data
        }

        // Save the complaint record
        $complaint->save();

        $complaintData = [
            'id' => $complaint->id,
            'comp_date' => $complaint->comp_date,
            'comp_desc' => $complaint->comp_desc,
            'comp_location' => $complaint->comp_location,
            'officer_id' => $complaint->officer_id,
            'cleaner_id' => $complaint->cleaner_id,
        ];

        // Get all admin users to notify
        $adminUsers = User::where('is_admin', true)->get();

        // Send the notification
        Notification::send($adminUsers, new NewCleaningComplaint($complaintData));

        return redirect()->route('supervisor.complaints.index')
            ->with('success', 'Complaint created and notification sent successfully.');
    }

    // Supervisor: Show complaint details with available cleaners
    public function show($id)
    {
        $complaint = Complaint::with('cleaners')->findOrFail($id);
        $availableCleaners = Cleaner::where('status', 'available')->get();

        return view('supervisor.complaints.show', compact('complaint', 'availableCleaners'));
    }

    // Fetch image for display
    public function showImage($id)
    {
        $complaint = Complaint::findOrFail($id);

        if ($complaint->comp_image) {
            $mimeType = finfo_buffer(finfo_open(), $complaint->comp_image, FILEINFO_MIME_TYPE);
            return response($complaint->comp_image)->header('Content-Type', $mimeType);
        } else {
            return response()->json(['message' => 'No image available'], 404);
        }
    }

    public function assignCleaner(Request $request, $id)
    {
        $request->validate([
            'no_of_cleaners' => 'required|integer|min:1|max:3',
            'cleaners' => 'required|array|size:' . $request->no_of_cleaners,
            'cleaners.*' => 'exists:users,id', // Assuming 'users' table stores cleaners
        ]);

        $complaint = Complaint::findOrFail($id);

        // Check if cleaners are already assigned
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
                'no_of_cleaners' => $request->no_of_cleaners, // Update complaint table as well
                'assigned_by' => Auth::id(),
                'assigned_date' => now(),
            ]);
        });

        return redirect()->route('supervisor.complaints.show', $id)
                        ->with('success', 'Cleaners assigned successfully.');
    }

    // Update complaint details
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

    // Delete a complaint by ID
    public function destroy($id)
    {
        Complaint::findOrFail($id)->delete();
        return response()->json(null, 204);
    }

    // Update cleaner assignment
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
>>>>>>> origin/of
}
