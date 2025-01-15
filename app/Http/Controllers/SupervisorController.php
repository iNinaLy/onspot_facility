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
        // Retrieve the current supervisor ID
        $supervisorId = Auth::id();
    
        // Check if filtering by complaints assigned by the current supervisor is requested
        $filterByMe = $request->query('assigned_by_me', false);
    
        // Define date ranges
        $today       = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek   = Carbon::now()->endOfWeek();
    
        // Number of items per "Load More" request
        $perPage = 5;
    
        /**
         * Helper closure to conditionally apply the supervisor filter to a query.
         */
        $applySupervisorFilter = function($query) use ($filterByMe, $supervisorId) {
            if ($filterByMe) {
                $query->where('assigned_by', $supervisorId);
            }
            // Return the query for chaining
            return $query;
        };
    
        // ------------------------------------------------
        // 1. Ongoing Complaints Assigned Today
        // ------------------------------------------------
        $ongoingToday = $applySupervisorFilter(
            Complaint::where('comp_status', 'ongoing')
                     ->whereDate('assigned_date', $today)
        )
        ->with(['cleaners:id,cleaner_name,cleaner_phoneNo', 'officer:id,name', 'supervisor:id,name'])
        ->orderBy('assigned_date', 'desc')
        ->get();
    
        // ------------------------------------------------
        // 2. Ongoing Complaints Assigned This Week (Excl. Today)
        // ------------------------------------------------
        $ongoingThisWeek = $applySupervisorFilter(
            Complaint::where('comp_status', 'ongoing')
                     ->whereBetween('assigned_date', [$startOfWeek, $endOfWeek])
                     ->whereDate('assigned_date', '<>', $today)
        )
        ->with(['cleaners:id,cleaner_name,cleaner_phoneNo', 'officer:id,name', 'supervisor:id,name'])
        ->orderBy('assigned_date', 'desc')
        ->get();
    
        // ------------------------------------------------
        // 3. Older Ongoing Complaints (Before This Week)
        // ------------------------------------------------
        $ongoingOlder = $applySupervisorFilter(
            Complaint::where('comp_status', 'ongoing')
                     ->whereDate('assigned_date', '<', $startOfWeek)
        )
        ->with(['cleaners:id,cleaner_name,cleaner_phoneNo', 'officer:id,name', 'supervisor:id,name'])
        ->orderBy('assigned_date', 'desc')
        ->paginate($perPage);
    
        // ------------------------------------------------
        // 4. Completed Complaints Assigned Today
        // ------------------------------------------------
        $completedToday = $applySupervisorFilter(
            Complaint::where('comp_status', 'completed')
                     ->whereDate('assigned_date', $today)
        )
        ->with(['cleaners:id,cleaner_name,cleaner_phoneNo', 'officer:id,name', 'supervisor:id,name'])
        ->orderBy('assigned_date', 'desc')
        ->get();
    
        // ------------------------------------------------
        // 5. Completed Complaints Assigned This Week (Excl. Today)
        // ------------------------------------------------
        $completedThisWeek = $applySupervisorFilter(
            Complaint::where('comp_status', 'completed')
                     ->whereBetween('assigned_date', [$startOfWeek, $endOfWeek])
                     ->whereDate('assigned_date', '<>', $today)
        )
        ->with(['cleaners:id,cleaner_name,cleaner_phoneNo', 'officer:id,name', 'supervisor:id,name'])
        ->orderBy('assigned_date', 'desc')
        ->get();
    
        // ------------------------------------------------
        // 6. Older Completed Complaints (Before This Week)
        // ------------------------------------------------
        $completedOlder = $applySupervisorFilter(
            Complaint::where('comp_status', 'completed')
                     ->whereDate('assigned_date', '<', $startOfWeek)
        )
        ->with(['cleaners:id,cleaner_name,cleaner_phoneNo', 'officer:id,name', 'supervisor:id,name'])
        ->orderBy('assigned_date', 'desc')
        ->paginate($perPage);
    
        // ------------------------------------------------
        // Handle AJAX "Load More" Requests
        // ------------------------------------------------
        if ($request->ajax()) {
            // The front-end should send ?status=ongoing|completed&filter=older|thisWeek|today&page=2&search=...
            $status = $request->get('status'); // 'ongoing' or 'completed'
            $filter = $request->get('filter'); // 'older', 'thisWeek', 'today'
            $page   = $request->get('page', 2);
            $search = $request->get('search'); // optional search param
    
            // 1) Build the base query depending on status
            if ($status === 'ongoing') {
                $ajaxQuery = Complaint::where('comp_status', 'ongoing');
            } else {
                $ajaxQuery = Complaint::where('comp_status', 'completed');
            }
    
            // 2) Apply date range filter
            if ($filter === 'older') {
                // older -> assigned_date < startOfWeek
                $ajaxQuery->whereDate('assigned_date', '<', $startOfWeek);
            } elseif ($filter === 'thisWeek') {
                // assigned_date between startOfWeek and endOfWeek, excluding today
                $ajaxQuery->whereBetween('assigned_date', [$startOfWeek, $endOfWeek])
                          ->whereDate('assigned_date', '<>', $today);
            } elseif ($filter === 'today') {
                $ajaxQuery->whereDate('assigned_date', $today);
            }
            // else, if no filter param, do nothing or handle default
    
            // 3) Apply supervisor filter if needed
            $applySupervisorFilter($ajaxQuery);
    
            // 4) Optional: Apply search if provided
            // e.g., search by comp_desc or comp_location
            if ($search) {
                $ajaxQuery->where(function($q) use ($search) {
                    $q->where('comp_desc', 'like', "%{$search}%")
                      ->orWhere('comp_location', 'like', "%{$search}%");
                });
            }
    
            // 5) Paginate the results for the requested page
            $complaints = $ajaxQuery
                ->with(['cleaners:id,cleaner_name,cleaner_phoneNo', 'officer:id,name', 'supervisor:id,name'])
                ->orderBy('assigned_date', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);
    
            // Return JSON so the front-end can append
            return response()->json([
                'complaints' => $complaints->items(),   // array of complaint data
                'hasMore'    => $complaints->hasMorePages(),
            ]);
        }
    
        // If not AJAX, return the main Blade view
        return view('supervisor.history', [
            'ongoingToday'     => $ongoingToday,
            'ongoingThisWeek'  => $ongoingThisWeek,
            'ongoingOlder'     => $ongoingOlder,
            'completedToday'   => $completedToday,
            'completedThisWeek'=> $completedThisWeek,
            'completedOlder'   => $completedOlder,
            'assignedByMe'     => $filterByMe,
        ]);
    }
    
}
