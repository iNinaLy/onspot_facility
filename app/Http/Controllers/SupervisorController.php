<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\Supervisor;
use App\Models\Complaint;
use App\Models\Cleaner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


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
            $supervisorId = Auth::id();

            // Fetch today's complaints with cleaner details
            $todaysComplaints = Complaint::where('assigned_by', $supervisorId)
                ->whereDate('comp_date', Carbon::today())
                ->with(['cleaners' => function ($query) {
                    $query->select('cleaners.id', 'cleaner_name', 'cleaner_phoneNo');
                }])
                ->orderBy('comp_date', 'desc')
                ->get();

            // Fetch past complaints with cleaner details
            $pastComplaints = Complaint::where('assigned_by', $supervisorId)
                ->whereDate('comp_date', '<', Carbon::today())
                ->with(['cleaners' => function ($query) {
                    $query->select('cleaners.id', 'cleaner_name', 'cleaner_phoneNo');
                }])
                ->orderBy('comp_date', 'desc')
                ->get();

            return view('supervisor.history', compact('todaysComplaints', 'pastComplaints'));
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

    // API for fetching list of cleaners
    public function getAllCleaners(Request $request)
    {
        $status = $request->query('status', 'all');
    
        // Filter cleaners by status and eager load the related `user` records
        $query = Cleaner::with('user'); // Load the related user records
        if ($status !== 'all') {
            $query->where('status', $status);
        }
    
        $cleaners = $query->get();
    
        // Map the cleaners to include profile_pic from the users table
        $cleaners = $cleaners->map(function ($cleaner) {
            return [
                'user_id' => $cleaner->user_id,
                'cleaner_name' => $cleaner->cleaner_name,
                'cleaner_phoneNo' => $cleaner->cleaner_phoneNo,
                'profile_pic' => $cleaner->user->profile_pic ?? asset('storage/profile_pic/default.webp'), // Fetch from users table
                'cleaner_username' => $cleaner->cleaner_username,
                'status' => $cleaner->status,
                'created_at' => $cleaner->created_at,
                'updated_at' => $cleaner->updated_at,
                'building' => $cleaner->building,
            ];
        });
    
        return response()->json(['success' => true, 'data' => $cleaners], 200);
    }    
    

    public function showapi($user_id)
    {
        // Find the cleaner by user_id
        $cleaner = Cleaner::where('user_id', $user_id)->with('user')->first(); // Eager load the related user
    
        if (!$cleaner) {
            return response()->json(['message' => 'Cleaner not found'], 404);
        }
    
        // Retrieve the profile picture from the `users` table
        $profilePicUrl = $cleaner->user->profile_pic; // Access the user's profile_pic field
    
        // Retrieve the latest complaints assigned to this cleaner
        $latestComplaints = DB::table('complaints')
            ->join('complaint_cleaner', 'complaints.id', '=', 'complaint_cleaner.complaint_id')
            ->join('users', 'complaints.assigned_by', '=', 'users.id') // Join to fetch supervisor's name
            ->where('complaint_cleaner.cleaner_id', $user_id) // Use user_id for filtering
            ->select(
                'complaints.id as complaint_id',
                'complaints.comp_date',
                'complaints.comp_time',
                'complaints.comp_desc',
                'complaints.comp_location',
                'complaints.comp_image',
                'complaints.comp_status',
                'complaint_cleaner.assigned_date',
                'users.name as assigned_by' // Fetch the supervisor's name
            )
            ->orderBy('complaint_cleaner.assigned_date', 'desc')
            ->limit(1) // Retrieve the latest complaint
            ->get();
    
        return response()->json([
            'data' => [
                'user_id' => $cleaner->user_id,
                'cleaner_name' => $cleaner->cleaner_name,
                'cleaner_phoneNo' => $cleaner->cleaner_phoneNo,
                'profile_pic' => $profilePicUrl, // Retrieve the profile picture from the `users` table
                'cleaner_username' => $cleaner->cleaner_username,
                'status' => $cleaner->status,
                'created_at' => $cleaner->created_at,
                'updated_at' => $cleaner->updated_at,
                'building' => $cleaner->building,
                'latest_complaints' => $latestComplaints,
            ]
        ]);
    }
    
    
}