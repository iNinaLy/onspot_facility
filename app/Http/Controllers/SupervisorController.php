<?php

namespace App\Http\Controllers;

use App\Models\User;
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

    // Notifications
    $user = Auth::user();
    $unreadNotifications = $user->unreadNotifications;
    $notifications = $user->notifications->sortByDesc('created_at')->take(10);
    
    // Fetch the most recent ongoing complaint assigned by the supervisor
    $recentOngoingComplaint = Complaint::with(['user', 'cleaners'])
        ->where('comp_status', 'ongoing')
        ->where('assigned_by', $supervisorId)
        ->orderBy('comp_date', 'desc')
        ->first();

    // Fetch count of pending complaints
    $pendingComplaints = Complaint::where('comp_status', 'Pending')->count();

    // Pass data to the view
    return view('supervisor.dashboard', compact(
        'totalCleaners',
        'availableCleaners',
        'unavailableCleaners',
        'totalSupervisors',
        'recentOngoingComplaint',
        'unreadNotifications',
        'notifications',
        'pendingComplaints' // Pass the new variable
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
    
        // Fetch ongoing complaints assigned by the supervisor
        $ongoingComplaints = Complaint::where('assigned_by', $supervisorId)
            ->where('comp_status', 'ongoing')
            ->with(['cleaners:id,cleaner_name,cleaner_phoneNo'])
            ->orderBy('comp_date', 'desc')
            ->get();
    
        // Fetch today's completed complaints assigned by the supervisor
        $todaysComplaints = Complaint::where('assigned_by', $supervisorId)
            ->whereDate('comp_date', Carbon::today())
            ->where('comp_status', 'completed')
            ->with(['cleaners:id,cleaner_name,cleaner_phoneNo'])
            ->orderBy('comp_date', 'desc')
            ->get();
    
        // Fetch this week's completed complaints assigned by the supervisor (excluding today)
        $thisWeeksComplaints = Complaint::where('assigned_by', $supervisorId)
            ->whereBetween('comp_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->where('comp_status', 'completed')
            ->whereDate('comp_date', '<>', Carbon::today())
            ->with(['cleaners:id,cleaner_name,cleaner_phoneNo'])
            ->orderBy('comp_date', 'desc')
            ->get();
    
        // Fetch older completed complaints assigned by the supervisor with pagination
        $olderComplaints = Complaint::where('assigned_by', $supervisorId)
            ->whereDate('comp_date', '<', Carbon::now()->startOfWeek())
            ->where('comp_status', 'completed')
            ->with(['cleaners:id,cleaner_name,cleaner_phoneNo'])
            ->orderBy('comp_date', 'desc')
            ->paginate(5); // Adjust the number per page as needed
    
        // Pass all the data to the view without duplication
        return view('supervisor.history', compact(
            'ongoingComplaints',
            'todaysComplaints',
            'thisWeeksComplaints',
            'olderComplaints'
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
