<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Complaint;
use App\Models\Cleaner;
use Illuminate\Http\Request;
use App\Http\Requests\ProfileUpdateRequest;  
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
            'pendingComplaints' 
        ));
    }

    public function editProfile(Request $request)
    {
        $user = $request->user();
        return view('supervisor.profile.edit', compact('user'));
    }


    public function updateProfile(ProfileUpdateRequest $request)
    {
        $user = $request->user();
        $user->fill($request->validated());

        // Check if the email was changed and reset verification if necessary
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // If password provided in the request, hash and update it
        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();

        return redirect()->route('supervisor.profile.edit')->with('success', 'Profile updated successfully.');
    }


    public function destroyProfile(Request $request)
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

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
 
        $supervisorId = Auth::id();

        $filterByMe = $request->query('assigned_by_me', false);

        $today       = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek   = Carbon::now()->endOfWeek();
        $perPage = 6;


        $applySupervisorFilter = function($query) use ($filterByMe, $supervisorId) {
            if ($filterByMe) {
                $query->where('assigned_by', $supervisorId);
            }
           
            return $query;
        };

        $ongoingToday = $applySupervisorFilter(
            Complaint::where('comp_status', 'ongoing')
                     ->whereDate('assigned_date', $today)
        )
        ->with([
            'cleaners:id,cleaner_name,cleaner_phoneNo',
            'cleaners.media', 
            'officer:id,name',
            'supervisor:id,name'
        ])
        ->orderBy('assigned_date', 'desc')
        ->get();


        $ongoingThisWeek = $applySupervisorFilter(
            Complaint::where('comp_status', 'ongoing')
                     ->whereBetween('assigned_date', [$startOfWeek, $endOfWeek])
                     ->whereDate('assigned_date', '<>', $today)
        )
        ->with([
            'cleaners:id,cleaner_name,cleaner_phoneNo',
            'cleaners.media',
            'officer:id,name',
            'supervisor:id,name'
        ])
        ->orderBy('assigned_date', 'desc')
        ->get();

        
        $ongoingOlder = $applySupervisorFilter(
            Complaint::where('comp_status', 'ongoing')
                     ->whereDate('assigned_date', '<', $startOfWeek)
        )
        ->with([
            'cleaners:id,cleaner_name,cleaner_phoneNo',
            'cleaners.media',
            'officer:id,name',
            'supervisor:id,name'
        ])
        ->orderBy('assigned_date', 'desc')
        ->paginate($perPage);

        
        $completedToday = $applySupervisorFilter(
            Complaint::where('comp_status', 'completed')
                     ->whereDate('assigned_date', $today)
        )
        ->with([
            'cleaners:id,cleaner_name,cleaner_phoneNo',
            'cleaners.media',
            'officer:id,name',
            'supervisor:id,name'
        ])
        ->orderBy('assigned_date', 'desc')
        ->get();


        $completedThisWeek = $applySupervisorFilter(
            Complaint::where('comp_status', 'completed')
                     ->whereBetween('assigned_date', [$startOfWeek, $endOfWeek])
                     ->whereDate('assigned_date', '<>', $today)
        )
        ->with([
            'cleaners:id,cleaner_name,cleaner_phoneNo',
            'cleaners.media',
            'officer:id,name',
            'supervisor:id,name'
        ])
        ->orderBy('assigned_date', 'desc')
        ->get();

   
        $completedOlder = $applySupervisorFilter(
            Complaint::where('comp_status', 'completed')
                     ->whereDate('assigned_date', '<', $startOfWeek)
        )
        ->with([
            'cleaners:id,cleaner_name,cleaner_phoneNo',
            'cleaners.media',
            'officer:id,name',
            'supervisor:id,name'
        ])
        ->orderBy('assigned_date', 'desc')
        ->paginate($perPage);

        if ($request->ajax()) {
      
            $status = $request->get('status'); 
            $filter = $request->get('filter');
            $page   = $request->get('page', 2);
            $search = $request->get('search');

    
            if ($status === 'ongoing') {
                $ajaxQuery = Complaint::where('comp_status', 'ongoing');
            } else {
                $ajaxQuery = Complaint::where('comp_status', 'completed');
            }

   
            if ($filter === 'older') {
                $ajaxQuery->whereDate('assigned_date', '<', $startOfWeek);
            } elseif ($filter === 'thisWeek') {
                $ajaxQuery->whereBetween('assigned_date', [$startOfWeek, $endOfWeek])
                          ->whereDate('assigned_date', '<>', $today);
            } elseif ($filter === 'today') {
                $ajaxQuery->whereDate('assigned_date', $today);
            }

            $applySupervisorFilter($ajaxQuery);

            if ($search) {
                $ajaxQuery->where(function($q) use ($search) {
                    $q->where('comp_desc', 'like', "%{$search}%")
                      ->orWhere('comp_location', 'like', "%{$search}%");
                });
            }

            $complaints = $ajaxQuery
                ->with([
                    'cleaners:id,cleaner_name,cleaner_phoneNo',
                    'cleaners.media',
                    'officer:id,name',
                    'supervisor:id,name'
                ])
                ->orderBy('assigned_date', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);


            return response()->json([
                'complaints' => $complaints->items(),   
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

    //Admin

    public function supervisors(Request $request)
    {
        $query = User::where('role', 'supervisor');

        // Apply search filter if provided
        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                ->orWhere('phone_no', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Paginate the results
        $supervisors = $query->paginate(10);

        // Return the view with supervisors data
        return view('admin.supervisors.index', compact('supervisors'));
    }

    public function createSupervisor()
    {
        return view('admin.supervisors.create');
    }

    public function storeSupervisor(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_no' => 'required|string|max:20',
            'profile_pic' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('profile_pic')) {
            $data['profile_pic'] = $request->file('profile_pic')->store('profile_pics', 'public');
        }

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone_no' => $data['phone_no'],
            'role' => 'supervisor',
            'profile_pic' => $data['profile_pic'] ?? null,
        ]);

        return redirect()->route('admin.supervisors.index')->with('success', 'Supervisor created successfully!');
    }

    public function editSupervisor($id)
    {
        $supervisor = User::where('id', $id)->where('role', 'supervisor')->firstOrFail();
        return view('admin.supervisors.edit', compact('supervisor'));
    }

    public function updateSupervisor(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone_no' => 'required|string|max:20',
            'profile_pic' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $supervisor = User::where('id', $id)->where('role', 'supervisor')->firstOrFail();

        $supervisor->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone_no' => $request->phone_no,
        ]);

        if ($request->hasFile('profile_pic')) {
            $path = $request->file('profile_pic')->store('profile_pics', 'public');
            $supervisor->update(['profile_pic' => $path]);
        }

        return redirect()->route('admin.supervisors.index')->with('success', 'Supervisor information has been updated.');
    }

    public function resetSupervisorPassword(Request $request, $id)
    {
        $request->validate([
            'new_password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/[a-z]/', // at least one lowercase letter
                'regex:/[A-Z]/', // at least one uppercase letter
                'regex:/[0-9]/', // at least one digit
                'regex:/[@$!%*#?&]/' // at least one special character
            ],
        ]);

        $supervisor = User::findOrFail($id); // Ensure the user exists in the `users` table
        $supervisor->password = Hash::make($request->new_password); // Hash the new password
        $supervisor->save(); // Save the updated password to the database

        return redirect()->route('admin.supervisors.index', $id)->with('status', 'Password has been reset successfully.');
    }


    //Profile Settings

    
    
}
