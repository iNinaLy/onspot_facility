<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Cleaner;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ComplaintNotification;

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

    // Submit Complaint (Web)
    public function submitComplaint(Request $request)
    {
        // Validate the request data
        $request->validate([
            'comp_desc' => 'required|string',
            'comp_location' => 'required|string',
            'comp_date' => 'required|date',
            'comp_time' => 'required',
            'comp_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Create the complaint
        $complaint = Complaint::create([
            'comp_desc'      => $request->input('comp_desc'),
            'comp_location'  => $request->input('comp_location'),
            'comp_date'      => $request->input('comp_date'),
            'comp_time'      => $request->input('comp_time'),
            'officer_id'     => Auth::id(),
            'comp_status'    => 'pending',
        ]);

        // Handle image upload
        if ($request->hasFile('comp_image')) {
            $complaint->addMedia($request->file('comp_image'))
                ->toMediaCollection('complaint_images');
        }

        // Get the officer who submitted the complaint
        $officer = Auth::user();

        // Notify supervisors about the new complaint
        $supervisors = User::role('supervisor')->get(); // Ensure you're using Spatie's package
        foreach ($supervisors as $supervisor) {
            $supervisor->notify(new ComplaintNotification($complaint, $officer));
        }

        return response()->json(['message' => 'Complaint submitted successfully.']);
    }

    // Store a new complaint (Web)
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'comp_desc'     => 'required|string|max:255',
            'comp_location' => 'required|string|max:255',
            'comp_date'     => 'required|date',
            'comp_time'     => 'required',
            'comp_image'    => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'officer_id'    => 'required|exists:users,id',
        ]);

        $complaint = Complaint::create(array_merge($validatedData, [
            'comp_status' => 'pending',
        ]));

        if ($request->hasFile('comp_image')) {
            $complaint->addMedia($request->file('comp_image'))
                ->toMediaCollection('complaint_images');
        }

        $supervisors = User::role('supervisor')->get(); // Use Spatie's role method
        Notification::send($supervisors, new ComplaintNotification($complaint, Auth::user()));

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

    public function assignCleaner(Request $request, $id)
    {
        $validated = $request->validate([
            'no_of_cleaners' => 'required|integer|min:1|max:3',
            'cleaners'       => 'required|array|size:' . $request->no_of_cleaners,
            'cleaners.*'     => 'exists:cleaners,id',
        ]);

        $complaint = Complaint::findOrFail($id);

        if ($complaint->cleaners()->exists()) {
            return redirect()->route('supervisor.complaints.show', $id)
                ->withErrors('Cleaners have already been assigned for this complaint.');
        }

        try {
            DB::transaction(function () use ($validated, $complaint) {
                $assignments = [];

                foreach ($validated['cleaners'] as $cleanerId) {
                    $assignments[$cleanerId] = [
                        'assigned_by'   => Auth::id(),
                        'assigned_date' => now(),
                        'no_of_cleaners' => $validated['no_of_cleaners'],
                    ];

                    // Update cleaner status to unavailable
                    Cleaner::where('id', $cleanerId)->update(['status' => 'unavailable']);
                }

                // Attach cleaners to the complaint
                $complaint->cleaners()->attach($assignments);

                // Update complaint details
                $complaint->update([
                    'comp_status'     => 'ongoing',
                    'no_of_cleaners'  => $validated['no_of_cleaners'],
                    'assigned_by'     => Auth::id(),
                    'assigned_date'   => now(),
                ]);
            });

            // Notify cleaners and officer
            foreach ($validated['cleaners'] as $cleanerId) {
                $cleaner = Cleaner::find($cleanerId);
                $cleaner->notify(new ComplaintNotification($complaint, Auth::user(), true, true));
            }

            if ($complaint->officer) {
                $complaint->officer->notify(new ComplaintNotification($complaint, Auth::user(), null, true));
            }

            return redirect()->route('supervisor.complaints.show', $id)
                ->with('success', 'Cleaners assigned successfully.');
        } catch (\Exception $e) {
            Log::error('Error assigning cleaners to complaint ID: ' . $id, [
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('supervisor.complaints.show', $id)
                ->withErrors('An error occurred while assigning cleaners. Please try again.');
        }
    }


    // Supervisor: List complaints (Web)
    public function supervisorIndex(Request $request)
    {
        $query = Complaint::query();

        // Apply status filter if provided; default to 'pending' if not
        if ($request->filled('status')) {
            $query->where('comp_status', $request->status);
        } else {
            $query->where('comp_status', 'pending');
        }

        $complaints = $query->with(['officer', 'supervisor'])->paginate(10);
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
            'comp_date'     => 'required|date',
            'comp_time'     => 'required|date_format:H:i',
            'comp_desc'     => 'required|string|max:255',
            'comp_location' => 'required|string|max:255',
            'comp_status'   => 'required|string|in:pending,ongoing,completed',
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

    // Update complaint status (Web)
    public function updateStatus(Request $request, $id)
    {
        $complaint = Complaint::findOrFail($id);

        $validated = $request->validate([
            'comp_status' => 'required|in:pending,ongoing,completed',
        ]);

        try {
            DB::transaction(function () use ($validated, $complaint) {
                $complaint->update(['comp_status' => $validated['comp_status']]);

                // If the status is completed, mark all assigned cleaners as available
                if ($validated['comp_status'] === 'completed') {
                    // Get IDs of assigned cleaners
                    $cleanerIds = $complaint->cleaners()->pluck('users.id')->toArray();

                    // Update cleaners' status to 'available'
                    User::whereIn('id', $cleanerIds)->update(['status' => 'available']);
                }
            });

            return redirect()->route('supervisor.complaints.show', $id)
                ->with('success', 'Complaint status updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating complaint status for ID: ' . $id, [
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('supervisor.complaints.show', $id)
                ->withErrors('An error occurred while updating the complaint status. Please try again.');
        }
    }

    // API Functions

    // Store a new complaint (API)
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

        $supervisors = User::role('supervisor')->get(); // Use Spatie's role method
        foreach ($supervisors as $supervisor) {
            $supervisor->notify(new ComplaintNotification($complaint, Auth::user()));
        }

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

        $complaints = Complaint::where('officer_id', $officerId)
            ->orderBy('comp_date', 'desc')
            ->orderBy('comp_time', 'desc')
            ->get();

        return response()->json($complaints, 200);
    }

    // Get details of a specific complaint (API)
    public function getComplaintDetails($id)
    {
        $complaint = Complaint::find($id);

        if (!$complaint) {
            return response()->json(['error' => 'Complaint not found'], 404);
        }

        $compImageUrl = $complaint->getFirstMediaUrl('complaint_images') ? url($complaint->getFirstMediaUrl('complaint_images')) : null;

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

    // Get pending complaints (API)
    public function getPendingComplaints()
    {
        // Fetch complaints with 'pending' status
        $pendingComplaints = Complaint::where('comp_status', 'pending')->get();

        // Return as JSON response
        return response()->json($pendingComplaints);
    }

    // Assign cleaner to complaint (API)
    public function assignCleanerToComplaint(Request $request)
    {
        // Validate incoming data
        $validated = $request->validate([
            'complaint_id' => 'required|exists:complaints,id',
            'cleaner_id'   => 'required|exists:users,id',
        ]);

        // Assign cleaner to complaint with supervisor ID
        DB::transaction(function () use ($validated) {
            DB::table('complaint_cleaner')->insert([
                'complaint_id'  => $validated['complaint_id'],
                'cleaner_id'    => $validated['cleaner_id'],
                'assigned_date' => now(),
                'assigned_by'   => Auth::id(),
            ]);

            // Update complaint status to 'ongoing'
            Complaint::where('id', $validated['complaint_id'])->update(['comp_status' => 'ongoing']);

            // Update cleaner status to 'unavailable'
            User::where('id', $validated['cleaner_id'])->update(['status' => 'unavailable']);
        });

        return response()->json(['message' => 'Cleaner assigned successfully']);
    }

    // Notify relevant users about a complaint
    public function notifyRelevantUsers(Complaint $complaint)
    {
        // Notify supervisors
        $supervisors = User::role('supervisor')->where('is_active', true)->get();

        foreach ($supervisors as $supervisor) {
            $supervisor->notify(new ComplaintNotification($complaint, $complaint->officer));
        }

        // Notify assigned cleaners
        $cleaners = $complaint->cleaners;

        foreach ($cleaners as $cleaner) {
            $cleaner->notify(new ComplaintNotification($complaint, $complaint->officer, $cleaners, true));
        }

        // Notify officer about cleaner assignment
        $officer = $complaint->officer;

        $officer->notify(new ComplaintNotification($complaint, $officer, null, true));
    }
}
