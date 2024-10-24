<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Supervisor;
use App\Models\Complaint;
use App\Models\Cleaner;
use App\Models\Officer;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Show the dashboard.
     */
    public function dashboard()
    {
        // Fetch the overall counts
        $totalComplaints = Complaint::count();
        $activeCleaners = Cleaner::where('status', 'available')->count();
        $totalOfficers = User::where('role', 'officer')->count();
        $totalSupervisors = User::where('role', 'supervisor')->count();

        // Fetch the recent 5 complaints
        $recentComplaints = Complaint::select('id', 'comp_date', 'comp_time', 'comp_desc', 'comp_location', 'comp_status')
            ->latest()
            ->take(5)
            ->get();

        // Fetch data for charts
        $monthlyComplaints = Complaint::selectRaw("MONTH(comp_date) as month, COUNT(*) as count")
            ->groupBy('month')
            ->pluck('count', 'month');

        $cleanersByStatus = Cleaner::selectRaw("status, COUNT(*) as count")
            ->groupBy('status')
            ->pluck('count', 'status');

        $complaintsByStatus = Complaint::selectRaw("comp_status, COUNT(*) as count")
            ->groupBy('comp_status')
            ->pluck('count', 'comp_status');

        return view('admin.dashboard', compact(
            'totalComplaints', 'activeCleaners', 'totalOfficers', 'totalSupervisors', 'recentComplaints',
            'monthlyComplaints', 'cleanersByStatus', 'complaintsByStatus'
        ));
    }

    /**
     * List and filter complaints.
     */
    public function complaints(Request $request)
    {
        $status = $request->input('status');
        $query = Complaint::query();

        // Apply filter by status if provided
        if ($status) {
            $query->where('comp_status', $status);
        }

        // Order complaints by date descending
        $complaints = $query->orderBy('comp_date', 'desc')->paginate(10);

        return view('admin.complaints.index', compact('complaints', 'status'));
    }

    /**
     * Display form to create a complaint.
     */
    public function createComplaint()
    {
        return view('admin.complaints.create');
    }

    /**
     * Store a newly created complaint.
     */
    public function storeComplaint(Request $request)
    {
        $this->validateComplaint($request);

        Complaint::create($request->all());

        return redirect()->route('admin.complaints')->with('success', 'Complaint created successfully!');
    }

    /**
     * Edit a complaint.
     */
    public function editComplaint($id)
    {
        $complaint = Complaint::findOrFail($id);
        return view('admin.complaints.edit', compact('complaint'));
    }

    /**
     * Update a complaint.
     */
    public function updateComplaint(Request $request, $id)
    {
        $this->validateComplaint($request);

        $complaint = Complaint::findOrFail($id);
        $complaint->update($request->all());

        return redirect()->route('admin.complaints')->with('success', 'Complaint updated successfully!');
    }

    /**
     * Delete a complaint.
     */
    public function destroyComplaint($id)
    {
        $complaint = Complaint::findOrFail($id);
        $complaint->delete();

        return redirect()->route('admin.complaints')->with('success', 'Complaint deleted successfully!');
    }

    /**
     * Search complaints by description, location, or ID.
     */
    public function searchComplaints(Request $request)
    {
        $query = $request->input('query');

        $complaints = Complaint::where('comp_desc', 'like', "%{$query}%")
            ->orWhere('comp_location', 'like', "%{$query}%")
            ->orWhere('id', 'like', "%{$query}%")
            ->paginate(10);

        return view('admin.complaints.index', compact('complaints', 'query'));
    }

    /**
     * Validate complaint data.
     */
    private function validateComplaint(Request $request)
    {
        $request->validate([
            'comp_desc' => 'required|string|max:255',
            'comp_status' => 'required|in:pending,on going,completed',
            'comp_location' => 'required|string|max:255',
            'comp_date' => 'required|date',
            'comp_time' => 'required|date_format:H:i',
        ]);
    }

    /**
     * List and search cleaners.
     */
    public function cleaners(Request $request)
{
    // Retrieve the search query and status filter from the request
    $search = $request->query('search');
    $status = $request->query('status');

    // Query the cleaners table with optional search and status filtering
    $cleaners = Cleaner::when($search, function ($query, $search) {
            return $query->where('cleaner_name', 'LIKE', "%{$search}%")
                         ->orWhere('cleaner_phoneNo', 'LIKE', "%{$search}%")
                         ->orWhere('cleaner_username', 'LIKE', "%{$search}%");
        })
        ->when($status, function ($query, $status) {
            return $query->where('status', strtolower($status)); // Ensure it works with 'available' and 'unavailable'
        })
        ->paginate(10); // Adjust the pagination as needed

    // Return the view with the filtered cleaners
    return view('admin.cleaners.index', compact('cleaners', 'search', 'status'));
}


    /**
     * Display form to create a cleaner.
     */
    public function createCleaner()
    {
        return view('admin.cleaners.create');
    }

    /**
     * Store a newly created cleaner.
     */
    public function storeCleaner(Request $request)
    {
        $this->validateCleaner($request);

        // Handle profile picture as binary data (BLOB)
        $profilePicData = null;
        if ($request->hasFile('profile_pic')) {
            $profilePicData = file_get_contents($request->file('profile_pic')->getRealPath());
        }

        // Create user and cleaner records
        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'phone_no' => $request->phone_no,
            'password' => bcrypt($request->password),
            'role' => 'cleaner',
        ]);

        Cleaner::create([
            'cleaner_username' => $user->username,
            'cleaner_name' => $user->name,
            'cleaner_phoneNo' => $user->phone_no,
            'profile_pic' => $profilePicData,
            'status' => $request->status,
            'user_id' => $user->id,
        ]);

        return redirect()->route('admin.cleaners.index')->with('success', 'Cleaner created successfully!');
    }

    /**
     * Edit a cleaner.
     */
    public function editCleaner(Cleaner $cleaner)
    {
        return view('admin.cleaners.edit', compact('cleaner'));
    }

    /**
     * Update a cleaner.
     */
    public function updateCleaner(Request $request, Cleaner $cleaner)
    {
        $this->validateCleaner($request);

        $cleaner->update([
            'cleaner_username' => $request->username ?? $cleaner->cleaner_username,
            'cleaner_name' => $request->name ?? $cleaner->cleaner_name,
            'cleaner_phoneNo' => $request->phone_no ?? $cleaner->cleaner_phoneNo,
            'status' => $request->status ?? $cleaner->status,
        ]);

        if ($request->filled('password')) {
            $cleaner->update(['cleaner_password' => bcrypt($request->password)]);
        }

        if ($request->hasFile('profile_pic')) {
            $cleaner->update(['profile_pic' => file_get_contents($request->file('profile_pic')->getRealPath())]);
        }

        return redirect()->route('admin.cleaners')->with('success', 'Cleaner updated successfully!');
    }

    /**
     * Delete a cleaner.
     */
    public function destroyCleaner($id)
    {
        $cleaner = Cleaner::findOrFail($id);
        $user = User::findOrFail($cleaner->user_id);

        $cleaner->delete();
        $user->delete();

        return redirect()->route('admin.cleaners.index')->with('success', 'Cleaner deleted successfully!');
    }

    /**
     * Validate cleaner data.
     */
    private function validateCleaner(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'name' => 'required|string|max:255',
            'phone_no' => 'required|string|max:20',
            'status' => 'required|in:Available,Unavailable',
            'password' => 'nullable|confirmed|min:8',
            'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    }

    public function officers(Request $request)
    {
        $search = $request->query('search');

        // Apply search query to paginate officers
        $officers = User::where('role', 'officer')
            ->when($search, function ($query, $search) {
                return $query->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('phone_no', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%");
            })
            ->paginate(10); // This ensures pagination is used

        return view('admin.officers.index', compact('officers'));
    }

    /**
     * Show the form for creating a new officer.
     */
    public function createOfficer()
    {
        return view('admin.officers.create');
    }

    /**
     * Store a newly created officer in storage.
     */
    public function storeOfficer(Request $request)
    {
        $request->validate([
            'officer_name' => 'required|string|max:255',
            'officer_email' => 'required|email|unique:users,email',
            'officer_phoneNo' => 'required|string|max:20',
            'officer_pass' => 'required|string|min:8',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->officer_name,
                'email' => $request->officer_email,
                'password' => Hash::make($request->officer_pass),
                'role' => 'officer',
            ]);

            Officer::create([
                'user_id' => $user->id,
                'officer_name' => $request->officer_name,
                'officer_email' => $request->officer_email,
                'officer_phoneNo' => $request->officer_phoneNo,
            ]);
        });

        return redirect()->route('admin.officers')->with('success', 'Officer added successfully.');
    }

    /**
     * Show the form for editing the specified officer.
     */
    public function editOfficer($id)
    {
        $officer = User::where('id', $id)->where('role', 'officer')->firstOrFail();
        return view('admin.officers.edit', compact('officer'));
    }

    /**
     * Update the specified officer in storage.
     */
    public function updateOfficer(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id, // Ensure unique email except the current user
            'phone_no' => 'required|string|max:20',
            'password' => 'nullable|string|min:8',
            'profile_pic' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $officer = User::where('id', $id)->where('role', 'officer')->firstOrFail();

        // Update officer data
        $officer->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone_no' => $request->phone_no,
        ]);

        // If password is provided, update it
        if ($request->filled('password')) {
            $officer->update([
                'password' => Hash::make($request->password),
            ]);
        }

        // Handle profile picture upload
        if ($request->hasFile('profile_pic')) {
            $path = $request->file('profile_pic')->store('profile_pics', 'public');
            $officer->update(['profile_pic' => $path]);
        }

        return redirect()->route('admin.officers')->with('success', 'Officer updated successfully!');
    }

    /**
     * Remove the specified officer from storage.
     */
    public function destroyOfficer($id)
    {
        $officer = User::where('id', $id)->where('role', 'officer')->firstOrFail();
        $officer->delete();

        return redirect()->route('admin.officers')->with('success', 'Officer deleted successfully.');
    }
    public function supervisors(Request $request)
    {
        $search = $request->query('search');
        $supervisors = User::where('role', 'supervisor')
            ->when($search, function ($query, $search) {
                return $query->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('username', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%");
            })
            ->paginate(10); // Pagination: 10 items per page

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

        return redirect()->route('admin.supervisors.index')->with('success', 'Supervisor updated successfully!');
    }
    public function destroySupervisor($id)
    {
        $supervisor = User::where('id', $id)->where('role', 'supervisor')->firstOrFail();
        $supervisor->delete();
        return redirect()->route('admin.supervisors.index')->with('success', 'Supervisor deleted successfully!');
    }
    private function validateSupervisor(Request $request)
    {
        $request->validate([
            's_name' => 'required|string|max:255',
            's_email' => 'required|email|unique:supervisors,s_email,' . ($request->route('supervisor')->id ?? 'NULL'),
            's_phoneNo' => 'required|numeric',
            'profile_pic' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
    }
}
