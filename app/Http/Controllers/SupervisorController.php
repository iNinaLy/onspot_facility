<?php

namespace App\Http\Controllers;

use App\Models\User; // Use the User model instead of Supervisor
use App\Models\Complaint;
use App\Models\Cleaner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SupervisorController extends Controller
{
    /**
     * Display the supervisor dashboard.
     */
    public function dashboard()
    {
        $supervisorId = Auth::id();

        // Retrieve cleaner stats
        $totalCleaners = Cleaner::count();
        $availableCleaners = Cleaner::where('status', 'available')->count();
        $unavailableCleaners = Cleaner::where('status', 'unavailable')->count();

        // Fetch the total number of supervisors from the users table where role is 'supervisor'
        $totalSupervisors = User::where('role', 'supervisor')->count();

        // Fetch the most recent ongoing complaint assigned by the supervisor
        $recentOngoingComplaint = Complaint::with(['user', 'cleaners'])
            ->where('comp_status', 'ongoing') // Filter for ongoing complaints
            ->where('assigned_by', $supervisorId) // Only complaints assigned by the current supervisor
            ->orderBy('comp_date', 'desc') // Sort by the most recent complaint date
            ->first(); // Get only the most recent complaint

        // Retrieve unread notifications for the authenticated user
        $unreadNotifications = auth()->guard('web')->user()->unreadNotifications;

        // Pass data to the view
        return view('supervisor.dashboard', compact(
            'totalCleaners',
            'availableCleaners',
            'unavailableCleaners',
            'totalSupervisors',
            'recentOngoingComplaint', // Pass the most recent ongoing complaint
            'unreadNotifications'
        ));
    }

    public function editProfile(Request $request)
    {
        $user = $request->user();
        return view('supervisor.profile.edit', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $request->user()->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user = $request->user();
        $user->name = $request->input('name');
        $user->email = $request->input('email');

        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();

        return redirect()->route('supervisor.profile.edit')->with('success', 'Profile updated successfully.');
    }

    public function destroyProfile(Request $request)
    {
        $request->validate(['password' => ['required', 'current_password']]);

        $user = $request->user();
        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Account deleted successfully.');
    }


    /**
     * Show the history of complaints.
     */
    public function history()
    {
        $supervisorId = Auth::id();
        
        // Fetch today's complaints with 'completed' status
        $todaysComplaints = Complaint::where('assigned_by', $supervisorId)
            ->whereDate('comp_date', Carbon::today())
            ->where('comp_status', 'completed')
            ->with(['cleaners' => function ($query) {
                $query->select('cleaners.id', 'cleaner_name', 'cleaner_phoneNo');
            }])
            ->orderBy('comp_date', 'desc')
            ->get();
        
        // Fetch past complaints with 'completed' status
        $pastComplaints = Complaint::where('assigned_by', $supervisorId)
            ->whereDate('comp_date', '<', Carbon::today())
            ->where('comp_status', 'completed')
            ->with(['cleaners' => function ($query) {
                $query->select('cleaners.id', 'cleaner_name', 'cleaner_phoneNo');
            }])
            ->orderBy('comp_date', 'desc')
            ->get();
        
        // Fetch all ongoing complaints, regardless of date
        $ongoingComplaints = Complaint::where('assigned_by', $supervisorId)
            ->where('comp_status', 'ongoing')
            ->with(['cleaners' => function ($query) {
                $query->select('cleaners.id', 'cleaner_name', 'cleaner_phoneNo');
            }])
            ->orderBy('comp_date', 'desc')
            ->get();

        // Combine all completed complaints (today's and past) into one variable
        $completedComplaints = $todaysComplaints->merge($pastComplaints);

        // Pass the fetched data to the view
        return view('supervisor.history', compact(
            'todaysComplaints', 
            'pastComplaints', 
            'ongoingComplaints',
            'completedComplaints' // Add this to the view
        ));
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
        // Fetch supervisors from users table where role is 'supervisor'
        $supervisors = User::where('role', 'supervisor')->get();
        return response()->json(['success' => true, 'data' => $supervisors], 200);
    }

    /**
     * Store a newly created supervisor.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email'     => 'required|email|unique:users',
            'password'  => 'required|min:8',
            'name'      => 'required|string|max:255',
            'phone_no'  => 'required|string|max:15',
        ]);

        // Create a new supervisor in the users table
        $supervisor = User::create([
            'email'     => $request->email,
            'password'  => bcrypt($request->password),
            'name'      => $request->name,
            'phone_no'  => $request->phone_no,
            'role'      => 'supervisor',
        ]);

        return response()->json(['success' => true, 'data' => $supervisor], 201);
    }

    /**
     * Display the specified supervisor.
     */
    public function show($id)
    {
        // Find the supervisor in the users table where role is 'supervisor'
        $supervisor = User::where('role', 'supervisor')->find($id);

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
    public function edit($id)
    {
        // Find the supervisor in the users table
        $supervisor = User::where('role', 'supervisor')->findOrFail($id);
        return view('admin.supervisors.edit', compact('supervisor'));
    }

    /**
     * Update the specified supervisor.
     */
    public function update(Request $request, $id)
    {
        $supervisor = User::where('role', 'supervisor')->findOrFail($id);

        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|max:255|unique:users,email,' . $id,
            'phone_no'     => 'required|string|max:15',
            'profile_pic'  => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password'     => 'nullable|string|min:8|confirmed',
        ]);

        // Update supervisor data
        $supervisor->name     = $request->name;
        $supervisor->email    = $request->email;
        $supervisor->phone_no = $request->phone_no;

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
        if ($request->filled('password')) {
            $supervisor->password = bcrypt($request->password);
        }

        $supervisor->save();

        return redirect()->route('admin.supervisors.index')->with('success', 'Supervisor updated successfully.');
    }

    /**
     * Remove the specified supervisor.
     */
    public function destroy($id)
    {
        $supervisor = User::where('role', 'supervisor')->findOrFail($id);

        if ($supervisor->profile_pic && Storage::exists('public/' . $supervisor->profile_pic)) {
            Storage::delete('public/' . $supervisor->profile_pic);
        }

        $supervisor->delete();

        return response()->json(['success' => true, 'message' => 'Supervisor deleted successfully'], 200);
    }
}
