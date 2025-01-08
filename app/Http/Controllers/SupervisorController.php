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


    public function history(Request $request)
    {
        // You can still retrieve the current supervisor ID if needed for other logic,
        // but we won't use it to filter complaints anymore.
        $supervisorId = Auth::id();

        // Define date ranges based on assigned_date
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        // Number of items per "Load More" request
        $perPage = 5;

        /**
         * Fetch Ongoing Complaints Categorized by Assigned Date
         */

        // 1. Ongoing Complaints Assigned Today
        $ongoingToday = Complaint::where('comp_status', 'ongoing')
            ->whereDate('assigned_date', $today)
            ->with(['cleaners:id,cleaner_name,cleaner_phoneNo', 'officer:id,name', 'supervisor:id,name'])
            ->orderBy('assigned_date', 'desc')
            ->get();

        // 2. Ongoing Complaints Assigned This Week (Excluding Today)
        $ongoingThisWeek = Complaint::where('comp_status', 'ongoing')
            ->whereBetween('assigned_date', [$startOfWeek, $endOfWeek])
            ->whereDate('assigned_date', '<>', $today)
            ->with(['cleaners:id,cleaner_name,cleaner_phoneNo', 'officer:id,name', 'supervisor:id,name'])
            ->orderBy('assigned_date', 'desc')
            ->get();

        // 3. Older Ongoing Complaints (Assigned Before This Week)
        $ongoingOlder = Complaint::where('comp_status', 'ongoing')
            ->whereDate('assigned_date', '<', $startOfWeek)
            ->with(['cleaners:id,cleaner_name,cleaner_phoneNo', 'officer:id,name', 'supervisor:id,name'])
            ->orderBy('assigned_date', 'desc')
            ->paginate($perPage);

        /**
         * Fetch Completed Complaints Categorized by Assigned Date
         */

        // 4. Completed Complaints Assigned Today
        $completedToday = Complaint::where('comp_status', 'completed')
            ->whereDate('assigned_date', $today)
            ->with(['cleaners:id,cleaner_name,cleaner_phoneNo', 'officer:id,name', 'supervisor:id,name'])
            ->orderBy('assigned_date', 'desc')
            ->get();

        // 5. Completed Complaints Assigned This Week (Excluding Today)
        $completedThisWeek = Complaint::where('comp_status', 'completed')
            ->whereBetween('assigned_date', [$startOfWeek, $endOfWeek])
            ->whereDate('assigned_date', '<>', $today)
            ->with(['cleaners:id,cleaner_name,cleaner_phoneNo', 'officer:id,name', 'supervisor:id,name'])
            ->orderBy('assigned_date', 'desc')
            ->get();

        // 6. Older Completed Complaints (Assigned Before This Week)
        $completedOlder = Complaint::where('comp_status', 'completed')
            ->whereDate('assigned_date', '<', $startOfWeek)
            ->with(['cleaners:id,cleaner_name,cleaner_phoneNo', 'officer:id,name', 'supervisor:id,name'])
            ->orderBy('assigned_date', 'desc')
            ->paginate($perPage);

        /**
         * Handle AJAX Requests for "Load More"
         */
        if ($request->ajax()) {
            $filter = $request->input('filter'); // e.g., 'older'
            $status = $request->input('status'); // e.g., 'ongoing' or 'completed'
            $page = $request->input('page', 1);  // Current page number

            // Validate inputs
            if ($filter !== 'older') {
                return response()->json(['message' => 'Invalid filter parameter.'], 400);
            }

            if (!in_array($status, ['ongoing', 'completed'])) {
                return response()->json(['message' => 'Invalid status parameter.'], 400);
            }

            if ($status === 'ongoing') {
                // Load older ongoing complaints
                $ongoingOlder = Complaint::where('comp_status', 'ongoing')
                    ->whereDate('assigned_date', '<', $startOfWeek)
                    ->with(['cleaners:id,cleaner_name,cleaner_phoneNo', 'officer:id,name', 'supervisor:id,name'])
                    ->orderBy('assigned_date', 'desc')
                    ->paginate($perPage, ['*'], 'page', $page);

                return response()->json([
                    'complaints'  => $ongoingOlder->items(),
                    'hasMore'     => $ongoingOlder->hasMorePages(),
                    'currentPage' => $ongoingOlder->currentPage(),
                ]);

            } elseif ($status === 'completed') {
                // Load older completed complaints
                $completedOlder = Complaint::where('comp_status', 'completed')
                    ->whereDate('assigned_date', '<', $startOfWeek)
                    ->with(['cleaners:id,cleaner_name,cleaner_phoneNo', 'officer:id,name', 'supervisor:id,name'])
                    ->orderBy('assigned_date', 'desc')
                    ->paginate($perPage, ['*'], 'page', $page);

                return response()->json([
                    'complaints'  => $completedOlder->items(),
                    'hasMore'     => $completedOlder->hasMorePages(),
                    'currentPage' => $completedOlder->currentPage(),
                ]);
            }
        }

        /**
         * Return the view with all the data
         */
        return view('supervisor.history', compact(
            'ongoingToday',
            'ongoingThisWeek',
            'ongoingOlder',
            'completedToday',
            'completedThisWeek',
            'completedOlder'
        ));
    }
}
