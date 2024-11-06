<?php

namespace App\Http\Controllers;

use App\Models\Supervisor;
use App\Models\Complaint;
use App\Models\Cleaner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SupervisorController extends Controller
{
    /**
     * Display the supervisor dashboard.
     */
    public function dashboard()
    {
        // Retrieve cleaner stats
        $totalCleaners = Cleaner::count();
        $availableCleaners = Cleaner::where('status', 'available')->count();
        $unavailableCleaners = Cleaner::where('status', 'unavailable')->count();

        // Fetch the total number of supervisors
        $totalSupervisors = Supervisor::count();

        // Fetch the 5 most recent complaints
        $recentComplaints = Complaint::orderBy('comp_date', 'desc')->limit(5)->get();

        return view('supervisor.dashboard', compact('totalCleaners', 'availableCleaners', 'unavailableCleaners', 'totalSupervisors', 'recentComplaints'));
    }

    /**
     * Show the history of complaints.
     */
    public function history()
    {
        // Fetch complaints with status 'in progress' or 'completed'
        $complaints = Complaint::with('cleaners')
            ->whereIn('comp_status', ['in progress', 'completed'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('supervisor.history', compact('complaints'));
    }

    /**
     * Show all cleaners, with optional search filtering.
     */
    public function cleaners(Request $request)
    {
        // Fetch cleaners with optional search and pagination
        $search = $request->input('search');
        $cleaners = Cleaner::when($search, function ($query, $search) {
                return $query->where('cleaner_name', 'LIKE', "%{$search}%");
            })
            ->paginate(10);

        // Cleaner statistics
        $totalCleaners = Cleaner::count();
        $availableCount = Cleaner::where('status', 'available')->count();
        $unavailableCount = Cleaner::where('status', 'unavailable')->count();

        return view('supervisor.cleaners.index', compact('totalCleaners', 'availableCount', 'unavailableCount', 'cleaners'));
    }

    /**
     * List all supervisors (API).
     */
    public function index()
    {
        $supervisors = Supervisor::all();
        return response()->json(['success' => true, 'data' => $supervisors], 200);
    }

    /**
     * Store a newly created supervisor.
     */
    public function store(Request $request)
    {
        $request->validate([
            's_email' => 'required|email|unique:supervisors',
            's_pass' => 'required|min:8',
            's_name' => 'required|string|max:255',
            's_phoneNo' => 'required|string|max:15',
        ]);

        $supervisor = Supervisor::create([
            's_email' => $request->s_email,
            's_pass' => bcrypt($request->s_pass),
            's_name' => $request->s_name,
            's_phoneNo' => $request->s_phoneNo,
        ]);

        return response()->json(['success' => true, 'data' => $supervisor], 201);
    }

    /**
     * Display the specified supervisor.
     */
    public function show($id)
    {
        $supervisor = Supervisor::find($id);

        if (!$supervisor) {
            return response()->json(['success' => false, 'message' => 'Supervisor not found'], 404);
        }

        // Fetch available cleaners
        $cleaners = Cleaner::where('status', 'available')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'supervisor' => $supervisor,
                'cleaners' => $cleaners
            ]
        ], 200);
    }

    /**
     * Show the form for editing the specified supervisor.
     */
    public function edit(Supervisor $supervisor)
    {
        return view('admin.supervisors.edit', compact('supervisor'));
    }

    /**
     * Update the specified supervisor.
     */
    public function update(Request $request, $id)
    {
        $supervisor = Supervisor::findOrFail($id);

        $request->validate([
            's_name' => 'required|string|max:255',
            's_email' => 'required|email|max:255|unique:supervisors,s_email,' . $id,
            's_phoneNo' => 'required|string|max:15',
            'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            's_pass' => 'nullable|string|min:8|confirmed',
        ]);

        // Update supervisor data
        $supervisor->update([
            's_name' => $request->s_name,
            's_email' => $request->s_email,
            's_phoneNo' => $request->s_phoneNo,
        ]);

        // Handle profile picture upload
        if ($request->hasFile('profile_pic')) {
            // Delete the old picture if it exists
            if ($supervisor->profile_pic && Storage::exists('public/' . $supervisor->profile_pic)) {
                Storage::delete('public/' . $supervisor->profile_pic);
            }

            $path = $request->file('profile_pic')->store('profile_pics', 'public');
            $supervisor->profile_pic = $path;
        }

        // Update password if provided
        if ($request->filled('s_pass')) {
            $supervisor->s_pass = bcrypt($request->s_pass);
        }

        $supervisor->save();

        return redirect()->route('admin.supervisors.index')->with('success', 'Supervisor updated successfully.');
    }

    /**
     * Remove the specified supervisor.
     */
    public function destroy($id)
    {
        $supervisor = Supervisor::findOrFail($id);

        if ($supervisor->profile_pic && Storage::exists('public/' . $supervisor->profile_pic)) {
            Storage::delete('public/' . $supervisor->profile_pic);
        }

        $supervisor->delete();

        return response()->json(['success' => true, 'message' => 'Supervisor deleted successfully'], 200);
    }

    public function getAllCleaners()
    {
        $cleaners = Cleaner::all();
    
        foreach ($cleaners as $cleaner) {
            // Check and handle profile_pic as a blob
            if ($cleaner->profile_pic !== null) {
                // Convert the blob data to base64 encoding
                $cleaner->profile_pic = base64_encode($cleaner->profile_pic);
            }
    
            // Handle any other malformed UTF-8 fields
            foreach ($cleaner->getAttributes() as $key => $value) {
                if (!mb_check_encoding($value, 'UTF-8')) {
                    return response()->json(['success' => false, 'message' => "Malformed UTF-8 detected in Cleaner ID: {$cleaner->id}, Field: $key"], 500);
                }
            }
        }
    
        return response()->json(['success' => true, 'data' => $cleaners], 200);
    }
    
       
    // Show method to retrieve a specific cleaner by ID
    public function showapi($id)
    {
        // Find the cleaner by ID
        $cleaner = Cleaner::find($id);

        // Check if the cleaner exists
        if (!$cleaner) {
            return response()->json(['message' => 'Cleaner not found'], 404);
        }

        // Convert the BLOB data to Base64 if it exists
        if ($cleaner->profile_pic) {
            $cleaner->profile_pic = base64_encode($cleaner->profile_pic);
        }

        // Return the cleaner's details including profile_pic
        return response()->json([
            'data' => $cleaner,
        ]);
    }

}
