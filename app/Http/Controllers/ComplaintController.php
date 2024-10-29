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
use App\Notifications\NewCleaningComplaint;

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

        if ($complaint->cleaners()->exists()) {
            return redirect()->route('supervisor.complaints.show', $id)
                             ->withErrors('Cleaners have already been assigned for this complaint.');
        }

        DB::transaction(function () use ($request, $complaint) {
            $assignments = array_fill_keys($request->cleaners, [
                'assigned_by' => Auth::id(),
                'assigned_date' => now(),
                'no_of_cleaners' => $request->no_of_cleaners,
            ]);

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
            'comp_status' => 'required|string|in:Pending,Ongoing,Completed',
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

    // **API Functions**

    // Store a new complaint (API)
    public function apistore(Request $request)
    {
        $validatedData = $request->validate([
            'comp_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'comp_date' => 'required|date',
            'comp_time' => 'required',
            'comp_desc' => 'required|string',
            'comp_location' => 'required|string',
        ]);

        $complaint = Complaint::create(array_merge($validatedData, [
            'officer_id' => Auth::id(),
            'comp_status' => Complaint::STATUS_PENDING,
        ]));

        if ($request->hasFile('comp_image')) {
            $media = $complaint->addMediaFromRequest('comp_image')->toMediaCollection('complaint_images', 'public');
            $complaint->update(['comp_image' => $media->getUrl()]);
            Log::info('Media uploaded:', ['media' => $media]);
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

        $complaints = Complaint::where('officer_id', $officerId)
            ->orderBy('comp_date', 'desc')
            ->orderBy('comp_time', 'desc')
            ->get();

        return response()->json($complaints, 200);
    }
}
